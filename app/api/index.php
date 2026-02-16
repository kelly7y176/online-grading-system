<?php
/**
 * API Router
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * RESTful API endpoint handler.
 * TODO for students: Add rate limiting, API versioning, better error handling
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once dirname(__DIR__) . '/config/config.php';

// Parse request
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/api';
$path = parse_url($requestUri, PHP_URL_PATH);
$path = str_replace($basePath, '', $path);
$path = trim($path, '/');
$segments = explode('/', $path);

$method = $_SERVER['REQUEST_METHOD'];

// Get request body for POST/PUT
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Simple routing
$resource = $segments[0] ?? '';
$id = $segments[1] ?? null;
$action = $segments[2] ?? null;

// API Response helper
function apiResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $statusCode >= 200 && $statusCode < 300,
        'data' => $data,
        'timestamp' => date('c')
    ]);
    exit();
}

function apiError($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'error' => $message,
        'timestamp' => date('c')
    ]);
    exit();
}

// Authentication check for API
function apiAuth() {
    // Check for Bearer token or session
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (strpos($authHeader, 'Bearer ') === 0) {
        $token = substr($authHeader, 7);
        // TODO: Implement JWT or token validation
        // For now, simple session-based auth
    }
    
    if (!isLoggedIn()) {
        apiError('Unauthorized', 401);
    }
}

// Route to appropriate handler
try {
    switch ($resource) {
        case 'assignments':
            require_once __DIR__ . '/assignments.php';
            break;
        case 'submissions':
            require_once __DIR__ . '/submissions.php';
            break;
        case 'users':
            require_once __DIR__ . '/users.php';
            break;
        case 'rubrics':
            require_once __DIR__ . '/rubrics.php';
            break;
        case 'grades':
            require_once __DIR__ . '/grades.php';
            break;
        case 'courses':
            require_once __DIR__ . '/courses.php';
            break;
        case 'auth':
            require_once __DIR__ . '/auth.php';
            break;
        case 'health':
            apiResponse(['status' => 'ok', 'version' => APP_VERSION]);
            break;
        default:
            apiError('Endpoint not found', 404);
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    apiError('Internal server error', 500);
}
