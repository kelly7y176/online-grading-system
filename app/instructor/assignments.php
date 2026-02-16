<?php
/**
 * Instructor Assignments List
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/controllers/AssignmentController.php';

$controller = new AssignmentController();
$controller->index();
