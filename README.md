# CS425 Assignment Grading System - Starter Codebase

Welcome to the starter codebase for the **Software Engineering and Project Management** assignment. This project provides a functional, Dockerized web application for grading student assignments. Its purpose is to allow you to focus on **enhancement, optimization, and project management** rather than building a system from scratch.

## 1. Project Overview

This starter kit is a basic PHP, MySQL, and HTML/CSS application that simulates an online assignment grading platform. It comes with a pre-built RESTful API, a database schema, and a user interface for two main roles: **Instructors** and **Students**.

### Core Features

- **User Authentication:** Secure login and registration for instructors and students.
- **Role-Based Access Control:** Separate dashboards and permissions for each user role.
- **Instructor Features:**
  - Create, edit, and delete assignments.
  - Define grading rubrics with weighted criteria.
  - View student submissions for each assignment.
  - Grade submissions using the defined rubric and provide feedback.
  - Export grades to CSV.
- **Student Features:**
  - View available assignments and due dates.
  - Submit assignments via text input or file upload.
  - View grades and detailed feedback after grading.
- **RESTful API:** A JSON-based API for programmatic access to assignments, submissions, and grades.
- **Dockerized Environment:** A fully containerized setup using Docker Compose for easy, consistent deployment across all machines.
- **XAMPP Compatibility:** Includes instructions for setting up the project using a traditional XAMPP stack as a fallback.

## 2. Your Task: Enhance and Optimize

Your primary goal is **not** to build this system, but to **improve it**. You are expected to work in your groups to enhance and optimize the existing codebase, demonstrating your skills in software engineering and project management.

Here are some areas you can focus on. You should choose a few and document your process clearly.

| Category | Enhancement Ideas |
| :--- | :--- |
| **Frontend / UI/UX** | - Improve the visual design and layout.<br>- Implement a fully responsive, mobile-first interface.<br>- Add a dark mode.<br>- Use JavaScript to create a more dynamic, single-page-like experience (e.g., using AJAX for forms).<br>- Add client-side form validation for a better user experience. |
| **Backend / Performance** | - **Optimize SQL queries:** Identify slow queries and add indexes to the database tables (`schema.sql`).<br>- **Implement caching:** Use a system like Redis (which can be added to `docker-compose.yml`) to cache database queries.<br>- **Improve file handling:** Add more robust validation for file uploads.<br>- **Refactor code:** Improve the structure of the PHP code for better maintainability (e.g., implement a more formal MVC router). |
| **New Features** | - **Password Reset:** Implement a "Forgot Password" feature.<br>- **Assignment Cloning:** Allow instructors to duplicate an existing assignment.<br>- **Plagiarism Detection:** Integrate a basic plagiarism checker (e.g., comparing text submissions for similarity).<br>- **Notifications:** Create an in-app notification system for events like new grades or upcoming due dates.<br>- **Advanced Analytics:** Build a dashboard with charts (e.g., using Chart.js) to visualize grade distributions. |
| **API & Testing** | - **API Security:** Implement JWT (JSON Web Tokens) for more secure, stateless API authentication.<br>- **API Documentation:** Create professional API documentation (e.g., using Swagger/OpenAPI).<br>- **Automated Testing:** Write PHPUnit tests for the backend models and controllers. |
| **Project Management** | - Use tools like Jira or Trello to manage your tasks in sprints.<br>- Document your team roles, decisions, and progress.<br>- Use Git effectively with a clear branching strategy (e.g., GitFlow). |

**You will be assessed on the quality of your enhancements, the clarity of your documentation, and the effectiveness of your project management process.**

## 3. Environment Setup

You can set up the project in two ways: using **Docker (recommended)** or **XAMPP**.

### Method 1: Docker Setup (Recommended)

Docker provides a consistent, isolated environment. This is the preferred method.

**Prerequisites:**
- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

**Instructions:**

1.  **Clone the Repository:**
    ```bash
    git clone <repository_url>
    cd cs425-assignment-grading-system
    ```

2.  **Run the Setup Script:**
    This script will prepare the environment and start the Docker containers.
    ```bash
    chmod +x scripts/setup.sh
    ./scripts/setup.sh
    ```

3.  **Access the Application:**
    Once the script finishes, the application will be running. 
    - **Web Application:** [http://localhost:8080](http://localhost:8080)
    - **Database Admin (phpMyAdmin):** [http://localhost:8081](http://localhost:8081)

**Default Login Credentials:**
- **Instructor:** `instructor@example.com` / `password`
- **Student:** `alice@example.com` / `password`

### Method 2: XAMPP Setup (Alternative)

If you are unable to use Docker, you can use a local server stack like XAMPP.

**Prerequisites:**
- [XAMPP](https://www.apachefriends.org/index.html) with PHP 8.2+, Apache, and MySQL.

**Instructions:**

1.  **Start XAMPP:** Launch the XAMPP Control Panel and start the **Apache** and **MySQL** services.

2.  **Copy Project Files:**
    - Copy the `app` folder from this project into your XAMPP `htdocs` directory.
    - Rename the copied `app` folder to `grading-system`.
    - The final path should be `C:\xampp\htdocs\grading-system` or similar.

3.  **Create the Database:**
    - Open your browser and go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
    - Click **New** in the left sidebar.
    - Enter the database name `grading_system` and click **Create**.

4.  **Import the Database Schema:**
    - Select the `grading_system` database you just created.
    - Go to the **Import** tab.
    - Click **Choose File** and select the `database/schema.sql` file from this project.
    - Click **Go** at the bottom of the page.

5.  **Import Sample Data (Optional):**
    - After the schema is imported, go to the **Import** tab again.
    - Click **Choose File** and select the `database/seed.sql` file.
    - Click **Go**.

6.  **Access the Application:**
    - You can now access the application at: [http://localhost/grading-system](http://localhost/grading-system)

## 4. Project Structure

```
/
├── app/                  # Main application code (your workspace)
│   ├── api/              # API endpoint handlers
│   ├── assets/           # CSS, JS, images
│   ├── config/           # Database and app configuration
│   ├── controllers/      # Business logic handlers
│   ├── models/           # Database interaction logic
│   ├── views/            # HTML templates (frontend)
│   ├── uploads/          # Directory for file submissions
│   └── ...               # PHP page files (e.g., login.php)
├── database/
│   ├── schema.sql        # Database table structure
│   └── seed.sql          # Sample data for testing
├── docker/
│   └── Dockerfile        # PHP container configuration
├── docs/                 # Documentation files
├── scripts/
│   ├── setup.sh          # Docker setup script
│   └── ...
├── tests/
│   ├── api_tests.md      # Test cases for the API
│   └── manual_tests.md   # Manual test cases for the UI
├── docker-compose.yml    # Docker orchestration file
└── README.md             # This file
```

## 5. Using the API

The starter kit includes a RESTful API. You can use tools like [Postman](https://www.postman.com/) to interact with it. The API is useful for testing, automation, or building a separate frontend (e.g., a mobile app).

- **Base URL:** `http://localhost:8080/api`
- **Health Check:** `GET /api/health`
- **Authentication:** The API uses the same session-based authentication as the web app. Log in through the web interface first, and your API requests from the same browser will be authenticated.

For detailed endpoints and examples, see the `tests/api_tests.md` file.

Good luck!
