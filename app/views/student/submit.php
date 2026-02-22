<?php
/**
 * Modern Assignment Submission - GradeSys
 */
require_once dirname(dirname(__DIR__)) . '/config/config.php';
require_once dirname(__DIR__) . '/shared/header.php';

$isResubmit = isset($existingSubmission) && !empty($existingSubmission);
$pageTitle = $isResubmit ? 'Update Submission' : 'Submit Assignment';
?>

<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <a href="<?php echo BASE_URL; ?>/student/assignments.php" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 transition flex items-center gap-2 mb-2">
                <i class="fas fa-arrow-left"></i> Back to Assignments
            </a>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                <?php echo $isResubmit ? 'Update Submission' : 'New Submission'; ?>
            </h1>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-slate-900 p-2 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="pr-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Max Score</p>
                <p class="text-sm font-black text-slate-900 dark:text-white"><?php echo $assignment['max_score']; ?> Points</p>
            </div>
        </div>
    </div>

    <?php if ($isResubmit): ?>
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 p-4 rounded-2xl flex items-center gap-4">
        <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center text-white shrink-0">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-amber-900 dark:text-amber-200">Previous Submission Detected</p>
            <p class="text-xs text-amber-700 dark:text-amber-400 opacity-80 uppercase font-black tracking-tight">Submitting again will replace your current file.</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Assignment Brief</h3>
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-black text-slate-900 dark:text-white mb-2"><?php echo htmlspecialchars($assignment['title']); ?></h4>
                        <p class="text-sm text-slate-500"><?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <form action="<?php echo BASE_URL; ?>/student/submit_process.php?id=<?php echo $assignment['id']; ?>" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  data-ajax 
                  data-validate
                  class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 p-8 shadow-xl shadow-slate-200/50 dark:shadow-none space-y-8">
                
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <div class="space-y-3">
                    <label for="text_content" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Text Submission (Optional)</label>
                    <textarea id="text_content" name="text_content" rows="6" 
                              class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-3xl p-6 text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none"
                              placeholder="Type notes or paste links..."><?php echo $isResubmit ? htmlspecialchars($existingSubmission['text_content']) : ''; ?></textarea>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Upload File</label>
                    <div class="relative group border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-3xl p-10 text-center hover:border-blue-500 transition-colors">
                        <input type="file" id="submission_file" name="submission_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <i class="fas fa-cloud-upload-alt text-3xl text-slate-300 group-hover:text-blue-500 transition-colors mb-4 block"></i>
                        <p class="text-sm font-bold text-slate-600 dark:text-slate-300">
                            Click to upload or drag and drop
                        </p>
                    </div>
                </div>

                <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-700 text-white font-black uppercase tracking-[0.2em] rounded-3xl shadow-xl shadow-blue-500/30 transition-all active:scale-[0.98]">
                    <?php echo $isResubmit ? 'Update Submission' : 'Submit Now'; ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
