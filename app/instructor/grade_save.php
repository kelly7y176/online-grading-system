<?php
/**
 * Save Grade Handler
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/controllers/SubmissionController.php';

$controller = new SubmissionController();
$id = $_GET['id'] ?? null;

if (!$id) {
    redirect(BASE_URL . '/instructor/assignments.php');
}

$controller->grade($id);
