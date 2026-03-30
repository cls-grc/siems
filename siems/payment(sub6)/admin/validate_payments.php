<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireSubsystemAccess('payment_accounting');

$page_title = 'Validate Payments';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'], $_POST['action'])) {
    $payment_id = (int)$_POST['payment_id'];
    $action = $_POST['action'];

    $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ? AND verification_status = 'Pending'");
    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch();

    if ($payment) {
        $student_id = $payment['student_id'];
        $is_document_request_payment = !empty($payment['remarks']) && preg_match('/\[DOCREQ:(\d+)\]/', $payment['remarks']);
        $is_item_order_payment = !empty($payment['remarks']) && preg_match('/\[ITEMORDER:(\d+)\]/', $payment['remarks']);
        $ready_for_validation = $is_document_request_payment || !empty($payment['reference_no']);

        if (!$ready_for_validation) {
            $_SESSION['message'] = 'Student receipt details are still incomplete for this payment.';
            $_SESSION['msg_type'] = 'warning';
            header('Location: validate_payments.php');
            exit;
        }

        $status = $action === 'Approve' ? 'Verified' : 'Rejected';

        $stmt = $pdo->prepare("UPDATE payments SET verification_status = ? WHERE id = ?");
        $stmt->execute([$status, $payment_id]);

        if ($status === 'Verified') {
            calculateAssessment($student_id);
            logAudit($_SESSION['user_id'], $student_id, 'Payment Verified', 'Pending', 'Verified (Receipt: ' . $payment['receipt_no'] . ')');

            if ($is_document_request_payment && preg_match('/\[DOCREQ:(\d+)\]/', $payment['remarks'], $matches)) {
                $request_id = (int)$matches[1];
                $stmt = $pdo->prepare("UPDATE document_requests SET payment_status = 'Verified', payment_receipt_no = ? WHERE id = ? AND student_id = ?");
                $stmt->execute([$payment['receipt_no'], $request_id, $student_id]);
                logAudit($_SESSION['user_id'], $student_id, 'Document Payment Validated', 'Pending', 'Verified (Request #' . $request_id . ')');
            }

            if ($is_item_order_payment && preg_match('/\[ITEMORDER:(\d+)\]/', $payment['remarks'], $matches)) {
                $order_id = (int)$matches[1];
                $stmt = $pdo->prepare("UPDATE item_orders SET order_status = 'Paid', payment_id = ? WHERE id = ? AND student_id = ?");
                $stmt->execute([$payment_id, $order_id, $student_id]);
                logAudit($_SESSION['user_id'], $student_id, 'Store Order Validated', 'Pending Payment', 'Paid (Order #' . $order_id . ')');
            }

            $msg = 'Your payment of P' . number_format($payment['amount_paid'], 2) . ' via ' . $payment['payment_method'] . ' has been verified.';
            if ($is_item_order_payment) {
                $msg .= ' Your claim slip is now ready in the Uniforms & Books page.';
            }
            $pdo->prepare("INSERT INTO notifications (student_id, type, message) VALUES (?, 'Payment', ?)")->execute([$student_id, $msg]);

            $_SESSION['message'] = 'Payment approved and updated successfully.';
            $_SESSION['msg_type'] = 'success';
        } else {
            logAudit($_SESSION['user_id'], $student_id, 'Payment Rejected', 'Pending', 'Rejected (Receipt: ' . $payment['receipt_no'] . ')');

            if ($is_item_order_payment && preg_match('/\[ITEMORDER:(\d+)\]/', $payment['remarks'], $matches)) {
                $order_id = (int)$matches[1];
                $stmt = $pdo->prepare("UPDATE item_orders SET order_status = 'Payment Rejected' WHERE id = ? AND student_id = ?");
                $stmt->execute([$order_id, $student_id]);
            }

            $msg = 'Your payment of P' . number_format($payment['amount_paid'], 2) . ' via ' . $payment['payment_method'] . ' was rejected. Please resubmit your receipt details if needed.';
            $pdo->prepare("INSERT INTO notifications (student_id, type, message) VALUES (?, 'Payment', ?)")->execute([$student_id, $msg]);

            $_SESSION['message'] = 'Payment validation rejected.';
            $_SESSION['msg_type'] = 'warning';
        }
    }

    header('Location: validate_payments.php');
    exit;
}

