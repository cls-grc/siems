<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$studentId = $_SESSION['student_id'];
$requestId = (int) ($_GET['request_id'] ?? 0);

$request = siemsFetchOne("
    SELECT dr.*, u.full_name, u.program, u.year_level
    FROM document_requests dr
    INNER JOIN users u ON u.student_id = dr.student_id
    WHERE dr.id = ? AND dr.student_id = ? AND dr.status = 'Released'
    LIMIT 1
", [$requestId, $studentId]);

if (!$request) {
    $_SESSION['message'] = 'Document not found or not released for printing.';
    $_SESSION['msg_type'] = 'danger';
    header('Location: documents_credentials.php');
    exit;
}

$page_title = 'Print ' . htmlspecialchars($request['document_type']);
?>
<?php include '../includes/header.php'; ?>

<style>
@media print {
    body * { visibility: hidden; }
    #printable-document, #printable-document * { visibility: visible; }
    #printable-document { position: absolute; left: 0; top: 0; width: 100%; padding: 24px; background: #ffffff; }
    #printable-document .document-shell { box-shadow: none !important; border: 1px solid #000 !important; }
    .btn, .balance-header, .navbar, footer { display: none !important; }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="dashboard-title"><?php echo htmlspecialchars($request['document_type']); ?> <small class="text-muted">(#<?php echo str_pad($requestId, 5, '0', STR_PAD_LEFT); ?>)</small></h3>
    <div>
        <button onclick="window.print()" class="btn btn-success me-2 fw-bold" style="border-radius:8px;">
            <i class="bi bi-printer me-1"></i> Print Document
        </button>
        <a href="documents_credentials.php" class="btn btn-outline-secondary fw-bold" style="border-radius:8px;">← Back to Documents</a>
    </div>
</div>

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
                    <div style="font-size:1rem; font-weight:700; color:#0f172a;">DOC-<?php echo str_pad((string) $requestId, 5, '0', STR_PAD_LEFT); ?></div>
                    <div style="font-size:0.8rem; color:#64748b;" class="mt-2">Date Issued</div>
                    <div style="font-size:0.95rem; font-weight:600; color:#0f172a;"><?php echo date('F j, Y'); ?></div>
                </div>
            </div>
        </div>

        <div class="px-5 py-4">
                <?php if (str_contains($request['document_type'], 'Enrollment')): ?>
                    <div class="text-center mb-4">
                        <div style="font-size:1.4rem; font-weight:800; color:#0f172a; text-transform:uppercase; letter-spacing:0.08em;">
                            Certificate of Enrollment
                        </div>
                    </div>
                    <?php 
$currentPeriod = ['academic_year' => '2026-2027', 'semester' => '1st Semester'];
$enrollments = []; // Schedule data available once enrollment & scheduling complete
                    $totalUnits = 18; // Sample total units
                    $totalUnits = array_sum(array_column($enrollments, 'units'));
                    ?>
                    <div style="font-size:1rem; line-height:1.8; color:#1e293b; text-align:justify;">
                        <p>This is to certify that <strong><?php echo htmlspecialchars($request['full_name']); ?></strong> (ID: <strong><?php echo htmlspecialchars($request['student_id']); ?></strong>) is 
                        <strong>officially enrolled</strong> for <strong><?php echo $currentPeriod['academic_year']; ?> <?php echo $currentPeriod['semester']; ?> Semester</strong> in the 
                        <strong><?php echo htmlspecialchars($request['program']); ?></strong> program as a <strong><?php echo htmlspecialchars($request['year_level']); ?> student</strong>, taking a total of <strong><?php echo $totalUnits; ?> units</strong>.</p>
                        
                        <?php if (!empty($enrollments)): ?>
                            <div style="margin-top:24px;">
                                <div style="font-size:0.82rem; text-transform:uppercase; letter-spacing:0.05em; color:#64748b; margin-bottom:12px;">Current Semester Schedule</div>
                                <div style="font-size:0.88rem; line-height:1.6;">
                                    <?php foreach ($enrollments as $enr): ?>
                                        <div style="margin-bottom:8px;">
                                            <strong><?php echo htmlspecialchars($enr['code']); ?></strong> - <?php echo htmlspecialchars(substr($enr['description'], 0, 50)); ?>...
                                            <span style="float:right;">
                                                <?php echo htmlspecialchars($enr['day_of_week'] ?? 'TBA'); ?>, <?php echo htmlspecialchars($enr['time_start'] ?? 'TBA'); ?>-<?php echo htmlspecialchars($enr['time_end'] ?? 'TBA'); ?>
                                                <?php if ($enr['room_code']): ?> | <strong><?php echo htmlspecialchars($enr['room_code']); ?></strong><?php endif; ?>
                                                <?php if ($enr['teacher_name']): ?> | <?php echo htmlspecialchars($enr['teacher_name']); ?><?php endif; ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p>Schedule details will appear once classes are assigned and published.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <div style="font-size:1.4rem; font-weight:800; color:#0f172a; text-transform:uppercase; letter-spacing:0.08em;">
                            <?php echo htmlspecialchars($request['document_type']); ?>
                        </div>
                        <div style="font-size:0.92rem; color:#475569;">Generated upon approved request and subject to institutional release controls.</div>
                    </div>
                <?php endif; ?>

            <div class="row g-0 mb-4" style="border:1px solid #cbd5e1;">
                <div class="col-md-6 p-3" style="border-right:1px solid #cbd5e1;">
                    <div style="font-size:0.76rem; text-transform:uppercase; letter-spacing:0.08em; color:#64748b;">Student Name</div>
                    <div style="font-size:1rem; font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($request['full_name']); ?></div>
                </div>
                <div class="col-md-6 p-3">
                    <div style="font-size:0.76rem; text-transform:uppercase; letter-spacing:0.08em; color:#64748b;">Student Number</div>
                    <div style="font-size:1rem; font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($request['student_id']); ?></div>
                </div>
                <div class="col-md-6 p-3" style="border-top:1px solid #cbd5e1; border-right:1px solid #cbd5e1;">
                    <div style="font-size:0.76rem; text-transform:uppercase; letter-spacing:0.08em; color:#64748b;">Program</div>
                    <div style="font-size:0.98rem; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($request['program'] ?? 'N/A'); ?></div>
                </div>
                <div class="col-md-6 p-3" style="border-top:1px solid #cbd5e1;">
                    <div style="font-size:0.76rem; text-transform:uppercase; letter-spacing:0.08em; color:#64748b;">Year Level</div>
                    <div style="font-size:0.98rem; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($request['year_level'] ?? 'N/A'); ?></div>
                </div>
            </div>

            <div class="mb-4">
                <div style="font-size:0.78rem; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">Certification Statement</div>
                <div style="font-size:1rem; line-height:1.9; color:#1e293b; text-align:justify;">
                    This is to certify that the requested
                    <strong><?php echo htmlspecialchars($request['document_type']); ?></strong>
                    for <strong><?php echo htmlspecialchars($request['full_name']); ?></strong>,
                    a student under the <strong><?php echo htmlspecialchars($request['program'] ?? 'designated'); ?></strong> program,
                    has been prepared by the Records Office based on the official data available in the institutional registry.
                    This document is issued for the following declared purpose:
                    <strong><?php echo htmlspecialchars($request['purpose'] ?? 'official school transaction'); ?></strong>.
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="p-3 h-100" style="background:#f8fafc; border:1px solid #cbd5e1;">
                        <div style="font-size:0.76rem; text-transform:uppercase; color:#64748b;">Request Status</div>
                        <div style="font-size:1rem; font-weight:700; color:#166534;"><?php echo htmlspecialchars($request['status']); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 h-100" style="background:#f8fafc; border:1px solid #cbd5e1;">
                        <div style="font-size:0.76rem; text-transform:uppercase; color:#64748b;">Requested On</div>
                        <div style="font-size:1rem; font-weight:700; color:#0f172a;"><?php echo date('F j, Y', strtotime($request['requested_at'])); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 h-100" style="background:#f8fafc; border:1px solid #cbd5e1;">
                        <div style="font-size:0.76rem; text-transform:uppercase; color:#64748b;">Claim Reference</div>
                        <div style="font-size:1rem; font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($request['claim_reference'] ?? 'DOC-' . str_pad((string) $requestId, 5, '0', STR_PAD_LEFT)); ?></div>
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

<?php include '../includes/footer.php'; ?>

