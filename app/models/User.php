<?php
/**
 * User Model
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Handles all user-related database operations.
 * TODO for students: Add input validation, improve security
 */

require_once dirname(__DIR__) . '/config/config.php';

class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Find user by ID
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Find user by email
     * @param string $email
     * @return array|false
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Create new user
     * @param array $data
     * @return int|false - Returns user ID on success
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, email, password, role, created_at) 
                VALUES (:name, :email, :password, :role, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'student'
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Update user
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'password') {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = "password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $fields[] = "updated_at = NOW()";
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Authenticate user
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            // Remove password from returned data
            unset($user['password']);
            return $user;
        }
        
        return false;
    }

    /**
     * Get all students
     * @return array
     */
    public function getAllStudents() {
        $stmt = $this->db->prepare("SELECT id, name, email, created_at FROM {$this->table} WHERE role = 'student' ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all instructors
     * @return array
     */
    public function getAllInstructors() {
        $stmt = $this->db->prepare("SELECT id, name, email, created_at FROM {$this->table} WHERE role = 'instructor' ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete user
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Check if email exists
     * @param string $email
     * @param int|null $excludeId - Exclude this user ID from check
     * @return bool
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
