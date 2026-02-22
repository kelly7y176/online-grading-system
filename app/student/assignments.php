<?php
/**
 * Student Assignments List - Modernized for GradeSys
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/Submission.php';

if (!isStudent()) {
    redirect(BASE_URL . '/login.php');
}

$assignmentModel = new Assignment();
$submissionModel = new Submission();

$assignments = $assignmentModel->getAll();
$mySubmissions = $submissionModel->getByStudent($_SESSION['user_id']);

$submissionMap = [];
foreach ($mySubmissions as $sub) {
    $submissionMap[$sub['assignment_id']] = $sub;
}

$pageTitle = 'Assignments';
require_once dirname(__DIR__) . '/views/shared/header.php';
?>

<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Available Assignments</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Review your tasks and track your submission status.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Assignment</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Instructor</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Due Date</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Max Score</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (!empty($assignments)): foreach ($assignments as $assignment): 
                        $hasSubmission = isset($submissionMap[$assignment['id']]);
                        $submission = $hasSubmission ? $submissionMap[$assignment['id']] : null;
                        $dueDate = new DateTime($assignment['due_date']);
                        $isPast = $dueDate < new DateTime();
                    ?>
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4">
                            <span class="block font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($assignment['title']); ?></span>
                            <span class="text-[10px] text-slate-400 uppercase font-black truncate max-w-[150px] block"><?php echo htmlspecialchars(substr($assignment['description'], 0, 50)); ?>...</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-600 dark:text-slate-400">
                            <?php echo htmlspecialchars($assignment['instructor_name']); ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?php echo $isPast ? 'bg-red-50 text-red-600 dark:bg-red-900/20' : 'bg-blue-50 text-blue-600 dark:bg-blue-900/20'; ?>">
                                <?php echo $dueDate->format('M d, Y'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-bold text-slate-500">
                            <?php echo $assignment['max_score']; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($hasSubmission): ?>
                                <?php if ($submission['grade'] !== null): ?>
                                    <span class="text-[10px] font-black uppercase text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1 rounded-md border border-emerald-100 dark:border-emerald-800">Graded: <?php echo $submission['grade']; ?></span>
                                <?php else: ?>
                                    <span class="text-[10px] font-black uppercase text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-1 rounded-md border border-amber-100 dark:border-amber-800">Submitted</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-[10px] font-black uppercase text-slate-400 bg-slate-50 dark:bg-slate-800 px-2 py-1 rounded-md">Not Submitted</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <?php if ($hasSubmission): ?>
                                    <a href="<?php echo BASE_URL; ?>/student/submission_view.php?id=<?php echo $submission['id']; ?>" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase rounded-lg hover:bg-slate-200 transition">View</a>
                                    <?php if (!$isPast): ?>
                                        <a href="<?php echo BASE_URL; ?>/student/submit.php?id=<?php echo $assignment['id']; ?>" class="px-3 py-1.5 bg-blue-600 text-white text-[10px] font-black uppercase rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition">Resubmit</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if (!$isPast): ?>
                                        <a href="<?php echo BASE_URL; ?>/student/submit.php?id=<?php echo $assignment['id']; ?>" class="px-3 py-1.5 bg-blue-600 text-white text-[10px] font-black uppercase rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition">Submit</a>
                                    <?php else: ?>
                                        <span class="text-[10px] font-black text-red-500 uppercase italic">Past Due</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400 uppercase text-xs font-black">No assignments available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/views/shared/footer.php'; ?>
