<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../database/phase5-schema.php";

ensure_phase5_schema($conn);

$userId = (int)$_SESSION["user_id"];
$orderNumber = trim($_GET["order"] ?? "");

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("si", $orderNumber, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

$itemStmt = $conn->prepare("SELECT order_items.*, products.name, products.main_image
                            FROM order_items
                            INNER JOIN products ON order_items.product_id = products.id
                            WHERE order_items.order_id = ?");
$itemStmt->bind_param("i", $order["id"]);
$itemStmt->execute();
$items = $itemStmt->get_result();

$pageTitle = "Order Details | LOVA DUSK";
include "../includes/header.php";
?>

<section class="page-hero">
    <p class="eyebrow">Order Details</p>
    <h1><?= clean($order["order_number"]); ?></h1>
    <p>Status: <?= clean($order["order_status"]); ?></p>
</section>

<section class="orders-section">
    <div class="order-detail-box">
        <h2>Delivery Address</h2>
        <p><?= clean($order["customer_name"]); ?>, <?= clean($order["customer_phone"]); ?></p>
        <p><?= clean($order["address_line"]); ?>, <?= clean($order["city"]); ?>, <?= clean($order["state"]); ?> - <?= clean($order["pincode"]); ?></p>
    </div>

    <table class="order-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= clean($item["name"]); ?></td>
                    <td><?= clean($item["size"]); ?></td>
                    <td><?= (int)$item["qty"]; ?></td>
                    <td><?= money($item["price"]); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="order-total-line">
        <strong>Total: <?= money($order["total_amount"]); ?></strong>
    </div>
</section>

<?php include "../includes/footer.php"; ?>