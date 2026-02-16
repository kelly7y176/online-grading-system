<?php
/**
 * Grades API Endpoint
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles grade export and statistics via API.
 * TODO for students: Add grade analytics, export formats
 */

require_once dirname(__DIR__) . '/models/Submission.php';
require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/RubricGrade.php';

$submissionModel = new Submission();
$assignmentModel = new Assignment();
$rubricGradeModel = new RubricGrade();

apiAuth();

switch ($method) {
    case 'GET':
        if ($action === 'export' || (isset($_GET['export']) && $_GET['export'] === 'csv')) {
            // Export grades as CSV
            if (!isInstructor()) {
                apiError('Only instructors can export grades', 403);
            }

            $assignmentId = $id ?? $_GET['assignment_id'] ?? null;
            
            if (!$assignmentId) {
                apiError('Assignment ID is required');
            }

            $assignment = $assignmentModel->findById($assignmentId);
            
            if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
                apiError('Not authorized', 403);
            }

            $submissions = $submissionModel->getByAssignment($assignmentId);

            // Generate CSV
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="grades_' . $assignmentId . '_' . date('Y-m-d') . '.csv"');

            $output = fopen('php://output', 'w');
            
            // Header row
            fputcsv($output, ['Student Name', 'Student Email', 'Submitted At', 'Grade', 'Feedback', 'Graded At']);
            
            foreach ($submissions as $submission) {
                fputcsv($output, [
                    $submission['student_name'],
                    $submission['student_email'],
                    $submission['submitted_at'],
                    $submission['grade'] ?? 'Not graded',
                    $submission['feedback'] ?? '',
                    $submission['graded_at'] ?? ''
                ]);
            }
            
            fclose($output);
            exit();
        } elseif ($action === 'stats' || $id === 'stats') {
            // Get grade statistics
            if (!isInstructor()) {
                apiError('Only instructors can view statistics', 403);
            }

            $assignmentId = $_GET['assignment_id'] ?? null;
            
            if (!$assignmentId) {
                apiError('Assignment ID is required');
            }

            $assignment = $assignmentModel->findById($assignmentId);
            
            if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
                apiError('Not authorized', 403);
            }

            $stats = $submissionModel->getGradeStats($assignmentId);
            
            apiResponse([
                'assignment_id' => $assignmentId,
                'assignment_title' => $assignment['title'],
                'max_score' => $assignment['max_score'],
                'statistics' => $stats
            ]);
        } elseif ($id) {
            // Get grades for a specific submission
            $submission = $submissionModel->findById($id);
            
            if (!$submission) {
                apiError('Submission not found', 404);
            }

            // Check authorization
            if (isStudent() && $submission['student_id'] != $_SESSION['user_id']) {
                apiError('Not authorized', 403);
            }

            $assignment = $assignmentModel->findById($submission['assignment_id']);
            
            if (isInstructor() && $assignment['instructor_id'] != $_SESSION['user_id']) {
                apiError('Not authorized', 403);
            }

            $rubricGrades = $rubricGradeModel->getBySubmission($id);
            $totalPoints = $rubricGradeModel->getTotalPoints($id);

            apiResponse([
                'submission_id' => $id,
                'final_grade' => $submission['grade'],
                'feedback' => $submission['feedback'],
                'graded_at' => $submission['graded_at'],
                'rubric_grades' => $rubricGrades,
                'total_rubric_points' => $totalPoints
            ]);
        } else {
            // Get all grades for student
            if (isStudent()) {
                $submissions = $submissionModel->getByStudent($_SESSION['user_id']);
                
                $grades = [];
                foreach ($submissions as $submission) {
                    if ($submission['grade'] !== null) {
                        $grades[] = [
                            'assignment_title' => $submission['assignment_title'],
                            'grade' => $submission['grade'],
                            'max_score' => $submission['max_score'],
                            'feedback' => $submission['feedback'],
                            'graded_at' => $submission['graded_at']
                        ];
                    }
                }
                
                apiResponse($grades);
            } else {
                apiError('Please specify an assignment ID', 400);
            }
        }
        break;

    default:
        apiError('Method not allowed', 405);
}
