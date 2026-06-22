<?php
require_once "../includes/admin-check.php";
require_once "../config/db.php";
require_once "../database/phase9-schema.php";

ensure_phase9_schema($conn);

$subscribers = $conn->query("SELECT * FROM subscribers ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Subscribers | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="subscribers.php">Subscribers</a>
    <a href="logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Newsletter</p>
            <h1>Subscribers</h1>
        </div>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($subscriber = $subscribers->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($subscriber["email"]); ?></td>
                        <td><?= clean($subscriber["status"]); ?></td>
                        <td><?= clean($subscriber["created_at"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>