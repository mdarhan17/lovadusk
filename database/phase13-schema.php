<?php
function ensure_phase13_schema($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS addresses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        full_name VARCHAR(120),
        phone VARCHAR(30),
        address_line TEXT,
        city VARCHAR(80),
        state VARCHAR(80),
        pincode VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}
?>