<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../includes/csrf.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("cart/");
}

verify_csrf();

$userId = (int)$_SESSION["user_id"];
$cartId = (int)($_POST["cart_id"] ?? 0);
$qty = max(1, (int)($_POST["qty"] ?? 1));

$stmt = $conn->prepare("SELECT cart.product_id, cart.size, product_sizes.stock
                        FROM cart
                        INNER JOIN product_sizes ON cart.product_id = product_sizes.product_id
                        AND cart.size = product_sizes.size
                        WHERE cart.id = ? AND cart.user_id = ?
                        LIMIT 1");
$stmt->bind_param("ii", $cartId, $userId);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if ($item) {
    $availableStock = (int)$item["stock"];
    $qty = min($qty, $availableStock);

    if ($qty <= 0) {
        $delete = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $delete->bind_param("ii", $cartId, $userId);
        $delete->execute();
    } else {
        $update = $conn->prepare("UPDATE cart SET qty = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("iii", $qty, $cartId, $userId);
        $update->execute();
    }
}

redirect("cart/");
?>