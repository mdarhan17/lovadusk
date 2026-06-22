<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";
require_once "../../database/phase11-schema.php";

ensure_phase11_schema($conn);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    set_setting($conn, "shipping_text", trim($_POST["shipping_text"] ?? ""));
    set_setting($conn, "return_policy", trim($_POST["return_policy"] ?? ""));
    set_setting($conn, "privacy_policy", trim($_POST["privacy_policy"] ?? ""));
    set_setting($conn, "terms_policy", trim($_POST["terms_policy"] ?? ""));

    $message = "Policy settings updated.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shipping Settings | Admin</title>
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
            <p class="eyebrow">Store Policies</p>
            <h1>Shipping & Policies</h1>
        </div>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

        <form method="POST" class="admin-form">
            <?= csrf_input(); ?>

            <label>Shipping Text</label>
            <textarea name="shipping_text"><?= clean(get_setting($conn, "shipping_text")); ?></textarea>

            <label>Return Policy</label>
            <textarea name="return_policy"><?= clean(get_setting($conn, "return_policy")); ?></textarea>

            <label>Privacy Policy</label>
            <textarea name="privacy_policy"><?= clean(get_setting($conn, "privacy_policy")); ?></textarea>

            <label>Terms Policy</label>
            <textarea name="terms_policy"><?= clean(get_setting($conn, "terms_policy")); ?></textarea>

            <button type="submit" class="btn btn-dark">Save Policies</button>
        </form>
    </section>
</main>
</body>
</html>