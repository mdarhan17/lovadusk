<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../includes/csrf.php";
require_once "../database/phase9-schema.php";

ensure_phase9_schema($conn);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("shop.php");
}

verify_csrf();

$userId = (int)$_SESSION["user_id"];
$productId = (int)($_POST["product_id"] ?? 0);
$rating = (int)($_POST["rating"] ?? 0);
$review = trim($_POST["review"] ?? "");

if ($productId <= 0 || $rating < 1 || $rating > 5 || $review === "") {
    redirect("product.php?id=" . $productId);
}

$stmt = $conn->prepare("INSERT INTO product_reviews (user_id, product_id, rating, review) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $userId, $productId, $rating, $review);
$stmt->execute();

redirect("product.php?id=" . $productId);
?>