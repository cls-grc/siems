<?php
require_once __DIR__ . '/../config/db_connect.php';

function getDesignatedSubsystemAccounts() {
    return [
        'ADMIN001' => [
            'label' => 'System Administrator',
            'home' => 'admin/dashboard.php',
            'subsystems' => ['*'],
        ],
        'SUB1ADMIN' => [
            'label' => 'Subsystem 1 Admin',
            'home' => 'admin/student_information.php',
            'subsystems' => ['student_information'],
        ],
        'SUB2ADMIN' => [
            'label' => 'Subsystem 2 Admin',
            'home' => 'admin/enrollment_registration.php',
            'subsystems' => ['enrollment_registration'],
        ],
        'SUB3ADMIN' => [
            'label' => 'Subsystem 3 Admin',
            'home' => 'admin/curriculum_course.php',
            'subsystems' => ['curriculum_course'],
        ],
        'SUB4ADMIN' => [
            'label' => 'Subsystem 4 Admin',
            'home' => 'admin/class_scheduling.php',
            'subsystems' => ['class_scheduling'],
        ],
        'SUB5ADMIN' => [
            'label' => 'Subsystem 5 Admin',
            'home' => 'admin/grades_assessment.php',
            'subsystems' => ['grades_assessment'],
        ],
        'SUB6ADMIN' => [
            'label' => 'Subsystem 6 Admin',
            'home' => 'admin/dashboard.php',
            'subsystems' => ['payment_accounting'],
        ],
        'SUB7ADMIN' => [
            'label' => 'Subsystem 7 Admin',
            'home' => 'admin/document_requests.php',
            'subsystems' => ['documents_credentials'],
        ],
        'SUB8ADMIN' => [
            'label' => 'Subsystem 8 Admin',
            'home' => 'admin/human_resources.php',
            'subsystems' => ['human_resources'],
        ],
        'SUB9ADMIN' => [
            'label' => 'Subsystem 9 Admin',
            'home' => 'admin/clinic_medical.php',
            'subsystems' => ['clinic_medical'],
        ],
        'SUB10ADMIN' => [
            'label' => 'Subsystem 10 Admin',
            'home' => 'admin/user_management.php',
            'subsystems' => ['user_management'],
        ],
    ];
}

function getRoleBasedAccessMap() {
    return [
        'student' => [
            'home' => 'student/dashboard.php',
            'subsystems' => [],
        ],
        'admin' => [
            'home' => 'admin/siems_hub.php',
            'subsystems' => ['*'],
        ],
        'cashier' => [
            'home' => 'admin/dashboard.php',
            'subsystems' => ['payment_accounting'],
        ],
        'registrar' => [
            'home' => 'admin/enrollment_registration.php',
            'subsystems' => ['student_information', 'enrollment_registration'],
        ],
        'faculty' => [
            'home' => 'admin/grades_assessment.php',
            'subsystems' => ['grades_assessment'],
        ],
        'hr' => [
            'home' => 'admin/human_resources.php',
            'subsystems' => ['human_resources'],
        ],
        'clinic' => [
            'home' => 'admin/clinic_medical.php',
            'subsystems' => ['clinic_medical'],
        ],
    ];
}

function isDesignatedStaffAccount($studentId) {
    $accounts = getDesignatedSubsystemAccounts();
    return isset($accounts[$studentId]);
}

function isRecognizedStaffRole($role) {
    $roleMap = getRoleBasedAccessMap();
    return isset($roleMap[$role]) && $role !== 'student';
}

function getUserHomePath($studentId, $role) {
    return resolveUserHomePath($studentId, $role) ?? 'index.php';
}

function resolveUserHomePath($studentId, $role) {
    $accounts = getDesignatedSubsystemAccounts();
    if (isset($accounts[$studentId])) {
        return $accounts[$studentId]['home'];
    }

    $roleMap = getRoleBasedAccessMap();
    return $roleMap[$role]['home'] ?? null;
}

function getRoleHomePath($role) {
    return $role === 'student' ? 'student/dashboard.php' : 'index.php';
}

function requireAuthenticatedUser() {
    if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
        header('Location: ../index.php');
        exit;
    }
}

function requireStudentRole() {
    requireAuthenticatedUser();

    if ($_SESSION['role'] !== 'student') {
        $_SESSION['message'] = 'This page is only available to student accounts.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: ../' . getUserHomePath($_SESSION['student_id'] ?? '', $_SESSION['role']));
        exit;
    }
}

function requireRoleAccess(array $allowedRoles) {
    requireAuthenticatedUser();

    if (in_array($_SESSION['role'], $allowedRoles, true)) {
        return;
    }

    $_SESSION['message'] = 'Your account is not allowed to access that subsystem.';
    $_SESSION['msg_type'] = 'danger';
    header('Location: ../' . getUserHomePath($_SESSION['student_id'] ?? '', $_SESSION['role']));
    exit;
}

function requireSubsystemAccess($subsystemKey) {
    requireAuthenticatedUser();

    $role = $_SESSION['role'] ?? '';
    $studentId = $_SESSION['student_id'] ?? '';
    global $pdo;

    if ($role === 'student') {
        $_SESSION['message'] = 'Your account is not allowed to access that subsystem.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: ../student/dashboard.php');
        exit;
    }

    $accounts = getDesignatedSubsystemAccounts();
    if (isset($accounts[$studentId])) {
        if (in_array('*', $accounts[$studentId]['subsystems'], true) || in_array($subsystemKey, $accounts[$studentId]['subsystems'], true)) {
            return;
        }
        $_SESSION['message'] = 'Your account is only assigned to its designated subsystem.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: ../' . $accounts[$studentId]['home']);
        exit;
    }

    $roleMap = getRoleBasedAccessMap();
    if (isset($roleMap[$role])) {
        $allowedSubsystems = $roleMap[$role]['subsystems'];
        if (in_array('*', $allowedSubsystems, true) || in_array($subsystemKey, $allowedSubsystems, true)) {
            return;
        }
        $_SESSION['message'] = 'Your account is not allowed to access that subsystem.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: ../' . $roleMap[$role]['home']);
        exit;
    }

    $_SESSION['message'] = 'This staff account is not assigned to a subsystem.';
    $_SESSION['msg_type'] = 'danger';
    header('Location: ../index.php');
    exit;
}
