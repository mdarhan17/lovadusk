<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../includes/csrf.php";
require_once "../database/phase8-schema.php";

ensure_phase8_schema($conn);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("shop.php");
}

verify_csrf();

$userId = (int)$_SESSION["user_id"];
$productId = (int)($_POST["product_id"] ?? 0);

$stmt = $conn->prepare("SELECT id FROM products WHERE id = ? AND status = 'active' LIMIT 1");
$stmt->bind_param("i", $productId);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    $insert = $conn->prepare("INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $insert->bind_param("ii", $userId, $productId);
    $insert->execute();
}

redirect("user/wishlist.php");
?>