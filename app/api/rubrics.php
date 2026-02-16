<?php
/**
 * Rubrics API Endpoint
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles rubric CRUD operations via API.
 * TODO for students: Add rubric templates, import/export
 */

require_once dirname(__DIR__) . '/models/Rubric.php';
require_once dirname(__DIR__) . '/models/Assignment.php';

$rubricModel = new Rubric();
$assignmentModel = new Assignment();

switch ($method) {
    case 'GET':
        if ($id) {
            // Get single rubric
            $rubric = $rubricModel->findById($id);
            
            if (!$rubric) {
                apiError('Rubric not found', 404);
            }
            
            apiResponse($rubric);
        } else {
            // Get rubrics by assignment
            $assignmentId = $_GET['assignment_id'] ?? null;
            
            if (!$assignmentId) {
                apiError('Assignment ID is required');
            }
            
            $rubrics = $rubricModel->getByAssignment($assignmentId);
            $totalPoints = $rubricModel->getTotalMaxPoints($assignmentId);
            
            apiResponse([
                'rubrics' => $rubrics,
                'total_max_points' => $totalPoints
            ]);
        }
        break;

    case 'POST':
        apiAuth();
        
        if (!isInstructor()) {
            apiError('Only instructors can create rubrics', 403);
        }

        $assignmentId = $input['assignment_id'] ?? null;
        
        if (!$assignmentId) {
            apiError('Assignment ID is required');
        }

        // Verify instructor owns this assignment
        $assignment = $assignmentModel->findById($assignmentId);
        
        if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
            apiError('Not authorized', 403);
        }

        if (empty($input['criterion_name']) || !isset($input['max_points'])) {
            apiError('Criterion name and max points are required');
        }

        $rubricId = $rubricModel->create([
            'assignment_id' => $assignmentId,
            'criterion_name' => sanitize($input['criterion_name']),
            'description' => sanitize($input['description'] ?? ''),
            'max_points' => intval($input['max_points'])
        ]);

        if (!$rubricId) {
            apiError('Failed to create rubric', 500);
        }

        $rubric = $rubricModel->findById($rubricId);
        apiResponse($rubric, 201);
        break;

    case 'PUT':
        apiAuth();
        
        if (!$id) {
            apiError('Rubric ID required');
        }

        if (!isInstructor()) {
            apiError('Only instructors can update rubrics', 403);
        }

        $rubric = $rubricModel->findById($id);
        
        if (!$rubric) {
            apiError('Rubric not found', 404);
        }

        // Verify instructor owns the assignment
        $assignment = $assignmentModel->findById($rubric['assignment_id']);
        
        if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
            apiError('Not authorized', 403);
        }

        $result = $rubricModel->update($id, [
            'criterion_name' => sanitize($input['criterion_name'] ?? $rubric['criterion_name']),
            'description' => sanitize($input['description'] ?? $rubric['description']),
            'max_points' => intval($input['max_points'] ?? $rubric['max_points'])
        ]);

        if (!$result) {
            apiError('Failed to update rubric', 500);
        }

        $updatedRubric = $rubricModel->findById($id);
        apiResponse($updatedRubric);
        break;

    case 'DELETE':
        apiAuth();
        
        if (!$id) {
            apiError('Rubric ID required');
        }

        if (!isInstructor()) {
            apiError('Only instructors can delete rubrics', 403);
        }

        $rubric = $rubricModel->findById($id);
        
        if (!$rubric) {
            apiError('Rubric not found', 404);
        }

        // Verify instructor owns the assignment
        $assignment = $assignmentModel->findById($rubric['assignment_id']);
        
        if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
            apiError('Not authorized', 403);
        }

        $result = $rubricModel->delete($id);

        if (!$result) {
            apiError('Failed to delete rubric', 500);
        }

        apiResponse(['message' => 'Rubric deleted successfully']);
        break;

    default:
        apiError('Method not allowed', 405);
}
