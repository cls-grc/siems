<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Documents & Credentials';
$studentId = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_document'])) {
    try {
        $documentName = trim($_POST['document_type']);
        $type = siemsFetchOne("SELECT * FROM document_types WHERE document_name = ? LIMIT 1", [$documentName]);
        $fee = $type ? (float) $type['standard_fee'] : getDocumentRequestFeeAmount($documentName, $_POST['urgency'] ?? 'Regular');
        if (($_POST['urgency'] ?? 'Regular') === 'Rush') {
            $fee += 100;
        }

        $pdo->beginTransaction();
        $pdo->prepare("
            INSERT INTO document_requests (student_id, document_type, purpose, urgency, fee_amount, status, requested_at)
            VALUES (?, ?, ?, ?, ?, 'Pending', NOW())
        ")->execute([$studentId, $documentName, trim($_POST['purpose'] ?? '') ?: null, trim($_POST['urgency'] ?? 'Regular'), $fee]);
        $requestId = (int) $pdo->lastInsertId();
        postPayment($studentId, $fee, 'Document Request Payment [DOCREQ:' . $requestId . '] - ' . $documentName, trim($_POST['payment_method'] ?? 'GCash'), null, null, 'Pending');
        $pdo->commit();
        $_SESSION['message'] = 'Document request submitted successfully and queued for payment validation.';
        $_SESSION['msg_type'] = 'success';
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['message'] = 'Unable to submit document request.';
        $_SESSION['msg_type'] = 'danger';
    }
    header('Location: documents_credentials.php');
    exit;
}

$overview = siemsGetStudentDocumentsOverview($studentId);
$documentTypes = $overview['document_types'];
$requests = $overview['requests'];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-folder2-open text-success me-2"></i>Documents & Credentials</h3>
        <div class="row-text">Request official school documents and track processing through release.</div>
    </div>
    <a href="siems_portal.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;"><i class="bi bi-arrow-left me-1"></i> SIEMS Portal</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">NEW DOCUMENT REQUEST</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12">
                        <select name="document_type" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select document</option>
                            <?php foreach ($documentTypes as $type): ?>
                                <option value="<?php echo htmlspecialchars($type['document_name']); ?>"><?php echo htmlspecialchars($type['document_name'] . ' (PHP ' . number_format($type['standard_fee'], 2) . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12"><textarea name="purpose" rows="3" class="form-control row-text" placeholder="Purpose of request" style="border:2px solid #e2e8f0;border-radius:8px;"></textarea></div>
                    <div class="col-md-6"><select name="urgency" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Regular</option><option>Rush</option></select></div>
                    <div class="col-md-6"><select name="payment_method" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>GCash</option><option>Maya</option><option>Online Banking</option><option>Credit/Debit Card</option></select></div>
                    <div class="col-12"><button type="submit" name="request_document" class="btn btn-success fw-bold" style="border-radius:8px;">Submit Request</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">MY DOCUMENT REQUESTS</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">DOCUMENT</th><th class="group-label-grey py-3">PURPOSE</th><th class="group-label-grey py-3">STATUS</th><th class="group-label-grey py-3">CLAIM REF</th></tr></thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($request['document_type']); ?></div><div class="row-text"><?php echo htmlspecialchars($request['urgency']); ?> | PHP <?php echo number_format($request['fee_amount'], 2); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($request['purpose'] ?? '-'); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($request['status']); ?></td>
                                <td class="py-3 row-text">
                                    <?php if ($request['status'] === 'Released'): ?>
                                        <a href="print_document.php?request_id=<?php echo (int) $request['id']; ?>" class="btn btn-success btn-sm" style="border-radius:8px;">
                                            <i class="bi bi-printer me-1"></i> Print Document
                                        </a>
                                        <div><?php echo htmlspecialchars($request['claim_reference'] ?? 'DOC-' . str_pad((string) $request['id'], 5, '0', STR_PAD_LEFT)); ?></div>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($request['claim_reference'] ?? 'Pending release'); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($requests)): ?><tr><td colspan="4" class="text-center text-muted py-5">No document requests yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
