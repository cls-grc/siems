<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';

requireSubsystemAccess('documents_credentials');

$page_title = 'Document Requests';
$module = $_GET['module'] ?? '';
$allowedModules = ['request', 'processing', 'generation', 'release', 'archives', 'audit'];
if (!in_array($module, $allowedModules, true)) {
    $module = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['save_document_type'])) {
            $documentName = trim($_POST['document_name']);
            $standardFee = (float) ($_POST['standard_fee'] ?? 0);
            $processingDays = max(1, (int) ($_POST['processing_days'] ?? 1));

            $pdo->prepare("
                INSERT INTO document_types (document_name, standard_fee, processing_days)
                VALUES (?, ?, ?)
            ")->execute([$documentName, $standardFee, $processingDays]);

            siemsLogSubsystemEvent('Document Requests', 'Added document type', null, null, $documentName);
            $_SESSION['message'] = 'Document type saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: document_requests.php?module=request');
            exit;
        }

if (isset($_POST['request_id'], $_POST['payment_status'])) {
    $requestId = (int) $_POST['request_id'];
    $paymentStatus = trim($_POST['payment_status']);
    $receiptNo = trim($_POST['receipt_no'] ?? '');

    try {
        $stmt = $pdo->prepare("UPDATE document_requests SET payment_status = ?, payment_receipt_no = ? WHERE id = ?");
        $stmt->execute([$paymentStatus, $receiptNo, $requestId]);

        siemsLogSubsystemEvent('Document Requests', 'Payment status updated', null, null, "Request #$requestId to $paymentStatus");
        $_SESSION['message'] = 'Document payment status updated.';
        $_SESSION['msg_type'] = 'success';
    } catch (Exception $e) {
        $_SESSION['message'] = 'Update failed: ' . $e->getMessage();
        $_SESSION['msg_type'] = 'danger';
    }
}

        if (isset($_POST['update_request_status'])) {
            $requestId = (int) ($_POST['request_id'] ?? 0);
            $newStatus = trim($_POST['new_status'] ?? '');
            $claimReference = trim($_POST['claim_reference'] ?? '');

            $request = siemsFetchOne("SELECT * FROM document_requests WHERE id = ? LIMIT 1", [$requestId]);
            if ($request) {
                $pdo->prepare("UPDATE document_requests SET status = ? WHERE id = ?")->execute([$newStatus, $requestId]);

                if ($newStatus === 'Released' && siemsTableExists('document_releases')) {
                    $existingRelease = siemsFetchOne("SELECT id FROM document_releases WHERE document_request_id = ? LIMIT 1", [$requestId]);
                    if ($existingRelease) {
                        $pdo->prepare("
                            UPDATE document_releases
                            SET released_by = ?, released_at = NOW(), claim_reference = ?
                            WHERE document_request_id = ?
                        ")->execute([
                            $_SESSION['user_id'],
                            $claimReference !== '' ? $claimReference : ('DOC-' . str_pad((string) $requestId, 5, '0', STR_PAD_LEFT)),
                            $requestId,
                        ]);
                    } else {
                        $pdo->prepare("
                            INSERT INTO document_releases (document_request_id, released_by, released_at, claim_reference)
                            VALUES (?, ?, NOW(), ?)
                        ")->execute([
                            $requestId,
                            $_SESSION['user_id'],
                            $claimReference !== '' ? $claimReference : ('DOC-' . str_pad((string) $requestId, 5, '0', STR_PAD_LEFT)),
                        ]);
                    }
                }

                siemsLogSubsystemEvent('Document Requests', 'Updated document request status', $request['student_id'], $request['status'], $newStatus);
                $_SESSION['message'] = 'Document request status updated successfully.';
                $_SESSION['msg_type'] = 'success';
            }

            $redirectModule = in_array($newStatus, ['Released'], true) ? 'release' : 'processing';
            header('Location: document_requests.php?module=' . urlencode($redirectModule));
            exit;
        }

        if (isset($_POST['archive_request'])) {
            $requestId = (int) ($_POST['request_id'] ?? 0);
            $request = siemsFetchOne("SELECT * FROM document_requests WHERE id = ? LIMIT 1", [$requestId]);

            if ($request && siemsTableExists('archived_records')) {
                $existingArchive = siemsFetchOne("
                    SELECT id
                    FROM archived_records
                    WHERE source_table = 'document_requests' AND source_id = ?
                    LIMIT 1
                ", [$requestId]);

                if (!$existingArchive) {
                    $pdo->prepare("
                        INSERT INTO archived_records (record_type, source_table, source_id, archived_by, archived_at)
                        VALUES (?, ?, ?, ?, NOW())
                    ")->execute([
                        'Document Request',
                        'document_requests',
                        $requestId,
                        $_SESSION['user_id'],
                    ]);

                    siemsLogSubsystemEvent('Document Requests', 'Archived document request', $request['student_id'], null, 'Request #' . $requestId);
                    $_SESSION['message'] = 'Document request archived successfully.';
                    $_SESSION['msg_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'This document request is already archived.';
                    $_SESSION['msg_type'] = 'warning';
                }
            }

            header('Location: document_requests.php?module=archives');
            exit;
        }
    } catch (Throwable $e) {
        $_SESSION['message'] = 'Unable to save document workflow changes right now. Please check for duplicate or invalid values.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: document_requests.php' . ($module !== '' ? '?module=' . urlencode($module) : ''));
        exit;
    }
}

