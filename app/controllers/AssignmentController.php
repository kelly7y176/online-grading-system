<?php
/**
 * Assignment Controller
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles assignment CRUD operations for instructors.
 * TODO for students: Add file attachments, assignment cloning
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/Rubric.php';

class AssignmentController {
    private $assignmentModel;
    private $rubricModel;

    public function __construct() {
        $this->assignmentModel = new Assignment();
        $this->rubricModel = new Rubric();
    }

    /**
     * List all assignments for instructor
     */
    public function index() {
        if (!isInstructor()) {
            setFlashMessage('error', 'Access denied.');
            redirect(BASE_URL . '/login.php');
            return;
        }

        $assignments = $this->assignmentModel->getByInstructor($_SESSION['user_id']);
        
        // Add submission counts
        foreach ($assignments as &$assignment) {
            $assignment['submission_count'] = $this->assignmentModel->getSubmissionCount($assignment['id']);
            $assignment['graded_count'] = $this->assignmentModel->getGradedCount($assignment['id']);
        }

        require_once dirname(__DIR__) . '/views/instructor/assignments.php';
    }

    /**
     * Show create assignment form
     */
    public function create() {
        if (!isInstructor()) {
            setFlashMessage('error', 'Access denied.');
            redirect(BASE_URL . '/login.php');
            return;
        }

        require_once dirname(__DIR__) . '/views/instructor/assignment_form.php';
    }

    /**
     * Store new assignment
     */
    public function store() {
        if (!isInstructor() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/login.php');
            return;
        }

        // Validate CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $dueDate = sanitize($_POST['due_date'] ?? '');
        $maxScore = intval($_POST['max_score'] ?? 100);

        // Validation
        if (empty($title) || empty($dueDate)) {
            setFlashMessage('error', 'Title and due date are required.');
            redirect(BASE_URL . '/instructor/assignment_create.php');
            return;
        }

        $assignmentId = $this->assignmentModel->create([
            'instructor_id' => $_SESSION['user_id'],
            'title' => $title,
            'description' => $description,
            'due_date' => $dueDate,
            'max_score' => $maxScore
        ]);

        if ($assignmentId) {
            // Create default rubric criteria if provided
            if (isset($_POST['rubric']) && is_array($_POST['rubric'])) {
                foreach ($_POST['rubric'] as $criterion) {
                    if (!empty($criterion['name']) && !empty($criterion['points'])) {
                        $this->rubricModel->create([
                            'assignment_id' => $assignmentId,
                            'criterion_name' => sanitize($criterion['name']),
                            'description' => sanitize($criterion['description'] ?? ''),
                            'max_points' => intval($criterion['points'])
                        ]);
                    }
                }
            }

            setFlashMessage('success', 'Assignment created successfully.');
            redirect(BASE_URL . '/instructor/assignment_view.php?id=' . $assignmentId);
        } else {
            setFlashMessage('error', 'Failed to create assignment.');
            redirect(BASE_URL . '/instructor/assignment_create.php');
        }
    }

    /**
     * Show single assignment
     */
    public function show($id) {
        if (!isInstructor()) {
            redirect(BASE_URL . '/login.php');
            return;
        }

        $assignment = $this->assignmentModel->findById($id);

        if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Assignment not found.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        $rubrics = $this->rubricModel->getByAssignment($id);
        $assignment['submission_count'] = $this->assignmentModel->getSubmissionCount($id);
        $assignment['graded_count'] = $this->assignmentModel->getGradedCount($id);

        require_once dirname(__DIR__) . '/views/instructor/assignment_view.php';
    }

    /**
     * Show edit form
     */
    public function edit($id) {
        if (!isInstructor()) {
            redirect(BASE_URL . '/login.php');
            return;
        }

        $assignment = $this->assignmentModel->findById($id);

        if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Assignment not found.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        $rubrics = $this->rubricModel->getByAssignment($id);

        require_once dirname(__DIR__) . '/views/instructor/assignment_form.php';
    }

    /**
     * Update assignment
     */
    public function update($id) {
        if (!isInstructor() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/login.php');
            return;
        }

        // Validate CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        $assignment = $this->assignmentModel->findById($id);

        if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Assignment not found.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $dueDate = sanitize($_POST['due_date'] ?? '');
        $maxScore = intval($_POST['max_score'] ?? 100);

        if (empty($title) || empty($dueDate)) {
            setFlashMessage('error', 'Title and due date are required.');
            redirect(BASE_URL . '/instructor/assignment_edit.php?id=' . $id);
            return;
        }

        $result = $this->assignmentModel->update($id, [
            'title' => $title,
            'description' => $description,
            'due_date' => $dueDate,
            'max_score' => $maxScore
        ]);

        if ($result) {
            setFlashMessage('success', 'Assignment updated successfully.');
        } else {
            setFlashMessage('error', 'Failed to update assignment.');
        }

        redirect(BASE_URL . '/instructor/assignment_view.php?id=' . $id);
    }

    /**
     * Delete assignment
     */
    public function delete($id) {
        if (!isInstructor() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/login.php');
            return;
        }

        // Validate CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        $assignment = $this->assignmentModel->findById($id);

        if (!$assignment || $assignment['instructor_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Assignment not found.');
            redirect(BASE_URL . '/instructor/assignments.php');
            return;
        }

        // Delete rubrics first
        $this->rubricModel->deleteByAssignment($id);

        if ($this->assignmentModel->delete($id)) {
            setFlashMessage('success', 'Assignment deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete assignment.');
        }

        redirect(BASE_URL . '/instructor/assignments.php');
    }
}
