<?php
require_once "../includes/auth-check.php";

$pageTitle = "My Account | LOVA DUSK";
include "../includes/header.php";
?>

<section class="account-section">
    <div class="account-head">
        <p class="eyebrow">Customer Account</p>
        <h1>Welcome, <?= clean($_SESSION["user_name"] ?? "Customer"); ?></h1>
        <p>Manage your orders, profile, wishlist and invoices.</p>
    </div>

    <div class="account-grid">
        <a href="orders.php" class="account-box">
            <h3>My Orders</h3>
            <p>Track your LOVA DUSK purchases.</p>
        </a>

        <a href="profile.php" class="account-box">
            <h3>Profile</h3>
            <p>View and update your account details.</p>
        </a>

        <a href="wishlist.php" class="account-box">
            <h3>Wishlist</h3>
            <p>Your saved fashion drops.</p>
        </a>

        <a href="../auth/logout.php" class="account-box">
            <h3>Logout</h3>
            <p>End your current session securely.</p>
        </a>
    </div>
</section>

<?php include "../includes/footer.php"; ?>
