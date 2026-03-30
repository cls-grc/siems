<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$soa = getStudentSOA($student_id);
$balance = getStudentBalance($student_id);

// Fetch student details
$stmt = $pdo->prepare("SELECT program, year_level FROM users WHERE student_id = ?");
$stmt->execute([$student_id]);
$student_info = $stmt->fetch();
$program = $student_info['program'] ?? 'BSIT';
$year_level = $student_info['year_level'] ?? 1;

// Get assessment breakdown from DB directly (matching SOA)
$stmt = $pdo->prepare("SELECT total_tuition, total_misc, grand_total FROM student_assessments WHERE student_id = ? ORDER BY assessed_at DESC LIMIT 1");
$stmt->execute([$student_id]);
$assessment_row = $stmt->fetch();
$total_tuition = $assessment_row['total_tuition'] ?? 0;
$total_misc = $assessment_row['total_misc'] ?? 0;
$total_assessment = $assessment_row['grand_total'] ?? 0;

// Recent transactions
$stmt = $pdo->prepare("SELECT * FROM payments WHERE student_id = ? ORDER BY payment_date DESC LIMIT 5");
$stmt->execute([$student_id]);
$recent_transactions = $stmt->fetchAll();

// Notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE student_id = ? ORDER BY id DESC LIMIT 3");
$stmt->execute([$student_id]);
$notifications = $stmt->fetchAll();

$page_title = 'Payment & Accounting Dashboard';
?>
<?php include '../includes/header.php'; ?>



<div class="mb-4 mt-1">
    <h3 class="dashboard-title">Payment & Accounting Dashboard</h3>
</div>

<?php if (!empty($notifications)): ?>
<div class="mb-4">
    <span class="group-label-grey mb-2 d-block">RECENT NOTIFICATIONS</span>
    <?php foreach ($notifications as $notif): ?>
        <div class="alert d-flex align-items-center mb-2 shadow-sm" style="border-left: 4px solid #0ea5e9; background-color: #f0f9ff; border-radius: 8px; color: #0369a1; border-color: #e0f2fe;">
            <i class="bi bi-bell-fill me-3 fs-5 text-info"></i>
            <div>
                <strong><?php echo htmlspecialchars($notif['type'] ?? 'System'); ?> Notification:</strong> <?php echo htmlspecialchars($notif['message']); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="row g-4 mb-5">
    <!-- MY BALANCE Card -->
    <div class="col-lg-5">
        <div class="h-100 card-outline d-flex flex-column">
            <div class="balance-header py-3 text-center">
                <span class="balance-header-text">MY BALANCE</span>
            </div>
            <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center py-5 bg-white" style="border-radius: 0 0 12px 12px;">
                <div class="balance-amount">&#8369; <?php echo number_format($balance, 2); ?></div>
                <div class="balance-subtext mt-2">OUTSTANDING AMOUNT</div>
            </div>
        </div>
    </div>

    <!-- BALANCE BREAKDOWN Card -->
    <div class="col-lg-7">
        <div class="h-100 card-outline bg-white p-4 p-md-5">
            <div class="text-center mb-4">
                <span class="section-title">BALANCE BREAKDOWN</span>
            </div>
            
            <div class="mb-2">
                <span class="group-label-grey">STUDENT FINANCIAL STATUS</span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="row-text">Account Status</span>
                <span class="val-active"><?php echo $soa['enrolled'] ? 'ACTIVE' : 'PENDING'; ?></span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="row-text">Balance</span>
                <span class="row-val">&#8369; <?php echo number_format($balance, 2); ?></span>
            </div>
            
            <div class="divider"></div>
            
            <div class="mb-2">
                <span class="group-label-green">STANDARD ALLOCATION</span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="row-text">Balance</span>
                <span class="row-val"><?php echo number_format($balance, 2); ?></span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="row-text">Active Tuition</span>
                <span class="row-val"><?php echo number_format($total_tuition, 2); ?></span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="row-text">Misc Balance</span>
                <span class="row-val"><?php echo number_format($total_misc, 2); ?></span>
            </div>
            
            <div class="divider"></div>
            
            <div class="d-flex justify-content-between align-items-center mt-3 pt-1">
                <div>
                    <div class="val-total-label">TOTAL PAYABLE:</div>
                    <div class="val-total-sub mt-1">Due today</div>
                </div>
                <div class="val-total-amount">&#8369; <?php echo number_format($balance, 2); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="mb-3 mt-5">
    <h6 class="section-title">QUICK ACCESS</h6>
