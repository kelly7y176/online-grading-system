<?php
/**
 * Registration Page
 * CS425 Assignment Grading System
 */

require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->register();
} else {
    $auth->showRegister();
}
