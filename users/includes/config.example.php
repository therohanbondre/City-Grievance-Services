<?php
require_once __DIR__ . '/../../database.php';

try {
    $con = app_database();
} catch (Throwable $exception) {
    error_log('Database connection failed: ' . $exception->getMessage());
    http_response_code(503);
    die('Service temporarily unavailable. Please try again later.');
}
?>
