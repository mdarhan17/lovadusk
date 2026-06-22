<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../includes/csrf.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("shop.php");
}

verify_csrf();

$userId = (int)$_SESSION["user_id"];
$productId = (int)($_POST["product_id"] ?? 0);
$size = trim($_POST["size"] ?? "");
$qty = max(1, (int)($_POST["qty"] ?? 1));

$stmt = $conn->prepare("SELECT id, stock FROM products WHERE id = ? AND status = 'active' LIMIT 1");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product || $size === "") {
    redirect("shop.php");
}

$stockStmt = $conn->prepare("SELECT stock FROM product_sizes WHERE product_id = ? AND size = ? LIMIT 1");
$stockStmt->bind_param("is", $productId, $size);
$stockStmt->execute();
$stockRow = $stockStmt->get_result()->fetch_assoc();

$availableStock = (int)($stockRow["stock"] ?? 0);

if ($availableStock <= 0) {
    redirect("product.php?id=" . $productId);
}

$qty = min($qty, $availableStock);

$cartStmt = $conn->prepare("SELECT id, qty FROM cart WHERE user_id = ? AND product_id = ? AND size = ? LIMIT 1");
$cartStmt->bind_param("iis", $userId, $productId, $size);
$cartStmt->execute();
$cartItem = $cartStmt->get_result()->fetch_assoc();

if ($cartItem) {
    $newQty = min((int)$cartItem["qty"] + $qty, $availableStock);

    $update = $conn->prepare("UPDATE cart SET qty = ? WHERE id = ?");
    $update->bind_param("ii", $newQty, $cartItem["id"]);
    $update->execute();
} else {
    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, size, qty) VALUES (?, ?, ?, ?)");
    $insert->bind_param("iisi", $userId, $productId, $size, $qty);
    $insert->execute();
}

redirect("cart/");
?>