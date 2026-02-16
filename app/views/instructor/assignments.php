<?php
$pageTitle = 'Assignments';
require_once dirname(__DIR__) . '/shared/header.php';
?>

<div class="header">
    <h1>My Assignments</h1>
    <a href="<?php echo BASE_URL; ?>/instructor/assignment_create.php" class="btn btn-primary">+ New Assignment</a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($assignments)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Due Date</th>
                    <th>Max Score</th>
                    <th>Submissions</th>
                    <th>Progress</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $assignment): ?>
                <tr>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/instructor/assignment_view.php?id=<?php echo $assignment['id']; ?>">
                            <strong><?php echo htmlspecialchars($assignment['title']); ?></strong>
                        </a>
                        <?php if (!empty($assignment['description'])): ?>
                        <br><small class="text-muted"><?php echo substr(htmlspecialchars($assignment['description']), 0, 50); ?>...</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                        $dueDate = new DateTime($assignment['due_date']);
                        $now = new DateTime();
                        $isPast = $dueDate < $now;
                        ?>
                        <span class="badge badge-<?php echo $isPast ? 'danger' : 'primary'; ?>">
                            <?php echo $dueDate->format('M d, Y'); ?>
                        </span>
                    </td>
                    <td><?php echo $assignment['max_score']; ?></td>
                    <td><?php echo $assignment['submission_count']; ?></td>
                    <td>
                        <?php 
                        $progress = $assignment['submission_count'] > 0 
                            ? ($assignment['graded_count'] / $assignment['submission_count']) * 100 
                            : 0;
                        ?>
                        <div class="progress" style="width: 100px;">
                            <div class="progress-bar" style="width: <?php echo $progress; ?>%"></div>
                        </div>
                        <small><?php echo $assignment['graded_count']; ?>/<?php echo $assignment['submission_count']; ?> graded</small>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?php echo BASE_URL; ?>/instructor/submissions.php?assignment_id=<?php echo $assignment['id']; ?>" 
                               class="btn btn-sm btn-primary">Submissions</a>
                            <a href="<?php echo BASE_URL; ?>/instructor/assignment_edit.php?id=<?php echo $assignment['id']; ?>" 
                               class="btn btn-sm btn-secondary">Edit</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-center" style="padding: 40px;">
            <h3>No Assignments Yet</h3>
            <p class="text-muted">Create your first assignment to get started.</p>
            <a href="<?php echo BASE_URL; ?>/instructor/assignment_create.php" class="btn btn-primary">Create Assignment</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
