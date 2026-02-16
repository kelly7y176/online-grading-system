<?php
/**
 * Database Configuration
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * This file contains database connection settings.
 * Students can modify these settings for their environment.
 */

// Environment detection - Docker or XAMPP
$isDocker = getenv('DOCKER_ENV') === 'true';

if ($isDocker) {
    // Docker environment settings
    define('DB_HOST', getenv('DB_HOST') ?: 'mysql');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME') ?: 'grading_system');
    define('DB_USER', getenv('DB_USER') ?: 'grading_user');
    define('DB_PASS', getenv('DB_PASS') ?: 'grading_password');
} else {
    // XAMPP/Local environment settings
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'grading_system');
    define('DB_USER', 'root');
    define('DB_PASS', ''); // Default XAMPP has no password
}

// Database connection class
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error and show user-friendly message
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Helper function to get database connection
 * @return PDO
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
