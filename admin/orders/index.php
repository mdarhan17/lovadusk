<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../database/phase5-schema.php";

ensure_phase5_schema($conn);

$orders = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Orders | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="../drops/index.php">Drops</a>
    <a href="../products/index.php">Products</a>
    <a href="index.php">Orders</a>
    <a href="../logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Order Management</p>
            <h1>Orders</h1>
        </div>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($order["order_number"]); ?></td>
                        <td><?= clean($order["customer_name"] ?? "Customer"); ?></td>
                        <td><?= money($order["total_amount"]); ?></td>
                        <td><?= clean($order["payment_status"]); ?></td>
                        <td><?= clean($order["order_status"]); ?></td>
                        <td><?= clean($order["created_at"]); ?></td>
                        <td><a href="view.php?id=<?= $order["id"]; ?>">View</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>