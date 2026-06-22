<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";
require_once "../../database/phase5-schema.php";

ensure_phase5_schema($conn);

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

$itemStmt = $conn->prepare("SELECT order_items.*, products.name
                            FROM order_items
                            INNER JOIN products ON order_items.product_id = products.id
                            WHERE order_items.order_id = ?");
$itemStmt->bind_param("i", $id);
$itemStmt->execute();
$items = $itemStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order View | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Orders</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Order Details</p>
            <h1><?= clean($order["order_number"]); ?></h1>
        </div>
        <a href="index.php" class="btn btn-light">Back</a>
    </div>

    <section class="admin-panel">
        <h2>Customer</h2>
        <p><?= clean($order["customer_name"]); ?> | <?= clean($order["customer_phone"]); ?> | <?= clean($order["customer_email"]); ?></p>
        <p><?= clean($order["address_line"]); ?>, <?= clean($order["city"]); ?>, <?= clean($order["state"]); ?> - <?= clean($order["pincode"]); ?></p>

        <form method="POST" action="update-status.php" class="admin-form status-form">
            <?= csrf_input(); ?>
            <input type="hidden" name="id" value="<?= $order["id"]; ?>">

            <label>Order Status</label>
            <select name="order_status">
                <option value="placed" <?= $order["order_status"] === "placed" ? "selected" : ""; ?>>Placed</option>
                <option value="processing" <?= $order["order_status"] === "processing" ? "selected" : ""; ?>>Processing</option>
                <option value="shipped" <?= $order["order_status"] === "shipped" ? "selected" : ""; ?>>Shipped</option>
                <option value="delivered" <?= $order["order_status"] === "delivered" ? "selected" : ""; ?>>Delivered</option>
                <option value="cancelled" <?= $order["order_status"] === "cancelled" ? "selected" : ""; ?>>Cancelled</option>
            </select>

            <label>Payment Status</label>
            <select name="payment_status">
                <option value="pending" <?= $order["payment_status"] === "pending" ? "selected" : ""; ?>>Pending</option>
                <option value="paid" <?= $order["payment_status"] === "paid" ? "selected" : ""; ?>>Paid</option>
                <option value="failed" <?= $order["payment_status"] === "failed" ? "selected" : ""; ?>>Failed</option>
                <option value="refunded" <?= $order["payment_status"] === "refunded" ? "selected" : ""; ?>>Refunded</option>
            </select>

            <button type="submit" class="btn btn-dark">Update Status</button>
        </form>
    </section>

    <section class="admin-panel">
        <h2>Items</h2>
        <table class="admin-table">
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

        <h2>Total: <?= money($order["total_amount"]); ?></h2>
    </section>
</main>
</body>
</html>