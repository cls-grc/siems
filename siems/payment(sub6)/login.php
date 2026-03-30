<?php
$page_title = 'Login - SIEMS';
require_once 'config/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if (isset($_SESSION['user_id'])) {
    $dashboard = resolveUserHomePath($_SESSION['student_id'] ?? '', $_SESSION['role'] ?? '');

    if ($dashboard !== null && $dashboard !== 'login.php') {
        header("Location: " . $dashboard);
        exit;
    }

    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    $_SESSION['message'] = 'Your session was reset because its home page could not be resolved.';
    $_SESSION['msg_type'] = 'warning';
}
?>
<?php include 'includes/header.php'; ?>

<div class="row justify-content-center align-items-center">
    <div class="col-md-6 col-lg-5">
        <div class="card-outline bg-white mb-5 mt-3 mt-md-5" style="border-width: 2px !important; box-shadow: 0 10px 15px -3px rgba(34,197,94,0.1), 0 4px 6px -2px rgba(34,197,94,0.05) !important;">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 80px; height: 80px;">
                        <i class="bi bi-mortarboard-fill fs-1"></i>
                    </div>
                </div>
                <h3 class="dashboard-title mb-2 text-dark" style="font-size: 1.5rem;">System Login</h3>
                <p class="row-text text-muted mb-5">Enter your assigned account to access SIEMS.</p>

                <form method="POST" action="process_login.php" class="text-start">
                    <div class="mb-4">
                        <label class="form-label row-val mb-2">Student ID / Staff ID</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-success" style="border: 2px solid #e2e8f0; border-right: none;"><i class="bi bi-person-badge fs-5"></i></span>
                            <input type="text" class="form-control form-control-lg row-text" name="student_id" required
                                   placeholder="e.g. 2024001 or SUB1ADMIN" autofocus style="border: 2px solid #e2e8f0; border-left: none; box-shadow: none;">
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="form-label row-val mb-2">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-success" style="border: 2px solid #e2e8f0; border-right: none;"><i class="bi bi-lock fs-5"></i></span>
                            <input type="password" class="form-control form-control-lg row-text" name="password" required style="border: 2px solid #e2e8f0; border-left: none; box-shadow: none;" placeholder="••••••••">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100 mb-4 fw-bold shadow-sm" style="border-radius: 8px;">
                        Login to Account <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="text-center pt-3" style="border-top: 2px dashed #e2e8f0;">
                    <small class="group-label-grey d-block mb-3">ASSIGNED ACCESS ONLY</small>
                    <div class="d-flex flex-column gap-2 row-text">
                        <div>Super Admin: <strong>ADMIN001</strong></div>
                        <div>Subsystem Admin Example: <strong>SUB1ADMIN</strong></div>
                        <div>Student Example: <strong>2024001</strong></div>
                        <div class="text-muted mt-1">Password: <code>password</code></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
