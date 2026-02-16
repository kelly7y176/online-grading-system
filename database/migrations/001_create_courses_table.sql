-- =====================================================
-- Migration: Create Courses Table
-- CS425 Assignment Grading System - Team A Easy Level
-- =====================================================
-- This migration adds a new 'courses' table to support
-- the new /api/courses endpoint.
-- =====================================================

-- Create the courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    instructor_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_code (code),
    INDEX idx_instructor (instructor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO courses (code, name, description, instructor_id) VALUES
('CS425', 'Systems Engineering and Project Management', 'Learn software engineering principles and project management techniques.', 1),
('CS301', 'Database Systems', 'Introduction to relational databases and SQL.', 1),
('CS201', 'Web Development', 'Full-stack web development with modern technologies.', 2);
