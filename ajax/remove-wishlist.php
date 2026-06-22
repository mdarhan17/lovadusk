<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../includes/csrf.php";
require_once "../database/phase8-schema.php";

ensure_phase8_schema($conn);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("user/wishlist.php");
}

verify_csrf();

$userId = (int)$_SESSION["user_id"];
$productId = (int)($_POST["product_id"] ?? 0);

$stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $userId, $productId);
$stmt->execute();

redirect("user/wishlist.php");
?>