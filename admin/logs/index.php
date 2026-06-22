<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/logger.php";

$conn->query("CREATE TABLE IF NOT EXISTS admin_activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL,
    action VARCHAR(180) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(60),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$logs = $conn->query("SELECT admin_activity_logs.*, users.name AS admin_name
                     FROM admin_activity_logs
                     LEFT JOIN users ON admin_activity_logs.admin_id = users.id
                     ORDER BY admin_activity_logs.id DESC
                     LIMIT 200");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Activity Logs | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Activity Logs</a>
    <a href="../tools/backup.php">Backup</a>
    <a href="../logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Security</p>
            <h1>Activity Logs</h1>
        </div>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $logs->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($log["admin_name"] ?? "System"); ?></td>
                        <td><?= clean($log["action"]); ?></td>
                        <td><?= clean($log["details"] ?? ""); ?></td>
                        <td><?= clean($log["ip_address"] ?? ""); ?></td>
                        <td><?= clean($log["created_at"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>