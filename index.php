<?php
require_once "config/constants.php";
require_once "config/db.php";
require_once "includes/functions.php";
require_once "database/phase11-schema.php";
require_once "database/phase18-schema.php";

ensure_phase11_schema($conn);
ensure_phase18_schema($conn);

$pageTitle = get_setting($conn, "site_name", "LOVA DUSK") . " | Luxury Fashion Drops";

$heroProduct = $conn->query("SELECT * FROM products WHERE status = 'active' AND main_image IS NOT NULL AND main_image != '' ORDER BY id DESC LIMIT 1");
$heroItem = $heroProduct ? $heroProduct->fetch_assoc() : null;

$newArrivals = $conn->query("SELECT * FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 6");

$bestSellers = $conn->query("SELECT products.*, COALESCE(SUM(order_items.qty),0) AS sold_qty
                             FROM products
                             LEFT JOIN order_items ON products.id = order_items.product_id
                             WHERE products.status = 'active'
                             GROUP BY products.id
                             ORDER BY sold_qty DESC, products.id DESC
                             LIMIT 6");

$categories = $conn->query("SELECT categories.*, COUNT(products.id) AS product_count
                            FROM categories
                            LEFT JOIN products ON categories.id = products.category_id
                            WHERE categories.status = 'active'
                            GROUP BY categories.id
                            ORDER BY categories.id DESC
                            LIMIT 6");

$instagram = $conn->query("SELECT * FROM instagram_gallery WHERE status = 'active' ORDER BY id DESC LIMIT 6");

include "includes/header.php";
?>

<section class="editorial-hero">
    <div class="editorial-copy">
        <p class="eyebrow"><?= clean(get_setting($conn, "hero_eyebrow")); ?></p>
        <h1><?= clean(get_setting($conn, "hero_title")); ?></h1>
        <p><?= clean(get_setting($conn, "hero_subtitle")); ?></p>

        <div class="hero-actions">
            <a href="shop.php" class="btn btn-dark"><?= clean(get_setting($conn, "hero_button_primary")); ?></a>
            <a href="drops/" class="btn btn-light"><?= clean(get_setting($conn, "hero_button_secondary")); ?></a>
        </div>
    </div>

    <div class="editorial-visual">
        <?php if ($heroItem && $heroItem["main_image"]): ?>
            <img src="<?= BASE_URL . clean($heroItem["main_image"]); ?>" alt="<?= clean($heroItem["name"]); ?>">
        <?php else: ?>
            <div class="editorial-placeholder">
                <span>LOVA</span>
                <span>DUSK</span>
            </div>
        <?php endif; ?>

        <div class="floating-label">
            <span>Hero Banner</span>
            <strong><?= $heroItem ? clean($heroItem["name"]) : "New Arrival Coming Soon"; ?></strong>
        </div>
    </div>
</section>

<section class="modern-strip">
    <span>Limited Drops</span>
    <span>Premium Silhouettes</span>
    <span>Rich Brown Theme</span>
    <span>Quiet Luxury</span>
</section>

<section class="modern-featured">
    <div class="section-heading modern-heading">
        <p>New Arrival</p>
        <h2><?= clean(get_setting($conn, "featured_title")); ?></h2>
    </div>

    <div class="modern-product-grid">
        <?php if ($newArrivals && $newArrivals->num_rows > 0): ?>
            <?php while ($product = $newArrivals->fetch_assoc()): ?>
                <article class="modern-product-card">
                    <a href="product.php?id=<?= $product["id"]; ?>">
                        <div class="modern-product-image">
                            <?php if ($product["main_image"]): ?>
                                <img src="<?= BASE_URL . clean($product["main_image"]); ?>" alt="<?= clean($product["name"]); ?>">
                            <?php else: ?>
                                <div class="product-image"></div>
                            <?php endif; ?>
                        </div>
                        <div class="modern-product-info">
                            <h3><?= clean($product["name"]); ?></h3>
                            <p><?= $product["sale_price"] ? money($product["sale_price"]) : money($product["price"]); ?></p>
                        </div>
                    </a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products added yet.</p>
        <?php endif; ?>
    </div>
</section>

<section class="modern-featured best-sellers-section">
    <div class="section-heading modern-heading">
        <p>Most Loved</p>
        <h2><?= clean(get_setting($conn, "best_sellers_title", "Best Sellers")); ?></h2>
    </div>

    <div class="modern-product-grid compact-grid">
        <?php if ($bestSellers && $bestSellers->num_rows > 0): ?>
            <?php while ($product = $bestSellers->fetch_assoc()): ?>
                <article class="modern-product-card">
                    <a href="product.php?id=<?= $product["id"]; ?>">
                        <div class="modern-product-image compact-image">
                            <?php if ($product["main_image"]): ?>
                                <img src="<?= BASE_URL . clean($product["main_image"]); ?>" alt="<?= clean($product["name"]); ?>">
                            <?php else: ?>
                                <div class="product-image"></div>
                            <?php endif; ?>
                        </div>
                        <div class="modern-product-info">
                            <h3><?= clean($product["name"]); ?></h3>
                            <p><?= $product["sale_price"] ? money($product["sale_price"]) : money($product["price"]); ?></p>
                        </div>
                    </a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No best sellers yet.</p>
        <?php endif; ?>
    </div>
</section>

<section class="category-collection">
    <div class="section-heading">
        <p>Shop By Mood</p>
        <h2><?= clean(get_setting($conn, "category_title", "Category Collection")); ?></h2>
    </div>

    <div class="category-grid">
        <?php if ($categories && $categories->num_rows > 0): ?>
            <?php while ($category = $categories->fetch_assoc()): ?>
                <a href="shop.php?category=<?= urlencode($category["slug"]); ?>" class="category-card">
                    <span><?= (int)$category["product_count"]; ?> Products</span>
                    <h3><?= clean($category["name"]); ?></h3>
                    <p>Explore collection</p>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No categories added yet.</p>
        <?php endif; ?>
    </div>
</section>

<section class="aesthetic-story">
    <div>
        <p class="eyebrow">Brand Story</p>
        <h2><?= clean(get_setting($conn, "about_title")); ?></h2>
    </div>

    <p><?= nl2br(clean(get_setting($conn, "about_content"))); ?></p>

    <a href="about.php" class="btn btn-light">Read Brand Story</a>
</section>

<section class="instagram-gallery-section">
    <div class="section-heading">
        <p>Social Mood</p>
        <h2><?= clean(get_setting($conn, "instagram_title", "Instagram Gallery")); ?></h2>
    </div>

    <div class="instagram-grid">
        <?php if ($instagram && $instagram->num_rows > 0): ?>
            <?php while ($post = $instagram->fetch_assoc()): ?>
                <a href="<?= clean($post["instagram_link"] ?: get_setting($conn, "instagram_link", "#")); ?>" target="_blank" class="instagram-card">
                    <img src="<?= BASE_URL . clean($post["image"]); ?>" alt="<?= clean($post["caption"] ?: "LOVA DUSK Instagram"); ?>">
                    <span><?= clean($post["caption"] ?: "View on Instagram"); ?></span>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="instagram-card empty-insta"><span>Instagram post 01</span></div>
            <div class="instagram-card empty-insta"><span>Instagram post 02</span></div>
            <div class="instagram-card empty-insta"><span>Instagram post 03</span></div>
            <div class="instagram-card empty-insta"><span>Instagram post 04</span></div>
        <?php endif; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>