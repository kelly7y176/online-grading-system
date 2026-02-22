<?php
/**
 * My Grades Page - Modernized for GradeSys
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/Submission.php';

if (!isStudent()) {
    redirect(BASE_URL . '/login.php');
}

$submissionModel = new Submission();
$submissions = $submissionModel->getByStudent($_SESSION['user_id']);

// Filter to only graded submissions
$grades = array_filter($submissions, function($s) {
    return $s['grade'] !== null;
});

// Calculate overall stats
$totalGrades = count($grades);
$totalPoints = 0;
$totalMaxPoints = 0;
foreach ($grades as $grade) {
    $totalPoints += $grade['grade'];
    $totalMaxPoints += $grade['max_score'];
}
$overallPercentage = $totalMaxPoints > 0 ? ($totalPoints / $totalMaxPoints) * 100 : 0;

$pageTitle = 'My Grades';
require_once dirname(__DIR__) . '/views/shared/header.php';
?>

<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Academic Performance</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Review your grades and instructor feedback.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Graded Assignments</span>
            <div class="text-3xl font-black text-slate-900 dark:text-white"><?php echo $totalGrades; ?></div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Total Points Earned</span>
            <div class="text-3xl font-black text-slate-900 dark:text-white"><?php echo number_format($totalPoints, 1); ?></div>
        </div>
        <div class="bg-blue-600 p-6 rounded-[2rem] shadow-xl shadow-blue-500/20">
            <span class="text-[10px] font-black uppercase tracking-widest text-white/70 block mb-2">Overall Average</span>
            <div class="text-3xl font-black text-white"><?php echo number_format($overallPercentage, 1); ?>%</div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Assignment</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Score</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Percentage</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Graded On</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (!empty($grades)): foreach ($grades as $grade): 
                        $percentage = ($grade['grade'] / $grade['max_score']) * 100;
                        $colorClass = $percentage >= 80 ? 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : ($percentage >= 60 ? 'text-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'text-rose-500 bg-rose-50 dark:bg-rose-900/20');
                    ?>
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="px-6 py-4">
                            <span class="block font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($grade['assignment_title']); ?></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-black text-slate-700 dark:text-slate-200"><?php echo $grade['grade']; ?></span>
                            <span class="text-slate-400 text-xs">/ <?php echo $grade['max_score']; ?></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?php echo $colorClass; ?>">
                                <?php echo number_format($percentage, 1); ?>%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium text-slate-500">
                            <?php echo date('M d, Y', strtotime($grade['graded_at'])); ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="<?php echo BASE_URL; ?>/student/submission_view.php?id=<?php echo $grade['id']; ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase rounded-xl hover:bg-blue-600 hover:text-white transition group">
                                View Feedback
                                <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <i class="fas fa-chart-bar text-4xl text-slate-200"></i>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">No Grades Yet</h3>
                                <p class="text-slate-500 text-sm">Your submissions haven't been graded by the instructor yet.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/views/shared/footer.php'; ?>
