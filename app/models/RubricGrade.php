<?php
/**
 * RubricGrade Model
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles individual rubric criterion grades for submissions.
 * TODO for students: Add grade history, bulk grading
 */

require_once dirname(__DIR__) . '/config/config.php';

class RubricGrade {
    private $db;
    private $table = 'rubric_grades';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Get rubric grades for a submission
     * @param int $submissionId
     * @return array
     */
    public function getBySubmission($submissionId) {
        $sql = "SELECT rg.*, r.criterion_name, r.description, r.max_points
                FROM {$this->table} rg
                LEFT JOIN rubrics r ON rg.rubric_id = r.id
                WHERE rg.submission_id = :submission_id
                ORDER BY r.sort_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['submission_id' => $submissionId]);
        return $stmt->fetchAll();
    }

    /**
     * Save or update rubric grade
     * @param array $data
     * @return bool
     */
    public function saveGrade($data) {
        // Check if grade exists
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE submission_id = :submission_id AND rubric_id = :rubric_id");
        $stmt->execute([
            'submission_id' => $data['submission_id'],
            'rubric_id' => $data['rubric_id']
        ]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Update existing grade
            $sql = "UPDATE {$this->table} 
                    SET points = :points, comment = :comment, updated_at = NOW() 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $existing['id'],
                'points' => $data['points'],
                'comment' => $data['comment'] ?? null
            ]);
        } else {
            // Create new grade
            $sql = "INSERT INTO {$this->table} (submission_id, rubric_id, points, comment, created_at) 
                    VALUES (:submission_id, :rubric_id, :points, :comment, NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'submission_id' => $data['submission_id'],
                'rubric_id' => $data['rubric_id'],
                'points' => $data['points'],
                'comment' => $data['comment'] ?? null
            ]);
        }
    }

    /**
     * Save multiple rubric grades at once
     * @param int $submissionId
     * @param array $grades - Array of [rubric_id => ['points' => x, 'comment' => y]]
     * @return bool
     */
    public function saveMultiple($submissionId, $grades) {
        $this->db->beginTransaction();
        try {
            foreach ($grades as $rubricId => $gradeData) {
                $this->saveGrade([
                    'submission_id' => $submissionId,
                    'rubric_id' => $rubricId,
                    'points' => $gradeData['points'],
                    'comment' => $gradeData['comment'] ?? null
                ]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Calculate total points for a submission
     * @param int $submissionId
     * @return float
     */
    public function getTotalPoints($submissionId) {
        $stmt = $this->db->prepare("SELECT SUM(points) FROM {$this->table} WHERE submission_id = :submission_id");
        $stmt->execute(['submission_id' => $submissionId]);
        return $stmt->fetchColumn() ?? 0;
    }

    /**
     * Delete all grades for a submission
     * @param int $submissionId
     * @return bool
     */
    public function deleteBySubmission($submissionId) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE submission_id = :submission_id");
        return $stmt->execute(['submission_id' => $submissionId]);
    }
}
