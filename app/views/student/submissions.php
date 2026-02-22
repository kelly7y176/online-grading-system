<?php
$pageTitle = 'My Submissions';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="space-y-10">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                My Submissions
            </h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">
                Track your progress and review instructor feedback.
            </p>
        </div>

        <div class="flex items-center gap-3 px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-800">
            <i class="fas fa-check-double text-emerald-600"></i>
            <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">
                <?php echo count($submissions ?? []); ?> Submissions
            </span>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <?php if (!empty($submissions)): ?>
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Assignment</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Submitted Date</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Due Date</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Grade</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($submissions as $submission): 
                        $dueDate = new DateTime($submission['due_date']);
                        $submittedDate = new DateTime($submission['submitted_at']);
                        $isLate = $submittedDate > $dueDate;
                    ?>
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="p-6">
                            <span class="block font-black text-slate-900 dark:text-white"><?php echo htmlspecialchars($submission['assignment_title']); ?></span>
                        </td>
                        
                        <td class="p-6">
                            <span class="block font-bold text-sm text-slate-700 dark:text-slate-200">
                                <?php echo $submittedDate->format('M d, Y'); ?>
                            </span>
                            <span class="text-[10px] uppercase font-black text-slate-400 tracking-tighter">
                                at <?php echo $submittedDate->format('H:i'); ?>
                            </span>
                        </td>

                        <td class="p-6">
                            <span class="block text-sm font-medium text-slate-500">
                                <?php echo $dueDate->format('M d, Y'); ?>
                            </span>
                            <?php if ($isLate): ?>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-rose-50 dark:bg-rose-900/20 text-rose-500 text-[9px] font-black uppercase rounded-md border border-rose-100 dark:border-rose-800">Late</span>
                            <?php endif; ?>
                        </td>

                        <td class="p-6">
                            <?php if ($submission['grade'] !== null): ?>
                                <div class="flex flex-col">
                                    <span class="font-black text-slate-900 dark:text-white">
                                        <?php echo $submission['grade']; ?> <span class="text-slate-400 text-xs">/ <?php echo $submission['max_score']; ?></span>
                                    </span>
                                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">
                                        <?php echo number_format(($submission['grade'] / $submission['max_score']) * 100, 1); ?>%
                                    </span>
                                </div>
                            <?php else: ?>
                                <span class="text-slate-300 dark:text-slate-700 font-black">--</span>
                            <?php endif; ?>
                        </td>

                        <td class="p-6">
                            <?php if ($submission['grade'] !== null): ?>
                                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-emerald-100 dark:border-emerald-800">Graded</span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-amber-100 dark:border-amber-800">Pending</span>
                            <?php endif; ?>
                        </td>

                        <td class="p-6 text-right">
                            <a href="<?php echo BASE_URL; ?>/student/submission_view.php?id=<?php echo $submission['id']; ?>" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm">
                                Details
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="p-20 text-center">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-upload text-slate-300 text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">No Submissions Yet</h3>
                <p class="text-slate-500 dark:text-slate-400 font-medium mb-8">You haven't uploaded any assignments yet.</p>
                <a href="<?php echo BASE_URL; ?>/student/assignments.php" 
                   class="inline-flex items-center gap-3 px-6 py-4 bg-blue-600 text-white rounded-2xl font-black shadow-xl shadow-blue-500/20 hover:scale-[1.02] transition transform active:scale-95 text-xs uppercase tracking-widest">
                    View Assignments
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>