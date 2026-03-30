<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';

requireSubsystemAccess('enrollment_registration');

$page_title = 'Enrollment & Registration';

$module = $_GET['module'] ?? '';
$allowedModules = ['application', 'subjects', 'validation', 'status', 'summary', 'audit'];
if (!in_array($module, $allowedModules, true)) {
    $module = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_application'])) {
    $applicationId = (int) ($_POST['application_id'] ?? 0);
    $newStatus = trim($_POST['new_status'] ?? 'Pending');
    $notes = trim($_POST['validation_notes'] ?? '');

    try {
        $application = siemsFetchOne("
            SELECT ea.*, ap.academic_year, ap.semester
            FROM enrollment_applications ea
            INNER JOIN academic_periods ap ON ap.id = ea.academic_period_id
            WHERE ea.id = ?
            LIMIT 1
        ", [$applicationId]);

        if (!$application) {
            throw new RuntimeException('Enrollment application not found.');
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE enrollment_applications SET status = ?, remarks = ? WHERE id = ?");
        $stmt->execute([$newStatus, $notes !== '' ? $notes : null, $applicationId]);

        if (siemsTableExists('enrollment_validations')) {
            $validation = siemsFetchOne("SELECT id FROM enrollment_validations WHERE application_id = ? ORDER BY id DESC LIMIT 1", [$applicationId]);
            $validationStatus = 'Pending';
            if (in_array($newStatus, ['Validated', 'Approved', 'Paid', 'Enrolled'], true)) {
                $validationStatus = 'Approved';
            } elseif ($newStatus === 'Returned') {
                $validationStatus = 'Returned';
            }

            if ($validation) {
                $stmt = $pdo->prepare("
                    UPDATE enrollment_validations
                    SET validated_by = ?, validation_status = ?, validation_notes = ?, validated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $validationStatus, $notes !== '' ? $notes : null, $validation['id']]);
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO enrollment_validations
                        (application_id, validated_by, validation_status, validation_notes, validated_at)
                    VALUES
                        (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$applicationId, $_SESSION['user_id'], $validationStatus, $notes !== '' ? $notes : null]);
            }
        }

        $pdo->prepare("UPDATE users SET enrollment_status = ? WHERE student_id = ?")->execute([$newStatus, $application['student_id']]);

        if ($newStatus === 'Enrolled' && siemsTableExists('enrollment_application_items') && siemsTableExists('enrollments')) {
            $items = siemsFetchAll("SELECT * FROM enrollment_application_items WHERE application_id = ?", [$applicationId]);
            $insertStmt = $pdo->prepare("
                INSERT IGNORE INTO enrollments
                    (student_id, subject_id, academic_year, semester, section_code)
                VALUES
                    (?, ?, ?, ?, ?)
            ");
            $itemUpdateStmt = $pdo->prepare("UPDATE enrollment_application_items SET status = 'Validated' WHERE id = ?");

            foreach ($items as $item) {
                $insertStmt->execute([
                    $application['student_id'],
                    $item['subject_id'],
                    $application['academic_year'],
                    $application['semester'],
                    $item['section_code'],
                ]);
                $itemUpdateStmt->execute([$item['id']]);
            }
        }

        siemsLogSubsystemEvent('Enrollment & Registration', 'Updated enrollment application', $application['student_id'], $application['status'], $newStatus);

        $pdo->commit();
        $_SESSION['message'] = 'Enrollment application updated successfully.';
        $_SESSION['msg_type'] = 'success';
        header('Location: enrollment_registration.php?module=validation&application_id=' . $applicationId);
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $_SESSION['message'] = $e->getMessage();
        $_SESSION['msg_type'] = 'danger';
        header('Location: enrollment_registration.php?module=validation');
        exit;
    }
}

$statusFilter = trim($_GET['status'] ?? '');
$search = trim($_GET['search'] ?? '');
$selectedApplicationId = (int) ($_GET['application_id'] ?? 0);

$applicationsSql = "
    SELECT ea.*, u.full_name, u.program, u.year_level, ap.academic_year, ap.semester,
           ev.validation_status, ev.validation_notes, ev.validated_at
    FROM enrollment_applications ea
    INNER JOIN users u ON u.student_id = ea.student_id
    INNER JOIN academic_periods ap ON ap.id = ea.academic_period_id
    LEFT JOIN enrollment_validations ev ON ev.application_id = ea.id
    WHERE 1 = 1
";
$applicationParams = [];

if ($statusFilter !== '') {
    $applicationsSql .= " AND ea.status = ?";
    $applicationParams[] = $statusFilter;
}
if ($search !== '') {
    $applicationsSql .= " AND (ea.student_id LIKE ? OR u.full_name LIKE ? OR u.program LIKE ?)";
    $like = '%' . $search . '%';
    $applicationParams[] = $like;
    $applicationParams[] = $like;
    $applicationParams[] = $like;
}

$applicationsSql .= " ORDER BY ea.id DESC";
$applications = siemsFetchAll($applicationsSql, $applicationParams);

if ($selectedApplicationId === 0 && !empty($applications)) {
    $selectedApplicationId = (int) $applications[0]['id'];
}

$selectedApplication = null;
$selectedItems = [];
if ($selectedApplicationId > 0) {
    $selectedApplication = siemsFetchOne("
        SELECT ea.*, u.full_name, u.program, u.year_level, ap.academic_year, ap.semester,
               ev.validation_status, ev.validation_notes, ev.validated_at
        FROM enrollment_applications ea
        INNER JOIN users u ON u.student_id = ea.student_id
        INNER JOIN academic_periods ap ON ap.id = ea.academic_period_id
        LEFT JOIN enrollment_validations ev ON ev.application_id = ea.id
        WHERE ea.id = ?
        LIMIT 1
    ", [$selectedApplicationId]);

    if ($selectedApplication) {
        $selectedItems = siemsFetchAll("
            SELECT eai.*, s.code, s.description, s.units
            FROM enrollment_application_items eai
            INNER JOIN subjects s ON s.id = eai.subject_id
            WHERE eai.application_id = ?
            ORDER BY s.code ASC
        ", [$selectedApplicationId]);
    }
}

$summary = [
    'pending' => siemsCountRows('enrollment_applications', "status IN ('Pending', 'Submitted', 'For Review')"),
    'validated' => siemsCountRows('enrollment_applications', "status IN ('Validated', 'Approved', 'Paid')"),
    'enrolled' => siemsCountRows('enrollment_applications', "status = 'Enrolled'"),
    'returned' => siemsCountRows('enrollment_applications', "status = 'Returned'"),
];

$openPeriods = siemsGetOpenAcademicPeriods();
$recentAuditLogs = siemsTableExists('audit_log') ? siemsFetchAll("
    SELECT al.*, u.full_name AS user_name
    FROM audit_log al
    LEFT JOIN users u ON u.id = al.user_id
    WHERE " . (siemsColumnExists('audit_log', 'subsystem') ? "al.subsystem = 'Enrollment & Registration'" : "al.action LIKE '%enrollment application%'") . "
    ORDER BY al.created_at DESC, al.id DESC
    LIMIT 15
") : [];

$selectedApplicationLogs = ($selectedApplication && siemsTableExists('audit_log')) ? siemsFetchAll("
    SELECT al.*, u.full_name AS user_name
    FROM audit_log al
    LEFT JOIN users u ON u.id = al.user_id
    WHERE al.student_id = ?
    ORDER BY al.created_at DESC, al.id DESC
    LIMIT 12
", [$selectedApplication['student_id']]) : [];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-journal-check text-success me-2"></i>Enrollment & Registration</h3>
        <div class="row-text">Subsystem 2 for online applications, subject selection, validation workflow, status monitoring, reports, and audit visibility.</div>
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
                <a href="enrollment_registration.php?module=application<?php echo $selectedApplicationId ? '&application_id=' . $selectedApplicationId : ''; ?>" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-send-check d-block fs-4 mb-2"></i>Online Application
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="enrollment_registration.php?module=subjects<?php echo $selectedApplicationId ? '&application_id=' . $selectedApplicationId : ''; ?>" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-list-check d-block fs-4 mb-2"></i>Subject Selection
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="enrollment_registration.php?module=validation<?php echo $selectedApplicationId ? '&application_id=' . $selectedApplicationId : ''; ?>" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-check2-square d-block fs-4 mb-2"></i>Validation
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="enrollment_registration.php?module=status<?php echo $selectedApplicationId ? '&application_id=' . $selectedApplicationId : ''; ?>" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-activity d-block fs-4 mb-2"></i>Status Monitoring
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="enrollment_registration.php?module=summary" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-bar-chart-line d-block fs-4 mb-2"></i>Summary Report
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="enrollment_registration.php?module=audit<?php echo $selectedApplicationId ? '&application_id=' . $selectedApplicationId : ''; ?>" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-clock-history d-block fs-4 mb-2"></i>Audit Logs
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($module === ''): ?>
<div class="card-outline bg-white p-5 text-center">
    <i class="bi bi-grid-1x2 fs-1 text-success d-block mb-3"></i>
    <h4 class="dashboard-title mb-2">Subsystem 2 Module Launcher</h4>
    <div class="row-text">Choose a quick action button above to open one module at a time.</div>
</div>
<?php elseif (in_array($module, ['application', 'subjects', 'validation', 'status', 'audit'], true)): ?>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4">
                <span class="section-title">APPLICATION QUEUE</span>
            </div>
            <div class="card-body p-4">
                <form method="GET" class="row g-2 mb-3">
                    <input type="hidden" name="module" value="<?php echo htmlspecialchars($module); ?>">
                    <div class="col-md-5">
                        <select name="status" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            <option value="">All statuses</option>
                            <?php foreach (['Pending', 'Submitted', 'For Review', 'Validated', 'Approved', 'Paid', 'Enrolled', 'Returned'] as $status): ?>
                                <option value="<?php echo $status; ?>" <?php echo $statusFilter === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control row-text" placeholder="Search student ID, name, or program" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                </form>
                <div style="max-height: 700px; overflow-y: auto;">
                    <?php foreach ($applications as $application): ?>
                        <a href="?module=<?php echo urlencode($module); ?>&application_id=<?php echo (int) $application['id']; ?><?php echo $statusFilter !== '' ? '&status=' . urlencode($statusFilter) : ''; ?><?php echo $search !== '' ? '&search=' . urlencode($search) : ''; ?>" class="text-decoration-none">
                            <div class="p-3 mb-2 rounded-3 <?php echo $selectedApplicationId === (int) $application['id'] ? 'border border-success bg-light' : 'border'; ?>" style="border-color: #e2e8f0 !important;">
                                <div class="row-val"><?php echo htmlspecialchars($application['full_name']); ?></div>
                                <div class="row-text"><?php echo htmlspecialchars($application['student_id']); ?> | <?php echo htmlspecialchars($application['program']); ?> | Year <?php echo htmlspecialchars((string) $application['year_level']); ?></div>
                                <div class="group-label-grey mt-1"><?php echo htmlspecialchars($application['academic_year'] . ' / ' . $application['semester']); ?> | <?php echo htmlspecialchars($application['status']); ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    <?php if (empty($applications)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                            No applications found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <?php if ($selectedApplication): ?>
            <?php if ($module === 'application'): ?>
                <div class="card-outline bg-white mb-4">
                    <div class="balance-header py-3 px-4">
                        <span class="section-title">ONLINE ENROLLMENT APPLICATION</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="group-label-grey">STUDENT</div>
                                <div class="row-val"><?php echo htmlspecialchars($selectedApplication['full_name']); ?></div>
                                <div class="row-text"><?php echo htmlspecialchars($selectedApplication['student_id']); ?> | <?php echo htmlspecialchars($selectedApplication['program']); ?></div>
                            </div>
                            <div class="col-md-3">
                                <div class="group-label-grey">TERM</div>
                                <div class="row-val"><?php echo htmlspecialchars($selectedApplication['academic_year'] . ' / ' . $selectedApplication['semester']); ?></div>
                            </div>
                            <div class="col-md-3">
                                <div class="group-label-grey">CURRENT STATUS</div>
                                <div class="row-val"><?php echo htmlspecialchars($selectedApplication['status']); ?></div>
                            </div>
                            <div class="col-12">
                                <div class="group-label-grey">APPLICANT NOTES</div>
                                <div class="row-text"><?php echo htmlspecialchars($selectedApplication['remarks'] ?? 'No remarks submitted.'); ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="group-label-grey">VALIDATION STATE</div>
                                <div class="row-val"><?php echo htmlspecialchars($selectedApplication['validation_status'] ?? 'Pending'); ?></div>
                            </div>
                            <div class="col-md-8">
                                <div class="group-label-grey">VALIDATION NOTES</div>
                                <div class="row-text"><?php echo htmlspecialchars($selectedApplication['validation_notes'] ?? 'Awaiting reviewer update.'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($module === 'subjects'): ?>
                <div class="card-outline bg-white mb-4">
                    <div class="balance-header py-3 px-4">
                        <span class="section-title">PRE-ENROLLMENT SUBJECT SELECTION</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row-text mb-3">Selected subject load for the chosen online enrollment application.</div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="group-label-grey py-3">SUBJECT</th>
                                        <th class="group-label-grey py-3">UNITS</th>
                                        <th class="group-label-grey py-3">SECTION</th>
                                        <th class="group-label-grey py-3">ITEM STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($selectedItems as $item): ?>
                                        <tr>
                                            <td class="py-3">
                                                <div class="row-val"><?php echo htmlspecialchars($item['code']); ?></div>
                                                <div class="row-text"><?php echo htmlspecialchars($item['description']); ?></div>
                                            </td>
                                            <td class="py-3 row-text"><?php echo htmlspecialchars((string) $item['units']); ?></td>
                                            <td class="py-3 row-text"><?php echo htmlspecialchars($item['section_code'] ?? 'TBA'); ?></td>
                                            <td class="py-3 row-val"><?php echo htmlspecialchars($item['status']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($selectedItems)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No subject items found for this application.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif ($module === 'validation'): ?>
                <div class="card-outline bg-white mb-4">
                    <div class="balance-header py-3 px-4">
                        <span class="section-title">ENROLLMENT VALIDATION & APPROVAL</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="group-label-grey">STUDENT</div>
                                <div class="row-val"><?php echo htmlspecialchars($selectedApplication['full_name']); ?></div>
                                <div class="row-text"><?php echo htmlspecialchars($selectedApplication['student_id']); ?> | <?php echo htmlspecialchars($selectedApplication['program']); ?></div>
                            </div>
                            <div class="col-md-3">
                                <div class="group-label-grey">TERM</div>
                                <div class="row-val"><?php echo htmlspecialchars($selectedApplication['academic_year'] . ' / ' . $selectedApplication['semester']); ?></div>
                            </div>
                            <div class="col-md-3">
                                <div class="group-label-grey">CURRENT STATUS</div>
                                <div class="row-val"><?php echo htmlspecialchars($selectedApplication['status']); ?></div>
                            </div>
                        </div>

                        <form method="POST" class="mb-4">
                            <input type="hidden" name="application_id" value="<?php echo (int) $selectedApplication['id']; ?>">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label row-val">Next Status</label>
                                    <select name="new_status" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                        <?php foreach (['For Review', 'Validated', 'Approved', 'Paid', 'Enrolled', 'Returned'] as $status): ?>
                                            <option value="<?php echo $status; ?>" <?php echo $selectedApplication['status'] === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label row-val">Validation Notes</label>
                                    <input type="text" name="validation_notes" class="form-control row-text" value="<?php echo htmlspecialchars($selectedApplication['validation_notes'] ?? $selectedApplication['remarks'] ?? ''); ?>" placeholder="Requirements checked, schedule confirmed, payment routed, etc." style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="update_application" class="btn btn-success fw-bold px-4" style="border-radius: 8px;">
                                        <i class="bi bi-check2-square me-1"></i> Update Application
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php elseif ($module === 'status'): ?>
                <div class="card-outline bg-white mb-4">
                    <div class="balance-header py-3 px-4">
                        <span class="section-title">ENROLLMENT STATUS MONITORING</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3"><div class="border rounded-3 p-3 h-100" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">PENDING</div><div class="val-total-amount fs-3 mt-2" style="color:#f59e0b;"><?php echo $summary['pending']; ?></div></div></div>
                            <div class="col-md-3"><div class="border rounded-3 p-3 h-100" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">VALIDATED</div><div class="val-total-amount fs-3 mt-2" style="color:#0ea5e9;"><?php echo $summary['validated']; ?></div></div></div>
                            <div class="col-md-3"><div class="border rounded-3 p-3 h-100" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">ENROLLED</div><div class="val-total-amount fs-3 mt-2 text-success"><?php echo $summary['enrolled']; ?></div></div></div>
                            <div class="col-md-3"><div class="border rounded-3 p-3 h-100" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">RETURNED</div><div class="val-total-amount fs-3 mt-2" style="color:#ef4444;"><?php echo $summary['returned']; ?></div></div></div>
                        </div>
                        <div class="border rounded-3 p-4" style="border-color: #e2e8f0 !important;">
                            <div class="group-label-grey">SELECTED APPLICATION</div>
                            <div class="row-val mt-2"><?php echo htmlspecialchars($selectedApplication['full_name']); ?></div>
                            <div class="row-text"><?php echo htmlspecialchars($selectedApplication['student_id']); ?> | <?php echo htmlspecialchars($selectedApplication['academic_year'] . ' / ' . $selectedApplication['semester']); ?></div>
                            <div class="row-text mt-2">Current status: <strong><?php echo htmlspecialchars($selectedApplication['status']); ?></strong></div>
                            <div class="row-text">Validation state: <strong><?php echo htmlspecialchars($selectedApplication['validation_status'] ?? 'Pending'); ?></strong></div>
                        </div>
                    </div>
                </div>
            <?php elseif ($module === 'audit'): ?>
                <div class="card-outline bg-white mb-4">
                    <div class="balance-header py-3 px-4">
                        <span class="section-title">AUDIT TRAIL FOR SELECTED APPLICATION</span>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($selectedApplicationLogs)): ?>
                            <?php foreach ($selectedApplicationLogs as $log): ?>
                                <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="row-val"><?php echo htmlspecialchars($log['action']); ?></div>
                                        <div class="group-label-grey"><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($log['created_at']))); ?></div>
                                    </div>
                                    <div class="row-text mt-2">Actor: <?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></div>
                                    <?php if (!empty($log['old_value']) || !empty($log['new_value'])): ?>
                                        <div class="group-label-grey mt-2">Change: <?php echo htmlspecialchars(trim(($log['old_value'] ?? '-') . ' -> ' . ($log['new_value'] ?? '-'))); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted">No audit trail entries found for this application yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card-outline bg-white p-5 text-center">
                <i class="bi bi-journal-x fs-1 text-muted d-block mb-3"></i>
                <div class="row-text">Select an application from the left to open this module.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php elseif ($module === 'summary'): ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4">
                <span class="section-title">ENROLLMENT SUMMARY REPORT</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-3"><div class="border rounded-3 p-3 text-center h-100" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">OPEN TERMS</div><div class="val-total-amount fs-3 mt-2 text-success"><?php echo count($openPeriods); ?></div></div></div>
                    <div class="col-md-3"><div class="border rounded-3 p-3 text-center h-100" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">PENDING</div><div class="val-total-amount fs-3 mt-2" style="color:#f59e0b;"><?php echo $summary['pending']; ?></div></div></div>
                    <div class="col-md-3"><div class="border rounded-3 p-3 text-center h-100" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">VALIDATED</div><div class="val-total-amount fs-3 mt-2" style="color:#0ea5e9;"><?php echo $summary['validated']; ?></div></div></div>
                    <div class="col-md-3"><div class="border rounded-3 p-3 text-center h-100" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">ENROLLED</div><div class="val-total-amount fs-3 mt-2 text-success"><?php echo $summary['enrolled']; ?></div></div></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #f8fafc;">
                            <tr>
                                <th class="group-label-grey py-3">STUDENT</th>
                                <th class="group-label-grey py-3">PROGRAM</th>
                                <th class="group-label-grey py-3">TERM</th>
                                <th class="group-label-grey py-3">STATUS</th>
                                <th class="group-label-grey py-3">VALIDATION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <tr>
                                    <td class="py-3">
                                        <div class="row-val"><?php echo htmlspecialchars($application['full_name']); ?></div>
                                        <div class="row-text"><?php echo htmlspecialchars($application['student_id']); ?></div>
                                    </td>
                                    <td class="py-3 row-text"><?php echo htmlspecialchars($application['program']); ?></td>
                                    <td class="py-3 row-text"><?php echo htmlspecialchars($application['academic_year'] . ' / ' . $application['semester']); ?></td>
                                    <td class="py-3 row-val"><?php echo htmlspecialchars($application['status']); ?></td>
                                    <td class="py-3 row-text"><?php echo htmlspecialchars($application['validation_status'] ?? 'Pending'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($applications)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">No enrollment summary data found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4">
                <span class="section-title">STATUS BREAKDOWN</span>
            </div>
            <div class="card-body p-4">
                <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">Pending Review</div><div class="row-val mt-1"><?php echo $summary['pending']; ?></div></div>
                <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">Validated Pipeline</div><div class="row-val mt-1"><?php echo $summary['validated']; ?></div></div>
                <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">Officially Enrolled</div><div class="row-val mt-1"><?php echo $summary['enrolled']; ?></div></div>
                <div class="border rounded-3 p-3" style="border-color: #e2e8f0 !important;"><div class="group-label-grey">Returned Applications</div><div class="row-val mt-1"><?php echo $summary['returned']; ?></div></div>
            </div>
        </div>
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4">
                <span class="section-title">RECENT USER ACTIVITY</span>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($recentAuditLogs)): ?>
                    <?php foreach ($recentAuditLogs as $log): ?>
                        <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;">
                            <div class="row-val"><?php echo htmlspecialchars($log['action']); ?></div>
                            <div class="row-text mt-1"><?php echo htmlspecialchars($log['student_id'] ?? 'General event'); ?></div>
                            <div class="group-label-grey mt-2"><?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?> | <?php echo htmlspecialchars(date('M j, Y H:i', strtotime($log['created_at']))); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted">No recent subsystem activity found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