$stmt = $pdo->query("
    SELECT p.*, u.full_name, u.program
    FROM payments p
    JOIN users u ON p.student_id = u.student_id
    WHERE p.verification_status = 'Pending'
    ORDER BY p.payment_date ASC
");
$pending_payments = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title">
        <i class="bi bi-shield-check text-warning me-2"></i>
        Validate Pending Payments
    </h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<div class="card-outline bg-white mb-5">
    <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="section-title">PENDING VERIFICATION</span>
        <span class="badge bg-warning text-dark rounded-pill shadow-sm"><?php echo count($pending_payments); ?> Pending</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
            <thead style="background-color: #f8fafc;">
                <tr>
                    <th class="group-label-grey py-3 px-4">DATE & RECEIPT</th>
                    <th class="group-label-grey py-3">STUDENT</th>
                    <th class="group-label-grey py-3">PAYMENT INFO</th>
                    <th class="group-label-grey py-3 px-4 text-end">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_payments as $pay): ?>
                    <?php $is_document_request_payment = !empty($pay['remarks']) && preg_match('/\[DOCREQ:(\d+)\]/', $pay['remarks']); ?>
                    <?php $is_item_order_payment = !empty($pay['remarks']) && preg_match('/\[ITEMORDER:(\d+)\]/', $pay['remarks']); ?>
                    <?php $ready_for_validation = $is_document_request_payment || !empty($pay['reference_no']); ?>
                    <tr>
                        <td class="py-3 px-4">
                            <div class="row-val text-primary"><?php echo htmlspecialchars($pay['receipt_no']); ?></div>
                            <div class="text-muted small"><?php echo date('M j, Y h:i A', strtotime($pay['payment_date'])); ?></div>
                        </td>
                        <td class="py-3">
                            <div class="row-text fw-bold"><?php echo htmlspecialchars($pay['full_name']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($pay['student_id']); ?> - <?php echo htmlspecialchars($pay['program']); ?></div>
                        </td>
                        <td class="py-3">
                            <div class="row-val text-success">&#8369;<?php echo number_format($pay['amount_paid'], 2); ?></div>
                            <div class="text-muted small">
                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($pay['payment_method']); ?></span>
                                <?php if ($is_document_request_payment): ?>
                                    Document payment request
                                <?php elseif ($is_item_order_payment): ?>
                                    Store item order
                                <?php else: ?>
                                    Receipt No.: <?php echo htmlspecialchars($pay['reference_no'] ?: 'Awaiting student'); ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($pay['remarks'])): ?>
                                <div class="small text-muted mt-1"><?php echo htmlspecialchars($pay['remarks']); ?></div>
                            <?php endif; ?>
                            <?php if (!$ready_for_validation): ?>
                                <div class="small text-warning mt-1">Waiting for receipt number.</div>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-end">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="payment_id" value="<?php echo $pay['id']; ?>">
                                <button type="submit" name="action" value="Approve" class="btn btn-sm btn-success fw-bold me-1" style="border-radius: 6px;" onclick="return confirm('Approve this payment?');" <?php echo $ready_for_validation ? '' : 'disabled'; ?>>
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                                <button type="submit" name="action" value="Reject" class="btn btn-sm btn-danger fw-bold" style="border-radius: 6px;" onclick="return confirm('Reject this payment?');" <?php echo $ready_for_validation ? '' : 'disabled'; ?>>
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($pending_payments)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-check2-circle fs-1 d-block mb-3 text-success opacity-50"></i>
                            <span class="fs-5">All caught up!</span><br>
                            No pending payments to validate.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
