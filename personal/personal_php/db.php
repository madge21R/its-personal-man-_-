<?php
// db.php - central DB connection using mysqli and error handling
$DB_HOST = 'localhost';
$DB_NAME = 'users_db';
$DB_USER = 'root';       // change if different
$DB_PASS = '';           // change if different

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_errno) {
    // For production, do not expose details. Log them instead.
    error_log("DB connect error: " . $mysqli->connect_error);
    die("Database connection failed.");
}

// set charset
$mysqli->set_charset("utf8mb4");
