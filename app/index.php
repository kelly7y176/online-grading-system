<?php
/**
 * Main Entry Point
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * This is the main entry point for the application.
 * Redirects to appropriate dashboard or login page.
 */

require_once __DIR__ . '/config/config.php';

// Redirect based on authentication status
if (isLoggedIn()) {
    if (isInstructor()) {
        redirect(BASE_URL . '/instructor/dashboard.php');
    } else {
        redirect(BASE_URL . '/student/dashboard.php');
    }
} else {
    redirect(BASE_URL . '/login.php');
}
