<?php
require_once "../includes/admin-check.php";
require_once "../config/db.php";
require_once "../includes/logger.php";
require_once "../database/phase5-schema.php";
require_once "../database/phase8-schema.php";
require_once "../database/phase9-schema.php";

ensure_phase5_schema($conn);
ensure_phase8_schema($conn);
ensure_phase9_schema($conn);

admin_activity($conn, "Admin dashboard viewed", "Dashboard opened.");

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'user'")->fetch_assoc()["total"] ?? 0;
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()["total"] ?? 0;
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()["total"] ?? 0;
$totalRevenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) AS total FROM orders WHERE payment_status = 'paid'")->fetch_assoc()["total"] ?? 0;
$pendingOrders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE order_status IN ('placed','processing')")->fetch_assoc()["total"] ?? 0;
$lowStock = $conn->query("SELECT COUNT(*) AS total FROM products WHERE stock <= 5")->fetch_assoc()["total"] ?? 0;
$newMessages = $conn->query("SELECT COUNT(*) AS total FROM contact_messages WHERE status = 'new'")->fetch_assoc()["total"] ?? 0;
$pendingReviews = $conn->query("SELECT COUNT(*) AS total FROM product_reviews WHERE status = 'pending'")->fetch_assoc()["total"] ?? 0;

$latestOrders = $conn->query("SELECT order_number, customer_name, total_amount, payment_status, order_status, created_at FROM orders ORDER BY id DESC LIMIT 6");
$lowStockProducts = $conn->query("SELECT name, stock FROM products WHERE stock <= 5 ORDER BY stock ASC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard | LOVA DUSK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>

<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="drops/index.php">Drops</a>
    <a href="products/index.php">Products</a>
    <a href="orders/index.php">Orders</a>
    <a href="payments/index.php">Payments</a>
    <a href="coupons/index.php">Coupons</a>
    <a href="reviews/index.php">Reviews</a>
    <a href="contact-messages/index.php">Messages</a>
    <a href="reports/sales.php">Reports</a>
    <a href="logs/index.php">Logs</a>
    <a href="tools/backup.php">Backup</a>
    <a href="logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Control Center</p>
            <h1>Admin Dashboard</h1>
        </div>
        <p>Welcome, <?= clean($_SESSION["admin_name"] ?? "Admin"); ?></p>
    </div>

    <div class="admin-stats">
        <div class="stat-card"><h3><?= money($totalRevenue); ?></h3><p>Total Revenue</p></div>
        <div class="stat-card"><h3><?= (int)$totalOrders; ?></h3><p>Total Orders</p></div>
        <div class="stat-card"><h3><?= (int)$totalUsers; ?></h3><p>Customers</p></div>
        <div class="stat-card"><h3><?= (int)$totalProducts; ?></h3><p>Products</p></div>
        <div class="stat-card"><h3><?= (int)$pendingOrders; ?></h3><p>Pending Orders</p></div>
        <div class="stat-card"><h3><?= (int)$lowStock; ?></h3><p>Low Stock</p></div>
        <div class="stat-card"><h3><?= (int)$newMessages; ?></h3><p>New Messages</p></div>
        <div class="stat-card"><h3><?= (int)$pendingReviews; ?></h3><p>Pending Reviews</p></div>
    </div>

    <div class="admin-two-col">
        <section class="admin-panel">
            <h2>Latest Orders</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $latestOrders->fetch_assoc()): ?>
                        <tr>
                            <td><?= clean($order["order_number"]); ?></td>
                            <td><?= clean($order["customer_name"] ?? ""); ?></td>
                            <td><?= money($order["total_amount"]); ?></td>
                            <td><?= clean($order["payment_status"]); ?></td>
                            <td><?= clean($order["order_status"]); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section class="admin-panel">
            <h2>Low Stock Alerts</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($lowStockProducts && $lowStockProducts->num_rows > 0): ?>
                        <?php while ($product = $lowStockProducts->fetch_assoc()): ?>
                            <tr>
                                <td><?= clean($product["name"]); ?></td>
                                <td><?= (int)$product["stock"]; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2">No low stock products.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</main>

</body>
</html>