<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";

$id = (int)($_GET["id"] ?? 0);
$message = "";

$stmt = $conn->prepare("SELECT * FROM drops WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$drop = $stmt->get_result()->fetch_assoc();

if (!$drop) {
    die("Drop not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $launchDate = trim($_POST["launch_date"] ?? "");
    $status = $_POST["status"] ?? "upcoming";

    $stmt = $conn->prepare("UPDATE drops SET title = ?, description = ?, launch_date = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $description, $launchDate, $status, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    }

    $message = "Drop update failed.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Drop | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Drops</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Edit Collection</p>
            <h1>Edit Drop</h1>
        </div>
        <a href="index.php" class="btn btn-light">Back</a>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

        <form method="POST" class="admin-form">
            <?= csrf_input(); ?>

            <label>Drop Title</label>
            <input type="text" name="title" value="<?= clean($drop["title"]); ?>" required>

            <label>Description</label>
            <textarea name="description"><?= clean($drop["description"] ?? ""); ?></textarea>

            <label>Launch Date</label>
            <input type="datetime-local" name="launch_date" value="<?= $drop["launch_date"] ? date("Y-m-d\TH:i", strtotime($drop["launch_date"])) : ""; ?>">

            <label>Status</label>
            <select name="status">
                <option value="upcoming" <?= $drop["status"] === "upcoming" ? "selected" : ""; ?>>Upcoming</option>
                <option value="active" <?= $drop["status"] === "active" ? "selected" : ""; ?>>Active</option>
                <option value="closed" <?= $drop["status"] === "closed" ? "selected" : ""; ?>>Closed</option>
            </select>

            <button type="submit" class="btn btn-dark">Update Drop</button>
        </form>
    </section>
</main>
</body>
</html>