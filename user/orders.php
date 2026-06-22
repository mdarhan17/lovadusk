<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../database/phase5-schema.php";

ensure_phase5_schema($conn);

$userId = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$orders = $stmt->get_result();

$pageTitle = "My Orders | LOVA DUSK";
include "../includes/header.php";
?>

<section class="page-hero">
    <p class="eyebrow">Order History</p>
    <h1>My Orders</h1>
    <p>Track your LOVA DUSK purchases and invoices.</p>
</section>

<section class="orders-section">
    <?php if ($orders->num_rows > 0): ?>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Details</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($order["order_number"]); ?></td>
                        <td><?= money($order["total_amount"]); ?></td>
                        <td><?= clean($order["payment_status"]); ?></td>
                        <td><?= clean($order["order_status"]); ?></td>
                        <td><?= clean($order["created_at"]); ?></td>
                        <td><a href="order-details.php?order=<?= urlencode($order["order_number"]); ?>">View</a></td>
                        <td><a href="invoice.php?order=<?= urlencode($order["order_number"]); ?>">Invoice</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-cart">
            <h2>No orders yet</h2>
            <a href="<?= BASE_URL; ?>shop.php" class="btn btn-dark">Shop Now</a>
        </div>
    <?php endif; ?>
</section>

<?php include "../includes/footer.php"; ?>