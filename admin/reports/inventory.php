<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";

$sql = "SELECT products.id, products.name, products.stock, products.price, products.sale_price,
               drops.title AS drop_title,
               COALESCE(SUM(order_items.qty),0) AS sold_qty
        FROM products
        LEFT JOIN drops ON products.drop_id = drops.id
        LEFT JOIN order_items ON products.id = order_items.product_id
        GROUP BY products.id
        ORDER BY products.stock ASC";

$inventory = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Inventory Report | Admin</title>
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
            <p class="eyebrow">Stock Control</p>
            <h1>Inventory Report</h1>
        </div>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Drop</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Sold Qty</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $inventory->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($item["name"]); ?></td>
                        <td><?= clean($item["drop_title"] ?? "No drop"); ?></td>
                        <td><?= money($item["sale_price"] ?: $item["price"]); ?></td>
                        <td><?= (int)$item["stock"]; ?></td>
                        <td><?= (int)$item["sold_qty"]; ?></td>
                        <td>
                            <?php if ((int)$item["stock"] <= 0): ?>
                                Out of stock
                            <?php elseif ((int)$item["stock"] <= 5): ?>
                                Low stock
                            <?php else: ?>
                                In stock
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>