<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";

$message = "";

function category_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace("/[^a-z0-9]+/", "-", $text);
    return trim($text, "-");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $name = trim($_POST["name"] ?? "");
    $status = $_POST["status"] ?? "active";

    if ($name === "") {
        $message = "Category name is required.";
    } else {
        $slug = category_slug($name) . "-" . time();

        $stmt = $conn->prepare("INSERT INTO categories (name, slug, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $slug, $status);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        }

        $message = "Category could not be added.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Category | Admin</title>
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
            <p class="eyebrow">New Category</p>
            <h1>Add Category</h1>
        </div>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

        <form method="POST" class="admin-form">
            <?= csrf_input(); ?>

            <label>Name</label>
            <input type="text" name="name" required>

            <label>Status</label>
            <select name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button class="btn btn-dark">Save Category</button>
        </form>
    </section>
</main>
</body>
</html>