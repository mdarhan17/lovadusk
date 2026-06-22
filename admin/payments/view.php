<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../database/phase5-schema.php";

ensure_phase5_schema($conn);

$id = (int)($_GET["id"] ?? 0);

$sql = "SELECT payments.*, orders.order_number, orders.customer_name, orders.customer_email,
               orders.customer_phone, orders.total_amount, orders.payment_status, orders.order_status
        FROM payments
        INNER JOIN orders ON payments.order_id = orders.id
        WHERE payments.id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();

if (!$payment) {
    die("Payment not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment Details | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Payments</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Payment Details</p>
            <h1><?= clean($payment["order_number"]); ?></h1>
        </div>
        <a href="index.php" class="btn btn-light">Back</a>
    </div>

    <section class="admin-panel payment-detail-grid">
        <div>
            <h2>Customer</h2>
            <p><strong>Name:</strong> <?= clean($payment["customer_name"]); ?></p>
            <p><strong>Email:</strong> <?= clean($payment["customer_email"]); ?></p>
            <p><strong>Phone:</strong> <?= clean($payment["customer_phone"]); ?></p>
        </div>

        <div>
            <h2>Payment</h2>
            <p><strong>Amount:</strong> <?= money($payment["total_amount"]); ?></p>
            <p><strong>Payment Status:</strong> <?= clean($payment["payment_status"]); ?></p>
            <p><strong>Order Status:</strong> <?= clean($payment["order_status"]); ?></p>
            <p><strong>Record Status:</strong> <?= clean($payment["status"]); ?></p>
        </div>

        <div>
            <h2>Razorpay</h2>
            <p><strong>Order ID:</strong> <?= clean($payment["razorpay_order_id"] ?? ""); ?></p>
            <p><strong>Payment ID:</strong> <?= clean($payment["razorpay_payment_id"] ?? "Pending"); ?></p>
            <p><strong>Signature:</strong> <?= clean($payment["razorpay_signature"] ?? "Pending"); ?></p>
        </div>
    </section>
</main>
</body>
</html>