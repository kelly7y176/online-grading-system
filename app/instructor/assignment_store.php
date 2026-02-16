<?php
/**
 * Store Assignment Handler
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/controllers/AssignmentController.php';

$controller = new AssignmentController();
$controller->store();
