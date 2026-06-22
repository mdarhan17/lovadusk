<?php
require_once "../config/constants.php";
require_once "../config/db.php";
require_once "../includes/functions.php";

$pageTitle = "Page Not Found | LOVA DUSK";
include "../includes/header.php";
?>

<section class="success-section">
    <p class="eyebrow">404</p>
    <h1>Page Not Found</h1>
    <p>The page you are looking for does not exist.</p>
    <a href="<?= BASE_URL; ?>" class="btn btn-dark">Back Home</a>
</section>

<?php include "../includes/footer.php"; ?>