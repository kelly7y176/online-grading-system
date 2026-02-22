<?php
// Since this file is in /app/, and config is in /app/config/
require_once __DIR__ . '/config/config.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process logic here...
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Instructions sent to your email!',
            // Path from the browser's perspective back to login
            'redirect' => '../../login.php' 
        ]);
        exit;
    }
}