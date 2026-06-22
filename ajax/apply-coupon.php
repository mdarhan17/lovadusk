<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../includes/csrf.php";
require_once "../database/phase8-schema.php";

ensure_phase8_schema($conn);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("cart/");
}

verify_csrf();

$userId = (int)$_SESSION["user_id"];
$code = strtoupper(trim($_POST["coupon_code"] ?? ""));

$stmt = $conn->prepare("SELECT cart.qty, products.price, products.sale_price
                        FROM cart
                        INNER JOIN products ON cart.product_id = products.id
                        WHERE cart.user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$subtotal = 0;
while ($item = $result->fetch_assoc()) {
    $price = $item["sale_price"] ? (float)$item["sale_price"] : (float)$item["price"];
    $subtotal += $price * (int)$item["qty"];
}

$check = calculate_coupon_discount($conn, $code, $subtotal);

if ($check["valid"]) {
    $_SESSION["coupon_code"] = $code;
    $_SESSION["coupon_message"] = "Coupon applied successfully.";
} else {
    unset($_SESSION["coupon_code"]);
    $_SESSION["coupon_message"] = $check["message"];
}

redirect("cart/");
?>