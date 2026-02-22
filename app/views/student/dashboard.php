<?php
/**
 * Student Dashboard
 * views/student/dashboard.php
 */
$pageTitle = 'Student Dashboard';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="space-y-10">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="relative">
                <div class="w-16 h-16 bg-blue-600 rounded-[1.5rem] flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-blue-500/30">
                    <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'S', 0, 1)); ?>
                </div>
                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 border-4 border-white dark:border-slate-950 rounded-full"></div>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                        Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Student'); ?>!
                    </h1>
                    <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-[0.15em] rounded-lg border border-blue-100 dark:border-blue-800/50">
                        <?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Student'); ?>
                    </span>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">Here's your academic overview for today.</p>
            </div>
        </div>
        
        <div class="hidden md:block text-right">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Current Session</p>
            <p class="text-sm font-bold text-slate-700 dark:text-slate-300"><?php echo date('l, F jS'); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm transition-transform hover:scale-[1.02]">
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Active Tasks</span>
            <div class="text-3xl font-black text-slate-900 dark:text-white"><?php echo $activeAssignments ?? 0; ?></div>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm transition-transform hover:scale-[1.02]">
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Submitted</span>
            <div class="text-3xl font-black text-slate-900 dark:text-white"><?php echo $submittedCount ?? 0; ?></div>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm transition-transform hover:scale-[1.02]">
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Graded</span>
            <div class="text-3xl font-black text-slate-900 dark:text-white"><?php echo $gradedCount ?? 0; ?></div>
        </div>
        
        <div class="bg-blue-600 p-6 rounded-[2rem] shadow-xl shadow-blue-500/20 transition-transform hover:scale-[1.02]">
            <span class="text-[10px] font-black uppercase tracking-widest text-white/70 block mb-2">Average Grade</span>
            <div class="text-3xl font-black text-white"><?php echo number_format($averageGrade ?? 0, 1); ?>%</div>
        </div>
    </div>

    <div class="mt-12 space-y-6">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Upcoming Assignments</h2>
            <a href="<?php echo BASE_URL; ?>/student/assignments.php" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">View All Tasks</a>
        </div>
        
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50">
                            <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Assignment</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Instructor</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Due Date</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Max Score</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Status</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (!empty($upcomingAssignments)): foreach ($upcomingAssignments as $assignment): 
                            $dueDate = new DateTime($assignment['due_date']);
                            $isUrgent = (new DateTime())->diff($dueDate)->days <= 2;
                        ?>
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <td class="p-6">
                                <span class="block font-black text-slate-900 dark:text-white group-hover:text-blue-600 transition"><?php echo htmlspecialchars($assignment['title']); ?></span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">ID: #<?php echo $assignment['id']; ?></span>
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-[10px] font-bold text-slate-400">
                                        <?php echo strtoupper(substr($assignment['instructor_name'] ?? 'I', 0, 1)); ?>
                                    </div>
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-300">
                                        <?php echo htmlspecialchars($assignment['instructor_name']); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="p-6">
                                <span class="block font-bold text-sm <?php echo $isUrgent ? 'text-rose-500 font-black' : 'text-slate-700 dark:text-slate-200'; ?>">
                                    <?php echo $dueDate->format('M d, Y'); ?>
                                </span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                    <?php echo $dueDate->format('h:i A'); ?>
                                </span>
                            </td>
                            <td class="p-6 text-sm font-black text-slate-900 dark:text-white text-center">
                                <?php echo $assignment['max_score'] ?? 100; ?>
                            </td>
                            <td class="p-6 text-center">
                                <?php if (isset($assignment['submitted']) && $assignment['submitted']): ?>
                                    <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-emerald-100 dark:border-emerald-800">
                                        Submitted
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-400 text-[10px] font-black rounded-full uppercase tracking-widest">
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6 text-right">
                                <?php if (isset($assignment['submitted']) && $assignment['submitted']): ?>
                                    <a href="<?php echo BASE_URL; ?>/student/submission_view.php?id=<?php echo $assignment['submission_id']; ?>" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-200 transition">View</a>
                                <?php else: ?>
                                    <a href="<?php echo BASE_URL; ?>/student/submit.php?id=<?php echo $assignment['id']; ?>" class="px-4 py-2 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-500/20">Submit</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="6" class="p-16 text-center text-slate-400 font-bold uppercase text-xs tracking-widest">No upcoming assignments. Enjoy your day!</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>