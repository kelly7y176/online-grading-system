<?php
/**
 * Submission Model
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles student submission operations.
 * TODO for students: Add file validation, late submission handling
 */

require_once dirname(__DIR__) . '/config/config.php';

class Submission {
    private $db;
    private $table = 'submissions';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Find submission by ID
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $sql = "SELECT s.*, a.title as assignment_title, u.name as student_name, u.email as student_email
                FROM {$this->table} s
                LEFT JOIN assignments a ON s.assignment_id = a.id
                LEFT JOIN users u ON s.student_id = u.id
                WHERE s.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get submissions for an assignment
     * @param int $assignmentId
     * @return array
     */
    public function getByAssignment($assignmentId) {
        $sql = "SELECT s.*, u.name as student_name, u.email as student_email
                FROM {$this->table} s
                LEFT JOIN users u ON s.student_id = u.id
                WHERE s.assignment_id = :assignment_id
                ORDER BY s.submitted_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['assignment_id' => $assignmentId]);
        return $stmt->fetchAll();
    }

    /**
     * Get submissions by student
     * @param int $studentId
     * @return array
     */
    public function getByStudent($studentId) {
        $sql = "SELECT s.*, a.title as assignment_title, a.due_date, a.max_score
                FROM {$this->table} s
                LEFT JOIN assignments a ON s.assignment_id = a.id
                WHERE s.student_id = :student_id
                ORDER BY s.submitted_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    /**
     * Check if student has submitted for an assignment
     * @param int $studentId
     * @param int $assignmentId
     * @return array|false
     */
    public function getStudentSubmission($studentId, $assignmentId) {
        $sql = "SELECT * FROM {$this->table} WHERE student_id = :student_id AND assignment_id = :assignment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'student_id' => $studentId,
            'assignment_id' => $assignmentId
        ]);
        return $stmt->fetch();
    }

    /**
     * Create new submission
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (student_id, assignment_id, file_path, text_content, submitted_at) 
                VALUES (:student_id, :assignment_id, :file_path, :text_content, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'student_id' => $data['student_id'],
            'assignment_id' => $data['assignment_id'],
            'file_path' => $data['file_path'] ?? null,
            'text_content' => $data['text_content'] ?? null
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Update submission (resubmit)
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET file_path = :file_path, text_content = :text_content, 
                    submitted_at = NOW(), grade = NULL, feedback = NULL 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'file_path' => $data['file_path'] ?? null,
            'text_content' => $data['text_content'] ?? null
        ]);
    }

    /**
     * Grade a submission
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function grade($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET grade = :grade, feedback = :feedback, graded_at = NOW(), graded_by = :graded_by 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'grade' => $data['grade'],
            'feedback' => $data['feedback'] ?? null,
            'graded_by' => $data['graded_by']
        ]);
    }

    /**
     * Delete submission
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get ungraded submissions for an instructor
     * @param int $instructorId
     * @return array
     */
    public function getUngradedByInstructor($instructorId) {
        $sql = "SELECT s.*, a.title as assignment_title, u.name as student_name
                FROM {$this->table} s
                LEFT JOIN assignments a ON s.assignment_id = a.id
                LEFT JOIN users u ON s.student_id = u.id
                WHERE a.instructor_id = :instructor_id AND s.grade IS NULL
                ORDER BY s.submitted_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instructor_id' => $instructorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get grade statistics for an assignment
     * @param int $assignmentId
     * @return array
     */
    public function getGradeStats($assignmentId) {
        $sql = "SELECT 
                    COUNT(*) as total_submissions,
                    COUNT(grade) as graded_count,
                    AVG(grade) as average_grade,
                    MIN(grade) as min_grade,
                    MAX(grade) as max_grade
                FROM {$this->table} 
                WHERE assignment_id = :assignment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['assignment_id' => $assignmentId]);
        return $stmt->fetch();
    }
}
