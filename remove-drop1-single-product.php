<?php
require_once "config/constants.php";
require_once "config/db.php";

echo "<h2>LOVA DUSK - Remove Drop 1 Single Product Changes</h2>";

function table_exists_remove($conn, $table) {
    $table = $conn->real_escape_string($table);
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

$productSlug = "khadi-cotton-dress";

$stmt = $conn->prepare("SELECT id, name FROM products WHERE slug = ? LIMIT 1");
$stmt->bind_param("s", $productSlug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p style='color:orange;'>No product found with slug: khadi-cotton-dress</p>";
} else {
    $product = $result->fetch_assoc();
    $productId = (int)$product["id"];

    echo "<p>Found product: " . htmlspecialchars($product["name"]) . "</p>";

    if (table_exists_remove($conn, "product_sizes")) {
        $conn->query("DELETE FROM product_sizes WHERE product_id = $productId");
        echo "<p style='color:green;'>Product sizes removed.</p>";
    }

    if (table_exists_remove($conn, "product_images")) {
        $conn->query("DELETE FROM product_images WHERE product_id = $productId");
        echo "<p style='color:green;'>Product gallery images removed.</p>";
    }

    if (table_exists_remove($conn, "wishlist")) {
        $conn->query("DELETE FROM wishlist WHERE product_id = $productId");
        echo "<p style='color:green;'>Wishlist records removed.</p>";
    }

    if (table_exists_remove($conn, "product_reviews")) {
        $conn->query("DELETE FROM product_reviews WHERE product_id = $productId");
        echo "<p style='color:green;'>Reviews removed.</p>";
    }

    if (table_exists_remove($conn, "cart_items")) {
        $conn->query("DELETE FROM cart_items WHERE product_id = $productId");
        echo "<p style='color:green;'>Cart records removed.</p>";
    }

    $conn->query("DELETE FROM products WHERE id = $productId");
    echo "<p style='color:green;'>Product removed successfully.</p>";
}

echo "<hr>";
echo "<h3>Done bhai ✅</h3>";
echo "<p>Single product change removed. Drop 1 and category are kept safely.</p>";
echo "<p><a href='shop.php'>Go to Shop</a></p>";
echo "<p><a href='drops/'>Go to Drops</a></p>";
?>
