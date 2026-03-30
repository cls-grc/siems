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

// Get detailed assessment breakdown
$stmt = $pdo->prepare("SELECT fc.fee_name, fc.amount, sa.* FROM student_assessments sa JOIN fee_configs fc ON 1=1 WHERE sa.student_id = ? ORDER BY sa.id DESC LIMIT 1");
$stmt->execute([$student_id]);
$assessment_details = $stmt->fetchAll();

// Only school-account payments should affect the SOA balance.
$stmt = $pdo->prepare("
    SELECT *
    FROM payments
    WHERE student_id = ?
      AND verification_status = 'Verified'
      AND (
          remarks IS NULL
          OR (
              remarks NOT LIKE 'Document Request Payment [DOCREQ:%'
              AND remarks NOT LIKE 'Store Purchase [ITEMORDER:%'
          )
      )
    ORDER BY payment_date ASC
");
$stmt->execute([$student_id]);
$all_payments = $stmt->fetchAll();

$page_title = 'Statement of Account';
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title"><i class="bi bi-file-earmark-text text-success me-2"></i>Statement of Account</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card-outline bg-white mb-5">
            <div class="balance-header py-4 text-center">
                <h4 class="dashboard-title mb-1"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h4>
                <p class="mb-1 group-label-grey" style="font-size: 0.85rem;"><?php echo $student_id; ?> | <?php echo $_SESSION['program']; ?> Year <?php echo $pdo->query("SELECT year_level FROM users WHERE student_id='$student_id'")->fetchColumn(); ?></p>
                <div class="mt-2"><span class="group-label-green px-3 py-1" style="background:#e8f5e9; border-radius:12px;">SEMESTER 2024-1</span></div>
            </div>
            <div class="card-body p-4 p-md-5">
                <div class="row text-center mb-4">
                    <div class="col-md-4 py-3" style="border-right: 1px solid #e2e8f0;">
                        <div class="val-total-amount fs-2">&#8369; <?php echo number_format($soa['total_paid'], 2); ?></div>
                        <div class="group-label-grey mt-2">TOTAL PAID</div>
                    </div>
                    <div class="col-md-4 py-3" style="border-right: 1px solid #e2e8f0;">
                        <div class="fs-2 mb-1">
                            <?php echo $soa['enrolled'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>'; ?>
                        </div>
                        <div class="group-label-grey mt-2"><?php echo $soa['enrolled'] ? 'OFFICIALLY ENROLLED' : 'PENDING ENROLLMENT'; ?></div>
                    </div>
                    <div class="col-md-4 py-3">
                        <div class="val-total-amount fs-2 text-danger">&#8369; <?php echo number_format($balance, 2); ?></div>
                        <div class="group-label-grey mt-2">BALANCE DUE</div>
                    </div>
                </div>
                
                <div class="divider mb-4"></div>
                
                <div class="row g-5">
                    <div class="col-md-6">
                        <h6 class="section-title mb-3">ASSESSMENT BREAKDOWN</h6>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="row-text">Tuition Fee</span>
                            <span class="row-val">&#8369; <?php echo number_format($soa['assessment']['total_tuition'] ?? 0, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="row-text">Miscellaneous Fees</span>
                            <span class="row-val">&#8369; <?php echo number_format($soa['assessment']['total_misc'] ?? 0, 2); ?></span>
                        </div>
                        <?php if (($soa['assessment']['total_registration'] ?? 0) > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="row-text">Registration Fee</span>
                            <span class="row-val">&#8369; <?php echo number_format($soa['assessment']['total_registration'], 2); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="divider my-2"></div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="row-val">Gross Total</span>
                            <span class="row-val">&#8369; <?php echo number_format($soa['assessment']['gross_total'] ?? 0, 2); ?></span>
                        </div>
                        
                        <?php if (($soa['assessment']['discount_amount'] ?? 0) > 0): ?>
                        <div class="d-flex justify-content-between mb-2 p-2 rounded" style="background-color: #fdf6b2; border: 1px solid #fce96a;">
                            <span class="row-text text-dark" style="font-size:0.8rem;">Scholarship Discount (<?php echo htmlspecialchars($soa['assessment']['discount_text'] ?? ''); ?>)</span>
                            <span class="row-val text-success">-&#8369; <?php echo number_format($soa['assessment']['discount_amount'], 2); ?></span>
                        </div>
                        <div class="divider my-2"></div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between mt-3 p-3 rounded" style="background-color: #f1f8f4; border: 1px solid #e2e8f0;">
                            <span class="val-total-label">NET ASSESSMENT</span>
                            <span class="val-total-amount fs-5">&#8369; <?php echo number_format($soa['assessment']['net_total'] ?? 0, 2); ?></span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="section-title mb-3">STATEMENT LEDGER</h6>
                        <div class="table-responsive" style="border: 1px solid #e2e8f0; border-radius: 8px;">
                            <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                                <thead style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="group-label-grey py-3 px-3">DATE</th>
                                        <th class="group-label-grey py-3">DESCRIPTION</th>
                                        <th class="group-label-grey py-3 text-end">CHARGE</th>
                                        <th class="group-label-grey py-3 text-end">PAYMENT</th>
                                        <th class="group-label-grey py-3 text-end px-3">BALANCE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="background-color: #f8fafc;">
                                        <td class="py-3 px-3 row-text"><?php echo isset($soa['assessment']['assessed_at']) ? date('M j, Y', strtotime($soa['assessment']['assessed_at'])) : date('M j, Y'); ?></td>
                                        <td class="py-3 row-val">Assessed Net Total</td>
                                        <td class="py-3 text-end row-val text-danger">&#8369; <?php echo number_format($soa['assessment']['net_total'] ?? 0, 2); ?></td>
                                        <td class="py-3 text-end row-text">-</td>
                                        <td class="py-3 text-end row-val px-3">&#8369; <?php echo number_format($soa['assessment']['net_total'] ?? 0, 2); ?></td>
                                    </tr>
                                    <?php 
                                    $running_balance = $soa['assessment']['net_total'] ?? 0;
                                    if (!empty($all_payments)): 
                                        foreach ($all_payments as $pay): 
                                            $running_balance -= $pay['amount_paid'];
                                    ?>
                                        <tr>
                                            <td class="py-3 px-3 row-text"><?php echo date('M j, Y', strtotime($pay['payment_date'])); ?></td>
                                            <td class="py-3 row-text">Payment (<?php echo htmlspecialchars($pay['receipt_no']); ?>)</td>
                                            <td class="py-3 text-end row-text">-</td>
                                            <td class="py-3 text-end row-val text-success">&#8369; <?php echo number_format($pay['amount_paid'], 2); ?></td>
                                            <td class="py-3 text-end row-val px-3">&#8369; <?php echo number_format($running_balance, 2); ?></td>
                                        </tr>
                                    <?php 
                                        endforeach; 
                                    endif; 
                                    ?>
                                    <tr style="background-color: #f1f8f4; border-top: 2px solid #e2e8f0;">
                                        <td colspan="4" class="text-end py-3 px-3 val-total-label">CURRENT BALANCE DUE:</td>
                                        <td class="text-end py-3 px-3 val-total-amount text-danger fs-5">&#8369; <?php echo number_format(max(0, $running_balance), 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-center py-4 border-top-0 rounded-bottom-4">
                <button class="btn btn-outline-success btn-lg fw-bold px-5" style="border-radius: 8px; border-width: 2px;" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i> Print Statement of Account
                </button>
            </div>
        </div>
    </div>
</div>

<style media="print">
    .card-footer { display: none; }
    @page { margin: 1in; }
</style>

<?php include '../includes/footer.php'; ?>
