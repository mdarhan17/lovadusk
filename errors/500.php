<?php
require_once "../config/constants.php";
require_once "../config/db.php";
require_once "../includes/functions.php";

$pageTitle = "Server Error | LOVA DUSK";
include "../includes/header.php";
?>

<section class="success-section">
    <p class="eyebrow">500</p>
    <h1>Something Went Wrong</h1>
    <p>Please try again after some time.</p>
    <a href="<?= BASE_URL; ?>" class="btn btn-dark">Back Home</a>
</section>

<?php include "../includes/footer.php"; ?>