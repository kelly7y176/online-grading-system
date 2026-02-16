<?php
/**
 * Student Dashboard
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/Submission.php';

if (!isStudent()) {
    redirect(BASE_URL . '/login.php');
}

$assignmentModel = new Assignment();
$submissionModel = new Submission();

// Get all active assignments
$allAssignments = $assignmentModel->getActive();
$mySubmissions = $submissionModel->getByStudent($_SESSION['user_id']);

// Create a map of submissions by assignment_id
$submissionMap = [];
foreach ($mySubmissions as $sub) {
    $submissionMap[$sub['assignment_id']] = $sub;
}

// Add submission status to assignments
$upcomingAssignments = [];
foreach ($allAssignments as $assignment) {
    $assignment['submitted'] = isset($submissionMap[$assignment['id']]);
    if ($assignment['submitted']) {
        $assignment['submission_id'] = $submissionMap[$assignment['id']]['id'];
    }
    $upcomingAssignments[] = $assignment;
}

// Get recent grades
$recentGrades = array_filter($mySubmissions, function($s) {
    return $s['grade'] !== null;
});

// Calculate stats
$activeAssignments = count($allAssignments);
$submittedCount = count($mySubmissions);
$gradedCount = count($recentGrades);
$averageGrade = 0;
if ($gradedCount > 0) {
    $totalPercentage = 0;
    foreach ($recentGrades as $grade) {
        $totalPercentage += ($grade['grade'] / $grade['max_score']) * 100;
    }
    $averageGrade = $totalPercentage / $gradedCount;
}

require_once dirname(__DIR__) . '/views/student/dashboard.php';
