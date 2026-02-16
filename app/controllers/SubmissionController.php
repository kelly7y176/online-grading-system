<?php
/**
 * Submission Controller
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles student submissions and instructor grading.
 * TODO for students: Add file type validation, plagiarism check integration
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/Submission.php';
require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/Rubric.php';
require_once dirname(__DIR__) . '/models/RubricGrade.php';

class SubmissionController {
    private $submissionModel;
    private $assignmentModel;
    private $rubricModel;
    private $rubricGradeModel;

    public function __construct() {
        $this->submissionModel = new Submission();
        $this->assignmentModel = new Assignment();
        $this->rubricModel = new Rubric();
        $this->rubricGradeModel = new RubricGrade();
    }

    /**
     * List submissions for an assignment (instructor view)
     */
    public function listByAssignment($assignmentId) {
        if (!isInstructor()) {
            redirect(BASE_URL . '/login.php');
            return;
        }

        $assignment = $this->assignmentModel->findById($assignmentId);

        if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Assignment not found.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        $submissions = $this->submissionModel->getByAssignment($assignmentId);
        $stats = $this->submissionModel->getGradeStats($assignmentId);

        require_once dirname(__DIR__) . '/views/instructor/submissions.php';
    }

    /**
     * Show submission form for students
     */
    public function showSubmitForm($assignmentId) {
        if (!isStudent()) {
            redirect(BASE_URL . '/login.php');
            return;
        }

        $assignment = $this->assignmentModel->findById($assignmentId);

        if (!$assignment) {
            setFlashMessage('error', 'Assignment not found.');
            redirect(BASE_URL . '/student/dashboard.php');
            return;
        }

        $existingSubmission = $this->submissionModel->getStudentSubmission($_SESSION['user_id'], $assignmentId);

        require_once dirname(__DIR__) . '/views/student/submit.php';
    }

    /**
     * Process student submission
     */
    public function submit($assignmentId) {
        if (!isStudent() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/login.php');
            return;
        }

        // Validate CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/student/submit.php?id=' . $assignmentId);
            return;
        }

        $assignment = $this->assignmentModel->findById($assignmentId);

        if (!$assignment) {
            setFlashMessage('error', 'Assignment not found.');
            redirect(BASE_URL . '/student/dashboard.php');
            return;
        }

        $textContent = sanitize($_POST['text_content'] ?? '');
        $filePath = null;

        // Handle file upload
        if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['submission_file'];
            
            // Validate file size
            if ($file['size'] > MAX_FILE_SIZE) {
                setFlashMessage('error', 'File size exceeds maximum allowed (' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB).');
                redirect(BASE_URL . '/student/submit.php?id=' . $assignmentId);
                return;
            }

            // Validate file type
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ALLOWED_FILE_TYPES)) {
                setFlashMessage('error', 'File type not allowed. Allowed types: ' . implode(', ', ALLOWED_FILE_TYPES));
                redirect(BASE_URL . '/student/submit.php?id=' . $assignmentId);
                return;
            }

            // Create upload directory if not exists
            $uploadDir = UPLOAD_PATH . $assignmentId . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $newFilename = $_SESSION['user_id'] . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
            $filePath = $uploadDir . $newFilename;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                setFlashMessage('error', 'Failed to upload file.');
                redirect(BASE_URL . '/student/submit.php?id=' . $assignmentId);
                return;
            }

            // Store relative path
            $filePath = 'uploads/' . $assignmentId . '/' . $newFilename;
        }

        // Check for existing submission
        $existingSubmission = $this->submissionModel->getStudentSubmission($_SESSION['user_id'], $assignmentId);

        if ($existingSubmission) {
            // Update existing submission
            $result = $this->submissionModel->update($existingSubmission['id'], [
                'file_path' => $filePath ?: $existingSubmission['file_path'],
                'text_content' => $textContent
            ]);
            $message = 'Submission updated successfully.';
        } else {
            // Create new submission
            $result = $this->submissionModel->create([
                'student_id' => $_SESSION['user_id'],
                'assignment_id' => $assignmentId,
                'file_path' => $filePath,
                'text_content' => $textContent
            ]);
            $message = 'Submission successful.';
        }

        if ($result) {
            setFlashMessage('success', $message);
            redirect(BASE_URL . '/student/submissions.php');
        } else {
            setFlashMessage('error', 'Submission failed. Please try again.');
            redirect(BASE_URL . '/student/submit.php?id=' . $assignmentId);
        }
    }

    /**
     * Show grading form for instructor
     */
    public function showGradeForm($submissionId) {
        if (!isInstructor()) {
            redirect(BASE_URL . '/login.php');
            return;
        }

        $submission = $this->submissionModel->findById($submissionId);

        if (!$submission) {
            setFlashMessage('error', 'Submission not found.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        $assignment = $this->assignmentModel->findById($submission['assignment_id']);

        if ($assignment['instructor_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Access denied.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        $rubrics = $this->rubricModel->getByAssignment($submission['assignment_id']);
        $rubricGrades = $this->rubricGradeModel->getBySubmission($submissionId);

        // Index rubric grades by rubric_id for easy lookup
        $gradesMap = [];
        foreach ($rubricGrades as $grade) {
            $gradesMap[$grade['rubric_id']] = $grade;
        }

        require_once dirname(__DIR__) . '/views/instructor/grade.php';
    }

    /**
     * Process grading
     */
    public function grade($submissionId) {
        if (!isInstructor() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/login.php');
            return;
        }

        // Validate CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/instructor/grade.php?id=' . $submissionId);
            return;
        }

        $submission = $this->submissionModel->findById($submissionId);

        if (!$submission) {
            setFlashMessage('error', 'Submission not found.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        $assignment = $this->assignmentModel->findById($submission['assignment_id']);

        if ($assignment['instructor_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Access denied.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        // Process rubric grades
        $totalPoints = 0;
        if (isset($_POST['rubric_grades']) && is_array($_POST['rubric_grades'])) {
            foreach ($_POST['rubric_grades'] as $rubricId => $gradeData) {
                $points = floatval($gradeData['points'] ?? 0);
                $comment = sanitize($gradeData['comment'] ?? '');

                $this->rubricGradeModel->saveGrade([
                    'submission_id' => $submissionId,
                    'rubric_id' => $rubricId,
                    'points' => $points,
                    'comment' => $comment
                ]);

                $totalPoints += $points;
            }
        }

        // Calculate final grade (percentage or raw score)
        $grade = floatval($_POST['final_grade'] ?? $totalPoints);
        $feedback = sanitize($_POST['feedback'] ?? '');

        $result = $this->submissionModel->grade($submissionId, [
            'grade' => $grade,
            'feedback' => $feedback,
            'graded_by' => $_SESSION['user_id']
        ]);

        if ($result) {
            setFlashMessage('success', 'Grade saved successfully.');
            redirect(BASE_URL . '/instructor/submissions.php?assignment_id=' . $submission['assignment_id']);
        } else {
            setFlashMessage('error', 'Failed to save grade.');
            redirect(BASE_URL . '/instructor/grade.php?id=' . $submissionId);
        }
    }

    /**
     * View submission details (student)
     */
    public function viewSubmission($submissionId) {
        if (!isStudent()) {
            redirect(BASE_URL . '/login.php');
            return;
        }

        $submission = $this->submissionModel->findById($submissionId);

        if (!$submission || $submission['student_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Submission not found.');
            redirect(BASE_URL . '/student/submissions.php');
            return;
        }

        $rubricGrades = $this->rubricGradeModel->getBySubmission($submissionId);

        require_once dirname(__DIR__) . '/views/student/submission_view.php';
    }

    /**
     * List all submissions for current student
     */
    public function mySubmissions() {
        if (!isStudent()) {
            redirect(BASE_URL . '/login.php');
            return;
        }

        $submissions = $this->submissionModel->getByStudent($_SESSION['user_id']);

        require_once dirname(__DIR__) . '/views/student/submissions.php';
    }
}
