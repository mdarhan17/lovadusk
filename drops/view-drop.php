<?php
include "../includes/header.php";

function ld_drop_column_exists($conn, $column) {
    $column = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM drops LIKE '$column'");
    return $result && $result->num_rows > 0;
}

$dropId = (int)($_GET["id"] ?? 0);

if ($dropId <= 0) {
    die("Drop not found.");
}

$titleColumn = ld_drop_column_exists($conn, "name") ? "name" : "title";

$stmt = $conn->prepare("SELECT * FROM drops WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $dropId);
$stmt->execute();
$drop = $stmt->get_result()->fetch_assoc();

if (!$drop) {
    die("Drop not found.");
}

$dropTitle = $drop[$titleColumn] ?? "Drop";

$productStmt = $conn->prepare("SELECT * FROM products WHERE drop_id = ? AND status = 'active' ORDER BY id DESC");
$productStmt->bind_param("i", $dropId);
$productStmt->execute();
$products = $productStmt->get_result();
?>

<section class="page-hero">
    <p class="eyebrow"><?= clean($drop["status"] ?? "active"); ?></p>
    <h1><?= clean($dropTitle); ?></h1>
    <p><?= clean($drop["description"] ?? "Explore this limited Lova Dusk collection."); ?></p>
</section>

<section class="modern-featured">
    <div class="section-heading modern-heading">
        <p>Drop Products</p>
        <h2>Shop This Drop</h2>
    </div>

    <div class="modern-product-grid">
        <?php if ($products && $products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()): ?>
                <article class="modern-product-card">
                    <a href="<?= BASE_URL; ?>product.php?id=<?= $product["id"]; ?>">
                        <div class="modern-product-image">
                            <?php if (!empty($product["main_image"])): ?>
                                <img src="<?= BASE_URL . clean($product["main_image"]); ?>" alt="<?= clean($product["name"]); ?>">
                            <?php else: ?>
                                <div class="product-image"></div>
                            <?php endif; ?>
                        </div>

                        <div class="modern-product-info">
                            <h3><?= clean($product["name"]); ?></h3>

                            <?php if (!empty($product["sale_price"])): ?>
                                <p>
                                    <span class="old-price"><?= money($product["price"]); ?></span>
                                    <?= money($product["sale_price"]); ?>
                                </p>
                            <?php else: ?>
                                <p><?= money($product["price"]); ?></p>
                            <?php endif; ?>

                            <span class="view-product-link">View Product</span>
                        </div>
                    </a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-drop-box">
                <h3>No products added in this drop yet.</h3>
                <p>Add products from admin panel or setup script with drop_id = <?= $dropId; ?>.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include "../includes/footer.php"; ?>
