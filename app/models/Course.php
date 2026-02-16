<?php
/**
 * Course Model
 * CS425 Assignment Grading System - NEW MODEL (Team A Easy Level)
 * 
 * This is a demonstration of adding a new model for the courses API.
 * Handles all course-related database operations.
 */

require_once dirname(__DIR__) . '/config/config.php';

class Course {
    private $db;
    private $table = 'courses';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Find course by ID
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $sql = "SELECT c.*, u.name as instructor_name 
                FROM {$this->table} c 
                LEFT JOIN users u ON c.instructor_id = u.id 
                WHERE c.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get all courses
     * @return array
     */
    public function getAll() {
        $sql = "SELECT c.*, u.name as instructor_name 
                FROM {$this->table} c 
                LEFT JOIN users u ON c.instructor_id = u.id 
                ORDER BY c.code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get courses by instructor
     * @param int $instructorId
     * @return array
     */
    public function getByInstructor($instructorId) {
        $sql = "SELECT c.*, u.name as instructor_name 
                FROM {$this->table} c 
                LEFT JOIN users u ON c.instructor_id = u.id 
                WHERE c.instructor_id = :instructor_id 
                ORDER BY c.code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instructor_id' => $instructorId]);
        return $stmt->fetchAll();
    }

    /**
     * Search courses by name or code
     * @param string $query
     * @return array
     */
    public function search($query) {
        $sql = "SELECT c.*, u.name as instructor_name 
                FROM {$this->table} c 
                LEFT JOIN users u ON c.instructor_id = u.id 
                WHERE c.code LIKE :query OR c.name LIKE :query 
                ORDER BY c.code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['query' => '%' . $query . '%']);
        return $stmt->fetchAll();
    }

    /**
     * Create new course
     * @param array $data
     * @return int|false - Returns course ID on success
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (code, name, description, instructor_id, created_at) 
                VALUES (:code, :name, :description, :instructor_id, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'instructor_id' => $data['instructor_id']
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Update course
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        $fields[] = "updated_at = NOW()";
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete course
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Check if course code exists
     * @param string $code
     * @param int|null $excludeId - Exclude this course ID from check
     * @return bool
     */
    public function codeExists($code, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE code = :code";
        $params = ['code' => $code];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
