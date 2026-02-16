<?php
/**
 * Logout Handler
 * CS425 Assignment Grading System
 */

require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController();
$auth->logout();
