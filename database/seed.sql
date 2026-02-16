-- =====================================================
-- CS425 Assignment Grading System - Seed Data
-- =====================================================
-- This file inserts sample data for testing purposes.
-- Run this after schema.sql to populate the database.
-- 
-- Default credentials:
-- Instructor: instructor@example.com / password
-- Students: alice@example.com, bob@example.com, carol@example.com / password
-- =====================================================

USE grading_system;

-- =====================================================
-- Sample Users
-- Password for all users is: password
-- =====================================================
INSERT INTO users (name, email, password, role) VALUES
('Dr. John Smith', 'instructor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor'),
('Prof. Jane Doe', 'instructor2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor'),
('Alice Johnson', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Bob Williams', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Carol Davis', 'carol@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('David Brown', 'david@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Emma Wilson', 'emma@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

-- =====================================================
-- Sample Assignments
-- =====================================================
INSERT INTO assignments (instructor_id, title, description, due_date, max_score) VALUES
(1, 'Web Development Project', 
 'Create a responsive website using HTML, CSS, and JavaScript. The website should include at least 5 pages and demonstrate proper use of semantic HTML elements. Include a contact form and navigation menu.',
 DATE_ADD(CURDATE(), INTERVAL 14 DAY), 100),

(1, 'Database Design Assignment', 
 'Design a database schema for an e-commerce platform. Include ER diagrams, normalized tables, and sample SQL queries for common operations.',
 DATE_ADD(CURDATE(), INTERVAL 7 DAY), 100),

(1, 'API Integration Task', 
 'Build a PHP application that consumes a public REST API. Display the data in a user-friendly format and implement error handling.',
 DATE_ADD(CURDATE(), INTERVAL 21 DAY), 80),

(2, 'Software Testing Report', 
 'Write a comprehensive testing report for a given software application. Include test cases, test results, and recommendations for improvement.',
 DATE_ADD(CURDATE(), INTERVAL 10 DAY), 100);

-- =====================================================
-- Sample Rubrics
-- =====================================================
-- Rubric for Assignment 1 (Web Development Project)
INSERT INTO rubrics (assignment_id, criterion_name, description, max_points, sort_order) VALUES
(1, 'HTML Structure', 'Proper use of semantic HTML5 elements, valid markup, and accessibility considerations', 25, 1),
(1, 'CSS Styling', 'Effective use of CSS for layout, typography, colors, and visual design', 25, 2),
(1, 'Responsiveness', 'Website adapts well to different screen sizes (mobile, tablet, desktop)', 20, 3),
(1, 'JavaScript Functionality', 'Interactive features work correctly and enhance user experience', 15, 4),
(1, 'Code Quality', 'Clean, well-organized, properly indented, and commented code', 15, 5);

-- Rubric for Assignment 2 (Database Design)
INSERT INTO rubrics (assignment_id, criterion_name, description, max_points, sort_order) VALUES
(2, 'ER Diagram', 'Complete and accurate entity-relationship diagram', 30, 1),
(2, 'Normalization', 'Tables properly normalized to at least 3NF', 25, 2),
(2, 'SQL Queries', 'Correct and efficient SQL queries for required operations', 25, 3),
(2, 'Documentation', 'Clear explanations and justifications for design decisions', 20, 4);

-- Rubric for Assignment 3 (API Integration)
INSERT INTO rubrics (assignment_id, criterion_name, description, max_points, sort_order) VALUES
(3, 'API Integration', 'Successfully connects to and retrieves data from the API', 30, 1),
(3, 'Data Display', 'Data is presented in a clear, user-friendly format', 20, 2),
(3, 'Error Handling', 'Gracefully handles API errors and edge cases', 15, 3),
(3, 'Code Quality', 'Well-structured PHP code with proper separation of concerns', 15, 4);

-- Rubric for Assignment 4 (Testing Report)
INSERT INTO rubrics (assignment_id, criterion_name, description, max_points, sort_order) VALUES
(4, 'Test Coverage', 'Comprehensive test cases covering all major functionality', 30, 1),
(4, 'Test Documentation', 'Clear documentation of test procedures and expected results', 25, 2),
(4, 'Results Analysis', 'Thorough analysis of test results with evidence', 25, 3),
(4, 'Recommendations', 'Actionable recommendations for improvement', 20, 4);

-- =====================================================
-- Sample Submissions
-- =====================================================
INSERT INTO submissions (student_id, assignment_id, text_content, submitted_at) VALUES
(3, 1, 'My web development project includes a portfolio website with 5 pages: Home, About, Projects, Skills, and Contact. I used semantic HTML5 elements throughout and implemented a responsive design using CSS Grid and Flexbox.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 1, 'I created a restaurant website with menu, gallery, reservations, about us, and contact pages. The site is fully responsive and includes JavaScript for the image gallery and form validation.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(5, 1, 'Submitted my e-commerce landing page project. Features include product showcase, testimonials, newsletter signup, and contact form.', NOW()),
(3, 2, 'Database design for an online bookstore. Includes tables for books, authors, customers, orders, and reviews. ER diagram attached.', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- =====================================================
-- Sample Grades (for some submissions)
-- =====================================================
UPDATE submissions SET grade = 85, feedback = 'Good work on the HTML structure and CSS styling. The responsiveness could be improved for tablet sizes. JavaScript functionality works well.', graded_at = NOW(), graded_by = 1 WHERE id = 1;

-- Sample Rubric Grades
INSERT INTO rubric_grades (submission_id, rubric_id, points, comment) VALUES
(1, 1, 22, 'Good use of semantic elements'),
(1, 2, 23, 'Nice visual design'),
(1, 3, 15, 'Some issues on tablet view'),
(1, 4, 13, 'Form validation works well'),
(1, 5, 12, 'Code could be better organized');

-- =====================================================
-- End of Seed Data
-- =====================================================
