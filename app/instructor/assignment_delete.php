<?php
/**
 * Assignment Delete Handler
 * CS425 Assignment Grading System
 */

require_once dirname(__DIR__) . '/controllers/AssignmentController.php';

$controller = new AssignmentController();
$id = intval($_POST['id'] ?? 0);

if ($id > 0) {
    $controller->delete($id);
} else {
    setFlashMessage('error', 'Invalid assignment ID.');
    redirect(BASE_URL . '/instructor/assignments.php');
}
