<?php
/**
 * Process Submission Handler
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/controllers/SubmissionController.php';

$controller = new SubmissionController();
$id = $_GET['id'] ?? null;

if (!$id) {
    redirect(BASE_URL . '/student/assignments.php');
}

$controller->submit($id);
