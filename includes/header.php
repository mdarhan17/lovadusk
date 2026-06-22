<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/security.php";
require_once __DIR__ . "/functions.php";
require_once __DIR__ . "/csrf.php";

if (file_exists(__DIR__ . "/logger.php")) {
    require_once __DIR__ . "/logger.php";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . "/seo.php"; ?>
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/responsive.css">
</head>
<body>
<?php include __DIR__ . "/navbar.php"; ?>