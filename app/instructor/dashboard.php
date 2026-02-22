<?php
/**
 * Instructor Dashboard Controller
 * public/instructor/dashboard.php
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/Submission.php';
require_once dirname(__DIR__) . '/models/User.php';

// 1. Check authentication
if (!isInstructor()) {
    redirect(BASE_URL . '/login.php');
}

// 2. Initialize Models
$assignmentModel = new Assignment();
$submissionModel = new Submission();
$userModel = new User();

// 3. Get Assignments Data
$recentAssignments = $assignmentModel->getByInstructor($_SESSION['user_id']);

foreach ($recentAssignments as &$assignment) {
    $assignment['submission_count'] = $assignmentModel->getSubmissionCount($assignment['id']);
    $assignment['graded_count'] = $assignmentModel->getGradedCount($assignment['id']);
}

// 4. Get Submissions Data
$ungradedSubmissions = $submissionModel->getUngradedByInstructor($_SESSION['user_id']);

// 5. Prepare Variables for the View (These must match your dashboard.php)
$pendingSubmissions = count($ungradedSubmissions);
$totalAssignments = count($recentAssignments);
$totalStudents = count($userModel->getAllStudents());
$gradedToday = 0; // Optional: implement logic later

// 6. Map to $stats array as a backup
$stats = [
    'total_students' => $totalStudents,
    'total_assignments' => $totalAssignments,
    'pending_grades' => $pendingSubmissions
];

// 7. Load the View
require_once dirname(__DIR__) . '/views/instructor/dashboard.php';
