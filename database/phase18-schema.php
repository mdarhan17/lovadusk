<?php
function ensure_phase18_schema($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS instagram_gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image VARCHAR(255) NOT NULL,
        caption VARCHAR(180),
        instagram_link VARCHAR(255),
        status ENUM('active','inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    if (file_exists(__DIR__ . "/phase11-schema.php")) {
        require_once __DIR__ . "/phase11-schema.php";
        ensure_phase11_schema($conn);

        $defaults = [
            "whatsapp_number" => "919876543210",
            "instagram_link" => "https://instagram.com/lovadusk",
            "best_sellers_title" => "Best Sellers",
            "category_title" => "Category Collection",
            "instagram_title" => "Instagram Gallery"
        ];

        foreach ($defaults as $key => $value) {
            set_setting($conn, $key, $value);
        }
    }
}
?>