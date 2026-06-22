<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../database/phase5-schema.php";

ensure_phase5_schema($conn);

$sql = "SELECT users.id, users.name, users.email, users.phone, users.created_at,
               COUNT(orders.id) AS total_orders,
               COALESCE(SUM(CASE WHEN orders.payment_status = 'paid' THEN orders.total_amount ELSE 0 END),0) AS total_spent
        FROM users
        LEFT JOIN orders ON users.id = orders.user_id
        WHERE users.role = 'user'
        GROUP BY users.id
        ORDER BY total_spent DESC";

$customers = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customer Report | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="sales.php">Sales Report</a>
    <a href="customers.php">Customer Report</a>
    <a href="inventory.php">Inventory Report</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Customer Insights</p>
            <h1>Customer Report</h1>
        </div>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($customer = $customers->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($customer["name"]); ?></td>
                        <td><?= clean($customer["email"]); ?></td>
                        <td><?= clean($customer["phone"] ?? ""); ?></td>
                        <td><?= (int)$customer["total_orders"]; ?></td>
                        <td><?= money($customer["total_spent"]); ?></td>
                        <td><?= clean($customer["created_at"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>