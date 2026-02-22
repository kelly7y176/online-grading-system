<?php
$pageTitle = 'Grade Submission';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-400 mb-1">
                <a href="<?php echo BASE_URL; ?>/instructor/dashboard.php" class="hover:text-primary">Dashboard</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span>Grading Portal</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                Grade Submission
            </h1>
        </div>
        <a href="<?php echo BASE_URL; ?>/instructor/submissions.php?assignment_id=<?php echo $submission['assignment_id']; ?>" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition shadow-sm">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Submissions</span>
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <div class="xl:col-span-2 space-y-6">
            
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-primary/10 text-primary rounded-2xl flex items-center justify-center text-xl font-black">
                            <?php echo strtoupper(substr($submission['student_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h3 class="font-black text-lg text-slate-900 dark:text-white"><?php echo htmlspecialchars($submission['student_name']); ?></h3>
                            <p class="text-sm text-slate-500"><?php echo htmlspecialchars($submission['student_email']); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-1">Submitted On</span>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">
                            <?php echo date('M d, Y • H:i', strtotime($submission['submitted_at'])); ?>
                        </span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-1">Assignment</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200"><?php echo htmlspecialchars($submission['assignment_title']); ?></span>
                    </div>
                    <?php if ($submission['file_path']): ?>
                    <div>
                        <a href="<?php echo BASE_URL . '/' . $submission['file_path']; ?>" target="_blank" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl text-sm font-bold hover:bg-blue-100 transition">
                            <i class="fas fa-file-download"></i>
                            <span>Download Submission</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($submission['text_content']): ?>
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-black text-sm uppercase tracking-widest text-slate-400">Submission Text</h3>
                    <i class="fas fa-align-left text-slate-300"></i>
                </div>
                <div class="p-8 text-slate-700 dark:text-slate-300 leading-relaxed max-h-[600px] overflow-y-auto font-mono text-sm">
                    <?php echo nl2br(htmlspecialchars($submission['text_content'])); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <form action="<?php echo BASE_URL; ?>/instructor/grade_save.php?id=<?php echo $submission['id']; ?>" method="POST" class="sticky top-24">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
                    <div class="p-6 bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-pen-nib text-primary"></i>
                            Grading Panel
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <?php if (!empty($rubrics)): ?>
                        <div class="space-y-4">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Rubric Criteria</h4>
                            <?php foreach ($rubrics as $rubric): ?>
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($rubric['criterion_name']); ?></span>
                                    <span class="text-xs font-bold text-primary">/ <?php echo $rubric['max_points']; ?></span>
                                </div>
                                
                                <input type="number" name="rubric_grades[<?php echo $rubric['id']; ?>][points]" 
                                       class="rubric-grade-input w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 outline-none focus:ring-2 focus:ring-primary font-bold text-sm mb-2" 
                                       min="0" max="<?php echo $rubric['max_points']; ?>" step="0.5"
                                       value="<?php echo isset($gradesMap[$rubric['id']]) ? $gradesMap[$rubric['id']]['points'] : ''; ?>"
                                       onchange="calculateGrade()">
                                
                                <textarea name="rubric_grades[<?php echo $rubric['id']; ?>][comment]" 
                                          class="w-full px-4 py-2 rounded-xl border border-slate-100 dark:border-slate-700 bg-white/50 dark:bg-slate-800 outline-none text-xs" 
                                          rows="1" placeholder="Criterion feedback..."><?php echo isset($gradesMap[$rubric['id']]) ? htmlspecialchars($gradesMap[$rubric['id']]['comment']) : ''; ?></textarea>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex justify-between items-end mb-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Final Score</label><div class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-md text-[10px] font-black text-slate-500 uppercase tracking-widest"> Max: <?php echo $assignment['max_score']; ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                         <div class="relative flex-1">
                         <input type="number" id="final-grade" name="final_grade" 
                   class="w-full px-5 py-4 rounded-2xl bg-white dark:bg-slate-900 border-2 border-primary text-primary text-3xl font-black outline-none focus:ring-4 focus:ring-primary/10 transition" 
                   min="0" max="<?php echo $assignment['max_score']; ?>" step="0.5" required
                   value="<?php echo $submission['grade'] ?? ''; ?>">
                </div>
                <div class="h-16 w-16 bg-primary rounded-2xl flex flex-col items-center justify-center text-white shadow-lg shadow-primary/20">
                    <span class="text-[10px] font-black uppercase tracking-tighter leading-none opacity-80">Score</span><span class="text-xs font-black">PTS</span>
                </div>
            </div>
        </div>

                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Overall Feedback</label>
                            <textarea name="feedback" class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 outline-none focus:ring-2 focus:ring-primary text-sm min-h-[120px]"
                                      placeholder="Provide detailed feedback for the student..."><?php echo htmlspecialchars($submission['feedback'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-slate-900 dark:bg-blue-600 text-white font-black rounded-2xl shadow-lg hover:scale-[1.02] active:scale-[0.98] transition transform">
                            Save Final Grade
                        </button>
                        
                        <?php if ($submission['grade'] !== null): ?>
                        <p class="text-[10px] text-center text-slate-400 font-bold uppercase tracking-tighter">
                            Last Updated: <?php echo date('M d, Y H:i', strtotime($submission['graded_at'])); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/**
 * Simple script to sum up rubric points into the final grade
 */
function calculateGrade() {
    let total = 0;
    document.querySelectorAll('.rubric-grade-input').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    const finalInput = document.getElementById('final-grade');
    const maxScore = <?php echo $assignment['max_score']; ?>;
    
    // Auto-update final grade, but don't exceed max
    finalInput.value = Math.min(total, maxScore);
    
    // Visual feedback if they exceed max
    if (total > maxScore) {
        finalInput.classList.add('bg-rose-500');
        finalInput.classList.remove('bg-primary');
    } else {
        finalInput.classList.remove('bg-rose-500');
        finalInput.classList.add('bg-primary');
    }
}
</script>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>