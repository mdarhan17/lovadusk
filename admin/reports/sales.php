<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../database/phase5-schema.php";

ensure_phase5_schema($conn);

$from = $_GET["from"] ?? date("Y-m-01");
$to = $_GET["to"] ?? date("Y-m-d");

$stmt = $conn->prepare("SELECT DATE(created_at) AS order_date,
                               COUNT(*) AS total_orders,
                               COALESCE(SUM(total_amount),0) AS revenue
                        FROM orders
                        WHERE DATE(created_at) BETWEEN ? AND ?
                        GROUP BY DATE(created_at)
                        ORDER BY order_date DESC");
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$sales = $stmt->get_result();

$totalStmt = $conn->prepare("SELECT COUNT(*) AS orders_count,
                                    COALESCE(SUM(total_amount),0) AS revenue
                             FROM orders
                             WHERE DATE(created_at) BETWEEN ? AND ? AND payment_status = 'paid'");
$totalStmt->bind_param("ss", $from, $to);
$totalStmt->execute();
$totals = $totalStmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sales Report | Admin</title>
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
            <p class="eyebrow">Business Report</p>
            <h1>Sales Report</h1>
        </div>
    </div>

    <section class="admin-panel">
        <form method="GET" class="report-filter">
            <input type="date" name="from" value="<?= clean($from); ?>">
            <input type="date" name="to" value="<?= clean($to); ?>">
            <button class="btn btn-dark">Filter</button>
        </form>

        <div class="admin-stats compact-stats">
            <div class="stat-card"><h3><?= (int)$totals["orders_count"]; ?></h3><p>Paid Orders</p></div>
            <div class="stat-card"><h3><?= money($totals["revenue"]); ?></h3><p>Paid Revenue</p></div>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $sales->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($row["order_date"]); ?></td>
                        <td><?= (int)$row["total_orders"]; ?></td>
                        <td><?= money($row["revenue"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>