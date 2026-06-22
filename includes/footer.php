<?php
if (file_exists(__DIR__ . "/../database/phase11-schema.php")) {
    require_once __DIR__ . "/../database/phase11-schema.php";
    ensure_phase11_schema($conn);
}

if (file_exists(__DIR__ . "/../database/phase18-schema.php")) {
    require_once __DIR__ . "/../database/phase18-schema.php";
    ensure_phase18_schema($conn);
}

$waNumber = function_exists("get_setting") ? get_setting($conn, "whatsapp_number", "919876543210") : "919876543210";
$instagramLink = function_exists("get_setting") ? get_setting($conn, "instagram_link", "#") : "#";
?>
<footer class="site-footer">
    <div>
        <h3><?= clean(function_exists("get_setting") ? get_setting($conn, "site_name", "LOVA DUSK") : "LOVA DUSK"); ?></h3>
        <p><?= clean(function_exists("get_setting") ? get_setting($conn, "footer_tagline", "Luxury silhouettes. Limited drops. Modern fashion.") : "Luxury silhouettes. Limited drops. Modern fashion."); ?></p>
        <p><?= clean(function_exists("get_setting") ? get_setting($conn, "contact_email", "") : ""); ?> | <?= clean(function_exists("get_setting") ? get_setting($conn, "contact_phone", "") : ""); ?></p>
        <p><?= clean(function_exists("get_setting") ? get_setting($conn, "instagram_handle", "@lovadusk") : "@lovadusk"); ?></p>

        <form method="POST" action="<?= BASE_URL; ?>ajax/newsletter.php" class="newsletter-form">
            <?= csrf_input(); ?>
            <input type="email" name="email" placeholder="Join newsletter" required>
            <button type="submit">Subscribe</button>
        </form>
    </div>

    <div class="footer-links">
        <a href="<?= BASE_URL; ?>size-guide.php">Size Guide</a>
        <a href="<?= BASE_URL; ?>track-order.php">Track Order</a>
        <a href="<?= BASE_URL; ?>shipping-policy.php">Shipping</a>
        <a href="<?= BASE_URL; ?>returns-policy.php">Returns</a>
        <a href="<?= BASE_URL; ?>privacy-policy.php">Privacy</a>
        <a href="<?= BASE_URL; ?>terms-services.php">Terms</a>
        <a href="<?= BASE_URL; ?>contact.php">Contact</a>
    </div>
</footer>

<div class="floating-socials">
    <a href="https://wa.me/<?= clean($waNumber); ?>" target="_blank" class="float-btn whatsapp-btn">WhatsApp</a>
    <a href="<?= clean($instagramLink); ?>" target="_blank" class="float-btn instagram-btn">Instagram</a>
</div>

<script src="<?= BASE_URL; ?>assets/js/main.js"></script>
</body>
</html>