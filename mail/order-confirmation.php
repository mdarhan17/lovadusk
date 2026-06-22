<?php
function log_order_confirmation($order, $items) {
    $logDir = __DIR__ . "/../storage/logs";

    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $message = "ORDER CONFIRMATION\n";
    $message .= "Date: " . date("Y-m-d H:i:s") . "\n";
    $message .= "Order: " . ($order["order_number"] ?? "") . "\n";
    $message .= "Customer: " . ($order["customer_name"] ?? "") . "\n";
    $message .= "Email: " . ($order["customer_email"] ?? "") . "\n";
    $message .= "Total: " . ($order["total_amount"] ?? "") . "\n";
    $message .= "Items:\n";

    foreach ($items as $item) {
        $message .= "- " . $item["name"] . " | Size: " . $item["size"] . " | Qty: " . $item["qty"] . " | Price: " . $item["price"] . "\n";
    }

    $message .= "-----------------------------\n";

    file_put_contents($logDir . "/mail.log", $message, FILE_APPEND);
}
?>