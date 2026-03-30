<?php
/**
 * College Payment System - Admin Post Payment
 * Fixed syntax errors, UI, search logic
 */
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireSubsystemAccess('payment_accounting');

$page_title = 'Post Payment';
$selected_student = null;
$student_fees = array();
$soa = array();
$error_msg = '';
$search_results = array();

try {
    if (!empty($_GET['student_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ? AND role = 'student'");
        $stmt->execute([$_GET['student_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $selected_student = $result;
            $student_fees = getStudentFees($selected_student['student_id']);
            $soa = getStudentSOA($selected_student['student_id']);
        }
    }

    if (!empty($_POST['student_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ? AND role = 'student'");
        $stmt->execute([$_POST['student_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $selected_student = $result;
            $student_fees = getStudentFees($selected_student['student_id']);
            $soa = getStudentSOA($selected_student['student_id']);
        }
    }

    // Search student
    if (isset($_POST['search_student'])) {
        $search_term = trim($_POST['search'] ?? '');
        if (!empty($search_term)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'student' AND (student_id LIKE ? OR full_name LIKE ?) ORDER BY student_id ASC LIMIT 10");
            $like = '%' . $search_term . '%';
            $stmt->execute(array($like, $like));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($results) == 1) {
                $selected_student = $results[0];
                $student_fees = getStudentFees($selected_student['student_id']);
                $soa = getStudentSOA($selected_student['student_id']);
            } elseif (count($results) > 1) {
                $search_results = $results;
            } else {
                $error_msg = 'No student found for "' . htmlspecialchars($search_term) . '"';
            }
        }
    }
    
    // Select from search results
    if (isset($_POST['select_student']) && !empty($_POST['select_student'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ? AND role = 'student'");
        $stmt->execute(array($_POST['select_student']));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $selected_student = $result;
            $student_fees = getStudentFees($selected_student['student_id']);
            $soa = getStudentSOA($selected_student['student_id']);
        }
    }
    
    // Post payment
    if (isset($_POST['post_payment']) && $selected_student) {
        $amount = floatval($_POST['amount_paid']);
        $fees = isset($_POST['selected_fees']) ? $_POST['selected_fees'] : array();
        $remarks = trim($_POST['remarks'] ?? '');
        $method = $_POST['payment_method'] ?? 'Cash';
        $current_balance = getStudentBalance($selected_student['student_id']);

        if ($amount > 0 && ($current_balance <= 0 || $amount <= $current_balance)) {
            $receipt_no = postPayment($selected_student['student_id'], $amount, $remarks, $method);
            if ($receipt_no) {
                $_SESSION['message'] = 'Payment posted! Receipt: <strong>' . htmlspecialchars($receipt_no) . '</strong>';
                $_SESSION['msg_type'] = 'success';
                header('Location: post_payment.php?student_id=' . urlencode($selected_student['student_id']));
                exit;
            } else {
                $error_msg = 'Payment failed. Please try again.';
            }
        } elseif ($current_balance > 0 && $amount > $current_balance) {
            $error_msg = 'Amount paid cannot be greater than the student balance.';
        } else {
            $error_msg = 'Amount must be greater than 0.';
        }
    }
    
} catch (Exception $e) {
    $error_msg = 'System error: ' . $e->getMessage();
}
?>

<?php include '../includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <h3 class="dashboard-title"><i class="bi bi-receipt text-success me-2"></i>Post Payment</h3>
        <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <!-- SEARCH FORM -->
    <div class="card-outline bg-white mb-4">
        <div class="balance-header py-3 px-4">
            <span class="section-title"><i class="bi bi-search me-2" style="color: #22c55e;"></i>FIND STUDENT</span>
        </div>
        <div class="card-body p-4">
            <form method="POST" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white" style="border: 2px solid #e2e8f0; border-right: none;">
                            <i class="bi bi-person-circle text-success"></i>
                        </span>
                        <input type="text" 
                               class="form-control form-control-lg row-text" 
                               name="search" 
                               style="border: 2px solid #e2e8f0; border-left: none; font-size: 1rem;"
                               placeholder="Type Student ID (2024001) or Name (John Doe)"
                               value="<?php echo htmlspecialchars($_POST['search'] ?? ''); ?>"
                               required>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="search_student" class="btn btn-success w-100 h-100 fw-bold" style="border-radius: 8px;">
                        <i class="bi bi-search"></i>
                        <span class="d-none d-sm-inline"> Search</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($error_msg)): ?>
    <div class="alert alert-warning shadow-sm">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?php echo htmlspecialchars($error_msg); ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($search_results)): ?>
    <!-- MULTIPLE RESULTS TABLE -->
    <div class="card-outline bg-white mb-4">
        <div class="balance-header py-3 px-4">
            <span class="section-title text-success"><i class="bi bi-people me-2"></i><?php echo count($search_results); ?> STUDENTS FOUND</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                <thead style="background-color: #f8fafc;">
                    <tr>
                        <th class="group-label-grey py-3 px-4">ID</th>
                        <th class="group-label-grey py-3">NAME</th>
                        <th class="group-label-grey py-3">PROGRAM</th>
                        <th class="group-label-grey py-3">BALANCE</th>
                        <th class="group-label-grey py-3 px-4 text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_results as $student): 
                        $balance = getStudentBalance($student['student_id']);
                    ?>
                    <tr onclick="document.getElementById('select_<?php echo $student['student_id']; ?>').click();" style="cursor:pointer;">
                        <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td class="py-3"><span class="badge" style="background-color: #e2e8f0; color: #475569;"><?php echo $student['program']; ?></span></td>
                        <td class="py-3 row-val <?php echo $balance > 0 ? 'text-danger' : 'text-success'; ?>">
                            &#8369; <?php echo number_format($balance, 2); ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <form id="select_<?php echo $student['student_id']; ?>" method="POST" style="display:inline;">
                                <input type="hidden" name="select_student" value="<?php echo $student['student_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success fw-bold" style="border-radius: 6px;">
                                    <i class="bi bi-check-lg"></i> Select
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($selected_student): ?>
    <!-- SELECTED STUDENT INFO -->
    <div class="card-outline bg-white mb-4" style="border-color: #22c55e !important; box-shadow: 0 4px 6px rgba(34,197,94,0.1) !important;">
        <div class="p-4" style="background-color: #f1f8f4; border-bottom: 2px solid #e2e8f0; border-radius: 12px 12px 0 0;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($selected_student['full_name']); ?></h4>
                    <div class="row-text"><?php echo $selected_student['student_id']; ?> &bull; <?php echo $selected_student['program']; ?> Year <?php echo $selected_student['year_level']; ?></div>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge fs-6 px-3 py-2" style="<?php echo isset($soa['enrolled']) && $soa['enrolled'] ? 'background-color: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0;' : 'background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;'; ?>">
                        <?php echo isset($soa['enrolled']) && $soa['enrolled'] ? 'Officially Enrolled' : 'Pending Enrollment'; ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <h4 class="row-val mb-1">&#8369; <?php echo isset($soa['assessment']) ? number_format($soa['assessment']['net_total'] ?? 0, 2) : '0.00'; ?></h4>
                        <div class="group-label-grey">TOTAL ASSESSMENT</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <h4 class="row-val text-success mb-1">&#8369; <?php echo isset($soa['total_paid']) ? number_format($soa['total_paid'],2) : '0.00'; ?></h4>
                        <div class="group-label-grey">TOTAL PAID</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 rounded" style="background-color: #fffbeb; border: 1px solid #fde68a;">
                        <h4 class="row-val mb-1 text-warning">&#8369; <?php echo number_format(getStudentBalance($selected_student['student_id']),2); ?></h4>
                        <div class="group-label-grey text-warning">BALANCE DUE</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 rounded" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                        <h4 class="row-val mb-1 text-success"><?php echo count($student_fees); ?></h4>
                        <div class="group-label-grey text-success">FEE ITEMS</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PAYMENT FORM -->
    <div class="card-outline bg-white mb-5" style="border-width: 2px !important;">
        <div class="balance-header py-4 px-4 d-flex align-items-center">
            <i class="bi bi-cash-coin fs-4 text-success me-3"></i>
            <span class="section-title m-0 fs-5">RECORD NEW PAYMENT</span>
        </div>
        <div class="card-body p-4">
            <form method="POST">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($selected_student['student_id']); ?>">
                
                <div class="row g-4">
                    <div class="col-lg-7">
                        <label class="form-label row-val mb-3">Fee Items (Optional - click to select)</label>
                        <div class="rounded-4 p-4" style="background-color: #f8fafc; border: 2px solid #e2e8f0; max-height: 400px; overflow-y: auto;">
                            <?php if (!empty($student_fees)): ?>
                                <?php foreach ($student_fees as $fee): ?>
                                    <div class="form-check mb-3 p-3 bg-white hoverable" style="border: 2px solid #e2e8f0; border-radius: 8px; transition: all 0.2s;">
                                        <input class="form-check-input ms-1 mt-2" type="checkbox" name="selected_fees[]" value="<?php echo $fee['id']; ?>" id="fee_<?php echo $fee['id']; ?>" style="transform: scale(1.2);">
                                        <label class="form-check-label w-100 ps-2" for="fee_<?php echo $fee['id']; ?>" style="cursor: pointer;">
                                            <div class="row align-items-center">
                                                <div class="col row-text fw-bold text-dark">
                                                    <?php echo htmlspecialchars($fee['fee_name']); ?>
                                                </div>
                                                <div class="col-auto row-val text-success">
                                                    &#8369; <?php echo number_format($fee['amount'], 2); ?>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 mb-3 text-muted opacity-50"></i>
                                    <p class="row-text text-muted">No fee items configured</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="position-sticky" style="top: 20px;">
                            <div class="card-outline bg-white p-4 text-center" style="border: 2px solid #22c55e;">
                                <div class="group-label-grey mb-3">PAYMENT DETAILS</div>
                                <div class="mb-4 pb-3" style="border-bottom: 2px dashed #e2e8f0;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="row-text text-muted">Total Balance:</span>
                                        <span class="row-val fs-5 text-dark">&#8369; <?php echo number_format(getStudentBalance($selected_student['student_id']), 2); ?></span>
                                    </div>
                                </div>
                                <div class="row g-3 mb-4 text-start">
                                    <div class="col-12">
                                        <label class="form-label row-val mb-2">Amount Paid <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold text-success" style="border: 2px solid #e2e8f0; border-right: none;">&#8369;</span>
                                            <input type="number" 
                                                   step="0.01" 
                                                   class="form-control form-control-lg text-end row-val text-success" 
                                                   name="amount_paid" 
                                                   min="1" 
                                                   max="<?php echo getStudentBalance($selected_student['student_id']); ?>" 
                                                   style="border: 2px solid #e2e8f0; border-left: none;"
                                                   placeholder="0.00"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="form-label row-val mb-2">Payment Method</label>
                                        <input type="text" class="form-control row-text bg-light" value="Cash" readonly style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                        <input type="hidden" name="payment_method" value="Cash">
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="form-label row-val mb-2">Remarks (optional)</label>
                                        <input type="text" 
                                               class="form-control row-text" 
                                               name="remarks" 
                                               style="border: 2px solid #e2e8f0; border-radius: 8px;"
                                               placeholder="Cash payment / Check #1234">
                                    </div>
                                </div>
                                <button type="submit" name="post_payment" class="btn btn-success w-100 py-3 fw-bold fs-6" style="border-radius: 8px;">
                                    <i class="bi bi-printer me-2"></i> POST PAYMENT & PRINT
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.bg-gradient {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}
.hoverable:hover {
    background-color: rgba(0,0,0,0.05);
    border-color: #28a745 !important;
}
.table tbody tr:hover {
    background-color: rgba(40, 167, 69, 0.05);
}
.btn-warning-lg {
    font-size: 1.1rem !important;
    padding: 1rem 2rem !important;
}
</style>

<?php include '../includes/footer.php'; ?>

