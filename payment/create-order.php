<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../config/razorpay.php";
require_once "../includes/csrf.php";
require_once "../database/phase5-schema.php";

header("Content-Type: application/json");

ensure_phase5_schema($conn);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

verify_csrf();

$userId = (int)$_SESSION["user_id"];
$orderNumber = trim($_POST["order_number"] ?? "");

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ? AND payment_status = 'pending' LIMIT 1");
$stmt->bind_param("si", $orderNumber, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(["success" => false, "message" => "Order not found or already paid."]);
    exit;
}

$amountPaise = (int)round(((float)$order["total_amount"]) * 100);

$payload = json_encode([
    "amount" => $amountPaise,
    "currency" => RAZORPAY_CURRENCY,
    "receipt" => $order["order_number"],
    "payment_capture" => 1
]);

$ch = curl_init("https://api.razorpay.com/v1/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ":" . RAZORPAY_KEY_SECRET);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error || $httpCode < 200 || $httpCode >= 300) {
    echo json_encode(["success" => false, "message" => "Razorpay order creation failed. Check key details."]);
    exit;
}

$data = json_decode($response, true);

if (empty($data["id"])) {
    echo json_encode(["success" => false, "message" => "Invalid Razorpay response."]);
    exit;
}

$razorpayOrderId = $data["id"];
$status = "created";

$payStmt = $conn->prepare("INSERT INTO payments (order_id, razorpay_order_id, status) VALUES (?, ?, ?)");
$payStmt->bind_param("iss", $order["id"], $razorpayOrderId, $status);
$payStmt->execute();

echo json_encode([
    "success" => true,
    "razorpay_order_id" => $razorpayOrderId,
    "amount" => $amountPaise
]);
?>