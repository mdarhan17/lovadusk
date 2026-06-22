<?php
include "../includes/header.php";

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM drops WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$drop = $stmt->get_result()->fetch_assoc();

if (!$drop) {
    die("Drop not found.");
}

$productStmt = $conn->prepare("SELECT * FROM products WHERE drop_id = ? AND status = 'active' ORDER BY id DESC");
$productStmt->bind_param("i", $id);
$productStmt->execute();
$products = $productStmt->get_result();
?>

<section class="page-hero">
    <p class="eyebrow"><?= clean($drop["status"]); ?></p>
    <h1><?= clean($drop["title"]); ?></h1>
    <p><?= clean($drop["description"] ?? "Limited LOVA DUSK drop."); ?></p>
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
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products in this drop yet.</p>
        <?php endif; ?>
    </div>
</section>

<?php include "../includes/footer.php"; ?>