<?php 
// 1. Dynamic Pathing: Find the project root regardless of where we are
$configPath = $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
if (file_exists($configPath)) {
    require_once $configPath;
} else {
    // Fallback pathing if DOCUMENT_ROOT fails
    require_once dirname(dirname(dirname(__DIR__))) . '/config/config.php';
}

// 2. Correct Cookie Detection for Theme
$themeClass = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? 'dark' : ''; 

// 3. Navigation Helper
if (!function_exists('isActive')) {
    function isActive($path) {
        return strpos($_SERVER['PHP_SELF'], $path) !== false 
            ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' 
            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600';
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $themeClass; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="https://img.favpng.com/13/15/8/computer-icons-clip-art-portable-network-graphics-student-icon-design-png-favpng-2t9HGG0V6WbuE0N7JK7xmJv3B.jpg" />
    <title><?php echo $pageTitle ?? 'Grading System'; ?> | GradeSys</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { primary: '#4361ee' }
                }
            }
        }
    </script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-300 min-h-screen">
    
<nav class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="<?php echo BASE_URL; ?>" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <i class="fas fa-graduation-cap text-white text-xs"></i>
                    </div>
                    <span class="font-black tracking-tighter text-xl text-slate-900 dark:text-white uppercase">Grade<span class="text-blue-600">Sys</span></span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'instructor'): ?>
                        <a href="<?php echo BASE_URL; ?>/instructor/dashboard.php" class="px-4 py-2 rounded-xl text-sm font-bold transition <?php echo isActive('instructor/dashboard.php'); ?>">Dashboard</a>
                        <a href="<?php echo BASE_URL; ?>/instructor/assignments.php" class="px-4 py-2 rounded-xl text-sm font-bold transition <?php echo isActive('instructor/assignments.php'); ?>">Assignments</a>
                        <a href="<?php echo BASE_URL; ?>/instructor/students.php" class="px-4 py-2 rounded-xl text-sm font-bold transition <?php echo isActive('instructor/students.php'); ?>">Students</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/student/dashboard.php" class="px-4 py-2 rounded-xl text-sm font-bold transition <?php echo isActive('student/dashboard.php'); ?>">Dashboard</a>
                        <a href="<?php echo BASE_URL; ?>/student/assignments.php" class="px-4 py-2 rounded-xl text-sm font-bold transition <?php echo isActive('student/assignments.php'); ?>">Assignments</a>
                        <a href="<?php echo BASE_URL; ?>/student/submissions.php" class="px-4 py-2 rounded-xl text-sm font-bold transition <?php echo isActive('student/submissions.php'); ?>">Submissions</a>
                        <a href="<?php echo BASE_URL; ?>/student/grades.php" class="px-4 py-2 rounded-xl text-sm font-bold transition <?php echo isActive('student/grades.php'); ?>">Grades</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button id="theme-toggle" class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition" title="Toggle Theme">
                    <i class="fas fa-moon dark:hidden text-lg"></i>
                    <i class="fas fa-sun hidden dark:block text-lg"></i>
                </button>
                
                <button id="mobile-menu-button" class="md:hidden p-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <div class="hidden md:flex items-center gap-4 ml-2 pl-4 border-l border-slate-100 dark:border-slate-800">
                    <a href="<?php echo BASE_URL; ?>/logout.php" class="text-xs font-black text-slate-400 hover:text-red-500 transition uppercase tracking-widest">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 space-y-1">
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'instructor'): ?>
            <a href="<?php echo BASE_URL; ?>/instructor/dashboard.php" class="block px-4 py-3 rounded-xl text-base font-bold <?php echo isActive('instructor/dashboard.php'); ?>">Dashboard</a>
            <a href="<?php echo BASE_URL; ?>/instructor/assignments.php" class="block px-4 py-3 rounded-xl text-base font-bold <?php echo isActive('instructor/assignments.php'); ?>">Assignments</a>
            <a href="<?php echo BASE_URL; ?>/instructor/students.php" class="block px-4 py-3 rounded-xl text-base font-bold <?php echo isActive('instructor/students.php'); ?>">Students</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/student/dashboard.php" class="block px-4 py-3 rounded-xl text-base font-bold <?php echo isActive('student/dashboard.php'); ?>">Dashboard</a>
            <a href="<?php echo BASE_URL; ?>/student/assignments.php" class="block px-4 py-3 rounded-xl text-base font-bold <?php echo isActive('student/assignments.php'); ?>">Assignments</a>
            <a href="<?php echo BASE_URL; ?>/student/submissions.php" class="block px-4 py-3 rounded-xl text-base font-bold <?php echo isActive('student/submissions.php'); ?>">Submissions</a>
            <a href="<?php echo BASE_URL; ?>/student/grades.php" class="block px-4 py-3 rounded-xl text-base font-bold <?php echo isActive('student/grades.php'); ?>">Grades</a>
        <?php endif; ?>
        <hr class="border-slate-100 dark:border-slate-800 my-2">
        <a href="<?php echo BASE_URL; ?>/logout.php" class="block px-4 py-3 rounded-xl text-base font-bold text-red-500">Logout</a>
    </div>
</nav>

<main class="max-w-7xl mx-auto p-4 md:p-8">