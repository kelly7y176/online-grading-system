<?php
/**
 * Authentication API Endpoint
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles user authentication via API.
 * TODO for students: Add JWT tokens, refresh tokens, password reset
 */

require_once dirname(__DIR__) . '/models/User.php';

$userModel = new User();

switch ($method) {
    case 'POST':
        if ($action === 'login' || $id === 'login') {
            // Login
            $email = sanitize($input['email'] ?? '');
            $password = $input['password'] ?? '';

            if (empty($email) || empty($password)) {
                apiError('Email and password are required');
            }

            $user = $userModel->authenticate($email, $password);

            if (!$user) {
                apiError('Invalid credentials', 401);
            }

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            session_regenerate_id(true);

            apiResponse([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ]);
        } elseif ($action === 'register' || $id === 'register') {
            // Register
            $name = sanitize($input['name'] ?? '');
            $email = sanitize($input['email'] ?? '');
            $password = $input['password'] ?? '';
            $role = sanitize($input['role'] ?? 'student');

            // Validation
            $errors = [];

            if (empty($name)) {
                $errors[] = 'Name is required';
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required';
            }

            if (strlen($password) < PASSWORD_MIN_LENGTH) {
                $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
            }

            if (!in_array($role, ['student', 'instructor'])) {
                $errors[] = 'Invalid role';
            }

            if ($userModel->emailExists($email)) {
                $errors[] = 'Email already registered';
            }

            if (!empty($errors)) {
                apiError(implode(', ', $errors));
            }

            $userId = $userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => $role
            ]);

            if (!$userId) {
                apiError('Registration failed', 500);
            }

            $user = $userModel->findById($userId);
            unset($user['password']);

            apiResponse([
                'message' => 'Registration successful',
                'user' => $user
            ], 201);
        } elseif ($action === 'logout' || $id === 'logout') {
            // Logout
            $_SESSION = [];
            session_destroy();

            apiResponse(['message' => 'Logout successful']);
        } else {
            apiError('Invalid action', 400);
        }
        break;

    case 'GET':
        if ($action === 'me' || $id === 'me') {
            // Get current user
            if (!isLoggedIn()) {
                apiError('Not authenticated', 401);
            }

            $user = $userModel->findById($_SESSION['user_id']);
            unset($user['password']);

            apiResponse($user);
        } elseif ($action === 'check' || $id === 'check') {
            // Check authentication status
            apiResponse([
                'authenticated' => isLoggedIn(),
                'user' => isLoggedIn() ? [
                    'id' => $_SESSION['user_id'],
                    'name' => $_SESSION['user_name'],
                    'email' => $_SESSION['user_email'],
                    'role' => $_SESSION['user_role']
                ] : null
            ]);
        } else {
            apiError('Invalid action', 400);
        }
        break;

    default:
        apiError('Method not allowed', 405);
}
