<?php
require_once "../includes/auth-check.php";

$pageTitle = "Profile | LOVA DUSK";
include "../includes/header.php";

$userId = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT name, email, phone, created_at FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<section class="account-section">
    <div class="auth-card">
        <p class="eyebrow">My Details</p>
        <h1>Profile</h1>

        <div class="profile-list">
            <p><strong>Name:</strong> <?= clean($user["name"] ?? ""); ?></p>
            <p><strong>Email:</strong> <?= clean($user["email"] ?? ""); ?></p>
            <p><strong>Phone:</strong> <?= clean($user["phone"] ?? "Not added"); ?></p>
            <p><strong>Joined:</strong> <?= clean($user["created_at"] ?? ""); ?></p>
        </div>

        <a href="dashboard.php" class="btn btn-light">Back to Dashboard</a>
    </div>
</section>

<?php include "../includes/footer.php"; ?>
