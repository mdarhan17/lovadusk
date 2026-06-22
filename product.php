<?php
require_once "config/constants.php";
require_once "config/db.php";
require_once "includes/functions.php";
require_once "includes/csrf.php";
require_once "database/phase9-schema.php";

ensure_phase9_schema($conn);

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT products.*, drops.title AS drop_title
                        FROM products
                        LEFT JOIN drops ON products.drop_id = drops.id
                        WHERE products.id = ? AND products.status = 'active'
                        LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

$pageTitle = $product["name"] . " | LOVA DUSK";

$sizeStmt = $conn->prepare("SELECT size, stock FROM product_sizes WHERE product_id = ? ORDER BY FIELD(size, 'S','M','L','XL')");
$sizeStmt->bind_param("i", $id);
$sizeStmt->execute();
$sizes = $sizeStmt->get_result();

$reviewStmt = $conn->prepare("SELECT product_reviews.*, users.name AS user_name
                              FROM product_reviews
                              INNER JOIN users ON product_reviews.user_id = users.id
                              WHERE product_reviews.product_id = ? AND product_reviews.status = 'approved'
                              ORDER BY product_reviews.id DESC");
$reviewStmt->bind_param("i", $id);
$reviewStmt->execute();
$reviews = $reviewStmt->get_result();

include "includes/header.php";
?>

<section class="product-detail">
    <div class="product-detail-image">
        <?php if ($product["main_image"]): ?>
            <img src="<?= BASE_URL . clean($product["main_image"]); ?>" alt="<?= clean($product["name"]); ?>">
        <?php else: ?>
            <div class="product-image large"></div>
        <?php endif; ?>
    </div>

    <div class="product-detail-info">
        <p class="eyebrow"><?= clean($product["drop_title"] ?? "LOVA DUSK"); ?></p>
        <h1><?= clean($product["name"]); ?></h1>

        <?php if ($product["sale_price"]): ?>
            <p class="price"><span class="old-price"><?= money($product["price"]); ?></span> <?= money($product["sale_price"]); ?></p>
        <?php else: ?>
            <p class="price"><?= money($product["price"]); ?></p>
        <?php endif; ?>

        <p class="detail-text"><?= nl2br(clean($product["description"] ?? "Premium LOVA DUSK fashion piece.")); ?></p>

        <form method="POST" action="cart/add.php" class="product-buy-form">
            <?= csrf_input(); ?>
            <input type="hidden" name="product_id" value="<?= $product["id"]; ?>">

            <label>Select Size</label>
            <div class="size-options">
                <?php while ($size = $sizes->fetch_assoc()): ?>
                    <label class="<?= (int)$size["stock"] <= 0 ? "disabled" : ""; ?>">
                        <input type="radio" name="size" value="<?= clean($size["size"]); ?>" <?= (int)$size["stock"] <= 0 ? "disabled" : ""; ?> required>
                        <span><?= clean($size["size"]); ?></span>
                    </label>
                <?php endwhile; ?>
            </div>

            <label>Quantity</label>
            <input type="number" name="qty" min="1" value="1">

            <button type="submit" class="btn btn-dark">Add To Cart</button>
        </form>

        <?php if (isLoggedIn()): ?>
            <form method="POST" action="<?= BASE_URL; ?>ajax/add-to-wishlist.php" class="wishlist-form">
                <?= csrf_input(); ?>
                <input type="hidden" name="product_id" value="<?= $product["id"]; ?>">
                <button type="submit" class="btn btn-light">Add To Wishlist</button>
            </form>
        <?php else: ?>
            <a href="<?= BASE_URL; ?>auth/login.php" class="btn btn-light wishlist-login-btn">Login To Save Wishlist</a>
        <?php endif; ?>

        <div class="product-extra">
            <p>Free shipping on prepaid orders.</p>
            <p><a href="size-guide.php">View size guide</a></p>
        </div>
    </div>
</section>

<section class="reviews-section">
    <div class="reviews-layout">
        <div>
            <p class="eyebrow">Customer Reviews</p>
            <h2>Reviews & Ratings</h2>

            <?php if ($reviews->num_rows > 0): ?>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <div class="review-card">
                        <strong><?= clean($review["user_name"]); ?></strong>
                        <p><?= str_repeat("★", (int)$review["rating"]); ?><?= str_repeat("☆", 5 - (int)$review["rating"]); ?></p>
                        <p><?= clean($review["review"]); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="detail-text">No approved reviews yet.</p>
            <?php endif; ?>
        </div>

        <div class="review-form-box">
            <?php if (isLoggedIn()): ?>
                <h3>Write a Review</h3>
                <form method="POST" action="<?= BASE_URL; ?>ajax/submit-review.php" class="auth-form">
                    <?= csrf_input(); ?>
                    <input type="hidden" name="product_id" value="<?= $product["id"]; ?>">

                    <label>Rating</label>
                    <select name="rating" required>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Bad</option>
                    </select>

                    <label>Review</label>
                    <textarea name="review" required></textarea>

                    <button type="submit" class="btn btn-dark">Submit Review</button>
                </form>
                <p class="detail-text">Review will show after admin approval.</p>
            <?php else: ?>
                <h3>Login to review</h3>
                <a href="<?= BASE_URL; ?>auth/login.php" class="btn btn-dark">Login</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>