<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";

$message = "";
$drops = $conn->query("SELECT id, title FROM drops ORDER BY id DESC");

function make_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace("/[^a-z0-9]+/", "-", $text);
    return trim($text, "-");
}

function upload_product_image() {
    if (empty($_FILES["main_image"]["name"])) {
        return "";
    }

    $allowed = ["jpg", "jpeg", "png", "webp"];
    $ext = strtolower(pathinfo($_FILES["main_image"]["name"], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return "";
    }

    $fileName = "product_" . time() . "_" . rand(1000, 9999) . "." . $ext;
    $target = "../../uploads/products/" . $fileName;

    if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $target)) {
        return "uploads/products/" . $fileName;
    }

    return "";
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

    if ($name === "" || $price <= 0) {
        $message = "Product name and price are required.";
    } else {
        $slug = make_slug($name) . "-" . time();
        $imagePath = upload_product_image();

        $stmt = $conn->prepare("INSERT INTO products (drop_id, category_id, name, slug, description, price, sale_price, main_image, stock, status) VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssddsis", $dropId, $name, $slug, $description, $price, $salePrice, $imagePath, $totalStock, $status);

        if ($stmt->execute()) {
            $productId = $stmt->insert_id;

            $sizeStmt = $conn->prepare("INSERT INTO product_sizes (product_id, size, stock) VALUES (?, ?, ?)");
            foreach ($sizes as $size => $stock) {
                $sizeStmt->bind_param("isi", $productId, $size, $stock);
                $sizeStmt->execute();
            }

            header("Location: index.php");
            exit;
        }

        $message = "Product could not be added.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Product | Admin</title>
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
            <p class="eyebrow">New Fashion Piece</p>
            <h1>Add Product</h1>
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
                    <option value="<?= $drop["id"]; ?>"><?= clean($drop["title"]); ?></option>
                <?php endwhile; ?>
            </select>

            <label>Product Name</label>
            <input type="text" name="name" placeholder="Luna Shirt Black" required>

            <label>Description</label>
            <textarea name="description" placeholder="Product details, fabric, fit and styling notes"></textarea>

            <div class="form-row">
                <div>
                    <label>Price</label>
                    <input type="number" name="price" min="1" step="0.01" required>
                </div>
                <div>
                    <label>Sale Price</label>
                    <input type="number" name="sale_price" min="1" step="0.01">
                </div>
            </div>

            <label>Main Image</label>
            <input type="file" name="main_image" accept="image/*">

            <label>Size Stock</label>
            <div class="form-row four">
                <input type="number" name="stock_s" placeholder="S" min="0" value="0">
                <input type="number" name="stock_m" placeholder="M" min="0" value="0">
                <input type="number" name="stock_l" placeholder="L" min="0" value="0">
                <input type="number" name="stock_xl" placeholder="XL" min="0" value="0">
            </div>

            <label>Status</label>
            <select name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit" class="btn btn-dark">Save Product</button>
        </form>
    </section>
</main>
</body>
</html>