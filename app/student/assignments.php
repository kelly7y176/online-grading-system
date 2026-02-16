<?php
/**
 * Student Assignments List
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/Assignment.php';
require_once dirname(__DIR__) . '/models/Submission.php';

if (!isStudent()) {
    redirect(BASE_URL . '/login.php');
}

$assignmentModel = new Assignment();
$submissionModel = new Submission();

$assignments = $assignmentModel->getAll();
$mySubmissions = $submissionModel->getByStudent($_SESSION['user_id']);

// Create submission map
$submissionMap = [];
foreach ($mySubmissions as $sub) {
    $submissionMap[$sub['assignment_id']] = $sub;
}

$pageTitle = 'Assignments';
require_once dirname(__DIR__) . '/views/shared/header.php';
?>

<div class="header">
    <h1>Available Assignments</h1>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($assignments)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Assignment</th>
                    <th>Instructor</th>
                    <th>Due Date</th>
                    <th>Max Score</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $assignment): ?>
                <?php 
                $hasSubmission = isset($submissionMap[$assignment['id']]);
                $submission = $hasSubmission ? $submissionMap[$assignment['id']] : null;
                $dueDate = new DateTime($assignment['due_date']);
                $now = new DateTime();
                $isPast = $dueDate < $now;
                ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($assignment['title']); ?></strong>
                        <?php if (!empty($assignment['description'])): ?>
                        <br><small class="text-muted"><?php echo substr(htmlspecialchars($assignment['description']), 0, 80); ?>...</small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($assignment['instructor_name']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $isPast ? 'danger' : 'primary'; ?>">
                            <?php echo $dueDate->format('M d, Y'); ?>
                        </span>
                    </td>
                    <td><?php echo $assignment['max_score']; ?></td>
                    <td>
                        <?php if ($hasSubmission): ?>
                            <?php if ($submission['grade'] !== null): ?>
                            <span class="badge badge-success">Graded: <?php echo $submission['grade']; ?></span>
                            <?php else: ?>
                            <span class="badge badge-warning">Submitted</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge badge-secondary">Not Submitted</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($hasSubmission): ?>
                        <a href="<?php echo BASE_URL; ?>/student/submission_view.php?id=<?php echo $submission['id']; ?>" class="btn btn-sm btn-secondary">View</a>
                        <?php if (!$isPast): ?>
                        <a href="<?php echo BASE_URL; ?>/student/submit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary">Resubmit</a>
                        <?php endif; ?>
                        <?php else: ?>
                            <?php if (!$isPast): ?>
                            <a href="<?php echo BASE_URL; ?>/student/submit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary">Submit</a>
                            <?php else: ?>
                            <span class="text-muted">Past due</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-muted text-center">No assignments available.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/views/shared/footer.php'; ?>
