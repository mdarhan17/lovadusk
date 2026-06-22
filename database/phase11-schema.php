<?php
function ensure_phase11_schema($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(120) NOT NULL UNIQUE,
        setting_value TEXT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $defaults = [
        "site_name" => "LOVA DUSK",
        "hero_eyebrow" => "Limited Drop Fashion",
        "hero_title" => "LOVA DUSK",
        "hero_subtitle" => "Modern silhouettes crafted for quiet luxury.",
        "hero_button_primary" => "Shop Now",
        "hero_button_secondary" => "Explore Drops",
        "featured_title" => "Latest Drops",
        "featured_subtitle" => "New arrivals",
        "footer_tagline" => "Luxury silhouettes. Limited drops. Modern fashion.",
        "contact_email" => "support@lovadusk.com",
        "contact_phone" => "+91 98765 43210",
        "instagram_handle" => "@lovadusk",
        "about_title" => "A Quiet Luxury Fashion House",
        "about_content" => "LOVA DUSK is a luxury drop-based fashion brand focused on clean silhouettes, limited releases and premium styling.",
        "shipping_text" => "Free shipping on all prepaid orders.",
        "return_policy" => "Returns are accepted only for damaged or incorrect items within 3 days of delivery.",
        "privacy_policy" => "We collect only the details required to process orders, payments and customer support.",
        "terms_policy" => "By using LOVA DUSK, you agree to our purchase, payment and order policies."
    ];

    $stmt = $conn->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");

    foreach ($defaults as $key => $value) {
        $stmt->bind_param("ss", $key, $value);
        $stmt->execute();
    }
}

function get_setting($conn, $key, $default = "") {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    return $row ? $row["setting_value"] : $default;
}

function set_setting($conn, $key, $value) {
    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->bind_param("ss", $key, $value);
    return $stmt->execute();
}
?>