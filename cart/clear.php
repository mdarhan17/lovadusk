<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../includes/csrf.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("cart/");
}

verify_csrf();

$userId = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();

redirect("cart/");
?>