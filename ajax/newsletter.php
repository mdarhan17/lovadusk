<?php
require_once "../config/constants.php";
require_once "../config/db.php";
require_once "../includes/functions.php";
require_once "../includes/csrf.php";
require_once "../database/phase9-schema.php";

ensure_phase9_schema($conn);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("");
}

verify_csrf();

$email = strtolower(trim($_POST["email"] ?? ""));

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $stmt = $conn->prepare("INSERT IGNORE INTO subscribers (email) VALUES (?)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
}

redirect("");
?>