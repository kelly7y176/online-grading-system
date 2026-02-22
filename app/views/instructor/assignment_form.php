<?php
$isEdit = isset($assignment) && !empty($assignment);
$pageTitle = $isEdit ? 'Edit Assignment' : 'Create Assignment';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="max-w-5xl mx-auto space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-400 mb-1">
                <a href="<?php echo BASE_URL; ?>/instructor/assignments.php" class="hover:text-primary transition">Assignments</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span><?php echo $isEdit ? 'Editor' : 'New'; ?></span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                <?php echo $pageTitle; ?>
            </h1>
        </div>
        <a href="<?php echo BASE_URL; ?>/instructor/assignments.php" class="text-sm font-bold text-slate-500 hover:text-slate-700 transition">
            <i class="fas fa-times mr-1"></i> Cancel
        </a>
    </div>

    <form action="<?php echo BASE_URL; ?>/instructor/<?php echo $isEdit ? 'assignment_update.php?id=' . $assignment['id'] : 'assignment_store.php'; ?>" 
          method="POST" data-validate class="space-y-8">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 p-8 shadow-sm space-y-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h3 class="font-black text-lg text-slate-900 dark:text-white uppercase tracking-tight">General Info</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2 block">Assignment Title</label>
                    <input type="text" name="title" required placeholder="e.g. Final Project: Database Design"
                           class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:bg-white dark:focus:bg-slate-900 outline-none focus:ring-4 focus:ring-blue-500/10 transition font-bold"
                           value="<?php echo $isEdit ? htmlspecialchars($assignment['title']) : ''; ?>">
                </div>

                <div class="md:col-span-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2 block">Instructions / Description</label>
                    <textarea name="description" rows="4" placeholder="What should the students do?"
                              class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:bg-white dark:focus:bg-slate-900 outline-none focus:ring-4 focus:ring-blue-500/10 transition text-sm leading-relaxed"><?php echo $isEdit ? htmlspecialchars($assignment['description']) : ''; ?></textarea>
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2 block">Due Date</label>
                    <input type="date" name="due_date" required
                           class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 outline-none focus:ring-4 focus:ring-blue-500/10 transition font-bold"
                           value="<?php echo $isEdit ? $assignment['due_date'] : ''; ?>">
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2 block">Maximum Score</label>
                    <div class="relative">
                        <input type="number" id="max_score" name="max_score" required min="1"
                               class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 outline-none focus:ring-4 focus:ring-blue-500/10 transition font-black text-blue-600"
                               value="<?php echo $isEdit ? $assignment['max_score'] : '100'; ?>">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">PTS</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900 dark:text-white uppercase tracking-tight">Grading Rubric</h3>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-0.5">Define criteria for automated calculation</p>
                    </div>
                </div>
                <button type="button" id="add-rubric" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-plus"></i> Add Criterion
                </button>
            </div>

            <div id="rubric-container" class="space-y-4 mb-8">
                <?php 
                $defaultRubrics = [
                    ['name' => 'Content Quality', 'desc' => 'Accuracy and depth of content', 'pts' => 40],
                    ['name' => 'Presentation', 'desc' => 'Organization and formatting', 'pts' => 30],
                    ['name' => 'Technical Accuracy', 'desc' => 'Correct implementation', 'pts' => 30]
                ];
                $displayRubrics = ($isEdit && !empty($rubrics)) ? $rubrics : $defaultRubrics;

                foreach ($displayRubrics as $index => $r): 
                    $rName = $r['criterion_name'] ?? $r['name'];
                    $rDesc = $r['description'] ?? $r['desc'];
                    $rPts = $r['max_points'] ?? $r['pts'];
                ?>
                <div class="rubric-item group bg-slate-50 dark:bg-slate-800/40 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 transition hover:border-emerald-500/30">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 rounded-full">Criterion</span>
                        <button type="button" class="remove-rubric w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 hover:bg-rose-50 hover:text-rose-500 transition">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-3">
                            <input type="text" name="rubric[<?php echo $index; ?>][name]" placeholder="Name" required
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-bold"
                                   value="<?php echo htmlspecialchars($rName); ?>">
                        </div>
                        <div>
                            <input type="number" name="rubric[<?php echo $index; ?>][points]" placeholder="Pts" required
                                   class="rubric-points w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-center font-black text-emerald-600"
                                   value="<?php echo $rPts; ?>">
                        </div>
                        <div class="md:col-span-4">
                            <textarea name="rubric[<?php echo $index; ?>][description]" placeholder="Description (Optional)" rows="2"
                                      class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white/50 dark:bg-slate-900 text-xs"><?php echo htmlspecialchars($rDesc); ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="flex items-center justify-between p-6 bg-slate-900 rounded-[1.5rem] text-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calculator text-xs"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest">Total Rubric Points</span>
                </div>
                <div class="text-2xl font-black">
                    <span id="total-points">100</span> <span class="text-[10px] opacity-50 uppercase tracking-widest ml-1">pts</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="flex-1 py-4 bg-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-500/20 hover:bg-blue-700 transition transform active:scale-[0.98]">
                <?php echo $isEdit ? 'Save Changes' : 'Launch Assignment'; ?>
            </button>
            <a href="<?php echo BASE_URL; ?>/instructor/assignments.php" class="px-8 py-4 bg-white dark:bg-slate-900 text-slate-500 font-bold rounded-2xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 transition">
                Discard
            </a>
        </div>
    </form>

    <?php if ($isEdit): ?>
    <div class="bg-rose-50 dark:bg-rose-950/20 rounded-[2.5rem] border border-rose-100 dark:border-rose-900/30 p-8">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-rose-500 text-white rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-rose-500/20">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-black text-rose-900 dark:text-rose-400 uppercase tracking-tight text-lg">Danger Zone</h3>
                <p class="text-sm text-rose-700 dark:text-rose-500/80 mb-6">Deleting this assignment is permanent. It will remove all submissions and grades associated with it.</p>
                <form action="<?php echo BASE_URL; ?>/instructor/assignment_delete.php?id=<?php echo $assignment['id']; ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <button type="submit" data-confirm="Are you sure you want to delete this assignment? All student work will be lost." 
                            class="px-6 py-3 bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-rose-600 transition shadow-lg shadow-rose-500/20">
                        Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
