<?php
$pageTitle = "Shop | LOVA DUSK";
include "includes/header.php";

function shop_column_exists($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

$dropTitleColumn = "drops.id";

if (shop_column_exists($conn, "drops", "name")) {
    $dropTitleColumn = "drops.name";
} elseif (shop_column_exists($conn, "drops", "title")) {
    $dropTitleColumn = "drops.title";
}

$sql = "SELECT products.*, $dropTitleColumn AS drop_title
        FROM products
        LEFT JOIN drops ON products.drop_id = drops.id
        WHERE products.status = 'active'
        ORDER BY products.id DESC";

$products = $conn->query($sql);
?>

<section class="page-hero">
    <p class="eyebrow">Shop The Drop</p>
    <h1>Luxury Fashion Pieces</h1>
    <p>Limited silhouettes designed for modern minimal wardrobes.</p>
</section>

<section class="featured-section">
    <div class="product-grid">
        <?php if ($products && $products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()): ?>
                <article class="product-card real-card">
                    <a href="product.php?id=<?= $product["id"]; ?>">
                        <?php if (!empty($product["main_image"])): ?>
                            <img src="<?= BASE_URL . clean($product["main_image"]); ?>" alt="<?= clean($product["name"]); ?>">
                        <?php else: ?>
                            <div class="product-image"></div>
                        <?php endif; ?>

                        <h3><?= clean($product["name"]); ?></h3>
                        <p class="drop-name"><?= clean($product["drop_title"] ?? "LOVA DUSK"); ?></p>

                        <?php if (!empty($product["sale_price"])): ?>
                            <p>
                                <span class="old-price"><?= money($product["price"]); ?></span>
                                <?= money($product["sale_price"]); ?>
                            </p>
                        <?php else: ?>
                            <p><?= money($product["price"]); ?></p>
                        <?php endif; ?>

                        <span class="view-product-link">View Product</span>
                    </a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-drop-box">
                <h3>No products added yet.</h3>
                <p>Add products from admin panel or setup script.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>
