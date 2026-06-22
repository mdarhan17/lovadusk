<?php
require_once "../includes/auth-check.php";

unset($_SESSION["coupon_code"]);
$_SESSION["coupon_message"] = "Coupon removed.";

redirect("cart/");
?>