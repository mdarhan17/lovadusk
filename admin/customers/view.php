<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'user' LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

if (!$customer) {
    die("Customer not found.");
}

$orderStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$orderStmt->bind_param("i", $id);
$orderStmt->execute();
$orders = $orderStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customer View | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Customers</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Customer Details</p>
            <h1><?= clean($customer["name"]); ?></h1>
        </div>
        <a href="index.php" class="btn btn-light">Back</a>
    </div>

    <section class="admin-panel">
        <p><strong>Email:</strong> <?= clean($customer["email"]); ?></p>
        <p><strong>Phone:</strong> <?= clean($customer["phone"] ?? ""); ?></p>
        <p><strong>Status:</strong> <?= clean($customer["status"]); ?></p>
    </section>

    <section class="admin-panel">
        <h2>Orders</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($order["order_number"]); ?></td>
                        <td><?= money($order["total_amount"]); ?></td>
                        <td><?= clean($order["payment_status"]); ?></td>
                        <td><?= clean($order["order_status"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>