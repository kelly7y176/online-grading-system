<?php
/**
 * AJAX-Enabled Process Submission Handler
 * CS425 Assignment Grading System
 */

// 1. Setup - Path remains the same to reach your controller
require_once dirname(__DIR__) . '/controllers/SubmissionController.php';

// 2. Initialize Controller
$controller = new SubmissionController();
$id = $_GET['id'] ?? null;

// 3. AJAX Detection
// We check if the 'X-Requested-With' header is present (set by your fetch call in main.js)
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// 4. Basic Validation before calling controller
if (!$id) {
    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid Assignment ID']);
        exit;
    }
    redirect(BASE_URL . '/student/assignments.php');
}

/**
 * 5. Handle the Submission
 * We use Output Buffering to "catch" any redirects or echoes 
 * that the controller might perform, allowing us to control the response.
 */
if ($isAjax) {
    ob_start(); // Start capturing output
    try {
        // We call the original controller method
        $controller->submit($id);
        
        // Clear the buffer (we don't want the controller's automatic redirect to trigger)
        ob_end_clean(); 

        // Return a clean JSON response to your main.js
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Assignment uploaded and submitted successfully!',
            'redirect' => BASE_URL . '/student/submissions.php'
        ]);
        exit;
    } catch (Exception $e) {
        ob_end_clean();
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Server Error: ' . $e->getMessage()
        ]);
        exit;
    }
} else {
    // 6. Normal Fallback (if JavaScript is disabled)
    $controller->submit($id);
}