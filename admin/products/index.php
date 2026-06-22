<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";

$sql = "SELECT products.*, drops.title AS drop_title
        FROM products
        LEFT JOIN drops ON products.drop_id = drops.id
        ORDER BY products.id DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Products | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="../drops/index.php">Drops</a>
    <a href="index.php">Products</a>
    <a href="../orders/index.php">Orders</a>
    <a href="../logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Fashion Inventory</p>
            <h1>Products</h1>
        </div>
        <a href="add.php" class="btn btn-dark">Add Product</a>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Drop</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if ($product["main_image"]): ?>
                                <img src="<?= BASE_URL . clean($product["main_image"]); ?>" class="table-img">
                            <?php else: ?>
                                No image
                            <?php endif; ?>
                        </td>
                        <td><?= clean($product["name"]); ?></td>
                        <td><?= clean($product["drop_title"] ?? "No drop"); ?></td>
                        <td><?= money($product["price"]); ?></td>
                        <td><?= (int)$product["stock"]; ?></td>
                        <td><?= clean($product["status"]); ?></td>
                        <td class="table-actions">
                            <a href="edit.php?id=<?= $product["id"]; ?>">Edit</a>
                            <form method="POST" action="delete.php" onsubmit="return confirm('Delete this product?');">
                                <input type="hidden" name="id" value="<?= $product["id"]; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>