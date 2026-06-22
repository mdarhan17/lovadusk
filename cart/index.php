<?php
require_once "../includes/auth-check.php";
require_once "../database/phase8-schema.php";

ensure_phase8_schema($conn);

$pageTitle = "Cart | LOVA DUSK";
include "../includes/header.php";

$userId = (int)$_SESSION["user_id"];

$sql = "SELECT cart.id AS cart_id, cart.qty, cart.size, products.id AS product_id,
               products.name, products.price, products.sale_price, products.main_image
        FROM cart
        INNER JOIN products ON cart.product_id = products.id
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

$discount = 0;
$couponCode = $_SESSION["coupon_code"] ?? "";

if ($couponCode !== "") {
    $couponCheck = calculate_coupon_discount($conn, $couponCode, $subtotal);
    if ($couponCheck["valid"]) {
        $discount = $couponCheck["discount"];
    } else {
        unset($_SESSION["coupon_code"]);
        $_SESSION["coupon_message"] = $couponCheck["message"];
        $couponCode = "";
    }
}

$total = max(0, $subtotal - $discount);
?>

<section class="page-hero">
    <p class="eyebrow">Your Selection</p>
    <h1>Shopping Cart</h1>
    <p>Review your LOVA DUSK pieces before checkout.</p>
</section>

<section class="cart-section">
    <?php if (count($items) > 0): ?>
        <div class="cart-layout">
            <div class="cart-items">
                <?php foreach ($items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-img">
                            <?php if ($item["main_image"]): ?>
                                <img src="<?= BASE_URL . clean($item["main_image"]); ?>" alt="<?= clean($item["name"]); ?>">
                            <?php endif; ?>
                        </div>

                        <div class="cart-info">
                            <h3><?= clean($item["name"]); ?></h3>
                            <p>Size: <?= clean($item["size"]); ?></p>
                            <p><?= money($item["final_price"]); ?></p>

                            <form method="POST" action="update.php" class="cart-update-form">
                                <?= csrf_input(); ?>
                                <input type="hidden" name="cart_id" value="<?= $item["cart_id"]; ?>">
                                <input type="number" name="qty" min="1" value="<?= (int)$item["qty"]; ?>">
                                <button type="submit">Update</button>
                            </form>
                        </div>

                        <div class="cart-price">
                            <p><?= money($item["line_total"]); ?></p>

                            <form method="POST" action="remove.php">
                                <?= csrf_input(); ?>
                                <input type="hidden" name="cart_id" value="<?= $item["cart_id"]; ?>">
                                <button type="submit" class="remove-btn">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <aside class="cart-summary">
                <h2>Order Summary</h2>

                <?php if (!empty($_SESSION["coupon_message"])): ?>
                    <div class="alert"><?= clean($_SESSION["coupon_message"]); unset($_SESSION["coupon_message"]); ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL; ?>ajax/apply-coupon.php" class="coupon-form">
                    <?= csrf_input(); ?>
                    <input type="text" name="coupon_code" placeholder="Coupon code" value="<?= clean($couponCode); ?>">
                    <button type="submit">Apply</button>
                </form>

                <?php if ($couponCode): ?>
                    <form method="POST" action="<?= BASE_URL; ?>ajax/remove-coupon.php">
                        <button type="submit" class="clear-cart-btn">Remove Coupon</button>
                    </form>
                <?php endif; ?>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong><?= money($subtotal); ?></strong>
                </div>

                <div class="summary-row">
                    <span>Discount</span>
                    <strong>- <?= money($discount); ?></strong>
                </div>

                <div class="summary-row">
                    <span>Shipping</span>
                    <strong>Free</strong>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <strong><?= money($total); ?></strong>
                </div>

                <a href="<?= BASE_URL; ?>checkout/" class="btn btn-dark full-btn">Proceed To Checkout</a>

                <form method="POST" action="clear.php">
                    <?= csrf_input(); ?>
                    <button type="submit" class="clear-cart-btn">Clear Cart</button>
                </form>
            </aside>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <p>Explore the latest LOVA DUSK drops.</p>
            <a href="<?= BASE_URL; ?>shop.php" class="btn btn-dark">Shop Now</a>
        </div>
    <?php endif; ?>
</section>

<?php include "../includes/footer.php"; ?>