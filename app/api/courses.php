<?php
/**
 * Courses API Endpoint
 * CS425 Assignment Grading System - NEW ENDPOINT (Team A Easy Level)
 * 
 * This is a demonstration of adding a new API endpoint.
 * Handles course-related operations via API.
 * 
 * Endpoints:
 *   GET /api/courses          - List all courses
 *   GET /api/courses/{id}     - Get single course by ID
 *   POST /api/courses         - Create new course (instructor only)
 *   PUT /api/courses/{id}     - Update course (instructor only)
 *   DELETE /api/courses/{id}  - Delete course (instructor only)
 */

require_once dirname(__DIR__) . '/models/Course.php';

$courseModel = new Course();

// Authentication: Only require auth for non-GET requests
if ($method !== 'GET') {
    apiAuth();
}

switch ($method) {
    case 'GET':
        if ($id) {
            // GET /api/courses/{id} - Get single course
            $course = $courseModel->findById($id);
            
            if (!$course) {
                apiError('Course not found', 404);
            }
            
            apiResponse($course);
        } else {
            // GET /api/courses - List all courses
            // Optional query parameters: ?instructor_id=1 or ?search=web
            $instructorId = $_GET['instructor_id'] ?? null;
            $search = $_GET['search'] ?? null;
            
            if ($instructorId) {
                $courses = $courseModel->getByInstructor($instructorId);
            } elseif ($search) {
                $courses = $courseModel->search($search);
            } else {
                $courses = $courseModel->getAll();
            }
            
            apiResponse($courses);
        }
        break;

    case 'POST':
        // POST /api/courses - Create new course (instructor only)
        if (!isInstructor()) {
            apiError('Only instructors can create courses', 403);
        }
        
        // Validate required fields
        if (empty($input['code']) || empty($input['name'])) {
            apiError('Course code and name are required', 400);
        }
        
        // Check if course code already exists
        if ($courseModel->codeExists($input['code'])) {
            apiError('Course code already exists', 400);
        }
        
        $courseData = [
            'code' => sanitize($input['code']),
            'name' => sanitize($input['name']),
            'description' => sanitize($input['description'] ?? ''),
            'instructor_id' => $_SESSION['user_id']
        ];
        
        $courseId = $courseModel->create($courseData);
        
        if (!$courseId) {
            apiError('Failed to create course', 500);
        }
        
        $newCourse = $courseModel->findById($courseId);
        apiResponse($newCourse, 201);
        break;

    case 'PUT':
        // PUT /api/courses/{id} - Update course (instructor only)
        if (!$id) {
            apiError('Course ID required', 400);
        }
        
        if (!isInstructor()) {
            apiError('Only instructors can update courses', 403);
        }
        
        $course = $courseModel->findById($id);
        
        if (!$course) {
            apiError('Course not found', 404);
        }
        
        // Only the course instructor can update it
        if ($course['instructor_id'] != $_SESSION['user_id']) {
            apiError('Not authorized to update this course', 403);
        }
        
        $updateData = [];
        
        if (isset($input['name'])) {
            $updateData['name'] = sanitize($input['name']);
        }
        
        if (isset($input['description'])) {
            $updateData['description'] = sanitize($input['description']);
        }
        
        if (isset($input['code'])) {
            $newCode = sanitize($input['code']);
            if ($courseModel->codeExists($newCode, $id)) {
                apiError('Course code already in use', 400);
            }
            $updateData['code'] = $newCode;
        }
        
        if (empty($updateData)) {
            apiError('No valid fields to update', 400);
        }
        
        $result = $courseModel->update($id, $updateData);
        
        if (!$result) {
            apiError('Failed to update course', 500);
        }
        
        $updatedCourse = $courseModel->findById($id);
        apiResponse($updatedCourse);
        break;

    case 'DELETE':
        // DELETE /api/courses/{id} - Delete course (instructor only)
        if (!$id) {
            apiError('Course ID required', 400);
        }
        
        if (!isInstructor()) {
            apiError('Only instructors can delete courses', 403);
        }
        
        $course = $courseModel->findById($id);
        
        if (!$course) {
            apiError('Course not found', 404);
        }
        
        // Only the course instructor can delete it
        if ($course['instructor_id'] != $_SESSION['user_id']) {
            apiError('Not authorized to delete this course', 403);
        }
        
        $result = $courseModel->delete($id);
        
        if (!$result) {
            apiError('Failed to delete course', 500);
        }
        
        apiResponse(['message' => 'Course deleted successfully']);
        break;

    default:
        apiError('Method not allowed', 405);
}
