<?php
function ensure_phase8_schema($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS coupons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) NOT NULL UNIQUE,
        discount_type ENUM('fixed','percentage') DEFAULT 'fixed',
        discount_value DECIMAL(10,2) NOT NULL,
        min_order DECIMAL(10,2) DEFAULT 0,
        max_discount DECIMAL(10,2) DEFAULT 0,
        starts_at DATETIME NULL,
        expires_at DATETIME NULL,
        usage_limit INT DEFAULT 0,
        used_count INT DEFAULT 0,
        status ENUM('active','inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_product (user_id, product_id)
    )");

    $columns = [
        "coupon_code" => "VARCHAR(50) NULL",
        "discount_amount" => "DECIMAL(10,2) DEFAULT 0"
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
}

function calculate_coupon_discount($conn, $code, $subtotal) {
    $code = strtoupper(trim($code));

    if ($code === "" || $subtotal <= 0) {
        return ["valid" => false, "discount" => 0, "message" => "Invalid coupon."];
    }

    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' LIMIT 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $coupon = $stmt->get_result()->fetch_assoc();

    if (!$coupon) {
        return ["valid" => false, "discount" => 0, "message" => "Coupon not found."];
    }

    $now = date("Y-m-d H:i:s");

    if (!empty($coupon["starts_at"]) && $coupon["starts_at"] > $now) {
        return ["valid" => false, "discount" => 0, "message" => "Coupon not started yet."];
    }

    if (!empty($coupon["expires_at"]) && $coupon["expires_at"] < $now) {
        return ["valid" => false, "discount" => 0, "message" => "Coupon expired."];
    }

    if ((float)$subtotal < (float)$coupon["min_order"]) {
        return ["valid" => false, "discount" => 0, "message" => "Minimum order amount not reached."];
    }

    if ((int)$coupon["usage_limit"] > 0 && (int)$coupon["used_count"] >= (int)$coupon["usage_limit"]) {
        return ["valid" => false, "discount" => 0, "message" => "Coupon usage limit reached."];
    }

    if ($coupon["discount_type"] === "percentage") {
        $discount = ((float)$subtotal * (float)$coupon["discount_value"]) / 100;
        if ((float)$coupon["max_discount"] > 0) {
            $discount = min($discount, (float)$coupon["max_discount"]);
        }
    } else {
        $discount = (float)$coupon["discount_value"];
    }

    $discount = min($discount, (float)$subtotal);

    return [
        "valid" => true,
        "coupon" => $coupon,
        "discount" => round($discount, 2),
        "message" => "Coupon applied."
    ];
}
?>