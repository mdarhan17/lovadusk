<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";
require_once "../../database/phase18-schema.php";

ensure_phase18_schema($conn);

$message = "";

function upload_instagram_image() {
    if (empty($_FILES["image"]["name"])) {
        return "";
    }

    $allowed = ["jpg", "jpeg", "png", "webp"];
    $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return "";
    }

    $fileName = "instagram_" . time() . "_" . rand(1000, 9999) . "." . $ext;
    $target = "../../uploads/instagram/" . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
        return "uploads/instagram/" . $fileName;
    }

    return "";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $caption = trim($_POST["caption"] ?? "");
    $link = trim($_POST["instagram_link"] ?? "");
    $status = $_POST["status"] ?? "active";
    $image = upload_instagram_image();

    if ($image === "") {
        $message = "Please upload a valid image.";
    } else {
        $stmt = $conn->prepare("INSERT INTO instagram_gallery (image, caption, instagram_link, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $image, $caption, $link, $status);
        $stmt->execute();
        $message = "Instagram image added.";
    }
}

$posts = $conn->query("SELECT * FROM instagram_gallery ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Instagram Gallery | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Instagram</a>
    <a href="../settings/general.php">Settings</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Social Gallery</p>
            <h1>Instagram Gallery</h1>
        </div>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <?= csrf_input(); ?>

            <label>Image</label>
            <input type="file" name="image" accept="image/*" required>

            <label>Caption</label>
            <input type="text" name="caption" placeholder="New drop mood">

            <label>Instagram Link</label>
            <input type="url" name="instagram_link" placeholder="https://instagram.com/...">

            <label>Status</label>
            <select name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button class="btn btn-dark">Add To Gallery</button>
        </form>
    </section>

    <section class="admin-panel">
        <h2>Gallery Posts</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Caption</th>
                    <th>Status</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?= BASE_URL . clean($post["image"]); ?>" class="table-img"></td>
                        <td><?= clean($post["caption"]); ?></td>
                        <td><?= clean($post["status"]); ?></td>
                        <td><a href="delete.php?id=<?= $post["id"]; ?>" onclick="return confirm('Delete this post?')">Delete</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>