<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../database/phase8-schema.php";

ensure_phase8_schema($conn);

$coupons = $conn->query("SELECT * FROM coupons ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Coupons | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="../products/index.php">Products</a>
    <a href="../orders/index.php">Orders</a>
    <a href="index.php">Coupons</a>
    <a href="../logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Discount Control</p>
            <h1>Coupons</h1>
        </div>
        <a href="add.php" class="btn btn-dark">Add Coupon</a>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min Order</th>
                    <th>Used</th>
                    <th>Status</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($coupon = $coupons->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($coupon["code"]); ?></td>
                        <td><?= clean($coupon["discount_type"]); ?></td>
                        <td><?= clean($coupon["discount_value"]); ?></td>
                        <td><?= money($coupon["min_order"]); ?></td>
                        <td><?= (int)$coupon["used_count"]; ?> / <?= (int)$coupon["usage_limit"]; ?></td>
                        <td><?= clean($coupon["status"]); ?></td>
                        <td><a href="edit.php?id=<?= $coupon["id"]; ?>">Edit</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>