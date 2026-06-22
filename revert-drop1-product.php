<?php
require_once "config/constants.php";
require_once "config/db.php";

echo "<h2>LOVA DUSK - Revert Drop 1 Product Changes</h2>";

function table_exists_revert($conn, $table) {
    $table = $conn->real_escape_string($table);
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

function delete_product_full($conn, $slug) {
    $stmt = $conn->prepare("SELECT id, name FROM products WHERE slug = ? LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<p style='color:orange;'>No product found: $slug</p>";
        return;
    }

    $product = $result->fetch_assoc();
    $productId = (int)$product["id"];
    $productName = htmlspecialchars($product["name"]);

    echo "<h3>Removing: $productName</h3>";

    $tables = [
        "product_sizes" => "product_id",
        "product_images" => "product_id",
        "wishlist" => "product_id",
        "product_reviews" => "product_id",
        "cart_items" => "product_id",
        "recently_viewed" => "product_id"
    ];

    foreach ($tables as $table => $column) {
        if (table_exists_revert($conn, $table)) {
            $conn->query("DELETE FROM `$table` WHERE `$column` = $productId");
            echo "<p style='color:green;'>Removed from $table</p>";
        }
    }

    $conn->query("DELETE FROM products WHERE id = $productId");
    echo "<p style='color:green;'>Product deleted from products table.</p>";
}

/* Remove latest correct product */
delete_product_full($conn, "khadi-cotton-dress");

/* Remove old wrong 5 products also, if created earlier */
$oldSlugs = [
    "khadi-cotton-dress-01",
    "khadi-cotton-dress-02",
    "khadi-cotton-dress-03",
    "khadi-cotton-dress-04",
    "khadi-cotton-dress-05"
];

foreach ($oldSlugs as $slug) {
    delete_product_full($conn, $slug);
}

echo "<hr>";
echo "<h3>Revert Done ✅</h3>";
echo "<p>Drop 1 product changes removed.</p>";
echo "<p>Images inside assets/images/drops/ are kept safely.</p>";
echo "<p><a href='shop.php'>Go to Shop</a></p>";
echo "<p><a href='drops/'>Go to Drops</a></p>";
?>
