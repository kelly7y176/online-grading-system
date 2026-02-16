# Manual Test Cases

## CS425 Assignment Grading System

This document contains manual test procedures for the web application.

---

## Test Environment Setup

1. Start the application using Docker: `docker-compose up -d`
2. Access the application at: http://localhost:8080
3. Access phpMyAdmin at: http://localhost:8081

---

## Authentication Tests

### TC-001: Instructor Login

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Navigate to http://localhost:8080 | Login page displayed |
| 2 | Enter email: instructor@example.com | Email field populated |
| 3 | Enter password: password123 | Password field populated |
| 4 | Click "Login" button | Redirected to instructor dashboard |
| 5 | Verify dashboard shows instructor name | "Dr. John Smith" displayed |

### TC-002: Student Login

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Navigate to http://localhost:8080 | Login page displayed |
| 2 | Enter email: alice@example.com | Email field populated |
| 3 | Enter password: password123 | Password field populated |
| 4 | Click "Login" button | Redirected to student dashboard |
| 5 | Verify dashboard shows student name | "Alice Johnson" displayed |

### TC-003: Invalid Login

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Navigate to login page | Login page displayed |
| 2 | Enter email: invalid@example.com | Email field populated |
| 3 | Enter password: wrongpassword | Password field populated |
| 4 | Click "Login" button | Error message displayed |
| 5 | Verify error message | "Invalid email or password" shown |

### TC-004: User Registration

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Click "Register here" link | Registration page displayed |
| 2 | Fill in all required fields | Fields populated |
| 3 | Select role: Student | Role selected |
| 4 | Click "Register" button | Success message displayed |
| 5 | Verify redirect to login | Login page shown |

---

## Instructor Features

### TC-005: Create Assignment

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Login as instructor | Dashboard displayed |
| 2 | Click "New Assignment" button | Assignment form displayed |
| 3 | Enter title: "Test Assignment" | Title field populated |
| 4 | Enter description | Description field populated |
| 5 | Select due date (future date) | Date selected |
| 6 | Enter max score: 100 | Score field populated |
| 7 | Add rubric criteria | Criteria added |
| 8 | Click "Create Assignment" | Success message displayed |
| 9 | Verify assignment in list | New assignment visible |

### TC-006: Edit Assignment

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Navigate to Assignments page | Assignment list displayed |
| 2 | Click "Edit" on an assignment | Edit form displayed |
| 3 | Modify title | Title updated |
| 4 | Click "Update Assignment" | Success message displayed |
| 5 | Verify changes saved | Updated title visible |

### TC-007: View Submissions

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Navigate to Assignments page | Assignment list displayed |
| 2 | Click "Submissions" button | Submissions list displayed |
| 3 | Verify submission details | Student names, dates visible |
| 4 | Verify grade status | Graded/Pending badges shown |

### TC-008: Grade Submission

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Navigate to submissions list | Submissions displayed |
| 2 | Click "Grade" on a submission | Grading form displayed |
| 3 | View submission content | Content/file visible |
| 4 | Enter rubric grades | Points entered for each criterion |
| 5 | Enter overall feedback | Feedback text entered |
| 6 | Click "Save Grade" | Success message displayed |
| 7 | Verify grade saved | Grade visible in list |

### TC-009: Export Grades

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Navigate to submissions list | Submissions displayed |
| 2 | Click "Export CSV" button | CSV file downloaded |
| 3 | Open CSV file | Contains all submission data |

---

## Student Features

### TC-010: View Assignments

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Login as student | Dashboard displayed |
| 2 | Click "Assignments" in menu | Assignment list displayed |
| 3 | Verify assignment details | Title, due date, status visible |

### TC-011: Submit Assignment (Text)

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Navigate to Assignments page | Assignment list displayed |
| 2 | Click "Submit" on an assignment | Submission form displayed |
| 3 | Enter text content | Text area populated |
| 4 | Click "Submit Assignment" | Success message displayed |
| 5 | Verify submission status | Status changed to "Submitted" |

### TC-012: Submit Assignment (File)

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Navigate to submission form | Form displayed |
| 2 | Click file upload area | File browser opens |
| 3 | Select a PDF file | File name displayed |
| 4 | Click "Submit Assignment" | Success message displayed |
| 5 | Verify file uploaded | File link visible |

### TC-013: View Grades

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Login as student | Dashboard displayed |
| 2 | Click "My Grades" in menu | Grades page displayed |
| 3 | Verify grade details | Scores, percentages visible |
| 4 | Click "View Feedback" | Feedback page displayed |
| 5 | Verify rubric breakdown | Individual criterion grades shown |

### TC-014: Resubmit Assignment

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Navigate to submitted assignment | Submission visible |
| 2 | Click "Resubmit" button | Submission form displayed |
| 3 | Modify content | New content entered |
| 4 | Click "Update Submission" | Success message displayed |
| 5 | Verify previous grade cleared | Grade reset to pending |

---

## Security Tests

### TC-015: Access Control - Instructor Pages

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Login as student | Student dashboard displayed |
| 2 | Navigate to /instructor/dashboard.php | Redirected to login |
| 3 | Verify access denied | Cannot access instructor pages |

### TC-016: Access Control - Student Pages

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Login as instructor | Instructor dashboard displayed |
| 2 | Navigate to /student/dashboard.php | Redirected to login |
| 3 | Verify access denied | Cannot access student pages |

### TC-017: CSRF Protection

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | View page source | CSRF token in form |
| 2 | Submit form without token | Error message displayed |
| 3 | Verify form rejected | "Invalid request" shown |

---

## Responsive Design Tests

### TC-018: Mobile View

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Open browser dev tools | Dev tools displayed |
| 2 | Select mobile device | Mobile viewport active |
| 3 | Navigate through pages | Layout adapts to mobile |
| 4 | Verify navigation | Menu accessible on mobile |

---

## Test Results Template

| Test ID | Description | Status | Tester | Date | Notes |
|---------|-------------|--------|--------|------|-------|
| TC-001 | Instructor Login | | | | |
| TC-002 | Student Login | | | | |
| TC-003 | Invalid Login | | | | |
| ... | ... | | | | |

**Status Values:** PASS, FAIL, BLOCKED, SKIP
