<?php
/**
 * Users API Endpoint
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles user management via API.
 * TODO for students: Add user profile updates, avatar upload
 */

require_once dirname(__DIR__) . '/models/User.php';

$userModel = new User();

// TEMPORARY: Skip authentication for GET requests (for in-class exercise testing)
// TODO: Remove this bypass before production deployment
if ($method !== 'GET') {
    apiAuth();
}


switch ($method) {
    case 'GET':
        if ($id) {
            // Get single user
            $user = $userModel->findById($id);
            
            if (!$user) {
                apiError('User not found', 404);
            }

            // Remove sensitive data
            unset($user['password']);
            
            apiResponse($user);
        } else {
            // List users (instructors only)
            if (!isInstructor()) {
                apiError('Not authorized', 403);
            }

            $role = $_GET['role'] ?? null;
            
            if ($role === 'student') {
                $users = $userModel->getAllStudents();
            } elseif ($role === 'instructor') {
                $users = $userModel->getAllInstructors();
            } else {
                // Get all students by default
                $users = $userModel->getAllStudents();
            }
            
            apiResponse($users);
        }
        break;

    case 'PUT':
        if (!$id) {
            apiError('User ID required');
        }

        // Users can only update their own profile
        if ($id != $_SESSION['user_id']) {
            apiError('Not authorized to update this user', 403);
        }

        $user = $userModel->findById($id);
        
        if (!$user) {
            apiError('User not found', 404);
        }

        $updateData = [];

        if (isset($input['name'])) {
            $updateData['name'] = sanitize($input['name']);
        }

        if (isset($input['email'])) {
            $newEmail = sanitize($input['email']);
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                apiError('Invalid email format');
            }
            if ($userModel->emailExists($newEmail, $id)) {
                apiError('Email already in use');
            }
            $updateData['email'] = $newEmail;
        }

        if (isset($input['password']) && !empty($input['password'])) {
            if (strlen($input['password']) < PASSWORD_MIN_LENGTH) {
                apiError('Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters');
            }
            $updateData['password'] = $input['password'];
        }

        if (empty($updateData)) {
            apiError('No valid fields to update');
        }

        $result = $userModel->update($id, $updateData);

        if (!$result) {
            apiError('Failed to update user', 500);
        }

        // Update session if needed
        if (isset($updateData['name'])) {
            $_SESSION['user_name'] = $updateData['name'];
        }
        if (isset($updateData['email'])) {
            $_SESSION['user_email'] = $updateData['email'];
        }

        $updatedUser = $userModel->findById($id);
        unset($updatedUser['password']);

        apiResponse($updatedUser);
        break;

    default:
        apiError('Method not allowed', 405);
}
