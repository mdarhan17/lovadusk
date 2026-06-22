<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../database/phase9-schema.php";

ensure_phase9_schema($conn);

$sql = "SELECT product_reviews.*, users.name AS user_name, products.name AS product_name
        FROM product_reviews
        INNER JOIN users ON product_reviews.user_id = users.id
        INNER JOIN products ON product_reviews.product_id = products.id
        ORDER BY product_reviews.id DESC";

$reviews = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reviews | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="../products/index.php">Products</a>
    <a href="index.php">Reviews</a>
    <a href="../logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Customer Feedback</p>
            <h1>Reviews</h1>
        </div>
    </div>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <tr>
                        <td><?= clean($review["product_name"]); ?></td>
                        <td><?= clean($review["user_name"]); ?></td>
                        <td><?= (int)$review["rating"]; ?>/5</td>
                        <td><?= clean($review["review"]); ?></td>
                        <td><?= clean($review["status"]); ?></td>
                        <td class="table-actions">
                            <a href="update.php?id=<?= $review["id"]; ?>&status=approved">Approve</a>
                            <a href="update.php?id=<?= $review["id"]; ?>&status=rejected">Reject</a>
                            <a href="delete.php?id=<?= $review["id"]; ?>" onclick="return confirm('Delete review?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>