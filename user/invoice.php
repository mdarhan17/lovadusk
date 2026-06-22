<?php
require_once "../includes/auth-check.php";
require_once "../config/db.php";
require_once "../database/phase5-schema.php";
require_once "../invoice/template.php";

ensure_phase5_schema($conn);

$userId = (int)$_SESSION["user_id"];
$orderNumber = trim($_GET["order"] ?? "");

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("si", $orderNumber, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Invoice not found.");
}

$itemStmt = $conn->prepare("SELECT order_items.*, products.name
                            FROM order_items
                            INNER JOIN products ON order_items.product_id = products.id
                            WHERE order_items.order_id = ?");
$itemStmt->bind_param("i", $order["id"]);
$itemStmt->execute();
$result = $itemStmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$pageTitle = "Invoice | LOVA DUSK";
include "../includes/header.php";
?>

<section class="invoice-page">
    <div class="invoice-actions">
        <a href="orders.php" class="btn btn-light">Back to Orders</a>
        <button onclick="window.print()" class="btn btn-dark">Print Invoice</button>
    </div>

    <?= render_invoice_html($order, $items); ?>
</section>

<?php include "../includes/footer.php"; ?>