<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../database/phase9-schema.php";

ensure_phase9_schema($conn);

$messages = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact Messages | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Messages</a>
    <a href="../logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Customer Support</p>
            <h1>Contact Messages</h1>
        </div>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($msg = $messages->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($msg["name"]); ?></td>
                        <td><?= clean($msg["email"]); ?></td>
                        <td><?= clean($msg["phone"]); ?></td>
                        <td><?= clean($msg["subject"]); ?></td>
                        <td><?= clean($msg["message"]); ?></td>
                        <td><?= clean($msg["status"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>