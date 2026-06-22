<?php
require_once "config/constants.php";
require_once "config/db.php";
require_once "includes/functions.php";

$checks = [];

function add_check(&$checks, $name, $status, $message) {
    $checks[] = [
        "name" => $name,
        "status" => $status,
        "message" => $message
    ];
}

add_check($checks, "Database connection", isset($conn) && !$conn->connect_error, "MySQL connection checked.");

$tables = ["users", "products", "drops", "cart", "orders", "order_items", "payments"];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    add_check($checks, "Table: $table", $result && $result->num_rows > 0, "Required table check.");
}

$folders = ["uploads/products", "storage/logs", "storage/backups"];
foreach ($folders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    add_check($checks, "Folder: $folder", is_dir($folder), "Folder exists.");
}

$admin = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
add_check($checks, "Admin account", $admin && $admin->num_rows > 0, "Admin login account check.");

$pageTitle = "Setup Check | LOVA DUSK";
include "includes/header.php";
?>

<section class="page-hero">
    <p class="eyebrow">System Check</p>
    <h1>LOVA DUSK Setup Check</h1>
    <p>Use this page to verify core setup before testing.</p>
</section>

<section class="orders-section">
    <table class="order-table">
        <thead>
            <tr>
                <th>Check</th>
                <th>Status</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($checks as $check): ?>
                <tr>
                    <td><?= clean($check["name"]); ?></td>
                    <td><?= $check["status"] ? "OK" : "Fix Needed"; ?></td>
                    <td><?= clean($check["message"]); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php include "includes/footer.php"; ?>