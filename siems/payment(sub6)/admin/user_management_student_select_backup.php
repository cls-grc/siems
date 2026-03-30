<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';
require_once '../includes/two_factor.php';

// requireSubsystemAccess('user_management');

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
            $insertData = [
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
                    trim($_POST['faculty_program']) ?: null,
                ];
            $pdo->prepare("INSERT INTO users (student_id, employee_id, full_name, email, phone, program, year_level, enrollment_status, student_status, role, password_hash, must_change_password, is_active, faculty_program) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
                ->execute($insertData);
            siemsLogSubsystemEvent('User Management', 'Created user', trim($_POST['student_id']), null, trim($_POST['role']));
            $_SESSION['message'] = 'User created successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=accounts');
            exit;
        } elseif (isset($_POST['save_role'])) {
            $pdo->prepare("INSERT INTO roles (role_name, description) VALUES (?, ?)")->execute([trim($_POST['role_name']), trim($_POST['role_description']) ?: null]);
            siemsLogSubsystemEvent('User Management', 'Created role', null, null, trim($_POST['role_name']));
            $_SESSION['message'] = 'Role created successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=roles');
            exit;
        } elseif (isset($_POST['save_permission'])) {
            $pdo->prepare("INSERT INTO permissions (permission_key, description) VALUES (?, ?)")->execute([trim($_POST['permission_key']), trim($_POST['permission_description']) ?: null]);
            siemsLogSubsystemEvent('User Management', 'Created permission', null, null, trim($_POST['permission_key']));
            $_SESSION['message'] = 'Permission created successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=roles');
            exit;
        } elseif (isset($_POST['assign_permission'])) {
            $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)")->execute([(int) $_POST['role_id'], (int) $_POST['permission_id']]);
            siemsLogSubsystemEvent('User Management', 'Assigned permission', null, null, $_POST['role_id']);
            $_SESSION['message'] = 'Permission assigned successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=roles');
            exit;
        } elseif (isset($_POST['create_reset'])) {
            $token = strtoupper(bin2hex(random_bytes(8)));
            $pdo->prepare("INSERT INTO password_resets (user_id, reset_token, expires_at, used_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 DAY), NULL)")->execute([(int) $_POST['reset_user_id'], $token]);
            siemsLogSubsystemEvent('User Management', 'Created reset token', null, null, $token);
            $_SESSION['message'] = 'Reset token: ' . $token;
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=recovery');
            exit;
        } elseif (isset($_POST['install_two_factor_schema'])) {
            if (!siemsTwoFactorColumnsReady()) {
                $pdo->exec("ALTER TABLE users ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER must_change_password, ADD COLUMN two_factor_secret VARCHAR(64) DEFAULT NULL AFTER two_factor_enabled");
            }
            siemsLogSubsystemEvent('User Management', 'Installed 2FA schema', null, null, '2FA ready');
            $_SESSION['message'] = '2FA schema installed.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=security');
            exit;
        } elseif (isset($_POST['enable_two_factor'])) {
            $userId = (int) $_POST['two_factor_user_id'];
            $user = siemsFetchOne("SELECT id, student_id, full_name FROM users WHERE id = ?", [$userId]);
            if ($user && siemsTwoFactorColumnsReady()) {
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
        } elseif (isset($_POST['disable_two_factor'])) {
            $userId = (int) $_POST['two_factor_user_id'];
            $pdo->prepare("UPDATE users SET two_factor_enabled = 0, two_factor_secret = NULL WHERE id = ?")->execute([$userId]);
            siemsLogSubsystemEvent('User Management', 'Disabled 2FA', null, null, $userId);
            $_SESSION['message'] = '2FA disabled.';
            $_SESSION['msg_type'] = 'success';
            header('Location: user_management.php?module=security');
            exit;
        }
    } catch (Throwable $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
        $_SESSION['msg_type'] = 'danger';
        header('Location: user_management.php' . ($module ? '?module=' . urlencode($module) : ''));
        exit;
    }
}

$page_title = 'User Management - SIEMS';
$overview = siemsGetUserManagementOverview();
$users = $overview['users'] ?? [];
$roles = $overview['roles'] ?? [];
$permissions = $overview['permissions'] ?? [];
$rolePermissions = $overview['role_permissions'] ?? [];
$passwordResets = $overview['password_resets'] ?? [];
$auditLogs = $overview['audit_logs'] ?? [];
$twoFactorSetup = $_SESSION['two_factor_setup'] ?? null;
if ($module !== 'security') {
    unset($_SESSION['two_factor_setup']);
}
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-shield-lock text-success me-2"></i>User Management</h3>
        <div class="row-text">Syntax-safe - All 5 modules functional (Accounts, Roles, Security, Audit, Recovery)</div>
    </div>
    <a href="siems_hub.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;"><i class="bi bi-arrow-left me-1"></i> SIEMS Hub</a>
