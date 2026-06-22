<?php
function ensure_phase5_schema($conn) {
    $columns = [
        "customer_name" => "VARCHAR(120) NULL",
        "customer_email" => "VARCHAR(120) NULL",
        "customer_phone" => "VARCHAR(30) NULL",
        "address_line" => "TEXT NULL",
        "city" => "VARCHAR(80) NULL",
        "state" => "VARCHAR(80) NULL",
        "pincode" => "VARCHAR(20) NULL",
        "payment_method" => "VARCHAR(50) DEFAULT 'razorpay'",
        "notes" => "TEXT NULL"
    ];

    foreach ($columns as $column => $definition) {
        $check = $conn->prepare("SELECT COUNT(*) AS total FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = ?");
        $check->bind_param("s", $column);
        $check->execute();
        $exists = (int)$check->get_result()->fetch_assoc()["total"];

        if ($exists === 0) {
            $conn->query("ALTER TABLE orders ADD `$column` $definition");
        }
    }

    $conn->query("CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        razorpay_order_id VARCHAR(255),
        razorpay_payment_id VARCHAR(255),
        razorpay_signature VARCHAR(255),
        status VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}
?>