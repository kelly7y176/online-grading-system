<?php
$pageTitle = 'My Assignments';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase">My Assignments</h1>
            <p class="text-slate-500 dark:text-slate-400">Manage, track, and grade your course assignments.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/instructor/assignment_create.php" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition transform active:scale-95">
            <i class="fas fa-plus"></i>
            <span>Create Assignment</span>
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <?php if (!empty($assignments)): ?>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Assignment Details</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Due Date</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Stats</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Grading Progress</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($assignments as $assignment): 
                        $dueDate = new DateTime($assignment['due_date']);
                        $now = new DateTime();
                        $isPast = $dueDate < $now;
                        $progress = $assignment['submission_count'] > 0 
                            ? ($assignment['graded_count'] / $assignment['submission_count']) * 100 
                            : 0;
                    ?>
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="p-6">
                            <div class="flex flex-col">
                                <a href="<?php echo BASE_URL; ?>/instructor/assignment_view.php?id=<?php echo $assignment['id']; ?>" 
                                   class="font-black text-slate-900 dark:text-white hover:text-blue-600 transition">
                                    <?php echo htmlspecialchars($assignment['title']); ?>
                                </a>
                                <?php if (!empty($assignment['description'])): ?>
                                <p class="text-xs text-slate-400 mt-1 line-clamp-1">
                                    <?php echo htmlspecialchars(substr($assignment['description'], 0, 60)); ?>...
                                </p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="p-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold <?php echo $isPast ? 'text-rose-500' : 'text-slate-700 dark:text-slate-300'; ?>">
                                    <?php echo $dueDate->format('M d, Y'); ?>
                                </span>
                                <span class="text-[10px] font-black uppercase tracking-tighter <?php echo $isPast ? 'text-rose-400' : 'text-slate-400'; ?>">
                                    <?php echo $isPast ? 'Deadline Passed' : 'Active'; ?>
                                </span>
                            </div>
                        </td>
                        <td class="p-6">
                            <div class="flex items-center justify-center gap-4">
                                <div class="text-center">
                                    <span class="block text-sm font-black text-slate-900 dark:text-white"><?php echo $assignment['max_score']; ?></span>
                                    <span class="text-[9px] font-black uppercase text-slate-400">Points</span>
                                </div>
                                <div class="text-center">
                                    <span class="block text-sm font-black text-blue-600"><?php echo $assignment['submission_count']; ?></span>
                                    <span class="text-[9px] font-black uppercase text-slate-400">Subs</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-6">
                            <div class="w-full max-w-[140px]">
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-[10px] font-black text-slate-500 uppercase"><?php echo $assignment['graded_count']; ?>/<?php echo $assignment['submission_count']; ?> Graded</span>
                                    <span class="text-[10px] font-black text-blue-600"><?php echo round($progress); ?>%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-600 rounded-full transition-all duration-500" style="width: <?php echo $progress; ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?php echo BASE_URL; ?>/instructor/submissions.php?assignment_id=<?php echo $assignment['id']; ?>" 
                                   class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-blue-600 hover:text-white text-slate-600 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-xl transition">
                                    Submissions
                                </a>
                                <a href="<?php echo BASE_URL; ?>/instructor/assignment_edit.php?id=<?php echo $assignment['id']; ?>" 
                                   class="p-2 text-slate-400 hover:text-blue-600 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="p-20 text-center">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-clipboard-list text-3xl text-slate-300"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">No Assignments Yet</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-8 max-w-sm mx-auto">Create your first assignment to start accepting submissions from your students.</p>
                <a href="<?php echo BASE_URL; ?>/instructor/assignment_create.php" class="inline-flex items-center gap-2 px-8 py-4 bg-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-500/20 hover:bg-blue-700 transition">
                    Get Started Now
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
