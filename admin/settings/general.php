<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";
require_once "../../database/phase11-schema.php";
require_once "../../database/phase18-schema.php";

ensure_phase11_schema($conn);
ensure_phase18_schema($conn);

$message = "";

$fields = [
    "site_name",
    "footer_tagline",
    "contact_email",
    "contact_phone",
    "instagram_handle",
    "instagram_link",
    "whatsapp_number"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    foreach ($fields as $field) {
        set_setting($conn, $field, trim($_POST[$field] ?? ""));
    }

    $message = "General settings updated.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>General Settings | Admin</title>
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
    <a href="../instagram/index.php">Instagram</a>
    <a href="../logout.php">Logout</a>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <div>
            <p class="eyebrow">Store Setup</p>
            <h1>General Settings</h1>
        </div>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

        <form method="POST" class="admin-form">
            <?= csrf_input(); ?>

            <label>Site Name</label>
            <input type="text" name="site_name" value="<?= clean(get_setting($conn, "site_name")); ?>">

            <label>Footer Tagline</label>
            <textarea name="footer_tagline"><?= clean(get_setting($conn, "footer_tagline")); ?></textarea>

            <label>Contact Email</label>
            <input type="email" name="contact_email" value="<?= clean(get_setting($conn, "contact_email")); ?>">

            <label>Contact Phone</label>
            <input type="text" name="contact_phone" value="<?= clean(get_setting($conn, "contact_phone")); ?>">

            <label>Instagram Handle</label>
            <input type="text" name="instagram_handle" value="<?= clean(get_setting($conn, "instagram_handle")); ?>">

            <label>Instagram Link</label>
            <input type="url" name="instagram_link" value="<?= clean(get_setting($conn, "instagram_link")); ?>">

            <label>WhatsApp Number With Country Code</label>
            <input type="text" name="whatsapp_number" value="<?= clean(get_setting($conn, "whatsapp_number")); ?>" placeholder="919876543210">

            <button type="submit" class="btn btn-dark">Save Settings</button>
        </form>
    </section>
</main>
</body>
</html>