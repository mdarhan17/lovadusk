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

$pageTitle = "Order Success | LOVA DUSK";
include "../includes/header.php";
?>

<section class="success-section">
    <?php if ($order): ?>
        <p class="eyebrow">Order Confirmed</p>
        <h1>Thank You</h1>
        <p>Your LOVA DUSK order has been placed successfully.</p>

        <div class="success-box">
            <p><strong>Order Number:</strong> <?= clean($order["order_number"]); ?></p>
            <p><strong>Total:</strong> <?= money($order["total_amount"]); ?></p>
            <p><strong>Payment:</strong> <?= clean($order["payment_status"]); ?></p>
            <p><strong>Status:</strong> <?= clean($order["order_status"]); ?></p>
        </div>

        <a href="<?= BASE_URL; ?>user/orders.php" class="btn btn-dark">View My Orders</a>
    <?php else: ?>
        <h1>Order not found</h1>
        <a href="<?= BASE_URL; ?>shop.php" class="btn btn-dark">Back to Shop</a>
    <?php endif; ?>
</section>

<?php include "../includes/footer.php"; ?>