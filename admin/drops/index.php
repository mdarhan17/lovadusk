<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";

$result = $conn->query("SELECT * FROM drops ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Drops | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="index.php">Drops</a>
    <a href="../products/index.php">Products</a>
    <a href="../orders/index.php">Orders</a>
    <a href="../logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Collection Control</p>
            <h1>Drops</h1>
        </div>
        <a href="add.php" class="btn btn-dark">Add Drop</a>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Drop</th>
                    <th>Launch Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($drop = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $drop["id"]; ?></td>
                        <td><?= clean($drop["title"]); ?></td>
                        <td><?= clean($drop["launch_date"] ?? "Not set"); ?></td>
                        <td><?= clean($drop["status"]); ?></td>
                        <td class="table-actions">
                            <a href="edit.php?id=<?= $drop["id"]; ?>">Edit</a>
                            <form method="POST" action="delete.php" onsubmit="return confirm('Delete this drop?');">
                                <input type="hidden" name="id" value="<?= $drop["id"]; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>