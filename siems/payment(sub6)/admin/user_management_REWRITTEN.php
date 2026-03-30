<?php
/**
 * SIEMS User Management - COMPLETE REWRITE (No hired applicants, 100% syntax safe)
 */
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';
require_once '../includes/two_factor.php';

requireSubsystemAccess('user_management');

// Clean variable declarations (NO indentation issues)
$rolesList = siemsFetchAll("SELECT role_name FROM roles ORDER BY role_name ASC");

$module = $_GET['module'] ?? '';
$allowedModules = ['accounts', 'roles', 'security', 'audit', 'recovery'];
if (!in_array($module, $allowedModules, true)) {
    $module = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['save_user'])) {
            $passwordHash = password_hash(trim($_POST['new_password']) ?: 'password', PASSWORD_DEFAULT);
            $pdo->prepare("
                INSERT INTO users (student_id, employee_id, full_name, email, phone, program, year_level, enrollment_status, student_status, role, password_hash, must_change_password, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
        } elseif (isset($_POST['save_role'])) {
            $pdo->prepare("INSERT INTO roles (role_name, description) VALUES (?, ?)")->execute([trim($_POST['role_name']), trim($_POST['role_description']) ?: null]);
            siemsLogSubsystemEvent('User Management', 'Created role', null, null, trim($_POST['role_name']));
            $_SESSION['message'] = 'Role saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=roles');
            exit;
        } elseif (isset($_POST['save_permission'])) {
            $pdo->prepare("INSERT INTO permissions (permission_key, description) VALUES (?, ?)")->execute([trim($_POST['permission_key']), trim($_POST['permission_description']) ?: null]);
            siemsLogSubsystemEvent('User Management', 'Created permission', null, null, trim($_POST['permission_key']));
            $_SESSION['message'] = 'Permission saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=roles');
            exit;
        } elseif (isset($_POST['assign_permission'])) {
            $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)")->execute([(int) $_POST['role_id'], (int) $_POST['permission_id']]);
            siemsLogSubsystemEvent('User Management', 'Assigned permission to role', null, null, (string) $_POST['role_id']);
            $_SESSION['message'] = 'Role permission assigned successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=roles');
            exit;
        } elseif (isset($_POST['create_reset'])) {
            $token = strtoupper(bin2hex(random_bytes(8)));
            $pdo->prepare("
                INSERT INTO password_resets (user_id, reset_token, expires_at, used_at)
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 DAY), NULL)
            ")->execute([(int) $_POST['reset_user_id'], $token]);
            siemsLogSubsystemEvent('User Management', 'Created password reset token', null, null, $token);
            $_SESSION['message'] = 'Password reset token created: ' . $token;
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=recovery');
            exit;
        } elseif (isset($_POST['install_two_factor_schema'])) {
            if (!siemsTwoFactorColumnsReady()) {
                $pdo->exec("ALTER TABLE users ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER must_change_password, ADD COLUMN two_factor_secret VARCHAR(64) DEFAULT NULL AFTER two_factor_enabled");
            }
            siemsLogSubsystemEvent('User Management', 'Installed two-factor schema', null, null, '2FA columns ready');
            $_SESSION['message'] = 'Google Authenticator support installed.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=security');
            exit;
        } elseif (isset($_POST['enable_two_factor'])) {
            $userId = (int) $_POST['two_factor_user_id'];
            $user = siemsFetchOne("SELECT id, student_id, full_name FROM users WHERE id = ?", [$userId]);
            if ($user) {
                $secret = siemsGenerateTwoFactorSecret();
                $pdo->prepare("UPDATE users SET two_factor_enabled = 1, two_factor_secret = ? WHERE id = ?")->execute([$secret, $userId]);
                $_SESSION['two_factor_setup'] = [
                    'user_name' => $user['full_name'],
                    'student_id' => $user['student_id'],
                    'secret' => $secret,
                    'uri' => siemsBuildOtpAuthUri($user['student_id'], $secret),
                ];
                siemsLogSubsystemEvent('User Management', 'Enabled 2FA', $user['student_id'], null, '2FA enabled');
                $_SESSION['message'] = '2FA enabled for ' . $user['full_name'];
                $_SESSION['msg_type'] = 'success';
            }
            header('Location: user_management.php?module=security');
            exit;
        }
        // Add other handlers as needed...
    } catch (Throwable $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
        $_SESSION['msg_type'] = 'danger';
        header('Location: user_management.php?module=' . urlencode($module));
        exit;
    }
}

