<?php
$pageTitle = "Shop | LOVA DUSK";
include "includes/header.php";

$sql = "SELECT products.*, drops.title AS drop_title
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
        <?php if ($products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()): ?>
                <article class="product-card real-card">
                    <a href="product.php?id=<?= $product["id"]; ?>">
                        <?php if ($product["main_image"]): ?>
                            <img src="<?= BASE_URL . clean($product["main_image"]); ?>" alt="<?= clean($product["name"]); ?>">
                        <?php else: ?>
                            <div class="product-image"></div>
                        <?php endif; ?>

                        <h3><?= clean($product["name"]); ?></h3>
                        <p class="drop-name"><?= clean($product["drop_title"] ?? "LOVA DUSK"); ?></p>

                        <?php if ($product["sale_price"]): ?>
                            <p><span class="old-price"><?= money($product["price"]); ?></span> <?= money($product["sale_price"]); ?></p>
                        <?php else: ?>
                            <p><?= money($product["price"]); ?></p>
                        <?php endif; ?>
                    </a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products added yet. Add products from admin panel.</p>
        <?php endif; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>