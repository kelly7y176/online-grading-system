<?php
/**
 * Students List Page
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/User.php';

if (!isInstructor()) {
    redirect(BASE_URL . '/login.php');
}

$userModel = new User();
$students = $userModel->getAllStudents();

$pageTitle = 'Students';
// Use the 'views' path here
require_once dirname(__DIR__) . '/views/shared/header.php'; 
?>

<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase">Student Roster</h1>
            <p class="text-slate-500 dark:text-slate-400">View and manage students currently enrolled in the system.</p>
        </div>
        <div class="bg-white dark:bg-slate-900 px-6 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block">Total Enrollment</span>
            <span class="text-2xl font-black text-blue-600"><?php echo count($students); ?> <span class="text-xs text-slate-400 uppercase">Students</span></span>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <?php if (!empty($students)): ?>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Student Profile</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Contact Email</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Registration Date</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($students as $student): ?>
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="p-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-lg shadow-blue-500/20">
                                    <?php 
                                        $initials = explode(' ', $student['name']);
                                        echo strtoupper(substr($initials[0], 0, 1) . (isset($initials[1]) ? substr($initials[1], 0, 1) : ''));
                                    ?>
                                </div>
                                <div>
                                    <span class="block font-black text-slate-900 dark:text-white text-lg leading-none mb-1">
                                        <?php echo htmlspecialchars($student['name']); ?>
                                    </span>
                                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded">Student</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-6">
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <i class="far fa-envelope text-xs opacity-50"></i>
                                <span class="font-bold text-sm"><?php echo htmlspecialchars($student['email']); ?></span>
                            </div>
                        </td>
                        <td class="p-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    <?php echo date('M d, Y', strtotime($student['created_at'])); ?>
                                </span>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Joined System</span>
                            </div>
                        </td>
                        <td class="p-6 text-right">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-full text-[10px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="p-20 text-center">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-users text-3xl text-slate-300"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">No Students Found</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">When students register for an account, they will automatically appear here in your roster.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
// Fix the footer path to include /views/
require_once dirname(__DIR__) . '/views/shared/footer.php'; 
?>