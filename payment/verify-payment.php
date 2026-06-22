<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../config/razorpay.php";
require_once "../includes/csrf.php";
require_once "../database/phase5-schema.php";
require_once "../mail/order-confirmation.php";

header("Content-Type: application/json");

ensure_phase5_schema($conn);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

verify_csrf();

$userId = (int)$_SESSION["user_id"];
$orderNumber = trim($_POST["order_number"] ?? "");
$razorpayOrderId = trim($_POST["razorpay_order_id"] ?? "");
$razorpayPaymentId = trim($_POST["razorpay_payment_id"] ?? "");
$razorpaySignature = trim($_POST["razorpay_signature"] ?? "");

if ($orderNumber === "" || $razorpayOrderId === "" || $razorpayPaymentId === "" || $razorpaySignature === "") {
    echo json_encode(["success" => false, "message" => "Missing payment details."]);
    exit;
}

$generatedSignature = hash_hmac("sha256", $razorpayOrderId . "|" . $razorpayPaymentId, RAZORPAY_KEY_SECRET);

if (!hash_equals($generatedSignature, $razorpaySignature)) {
    echo json_encode(["success" => false, "message" => "Payment signature verification failed."]);
    exit;
}

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ? LIMIT 1 FOR UPDATE");
    $stmt->bind_param("si", $orderNumber, $userId);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) {
        throw new Exception("Order not found.");
    }

    if ($order["payment_status"] !== "paid") {
        $paymentStatus = "paid";
        $orderStatus = "placed";

        $updateOrder = $conn->prepare("UPDATE orders SET payment_status = ?, order_status = ?, payment_method = 'razorpay' WHERE id = ?");
        $updateOrder->bind_param("ssi", $paymentStatus, $orderStatus, $order["id"]);
        $updateOrder->execute();

        $updatePayment = $conn->prepare("UPDATE payments SET razorpay_payment_id = ?, razorpay_signature = ?, status = 'paid' WHERE order_id = ? AND razorpay_order_id = ?");
        $updatePayment->bind_param("ssis", $razorpayPaymentId, $razorpaySignature, $order["id"], $razorpayOrderId);
        $updatePayment->execute();

        $itemsStmt = $conn->prepare("SELECT order_items.*, products.name FROM order_items INNER JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = ?");
        $itemsStmt->bind_param("i", $order["id"]);
        $itemsStmt->execute();
        $itemsResult = $itemsStmt->get_result();

        $itemsForMail = [];

        $sizeStockStmt = $conn->prepare("UPDATE product_sizes SET stock = stock - ? WHERE product_id = ? AND size = ? AND stock >= ?");
        $productStockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");

        while ($item = $itemsResult->fetch_assoc()) {
            $itemsForMail[] = $item;

            $qty = (int)$item["qty"];
            $productId = (int)$item["product_id"];
            $size = $item["size"];

            $sizeStockStmt->bind_param("iisi", $qty, $productId, $size, $qty);
            $sizeStockStmt->execute();

            $productStockStmt->bind_param("iii", $qty, $productId, $qty);
            $productStockStmt->execute();
        }

        $clearCart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clearCart->bind_param("i", $userId);
        $clearCart->execute();

        log_order_confirmation($order, $itemsForMail);
    }

    $conn->commit();

    echo json_encode([
        "success" => true,
        "redirect" => BASE_URL . "checkout/success.php?order=" . urlencode($orderNumber)
    ]);
} catch (Exception $e) {
    $conn->rollback();

    echo json_encode([
        "success" => false,
        "message" => "Payment verified, but order update failed. Contact support."
    ]);
}
?>