<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";
require_once "../../database/phase11-schema.php";

ensure_phase11_schema($conn);

$message = "";

$fields = [
    "hero_eyebrow",
    "hero_title",
    "hero_subtitle",
    "hero_button_primary",
    "hero_button_secondary",
    "featured_title",
    "featured_subtitle",
    "about_title",
    "about_content"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    foreach ($fields as $field) {
        set_setting($conn, $field, trim($_POST[$field] ?? ""));
    }

    $message = "Homepage settings updated.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Homepage Settings | Admin</title>
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
            <p class="eyebrow">Content Control</p>
            <h1>Homepage Settings</h1>
        </div>
    </div>

    <section class="admin-panel">
        <?php if ($message): ?><div class="alert"><?= clean($message); ?></div><?php endif; ?>

        <form method="POST" class="admin-form">
            <?= csrf_input(); ?>

            <label>Hero Eyebrow</label>
            <input type="text" name="hero_eyebrow" value="<?= clean(get_setting($conn, "hero_eyebrow")); ?>">

            <label>Hero Title</label>
            <input type="text" name="hero_title" value="<?= clean(get_setting($conn, "hero_title")); ?>">

            <label>Hero Subtitle</label>
            <textarea name="hero_subtitle"><?= clean(get_setting($conn, "hero_subtitle")); ?></textarea>

            <div class="form-row">
                <div>
                    <label>Primary Button</label>
                    <input type="text" name="hero_button_primary" value="<?= clean(get_setting($conn, "hero_button_primary")); ?>">
                </div>
                <div>
                    <label>Secondary Button</label>
                    <input type="text" name="hero_button_secondary" value="<?= clean(get_setting($conn, "hero_button_secondary")); ?>">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label>Featured Subtitle</label>
                    <input type="text" name="featured_subtitle" value="<?= clean(get_setting($conn, "featured_subtitle")); ?>">
                </div>
                <div>
                    <label>Featured Title</label>
                    <input type="text" name="featured_title" value="<?= clean(get_setting($conn, "featured_title")); ?>">
                </div>
            </div>

            <label>About Title</label>
            <input type="text" name="about_title" value="<?= clean(get_setting($conn, "about_title")); ?>">

            <label>About Content</label>
            <textarea name="about_content"><?= clean(get_setting($conn, "about_content")); ?></textarea>

            <button type="submit" class="btn btn-dark">Save Homepage</button>
        </form>
    </section>
</main>
</body>
</html>