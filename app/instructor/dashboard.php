<?php
/**
 * Instructor Dashboard
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/Submission.php';
require_once dirname(__DIR__) . '/models/User.php';

// Check authentication
if (!isInstructor()) {
    redirect(BASE_URL . '/login.php');
}

$assignmentModel = new Assignment();
$submissionModel = new Submission();
$userModel = new User();

// Get dashboard data
$recentAssignments = $assignmentModel->getByInstructor($_SESSION['user_id']);
foreach ($recentAssignments as &$assignment) {
    $assignment['submission_count'] = $assignmentModel->getSubmissionCount($assignment['id']);
    $assignment['graded_count'] = $assignmentModel->getGradedCount($assignment['id']);
}

$ungradedSubmissions = $submissionModel->getUngradedByInstructor($_SESSION['user_id']);
$totalAssignments = count($recentAssignments);
$pendingSubmissions = count($ungradedSubmissions);
$totalStudents = count($userModel->getAllStudents());
$gradedToday = 0; // TODO: Implement this query

require_once dirname(__DIR__) . '/views/instructor/dashboard.php';
