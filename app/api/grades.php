<?php
/**
 * Grades API Endpoint - DATA ONLY
 */
require_once dirname(__DIR__) . '/models/Submission.php';

$submissionModel = new Submission();
apiAuth(); // Ensure user is logged in

if ($method === 'GET') {
    $submissions = $submissionModel->getByStudent($_SESSION['user_id']);
    
    // Filter to only graded ones for the API response
    $grades = array_filter($submissions, function($s) {
        return $s['grade'] !== null;
    });

    // Return clean JSON
    apiResponse(array_values($grades)); 
} else {
    apiError('Method not allowed', 405);
}