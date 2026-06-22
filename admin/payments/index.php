<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../database/phase5-schema.php";

ensure_phase5_schema($conn);

$sql = "SELECT payments.*, orders.order_number, orders.customer_name, orders.total_amount, orders.payment_status
        FROM payments
        INNER JOIN orders ON payments.order_id = orders.id
        ORDER BY payments.id DESC";

$payments = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payments | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="../drops/index.php">Drops</a>
    <a href="../products/index.php">Products</a>
    <a href="../orders/index.php">Orders</a>
    <a href="index.php">Payments</a>
    <a href="../logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Payment Records</p>
            <h1>Payments</h1>
        </div>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Razorpay Order</th>
                    <th>Payment ID</th>
                    <th>Status</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($payments && $payments->num_rows > 0): ?>
                    <?php while ($payment = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><?= clean($payment["order_number"]); ?></td>
                            <td><?= clean($payment["customer_name"] ?? ""); ?></td>
                            <td><?= money($payment["total_amount"]); ?></td>
                            <td><?= clean($payment["razorpay_order_id"] ?? ""); ?></td>
                            <td><?= clean($payment["razorpay_payment_id"] ?? "Pending"); ?></td>
                            <td><?= clean($payment["status"]); ?></td>
                            <td><a href="view.php?id=<?= $payment["id"]; ?>">View</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No payment records yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>