<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$balance = getStudentBalance($student_id);
$page_title = 'Pay Online';
?>
<?php include '../includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card-outline bg-white mb-5">
            <div class="balance-header py-3 text-center">
                <span class="section-title"><i class="bi bi-wallet2 me-2"></i>ONLINE PAYMENT GATEWAY</span>
            </div>
            <div class="card-body p-4 p-md-5">
                <?php if ($balance <= 0): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                        <h3 class="dashboard-title text-success">You are fully paid!</h3>
                        <p class="row-text mt-2">You have no outstanding balance to settle.</p>
                        <a href="dashboard.php" class="btn btn-outline-success mt-4 px-5 fw-bold" style="border-radius: 8px;">Back to Dashboard</a>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <span class="group-label-grey">CURRENT BALANCE</span>
                        <div class="balance-amount mt-1">&#8369; <?php echo number_format($balance, 2); ?></div>
                    </div>

                    <form action="process_online_payment.php" method="POST" id="paymentForm">
                        <div class="mb-4">
                            <label class="form-label row-val">Payment Amount (&#8369;)</label>
                            <input type="number" name="amount" class="form-control form-control-lg text-end fw-bold text-success fs-3" style="border: 2px solid #e2e8f0; border-radius: 8px;" step="0.01" min="100" max="<?php echo $balance; ?>" value="<?php echo floatval($balance); ?>" required>
                            <div class="form-text mt-2 group-label-grey"><i class="bi bi-info-circle me-1"></i>MINIMUM PAYMENT: &#8369; 100.00</div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label row-val mb-3">Select Payment Method</label>
                            
                            <div class="form-check p-3 rounded-3 mb-3 hoverable payment-method" style="border: 1px solid #e2e8f0; border-radius: 8px;">
                                <input class="form-check-input mt-3 ms-2" type="radio" name="method" id="gcash" value="GCash" required checked>
                                <label class="form-check-label w-100 ps-3" for="gcash">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 50px; text-align: center;">
                                            <img src="../assets/img/gcash_logo.png" alt="GCash Logo" style="max-width: 100%; height: auto; border-radius: 8px;">
                                        </div>
                                        <div>
                                            <span class="d-block row-val">GCash</span>
                                            <span class="row-text" style="font-size: 0.75rem;">Pay instantly via GCash app</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check p-3 rounded-3 mb-3 hoverable payment-method" style="border: 1px solid #e2e8f0; border-radius: 8px;">
                                <input class="form-check-input mt-3 ms-2" type="radio" name="method" id="maya" value="Maya">
                                <label class="form-check-label w-100 ps-3" for="maya">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 50px; text-align: center;">
                                            <img src="../assets/img/maya_logo.png" alt="Maya Logo" style="max-width: 100%; height: auto; border-radius: 8px;">
                                        </div>
                                        <div>
                                            <span class="d-block row-val">Maya</span>
                                            <span class="row-text" style="font-size: 0.75rem;">Scan to pay with Maya</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check p-3 rounded-3 hoverable payment-method" style="border: 1px solid #e2e8f0; border-radius: 8px;">
                                <input class="form-check-input mt-3 ms-2" type="radio" name="method" id="card" value="Credit/Debit Card">
                                <label class="form-check-label w-100 ps-3" for="card">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 50px; text-align: center;">
                                            <i class="bi bi-credit-card fs-2 text-secondary"></i>
                                        </div>
                                        <div>
                                            <span class="d-block row-val">Credit / Debit Card</span>
                                            <span class="row-text" style="font-size: 0.75rem;">Visa, Mastercard, JCB</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm py-3" style="border-radius: 8px;" id="payBtn">
                            <i class="bi bi-shield-lock-fill me-2"></i>Submit Payment for Verification
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.tracking-wider { letter-spacing: 2px; }
.payment-method { transition: all 0.2s ease; cursor: pointer; border-width: 2px !important; }
.payment-method:has(input:checked) { border-color: #ffc107 !important; background-color: #fffaf0; }
.hoverable:hover { background-color: #f8f9fa; border-color: #ddd !important; }
.payment-method:has(input:checked):hover { border-color: #ffc107 !important; }
</style>

<script>
document.getElementById('paymentForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('payBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing Secure Payment...';
    btn.disabled = true;
});
</script>

<?php include '../includes/footer.php'; ?>
