<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";

$id = (int)($_GET["id"] ?? 0);
$status = $_GET["status"] ?? "pending";

$allowed = ["pending", "approved", "rejected"];

if (!in_array($status, $allowed)) {
    $status = "pending";
}

$stmt = $conn->prepare("UPDATE product_reviews SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: index.php");
exit;
?>