<?php
$pageTitle = 'My Submissions';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="header">
    <h1>My Submissions</h1>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($submissions)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Assignment</th>
                    <th>Submitted</th>
                    <th>Due Date</th>
                    <th>Grade</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($submission['assignment_title']); ?></strong>
                    </td>
                    <td>
                        <?php echo date('M d, Y', strtotime($submission['submitted_at'])); ?>
                        <br><small class="text-muted"><?php echo date('H:i', strtotime($submission['submitted_at'])); ?></small>
                    </td>
                    <td>
                        <?php 
                        $dueDate = new DateTime($submission['due_date']);
                        $submittedDate = new DateTime($submission['submitted_at']);
                        $isLate = $submittedDate > $dueDate;
                        ?>
                        <?php echo $dueDate->format('M d, Y'); ?>
                        <?php if ($isLate): ?>
                        <br><span class="badge badge-danger">Late</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($submission['grade'] !== null): ?>
                        <strong><?php echo $submission['grade']; ?></strong> / <?php echo $submission['max_score']; ?>
                        <br><small class="text-muted"><?php echo number_format(($submission['grade'] / $submission['max_score']) * 100, 1); ?>%</small>
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
                        <a href="<?php echo BASE_URL; ?>/student/submission_view.php?id=<?php echo $submission['id']; ?>" 
                           class="btn btn-sm btn-secondary">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-center" style="padding: 40px;">
            <h3>No Submissions Yet</h3>
            <p class="text-muted">You haven't submitted any assignments yet.</p>
            <a href="<?php echo BASE_URL; ?>/student/assignments.php" class="btn btn-primary">View Assignments</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