$overview = siemsGetAdminDocumentsOverview();
$documentTypes = $overview['document_types'];
$documentTypes = $overview['document_types'];
$requests = $overview['requests'];
$releases = $overview['releases'];
$archives = $overview['archives'];

$selectedRequestId = isset($_GET['request_id']) ? (int) $_GET['request_id'] : 0;
$selectedRequest = $selectedRequestId > 0 ? siemsFetchOne("
    SELECT dr.*, u.full_name, u.program, u.year_level, drel.claim_reference, drel.released_at
    FROM document_requests dr
    INNER JOIN users u ON u.student_id = dr.student_id
    LEFT JOIN document_releases drel ON drel.document_request_id = dr.id
    WHERE dr.id = ?
    LIMIT 1
", [$selectedRequestId]) : null;

$recentAuditLogs = siemsTableExists('audit_log') ? siemsFetchAll("
    SELECT al.*, u.full_name AS user_name
    FROM audit_log al
    LEFT JOIN users u ON u.id = al.user_id
    WHERE " . (siemsColumnExists('audit_log', 'subsystem') ? "al.subsystem = 'Document Requests'" : "al.action LIKE '%document%' OR al.action LIKE '%archive%'") . "
    ORDER BY al.created_at DESC, al.id DESC
    LIMIT 20
") : [];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-folder2-open text-success me-2"></i>Document Requests</h3>
        <div class="row-text">Subsystem 7 for request intake, document workflow, generation, release tracking, archiving, and audit visibility.</div>
    </div>
    <a href="siems_hub.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left me-1"></i> SIEMS Hub
    </a>
</div>

<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4">
        <span class="section-title">QUICK ACTION BUTTONS</span>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-4 col-lg-2">
                <a href="document_requests.php?module=request" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-journal-plus d-block fs-4 mb-2"></i>Document Request
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="document_requests.php?module=processing" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-diagram-3 d-block fs-4 mb-2"></i>Processing Workflow
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="document_requests.php?module=generation" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-file-earmark-pdf d-block fs-4 mb-2"></i>Generation
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="document_requests.php?module=release" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-box-arrow-up-right d-block fs-4 mb-2"></i>Release Tracking
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="document_requests.php?module=archives" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-archive d-block fs-4 mb-2"></i>Archived Records
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="document_requests.php?module=audit" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-clock-history d-block fs-4 mb-2"></i>Audit Logs
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($module === ''): ?>
<div class="card-outline bg-white p-5 text-center">
    <i class="bi bi-grid-1x2 fs-1 text-success d-block mb-3"></i>
    <h4 class="dashboard-title mb-2">Subsystem 7 Module Launcher</h4>
    <div class="row-text">Choose a quick action button above to open one module at a time.</div>
</div>
<?php elseif ($module === 'request'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">DOCUMENT REQUEST MODULE</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12">
                        <input type="text" name="document_name" class="form-control row-text" placeholder="Document name" required style="border:2px solid #e2e8f0;border-radius:8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="standard_fee" step="0.01" min="0" class="form-control row-text" placeholder="Standard fee" required style="border:2px solid #e2e8f0;border-radius:8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="processing_days" min="1" class="form-control row-text" placeholder="Processing days" required style="border:2px solid #e2e8f0;border-radius:8px;">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="save_document_type" class="btn btn-success fw-bold" style="border-radius:8px;">Save Document Type</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-outline bg-white mt-4">
            <div class="balance-header py-3 px-4"><span class="section-title">ACTIVE DOCUMENT TYPES</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">DOCUMENT</th><th class="group-label-grey py-3">FEE</th><th class="group-label-grey py-3">DAYS</th></tr></thead>
                    <tbody>
                    <?php foreach ($documentTypes as $type): ?>
                        <tr>
                            <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($type['document_name']); ?></td>
                            <td class="py-3 row-text">PHP <?php echo number_format((float) $type['standard_fee'], 2); ?></td>
                            <td class="py-3 row-text"><?php echo (int) $type['processing_days']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($documentTypes)): ?><tr><td colspan="3" class="text-center text-muted py-5">No document types configured yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="section-title">ONLINE REQUEST INTAKE</span>
                <span class="badge bg-success rounded-pill px-3 py-2"><?php echo count($requests); ?> Requests</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">STUDENT</th><th class="group-label-grey py-3">DOCUMENT</th><th class="group-label-grey py-3">REQUESTED</th><th class="group-label-grey py-3">STATUS</th></tr></thead>
                    <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td class="py-3 px-4">
                                <div class="row-val"><?php echo htmlspecialchars($request['full_name']); ?></div>
                                <div class="row-text"><?php echo htmlspecialchars($request['student_id'] . ' - ' . ($request['program'] ?? '')); ?></div>
                            </td>
                            <td class="py-3">
                                <div class="row-val"><?php echo htmlspecialchars($request['document_type']); ?></div>
                                <div class="row-text"><?php echo htmlspecialchars($request['purpose'] ?? 'No purpose stated'); ?></div>
                            </td>
                            <td class="py-3 row-text"><?php echo date('M j, Y g:i A', strtotime($request['requested_at'])); ?></td>
                            <td class="py-3 row-text"><?php echo htmlspecialchars($request['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($requests)): ?><tr><td colspan="4" class="text-center text-muted py-5">No document requests found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'processing'): ?>
<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">DOCUMENT PROCESSING WORKFLOW</span></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#f8fafc;">
                <tr>
                    <th class="group-label-grey py-3 px-4">STUDENT</th>
                    <th class="group-label-grey py-3">DOCUMENT</th>
                    <th class="group-label-grey py-3">FEE</th>
                    <th class="group-label-grey py-3">CURRENT STATUS</th>
                    <th class="group-label-grey py-3">WORKFLOW ACTION</th>
                </tr>
            </thead>
    <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td class="py-3 px-4">
                        <div class="row-val"><?php echo htmlspecialchars($request['full_name']); ?></div>
                        <div class="row-text"><?php echo htmlspecialchars($request['student_id']); ?></div>
                    </td>
                    <td class="py-3">
                        <div class="row-val"><?php echo htmlspecialchars($request['document_type']); ?></div>
                        <div class="row-text"><?php echo htmlspecialchars($request['urgency'] ?? 'Regular'); ?></div>
                    </td>
                    <td class="py-3 row-text">PHP <?php echo number_format((float) ($request['fee_amount'] ?? 0), 2); ?></td>
                    <td class="py-3 row-text"><?php echo htmlspecialchars($request['status']); ?></td>
                    <td class="py-3 row-text">
                        Payment: <span class="badge bg-<?php echo in_array($request['payment_status'], ['Verified', 'Paid']) ? 'success' : 'warning'; ?>">
                            <?php echo htmlspecialchars($request['payment_status'] ?? 'Pending'); ?>
                        </span>
                        <?php if (($request['payment_receipt_no'] ?? '') !== ''): ?>
                            <br><small><?php echo htmlspecialchars($request['payment_receipt_no']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="py-3">
                        <?php if (($request['payment_status'] ?? '') !== 'Verified'): ?>
                            <div class="text-center mb-2">
                                <span class="badge bg-warning text-dark w-100 d-block mb-1">Payment Pending</span>
                                <a href="update_document_payment_status.php?request_id=<?php echo (int) $request['id']; ?>" class="btn btn-outline-success btn-sm w-100 fw-bold" style="border-radius:8px;">
                                    Mark Paid
                                </a>
                            </div>
                        <?php endif; ?>
                        <form method="POST" class="row g-2">
                            <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                            <div class="col-12">
                                <select name="new_status" class="form-select form-select-sm" style="border:2px solid #e2e8f0;border-radius:8px;">
                                    <?php foreach (['Pending', 'Processing', 'For Approval', 'Ready for Release', 'Released'] as $statusOption): ?>
                                        <option value="<?php echo htmlspecialchars($statusOption); ?>" <?php echo $request['status'] === $statusOption ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($statusOption); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="update_request_status" class="btn btn-success btn-sm w-100 fw-bold mt-1" style="border-radius:8px;">Update Workflow</button>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($requests)): ?><tr><td colspan="6" class="text-center text-muted py-5">No requests available for processing.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php elseif ($module === 'generation'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">DOCUMENT GENERATION</span></div>
            <div class="list-group list-group-flush">
                <?php foreach ($requests as $request): ?>
                    <a href="document_requests.php?module=generation&request_id=<?php echo (int) $request['id']; ?>" class="list-group-item list-group-item-action px-4 py-3">
                        <div class="row-val"><?php echo htmlspecialchars($request['document_type']); ?></div>
                        <div class="row-text"><?php echo htmlspecialchars($request['full_name']); ?></div>
                        <div class="small text-muted"><?php echo htmlspecialchars($request['status']); ?></div>
                    </a>
                <?php endforeach; ?>
                <?php if (empty($requests)): ?><div class="px-4 py-5 text-center text-muted">No document requests available.</div><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                #printable-document,
                #printable-document * {
                    visibility: visible;
                }

                #printable-document {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    padding: 24px;
                    background: #ffffff;
                }

                #printable-document .document-shell {
                    box-shadow: none !important;
                    border: 1px solid #000000 !important;
                }

                .btn,
                .balance-header,
                .list-group,
                .navbar,
                footer {
                    display: none !important;
                }
            }
        </style>
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="section-title">PRINTABLE DOCUMENT TEMPLATE</span>
                <?php if ($selectedRequest): ?>
                    <button type="button" onclick="window.print()" class="btn btn-success btn-sm fw-bold" style="border-radius:8px;">
                        <i class="bi bi-printer me-1"></i> Print Document
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <?php if ($selectedRequest): ?>
                    <div id="printable-document">
                        <div class="document-shell mx-auto" style="max-width:760px; border:1px solid #0f172a; box-shadow:0 18px 40px rgba(15,23,42,0.08); background:#ffffff;">
                            <div class="px-5 pt-5 pb-4" style="border-bottom:4px solid #166534;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div style="font-size:0.88rem; letter-spacing:0.2em; text-transform:uppercase; color:#166534; font-weight:700;">Official School Record</div>
                                        <div style="font-size:1.7rem; font-weight:800; color:#0f172a; line-height:1.2;">School Information & Enrollment Management System</div>
                                        <div style="font-size:0.95rem; color:#475569;">Office of the Registrar and Student Records Division</div>
                                    </div>
                                    <div class="text-end">
                                        <div style="font-size:0.8rem; text-transform:uppercase; color:#64748b;">Control No.</div>
                                        <div style="font-size:1rem; font-weight:700; color:#0f172a;">DOC-<?php echo str_pad((string) $selectedRequest['id'], 5, '0', STR_PAD_LEFT); ?></div>
                                        <div style="font-size:0.8rem; color:#64748b;" class="mt-2">Date Issued</div>
                                        <div style="font-size:0.95rem; font-weight:600; color:#0f172a;"><?php echo date('F j, Y'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-5 py-4">
                                <div class="text-center mb-4">
                                    <div style="font-size:1.4rem; font-weight:800; color:#0f172a; text-transform:uppercase; letter-spacing:0.08em;">
                                        <?php echo htmlspecialchars($selectedRequest['document_type']); ?>
                                    </div>
                                    <div style="font-size:0.92rem; color:#475569;">Generated upon approved request and subject to institutional release controls.</div>
                                </div>

                                <div class="row g-0 mb-4" style="border:1px solid #cbd5e1;">
                                    <div class="col-md-6 p-3" style="border-right:1px solid #cbd5e1;">
                                        <div style="font-size:0.76rem; text-transform:uppercase; letter-spacing:0.08em; color:#64748b;">Student Name</div>
                                        <div style="font-size:1rem; font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($selectedRequest['full_name']); ?></div>
                                    </div>
                                    <div class="col-md-6 p-3">
                                        <div style="font-size:0.76rem; text-transform:uppercase; letter-spacing:0.08em; color:#64748b;">Student Number</div>
                                        <div style="font-size:1rem; font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($selectedRequest['student_id']); ?></div>
                                    </div>
                                    <div class="col-md-6 p-3" style="border-top:1px solid #cbd5e1; border-right:1px solid #cbd5e1;">
                                        <div style="font-size:0.76rem; text-transform:uppercase; letter-spacing:0.08em; color:#64748b;">Program</div>
                                        <div style="font-size:0.98rem; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($selectedRequest['program'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="col-md-6 p-3" style="border-top:1px solid #cbd5e1;">
                                        <div style="font-size:0.76rem; text-transform:uppercase; letter-spacing:0.08em; color:#64748b;">Year Level</div>
                                        <div style="font-size:0.98rem; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($selectedRequest['year_level'] ?? 'N/A'); ?></div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div style="font-size:0.78rem; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">Certification Statement</div>
                                    <div style="font-size:1rem; line-height:1.9; color:#1e293b; text-align:justify;">
                                        This is to certify that the requested
                                        <strong><?php echo htmlspecialchars($selectedRequest['document_type']); ?></strong>
                                        for <strong><?php echo htmlspecialchars($selectedRequest['full_name']); ?></strong>,
                                        a student under the <strong><?php echo htmlspecialchars($selectedRequest['program'] ?? 'designated'); ?></strong> program,
                                        has been prepared by the Records Office based on the official data available in the institutional registry.
                                        This document is issued for the following declared purpose:
                                        <strong><?php echo htmlspecialchars($selectedRequest['purpose'] ?? 'official school transaction'); ?></strong>.
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="p-3 h-100" style="background:#f8fafc; border:1px solid #cbd5e1;">
                                            <div style="font-size:0.76rem; text-transform:uppercase; color:#64748b;">Request Status</div>
                                            <div style="font-size:1rem; font-weight:700; color:#166534;"><?php echo htmlspecialchars($selectedRequest['status']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 h-100" style="background:#f8fafc; border:1px solid #cbd5e1;">
                                            <div style="font-size:0.76rem; text-transform:uppercase; color:#64748b;">Requested On</div>
                                            <div style="font-size:1rem; font-weight:700; color:#0f172a;"><?php echo date('F j, Y', strtotime($selectedRequest['requested_at'])); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 h-100" style="background:#f8fafc; border:1px solid #cbd5e1;">
                                            <div style="font-size:0.76rem; text-transform:uppercase; color:#64748b;">Claim Reference</div>
                                            <div style="font-size:1rem; font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($selectedRequest['claim_reference'] ?? ('DOC-' . str_pad((string) $selectedRequest['id'], 5, '0', STR_PAD_LEFT))); ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4 pt-4" style="margin-top:24px;">
                                    <div class="col-md-6">
                                        <div style="border-top:1px solid #0f172a; padding-top:10px; text-align:center;">
                                            <div style="font-weight:700; color:#0f172a;">Registrar / Records Officer</div>
                                            <div style="font-size:0.85rem; color:#64748b;">Authorized Signature Over Printed Name</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div style="border-top:1px solid #0f172a; padding-top:10px; text-align:center;">
                                            <div style="font-weight:700; color:#0f172a;">Releasing Officer</div>
                                            <div style="font-size:0.85rem; color:#64748b;">Date and Control Verification</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3" style="border-top:1px dashed #94a3b8; font-size:0.82rem; color:#64748b;">
                                    This document is system-generated by SIEMS and is valid only when bearing the proper school signature or release verification.
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        Select a request from the left to open the printable document template.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'release'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">READY FOR RELEASE</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">REQUEST</th><th class="group-label-grey py-3">ACTION</th></tr></thead>
                    <tbody>
                    <?php
                    $readyRequests = array_values(array_filter($requests, static function ($request) {
                        return in_array($request['status'], ['Ready for Release', 'Released'], true);
                    }));
                    ?>
                    <?php foreach ($readyRequests as $request): ?>
                        <tr>
                            <td class="py-3 px-4">
                                <div class="row-val"><?php echo htmlspecialchars($request['document_type']); ?></div>
                                <div class="row-text"><?php echo htmlspecialchars($request['full_name']); ?></div>
                            </td>
                            <td class="py-3">
                                <form method="POST" class="row g-2">
                                    <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                    <input type="hidden" name="new_status" value="Released">
                                    <div class="col-12">
                                        <input type="text" name="claim_reference" class="form-control row-text" placeholder="Claim reference" style="border:2px solid #e2e8f0;border-radius:8px;">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="update_request_status" class="btn btn-success btn-sm fw-bold" style="border-radius:8px;">Mark Released</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($readyRequests)): ?><tr><td colspan="2" class="text-center text-muted py-5">No requests ready for release.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">DOCUMENT RELEASE TRACKING</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">DOCUMENT</th><th class="group-label-grey py-3">STUDENT</th><th class="group-label-grey py-3">REFERENCE</th><th class="group-label-grey py-3">RELEASED</th></tr></thead>
                    <tbody>
                    <?php foreach ($releases as $release): ?>
                        <tr>
                            <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($release['document_type']); ?></td>
                            <td class="py-3 row-text"><?php echo htmlspecialchars($release['student_id']); ?></td>
                            <td class="py-3 row-text"><?php echo htmlspecialchars($release['claim_reference'] ?? '-'); ?></td>
                            <td class="py-3 row-text"><?php echo !empty($release['released_at']) ? date('M j, Y g:i A', strtotime($release['released_at'])) : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($releases)): ?><tr><td colspan="4" class="text-center text-muted py-5">No released documents recorded yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'archives'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">ARCHIVE DOCUMENT RECORDS</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">REQUEST</th><th class="group-label-grey py-3">STATUS</th><th class="group-label-grey py-3">ACTION</th></tr></thead>
                    <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td class="py-3 px-4">
                                <div class="row-val"><?php echo htmlspecialchars($request['document_type']); ?></div>
                                <div class="row-text"><?php echo htmlspecialchars($request['student_id'] . ' - ' . $request['full_name']); ?></div>
                            </td>
                            <td class="py-3 row-text"><?php echo htmlspecialchars($request['status']); ?></td>
                            <td class="py-3">
                                <form method="POST">
                                    <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                    <button type="submit" name="archive_request" class="btn btn-outline-secondary btn-sm fw-bold" style="border-radius:8px;">Archive</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($requests)): ?><tr><td colspan="3" class="text-center text-muted py-5">No document requests available for archiving.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">ARCHIVED RECORDS MANAGEMENT</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">RECORD TYPE</th><th class="group-label-grey py-3">SOURCE</th><th class="group-label-grey py-3">ARCHIVED AT</th></tr></thead>
                    <tbody>
                    <?php foreach ($archives as $archive): ?>
                        <tr>
                            <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($archive['record_type']); ?></td>
                            <td class="py-3 row-text"><?php echo htmlspecialchars($archive['source_table'] . ' #' . $archive['source_id']); ?></td>
                            <td class="py-3 row-text"><?php echo date('M j, Y g:i A', strtotime($archive['archived_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($archives)): ?><tr><td colspan="3" class="text-center text-muted py-5">No archived document records yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'audit'): ?>
<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">USER MANAGEMENT: ACTIVITY LOGS & AUDIT TRAIL</span></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">TIME</th><th class="group-label-grey py-3">USER</th><th class="group-label-grey py-3">STUDENT</th><th class="group-label-grey py-3">ACTION</th><th class="group-label-grey py-3">DETAILS</th></tr></thead>
            <tbody>
            <?php foreach ($recentAuditLogs as $log): ?>
                <tr>
                    <td class="py-3 px-4 row-text"><?php echo date('M j, Y g:i A', strtotime($log['created_at'])); ?></td>
                    <td class="py-3 row-text"><?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></td>
                    <td class="py-3 row-text"><?php echo htmlspecialchars($log['student_id'] ?? '-'); ?></td>
                    <td class="py-3 row-val"><?php echo htmlspecialchars($log['action']); ?></td>
                    <td class="py-3 row-text"><?php echo htmlspecialchars(trim(($log['old_value'] ?? '') . ' ' . ($log['new_value'] ?? '')) ?: '-'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($recentAuditLogs)): ?><tr><td colspan="5" class="text-center text-muted py-5">No audit trail records found for this subsystem yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
