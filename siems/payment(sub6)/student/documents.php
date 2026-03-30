<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$document_request_columns = $pdo->query("SHOW COLUMNS FROM document_requests")->fetchAll(PDO::FETCH_COLUMN);
$has_purpose_column = in_array('purpose', $document_request_columns, true);
$has_urgency_column = in_array('urgency', $document_request_columns, true);
$has_pickup_column = in_array('preferred_pickup_date', $document_request_columns, true);
$request_colspan = 5 + ($has_purpose_column ? 1 : 0) + ($has_urgency_column ? 1 : 0) + ($has_pickup_column ? 1 : 0);

// Handle document request
if (isset($_POST['request_doc']) && !empty($_POST['document_type'])) {
    $purpose = trim($_POST['purpose'] ?? '');
    $urgency = $_POST['urgency'] ?? 'Regular';
    $preferred_pickup = $_POST['preferred_pickup'] ?? date('Y-m-d', strtotime('+7 days'));
    $document_fee = getDocumentRequestFeeAmount($_POST['document_type'], $urgency);
    $payment_method = $_POST['payment_method'] ?? 'GCash';

    $insert_columns = ['student_id', 'document_type', 'status', 'fee_amount'];
    $placeholders = ['?', '?', "'Pending'", '?'];
    $params = [$student_id, $_POST['document_type'], $document_fee];

    if ($has_purpose_column) {
        $insert_columns[] = 'purpose';
        $placeholders[] = '?';
        $params[] = $purpose;
    }

    if ($has_urgency_column) {
        $insert_columns[] = 'urgency';
        $placeholders[] = '?';
        $params[] = $urgency;
    }

    if ($has_pickup_column) {
        $insert_columns[] = 'preferred_pickup_date';
        $placeholders[] = '?';
        $params[] = $preferred_pickup;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO document_requests (" . implode(', ', $insert_columns) . ")
            VALUES (" . implode(', ', $placeholders) . ")
        ");
        $success = $stmt->execute($params);

        if (!$success) {
            throw new RuntimeException('Failed to save document request.');
        }

        $request_id = (int)$pdo->lastInsertId();
        $remarks = 'Document Request Payment [DOCREQ:' . $request_id . '] - ' . $_POST['document_type'];
        $receipt_no = postPayment($student_id, $document_fee, $remarks, $payment_method, null, null, 'Pending');

        if (!$receipt_no) {
            throw new RuntimeException('Failed to save document payment.');
        }

        $pdo->commit();
        $_SESSION['message'] = 'Document request submitted with online payment. It is now waiting for admin payment validation.';
        $_SESSION['msg_type'] = 'success';
        header('Location: documents.php');
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['message'] = 'Failed to submit document request payment.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: documents.php');
        exit;
    }
}

