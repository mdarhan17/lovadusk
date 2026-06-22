<?php
require_once "config/constants.php";
require_once "config/db.php";
require_once "includes/functions.php";

$q = trim($_GET["q"] ?? "");
$pageTitle = "Search | LOVA DUSK";

$products = null;

if ($q !== "") {
    $like = "%" . $q . "%";
    $stmt = $conn->prepare("SELECT * FROM products WHERE status = 'active' AND (name LIKE ? OR description LIKE ?) ORDER BY id DESC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $products = $stmt->get_result();
}

include "includes/header.php";
?>

<section class="page-hero">
    <p class="eyebrow">Search</p>
    <h1>Find Your Piece</h1>
    <p>Search products by name, fit or collection mood.</p>
</section>

<section class="auth-section">
    <div class="auth-card search-card">
        <form method="GET" class="auth-form">
            <label>Search Products</label>
            <input type="text" name="q" value="<?= clean($q); ?>" placeholder="Luna shirt, black set..." required>
            <button type="submit" class="btn btn-dark">Search</button>
        </form>
    </div>
</section>

<?php if ($q !== ""): ?>
<section class="featured-section">
    <div class="section-heading">
        <p>Search Results</p>
        <h2><?= clean($q); ?></h2>
    </div>

    <div class="product-grid">
        <?php if ($products && $products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()): ?>
                <article class="product-card real-card">
                    <a href="product.php?id=<?= $product["id"]; ?>">
                        <?php if ($product["main_image"]): ?>
                            <img src="<?= BASE_URL . clean($product["main_image"]); ?>" alt="<?= clean($product["name"]); ?>">
                        <?php else: ?>
                            <div class="product-image"></div>
                        <?php endif; ?>
                        <h3><?= clean($product["name"]); ?></h3>
                        <p><?= $product["sale_price"] ? money($product["sale_price"]) : money($product["price"]); ?></p>
                    </a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No matching products found.</p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php include "includes/footer.php"; ?>