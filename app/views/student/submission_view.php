<?php
/**
 * Modern Submission View - GradeSys
 */

// PATH FIXES:
// 1. To get to config/models (Up 2 levels: student -> views -> root)
require_once dirname(dirname(__DIR__)) . '/config/config.php';
require_once dirname(dirname(__DIR__)) . '/models/Submission.php';
require_once dirname(dirname(__DIR__)) . '/models/RubricGrade.php';

if (!isStudent()) { redirect(BASE_URL . '/login.php'); }

// 2. FETCH DATA (This was missing in your original, causing the blank feedback)
$submissionModel = new Submission();
$rubricGradeModel = new RubricGrade();

$id = $_GET['id'] ?? null;
$submission = $submissionModel->findById($id);

// Security Check
if (!$submission || $submission['student_id'] != $_SESSION['user_id']) {
    die("Submission not found or access denied.");
}

$rubricGrades = $rubricGradeModel->getBySubmission($id);

$pageTitle = 'View Submission';
// 3. To get to shared (Up 1 level: student -> views)
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <a href="<?php echo BASE_URL; ?>/student/grades.php" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 transition flex items-center gap-2 mb-2">
                <i class="fas fa-arrow-left"></i> Back to Grades
            </a>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                <?php echo htmlspecialchars($submission['assignment_title']); ?>
            </h1>
        </div>

        <?php if ($submission['grade'] !== null): ?>
            <div class="px-6 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-4">
                <div class="text-right">
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Final Grade</p>
                    <p class="text-2xl font-black text-emerald-700 dark:text-emerald-400">
                        <?php echo $submission['grade']; ?> <span class="text-sm opacity-50">/ <?php echo $submission['max_score'] ?? 100; ?></span>
                    </p>
                </div>
                <i class="fas fa-award text-2xl text-emerald-500"></i>
            </div>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm p-8">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
                    <i class="fas fa-file-alt"></i> Your Submission
                </h3>
                
                <?php if ($submission['text_content']): ?>
                    <div class="text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 font-medium leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($submission['text_content'])); ?>
                    </div>
                <?php endif; ?>

                <?php if ($submission['file_path']): ?>
                    <div class="mt-6 flex items-center justify-between p-5 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                                <i class="fas fa-file-download"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black text-blue-900 dark:text-blue-300 uppercase tracking-tight">Attached Document</p>
                                <p class="text-[10px] text-blue-500 font-bold uppercase tracking-widest">Submission File</p>
                            </div>
                        </div>
                        <a href="<?php echo BASE_URL . '/' . $submission['file_path']; ?>" target="_blank" class="px-5 py-2.5 bg-blue-600 text-white text-[10px] font-black uppercase rounded-xl hover:bg-blue-700 transition">
                            Download
                        </a>
                    </div>
                <?php endif; ?>

                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <span>Submitted: <?php echo date('M d, Y H:i', strtotime($submission['submitted_at'])); ?></span>
                    <span class="text-blue-600">ID: #SUB-<?php echo $submission['id']; ?></span>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <?php if ($submission['grade'] !== null): ?>
                
                <?php if (!empty($rubricGrades)): ?>
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm p-8">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6 italic">Rubric Scoring</h3>
                    <div class="space-y-4">
                        <?php foreach ($rubricGrades as $rGrade): ?>
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold text-sm text-slate-900 dark:text-white"><?php echo htmlspecialchars($rGrade['criterion_name']); ?></span>
                                    <span class="text-xs font-black text-blue-600"><?php echo $rGrade['points']; ?>/<?php echo $rGrade['max_points']; ?></span>
                                </div>
                                <?php if ($rGrade['comment']): ?>
                                    <p class="text-[11px] text-slate-500 italic mt-2 border-t border-slate-200 dark:border-slate-700 pt-2 leading-relaxed">
                                        "<?php echo htmlspecialchars($rGrade['comment']); ?>"
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($submission['feedback']): ?>
                <div class="bg-blue-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-blue-500/20 relative overflow-hidden">
                    <i class="fas fa-quote-right absolute top-4 right-4 text-white/10 text-4xl"></i>
                    <h3 class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-4 flex items-center gap-2">
                        <i class="fas fa-comment-dots"></i> Instructor Summary
                    </h3>
                    <p class="text-sm font-medium leading-relaxed italic relative z-10">
                        "<?php echo nl2br(htmlspecialchars($submission['feedback'])); ?>"
                    </p>
                    <div class="mt-6 pt-4 border-t border-white/10 text-[9px] font-black uppercase tracking-widest opacity-60">
                        Finalized on <?php echo date('M d, Y', strtotime($submission['graded_at'])); ?>
                    </div>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-dashed border-slate-300 dark:border-slate-700 p-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <i class="fas fa-hourglass-half text-2xl"></i>
                    </div>
                    <h3 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em]">Pending Review</h3>
                    <p class="text-xs text-slate-500 mt-3 leading-relaxed">Your submission is in the queue. You'll see your feedback here once the instructor finishes grading.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>