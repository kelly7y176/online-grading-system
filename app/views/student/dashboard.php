<?php
$pageTitle = 'Dashboard';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="header">
    <h1>Student Dashboard</h1>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?php echo $activeAssignments ?? 0; ?></div>
        <div class="stat-label">Active Assignments</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo $submittedCount ?? 0; ?></div>
        <div class="stat-label">Submitted</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo $gradedCount ?? 0; ?></div>
        <div class="stat-label">Graded</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo number_format($averageGrade ?? 0, 1); ?>%</div>
        <div class="stat-label">Average Grade</div>
    </div>
</div>

<!-- Upcoming Assignments -->
<div class="card">
    <div class="card-header">
        <h3>Upcoming Assignments</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($upcomingAssignments)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Assignment</th>
                    <th>Instructor</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcomingAssignments as $assignment): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($assignment['title']); ?></strong>
                        <?php if (!empty($assignment['description'])): ?>
                        <br><small class="text-muted"><?php echo substr(htmlspecialchars($assignment['description']), 0, 60); ?>...</small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($assignment['instructor_name']); ?></td>
                    <td>
                        <?php 
                        $dueDate = new DateTime($assignment['due_date']);
                        $now = new DateTime();
                        $diff = $now->diff($dueDate);
                        $daysLeft = $dueDate > $now ? $diff->days : -$diff->days;
                        ?>
                        <span class="badge badge-<?php echo $daysLeft <= 2 ? 'danger' : ($daysLeft <= 7 ? 'warning' : 'primary'); ?>">
                            <?php echo $dueDate->format('M d, Y'); ?>
                        </span>
                        <?php if ($daysLeft > 0): ?>
                        <br><small class="text-muted"><?php echo $daysLeft; ?> days left</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (isset($assignment['submitted']) && $assignment['submitted']): ?>
                        <span class="badge badge-success">Submitted</span>
                        <?php else: ?>
                        <span class="badge badge-secondary">Not Submitted</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (isset($assignment['submitted']) && $assignment['submitted']): ?>
                        <a href="<?php echo BASE_URL; ?>/student/submission_view.php?id=<?php echo $assignment['submission_id']; ?>" class="btn btn-sm btn-secondary">View</a>
                        <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/student/submit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary">Submit</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-muted text-center">No upcoming assignments.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Grades -->
<div class="card">
    <div class="card-header">
        <h3>Recent Grades</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($recentGrades)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Assignment</th>
                    <th>Grade</th>
                    <th>Graded On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($recentGrades, 0, 5) as $grade): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grade['assignment_title']); ?></td>
                    <td>
                        <strong><?php echo $grade['grade']; ?></strong> / <?php echo $grade['max_score']; ?>
                        <span class="text-muted">(<?php echo number_format(($grade['grade'] / $grade['max_score']) * 100, 1); ?>%)</span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($grade['graded_at'])); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/student/submission_view.php?id=<?php echo $grade['id']; ?>" class="btn btn-sm btn-secondary">View Feedback</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="text-center">
            <a href="<?php echo BASE_URL; ?>/student/grades.php">View all grades →</a>
        </p>
        <?php else: ?>
        <p class="text-muted text-center">No grades yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
