<?php
/**
 * View Assignment Page
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/controllers/AssignmentController.php';

$controller = new AssignmentController();
$id = $_GET['id'] ?? null;

if (!$id) {
    redirect(BASE_URL . '/instructor/assignments.php');
}

$controller->show($id);