// Get existing requests
$stmt = $pdo->prepare("
    SELECT dr.*,
           (
               SELECT p.payment_method
               FROM payments p
               WHERE p.student_id = dr.student_id
                 AND p.remarks LIKE CONCAT('Document Request Payment [DOCREQ:', dr.id, ']%')
               ORDER BY p.id DESC
               LIMIT 1
           ) AS payment_method,
           (
               SELECT p.amount_paid
               FROM payments p
               WHERE p.student_id = dr.student_id
                 AND p.remarks LIKE CONCAT('Document Request Payment [DOCREQ:', dr.id, ']%')
               ORDER BY p.id DESC
               LIMIT 1
           ) AS payment_amount
    FROM document_requests dr
    WHERE student_id = ? 
    ORDER BY requested_at DESC
");
$stmt->execute([$student_id]);
$requests = $stmt->fetchAll();

$page_title = 'Document Requests';
?>
<?php include '../includes/header.php'; ?>

<style>
.print-slip {
    break-inside: avoid;
    page-break-inside: avoid;
}
.print-slip .no-print {
    display: none;
}
@media print {
    body * { visibility: hidden; }
    .print-slip, .print-slip * { visibility: visible; }
    .print-slip { position: absolute; left: 0; top: 0; width: 100%; }
    .print-slip td { display: block !important; }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title"><i class="bi bi-file-earmark-plus text-success me-2"></i>Document Requests</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<!-- NEW REQUEST FORM - Enhanced -->
<div class="card-outline bg-white mb-5">
    <div class="balance-header py-3 text-center">
        <span class="section-title"><i class="bi bi-plus-circle me-2"></i>NEW DOCUMENT REQUEST</span>
    </div>
    <div class="card-body p-4 p-md-5">
        <form method="POST">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label row-val mb-2">Document Type <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg row-text" name="document_type" style="border: 2px solid #e2e8f0; border-radius: 8px;" required>
                        <option value="">Select Document</option>
                        <option value="Certificate of Enrollment">Certificate of Enrollment (&#8369;100)</option>
                        <option value="Transcript of Records (TOR)">Transcript of Records / TOR (&#8369;200)</option>
                        <option value="Good Moral Certificate">Good Moral Certificate (&#8369;50)</option>
                        <option value="Diploma">Diploma (&#8369;300)</option>
                        <option value="Authenticated TOR">Authenticated TOR (&#8369;500)</option>
                        <option value="Certificate of Graduation">Certificate of Graduation (&#8369;150)</option>
                    </select>
                </div>
                <?php if ($has_urgency_column): ?>
                <div class="col-md-6">
                    <label class="form-label row-val mb-2">Urgency <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg row-text" name="urgency" style="border: 2px solid #e2e8f0; border-radius: 8px;" required>
                        <option value="Regular">Regular (7 working days)</option>
                        <option value="Rush">Rush (+&#8369;100, 3 working days)</option>
                    </select>
                </div>
                <?php endif; ?>
                <?php if ($has_purpose_column): ?>
                <div class="col-md-12">
                    <label class="form-label row-val mb-2">Purpose of Request <span class="text-danger">*</span></label>
                    <textarea class="form-control row-text" name="purpose" rows="3" style="border: 2px solid #e2e8f0; border-radius: 8px;" required 
                              placeholder="Employment / Scholarship application / Study Abroad / School Transfer / etc..."></textarea>
                    <div class="form-text mt-2 group-label-grey">BE SPECIFIC - HELPS THE DOCUMENT OFFICE PRIORITIZE</div>
                </div>
                <?php endif; ?>
                <?php if ($has_pickup_column): ?>
                <div class="col-md-6">
                    <label class="form-label row-val mb-2">Preferred Pickup Date</label>
                    <input type="date" class="form-control form-control-lg row-text" name="preferred_pickup" style="border: 2px solid #e2e8f0; border-radius: 8px;" 
                           value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" min="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
                    <div class="form-text mt-2 group-label-grey">AFTER PROCESSING (RUSH: 3 DAYS, REGULAR: 7 DAYS)</div>
                </div>
                <?php endif; ?>
                <div class="col-md-6">
                    <label class="form-label row-val mb-2">Current Balance Check</label>
                    <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <span class="row-text">Balance:</span> <strong class="val-total-amount fs-5 text-success ms-2">&#8369; <?php echo number_format(getStudentBalance($student_id), 2); ?></strong>
                        <div class="group-label-grey mt-2">DOCUMENT PAYMENTS ARE SEPARATE FROM YOUR SCHOOL BALANCE</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label row-val mb-2">Online Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg row-text" name="payment_method" style="border: 2px solid #e2e8f0; border-radius: 8px;" required>
                        <option value="GCash">GCash</option>
                        <option value="Maya">Maya</option>
                        <option value="Online Banking">Online Banking</option>
                        <option value="Credit/Debit Card">Credit / Debit Card</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label row-val mb-2">Payment Flow</label>
                    <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="row-text">You will pay this request immediately using the selected online method.</div>
                        <div class="group-label-grey mt-2">ADMIN WILL JUST VALIDATE THE PAYMENT AFTERWARD</div>
                    </div>
                </div>
                <div class="col-12 mt-5">
                    <button type="submit" name="request_doc" class="btn btn-success btn-lg px-5 py-3 w-100 shadow-sm fw-bold" style="border-radius: 8px;">
                        <i class="bi bi-file-earmark-plus me-2"></i> Submit Request and Pay Online
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- REQUESTS STATUS TABLE -->
<div class="card-outline bg-white mb-5">
    <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="section-title"><i class="bi bi-list-check me-2"></i>MY DOCUMENT REQUESTS</span>
        <span class="badge bg-success rounded-pill px-3"><?php echo count($requests); ?></span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($requests)): ?>
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-x fs-1 text-muted mb-3 d-block"></i>
                <h5 class="dashboard-title fs-5 mb-2">No requests yet</h5>
                <p class="row-text">Submit your first document request above</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th class="group-label-grey py-3 px-4">DATE</th>
                            <th class="group-label-grey py-3">DOCUMENT</th>
                            <th class="group-label-grey py-3">FEE</th>
                            <?php if ($has_purpose_column): ?><th class="group-label-grey py-3">PURPOSE</th><?php endif; ?>
                            <?php if ($has_urgency_column): ?><th class="group-label-grey py-3">URGENCY</th><?php endif; ?>
                            <?php if ($has_pickup_column): ?><th class="group-label-grey py-3">PICKUP DATE</th><?php endif; ?>
                            <th class="group-label-grey py-3">STATUS</th>
                            <th class="group-label-grey py-3 text-center px-4">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td class="py-3 px-4 row-text"><?php echo date('M j', strtotime($req['requested_at'])); ?></td>
                                <td class="py-3 row-val"><?php echo htmlspecialchars($req['document_type']); ?></td>
                                <td class="py-3 row-val text-success">&#8369; <?php echo number_format($req['fee_amount'] ?? 0, 2); ?></td>
                                <?php if ($has_purpose_column): ?>
                                <td class="py-3 row-text"><?php echo htmlspecialchars(substr($req['purpose'] ?? '', 0, 50)) . (strlen($req['purpose'] ?? '') > 50 ? '...' : ''); ?></td>
                                <?php endif; ?>
                                <?php if ($has_urgency_column): ?>
                                <td class="py-3">
                                    <span class="badge" style="background-color: <?php echo $req['urgency']=='Rush' ? '#fee2e2' : '#e0f2fe'; ?>; color: <?php echo $req['urgency']=='Rush' ? '#ef4444' : '#0284c7'; ?>;">
                                        <?php echo strtoupper($req['urgency']); ?>
                                    </span>
                                </td>
                                <?php endif; ?>
                                <?php if ($has_pickup_column): ?>
                                <td class="py-3 row-text"><?php echo date('M j', strtotime($req['preferred_pickup_date'])); ?></td>
                                <?php endif; ?>
                                <td class="py-3">
                                    <span class="badge px-3 py-2" style="<?php echo ($req['status'] ?? 'Pending') === 'Pending' ? 'background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;' : (($req['status'] ?? '') === 'Paid' ? 'background-color: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe;' : 'background-color: #f1f8f4; color: #16a34a; border: 1px solid #bbf7d0;'); ?>">
                                        <?php echo strtoupper(str_replace('_', ' ', $req['status'])); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <?php if (($req['status'] ?? '') === 'Paid' || ($req['status'] ?? '') === 'Generated'): ?>
                                        <button class="btn btn-outline-success btn-sm fw-bold print-slip-btn" style="border-radius: 6px; border-width: 2px;" onclick="printSlip(<?php echo $req['id']; ?>)">
                                            <i class="bi bi-printer"></i> Print Slip
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-light btn-sm row-text disabled" disabled title="Waiting approval from Document Office">
                                            <i class="bi bi-clock me-1"></i> Pending
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if (($req['status'] ?? '') === 'Paid' || ($req['status'] ?? '') === 'Generated'): ?>
                            <tr class="print-slip" id="slip_<?php echo $req['id']; ?>" style="display:none; background-color: #f8fafc;">
                                <td colspan="<?php echo $request_colspan; ?>" class="p-4 d-none d-print-block">
                                    <!-- Print formatting content stays identical to functionality but updated to minimalist classes -->
                                    <div class="row">
                                        <div class="col-md-8 mx-auto">
                                            <div class="text-center p-5 card-outline bg-white">
                                                <h3 class="dashboard-title mb-2"><i class="bi bi-file-earmark-check text-success me-2"></i>Document Ready Slip</h3>
                                                <div class="divider"></div>
                                                <div class="row mb-3 mt-4 text-start">
                                                    <div class="col-6 row-val"><span class="group-label-grey d-block mb-1">STUDENT</span> <?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                                                    <div class="col-6 row-val"><span class="group-label-grey d-block mb-1">ID NUMBER</span> <?php echo $student_id; ?></div>
                                                </div>
                                                <div class="mb-4 text-start">
                                                    <span class="group-label-grey d-block mb-1">DOCUMENT REQUESTED</span>
                                                    <div class="row-val fs-5"><?php echo htmlspecialchars($req['document_type']); ?></div>
                                                </div>
                                                <div class="row mb-4 text-start">
                                                    <div class="col-6">
                                                        <span class="group-label-grey d-block mb-1">PAYMENT AMOUNT</span>
                                                        <div class="row-val">&#8369; <?php echo number_format($req['payment_amount'] ?? $req['fee_amount'] ?? 0, 2); ?></div>
                                                    </div>
                                                    <div class="col-6">
                                                        <span class="group-label-grey d-block mb-1">PAYMENT METHOD</span>
                                                        <div class="row-val"><?php echo htmlspecialchars($req['payment_method'] ?? 'N/A'); ?></div>
                                                    </div>
                                                </div>
                                                <div class="row mb-4 text-start">
                                                    <?php if ($has_urgency_column): ?>
                                                    <div class="col-6"><span class="group-label-grey d-block mb-1">URGENCY</span> <div class="row-val"><?php echo $req['urgency']; ?></div></div>
                                                    <?php endif; ?>
                                                    <div class="col-6"><span class="group-label-grey d-block mb-1">REQUEST DATE</span> <div class="row-val"><?php echo date('M j, Y', strtotime($req['requested_at'])); ?></div></div>
                                                </div>
                                                <?php if ($has_pickup_column): ?>
                                                <div class="p-3 mb-4 rounded-3 text-center" style="background-color: #f1f8f4; border: 1px solid #bbf7d0;">
                                                    <span class="group-label-green d-block mb-1">READY FOR PICKUP ON OR AFTER</span>
                                                    <div class="val-total-amount fs-4 text-success"><?php echo date('M j, Y - H:i', strtotime($req['preferred_pickup_date'])); ?></div>
                                                </div>
                                                <?php endif; ?>
                                                <div class="divider"></div>
                                                <p class="mb-1 mt-4 group-label-grey">GENERATED BY PAYMENT SYSTEM &bull; <?php echo date('M j, Y H:i'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function printSlip(requestId) {
    const slip = document.getElementById('slip_' + requestId);
    if (slip) {
        slip.style.display = 'table-row';
        window.print();
        slip.style.display = 'none';
    }
}

// Auto-hide print slip rows
document.querySelectorAll('.print-slip').forEach(row => {
    row.style.display = 'none';
});
</script>

<?php include '../includes/footer.php'; ?>

