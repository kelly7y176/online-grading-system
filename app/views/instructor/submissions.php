<?php
$pageTitle = 'Submissions - ' . htmlspecialchars($assignment['title']);
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-400 mb-1">
                <a href="<?php echo BASE_URL; ?>/instructor/assignments.php" class="hover:text-primary transition">Assignments</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span>Submissions</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                <?php echo htmlspecialchars($assignment['title']); ?>
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?php echo BASE_URL; ?>/api/grades/<?php echo $assignment['id']; ?>/export" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-400 hover:bg-slate-50 transition shadow-sm">
                <i class="fas fa-download text-blue-500"></i>
                <span>Export CSV</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/instructor/assignment_view.php?id=<?php echo $assignment['id']; ?>" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-400 hover:bg-slate-50 transition shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Total Received</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white"><?php echo $stats['total_submissions'] ?? 0; ?></span>
                <span class="text-xs font-bold text-slate-400">Files</span>
            </div>
        </div>
        <div class="bg-blue-600 p-6 rounded-[2rem] shadow-lg shadow-blue-500/20 text-white">
            <span class="block text-[10px] font-black uppercase tracking-widest text-white/60 mb-2">Graded</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black"><?php echo $stats['graded_count'] ?? 0; ?></span>
                <span class="text-xs font-bold text-white/80">Completed</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Average Score</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white"><?php echo number_format($stats['average_grade'] ?? 0, 1); ?></span>
                <span class="text-xs font-bold text-slate-400">/ <?php echo $assignment['max_score']; ?></span>
            </div>
        </div>
        <div class="bg-rose-50 dark:bg-rose-900/10 p-6 rounded-[2rem] border border-rose-100 dark:border-rose-900/30">
            <span class="block text-[10px] font-black uppercase tracking-widest text-rose-500 mb-2">Needs Grading</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-rose-600"><?php echo ($stats['total_submissions'] ?? 0) - ($stats['graded_count'] ?? 0); ?></span>
                <span class="text-xs font-bold text-rose-400">Pending</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <?php if (!empty($submissions)): ?>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Student Info</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Submission Date</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Asset</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Score</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($submissions as $submission): 
                        $isGraded = $submission['grade'] !== null;
                    ?>
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center font-black text-slate-500 text-xs">
                                    <?php echo strtoupper(substr($submission['student_name'], 0, 2)); ?>
                                </div>
                                <div>
                                    <span class="block font-black text-slate-900 dark:text-white"><?php echo htmlspecialchars($submission['student_name']); ?></span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tight"><?php echo htmlspecialchars($submission['student_email']); ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="p-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300"><?php echo date('M d, Y', strtotime($submission['submitted_at'])); ?></span>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter"><?php echo date('H:i', strtotime($submission['submitted_at'])); ?></span>
                            </div>
                        </td>
                        <td class="p-6">
                            <?php if ($submission['file_path']): ?>
                                <a href="<?php echo BASE_URL . '/' . $submission['file_path']; ?>" target="_blank" 
                                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-black uppercase tracking-widest transition hover:bg-blue-100">
                                    <i class="fas fa-paperclip"></i> File
                                </a>
                            <?php else: ?>
                                <span class="text-[10px] font-black text-slate-300 uppercase italic">Text Only</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-6 text-center">
                            <?php if ($isGraded): ?>
                                <div class="inline-block px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                    <span class="font-black text-emerald-600"><?php echo $submission['grade']; ?></span>
                                    <span class="text-[10px] font-bold text-emerald-400">/<?php echo $assignment['max_score']; ?></span>
                                </div>
                            <?php else: ?>
                                <span class="text-slate-300 text-xl font-black">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-6">
                            <?php if ($isGraded): ?>
                                <div class="flex items-center gap-1.5 text-emerald-500">
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Graded</span>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center gap-1.5 text-amber-500">
                                    <div class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Pending</span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="p-6 text-right">
                            <a href="<?php echo BASE_URL; ?>/instructor/grade.php?id=<?php echo $submission['id']; ?>" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 <?php echo $isGraded ? 'bg-slate-100 dark:bg-slate-800 text-slate-600' : 'bg-blue-600 text-white shadow-lg shadow-blue-500/20'; ?> rounded-xl text-[10px] font-black uppercase tracking-widest transition transform hover:scale-105 active:scale-95">
                                <?php echo $isGraded ? '<i class="fas fa-eye"></i> Review' : '<i class="fas fa-pen-nib"></i> Grade Now'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="p-20 text-center">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-inbox text-3xl text-slate-300"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">No Submissions Yet</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Waiting for students to upload their work for this assignment.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>