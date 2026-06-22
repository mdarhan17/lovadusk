<?php
require_once "config/constants.php";
require_once "config/db.php";

$files = [
    "database/phase5-schema.php",
    "database/phase8-schema.php",
    "database/phase9-schema.php",
    "database/phase11-schema.php",
    "database/phase13-schema.php"
];

foreach ($files as $file) {
    if (file_exists($file)) {
        require_once $file;
    }
}

if (function_exists("ensure_phase5_schema")) {
    ensure_phase5_schema($conn);
}

if (function_exists("ensure_phase8_schema")) {
    ensure_phase8_schema($conn);
}

if (function_exists("ensure_phase9_schema")) {
    ensure_phase9_schema($conn);
}

if (function_exists("ensure_phase11_schema")) {
    ensure_phase11_schema($conn);
}

if (function_exists("ensure_phase13_schema")) {
    ensure_phase13_schema($conn);
}

echo "All schema updates completed successfully.";
?>