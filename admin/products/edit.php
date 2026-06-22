<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";

$id = (int)($_GET["id"] ?? 0);
$message = "";

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

$drops = $conn->query("SELECT id, title FROM drops ORDER BY id DESC");
$sizeData = ["S" => 0, "M" => 0, "L" => 0, "XL" => 0];

$sizeStmt = $conn->prepare("SELECT size, stock FROM product_sizes WHERE product_id = ?");
$sizeStmt->bind_param("i", $id);
$sizeStmt->execute();
$sizesResult = $sizeStmt->get_result();

while ($row = $sizesResult->fetch_assoc()) {
    $sizeData[$row["size"]] = (int)$row["stock"];
}

function upload_product_image_edit($oldImage) {
    if (empty($_FILES["main_image"]["name"])) {
        return $oldImage;
    }

    $allowed = ["jpg", "jpeg", "png", "webp"];
    $ext = strtolower(pathinfo($_FILES["main_image"]["name"], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return $oldImage;
    }

    $fileName = "product_" . time() . "_" . rand(1000, 9999) . "." . $ext;
    $target = "../../uploads/products/" . $fileName;

    if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $target)) {
        return "uploads/products/" . $fileName;
    }

    return $oldImage;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $dropId = (int)($_POST["drop_id"] ?? 0);
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = (float)($_POST["price"] ?? 0);
    $salePrice = $_POST["sale_price"] !== "" ? (float)$_POST["sale_price"] : null;
    $status = $_POST["status"] ?? "active";

    $sizes = [
        "S" => (int)($_POST["stock_s"] ?? 0),
        "M" => (int)($_POST["stock_m"] ?? 0),
        "L" => (int)($_POST["stock_l"] ?? 0),
        "XL" => (int)($_POST["stock_xl"] ?? 0)
    ];

    $totalStock = array_sum($sizes);
    $imagePath = upload_product_image_edit($product["main_image"]);

    $stmt = $conn->prepare("UPDATE products SET drop_id = ?, name = ?, description = ?, price = ?, sale_price = ?, main_image = ?, stock = ?, status = ? WHERE id = ?");
    $stmt->bind_param("issddsssi", $dropId, $name, $description, $price, $salePrice, $imagePath, $totalStock, $status, $id);

    if ($stmt->execute()) {
        $conn->query("DELETE FROM product_sizes WHERE product_id = " . $id);

        $insertSize = $conn->prepare("INSERT INTO product_sizes (product_id, size, stock) VALUES (?, ?, ?)");
        foreach ($sizes as $size => $stock) {
            $insertSize->bind_param("isi", $id, $size, $stock);
            $insertSize->execute();
        }

        header("Location: index.php");
        exit;
    }

    $message = "Product update failed.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Product | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Products</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Update Fashion Piece</p>
            <h1>Edit Product</h1>
        </div>
        <a href="index.php" class="btn btn-light">Back</a>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <?= csrf_input(); ?>

            <label>Drop</label>
            <select name="drop_id">
                <option value="0">No drop</option>
                <?php while ($drop = $drops->fetch_assoc()): ?>
                    <option value="<?= $drop["id"]; ?>" <?= (int)$product["drop_id"] === (int)$drop["id"] ? "selected" : ""; ?>>
                        <?= clean($drop["title"]); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Product Name</label>
            <input type="text" name="name" value="<?= clean($product["name"]); ?>" required>

            <label>Description</label>
            <textarea name="description"><?= clean($product["description"] ?? ""); ?></textarea>

            <div class="form-row">
                <div>
                    <label>Price</label>
                    <input type="number" name="price" step="0.01" value="<?= clean($product["price"]); ?>" required>
                </div>
                <div>
                    <label>Sale Price</label>
                    <input type="number" name="sale_price" step="0.01" value="<?= clean($product["sale_price"] ?? ""); ?>">
                </div>
            </div>

            <label>Main Image</label>
            <input type="file" name="main_image" accept="image/*">

            <label>Size Stock</label>
            <div class="form-row four">
                <input type="number" name="stock_s" placeholder="S" min="0" value="<?= $sizeData["S"]; ?>">
                <input type="number" name="stock_m" placeholder="M" min="0" value="<?= $sizeData["M"]; ?>">
                <input type="number" name="stock_l" placeholder="L" min="0" value="<?= $sizeData["L"]; ?>">
                <input type="number" name="stock_xl" placeholder="XL" min="0" value="<?= $sizeData["XL"]; ?>">
            </div>

            <label>Status</label>
            <select name="status">
                <option value="active" <?= $product["status"] === "active" ? "selected" : ""; ?>>Active</option>
                <option value="inactive" <?= $product["status"] === "inactive" ? "selected" : ""; ?>>Inactive</option>
            </select>

            <button type="submit" class="btn btn-dark">Update Product</button>
        </form>
    </section>
</main>
</body>
</html>