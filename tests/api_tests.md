# API Test Cases

## CS425 Assignment Grading System

This document contains test cases for the REST API endpoints. Use Postman or similar tools to execute these tests.

---

## Authentication API

### Test 1: User Login (Success)

**Endpoint:** `POST /api/auth/login`

**Request Body:**
```json
{
    "email": "instructor@example.com",
    "password": "password123"
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "data": {
        "message": "Login successful",
        "user": {
            "id": 1,
            "name": "Dr. John Smith",
            "email": "instructor@example.com",
            "role": "instructor"
        }
    }
}
```

### Test 2: User Login (Invalid Credentials)

**Endpoint:** `POST /api/auth/login`

**Request Body:**
```json
{
    "email": "instructor@example.com",
    "password": "wrongpassword"
}
```

**Expected Response (401):**
```json
{
    "success": false,
    "error": "Invalid credentials"
}
```

### Test 3: User Registration

**Endpoint:** `POST /api/auth/register`

**Request Body:**
```json
{
    "name": "New Student",
    "email": "newstudent@example.com",
    "password": "password123",
    "role": "student"
}
```

**Expected Response (201):**
```json
{
    "success": true,
    "data": {
        "message": "Registration successful",
        "user": {
            "id": 8,
            "name": "New Student",
            "email": "newstudent@example.com",
            "role": "student"
        }
    }
}
```

---

## Assignments API

### Test 4: Get All Assignments

**Endpoint:** `GET /api/assignments`

**Expected Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Web Development Project",
            "description": "...",
            "due_date": "2026-01-20",
            "max_score": 100,
            "submission_count": 3,
            "graded_count": 1
        }
    ]
}
```

### Test 5: Get Single Assignment

**Endpoint:** `GET /api/assignments/1`

**Expected Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Web Development Project",
        "rubrics": [
            {
                "id": 1,
                "criterion_name": "HTML Structure",
                "max_points": 25
            }
        ]
    }
}
```

### Test 6: Create Assignment (Instructor Only)

**Endpoint:** `POST /api/assignments`

**Headers:** Requires authenticated session as instructor

**Request Body:**
```json
{
    "title": "New Assignment",
    "description": "Assignment description",
    "due_date": "2026-02-15",
    "max_score": 100,
    "rubrics": [
        {
            "criterion_name": "Quality",
            "description": "Overall quality",
            "max_points": 50
        },
        {
            "criterion_name": "Completeness",
            "description": "All requirements met",
            "max_points": 50
        }
    ]
}
```

**Expected Response (201):**
```json
{
    "success": true,
    "data": {
        "id": 5,
        "title": "New Assignment",
        "rubrics": [...]
    }
}
```

---

## Submissions API

### Test 7: Submit Assignment (Student Only)

**Endpoint:** `POST /api/submissions`

**Headers:** Requires authenticated session as student

**Request Body:**
```json
{
    "assignment_id": 1,
    "text_content": "My submission content..."
}
```

**Expected Response (201):**
```json
{
    "success": true,
    "data": {
        "id": 5,
        "student_id": 3,
        "assignment_id": 1,
        "text_content": "My submission content..."
    }
}
```

### Test 8: Grade Submission (Instructor Only)

**Endpoint:** `PUT /api/submissions/1`

**Headers:** Requires authenticated session as instructor

**Request Body:**
```json
{
    "grade": 85,
    "feedback": "Good work overall!",
    "rubric_grades": {
        "1": {"points": 22, "comment": "Good HTML structure"},
        "2": {"points": 23, "comment": "Nice CSS styling"},
        "3": {"points": 20, "comment": "Responsive design works"},
        "4": {"points": 10, "comment": "JavaScript needs work"},
        "5": {"points": 10, "comment": "Code could be cleaner"}
    }
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "grade": 85,
        "feedback": "Good work overall!",
        "rubric_grades": [...]
    }
}
```

---

## Grades API

### Test 9: Export Grades as CSV

**Endpoint:** `GET /api/grades/1/export`

**Headers:** Requires authenticated session as instructor

**Expected Response:** CSV file download

### Test 10: Get Grade Statistics

**Endpoint:** `GET /api/grades/stats?assignment_id=1`

**Headers:** Requires authenticated session as instructor

**Expected Response (200):**
```json
{
    "success": true,
    "data": {
        "assignment_id": 1,
        "assignment_title": "Web Development Project",
        "max_score": 100,
        "statistics": {
            "total_submissions": 3,
            "graded_count": 1,
            "average_grade": 85,
            "min_grade": 85,
            "max_grade": 85
        }
    }
}
```

---

## Health Check

### Test 11: API Health Check

**Endpoint:** `GET /api/health`

**Expected Response (200):**
```json
{
    "success": true,
    "data": {
        "status": "ok",
        "version": "1.0.0"
    }
}
```

---

## Error Handling Tests

### Test 12: 404 Not Found

**Endpoint:** `GET /api/nonexistent`

**Expected Response (404):**
```json
{
    "success": false,
    "error": "Endpoint not found"
}
```

### Test 13: 401 Unauthorized

**Endpoint:** `POST /api/assignments` (without authentication)

**Expected Response (401):**
```json
{
    "success": false,
    "error": "Unauthorized"
}
```

### Test 14: 403 Forbidden

**Endpoint:** `POST /api/assignments` (as student)

**Expected Response (403):**
```json
{
    "success": false,
    "error": "Only instructors can create assignments"
}
```

---

## Postman Collection

Import the following collection into Postman for automated testing:

```json
{
    "info": {
        "name": "CS425 Grading System API",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    "item": [
        {
            "name": "Health Check",
            "request": {
                "method": "GET",
                "url": "{{base_url}}/api/health"
            }
        },
        {
            "name": "Login",
            "request": {
                "method": "POST",
                "url": "{{base_url}}/api/auth/login",
                "body": {
                    "mode": "raw",
                    "raw": "{\"email\": \"instructor@example.com\", \"password\": \"password123\"}"
                }
            }
        }
    ],
    "variable": [
        {
            "key": "base_url",
            "value": "http://localhost:8080"
        }
    ]
}
```
