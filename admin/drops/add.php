<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";

$message = "";

function make_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace("/[^a-z0-9]+/", "-", $text);
    return trim($text, "-");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $launchDate = trim($_POST["launch_date"] ?? "");
    $status = $_POST["status"] ?? "upcoming";

    if ($title === "") {
        $message = "Drop title is required.";
    } else {
        $slug = make_slug($title) . "-" . time();

        $stmt = $conn->prepare("INSERT INTO drops (title, slug, description, launch_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $slug, $description, $launchDate, $status);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        }

        $message = "Drop could not be added.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Drop | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Drops</a>
    <a href="../products/index.php">Products</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">New Collection</p>
            <h1>Add Drop</h1>
        </div>
        <a href="index.php" class="btn btn-light">Back</a>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

        <form method="POST" class="admin-form">
            <?= csrf_input(); ?>

            <label>Drop Title</label>
            <input type="text" name="title" placeholder="Drop 001 - Luna Collection" required>

            <label>Description</label>
            <textarea name="description" placeholder="Write drop story"></textarea>

            <label>Launch Date</label>
            <input type="datetime-local" name="launch_date">

            <label>Status</label>
            <select name="status">
                <option value="upcoming">Upcoming</option>
                <option value="active">Active</option>
                <option value="closed">Closed</option>
            </select>

            <button type="submit" class="btn btn-dark">Save Drop</button>
        </form>
    </section>
</main>
</body>
</html>