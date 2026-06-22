<?php
require_once "../../includes/admin-check.php";
require_once "../../config/db.php";
require_once "../../database/phase18-schema.php";

ensure_phase18_schema($conn);

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("DELETE FROM instagram_gallery WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit;
?>