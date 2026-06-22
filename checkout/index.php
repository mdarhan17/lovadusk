<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../includes/csrf.php";
require_once "../database/phase5-schema.php";
require_once "../database/phase8-schema.php";

ensure_phase5_schema($conn);
ensure_phase8_schema($conn);

$userId = (int)$_SESSION["user_id"];
$message = "";

$sql = "SELECT cart.id AS cart_id, cart.qty, cart.size, products.id AS product_id,
               products.name, products.price, products.sale_price, products.main_image,
               product_sizes.stock AS size_stock
        FROM cart
        INNER JOIN products ON cart.product_id = products.id
        INNER JOIN product_sizes ON cart.product_id = product_sizes.product_id AND cart.size = product_sizes.size
        WHERE cart.user_id = ?
        ORDER BY cart.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$cartItems = $stmt->get_result();

$items = [];
$subtotal = 0;

while ($item = $cartItems->fetch_assoc()) {
    $price = $item["sale_price"] ? (float)$item["sale_price"] : (float)$item["price"];
    $item["final_price"] = $price;
    $item["line_total"] = $price * (int)$item["qty"];
    $subtotal += $item["line_total"];
    $items[] = $item;
}

$couponCode = $_SESSION["coupon_code"] ?? "";
$discount = 0;

if ($couponCode !== "") {
    $couponCheck = calculate_coupon_discount($conn, $couponCode, $subtotal);
    if ($couponCheck["valid"]) {
        $discount = $couponCheck["discount"];
    } else {
        unset($_SESSION["coupon_code"]);
        $couponCode = "";
    }
}

$finalTotal = max(0, $subtotal - $discount);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $name = trim($_POST["customer_name"] ?? "");
    $email = strtolower(trim($_POST["customer_email"] ?? ""));
    $phone = trim($_POST["customer_phone"] ?? "");
    $address = trim($_POST["address_line"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $state = trim($_POST["state"] ?? "");
    $pincode = trim($_POST["pincode"] ?? "");
    $notes = trim($_POST["notes"] ?? "");

    if (count($items) === 0) {
        $message = "Your cart is empty.";
    } elseif ($name === "" || $email === "" || $phone === "" || $address === "" || $city === "" || $state === "" || $pincode === "") {
        $message = "Please fill all delivery details.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email.";
    } else {
        foreach ($items as $item) {
            if ((int)$item["qty"] > (int)$item["size_stock"]) {
                $message = clean($item["name"]) . " has only " . (int)$item["size_stock"] . " piece(s) left.";
                break;
            }
        }

        if ($message === "") {
            try {
                $conn->begin_transaction();

                $orderNumber = "LD" . date("YmdHis") . rand(100, 999);
                $paymentStatus = "pending";
                $orderStatus = "placed";
                $paymentMethod = "razorpay";

                $orderStmt = $conn->prepare("INSERT INTO orders
                    (user_id, order_number, customer_name, customer_email, customer_phone, address_line, city, state, pincode, total_amount, payment_status, order_status, payment_method, notes, coupon_code, discount_amount)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $orderStmt->bind_param(
                    "issssssssdsssssd",
                    $userId,
                    $orderNumber,
                    $name,
                    $email,
                    $phone,
                    $address,
                    $city,
                    $state,
                    $pincode,
                    $finalTotal,
                    $paymentStatus,
                    $orderStatus,
                    $paymentMethod,
                    $notes,
                    $couponCode,
                    $discount
                );

                $orderStmt->execute();
                $orderId = $orderStmt->insert_id;

                $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, size, qty, price) VALUES (?, ?, ?, ?, ?)");

                foreach ($items as $item) {
                    $productId = (int)$item["product_id"];
                    $qty = (int)$item["qty"];
                    $size = $item["size"];
                    $price = (float)$item["final_price"];

                    $itemStmt->bind_param("iisid", $orderId, $productId, $size, $qty, $price);
                    $itemStmt->execute();
                }

                if ($couponCode !== "") {
                    $couponUpdate = $conn->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE code = ?");
                    $couponUpdate->bind_param("s", $couponCode);
                    $couponUpdate->execute();
                }

                $conn->commit();

                header("Location: payment.php?order=" . urlencode($orderNumber));
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Order could not be created. Please try again.";
            }
        }
    }
}

$pageTitle = "Checkout | LOVA DUSK";
include "../includes/header.php";
?>

<section class="page-hero">
    <p class="eyebrow">Secure Checkout</p>
    <h1>Checkout</h1>
    <p>Pay securely using Razorpay.</p>
</section>

<section class="checkout-section">
    <?php if ($message): ?>
        <div class="alert"><?= clean($message); ?></div>
    <?php endif; ?>

    <?php if (count($items) > 0): ?>
        <div class="checkout-layout">
            <form method="POST" class="checkout-form">
                <?= csrf_input(); ?>

                <h2>Delivery Details</h2>

                <label>Full Name</label>
                <input type="text" name="customer_name" required>

                <label>Email</label>
                <input type="email" name="customer_email" required>

                <label>Phone</label>
                <input type="text" name="customer_phone" required>

                <label>Full Address</label>
                <textarea name="address_line" required></textarea>

                <div class="form-row">
                    <div>
                        <label>City</label>
                        <input type="text" name="city" required>
                    </div>
                    <div>
                        <label>State</label>
                        <input type="text" name="state" required>
                    </div>
                </div>

                <label>Pincode</label>
                <input type="text" name="pincode" required>

                <label>Order Notes</label>
                <textarea name="notes" placeholder="Optional"></textarea>

                <button type="submit" class="btn btn-dark">Continue To Payment</button>
            </form>

            <aside class="cart-summary">
                <h2>Order Summary</h2>

                <?php foreach ($items as $item): ?>
                    <div class="summary-row">
                        <span><?= clean($item["name"]); ?> × <?= (int)$item["qty"]; ?></span>
                        <strong><?= money($item["line_total"]); ?></strong>
                    </div>
                <?php endforeach; ?>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong><?= money($subtotal); ?></strong>
                </div>

                <div class="summary-row">
                    <span>Discount <?= $couponCode ? "(" . clean($couponCode) . ")" : ""; ?></span>
                    <strong>- <?= money($discount); ?></strong>
                </div>

                <div class="summary-row">
                    <span>Shipping</span>
                    <strong>Free</strong>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <strong><?= money($finalTotal); ?></strong>
                </div>
            </aside>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <a href="<?= BASE_URL; ?>shop.php" class="btn btn-dark">Shop Now</a>
        </div>
    <?php endif; ?>
</section>

<?php include "../includes/footer.php"; ?>