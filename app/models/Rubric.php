<?php
/**
 * Rubric Model
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles grading rubric operations.
 * TODO for students: Add rubric templates, bulk operations
 */

require_once dirname(__DIR__) . '/config/config.php';

class Rubric {
    private $db;
    private $table = 'rubrics';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Find rubric by ID
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get rubrics for an assignment
     * @param int $assignmentId
     * @return array
     */
    public function getByAssignment($assignmentId) {
        $sql = "SELECT * FROM {$this->table} WHERE assignment_id = :assignment_id ORDER BY sort_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['assignment_id' => $assignmentId]);
        return $stmt->fetchAll();
    }

    /**
     * Create new rubric criterion
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        // Get next sort order
        $stmt = $this->db->prepare("SELECT MAX(sort_order) FROM {$this->table} WHERE assignment_id = :assignment_id");
        $stmt->execute(['assignment_id' => $data['assignment_id']]);
        $maxOrder = $stmt->fetchColumn() ?? 0;

        $sql = "INSERT INTO {$this->table} (assignment_id, criterion_name, description, max_points, sort_order, created_at) 
                VALUES (:assignment_id, :criterion_name, :description, :max_points, :sort_order, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'assignment_id' => $data['assignment_id'],
            'criterion_name' => $data['criterion_name'],
            'description' => $data['description'] ?? '',
            'max_points' => $data['max_points'],
            'sort_order' => $maxOrder + 1
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Update rubric criterion
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET criterion_name = :criterion_name, description = :description, 
                    max_points = :max_points, updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'criterion_name' => $data['criterion_name'],
            'description' => $data['description'],
            'max_points' => $data['max_points']
        ]);
    }

    /**
     * Delete rubric criterion
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Delete all rubrics for an assignment
     * @param int $assignmentId
     * @return bool
     */
    public function deleteByAssignment($assignmentId) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE assignment_id = :assignment_id");
        return $stmt->execute(['assignment_id' => $assignmentId]);
    }

    /**
     * Get total max points for an assignment's rubric
     * @param int $assignmentId
     * @return int
     */
    public function getTotalMaxPoints($assignmentId) {
        $stmt = $this->db->prepare("SELECT SUM(max_points) FROM {$this->table} WHERE assignment_id = :assignment_id");
        $stmt->execute(['assignment_id' => $assignmentId]);
        return $stmt->fetchColumn() ?? 0;
    }

    /**
     * Reorder rubric criteria
     * @param int $assignmentId
     * @param array $order - Array of criterion IDs in desired order
     * @return bool
     */
    public function reorder($assignmentId, $order) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET sort_order = :sort_order WHERE id = :id AND assignment_id = :assignment_id");
            
            foreach ($order as $index => $id) {
                $stmt->execute([
                    'sort_order' => $index + 1,
                    'id' => $id,
                    'assignment_id' => $assignmentId
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
