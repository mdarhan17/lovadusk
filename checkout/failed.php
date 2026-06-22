<?php
$pageTitle = "Payment Failed | LOVA DUSK";
include "../includes/header.php";
?>

<section class="success-section">
    <p class="eyebrow">Payment Failed</p>
    <h1>Something Went Wrong</h1>
    <p>Your payment was not completed. Please try again.</p>
    <a href="<?= BASE_URL; ?>cart/" class="btn btn-dark">Back to Cart</a>
</section>

<?php include "../includes/footer.php"; ?>