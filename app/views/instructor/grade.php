<?php
$pageTitle = 'Grade Submission';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="header">
    <h1>Grade Submission</h1>
    <a href="<?php echo BASE_URL; ?>/instructor/submissions.php?assignment_id=<?php echo $submission['assignment_id']; ?>" class="btn btn-secondary">← Back to Submissions</a>
</div>

<div class="d-flex gap-1" style="flex-wrap: wrap;">
    <!-- Submission Details -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card">
            <div class="card-header">
                <h3>Submission Details</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Student</th>
                        <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($submission['student_email']); ?></td>
                    </tr>
                    <tr>
                        <th>Assignment</th>
                        <td><?php echo htmlspecialchars($submission['assignment_title']); ?></td>
                    </tr>
                    <tr>
                        <th>Submitted</th>
                        <td><?php echo date('M d, Y H:i', strtotime($submission['submitted_at'])); ?></td>
                    </tr>
                    <?php if ($submission['file_path']): ?>
                    <tr>
                        <th>File</th>
                        <td>
                            <a href="<?php echo BASE_URL . '/' . $submission['file_path']; ?>" target="_blank" class="btn btn-sm btn-primary">
                                📎 Download Submission
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <?php if ($submission['text_content']): ?>
                <h4 class="mt-2">Text Submission</h4>
                <div style="background: #f5f5f5; padding: 15px; border-radius: 4px; max-height: 300px; overflow-y: auto;">
                    <?php echo nl2br(htmlspecialchars($submission['text_content'])); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Grading Form -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card">
            <div class="card-header">
                <h3>Grading</h3>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/instructor/grade_save.php?id=<?php echo $submission['id']; ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <?php if (!empty($rubrics)): ?>
                    <h4>Rubric Grading</h4>
                    <?php foreach ($rubrics as $rubric): ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-between">
                                <strong><?php echo htmlspecialchars($rubric['criterion_name']); ?></strong>
                                <span class="text-muted">Max: <?php echo $rubric['max_points']; ?> pts</span>
                            </div>
                            <?php if ($rubric['description']): ?>
                            <p class="text-muted" style="font-size: 12px;"><?php echo htmlspecialchars($rubric['description']); ?></p>
                            <?php endif; ?>
                            
                            <div class="form-group mb-1">
                                <label>Points</label>
                                <input type="number" name="rubric_grades[<?php echo $rubric['id']; ?>][points]" 
                                       class="form-control rubric-grade-input" 
                                       min="0" max="<?php echo $rubric['max_points']; ?>" step="0.5"
                                       value="<?php echo isset($gradesMap[$rubric['id']]) ? $gradesMap[$rubric['id']]['points'] : ''; ?>"
                                       onchange="calculateGrade()">
                            </div>
                            <div class="form-group">
                                <label>Comment</label>
                                <textarea name="rubric_grades[<?php echo $rubric['id']; ?>][comment]" 
                                          class="form-control" rows="2" 
                                          placeholder="Optional comment for this criterion"><?php echo isset($gradesMap[$rubric['id']]) ? htmlspecialchars($gradesMap[$rubric['id']]['comment']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="final-grade">Final Grade (out of <?php echo $assignment['max_score']; ?>)</label>
                        <input type="number" id="final-grade" name="final_grade" class="form-control" 
                               min="0" max="<?php echo $assignment['max_score']; ?>" step="0.5" required
                               value="<?php echo $submission['grade'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="feedback">Overall Feedback</label>
                        <textarea id="feedback" name="feedback" class="form-control" rows="4"
                                  placeholder="Provide overall feedback for the student"><?php echo htmlspecialchars($submission['feedback'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Grade</button>
                    
                    <?php if ($submission['grade'] !== null): ?>
                    <p class="text-muted mt-1">
                        Last graded: <?php echo date('M d, Y H:i', strtotime($submission['graded_at'])); ?>
                    </p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
