<?php
/**
 * Assignments API Endpoint
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles CRUD operations for assignments via API.
 * TODO for students: Add filtering, sorting, pagination
 */

require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/Rubric.php';

$assignmentModel = new Assignment();
$rubricModel = new Rubric();

switch ($method) {
    case 'GET':
        if ($id) {
            // Get single assignment
            $assignment = $assignmentModel->findById($id);
            if (!$assignment) {
                apiError('Assignment not found', 404);
            }
            
            // Include rubrics
            $assignment['rubrics'] = $rubricModel->getByAssignment($id);
            $assignment['submission_count'] = $assignmentModel->getSubmissionCount($id);
            $assignment['graded_count'] = $assignmentModel->getGradedCount($id);
            
            apiResponse($assignment);
        } else {
            // List all assignments
            $assignments = $assignmentModel->getAll();
            
            // Add counts for each assignment
            foreach ($assignments as &$assignment) {
                $assignment['submission_count'] = $assignmentModel->getSubmissionCount($assignment['id']);
                $assignment['graded_count'] = $assignmentModel->getGradedCount($assignment['id']);
            }
            
            apiResponse($assignments);
        }
        break;

    case 'POST':
        apiAuth();
        
        if (!isInstructor()) {
            apiError('Only instructors can create assignments', 403);
        }

        // Validate required fields
        if (empty($input['title']) || empty($input['due_date'])) {
            apiError('Title and due date are required');
        }

        $assignmentId = $assignmentModel->create([
            'instructor_id' => $_SESSION['user_id'],
            'title' => sanitize($input['title']),
            'description' => sanitize($input['description'] ?? ''),
            'due_date' => sanitize($input['due_date']),
            'max_score' => intval($input['max_score'] ?? 100)
        ]);

        if (!$assignmentId) {
            apiError('Failed to create assignment', 500);
        }

        // Create rubrics if provided
        if (isset($input['rubrics']) && is_array($input['rubrics'])) {
            foreach ($input['rubrics'] as $rubric) {
                if (!empty($rubric['criterion_name']) && isset($rubric['max_points'])) {
                    $rubricModel->create([
                        'assignment_id' => $assignmentId,
                        'criterion_name' => sanitize($rubric['criterion_name']),
                        'description' => sanitize($rubric['description'] ?? ''),
                        'max_points' => intval($rubric['max_points'])
                    ]);
                }
            }
        }

        $assignment = $assignmentModel->findById($assignmentId);
        $assignment['rubrics'] = $rubricModel->getByAssignment($assignmentId);

        apiResponse($assignment, 201);
        break;

    case 'PUT':
        apiAuth();
        
        if (!$id) {
            apiError('Assignment ID required');
        }

        $assignment = $assignmentModel->findById($id);
        
        if (!$assignment) {
            apiError('Assignment not found', 404);
        }

        if (!isInstructor() || $assignment['instructor_id'] != $_SESSION['user_id']) {
            apiError('Not authorized to update this assignment', 403);
        }

        $result = $assignmentModel->update($id, [
            'title' => sanitize($input['title'] ?? $assignment['title']),
            'description' => sanitize($input['description'] ?? $assignment['description']),
            'due_date' => sanitize($input['due_date'] ?? $assignment['due_date']),
            'max_score' => intval($input['max_score'] ?? $assignment['max_score'])
        ]);

        if (!$result) {
            apiError('Failed to update assignment', 500);
        }

        $updatedAssignment = $assignmentModel->findById($id);
        $updatedAssignment['rubrics'] = $rubricModel->getByAssignment($id);

        apiResponse($updatedAssignment);
        break;

    case 'DELETE':
        apiAuth();
        
        if (!$id) {
            apiError('Assignment ID required');
        }

        $assignment = $assignmentModel->findById($id);
        
        if (!$assignment) {
            apiError('Assignment not found', 404);
        }

        if (!isInstructor() || $assignment['instructor_id'] != $_SESSION['user_id']) {
            apiError('Not authorized to delete this assignment', 403);
        }

        // Delete rubrics first
        $rubricModel->deleteByAssignment($id);
        
        $result = $assignmentModel->delete($id);

        if (!$result) {
            apiError('Failed to delete assignment', 500);
        }

        apiResponse(['message' => 'Assignment deleted successfully']);
        break;

    default:
        apiError('Method not allowed', 405);
}
