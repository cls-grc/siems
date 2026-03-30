<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/auth.php';


if (isset($_POST['student_id'], $_POST['password']) && $_POST['student_id'] && $_POST['password']) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
    $stmt->execute([$_POST['student_id']]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        if ((int) ($user['is_active'] ?? 1) !== 1) {
            $_SESSION['message'] = 'This account is inactive.';
            $_SESSION['msg_type'] = 'danger';
            header('Location: login.php');
            exit;
        }

        if ($user['role'] === 'enrollment') {
            $_SESSION['message'] = 'This account is no longer available.';
            $_SESSION['msg_type'] = 'danger';
            header('Location: login.php');
            exit;
        }

        if ($user['role'] !== 'student' && !isDesignatedStaffAccount($user['student_id']) && !isRecognizedStaffRole($user['role'])) {
            $_SESSION['message'] = 'This staff account is not designated to any subsystem.';
            $_SESSION['msg_type'] = 'danger';
            header('Location: login.php');
            exit;
        }

        $dashboard = resolveUserHomePath($user['student_id'], $user['role']);
        if ($dashboard === null) {
            $_SESSION = [];
            session_regenerate_id(true);
            $_SESSION['message'] = 'This account does not have a valid home page assignment.';
            $_SESSION['msg_type'] = 'danger';
            header('Location: login.php');
            exit;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['student_id'] = $user['student_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['program'] = $user['program'];

        // Pre-calculate assessment for students
        if ($user['role'] === 'student') {
            require_once 'includes/functions.php';
            calculateAssessment($user['student_id']);
        }

        $_SESSION['message'] = 'Welcome back, ' . $user['full_name'] . '!';
        $_SESSION['msg_type'] = 'success';

        header('Location: ' . $dashboard);
        exit;
    } else {
        $_SESSION['message'] = 'Invalid credentials!';
        $_SESSION['msg_type'] = 'danger';
    }
}

header('Location: login.php');
exit;
?>