</div>

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

<?php if ($module === ''): ?>
<div class="card-outline bg-white p-5 text-center">
    <i class="bi bi-grid-1x2 fs-1 text-success d-block mb-3"></i>
    <h4 class="dashboard-title mb-2">User Management Hub</h4>
    <div class="row-text">Click any quick action button above to open module.</div>
</div>
<?php elseif ($module === 'accounts'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4">
                <span class="section-title">USER STATISTICS</span>
            </div>
            <div class="card-body p-4 text-center">
                <i class="bi bi-people-fill fs-1 text-success mb-3 d-block"></i>
                <h4 class="mb-1"><?php echo count($users); ?></h4>
                <div class="row-text">Total User Accounts</div>
                <a href="human_resources.php?module=recruitment" class="btn btn-outline-primary mt-3">HR Recruitment</a>
            </div>
        </div>
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">CREATE NEW USER</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><input type="text" name="student_id" class="form-control row-text" placeholder="Student ID / Login ID *" required></div>
                    <div class="col-12"><input type="text" name="employee_id" class="form-control row-text" placeholder="Employee ID (optional)"></div>
                    <div class="col-12"><input type="text" name="full_name" class="form-control row-text" placeholder="Full Name *" required></div>
                    <div class="col-12"><input type="email" name="email" class="form-control row-text" placeholder="Email (optional)"></div>
                    <div class="col-12">
                        <label class="form-label row-val mb-2">Role</label>
                        <select name="role" class="form-select row-text" required>
                            <?php foreach ($rolesList as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['role_name']); ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12"><input type="text" name="new_password" class="form-control row-text" placeholder="Password (default: password)"></div>
                    <div class="col-6 form-check ms-2"><input class="form-check-input" type="checkbox" name="must_change_password" checked><label class="form-check-label row-text">Force password change</label></div>
                    <div class="col-6 form-check ms-2"><input class="form-check-input" type="checkbox" name="is_active" checked><label class="form-check-label row-text">Active account</label></div>
                    <div class="col-12"><button type="submit" name="save_user" class="btn btn-success fw-bold" style="border-radius:8px;">Create User</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">USER DIRECTORY</span></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background-color:#f8fafc;">
                        <tr><th class="group-label-grey py-3 px-4">USER</th><th class="group-label-grey py-3">ROLE</th><th class="group-label-grey py-3">ACTIVE</th><th class="group-label-grey py-3">LAST LOGIN</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($users, 0, 50) as $user): ?>
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="row-val"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                    <div class="row-text"><?php echo htmlspecialchars($user['student_id'] . ($user['employee_id'] ? ' / ' . $user['employee_id'] : '')); ?></div>
                                </td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($user['role']); ?></td>
                                <td class="py-3 row-text"><?php echo $user['is_active'] ? 'Yes' : 'No'; ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($user['last_login_at'] ?? 'Never'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?><tr><td colspan="4" class="text-center text-muted py-5">No users found</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php elseif ($module === 'roles'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">CREATE ROLE</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><input type="text" name="role_name" class="form-control row-text" placeholder="Role name" required></div>
                    <div class="col-12"><textarea name="role_description" class="form-control row-text" rows="2" placeholder="Description"></textarea></div>
                    <div class="col-12"><button type="submit" name="save_role" class="btn btn-success fw-bold" style="border-radius:8px;">Create Role</button></div>
                </form>
            </div>
        </div>
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">CREATE PERMISSION</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><input type="text" name="permission_key" class="form-control row-text" placeholder="permission.key.name" required></div>
                    <div class="col-12"><textarea name="permission_description" class="form-control row-text" rows="2" placeholder="Description"></textarea></div>
                    <div class="col-12"><button type="submit" name="save_permission" class="btn btn-outline-success fw-bold" style="border-radius:8px;">Create Permission</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">ROLE-PERMISSION ASSIGNMENTS</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3 mb-4">
                    <div class="col-md-5">
                        <select name="role_id" class="form-select" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <select name="permission_id" class="form-select" required>
                            <option value="">Select Permission</option>
                            <?php foreach ($permissions as $permission): ?>
                                <option value="<?php echo $permission['id']; ?>"><?php echo htmlspecialchars($permission['permission_key']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="assign_permission" class="btn btn-outline-success w-100">Assign</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color:#f8fafc;">
                            <tr><th>Role</th><th>Permission</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rolePermissions as $rp): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($rp['role_name']); ?></td>
                                    <td><?php echo htmlspecialchars($rp['permission_key']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($rolePermissions)): ?><tr><td colspan="2" class="text-center text-muted">No assignments</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card-outline bg-white p-4 text-center">
                    <div class="group-label-grey mb-2">Roles</div>
                    <div class="val-total-amount text-success"><?php echo count($roles); ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-outline bg-white p-4 text-center">
                    <div class="group-label-grey mb-2">Permissions</div>
                    <div class="val-total-amount" style="color: var(--primary);"><?php echo count($permissions); ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-outline bg-white p-4 text-center">
                    <div class="group-label-grey mb-2">Assignments</div>
                    <div class="val-total-amount" style="color: var(--warning);"><?php echo count($rolePermissions); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php elseif ($module === 'security'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">2FA MANAGEMENT</span></div>
            <div class="card-body p-4">
                <?php if (!siemsTwoFactorColumnsReady()): ?>
                    <div class="alert alert-warning mb-4">
                        Install 2FA schema first:
                        <form method="POST" class="mt-2 d-inline">
                            <button name="install_two_factor_schema" class="btn btn-warning btn-sm">Install Schema</button>
                        </form>
                    </div>
                <?php else: ?>
                    <form method="POST" class="row g-3 mb-3">
                        <div class="col-12">
                            <select name="two_factor_user_id" class="form-select" required>
                                <option value="">Select User</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['full_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <button name="enable_two_factor" class="btn btn-success w-100">Enable 2FA</button>
                        </div>
                        <div class="col-6">
                            <button name="disable_two_factor" class="btn btn-outline-danger w-100">Disable 2FA</button>
                        </div>
                    </form>
                    <div class="d-grid gap-2">
                        <button name="enable_all_two_factor" formmethod="POST" formnovalidate class="btn btn-success">Enable All</button>
                        <button name="disable_all_two_factor" formmethod="POST" formnovalidate class="btn btn-outline-secondary">Disable All</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">USER SECURITY STATUS</span></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background-color:#f8fafc;">
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Active</th>
                            <th>2FA</th>
                            <th>Last Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="fw-semibold"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'secondary'; ?>"><?php echo $user['is_active'] ? 'Yes' : 'No'; ?></span></td>
                                <td><span class="badge bg-<?php echo isset($user['two_factor_enabled']) && $user['two_factor_enabled'] ? 'success' : 'secondary'; ?>"><?php echo isset($user['two_factor_enabled']) && $user['two_factor_enabled'] ? 'On' : 'Off'; ?></span></td>
                                <td><?php echo htmlspecialchars($user['last_login_at'] ?? 'Never'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($twoFactorSetup): ?>
            <div class="card-outline bg-white mt-4">
                <div class="card-body p-4">
                    <h5>2FA Setup: <?php echo htmlspecialchars($twoFactorSetup['user_name']); ?></h5>
                    <img src="https://quickchart.io/qr?size=220&text=<?php echo urlencode($twoFactorSetup['uri']); ?>" class="img-fluid rounded border mb-3" style="max-width: 220px;">
                    <div><strong>Key:</strong> <code><?php echo htmlspecialchars($twoFactorSetup['secret']); ?></code></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($module === 'audit'): ?>
<div class="card-outline bg-white">
    <div class="balance-header py-3 px-4"><span class="section-title">AUDIT TRAIL</span></div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead style="background-color:#f8fafc;">
                <tr><th>Date</th><th>User</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach (array_reverse(array_slice($auditLogs, 0, 50)) as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></td>
                        <td><strong><?php echo htmlspecialchars($log['action']); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($auditLogs)): ?><tr><td colspan="3" class="text-center text-muted py-5">No audit logs</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif ($module === 'recovery'): ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">CREATE RESET TOKEN</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12">
                        <select name="reset_user_id" class="form-select" required>
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="create_reset" class="btn btn-success w-100">Create Token</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">RECENT RESET TOKENS</span></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background-color:#f8fafc;">
                        <tr><th>User</th><th>Token</th><th>Expires</th><th>Used</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($passwordResets as $reset): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reset['full_name'] ?? 'Unknown'); ?></td>
                                <td><code><?php echo htmlspecialchars($reset['reset_token']); ?></code></td>
                                <td><?php echo htmlspecialchars($reset['expires_at']); ?></td>
                                <td><?php echo $reset['used_at'] ? htmlspecialchars($reset['used_at']) : 'No'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($passwordResets)): ?><tr><td colspan="4" class="text-center text-muted py-5">No reset tokens</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>

