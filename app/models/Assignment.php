<?php
/**
 * Assignment Model
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles all assignment-related database operations.
 * TODO for students: Add pagination, search functionality
 */

require_once dirname(__DIR__) . '/config/config.php';

class Assignment {
    private $db;
    private $table = 'assignments';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Find assignment by ID
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $sql = "SELECT a.*, u.name as instructor_name 
                FROM {$this->table} a 
                LEFT JOIN users u ON a.instructor_id = u.id 
                WHERE a.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get all assignments
     * @return array
     */
    public function getAll() {
        $sql = "SELECT a.*, u.name as instructor_name 
                FROM {$this->table} a 
                LEFT JOIN users u ON a.instructor_id = u.id 
                ORDER BY a.due_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get assignments by instructor
     * @param int $instructorId
     * @return array
     */
    public function getByInstructor($instructorId) {
        $sql = "SELECT * FROM {$this->table} WHERE instructor_id = :instructor_id ORDER BY due_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instructor_id' => $instructorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get active assignments (not past due date)
     * @return array
     */
    public function getActive() {
        $sql = "SELECT a.*, u.name as instructor_name 
                FROM {$this->table} a 
                LEFT JOIN users u ON a.instructor_id = u.id 
                WHERE a.due_date >= CURDATE() 
                ORDER BY a.due_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create new assignment
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (instructor_id, title, description, due_date, max_score, created_at) 
                VALUES (:instructor_id, :title, :description, :due_date, :max_score, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'instructor_id' => $data['instructor_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'due_date' => $data['due_date'],
            'max_score' => $data['max_score'] ?? 100
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Update assignment
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET title = :title, description = :description, due_date = :due_date, 
                    max_score = :max_score, updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'],
            'due_date' => $data['due_date'],
            'max_score' => $data['max_score']
        ]);
    }

    /**
     * Delete assignment
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get submission count for assignment
     * @param int $assignmentId
     * @return int
     */
    public function getSubmissionCount($assignmentId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM submissions WHERE assignment_id = :id");
        $stmt->execute(['id' => $assignmentId]);
        return $stmt->fetchColumn();
    }

    /**
     * Get graded submission count for assignment
     * @param int $assignmentId
     * @return int
     */
    public function getGradedCount($assignmentId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM submissions WHERE assignment_id = :id AND grade IS NOT NULL");
        $stmt->execute(['id' => $assignmentId]);
        return $stmt->fetchColumn();
    }
}
