<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";

$userId = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT cart.*, products.price, products.sale_price 
FROM cart 
JOIN products ON cart.product_id = products.id 
WHERE cart.user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;

$items = [];

while($row = $result->fetch_assoc()){
    $price = $row['sale_price'] ?: $row['price'];
    $total += $price * $row['qty'];

    $items[] = $row;
}

$orderId = "ODR".time().rand(100,999);

$conn->query("INSERT INTO orders(user_id, order_number, total_amount, payment_status, order_status) 
VALUES ($userId, '$orderId', $total, 'pending', 'placed')");

$orderDBId = $conn->insert_id;

foreach($items as $i){
    $price = $i['sale_price'] ?: $i['price'];

    $conn->query("INSERT INTO order_items(order_id, product_id, qty, price)
    VALUES ($orderDBId, {$i['product_id']}, {$i['qty']}, $price)");
}

$conn->query("DELETE FROM cart WHERE user_id = $userId");

header("Location: success.php?order=$orderId");
exit;
?>