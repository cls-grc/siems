<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/auth.php';

requireSubsystemAccess('documents_credentials');

$requestId = (int) ($_GET['request_id'] ?? $_POST['request_id'] ?? 0);
$request = $requestId > 0 ? $pdo->query("SELECT dr.*, u.full_name FROM document_requests dr LEFT JOIN users u ON u.student_id = dr.student_id WHERE dr.id = $requestId")->fetch() : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_status']) && $request) {
    $paymentStatus = trim($_POST['payment_status']);
    $receiptNo = trim($_POST['receipt_no'] ?? '');

    // siemsLogSubsystemEvent('Document Requests', 'Payment status updated manually', $request['student_id'], null, "Request #$requestId to $paymentStatus");

    $stmt = $pdo->prepare("UPDATE document_requests SET payment_status = ?, payment_receipt_no = ? WHERE id = ?");
    $stmt->execute([$paymentStatus, $receiptNo, $requestId]);

    $_SESSION['message'] = 'Payment marked as ' . ucfirst($paymentStatus) . ' successfully.';
    $_SESSION['msg_type'] = 'success';
    header('Location: document_requests.php?module=processing');
    exit;
}

$page_title = 'Mark Document Payment';
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="dashboard-title">Mark Payment Status <small class="text-muted">(Request #<?php echo $requestId; ?>)</small></h3>
    <a href="document_requests.php?module=processing" class="btn btn-outline-secondary">← Back to Processing</a>
</div>

<?php if (!$request): ?>
    <div class="alert alert-warning">Invalid or missing request ID.</div>
<?php else: ?>
    <div class="card-outline bg-white">
        <div class="card-body p-4">
            <h5>Student: <?php echo htmlspecialchars($request['full_name'] ?? $request['student_id']); ?></h5>
            <p class="row-text"><strong>Document:</strong> <?php echo htmlspecialchars($request['document_type']); ?></p>
            <p class="row-text"><strong>Current Payment:</strong> <?php echo htmlspecialchars($request['payment_status'] ?? 'Pending'); ?></p>
            
            <form method="POST" class="row g-3">
                <input type="hidden" name="request_id" value="<?php echo (int) $requestId; ?>">
                <div class="col-md-6">
                    <label class="form-label row-text">Payment Status</label>
                    <select name="payment_status" class="form-select" required style="border-radius:8px;">
                        <option value="Pending" <?php echo ($request['payment_status'] ?? '') === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Paid" <?php echo ($request['payment_status'] ?? '') === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="Verified" <?php echo ($request['payment_status'] ?? '') === 'Verified' ? 'selected' : ''; ?>>Verified</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label row-text">Receipt No. (optional)</label>
                    <input type="text" name="receipt_no" class="form-control" value="<?php echo htmlspecialchars($request['payment_receipt_no'] ?? ''); ?>" placeholder="Enter receipt number" style="border-radius:8px;">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success fw-bold" style="border-radius:8px;">Update Payment Status</button>
                    <a href="document_requests.php?module=processing" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>



?>

