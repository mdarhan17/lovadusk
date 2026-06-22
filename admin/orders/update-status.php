<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../includes/csrf.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

verify_csrf();

$id = (int)($_POST["id"] ?? 0);
$orderStatus = $_POST["order_status"] ?? "placed";
$paymentStatus = $_POST["payment_status"] ?? "pending";

$allowedOrder = ["placed", "processing", "shipped", "delivered", "cancelled"];
$allowedPayment = ["pending", "paid", "failed", "refunded"];

if (!in_array($orderStatus, $allowedOrder)) {
    $orderStatus = "placed";
}

if (!in_array($paymentStatus, $allowedPayment)) {
    $paymentStatus = "pending";
}

$stmt = $conn->prepare("UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?");
$stmt->bind_param("ssi", $orderStatus, $paymentStatus, $id);
$stmt->execute();

header("Location: view.php?id=" . $id);
exit;
?>