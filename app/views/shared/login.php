<?php
/**
 * Login View
 * Path: /app/views/shared/login.php
 */

// We are in /views/shared/, so we go UP two levels to find /config/ and /controllers/
require_once dirname(dirname(__DIR__)) . '/config/config.php';
require_once dirname(dirname(__DIR__)) . '/controllers/AuthController.php';

$auth = new AuthController();

// Handle Login Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->login();
}
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? 'dark' : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GradeSys</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { primary: '#4361ee' },
                    borderRadius: { '3xl': '1.5rem', '4xl': '2rem' }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 min-h-screen flex items-center justify-center p-4 transition-colors duration-300">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-[1.5rem] shadow-xl shadow-blue-500/30 mb-4">
                <i class="fas fa-graduation-cap text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white uppercase">
                Grade<span class="text-blue-600">Sys</span>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium mt-2 text-sm uppercase tracking-widest">Academic Portal</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 p-8 md:p-10 shadow-xl shadow-slate-200/50 dark:shadow-none">
            
            <h2 class="text-xl font-black text-slate-900 dark:text-white mb-8 uppercase tracking-tight">Sign In</h2>

            <?php $flash = getFlashMessage(); ?>
            <?php if ($flash): ?>
                <div class="mb-6 p-4 rounded-2xl flex items-center gap-3 <?php echo $flash['type'] === 'error' ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 border border-rose-100 dark:border-rose-800' : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 border border-emerald-100 dark:border-emerald-800'; ?>">
                    <i class="fas <?php echo $flash['type'] === 'error' ? 'fa-circle-xmark' : 'fa-circle-check'; ?>"></i>
                    <p class="text-xs font-black uppercase tracking-tight"><?php echo $flash['message']; ?></p>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="space-y-2">
                    <label for="email" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-2">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="email" id="email" name="email" required 
                               class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl py-4 pl-12 pr-6 text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-slate-400"
                               placeholder="name@university.edu">
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center ml-2">
                        <label for="password" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Password</label>
                        <a href="forgot-password.php" class="text-[10px] font-black uppercase text-blue-600 hover:underline">Forgot?</a>
                    </div>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="password" id="password" name="password" required
                               class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl py-4 pl-12 pr-6 text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-slate-400"
                               placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-500/30 transition-all active:scale-[0.98] mt-4">
                    Sign Into Portal
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
                <p class="text-sm font-medium text-slate-500">
                    Don't have an account? 
                    <a href="register.php" class="text-blue-600 font-black uppercase tracking-tight hover:underline ml-1">Register here</a>
                </p>
            </div>
        </div>
        
        <div class="mt-8 flex justify-center">
            <button onclick="toggleTheme()" class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 hover:text-blue-600 transition flex items-center gap-2">
                <i class="fas fa-circle-half-stroke"></i>
                Switch Appearance
            </button>
        </div>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.toggle('dark');
            document.cookie = `theme=${isDark ? 'dark' : 'light'};path=/;max-age=31536000;SameSite=Lax`;
        }
    </script>
</body>
</html>