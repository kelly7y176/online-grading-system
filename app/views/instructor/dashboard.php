<?php
$pageTitle = 'Dashboard';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="header">
    <h1>Instructor Dashboard</h1>
    <a href="<?php echo BASE_URL; ?>/instructor/assignment_create.php" class="btn btn-primary">+ New Assignment</a>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?php echo $totalAssignments ?? 0; ?></div>
        <div class="stat-label">Total Assignments</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo $pendingSubmissions ?? 0; ?></div>
        <div class="stat-label">Pending Grading</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo $totalStudents ?? 0; ?></div>
        <div class="stat-label">Total Students</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo $gradedToday ?? 0; ?></div>
        <div class="stat-label">Graded Today</div>
    </div>
</div>

<!-- Recent Assignments -->
<div class="card">
    <div class="card-header">
        <h3>Recent Assignments</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($recentAssignments)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Due Date</th>
                    <th>Submissions</th>
                    <th>Graded</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentAssignments as $assignment): ?>
                <tr>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/instructor/assignment_view.php?id=<?php echo $assignment['id']; ?>">
                            <?php echo htmlspecialchars($assignment['title']); ?>
                        </a>
                    </td>
                    <td>
                        <?php 
                        $dueDate = new DateTime($assignment['due_date']);
                        $now = new DateTime();
                        $isPast = $dueDate < $now;
                        ?>
                        <span class="<?php echo $isPast ? 'text-danger' : ''; ?>">
                            <?php echo $dueDate->format('M d, Y'); ?>
                        </span>
                    </td>
                    <td><?php echo $assignment['submission_count']; ?></td>
                    <td>
                        <?php echo $assignment['graded_count']; ?>/<?php echo $assignment['submission_count']; ?>
                        <?php if ($assignment['submission_count'] > 0): ?>
                        <div class="progress" style="height: 5px; margin-top: 5px;">
                            <div class="progress-bar" style="width: <?php echo ($assignment['graded_count'] / $assignment['submission_count']) * 100; ?>%"></div>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/instructor/submissions.php?assignment_id=<?php echo $assignment['id']; ?>" 
                           class="btn btn-sm btn-primary">View Submissions</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-muted text-center">No assignments yet. <a href="<?php echo BASE_URL; ?>/instructor/assignment_create.php">Create your first assignment</a></p>
        <?php endif; ?>
    </div>
</div>

<!-- Pending Grading -->
<div class="card">
    <div class="card-header">
        <h3>Pending Grading</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($ungradedSubmissions)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Assignment</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($ungradedSubmissions, 0, 5) as $submission): ?>
                <tr>
                    <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($submission['assignment_title']); ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($submission['submitted_at'])); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/instructor/grade.php?id=<?php echo $submission['id']; ?>" 
                           class="btn btn-sm btn-success">Grade</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (count($ungradedSubmissions) > 5): ?>
        <p class="text-center mt-2">
            <a href="<?php echo BASE_URL; ?>/instructor/submissions.php?filter=ungraded">View all pending submissions</a>
        </p>
        <?php endif; ?>
        <?php else: ?>
        <p class="text-muted text-center">No submissions pending grading.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
