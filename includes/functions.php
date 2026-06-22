<?php
function clean($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}

function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION["user_id"]);
}

function isAdmin() {
    return isset($_SESSION["admin_id"]);
}

function money($amount) {
    return CURRENCY . number_format((float)$amount, 2);
}
?>
