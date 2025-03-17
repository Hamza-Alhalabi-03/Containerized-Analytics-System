<?php
session_start();

// API endpoint to verify authentication
header('Content-Type: application/json');

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    echo json_encode([
        'authenticated' => true,
        'username' => $_SESSION['username'],
        'name' => $_SESSION['name']
    ]);
} else {
    echo json_encode([
        'authenticated' => false
    ]);
}
?>