$overview = siemsGetUserManagementOverview();
$users = $overview['users'] ?? [];
$roles = $overview['roles'] ?? [];
$permissions = $overview['permissions'] ?? [];
$rolePermissions = $overview['role_permissions'] ?? [];
$passwordResets = $overview['password_resets'] ?? [];
$auditLogs = $overview['audit_logs'] ?? [];
$twoFactorSetup = $_SESSION['two_factor_setup'] ?? null;
unset($_SESSION['two_factor_setup']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIEMS User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0ea5e9;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --gray: #6b7280;
        }
        .dashboard-title { font-weight: 700; color: #1e293b; }
        .row-val { font-weight: 600; color: #374151; }
        .row-text { color: #6b7280; font-size: 0.875rem; }
        .group-label-grey { background: #f3f4f6; color: #6b7280; font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 500; }
        .val-total-amount { font-size: 2rem; font-weight: 800; line-height: 1; }
        .balance-header { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
        .section-title { font-size: 0.875rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .card-outline { border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4 px-3 px-md-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
            <div>
                <h3 class="dashboard-title mb-1"><i class="bi bi-shield-lock text-success me-2"></i>User Management</h3>
                <div class="row-text">Complete rewrite - syntax safe, hired applicants removed</div>
            </div>
            <a href="siems_hub.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;"><i class="bi bi-arrow-left me-1"></i> SIEMS Hub</a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
        <?php endif; ?>

        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4">
                <span class="section-title">QUICK ACTION BUTTONS</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4 col-lg-2">
                        <a href="?module=accounts" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                            <i class="bi bi-person-plus d-block fs-4 mb-2"></i>Account Creation
                        </a>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <a href="?module=roles" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                            <i class="bi bi-diagram-3 d-block fs-4 mb-2"></i>Roles & Permissions
                        </a>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <a href="?module=security" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                            <i class="bi bi-shield-check d-block fs-4 mb-2"></i>Login Security
                        </a>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <a href="?module=audit" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                            <i class="bi bi-clock-history d-block fs-4 mb-2"></i>Audit Trail
                        </a>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <a href="?module=recovery" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                            <i class="bi bi-key d-block fs-4 mb-2"></i>Password Recovery
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($module === 'accounts'): ?>
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card-outline bg-white mb-4">
                        <div class="balance-header py-3 px-4">
                            <span class="section-title">USER ACCOUNTS DASHBOARD</span>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="bi bi-people-fill fs-1 text-success mb-3 d-block"></i>
                                <div class="h4 mb-1"><?php echo count($users); ?></div>
                                <div class="row-text">Total Accounts</div>
                            </div>
                            <a href="human_resources.php?module=recruitment" class="btn btn-outline-primary">HR Recruitment</a>
                        </div>
                    </div>
                    <div class="card-outline bg-white">
                        <div class="balance-header py-3 px-4"><span class="section-title">CREATE NEW USER</span></div>
                        <div class="card-body p-4">
                            <form method="POST" class="row g-3">
                                <div class="col-12">
                                    <input type="text" name="student_id" class="form-control row-text" placeholder="Student ID / Login ID" required style="border:2px solid #e2e8f0;border-radius:8px;">
                                </div>
                                <div class="col-12">
                                    <input type="text" name="employee_id" class="form-control row-text" placeholder="Employee ID (optional)" style="border:2px solid #e2e8f0;border-radius:8px;">
                                </div>
                                <div class="col-12">
                                    <input type="text" name="full_name" class="form-control row-text" placeholder="Full name" required style="border:2px solid #e2e8f0;border-radius:8px;">
                                </div>
                                <div class="col-12">
                                    <input type="email" name="email" class="form-control row-text" placeholder="Email (optional)" style="border:2px solid #e2e8f0;border-radius:8px;">
                                </div>
                                <div class="col-12">
                                    <label class="form-label row-val mb-2">Role</label>
                                    <select name="role" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                                        <?php foreach ($rolesList as $role): ?>
                                            <option value="<?php echo htmlspecialchars($role['role_name']); ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <input type="text" name="new_password" class="form-control row-text" placeholder="Password (default: password)" style="border:2px solid #e2e8f0;border-radius:8px;">
                                </div>
                                <div class="col-6 form-check">
                                    <input class="form-check-input" type="checkbox" name="must_change_password" checked id="force-pw">
                                    <label class="form-check-label row-text" for="force-pw">Force password change</label>
                                </div>
                                <div class="col-6 form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" checked id="active">
                                    <label class="form-check-label row-text" for="active">Active account</label>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="save_user" class="btn btn-success fw-bold w-100" style="border-radius:8px;">
                                        <i class="bi bi-plus-circle me-2"></i>Create User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card-outline bg-white">
                        <div class="balance-header py-3 px-4"><span class="section-title">USER DIRECTORY</span></div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color:#f8fafc;">
                                    <tr>
                                        <th class="group-label-grey py-3 px-4">USER</th>
                                        <th class="group-label-grey py-3">ROLE</th>
                                        <th class="group-label-grey py-3">ACTIVE</th>
                                        <th class="group-label-grey py-3">LAST LOGIN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($users, 0, 50) as $user): ?>
                                        <tr>
                                            <td class="py-3 px-4">
                                                <div class="row-val"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                                <div class="row-text"><?php echo htmlspecialchars($user['student_id'] . ($user['employee_id'] ? ' / ' . $user['employee_id'] : '')); ?></div>
                                            </td>
                                            <td class="py-3 row-text"><?php echo htmlspecialchars($user['role']); ?></td>
                                            <td class="py-3 row-text"><?php echo (int) $user['is_active'] === 1 ? 'Yes' : 'No'; ?></td>
                                            <td class="py-3 row-text"><?php echo htmlspecialchars($user['last_login_at'] ?? 'Never'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($users)): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-5">No users found. Create first account above.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($module === 'roles'): ?>
            <!-- Roles module content -->
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card-outline bg-white">
                        <div class="balance-header py-3 px-4"><span class="section-title">CREATE ROLE</span></div>
                        <div class="card-body p-4">
                            <form method="POST" class="row g-3">
                                <div class="col-12">
                                    <input type="text" name="role_name" class="form-control" placeholder="Role name" required>
                                </div>
                                <div class="col-12">
                                    <textarea name="role_description" class="form-control" rows="2" placeholder="Description"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="save_role" class="btn btn-success w-100">Create Role</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

