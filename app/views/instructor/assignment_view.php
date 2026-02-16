<?php
/**
 * Assignment View Page
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Displays a single assignment with its details and rubric.
 */

$pageTitle = $assignment['title'];
require_once dirname(dirname(__DIR__)) . '/views/shared/header.php';
?>

<div class="page-header">
    <div class="header-content">
        <h1><?php echo htmlspecialchars($assignment['title']); ?></h1>
        <p class="text-muted">Assignment Details</p>
    </div>
    <div class="header-actions">
        <a href="<?php echo BASE_URL; ?>/instructor/assignment_edit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-secondary">
            ✏️ Edit
        </a>
        <a href="<?php echo BASE_URL; ?>/instructor/submissions.php?assignment_id=<?php echo $assignment['id']; ?>" class="btn btn-primary">
            📋 View Submissions
        </a>
    </div>
</div>

<div class="content-grid">
    <!-- Assignment Details Card -->
    <div class="card">
        <div class="card-header">
            <h3>📝 Assignment Details</h3>
        </div>
        <div class="card-body">
            <div class="detail-row">
                <span class="detail-label">Title:</span>
                <span class="detail-value"><?php echo htmlspecialchars($assignment['title']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value"><?php echo nl2br(htmlspecialchars($assignment['description'] ?? 'No description provided.')); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Due Date:</span>
                <span class="detail-value">
                    <?php 
                    $dueDate = new DateTime($assignment['due_date']);
                    $now = new DateTime();
                    $isPast = $dueDate < $now;
                    ?>
                    <span class="<?php echo $isPast ? 'text-danger' : 'text-success'; ?>">
                        <?php echo $dueDate->format('F j, Y \a\t g:i A'); ?>
                        <?php if ($isPast): ?> (Past Due)<?php endif; ?>
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Max Score:</span>
                <span class="detail-value"><?php echo $assignment['max_score']; ?> points</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Created:</span>
                <span class="detail-value"><?php echo date('F j, Y', strtotime($assignment['created_at'])); ?></span>
            </div>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="card">
        <div class="card-header">
            <h3>📊 Statistics</h3>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $assignment['submission_count']; ?></span>
                    <span class="stat-label">Total Submissions</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $assignment['graded_count']; ?></span>
                    <span class="stat-label">Graded</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $assignment['submission_count'] - $assignment['graded_count']; ?></span>
                    <span class="stat-label">Pending</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rubric Section -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>📋 Grading Rubric</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($rubrics)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Criterion</th>
                    <th>Description</th>
                    <th style="width: 120px; text-align: center;">Max Points</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalPoints = 0;
                foreach ($rubrics as $rubric): 
                    $totalPoints += $rubric['max_points'];
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($rubric['criterion_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($rubric['description'] ?? '-'); ?></td>
                    <td style="text-align: center;"><?php echo $rubric['max_points']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>Total Points:</strong></td>
                    <td style="text-align: center;"><strong><?php echo $totalPoints; ?></strong></td>
                </tr>
            </tfoot>
        </table>
        <?php else: ?>
        <p class="text-muted">No rubric criteria defined for this assignment.</p>
        <a href="<?php echo BASE_URL; ?>/instructor/assignment_edit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-secondary">
            Add Rubric Criteria
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Actions Section -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>⚙️ Actions</h3>
    </div>
    <div class="card-body">
        <div class="action-buttons">
            <a href="<?php echo BASE_URL; ?>/instructor/submissions.php?assignment_id=<?php echo $assignment['id']; ?>" class="btn btn-primary">
                📋 View All Submissions
            </a>
            <a href="<?php echo BASE_URL; ?>/instructor/assignment_edit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-secondary">
                ✏️ Edit Assignment
            </a>
            <form method="POST" action="<?php echo BASE_URL; ?>/instructor/assignment_delete.php" style="display: inline;" 
                  onsubmit="return confirm('Are you sure you want to delete this assignment? This action cannot be undone.');">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="id" value="<?php echo $assignment['id']; ?>">
                <button type="submit" class="btn btn-danger">🗑️ Delete Assignment</button>
            </form>
        </div>
    </div>
</div>

<style>
.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.detail-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #666;
    width: 120px;
    flex-shrink: 0;
}

.detail-value {
    flex: 1;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    text-align: center;
}

.stat-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
}

.stat-label {
    display: block;
    font-size: 0.85rem;
    color: #666;
    margin-top: 5px;
}

.text-danger {
    color: #dc3545;
}

.text-success {
    color: #28a745;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<?php require_once dirname(dirname(__DIR__)) . '/views/shared/footer.php'; ?>
