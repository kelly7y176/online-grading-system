<?php
/**
 * Submissions API Endpoint
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles submission operations via API.
 * TODO for students: Add file upload via API, batch grading
 */

require_once dirname(__DIR__) . '/models/Submission.php';
require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/RubricGrade.php';

$submissionModel = new Submission();
$assignmentModel = new Assignment();
$rubricGradeModel = new RubricGrade();

switch ($method) {
    case 'GET':
        apiAuth();
        
        if ($id) {
            // Get single submission
            $submission = $submissionModel->findById($id);
            
            if (!$submission) {
                apiError('Submission not found', 404);
            }

            // Check authorization
            $assignment = $assignmentModel->findById($submission['assignment_id']);
            
            if (isStudent() && $submission['student_id'] != $_SESSION['user_id']) {
                apiError('Not authorized to view this submission', 403);
            }
            
            if (isInstructor() && $assignment['instructor_id'] != $_SESSION['user_id']) {
                apiError('Not authorized to view this submission', 403);
            }

            // Include rubric grades
            $submission['rubric_grades'] = $rubricGradeModel->getBySubmission($id);
            
            apiResponse($submission);
        } else {
            // List submissions
            $assignmentId = $_GET['assignment_id'] ?? null;
            
            if (isInstructor()) {
                if ($assignmentId) {
                    // Verify instructor owns this assignment
                    $assignment = $assignmentModel->findById($assignmentId);
                    if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
                        apiError('Not authorized', 403);
                    }
                    $submissions = $submissionModel->getByAssignment($assignmentId);
                } else {
                    // Get ungraded submissions for instructor
                    $submissions = $submissionModel->getUngradedByInstructor($_SESSION['user_id']);
                }
            } else {
                // Student: get their own submissions
                $submissions = $submissionModel->getByStudent($_SESSION['user_id']);
            }
            
            apiResponse($submissions);
        }
        break;

    case 'POST':
        // TEMPORARY: Skip authentication for GET and POST requests (for in-class exercise testing)
// TODO: Remove this bypass before production deployment
if ($method !== 'GET' && $method !== 'POST') {
    apiAuth();
}

        
       // Only students can submit
// TEMPORARY: Bypass student check for in-class exercise
// if (!isStudent()) {
//     apiError('Only students can submit assignments', 403);
// }


        $assignmentId = $input['assignment_id'] ?? null;
        
        if (!$assignmentId) {
            apiError('Assignment ID is required');
        }

        $assignment = $assignmentModel->findById($assignmentId);
        
        if (!$assignment) {
            apiError('Assignment not found', 404);
        }

        // Check for existing submission
        $existing = $submissionModel->getStudentSubmission($input['student_id'], $assignmentId);
        
        if ($existing) {
            // Update existing submission
            $result = $submissionModel->update($existing['id'], [
                'text_content' => sanitize($input['text_content'] ?? ''),
                'file_path' => $input['file_path'] ?? $existing['file_path']
            ]);
            
            if (!$result) {
                apiError('Failed to update submission', 500);
            }
            
            $submission = $submissionModel->findById($existing['id']);
            apiResponse($submission);
        } else {
            // Create new submission
            $submissionId = $submissionModel->create([
                'student_id' => $input['student_id'],
                'assignment_id' => $assignmentId,
                'text_content' => sanitize($input['text_content'] ?? ''),
                'file_path' => $input['file_path'] ?? null
            ]);

            if (!$submissionId) {
                apiError('Failed to create submission', 500);
            }

            $submission = $submissionModel->findById($submissionId);
            apiResponse($submission, 201);
        }
        break;

    case 'PUT':
        apiAuth();
        
        if (!$id) {
            apiError('Submission ID required');
        }

        $submission = $submissionModel->findById($id);
        
        if (!$submission) {
            apiError('Submission not found', 404);
        }

        $assignment = $assignmentModel->findById($submission['assignment_id']);

        // Handle grading (instructor)
        if ($action === 'grade' || isset($input['grade'])) {
            if (!isInstructor() || $assignment['instructor_id'] != $_SESSION['user_id']) {
                apiError('Not authorized to grade this submission', 403);
            }

            // Save rubric grades if provided
            if (isset($input['rubric_grades']) && is_array($input['rubric_grades'])) {
                $rubricGradeModel->saveMultiple($id, $input['rubric_grades']);
            }

            $result = $submissionModel->grade($id, [
                'grade' => floatval($input['grade']),
                'feedback' => sanitize($input['feedback'] ?? ''),
                'graded_by' => $_SESSION['user_id']
            ]);

            if (!$result) {
                apiError('Failed to save grade', 500);
            }

            $updatedSubmission = $submissionModel->findById($id);
            $updatedSubmission['rubric_grades'] = $rubricGradeModel->getBySubmission($id);
            
            apiResponse($updatedSubmission);
        }

        // Handle resubmission (student)
        if (isStudent()) {
            if ($submission['student_id'] != $_SESSION['user_id']) {
                apiError('Not authorized to update this submission', 403);
            }

            $result = $submissionModel->update($id, [
                'text_content' => sanitize($input['text_content'] ?? $submission['text_content']),
                'file_path' => $input['file_path'] ?? $submission['file_path']
            ]);

            if (!$result) {
                apiError('Failed to update submission', 500);
            }

            $updatedSubmission = $submissionModel->findById($id);
            apiResponse($updatedSubmission);
        }

        apiError('Invalid operation', 400);
        break;

    case 'DELETE':
        apiAuth();
        
        if (!$id) {
            apiError('Submission ID required');
        }

        $submission = $submissionModel->findById($id);
        
        if (!$submission) {
            apiError('Submission not found', 404);
        }

        // Only student who submitted or instructor can delete
        $assignment = $assignmentModel->findById($submission['assignment_id']);
        
        if (isStudent() && $submission['student_id'] != $_SESSION['user_id']) {
            apiError('Not authorized to delete this submission', 403);
        }
        
        if (isInstructor() && $assignment['instructor_id'] != $_SESSION['user_id']) {
            apiError('Not authorized to delete this submission', 403);
        }

        // Delete rubric grades first
        $rubricGradeModel->deleteBySubmission($id);
        
        $result = $submissionModel->delete($id);

        if (!$result) {
            apiError('Failed to delete submission', 500);
        }

        apiResponse(['message' => 'Submission deleted successfully']);
        break;

    default:
        apiError('Method not allowed', 405);
}
