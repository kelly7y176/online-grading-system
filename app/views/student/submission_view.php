<?php
$pageTitle = 'View Submission';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="header">
    <h1>Submission Details</h1>
    <a href="<?php echo BASE_URL; ?>/student/submissions.php" class="btn btn-secondary">← Back to Submissions</a>
</div>

<div class="d-flex gap-1" style="flex-wrap: wrap;">
    <!-- Submission Info -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card">
            <div class="card-header">
                <h3><?php echo htmlspecialchars($submission['assignment_title']); ?></h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Submitted</th>
                        <td><?php echo date('M d, Y H:i', strtotime($submission['submitted_at'])); ?></td>
                    </tr>
                    <?php if ($submission['file_path']): ?>
                    <tr>
                        <th>File</th>
                        <td>
                            <a href="<?php echo BASE_URL . '/' . $submission['file_path']; ?>" target="_blank" class="btn btn-sm btn-primary">
                                📎 Download
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <?php if ($submission['text_content']): ?>
                <h4>Your Submission</h4>
                <div style="background: #f5f5f5; padding: 15px; border-radius: 4px; max-height: 300px; overflow-y: auto;">
                    <?php echo nl2br(htmlspecialchars($submission['text_content'])); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Grade & Feedback -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card">
            <div class="card-header">
                <h3>Grade & Feedback</h3>
            </div>
            <div class="card-body">
                <?php if ($submission['grade'] !== null): ?>
                <div class="text-center mb-2">
                    <div class="stat-value" style="font-size: 3rem; color: var(--primary-color);">
                        <?php echo $submission['grade']; ?>
                    </div>
                    <div class="text-muted">out of <?php echo $submission['max_score'] ?? 100; ?></div>
                    <div class="progress mt-1" style="height: 10px;">
                        <div class="progress-bar" style="width: <?php echo ($submission['grade'] / ($submission['max_score'] ?? 100)) * 100; ?>%"></div>
                    </div>
                    <div class="mt-1">
                        <span class="badge badge-<?php 
                            $percentage = ($submission['grade'] / ($submission['max_score'] ?? 100)) * 100;
                            echo $percentage >= 70 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                        ?>">
                            <?php echo number_format($percentage, 1); ?>%
                        </span>
                    </div>
                </div>
                
                <?php if (!empty($rubricGrades)): ?>
                <h4>Rubric Breakdown</h4>
                <?php foreach ($rubricGrades as $grade): ?>
                <div class="card mb-1">
                    <div class="card-body" style="padding: 10px;">
                        <div class="d-flex justify-between">
                            <strong><?php echo htmlspecialchars($grade['criterion_name']); ?></strong>
                            <span><?php echo $grade['points']; ?> / <?php echo $grade['max_points']; ?></span>
                        </div>
                        <?php if ($grade['comment']): ?>
                        <p class="text-muted" style="font-size: 12px; margin: 5px 0 0 0;">
                            <?php echo htmlspecialchars($grade['comment']); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if ($submission['feedback']): ?>
                <h4 class="mt-2">Instructor Feedback</h4>
                <div style="background: #e8f4f8; padding: 15px; border-radius: 4px; border-left: 4px solid var(--primary-color);">
                    <?php echo nl2br(htmlspecialchars($submission['feedback'])); ?>
                </div>
                <?php endif; ?>
                
                <p class="text-muted mt-2" style="font-size: 12px;">
                    Graded on: <?php echo date('M d, Y H:i', strtotime($submission['graded_at'])); ?>
                </p>
                <?php else: ?>
                <div class="text-center" style="padding: 40px;">
                    <h3>⏳ Pending</h3>
                    <p class="text-muted">Your submission is awaiting grading.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
