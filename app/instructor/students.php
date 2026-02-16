<?php
/**
 * Students List Page
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/User.php';

if (!isInstructor()) {
    redirect(BASE_URL . '/login.php');
}

$userModel = new User();
$students = $userModel->getAllStudents();

$pageTitle = 'Students';
require_once dirname(__DIR__) . '/views/shared/header.php';
?>

<div class="header">
    <h1>Students</h1>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($students)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($student['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-muted text-center">No students registered yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/views/shared/footer.php'; ?>
