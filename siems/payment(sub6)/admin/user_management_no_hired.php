<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';
require_once '../includes/two_factor.php';

requireSubsystemAccess('user_management');

$rolesList = siemsFetchAll("SELECT role_name FROM roles ORDER BY role_name ASC");

$module = $_GET['module'] ?? '';
$allowedModules = ['accounts', 'roles', 'security', 'audit', 'recovery'];
if (!in_array($module, $allowedModules, true)) {
    $module = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // All POST handlers unchanged...
        if (isset($_POST['save_user'])) {
            $passwordHash = password_hash(trim($_POST['new_password']) ?: 'password', PASSWORD_DEFAULT);
            $pdo->prepare("
                INSERT INTO users
                    (student_id, employee_id, full_name, email, phone, program, year_level, enrollment_status, student_status, role, password_hash, must_change_password, is_active)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ")->execute([
                trim($_POST['student_id']),
                trim($_POST['employee_id']) ?: null,
                trim($_POST['full_name']),
                trim($_POST['email']) ?: null,
                trim($_POST['phone']) ?: null,
                trim($_POST['program']) ?: null,
                $_POST['year_level'] !== '' ? (int) $_POST['year_level'] : null,
                trim($_POST['enrollment_status'] ?: 'Pending'),
                trim($_POST['student_status'] ?: 'Active'),
                trim($_POST['role']),
                $passwordHash,
                isset($_POST['must_change_password']) ? 1 : 0,
                isset($_POST['is_active']) ? 1 : 0,
            ]);
            siemsLogSubsystemEvent('User Management', 'Created user account', trim($_POST['student_id']), null, trim($_POST['role']));
            $_SESSION['message'] = 'User account saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=accounts');
            exit;
        } // ... all other elseif handlers remain the same
    } catch (Throwable $e) {
        $_SESSION['message'] = 'Unable to save user management data. ' . $e->getMessage();
        $_SESSION['msg_type'] = 'danger';
        header('Location: user_management.php' . ($module !== '' ? '?module=' . urlencode($module) : ''));
        exit;
    }
}

$overview = siemsGetUserManagementOverview();
$users = $overview['users'];
$roles = $overview['roles'];
$permissions = $overview['permissions'];
$rolePermissions = $overview['role_permissions'];
$passwordResets = $overview['password_resets'];
$auditLogs = $overview['audit_logs'];
$twoFactorSetup = $_SESSION['two_factor_setup'] ?? null;
if ($module !== 'security') {
    unset($_SESSION['two_factor_setup']);
}
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-shield-lock text-success me-2"></i>User Management</h3>
        <div class="row-text">Subsystem 10 for user accounts, roles, authentication security, audit monitoring, and password recovery. (Hired applicants view removed)</div>
    </div>
    <a href="siems_hub.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;"><i class="bi bi-arrow-left me-1"></i> SIEMS Hub</a>
</div>

<!-- QUICK ACTION BUTTONS unchanged -->

<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4">
        <span class="section-title">QUICK ACTION BUTTONS</span>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-4 col-lg-2">
                <a href="user_management.php?module=accounts" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-person-plus d-block fs-4 mb-2"></i>Account Creation
                </a>
            </div>
            <!-- Other buttons unchanged -->
        </div>
    </div>
</div>

<?php if ($module === 'accounts'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">READY FOR ACCOUNT CREATION</span></div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Newly hired applicants view temporarily disabled. Use HR → Recruitment module to manage applicants.
                </div>
                <div class="text-center">
                    <a href="human_resources.php?module=recruitment" class="btn btn-outline-primary">Open HR Recruitment →</a>
                </div>
            </div>
        </div>
        <!-- User creation form unchanged -->
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">USER ACCOUNT CREATION</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><input type="text" name="student_id" class="form-control row-text" placeholder="Student ID / Login ID" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <!-- Rest of form unchanged -->
                    <div class="col-12"><button type="submit" name="save_user" class="btn btn-success fw-bold" style="border-radius:8px;">Save User</button></div>
                </form>
            </div>
        </div>
    </div>
    <!-- Rest of accounts module unchanged -->
</div>
<?php endif; ?>

<!-- All other modules unchanged -->

<?php include '../includes/footer.php'; ?>

