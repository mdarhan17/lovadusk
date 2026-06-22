<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/logger.php";

admin_activity($conn, "Database backup opened", "Admin accessed backup tool.");

$backupDir = __DIR__ . "/../../storage/backups";

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fileName = "lovadusk_backup_" . date("Ymd_His") . ".sql";
    $filePath = $backupDir . "/" . $fileName;

    $sqlDump = "-- LOVA DUSK Database Backup\n";
    $sqlDump .= "-- Created at " . date("Y-m-d H:i:s") . "\n\n";

    $tables = $conn->query("SHOW TABLES");

    while ($tableRow = $tables->fetch_array()) {
        $table = $tableRow[0];

        $create = $conn->query("SHOW CREATE TABLE `$table`")->fetch_assoc();
        $sqlDump .= "\nDROP TABLE IF EXISTS `$table`;\n";
        $sqlDump .= $create["Create Table"] . ";\n\n";

        $rows = $conn->query("SELECT * FROM `$table`");

        while ($row = $rows->fetch_assoc()) {
            $columns = array_map(function($col) {
                return "`" . $col . "`";
            }, array_keys($row));

            $values = array_map(function($value) use ($conn) {
                if ($value === null) {
                    return "NULL";
                }
                return "'" . $conn->real_escape_string($value) . "'";
            }, array_values($row));

            $sqlDump .= "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");\n";
        }

        $sqlDump .= "\n";
    }

    file_put_contents($filePath, $sqlDump);
    admin_activity($conn, "Database backup created", $fileName);

    $message = "Backup created successfully: " . $fileName;
}

$files = glob($backupDir . "/*.sql");
rsort($files);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Backup | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="../logs/index.php">Activity Logs</a>
    <a href="backup.php">Backup</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Data Protection</p>
            <h1>Database Backup</h1>
        </div>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?>
            <div class="alert"><?= clean($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <button type="submit" class="btn btn-dark">Create Backup</button>
        </form>
    </section>

    <section class="admin-panel">
        <h2>Recent Backups</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>File</th>
                    <th>Created</th>
                    <th>Size</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($files): ?>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td><?= clean(basename($file)); ?></td>
                            <td><?= date("Y-m-d H:i:s", filemtime($file)); ?></td>
                            <td><?= round(filesize($file) / 1024, 2); ?> KB</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">No backups created yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>