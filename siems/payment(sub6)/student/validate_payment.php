<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'])) {
    $payment_id = intval($_POST['payment_id']);
    $reference_number = trim($_POST['reference_number'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ? AND student_id = ? AND verification_status = 'Pending'");
    $stmt->execute([$payment_id, $student_id]);
    $payment = $stmt->fetch();

    if (!$payment) {
        $_SESSION['message'] = 'Pending payment record not found.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: validate_payment.php');
        exit;
    }

    if ($reference_number === '') {
        $_SESSION['message'] = 'Receipt number is required.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: validate_payment.php?id=' . urlencode((string)$payment_id));
        exit;
    }

    $stmt = $pdo->prepare("UPDATE payments SET reference_no = ? WHERE id = ? AND student_id = ?");
    $stmt->execute([$reference_number, $payment_id, $student_id]);

    $_SESSION['message'] = 'Receipt details submitted. Your payment is now ready for admin validation.';
    $_SESSION['msg_type'] = 'success';
    header('Location: validate_payment.php?id=' . urlencode((string)$payment_id));
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM payments WHERE student_id = ? AND verification_status = 'Pending' ORDER BY payment_date DESC");
$stmt->execute([$student_id]);
$pending_payments = $stmt->fetchAll();

$selected_payment = null;
if ($payment_id > 0) {
    foreach ($pending_payments as $payment) {
        if ((int)$payment['id'] === $payment_id) {
            $selected_payment = $payment;
            break;
        }
    }
}

if (!$selected_payment && !empty($pending_payments)) {
    $selected_payment = $pending_payments[0];
}

$page_title = 'Payment Validation';
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title">
        <i class="bi bi-patch-check text-success me-2"></i>
        Submit Payment Validation
    </h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<?php if (empty($pending_payments)): ?>
    <div class="card-outline bg-white p-5 text-center">
        <i class="bi bi-check2-circle text-success mb-3" style="font-size: 4rem;"></i>
        <h4 class="dashboard-title">No pending online payments</h4>
        <p class="row-text mt-2">Once you submit an online payment, you can enter the receipt number here.</p>
        <a href="pay_online.php" class="btn btn-success mt-3 px-4 fw-bold" style="border-radius: 8px;">Create Payment</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card-outline bg-white h-100">
                <div class="balance-header py-3 px-4">
                    <span class="section-title">PENDING PAYMENTS</span>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($pending_payments as $payment): ?>
                        <?php $is_selected = $selected_payment && (int)$selected_payment['id'] === (int)$payment['id']; ?>
                        <a href="validate_payment.php?id=<?php echo $payment['id']; ?>" class="list-group-item list-group-item-action py-3 px-4 <?php echo $is_selected ? 'active border-0' : ''; ?>" style="<?php echo $is_selected ? 'background-color: #16a34a; color: white;' : 'border-color: #e2e8f0;'; ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($payment['receipt_no']); ?></div>
                                    <div class="small <?php echo $is_selected ? 'text-white-50' : 'text-muted'; ?>">
                                        <?php echo date('M j, Y h:i A', strtotime($payment['payment_date'])); ?>
                                    </div>
                                </div>
                                <div class="fw-bold">&#8369; <?php echo number_format($payment['amount_paid'], 2); ?></div>
                            </div>
                            <div class="small mt-2 <?php echo $is_selected ? 'text-white-50' : 'text-muted'; ?>">
                                <?php echo !empty($payment['reference_no']) ? 'Receipt details submitted' : 'Waiting for receipt details'; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card-outline bg-white">
                <div class="balance-header py-3 px-4">
                    <span class="section-title">SUBMIT RECEIPT DETAILS</span>
                </div>
                <?php if ($selected_payment): ?>
                    <div class="card-body p-4 p-md-5">
                        <div class="mb-4 p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="row-text">Receipt No.</span>
                                <span class="row-val"><?php echo htmlspecialchars($selected_payment['receipt_no']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="row-text">Payment Method</span>
                                <span class="row-val"><?php echo htmlspecialchars($selected_payment['payment_method']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="row-text">Amount</span>
                                <span class="row-val text-success">&#8369; <?php echo number_format($selected_payment['amount_paid'], 2); ?></span>
                            </div>
                        </div>

                        <form action="validate_payment.php?id=<?php echo $selected_payment['id']; ?>" method="POST">
                            <input type="hidden" name="payment_id" value="<?php echo $selected_payment['id']; ?>">

                            <div class="mb-4">
                                <label class="form-label row-val">Receipt Number</label>
                                <input type="text" name="reference_number" class="form-control form-control-lg row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;" required value="<?php echo htmlspecialchars($selected_payment['reference_no'] ?? ''); ?>" placeholder="e.g. 10023456789">
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold py-3" style="border-radius: 8px;">
                                <i class="bi bi-upload me-2"></i><?php echo !empty($selected_payment['reference_no']) ? 'Update Receipt Number' : 'Submit for Validation'; ?>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
