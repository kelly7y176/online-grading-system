<?php
/**
 * My Grades Page
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/Submission.php';

if (!isStudent()) {
    redirect(BASE_URL . '/login.php');
}

$submissionModel = new Submission();
$submissions = $submissionModel->getByStudent($_SESSION['user_id']);

// Filter to only graded submissions
$grades = array_filter($submissions, function($s) {
    return $s['grade'] !== null;
});

// Calculate overall stats
$totalGrades = count($grades);
$totalPoints = 0;
$totalMaxPoints = 0;
foreach ($grades as $grade) {
    $totalPoints += $grade['grade'];
    $totalMaxPoints += $grade['max_score'];
}
$overallPercentage = $totalMaxPoints > 0 ? ($totalPoints / $totalMaxPoints) * 100 : 0;

$pageTitle = 'My Grades';
require_once dirname(__DIR__) . '/views/shared/header.php';
?>

<div class="header">
    <h1>My Grades</h1>
</div>

<!-- Overall Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?php echo $totalGrades; ?></div>
        <div class="stat-label">Graded Assignments</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo number_format($totalPoints, 1); ?></div>
        <div class="stat-label">Total Points Earned</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo number_format($overallPercentage, 1); ?>%</div>
        <div class="stat-label">Overall Average</div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($grades)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Assignment</th>
                    <th>Grade</th>
                    <th>Percentage</th>
                    <th>Graded On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $grade): ?>
                <?php $percentage = ($grade['grade'] / $grade['max_score']) * 100; ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($grade['assignment_title']); ?></strong></td>
                    <td><?php echo $grade['grade']; ?> / <?php echo $grade['max_score']; ?></td>
                    <td>
                        <span class="badge badge-<?php echo $percentage >= 70 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger'); ?>">
                            <?php echo number_format($percentage, 1); ?>%
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($grade['graded_at'])); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/student/submission_view.php?id=<?php echo $grade['id']; ?>" class="btn btn-sm btn-secondary">View Feedback</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-center" style="padding: 40px;">
            <h3>No Grades Yet</h3>
            <p class="text-muted">Your submissions haven't been graded yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/views/shared/footer.php'; ?>
