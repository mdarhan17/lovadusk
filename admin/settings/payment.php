<?php
require_once "../../includes/admin-check.php";
require_once "../../config/razorpay.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment Settings | Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <h2>LOVA DUSK</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="general.php">General</a>
    <a href="homepage.php">Homepage</a>
    <a href="shipping.php">Shipping</a>
    <a href="payment.php">Payment</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Payment Gateway</p>
            <h1>Razorpay Settings</h1>
        </div>
    </div>

    <section class="admin-panel">
        <p><strong>Current Key ID:</strong> <?= clean(RAZORPAY_KEY_ID); ?></p>
        <p class="settings-note">For security, update Razorpay Key ID and Secret inside config/razorpay.php only.</p>

        <div class="success-box">
            <p>Use test keys first.</p>
            <p>After full testing, replace test keys with live Razorpay keys.</p>
            <p>Never share Key Secret publicly.</p>
        </div>
    </section>
</main>
</body>
</html>