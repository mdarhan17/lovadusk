<?php
require_once "../config/constants.php";
require_once "../config/db.php";

if (APP_ENV !== "local") {
    die("Admin setup is disabled.");
}

$name = "Admin";
$email = "admin@lovadusk.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$phone = "";
$role = "admin";

$check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    echo "Admin already exists.<br>";
    echo "Email: admin@lovadusk.com<br>";
    echo "Password: admin123";
    exit;
}

$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $phone, $password, $role);

if ($stmt->execute()) {
    echo "Admin created successfully.<br>";
    echo "Email: admin@lovadusk.com<br>";
    echo "Password: admin123";
} else {
    echo "Admin creation failed.";
}
?>
