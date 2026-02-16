<?php
/**
 * Authentication Controller
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles user authentication (login, logout, registration).
 * TODO for students: Add password reset, remember me, session timeout
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Display login page
     */
    public function showLogin() {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
        }
        require_once dirname(__DIR__) . '/views/shared/login.php';
    }

    /**
     * Process login request
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/login.php');
            return;
        }

        // Validate CSRF token
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request. Please try again.');
            redirect(BASE_URL . '/login.php');
            return;
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($email) || empty($password)) {
            setFlashMessage('error', 'Please enter both email and password.');
            redirect(BASE_URL . '/login.php');
            return;
        }

        // Attempt authentication
        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Regenerate session ID for security
            session_regenerate_id(true);

            setFlashMessage('success', 'Welcome back, ' . $user['name'] . '!');
            $this->redirectToDashboard();
        } else {
            setFlashMessage('error', 'Invalid email or password.');
            redirect(BASE_URL . '/login.php');
        }
    }

    /**
     * Display registration page
     */
    public function showRegister() {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
        }
        require_once dirname(__DIR__) . '/views/shared/register.php';
    }

    /**
     * Process registration request
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/register.php');
            return;
        }

        // Validate CSRF token
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request. Please try again.');
            redirect(BASE_URL . '/register.php');
            return;
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = sanitize($_POST['role'] ?? 'student');

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Name is required.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }

        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        if (!in_array($role, ['student', 'instructor'])) {
            $errors[] = 'Invalid role selected.';
        }

        if ($this->userModel->emailExists($email)) {
            $errors[] = 'Email is already registered.';
        }

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect(BASE_URL . '/register.php');
            return;
        }

        // Create user
        $userId = $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ]);

        if ($userId) {
            setFlashMessage('success', 'Registration successful! Please log in.');
            redirect(BASE_URL . '/login.php');
        } else {
            setFlashMessage('error', 'Registration failed. Please try again.');
            redirect(BASE_URL . '/register.php');
        }
    }

    /**
     * Process logout request
     */
    public function logout() {
        // Clear all session data
        $_SESSION = [];

        // Destroy session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();

        setFlashMessage('success', 'You have been logged out successfully.');
        redirect(BASE_URL . '/login.php');
    }

    /**
     * Redirect to appropriate dashboard based on role
     */
    private function redirectToDashboard() {
        if (isInstructor()) {
            redirect(BASE_URL . '/instructor/dashboard.php');
        } else {
            redirect(BASE_URL . '/student/dashboard.php');
        }
    }
}
