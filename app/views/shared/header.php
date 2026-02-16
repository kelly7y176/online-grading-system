<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <div class="page-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo APP_NAME; ?></h2>
                <p class="text-muted" style="font-size: 12px; color: rgba(255,255,255,0.6);">
                    <?php echo $_SESSION['user_name']; ?><br>
                    <span style="text-transform: capitalize;"><?php echo $_SESSION['user_role']; ?></span>
                </p>
            </div>
            
            <nav>
                <ul class="nav-menu">
                    <?php if (isInstructor()): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/instructor/dashboard.php" 
                           class="<?php echo strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : ''; ?>">
                            📊 Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/instructor/assignments.php"
                           class="<?php echo strpos($_SERVER['REQUEST_URI'], 'assignment') !== false ? 'active' : ''; ?>">
                            📝 Assignments
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/instructor/students.php"
                           class="<?php echo strpos($_SERVER['REQUEST_URI'], 'student') !== false ? 'active' : ''; ?>">
                            👥 Students
                        </a>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/student/dashboard.php"
                           class="<?php echo strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : ''; ?>">
                            📊 Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/student/assignments.php"
                           class="<?php echo strpos($_SERVER['REQUEST_URI'], 'assignment') !== false ? 'active' : ''; ?>">
                            📝 Assignments
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/student/submissions.php"
                           class="<?php echo strpos($_SERVER['REQUEST_URI'], 'submission') !== false ? 'active' : ''; ?>">
                            📤 My Submissions
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/student/grades.php"
                           class="<?php echo strpos($_SERVER['REQUEST_URI'], 'grade') !== false ? 'active' : ''; ?>">
                            📈 My Grades
                        </a>
                    </li>
                    <?php endif; ?>
                    <li style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px;">
                        <a href="<?php echo BASE_URL; ?>/logout.php">
                            🚪 Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Flash Messages -->
            <?php $flash = getFlashMessage(); ?>
            <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
            <?php endif; ?>
    <?php endif; ?>
