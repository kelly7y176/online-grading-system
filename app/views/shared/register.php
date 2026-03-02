<?php
/**
 * Registration View
 * Path: /app/views/shared/register.php
 */

// We go UP two levels from /views/shared/ to find /config/
require_once dirname(dirname(__DIR__)) . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? 'dark' : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | GradeSys</title>
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
        select { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 1rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem; -webkit-appearance: none; -moz-appearance: none; appearance: none; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 min-h-screen flex items-center justify-center p-6 transition-colors duration-300">

    <div class="w-full max-w-xl">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 rounded-2xl shadow-xl shadow-blue-500/30 mb-4">
                <i class="fas fa-user-plus text-white text-lg"></i>
            </div>
            <h1 class="text-2xl font-black tracking-tighter text-slate-900 dark:text-white uppercase">
                Join <span class="text-blue-600">GradeSys</span>
            </h1>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none">
            
            <h2 class="text-xl font-black text-slate-900 dark:text-white mb-8 uppercase tracking-tight">Create Account</h2>

            <?php $flash = getFlashMessage(); ?>
            <?php if ($flash): ?>
                <div class="mb-6 p-4 rounded-2xl flex items-center gap-3 <?php echo $flash['type'] === 'error' ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 border border-rose-100 dark:border-rose-800' : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 border border-emerald-100 dark:border-emerald-800'; ?>">
                    <i class="fas <?php echo $flash['type'] === 'error' ? 'fa-circle-xmark' : 'fa-circle-check'; ?>"></i>
                    <p class="text-xs font-black uppercase tracking-tight"><?php echo $flash['message']; ?></p>
                </div>
            <?php endif; ?>

            <form action="../../register.php" method="POST" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label for="name" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Full Name</label>
                        <input type="text" id="name" name="name" required 
                               class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl py-4 px-6 text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-slate-400"
                               placeholder="John Doe">
                    </div>

                    <div class="space-y-2">
                        <label for="role" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">I am a</label>
                        <select id="role" name="role" required 
                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl py-4 px-6 text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-blue-500/10 transition-all">
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl py-4 px-6 text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-slate-400"
                           placeholder="name@university.edu">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label for="password" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl py-4 px-6 text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-slate-400"
                               placeholder="••••••••">
                    </div>

                    <div class="space-y-2">
                        <label for="confirm_password" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Confirm</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl py-4 px-6 text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-slate-400"
                               placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-500/30 transition-all active:scale-[0.98] mt-6">
                    Create My Account
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
                <p class="text-sm font-medium text-slate-500">
                    Already have an account? 
                    <a href="../../login.php" class="text-blue-600 font-black uppercase tracking-tight hover:underline ml-1">Login here</a>
                </p>
            </div>
        </div>

        <div class="mt-8 flex justify-center">
            <button onclick="toggleTheme()" class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 hover:text-blue-600 transition flex items-center gap-2">
                <i class="fas fa-circle-half-stroke"></i>
                Change Theme
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