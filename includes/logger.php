<?php
function app_log($type, $message) {
    $dir = __DIR__ . "/../storage/logs";

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $line = "[" . date("Y-m-d H:i:s") . "] " . strtoupper($type) . " - " . $message . PHP_EOL;
    file_put_contents($dir . "/app.log", $line, FILE_APPEND);
}

function admin_activity($conn, $action, $details = "") {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $adminId = $_SESSION["admin_id"] ?? null;
    $ip = $_SERVER["REMOTE_ADDR"] ?? "";
    $userAgent = $_SERVER["HTTP_USER_AGENT"] ?? "";

    $conn->query("CREATE TABLE IF NOT EXISTS admin_activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id INT NULL,
        action VARCHAR(180) NOT NULL,
        details TEXT NULL,
        ip_address VARCHAR(60),
        user_agent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $stmt = $conn->prepare("INSERT INTO admin_activity_logs (admin_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $adminId, $action, $details, $ip, $userAgent);
    $stmt->execute();
}
?>