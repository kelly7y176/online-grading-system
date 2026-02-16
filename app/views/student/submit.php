<?php
$pageTitle = 'Submit Assignment';
require_once dirname(__DIR__) . '/shared/header.php';
$isResubmit = isset($existingSubmission) && !empty($existingSubmission);
?>

<div class="header">
    <h1><?php echo $isResubmit ? 'Update Submission' : 'Submit Assignment'; ?></h1>
    <a href="<?php echo BASE_URL; ?>/student/assignments.php" class="btn btn-secondary">← Back to Assignments</a>
</div>

<!-- Assignment Details -->
<div class="card">
    <div class="card-header">
        <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
    </div>
    <div class="card-body">
        <div class="d-flex gap-1" style="flex-wrap: wrap;">
            <div style="flex: 2; min-width: 300px;">
                <h4>Description</h4>
                <p><?php echo nl2br(htmlspecialchars($assignment['description'] ?: 'No description provided.')); ?></p>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <table class="table">
                    <tr>
                        <th>Due Date</th>
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
                    </tr>
                    <tr>
                        <th>Max Score</th>
                        <td><?php echo $assignment['max_score']; ?> points</td>
                    </tr>
                    <tr>
                        <th>Instructor</th>
                        <td><?php echo htmlspecialchars($assignment['instructor_name']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($isResubmit): ?>
<div class="alert alert-warning">
    <strong>Note:</strong> You have already submitted this assignment. Submitting again will replace your previous submission and reset any existing grade.
</div>
<?php endif; ?>

<!-- Submission Form -->
<div class="card">
    <div class="card-header">
        <h3>Your Submission</h3>
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/student/submit_process.php?id=<?php echo $assignment['id']; ?>" 
              method="POST" enctype="multipart/form-data" data-validate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="text_content">Text Submission</label>
                <textarea id="text_content" name="text_content" class="form-control" rows="8"
                          placeholder="Enter your submission text here (optional if uploading a file)"><?php echo $isResubmit ? htmlspecialchars($existingSubmission['text_content']) : ''; ?></textarea>
                <p class="form-text">You can type your submission directly or upload a file below.</p>
            </div>
            
            <div class="form-group">
                <label>File Upload</label>
                <div class="file-upload">
                    <input type="file" id="submission_file" name="submission_file" accept=".pdf,.doc,.docx,.txt,.zip,.rar">
                    <p class="file-label">
                        <?php if ($isResubmit && $existingSubmission['file_path']): ?>
                        Current file: <?php echo basename($existingSubmission['file_path']); ?><br>
                        <small>Click to upload a new file</small>
                        <?php else: ?>
                        Click to select a file or drag and drop
                        <?php endif; ?>
                    </p>
                </div>
                <p class="form-text">
                    Allowed file types: <?php echo implode(', ', ALLOWED_FILE_TYPES); ?><br>
                    Maximum file size: <?php echo MAX_FILE_SIZE / 1024 / 1024; ?>MB
                </p>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg">
                <?php echo $isResubmit ? 'Update Submission' : 'Submit Assignment'; ?>
            </button>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
