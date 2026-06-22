<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../database/phase8-schema.php";

ensure_phase8_schema($conn);

$userId = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("SELECT products.*
                        FROM wishlist
                        INNER JOIN products ON wishlist.product_id = products.id
                        WHERE wishlist.user_id = ?
                        ORDER BY wishlist.id DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$products = $stmt->get_result();

$pageTitle = "Wishlist | LOVA DUSK";
include "../includes/header.php";
?>

<section class="page-hero">
    <p class="eyebrow">Saved Pieces</p>
    <h1>Wishlist</h1>
    <p>Your selected LOVA DUSK fashion pieces.</p>
</section>

<section class="featured-section">
    <div class="product-grid">
        <?php if ($products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()): ?>
                <article class="product-card real-card">
                    <a href="../product.php?id=<?= $product["id"]; ?>">
                        <?php if ($product["main_image"]): ?>
                            <img src="<?= BASE_URL . clean($product["main_image"]); ?>" alt="<?= clean($product["name"]); ?>">
                        <?php else: ?>
                            <div class="product-image"></div>
                        <?php endif; ?>

                        <h3><?= clean($product["name"]); ?></h3>
                        <p><?= $product["sale_price"] ? money($product["sale_price"]) : money($product["price"]); ?></p>
                    </a>

                    <form method="POST" action="<?= BASE_URL; ?>ajax/remove-wishlist.php">
                        <?= csrf_input(); ?>
                        <input type="hidden" name="product_id" value="<?= $product["id"]; ?>">
                        <button type="submit" class="clear-cart-btn">Remove</button>
                    </form>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-cart">
                <h2>No wishlist items</h2>
                <a href="<?= BASE_URL; ?>shop.php" class="btn btn-dark">Shop Now</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include "../includes/footer.php"; ?>