</div>
<div class="row g-2 quick-access-row">
    <!-- Payment -->
    <div class="col-6 col-md-4 col-lg">
        <a href="pay_online.php" class="text-decoration-none">
            <div class="quick-card quick-access-card p-3 text-center h-100">
                <i class="bi bi-wallet2 quick-icon quick-access-icon"></i>
                <span class="quick-text">Payment</span>
            </div>
        </a>
    </div>
    <!-- Statement of Account -->
    <div class="col-6 col-md-4 col-lg">
        <a href="soa.php" class="text-decoration-none">
            <div class="quick-card quick-access-card p-3 text-center h-100">
                <i class="bi bi-file-text quick-icon quick-access-icon"></i>
                <span class="quick-text">SOA</span>
            </div>
        </a>
    </div>
    <!-- Transaction History -->
    <div class="col-6 col-md-4 col-lg">
        <a href="receipts.php" class="text-decoration-none">
            <div class="quick-card quick-access-card p-3 text-center h-100">
                <i class="bi bi-file-earmark-text quick-icon quick-access-icon"></i>
                <span class="quick-text">Transaction History</span>
            </div>
        </a>
    </div>
    <!-- Payment Validation -->
    <div class="col-6 col-md-4 col-lg">
        <a href="validate_payment.php" class="text-decoration-none">
            <div class="quick-card quick-access-card p-3 text-center h-100">
                <i class="bi bi-patch-check quick-icon quick-access-icon"></i>
                <span class="quick-text">Payment Validation</span>
            </div>
        </a>
    </div>
    <!-- Document Request -->
    <div class="col-6 col-md-4 col-lg">
        <a href="documents_credentials.php" class="text-decoration-none">
            <div class="quick-card quick-access-card p-3 text-center h-100">
                <i class="bi bi-file-earmark-plus quick-icon quick-access-icon"></i>
                <span class="quick-text">Documents</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="store.php" class="text-decoration-none">
            <div class="quick-card quick-access-card p-3 text-center h-100">
                <i class="bi bi-bag-check quick-icon quick-access-icon"></i>
                <span class="quick-text">Uniforms & Books</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="claim_slips.php" class="text-decoration-none">
            <div class="quick-card quick-access-card p-3 text-center h-100">
                <i class="bi bi-ticket-perforated quick-icon quick-access-icon"></i>
                <span class="quick-text">Claim Slips</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="grades_assessment.php" class="text-decoration-none">
            <div class="quick-card quick-access-card p-3 text-center h-100">
                <i class="bi bi-clipboard-data quick-icon quick-access-icon"></i>
                <span class="quick-text">Grades</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="clinic_medical.php" class="text-decoration-none">
            <div class="quick-card quick-access-card p-3 text-center h-100">
                <i class="bi bi-heart-pulse quick-icon quick-access-icon"></i>
                <span class="quick-text">Clinic</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="siems_portal.php" class="text-decoration-none">
            <div class="quick-card quick-access-card p-3 text-center h-100">
                <i class="bi bi-grid-1x2 quick-icon quick-access-icon"></i>
                <span class="quick-text">SIEMS Portal</span>
            </div>
        </a>
    </div>
</div>

<style>
.quick-access-row .col-lg {
    flex: 1 0 0;
}

.quick-access-card {
    min-height: 140px;
}

.quick-access-icon {
    font-size: 2rem;
    margin-bottom: 0.75rem;
}

.quick-access-card .quick-text {
    font-size: 0.9rem;
    line-height: 1.2;
}
</style>

<?php include '../includes/footer.php'; ?>

