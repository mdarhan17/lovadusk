<?php
session_start();
unset($_SESSION["admin_id"]);
unset($_SESSION["admin_name"]);
unset($_SESSION["admin_email"]);

require_once "../config/constants.php";

header("Location: " . BASE_URL . "admin/login.php");
exit;
?>
