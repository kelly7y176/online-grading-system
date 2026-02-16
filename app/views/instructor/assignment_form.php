<?php
$isEdit = isset($assignment) && !empty($assignment);
$pageTitle = $isEdit ? 'Edit Assignment' : 'Create Assignment';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="header">
    <h1><?php echo $pageTitle; ?></h1>
    <a href="<?php echo BASE_URL; ?>/instructor/assignments.php" class="btn btn-secondary">← Back to Assignments</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/instructor/<?php echo $isEdit ? 'assignment_update.php?id=' . $assignment['id'] : 'assignment_store.php'; ?>" 
              method="POST" data-validate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="title">Assignment Title *</label>
                <input type="text" id="title" name="title" class="form-control" required
                       value="<?php echo $isEdit ? htmlspecialchars($assignment['title']) : ''; ?>"
                       placeholder="Enter assignment title">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"
                          placeholder="Enter assignment description and instructions"><?php echo $isEdit ? htmlspecialchars($assignment['description']) : ''; ?></textarea>
            </div>
            
            <div class="d-flex gap-1">
                <div class="form-group" style="flex: 1;">
                    <label for="due_date">Due Date *</label>
                    <input type="date" id="due_date" name="due_date" class="form-control" required
                           value="<?php echo $isEdit ? $assignment['due_date'] : ''; ?>">
                </div>
                
                <div class="form-group" style="flex: 1;">
                    <label for="max_score">Maximum Score *</label>
                    <input type="number" id="max_score" name="max_score" class="form-control" required
                           value="<?php echo $isEdit ? $assignment['max_score'] : '100'; ?>" min="1">
                </div>
            </div>
            
            <!-- Rubric Section -->
            <div class="card mt-2">
                <div class="card-header d-flex justify-between align-center">
                    <h3>Grading Rubric</h3>
                    <button type="button" id="add-rubric" class="btn btn-sm btn-success">+ Add Criterion</button>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Define the criteria that will be used to grade submissions.</p>
                    
                    <div id="rubric-container">
                        <?php if ($isEdit && !empty($rubrics)): ?>
                            <?php foreach ($rubrics as $index => $rubric): ?>
                            <div class="rubric-item card mb-2">
                                <div class="card-body">
                                    <div class="d-flex justify-between align-center mb-1">
                                        <strong>Criterion <?php echo $index + 1; ?></strong>
                                        <button type="button" class="btn btn-sm btn-danger remove-rubric">Remove</button>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="rubric[<?php echo $index; ?>][name]" class="form-control" 
                                               placeholder="Criterion name" required
                                               value="<?php echo htmlspecialchars($rubric['criterion_name']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="rubric[<?php echo $index; ?>][description]" class="form-control" 
                                                  placeholder="Description (optional)" rows="2"><?php echo htmlspecialchars($rubric['description']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="number" name="rubric[<?php echo $index; ?>][points]" 
                                               class="form-control rubric-points" placeholder="Max points" min="0" required
                                               value="<?php echo $rubric['max_points']; ?>">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Default rubric items for new assignments -->
                            <div class="rubric-item card mb-2">
                                <div class="card-body">
                                    <div class="d-flex justify-between align-center mb-1">
                                        <strong>Criterion 1</strong>
                                        <button type="button" class="btn btn-sm btn-danger remove-rubric">Remove</button>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="rubric[0][name]" class="form-control" 
                                               placeholder="Criterion name" value="Content Quality">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="rubric[0][description]" class="form-control" 
                                                  placeholder="Description (optional)" rows="2">Accuracy and depth of content</textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="number" name="rubric[0][points]" class="form-control rubric-points" 
                                               placeholder="Max points" min="0" value="40">
                                    </div>
                                </div>
                            </div>
                            <div class="rubric-item card mb-2">
                                <div class="card-body">
                                    <div class="d-flex justify-between align-center mb-1">
                                        <strong>Criterion 2</strong>
                                        <button type="button" class="btn btn-sm btn-danger remove-rubric">Remove</button>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="rubric[1][name]" class="form-control" 
                                               placeholder="Criterion name" value="Presentation">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="rubric[1][description]" class="form-control" 
                                                  placeholder="Description (optional)" rows="2">Organization, formatting, and clarity</textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="number" name="rubric[1][points]" class="form-control rubric-points" 
                                               placeholder="Max points" min="0" value="30">
                                    </div>
                                </div>
                            </div>
                            <div class="rubric-item card mb-2">
                                <div class="card-body">
                                    <div class="d-flex justify-between align-center mb-1">
                                        <strong>Criterion 3</strong>
                                        <button type="button" class="btn btn-sm btn-danger remove-rubric">Remove</button>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="rubric[2][name]" class="form-control" 
                                               placeholder="Criterion name" value="Technical Accuracy">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="rubric[2][description]" class="form-control" 
                                                  placeholder="Description (optional)" rows="2">Correct implementation and functionality</textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="number" name="rubric[2][points]" class="form-control rubric-points" 
                                               placeholder="Max points" min="0" value="30">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-2">
                        <strong>Total Points: <span id="total-points">100</span></strong>
                    </div>
                </div>
            </div>
            
            <div class="mt-2">
                <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Assignment' : 'Create Assignment'; ?></button>
                <a href="<?php echo BASE_URL; ?>/instructor/assignments.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php if ($isEdit): ?>
<div class="card mt-2">
    <div class="card-header">
        <h3 class="text-danger">Danger Zone</h3>
    </div>
    <div class="card-body">
        <p>Deleting this assignment will also delete all associated submissions and grades. This action cannot be undone.</p>
        <form action="<?php echo BASE_URL; ?>/instructor/assignment_delete.php?id=<?php echo $assignment['id']; ?>" method="POST" style="display: inline;">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <button type="submit" class="btn btn-danger" data-confirm="Are you sure you want to delete this assignment? This action cannot be undone.">
                Delete Assignment
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
