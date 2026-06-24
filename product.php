<?php
require_once "config/constants.php";
require_once "config/db.php";
require_once "includes/functions.php";
require_once "includes/csrf.php";
require_once "database/phase9-schema.php";

ensure_phase9_schema($conn);

function ld_table_exists_product($conn, $table) {
    $table = $conn->real_escape_string($table);
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

function ld_column_exists_product($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

/* Gallery table support */
$conn->query("CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    is_main TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$id = (int)($_GET["id"] ?? 0);

/* Drop column safe handling */
$dropTitleColumn = "drops.id";

if (ld_column_exists_product($conn, "drops", "name")) {
    $dropTitleColumn = "drops.name";
} elseif (ld_column_exists_product($conn, "drops", "title")) {
    $dropTitleColumn = "drops.title";
}

$stmt = $conn->prepare("SELECT products.*, $dropTitleColumn AS drop_title
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

/* Sizes */
$sizeStmt = $conn->prepare("SELECT size, stock FROM product_sizes WHERE product_id = ? ORDER BY FIELD(size, 'S','M','L','XL')");
$sizeStmt->bind_param("i", $id);
$sizeStmt->execute();
$sizes = $sizeStmt->get_result();

/* Gallery images */
$galleryImages = [];

if (ld_table_exists_product($conn, "product_images")) {
    $galleryStmt = $conn->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY is_main DESC, sort_order ASC, id ASC");
    $galleryStmt->bind_param("i", $id);
    $galleryStmt->execute();
    $galleryResult = $galleryStmt->get_result();

    while ($img = $galleryResult->fetch_assoc()) {
        if (!empty($img["image"])) {
            $galleryImages[] = $img["image"];
        }
    }
}

if (empty($galleryImages) && !empty($product["main_image"])) {
    $galleryImages[] = $product["main_image"];
}

$mainImage = !empty($galleryImages) ? $galleryImages[0] : "";

/* Reviews */
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

<section class="product-detail premium-product-detail">
    <div class="product-gallery-box">
        <div class="product-main-image">
            <?php if ($mainImage): ?>
                <img id="mainProductImage" src="<?= BASE_URL . clean($mainImage); ?>" alt="<?= clean($product["name"]); ?>">
            <?php else: ?>
                <div class="product-image large"></div>
            <?php endif; ?>
        </div>

        <?php if (count($galleryImages) > 1): ?>
            <div class="product-thumbnails">
                <?php foreach ($galleryImages as $index => $img): ?>
                    <button type="button" class="thumb-btn <?= $index === 0 ? 'active' : ''; ?>" data-image="<?= BASE_URL . clean($img); ?>">
                        <img src="<?= BASE_URL . clean($img); ?>" alt="<?= clean($product["name"]); ?> image <?= $index + 1; ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="product-detail-info">
        <p class="eyebrow"><?= clean($product["drop_title"] ?? "LOVA DUSK"); ?></p>
        <h1><?= clean($product["name"]); ?></h1>

        <?php if (!empty($product["sale_price"])): ?>
            <p class="price">
                <span class="old-price"><?= money($product["price"]); ?></span>
                <?= money($product["sale_price"]); ?>
            </p>
        <?php else: ?>
            <p class="price"><?= money($product["price"]); ?></p>
        <?php endif; ?>

        <div class="product-info-tags">
            <div>
                <span>Fabric</span>
                <strong>Khadi Cotton</strong>
            </div>
            <div>
                <span>Dress Size</span>
                <strong>S M L XL</strong>
            </div>
            <div>
                <span>Collection</span>
                <strong><?= clean($product["drop_title"] ?? "Drop 1"); ?></strong>
            </div>
        </div>

        <p class="detail-text">
            <?= nl2br(clean($product["description"] ?? "Premium LOVA DUSK fashion piece.")); ?>
        </p>

        <form method="POST" action="cart/add.php" class="product-buy-form">
            <?= csrf_input(); ?>
            <input type="hidden" name="product_id" value="<?= $product["id"]; ?>">

            <label>Select Size</label>
            <div class="size-options">
                <?php if ($sizes->num_rows > 0): ?>
                    <?php while ($size = $sizes->fetch_assoc()): ?>
                        <label class="<?= (int)$size["stock"] <= 0 ? "disabled" : ""; ?>">
                            <input type="radio" name="size" value="<?= clean($size["size"]); ?>" <?= (int)$size["stock"] <= 0 ? "disabled" : ""; ?> required>
                            <span><?= clean($size["size"]); ?></span>
                        </label>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="detail-text">Sizes not available.</p>
                <?php endif; ?>
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

<script>
document.querySelectorAll(".thumb-btn").forEach(function(button) {
    button.addEventListener("click", function() {
        const image = this.getAttribute("data-image");
        const mainImage = document.getElementById("mainProductImage");

        if (mainImage && image) {
            mainImage.src = image;
        }

        document.querySelectorAll(".thumb-btn").forEach(function(btn) {
            btn.classList.remove("active");
        });

        this.classList.add("active");
    });
});
</script>

<?php include "includes/footer.php"; ?>
