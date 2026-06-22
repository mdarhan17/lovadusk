<?php
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["created_at"])) {
    $_SESSION["created_at"] = time();
}

if (time() - $_SESSION["created_at"] > 7200) {
    session_regenerate_id(true);
    $_SESSION["created_at"] = time();
}
?>