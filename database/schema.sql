-- =====================================================
-- CS425 Assignment Grading System - Database Schema
-- =====================================================
-- This file creates all necessary tables for the system.
-- Run this script to initialize your database.
-- 
-- TODO for students:
-- - Add indexes for frequently queried columns
-- - Add foreign key constraints if not present
-- - Consider adding audit/history tables
-- =====================================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS grading_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE grading_system;

-- =====================================================
-- Users Table
-- Stores both instructors and students
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'instructor') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Assignments Table
-- Stores assignment details created by instructors
-- =====================================================
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    due_date DATE NOT NULL,
    max_score INT NOT NULL DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_instructor (instructor_id),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Rubrics Table
-- Stores grading criteria for assignments
-- =====================================================
CREATE TABLE IF NOT EXISTS rubrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    criterion_name VARCHAR(255) NOT NULL,
    description TEXT,
    max_points INT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    INDEX idx_assignment (assignment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Submissions Table
-- Stores student submissions for assignments
-- =====================================================
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    assignment_id INT NOT NULL,
    file_path VARCHAR(500),
    text_content TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    grade DECIMAL(5,2),
    feedback TEXT,
    graded_at TIMESTAMP NULL,
    graded_by INT,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (graded_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_submission (student_id, assignment_id),
    INDEX idx_student (student_id),
    INDEX idx_assignment (assignment_id),
    INDEX idx_graded (grade)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Rubric Grades Table
-- Stores individual rubric criterion grades
-- =====================================================
CREATE TABLE IF NOT EXISTS rubric_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    rubric_id INT NOT NULL,
    points DECIMAL(5,2) NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (rubric_id) REFERENCES rubrics(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rubric_grade (submission_id, rubric_id),
    INDEX idx_submission (submission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Sample Data (Optional - for testing)
-- =====================================================
-- Uncomment the following section to insert sample data

/*
-- Sample Users (password is 'password123' hashed)
INSERT INTO users (name, email, password, role) VALUES
('Dr. John Smith', 'instructor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor'),
('Alice Johnson', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Bob Williams', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Carol Davis', 'carol@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

-- Sample Assignment
INSERT INTO assignments (instructor_id, title, description, due_date, max_score) VALUES
(1, 'Web Development Project', 'Create a responsive website using HTML, CSS, and JavaScript. The website should include at least 5 pages and demonstrate proper use of semantic HTML.', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 100);

-- Sample Rubric
INSERT INTO rubrics (assignment_id, criterion_name, description, max_points, sort_order) VALUES
(1, 'HTML Structure', 'Proper use of semantic HTML elements', 25, 1),
(1, 'CSS Styling', 'Effective use of CSS for layout and design', 25, 2),
(1, 'Responsiveness', 'Website works well on different screen sizes', 25, 3),
(1, 'Code Quality', 'Clean, well-organized, and commented code', 25, 4);
*/
