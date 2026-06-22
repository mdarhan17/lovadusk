<?php
require_once "config/constants.php";
require_once "config/db.php";
require_once "includes/functions.php";
require_once "database/phase5-schema.php";

ensure_phase5_schema($conn);

$pageTitle = "Track Order | LOVA DUSK";
$message = "";
$order = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderNumber = trim($_POST["order_number"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ? AND customer_phone = ? LIMIT 1");
    $stmt->bind_param("ss", $orderNumber, $phone);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) {
        $message = "No matching order found.";
    }
}

include "includes/header.php";
?>

<section class="auth-section">
    <div class="auth-card">
        <p class="eyebrow">Track Order</p>
        <h1>Order Status</h1>

        <?php if ($message): ?>
            <div class="alert"><?= clean($message); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label>Order Number</label>
            <input type="text" name="order_number" required>

            <label>Phone Number</label>
            <input type="text" name="phone" required>

            <button type="submit" class="btn btn-dark">Track</button>
        </form>

        <?php if ($order): ?>
            <div class="success-box">
                <p><strong>Order:</strong> <?= clean($order["order_number"]); ?></p>
                <p><strong>Payment:</strong> <?= clean($order["payment_status"]); ?></p>
                <p><strong>Status:</strong> <?= clean($order["order_status"]); ?></p>
                <p><strong>Total:</strong> <?= money($order["total_amount"]); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>