<?php
/**
 * My Submissions Page
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/controllers/SubmissionController.php';

$controller = new SubmissionController();
$controller->mySubmissions();
