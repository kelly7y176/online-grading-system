# Lecturer's Guide

## CS425 Assignment Grading System - Starter Codebase

---

### 1. Introduction

This document provides guidance for lecturers on setting up, running, and assessing student projects based on the CS425 Assignment Grading System starter codebase.

The goal of this starter project is to shift the focus of the assignment from building a foundational application to **enhancing, optimizing, and managing an existing system**. This approach allows students to engage with a more realistic software engineering workflow, where they are often required to understand and improve upon pre-existing code.

Students are expected to demonstrate their skills in:
- **Project Management:** Planning and executing tasks within a team.
- **Software Design & Architecture:** Making thoughtful improvements to the system's structure.
- **Code Quality:** Writing clean, maintainable, and efficient code.
- **Testing:** Verifying their changes and ensuring the system remains stable.

### 2. Setting Up a Student's Project for Assessment

Each student team will submit a `.zip` file containing their modified codebase and project management artifacts. The recommended way to assess each project is to run it within the provided Docker environment to ensure consistency.

**Setup Steps:**

1.  **Unzip the Submission:** Extract the student's `.zip` file into a unique folder (e.g., `student-group-A/`).

2.  **Navigate to the Project Directory:**
    ```bash
    cd student-group-A
    ```

3.  **Run the Setup Script:**
    The student's project should contain the same `setup.sh` script. Execute it to build and run their version of the application.
    ```bash
    chmod +x scripts/setup.sh
    ./scripts/setup.sh
    ```
    This command will build their Docker containers and launch the application. Each student project will run on the same ports (`8080` for the app, `8081` for phpMyAdmin), so you should only run one student's project at a time.

4.  **Access and Test:**
    - **Web Application:** [http://localhost:8080](http://localhost:8080)
    - **Database (phpMyAdmin):** [http://localhost:8081](http://localhost:8081)

5.  **Shut Down Before Next Assessment:**
    After you have finished assessing a project, shut down its containers to free up the ports for the next one.
    ```bash
    docker-compose down
    ```

### 3. Guide to Assessing Student Work

Assessment should focus on the **quality and documentation of the enhancements** made by the students. The starter code provides the baseline functionality required by the assignment brief.

Here is a guide to evaluating each assessment criterion:

| Criterion | What to Look For |
| :--- | :--- |
| **Project Management (20%)** | - **Planning Artifacts:** Look for evidence of planning (e.g., Trello board screenshots, Gantt charts, sprint plans).<br>- **Git History:** Check the `git log`. Is there a clear history of commits from all team members? Are the commit messages meaningful? Did they use branches for features (`git log --graph --oneline --all`)?<br>- **README/Report:** The students should have a report or an updated `README.md` that clearly documents their enhancements, design choices, and team roles. |
| **Software Design & Code Quality (30%)** | - **Code Review:** Examine the modified files. Is the new code clean, well-commented, and easy to understand?<br>- **Architecture:** Did they make logical improvements? For example, if they added a new feature, did they follow the existing MVC-like pattern (Model, View, Controller)?<br>- **Database:** Check `database/schema.sql`. Did they add indexes to optimize queries? Did they add new tables for new features in a normalized way? |
| **Requirements Coverage (15%)** | - **Functional Demo:** The baseline application already meets the core functional requirements. Marks here should be awarded for the **successful implementation of their chosen enhancements**.<br>- **Robustness:** Do their new features handle errors gracefully? For example, if they added a new form, does it have validation? |
| **Infrastructure & Version Control (15%)** | - **Docker:** Did they modify the Docker setup? For example, did they add a new service like Redis to `docker-compose.yml`? If so, is it configured correctly?<br>- **Version Control:** Assessed primarily through the Git history and project management artifacts. |
| **Testing (15%)** | - **Test Documentation:** Check the `tests/` directory. Did they update `api_tests.md` or `manual_tests.md` for their new features?<br>- **Automated Tests:** If they chose to implement automated testing (e.g., PHPUnit), are the tests well-written and do they pass? |
| **Demonstration (5%)** | - During the live demo, can the students clearly articulate the changes they made and demonstrate that their new features work correctly? |

### 4. Academic Integrity

Since all students start from the same codebase, standard plagiarism detection tools may flag high similarity scores between submissions. It is crucial to mitigate this by focusing the assessment on the **unique enhancements** and the **process documentation**.

- **Require a Statement of Work:** Each team should clearly document which enhancements they implemented. This makes it easier to compare projects based on their unique contributions.
- **Review Git History:** The commit history provides a fingerprint of the team's work process. Look for consistent contributions from all members.
- **Focus on the "Why":** The students' report or `README` should explain *why* they made certain design choices. This is harder to plagiarize than the code itself.

By providing this starter kit, we are testing the students' ability to work on a real-world project, which is a valuable skill that goes beyond writing code from a blank slate.
