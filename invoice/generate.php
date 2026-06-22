<?php
require_once "../includes/auth-check.php";

$orderNumber = urlencode($_GET["order"] ?? "");
header("Location: " . BASE_URL . "user/invoice.php?order=" . $orderNumber);
exit;
?>