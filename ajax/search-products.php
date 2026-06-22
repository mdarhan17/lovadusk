<?php
require_once "../config/db.php";
require_once "../includes/functions.php";

$q = trim($_GET["q"] ?? "");
$items = [];

if ($q !== "") {
    $like = "%" . $q . "%";
    $stmt = $conn->prepare("SELECT id, name FROM products WHERE status = 'active' AND name LIKE ? LIMIT 10");
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

header("Content-Type: application/json");
echo json_encode($items);
?>