<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireSubsystemAccess('payment_accounting');

$page_title = 'Payment & Accounting Dashboard';

// Stats
$total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();

// Collection should total all verified payment transactions regardless of payment method.
$collection_total = $pdo->query("SELECT COALESCE(SUM(amount_paid), 0) FROM payments WHERE verification_status = 'Verified'")->fetchColumn();

// Total transactions should reflect actual posted and verified payment records.
$total_receipts = $pdo->query("SELECT COUNT(*) FROM payments WHERE verification_status = 'Verified'")->fetchColumn();
$enrolled_count = 0;
foreach ($pdo->query("SELECT student_id FROM users WHERE role='student'")->fetchAll() as $stu) {
    if (checkEnrollmentStatus($stu['student_id'])) $enrolled_count++;
}
?>
<?php include '../includes/header.php'; ?>
<?php $is_super_admin = ($_SESSION['role'] ?? '') === 'admin'; ?>

<h3 class="dashboard-title mb-4 mt-2">
    <i class="bi bi-speedometer2 text-success me-2"></i>
    Payment & Accounting Dashboard
</h3>

<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4">
        <span class="section-title">QUICK ACTION BUTTONS</span>
    </div>
    <div class="card-body p-4">
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            <div class="col">
                <a href="fee_config.php" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-gear d-block fs-4 mb-2"></i>Fee Config
                </a>
            </div>
            <div class="col">
                <a href="validate_payments.php" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-shield-check d-block fs-4 mb-2"></i>Validate Payments
                </a>
            </div>
            <div class="col">
                <a href="post_payment.php" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-receipt d-block fs-4 mb-2"></i>Post Payment
                </a>
            </div>
            <div class="col">
                <a href="store_items.php" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-bag d-block fs-4 mb-2"></i>Store Items
                </a>
            </div>
            <div class="col">
                <a href="item_orders.php" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-box-seam d-block fs-4 mb-2"></i>Item Orders
                </a>
            </div>
            <div class="col">
                <a href="scholarships.php" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-award d-block fs-4 mb-2"></i>Scholarships
                </a>
            </div>
            <div class="col">
                <a href="reports.php" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-graph-up d-block fs-4 mb-2"></i>Reports
                </a>
            </div>
            <div class="col">
                <a href="audit_trail.php" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-clock-history d-block fs-4 mb-2"></i>Audit Trail
                </a>
            </div>
            <?php if ($is_super_admin): ?>
                <div class="col">
                    <a href="siems_hub.php" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                        <i class="bi bi-grid-1x2 d-block fs-4 mb-2"></i>SIEMS Hub
                    </a>
                </div>
                <div class="col">
                    <a href="user_management.php" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                        <i class="bi bi-shield-check d-block fs-4 mb-2"></i>User Management
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card-outline bg-white p-5 text-center mb-4">
    <i class="bi bi-grid-1x2 fs-1 text-success d-block mb-3"></i>
    <h4 class="dashboard-title mb-2">Subsystem 6 Module Launcher</h4>
    <div class="row-text">Choose a payment and accounting module above to open the existing subsystem 6 content.</div>
</div>

<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-1 mb-1" style="color: #6366f1;"><?php echo $total_students; ?></div>
            <div class="group-label-grey">TOTAL STUDENTS</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-2 mb-1 text-success mt-1">&#8369; <?php echo number_format($collection_total, 2); ?></div>
            <div class="group-label-grey mt-2">COLLECTION</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-1 mb-1" style="color: #0ea5e9;"><?php echo $total_receipts; ?></div>
            <div class="group-label-grey">TOTAL TRANSACTIONS</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-1 mb-1" style="color: #10b981;"><?php echo $enrolled_count; ?></div>
            <div class="group-label-grey">ENROLLED</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="section-title">RECENT TRANSACTIONS</span>
                <a href="post_payment.php" class="btn btn-outline-success btn-sm fw-bold" style="border-radius: 6px;">
                    <i class="bi bi-plus-circle me-1"></i> New Payment
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th class="group-label-grey py-3 px-4">RECEIPT</th>
                            <th class="group-label-grey py-3">STUDENT</th>
                            <th class="group-label-grey py-3">METHOD</th>
                            <th class="group-label-grey py-3">AMOUNT</th>
                            <th class="group-label-grey py-3 px-4 text-end">DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "
                            SELECT t.*, u.full_name 
                            FROM (
                                SELECT 
                                    receipt_no, 
                                    student_id, 
                                    payment_method,
                                    amount_paid, 
                                    payment_date as transaction_date, 
                                    'Payment' as type
                                FROM payments
                                WHERE verification_status = 'Verified'
                                UNION ALL
                                SELECT 
                                    'SCHOLARSHIP' as receipt_no, 
                                    student_id, 
                                    'Scholarship' as payment_method,
                                    discount_amount as amount_paid, 
                                    assessed_at as transaction_date, 
                                    'Scholarship' as type
                                FROM student_assessments
                                WHERE discount_amount > 0
                            ) t
                            JOIN users u ON t.student_id = u.student_id
                            ORDER BY t.transaction_date DESC
                            LIMIT 10
                        ";
                        $recent = $pdo->query($query)->fetchAll();
                        foreach ($recent as $tx): ?>
                            <tr>
                                <td class="py-3 px-4">
                                    <?php if ($tx['type'] === 'Scholarship'): ?>
                                        <span class="badge" style="background-color: #fef3c7; color: #d97706;"><i class="bi bi-award me-1"></i> Scholarship</span>
                                    <?php else: ?>
                                        <span class="row-val"><?php echo htmlspecialchars($tx['receipt_no']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($tx['full_name']); ?></td>
                                <td class="py-3">
                                    <span class="badge" style="background-color: <?php echo $tx['type'] === 'Scholarship' ? '#fef3c7' : ($tx['payment_method'] === 'Cash' ? '#dcfce7' : '#dbeafe'); ?>; color: <?php echo $tx['type'] === 'Scholarship' ? '#d97706' : ($tx['payment_method'] === 'Cash' ? '#166534' : '#1d4ed8'); ?>;">
                                        <?php echo htmlspecialchars($tx['payment_method'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td class="py-3 <?php echo $tx['type'] === 'Scholarship' ? 'text-success fw-bold' : 'row-val'; ?>">
                                    &#8369; <?php echo number_format($tx['amount_paid'], 2); ?>
                                </td>
                                <td class="py-3 px-4 text-end row-text"><?php echo date('M j, Y', strtotime($tx['transaction_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-outline bg-white h-100">
            <div class="balance-header py-3 px-4">
                <span class="section-title">MODULE NOTES</span>
            </div>
            <div class="card-body p-4">
                <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;">
                    <div class="group-label-grey">PAYMENT FLOW</div>
                    <div class="row-text mt-2">Use `Post Payment`, `Validate Payments`, and `Reports` as the main cashiering workflow for subsystem 6.</div>
                </div>
                <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;">
                    <div class="group-label-grey">STORE & AUXILIARY SALES</div>
                    <div class="row-text mt-2">Manage `Store Items` and `Item Orders` here without changing the underlying subsystem pages.</div>
                </div>
                <div class="border rounded-3 p-3" style="border-color: #e2e8f0 !important;">
                    <div class="group-label-grey">DISCOUNTS & CONTROLS</div>
                    <div class="row-text mt-2">Use `Scholarships`, `Fee Config`, and `Audit Trail` for financial controls and review.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

