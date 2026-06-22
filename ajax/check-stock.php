<?php
require_once "../config/db.php";

$productId = (int)($_GET["product_id"] ?? 0);
$size = trim($_GET["size"] ?? "");

$stmt = $conn->prepare("SELECT stock FROM product_sizes WHERE product_id = ? AND size = ? LIMIT 1");
$stmt->bind_param("is", $productId, $size);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

header("Content-Type: application/json");
echo json_encode(["stock" => (int)($row["stock"] ?? 0)]);
?>