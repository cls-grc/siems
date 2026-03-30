<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$stmt = $pdo->prepare("SELECT * FROM payments WHERE student_id = ? ORDER BY payment_date DESC");
$stmt->execute([$student_id]);
$receipts = $stmt->fetchAll();

$page_title = 'Payment Receipts';
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-receipt text-info me-2"></i>Payment Receipts
    </h1>
    <a href="dashboard.php" class="btn btn-outline-primary">
        <i class="bi bi-house-door"></i> Dashboard
    </a>
</div>

<div class="row">
    <?php foreach ($receipts as $receipt): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card-outline bg-white h-100 p-4 text-center">
                <div class="mb-3 mt-2">
                    <i class="bi bi-receipt quick-icon d-inline-block" style="font-size: 3rem;"></i>
                </div>
                <h5 class="dashboard-title fs-5 mb-1"><?php echo htmlspecialchars($receipt['receipt_no']); ?></h5>
                <p class="row-text mb-2 ms-0" style="font-size: 0.8rem;"><?php echo date('F j, Y g:i A', strtotime($receipt['payment_date'])); ?></p>
                <div class="val-total-amount mb-3 fs-3">&#8369; <?php echo number_format($receipt['amount_paid'], 2); ?></div>
                <div class="mb-3">
                    <span class="badge <?php echo ($receipt['verification_status'] ?? 'Verified') === 'Verified' ? 'bg-success' : (($receipt['verification_status'] ?? '') === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark'); ?>">
                        <?php echo htmlspecialchars($receipt['verification_status'] ?? 'Verified'); ?>
                    </span>
                </div>
                <?php if (!empty($receipt['reference_number'])): ?>
                    <p class="row-text mb-2" style="font-size: 0.8rem;">Receipt No: <?php echo htmlspecialchars($receipt['reference_number']); ?></p>
                <?php endif; ?>
                <?php if ($receipt['remarks']): ?>
                    <p class="row-text fst-italic shadow-none border-0 mb-3" style="font-size: 0.8rem;"><?php echo htmlspecialchars($receipt['remarks']); ?></p>
                <?php endif; ?>
                <?php if (!empty($receipt['proof_image'])): ?>
                    <a href="../<?php echo htmlspecialchars($receipt['proof_image']); ?>" target="_blank" class="btn btn-outline-primary fw-bold w-100 mb-2" style="border-radius: 8px; border-width: 2px;">
                        <i class="bi bi-image me-1"></i> View Proof
                    </a>
                <?php endif; ?>
                <?php if (($receipt['verification_status'] ?? 'Verified') === 'Verified'): ?>
                    <button class="btn btn-outline-success fw-bold w-100 mt-auto" style="border-radius: 8px; border-width: 2px;" onclick="printReceipt('<?php echo $receipt['receipt_no']; ?>')">
                        <i class="bi bi-printer me-1"></i> Print Receipt
                    </button>
                <?php else: ?>
                    <?php if (($receipt['verification_status'] ?? '') === 'Pending'): ?>
                        <a href="validate_payment.php?id=<?php echo $receipt['id']; ?>" class="btn btn-outline-warning fw-bold w-100 mb-2" style="border-radius: 8px; border-width: 2px;">
                            <i class="bi bi-upload me-1"></i> Submit Receipt Details
                        </a>
                    <?php endif; ?>
                    <div class="row-text mt-auto" style="font-size: 0.8rem;">
                        <?php echo ($receipt['verification_status'] ?? '') === 'Rejected' ? 'This payment was rejected by admin.' : 'Waiting for admin validation.'; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($receipts)): ?>
<div class="text-center py-5">
    <i class="bi bi-receipt-slash display-1 text-muted mb-3"></i>
    <h4>No payments found</h4>
    <p class="text-muted">Your payment history will appear here.</p>
</div>
<?php endif; ?>

<script>
function printReceipt(receiptNo) {
    window.print();
}
</script>

<?php include '../includes/footer.php'; ?>
