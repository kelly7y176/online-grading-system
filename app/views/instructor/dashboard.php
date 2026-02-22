<?php 
$pageTitle = 'Dashboard';
require_once dirname(__DIR__) . '/shared/header.php'; 
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div class="flex items-center gap-5">
        <div class="relative">
            <div class="w-16 h-16 bg-blue-600 rounded-[1.5rem] flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-blue-500/30">
                <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'I', 0, 1)); ?>
            </div>
            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 border-4 border-white dark:border-slate-950 rounded-full"></div>
        </div>
        
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                    Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Instructor'); ?>!
                </h1>
                <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-100 dark:border-blue-800">
                    <i class="fas fa-chalkboard-teacher mr-1"></i> Instructor
                </span>
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-medium">
                Here’s what’s happening with your classes today.
            </p>
        </div>
    </div>

    <a href="<?php echo BASE_URL; ?>/instructor/assignment_create.php" 
       class="inline-flex items-center gap-3 px-6 py-4 bg-slate-900 dark:bg-white dark:text-slate-900 text-white rounded-2xl font-black shadow-xl hover:scale-[1.02] transition transform active:scale-95 text-sm uppercase tracking-widest">
        <i class="fas fa-plus"></i>
        <span>New Assignment</span>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Total Assignments</p>
        <h2 class="text-4xl font-black text-slate-900 dark:text-white"><?php echo $totalAssignments ?? 0; ?></h2>
    </div>

    <div class="bg-blue-600 text-white p-6 rounded-[2rem] shadow-xl shadow-blue-500/20 relative overflow-hidden group">
        <p class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-1">Pending Grading</p>
        <h2 class="text-4xl font-black"><?php echo $pendingSubmissions ?? 0; ?></h2>
        <div class="mt-4 flex items-center text-[10px] font-bold uppercase tracking-widest">
            <i class="fas fa-clock mr-1"></i> Action Required
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Total Students</p>
        <h2 class="text-4xl font-black text-slate-900 dark:text-white"><?php echo $totalStudents ?? 0; ?></h2>
    </div>

    <div class="bg-emerald-500 text-white p-6 rounded-[2rem] shadow-xl shadow-emerald-500/20 relative overflow-hidden group">
        <p class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-1">Graded Today</p>
        <h2 class="text-4xl font-black"><?php echo $gradedToday ?? 0; ?></h2>
    </div>
</div>

<div class="mt-12 grid grid-cols-1 xl:grid-cols-2 gap-8">
    
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Recent Assignments</h2>
            <a href="<?php echo BASE_URL; ?>/instructor/assignments.php" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline"> 
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50">
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Assignment</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Progress</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (!empty($recentAssignments)): foreach (array_slice($recentAssignments, 0, 5) as $assignment): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <td class="p-5">
                                <div class="font-bold text-slate-800 dark:text-slate-200"><?php echo htmlspecialchars($assignment['title']); ?></div>
                                <div class="text-[9px] text-slate-400 uppercase font-black tracking-widest mt-1">
                                    Due: <?php echo date('M d', strtotime($assignment['due_date'])); ?>
                                </div>
                            </td>
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <?php $pct = ($assignment['submission_count'] > 0) ? ($assignment['graded_count'] / $assignment['submission_count']) * 100 : 0; ?>
                                        <div class="h-full bg-blue-600 rounded-full" style="width: <?php echo $pct; ?>%"></div>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-500"><?php echo $assignment['graded_count']; ?>/<?php echo $assignment['submission_count']; ?></span>
                                </div>
                            </td>
                            <td class="p-5 text-right">
                                <a href="<?php echo BASE_URL; ?>/instructor/submissions.php?assignment_id=<?php echo $assignment['id']; ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-blue-600 hover:text-white transition shadow-sm">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3" class="p-10 text-center text-slate-400 italic text-sm">No assignments created yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Pending Submissions</h2>
            <span class="px-3 py-1 bg-rose-50 dark:bg-rose-900/20 text-rose-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-rose-100 dark:border-rose-800">
                Action Required
            </span>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50">
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Student</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Submitted</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (!empty($ungradedSubmissions)): foreach (array_slice($ungradedSubmissions, 0, 5) as $submission): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <td class="p-5">
                                <div class="font-bold text-slate-800 dark:text-slate-200"><?php echo htmlspecialchars($submission['student_name']); ?></div>
                                <div class="text-[9px] text-blue-600 uppercase font-black tracking-widest mt-1">
                                    <?php echo htmlspecialchars($submission['assignment_title']); ?>
                                </div>
                            </td>
                            <td class="p-5 text-[11px] text-slate-500 font-bold uppercase">
                                <?php echo date('M d, H:i', strtotime($submission['submitted_at'])); ?>
                            </td>
                            <td class="p-5 text-right">
                                <a href="<?php echo BASE_URL; ?>/instructor/grade.php?id=<?php echo $submission['id']; ?>" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition shadow-lg shadow-emerald-500/20">
                                    Grade
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3" class="p-10 text-center text-slate-400 italic text-sm">All caught up! No pending submissions.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($ungradedSubmissions ?? []) > 5): ?>
            <div class="p-4 bg-slate-50 dark:bg-slate-800/20 text-center border-t border-slate-100 dark:border-slate-800">
                <a href="<?php echo BASE_URL; ?>/instructor/submissions.php?filter=ungraded" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 transition">
                    View all <?php echo count($ungradedSubmissions); ?> pending <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>