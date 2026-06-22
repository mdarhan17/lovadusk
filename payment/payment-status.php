<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";

$orderNumber = trim($_GET["order"] ?? "");
$userId = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("SELECT order_number, payment_status, order_status FROM orders WHERE order_number = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("si", $orderNumber, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

header("Content-Type: application/json");
echo json_encode($order ?: ["error" => "Order not found"]);
?>