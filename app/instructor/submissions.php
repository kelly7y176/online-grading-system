<?php
/**
 * View Submissions Page
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/controllers/SubmissionController.php';

$controller = new SubmissionController();
$assignmentId = $_GET['assignment_id'] ?? null;

if (!$assignmentId) {
    redirect(BASE_URL . '/instructor/assignments.php');
}

$controller->listByAssignment($assignmentId);
