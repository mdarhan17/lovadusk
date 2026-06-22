<?php
require_once "config/constants.php";
require_once "config/db.php";
require_once "includes/functions.php";
require_once "database/phase11-schema.php";

ensure_phase11_schema($conn);

$pageTitle = "Returns Policy | LOVA DUSK";
include "includes/header.php";
?>

<section class="page-hero">
    <p class="eyebrow">Policy</p>
    <h1>Returns Policy</h1>
    <p><?= clean(get_setting($conn, "return_policy")); ?></p>
</section>

<?php include "includes/footer.php"; ?>