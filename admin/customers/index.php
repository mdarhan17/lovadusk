<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";

$customers = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customers | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Customers</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Customer Base</p>
            <h1>Customers</h1>
        </div>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($customer = $customers->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($customer["name"]); ?></td>
                        <td><?= clean($customer["email"]); ?></td>
                        <td><?= clean($customer["phone"] ?? ""); ?></td>
                        <td><?= clean($customer["status"]); ?></td>
                        <td><?= clean($customer["created_at"]); ?></td>
                        <td><a href="view.php?id=<?= $customer["id"]; ?>">View</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>