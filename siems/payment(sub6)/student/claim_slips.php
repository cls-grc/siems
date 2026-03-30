<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$orders = array_values(array_filter(getStudentItemOrders($student_id), function ($order) {
    return in_array($order['order_status'], ['Paid', 'Claimed'], true);
}));

$page_title = 'Claim Slips';
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title"><i class="bi bi-ticket-perforated text-success me-2"></i>Claim Slips</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<div class="card-outline bg-white mb-5">
    <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="section-title">READY FOR CLAIM</span>
        <span class="badge bg-success rounded-pill px-3"><?php echo count($orders); ?></span>
    </div>
    <div class="card-body p-4">
        <?php if (empty($orders)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-ticket-perforated fs-1 d-block mb-3"></i>
                No claim slips available yet.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($orders as $order): ?>
                    <div class="col-lg-6">
                        <div class="card-outline bg-white h-100 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <div class="row-val fs-5"><?php echo htmlspecialchars($order['item_name']); ?></div>
                                    <div class="group-label-grey"><?php echo htmlspecialchars($order['category']); ?></div>
                                </div>
                                <span class="badge px-3 py-2" style="<?php echo $order['order_status'] === 'Claimed' ? 'background-color:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;' : 'background-color:#dcfce7;color:#166534;border:1px solid #bbf7d0;'; ?>">
                                    <?php echo htmlspecialchars(strtoupper($order['order_status'])); ?>
                                </span>
                            </div>

                            <div id="claim_<?php echo (int)$order['id']; ?>" class="p-4 rounded-3 mb-4" style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                                <div class="text-center mb-4">
                                    <h4 class="dashboard-title mb-1">Store Claim Slip</h4>
                                    <div class="group-label-grey">Present this at the cashier or releasing window.</div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 row-text"><strong>Student:</strong> <?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                                    <div class="col-md-6 row-text"><strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?></div>
                                    <div class="col-md-6 row-text"><strong>Claim Code:</strong> <?php echo htmlspecialchars($order['claim_code']); ?></div>
                                    <div class="col-md-6 row-text"><strong>Receipt:</strong> <?php echo htmlspecialchars($order['receipt_no'] ?? 'Pending'); ?></div>
                                    <div class="col-md-6 row-text"><strong>Item:</strong> <?php echo htmlspecialchars($order['item_name']); ?></div>
                                    <div class="col-md-6 row-text"><strong>Quantity:</strong> <?php echo (int)$order['quantity']; ?></div>
                                    <?php if (!empty($order['size_option'])): ?>
                                        <div class="col-md-6 row-text"><strong>Size:</strong> <?php echo htmlspecialchars($order['size_option']); ?></div>
                                    <?php endif; ?>
                                    <div class="col-md-6 row-text"><strong>Total:</strong> &#8369; <?php echo number_format($order['total_amount'], 2); ?></div>
                                    <div class="col-md-6 row-text"><strong>Paid On:</strong> <?php echo !empty($order['payment_date']) ? date('M j, Y', strtotime($order['payment_date'])) : 'Pending'; ?></div>
                                </div>
                            </div>

                            <?php if ($order['order_status'] === 'Paid'): ?>
                                <button class="btn btn-outline-success fw-bold w-100 print-slip" data-target="claim_<?php echo (int)$order['id']; ?>" style="border-radius: 8px;">
                                    <i class="bi bi-printer me-2"></i>Print Claim Slip
                                </button>
                            <?php else: ?>
                                <div class="text-center fw-bold text-primary py-2">Already claimed</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('.print-slip').forEach(function(button) {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const slip = document.getElementById(targetId);
        if (!slip) return;

        const printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Claim Slip</title>');
        printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">');
        printWindow.document.write('</head><body class="p-4 bg-white">');
        printWindow.document.write(slip.outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    });
});
</script>

<?php include '../includes/footer.php'; ?>
