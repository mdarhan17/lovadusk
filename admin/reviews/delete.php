<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("DELETE FROM product_reviews WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit;
?>