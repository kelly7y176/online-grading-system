<?php 
$pageTitle = "Reset Password";

// Use an absolute path based on the current file's directory
// From /views/shared/ to the root is 3 levels up
$configPath = dirname(dirname(dirname(__FILE__))) . '/config/config.php';

if (file_exists($configPath)) {
    require_once $configPath;
} else {
    // Emergency fallback to Document Root
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
}
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? 'dark' : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | GradeSys</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { colors: { primary: '#4361ee' } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 min-h-screen flex items-center justify-center p-4 transition-colors duration-300">

<div class="max-w-md w-full space-y-8 bg-white dark:bg-slate-900 p-10 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-xl">
    <div class="text-center">
        <div class="mx-auto h-12 w-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20 mb-6">
            <i class="fas fa-key text-xl"></i>
        </div>
        <h2 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Forgot Password?</h2>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 font-medium text-center">
            Enter your email and we'll send you instructions to reset your password.
        </p>
    </div>

    <form action="../../forgot_password_process.php" method="POST" data-ajax data-validate class="mt-8 space-y-6">
        <div class="space-y-4">
            <div class="space-y-1">
                <label for="email" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-2">Email Address</label>
                <input id="email" name="email" type="email" required 
                       class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all outline-none placeholder:text-slate-400" 
                       placeholder="name@university.edu">
            </div>
        </div>

        <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-blue-500/25 transition-all active:scale-[0.98]">
            Send Reset Link
        </button>

        <div class="text-center">
            <a href="../../login.php" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Login
            </a>
        </div>
    </form>
    <div class="mt-8 flex justify-center">
            <button onclick="toggleTheme()" class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 hover:text-blue-600 transition flex items-center gap-2">
                <i class="fas fa-circle-half-stroke"></i>
                Switch Appearance
            </button>
        </div>
</div>



<script src="../../../assets/js/main.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if(typeof initAjaxSubmissions === 'function') initAjaxSubmissions();
        if(typeof initForms === 'function') initForms();
    });
</script>

</body>
</html>