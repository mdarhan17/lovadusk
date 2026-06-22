<?php
require_once "../config/db.php";
require_once "../includes/logger.php";

$payload = file_get_contents("php://input");
app_log("razorpay_webhook", $payload ?: "Empty webhook payload");

http_response_code(200);
echo "OK";
?>