<?php
$pageTitle = 'Submissions - ' . htmlspecialchars($assignment['title']);
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="header">
    <h1>Submissions: <?php echo htmlspecialchars($assignment['title']); ?></h1>
    <div>
        <a href="<?php echo BASE_URL; ?>/api/grades/<?php echo $assignment['id']; ?>/export" class="btn btn-secondary">📥 Export CSV</a>
        <a href="<?php echo BASE_URL; ?>/instructor/assignment_view.php?id=<?php echo $assignment['id']; ?>" class="btn btn-secondary">← Back to Assignment</a>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?php echo $stats['total_submissions'] ?? 0; ?></div>
        <div class="stat-label">Total Submissions</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo $stats['graded_count'] ?? 0; ?></div>
        <div class="stat-label">Graded</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo number_format($stats['average_grade'] ?? 0, 1); ?></div>
        <div class="stat-label">Average Grade</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo ($stats['total_submissions'] ?? 0) - ($stats['graded_count'] ?? 0); ?></div>
        <div class="stat-label">Pending</div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($submissions)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Submitted</th>
                    <th>File</th>
                    <th>Grade</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($submission['student_name']); ?></strong>
                        <br><small class="text-muted"><?php echo htmlspecialchars($submission['student_email']); ?></small>
                    </td>
                    <td>
                        <?php echo date('M d, Y', strtotime($submission['submitted_at'])); ?>
                        <br><small class="text-muted"><?php echo date('H:i', strtotime($submission['submitted_at'])); ?></small>
                    </td>
                    <td>
                        <?php if ($submission['file_path']): ?>
                        <a href="<?php echo BASE_URL . '/' . $submission['file_path']; ?>" target="_blank" class="btn btn-sm btn-secondary">
                            📎 Download
                        </a>
                        <?php else: ?>
                        <span class="text-muted">Text only</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($submission['grade'] !== null): ?>
                        <strong><?php echo $submission['grade']; ?></strong> / <?php echo $assignment['max_score']; ?>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($submission['grade'] !== null): ?>
                        <span class="badge badge-success">Graded</span>
                        <?php else: ?>
                        <span class="badge badge-warning">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/instructor/grade.php?id=<?php echo $submission['id']; ?>" 
                           class="btn btn-sm btn-<?php echo $submission['grade'] !== null ? 'secondary' : 'primary'; ?>">
                            <?php echo $submission['grade'] !== null ? 'Review' : 'Grade'; ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-center" style="padding: 40px;">
            <h3>No Submissions Yet</h3>
            <p class="text-muted">Students haven't submitted any work for this assignment.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
