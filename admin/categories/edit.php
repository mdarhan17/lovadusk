<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$cat = $stmt->get_result()->fetch_assoc();

if (!$cat) {
    die("Category not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $name = trim($_POST["name"] ?? "");
    $status = $_POST["status"] ?? "active";

    $stmt = $conn->prepare("UPDATE categories SET name = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $status, $id);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Category | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Categories</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Edit Category</p>
            <h1><?= clean($cat["name"]); ?></h1>
        </div>
    </div>

    <section class="admin-panel">
        <form method="POST" class="admin-form">
            <?= csrf_input(); ?>

            <label>Name</label>
            <input type="text" name="name" value="<?= clean($cat["name"]); ?>" required>

            <label>Status</label>
            <select name="status">
                <option value="active" <?= $cat["status"] === "active" ? "selected" : ""; ?>>Active</option>
                <option value="inactive" <?= $cat["status"] === "inactive" ? "selected" : ""; ?>>Inactive</option>
            </select>

            <button class="btn btn-dark">Update Category</button>
        </form>
    </section>
</main>
</body>
</html>