<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';

requireSubsystemAccess('grades_assessment');

$page_title = 'Grades & Assessment';
$facultyProgram = null;
if ($_SESSION['role'] === 'faculty') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT faculty_program FROM users WHERE student_id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $facultyProgram = $stmt->fetchColumn();
}

$studentQuery = "SELECT student_id, full_name FROM users WHERE role = 'student'";
$params = [];
if ($_SESSION['role'] === 'faculty' && $facultyProgram) {
    $studentQuery .= " AND program = ?";
    $params[] = $facultyProgram;
}
$studentQuery .= " ORDER BY full_name ASC";
$students = siemsFetchAll($studentQuery, $params);
$subjects = siemsFetchAll("SELECT id, code, description FROM subjects ORDER BY code ASC");

$module = $_GET['module'] ?? '';
$allowedModules = ['encoding', 'verification', 'viewer', 'corrections', 'reports', 'audit'];
if (!in_array($module, $allowedModules, true)) {
    $module = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['save_grade_submission'])) {
            $studentId = trim($_POST['student_id']);
            $subjectId = (int) $_POST['subject_id'];
            $termLabel = trim($_POST['term_label']);
            $gradeValue = (float) $_POST['grade_value'];
            $status = trim($_POST['grade_status']);

            $stmt = $pdo->prepare("
                INSERT INTO grade_submissions
                    (student_id, subject_id, term_label, grade_value, encoded_by, verified_by, status, submitted_at)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $studentId,
                $subjectId,
                $termLabel,
                $gradeValue,
                $_SESSION['user_id'],
                in_array($status, ['Approved'], true) ? $_SESSION['user_id'] : null,
                $status,
            ]);

            if ($status === 'Approved') {
                $stmt = $pdo->prepare("
                    INSERT INTO academic_records
                        (student_id, subject_id, academic_year, semester, final_grade, remarks)
                    VALUES
                        (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE final_grade = VALUES(final_grade), remarks = VALUES(remarks)
                ");
                $stmt->execute([
                    $studentId,
                    $subjectId,
                    trim($_POST['academic_year']),
                    trim($_POST['semester']),
                    $gradeValue,
                    $gradeValue <= 3.0 ? 'Passed' : 'Failed',
                ]);
            }

            siemsLogSubsystemEvent('Grades & Assessment', 'Saved grade submission', $studentId, null, $termLabel . ' / ' . $gradeValue);
            $_SESSION['message'] = 'Grade submission saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: grades_assessment.php?module=encoding');
            exit;
        } elseif (isset($_POST['verify_grade'])) {
            $submissionId = (int) $_POST['submission_id'];
            $newStatus = trim($_POST['new_status']);

            $submission = siemsFetchOne("SELECT * FROM grade_submissions WHERE id = ? LIMIT 1", [$submissionId]);
            if ($submission) {
                $pdo->prepare("
                    UPDATE grade_submissions
                    SET status = ?, verified_by = ?, submitted_at = COALESCE(submitted_at, NOW())
                    WHERE id = ?
                ")->execute([$newStatus, $_SESSION['user_id'], $submissionId]);

                if ($newStatus === 'Approved') {
                    $pdo->prepare("
                        INSERT INTO academic_records
                            (student_id, subject_id, academic_year, semester, final_grade, remarks)
                        VALUES
                            (?, ?, ?, ?, ?, ?)
                    ")->execute([
                        $submission['student_id'],
                        $submission['subject_id'],
                        trim($_POST['verify_academic_year']),
                        trim($_POST['verify_semester']),
                        $submission['grade_value'],
                        $submission['grade_value'] <= 3.0 ? 'Passed' : 'Failed',
                    ]);
                }

                siemsLogSubsystemEvent('Grades & Assessment', 'Verified grade submission', $submission['student_id'], $submission['status'], $newStatus);
                $_SESSION['message'] = 'Grade status updated successfully.';
            }

            $_SESSION['msg_type'] = 'success';
            header('Location: grades_assessment.php?module=verification');
            exit;
        } elseif (isset($_POST['resolve_change_request'])) {
            $requestId = (int) $_POST['request_id'];
            $resolution = trim($_POST['resolution_status']);

            $request = siemsFetchOne("SELECT * FROM grade_change_requests WHERE id = ? LIMIT 1", [$requestId]);
            if ($request) {
                $pdo->prepare("
                    UPDATE grade_change_requests
                    SET status = ?, resolved_by = ?, resolved_at = NOW()
                    WHERE id = ?
                ")->execute([$resolution, $_SESSION['user_id'], $requestId]);

                if ($resolution === 'Approved') {
                    $pdo->prepare("UPDATE grade_submissions SET grade_value = ?, status = 'Approved', verified_by = ? WHERE id = ?")
                        ->execute([$request['new_grade'], $_SESSION['user_id'], $request['grade_submission_id']]);
                }

                siemsLogSubsystemEvent('Grades & Assessment', 'Resolved grade correction request', null, $request['status'], $resolution);
                $_SESSION['message'] = 'Grade change request updated successfully.';
            }

            $_SESSION['msg_type'] = 'success';
            header('Location: grades_assessment.php?module=corrections');
            exit;
        }
    } catch (Throwable $e) {
        $_SESSION['message'] = 'Unable to save grade data. Please check for duplicate or invalid values.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: grades_assessment.php' . ($module !== '' ? '?module=' . urlencode($module) : ''));
        exit;
    }
}

$overview = siemsGetAdminGradesOverview();
$submissions = $overview['submissions'];
$changeRequests = $overview['change_requests'];
$recordsCount = $overview['records'];
$postedRecords = siemsTableExists('academic_records') ? siemsFetchAll("
    SELECT ar.*, s.code, s.description, u.full_name AS student_name
    FROM academic_records ar
    INNER JOIN subjects s ON s.id = ar.subject_id
    INNER JOIN users u ON u.student_id = ar.student_id
    ORDER BY ar.academic_year DESC, ar.semester DESC, u.full_name ASC, s.code ASC
") : [];
$recentAuditLogs = siemsTableExists('audit_log') ? siemsFetchAll("
    SELECT al.*, u.full_name AS user_name
    FROM audit_log al
    LEFT JOIN users u ON u.id = al.user_id
    WHERE " . (siemsColumnExists('audit_log', 'subsystem') ? "al.subsystem = 'Grades & Assessment'" : "al.action LIKE '%grade%'") . "
    ORDER BY al.created_at DESC, al.id DESC
    LIMIT 20
") : [];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-clipboard-data text-success me-2"></i>Grades & Assessment</h3>
        <div class="row-text">Subsystem 5 for grade encoding, verification, student grade viewing, corrections, reports, and audit visibility.</div>
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
                <a href="grades_assessment.php?module=encoding" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-pencil-square d-block fs-4 mb-2"></i>Grade Encoding
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="grades_assessment.php?module=verification" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-check2-circle d-block fs-4 mb-2"></i>Verification
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="grades_assessment.php?module=viewer" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-person-vcard d-block fs-4 mb-2"></i>Student Viewer
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="grades_assessment.php?module=corrections" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-arrow-repeat d-block fs-4 mb-2"></i>Corrections
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="grades_assessment.php?module=reports" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-bar-chart-line d-block fs-4 mb-2"></i>Reports
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="grades_assessment.php?module=audit" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-clock-history d-block fs-4 mb-2"></i>Audit Logs
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($module === ''): ?>
<div class="card-outline bg-white p-5 text-center">
    <i class="bi bi-grid-1x2 fs-1 text-success d-block mb-3"></i>
    <h4 class="dashboard-title mb-2">Subsystem 5 Module Launcher</h4>
    <div class="row-text">Choose a quick action button above to open one module at a time.</div>
</div>
<?php elseif ($module === 'encoding'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">GRADE ENCODING</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="student_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select student</option><?php foreach ($students as $student): ?><option value="<?php echo htmlspecialchars($student['student_id']); ?>"><?php echo htmlspecialchars($student['full_name'] . ' (' . $student['student_id'] . ')'); ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><select name="subject_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select subject</option><?php foreach ($subjects as $subject): ?><option value="<?php echo (int) $subject['id']; ?>"><?php echo htmlspecialchars($subject['code'] . ' - ' . $subject['description']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6">
                        <select name="term_label" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select grading period</option>
                            <option value="Prelim">Prelim</option>
                            <option value="Midterm">Midterm</option>
                            <option value="Finals">Finals</option>
                        </select>
                    </div>
                    <div class="col-md-6"><input type="number" step="0.01" min="1" max="5" name="grade_value" class="form-control row-text" placeholder="Grade" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="text" name="academic_year" value="2026-2027" class="form-control row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><select name="semester" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>1st</option><option>2nd</option><option>Summer</option></select></div>
                    <div class="col-12"><select name="grade_status" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Draft</option><option>Submitted</option><option>Pending Verification</option><option>Approved</option></select></div>
                    <div class="col-12"><button type="submit" name="save_grade_submission" class="btn btn-success fw-bold" style="border-radius:8px;">Save Grade</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">RECENT GRADE ENTRIES</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">STUDENT</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">TERM</th><th class="group-label-grey py-3">GRADE</th><th class="group-label-grey py-3">STATUS</th></tr></thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($submission['student_name']); ?></div><div class="row-text"><?php echo htmlspecialchars($submission['student_id']); ?></div></td>
                                <td class="py-3"><div class="row-val"><?php echo htmlspecialchars($submission['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($submission['description']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($submission['term_label']); ?></td>
                                <td class="py-3 row-val"><?php echo htmlspecialchars((string) $submission['grade_value']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($submission['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($submissions)): ?><tr><td colspan="5" class="text-center text-muted py-5">No grade submissions yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'verification'): ?>
<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">GRADE VERIFICATION & APPROVAL</span></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">STUDENT</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">TERM</th><th class="group-label-grey py-3">GRADE</th><th class="group-label-grey py-3">STATUS</th><th class="group-label-grey py-3 px-4">VERIFY</th></tr></thead>
            <tbody>
                <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($submission['student_name']); ?></div><div class="row-text"><?php echo htmlspecialchars($submission['student_id']); ?></div></td>
                        <td class="py-3"><div class="row-val"><?php echo htmlspecialchars($submission['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($submission['description']); ?></div></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($submission['term_label']); ?></td>
                        <td class="py-3 row-val"><?php echo htmlspecialchars((string) $submission['grade_value']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($submission['status']); ?></td>
                        <td class="py-3 px-4">
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="submission_id" value="<?php echo (int) $submission['id']; ?>">
                                <input type="hidden" name="verify_academic_year" value="2026-2027">
                                <input type="hidden" name="verify_semester" value="1st">
                                <select name="new_status" class="form-select form-select-sm row-text" style="min-width:150px;">
                                    <option <?php echo $submission['status'] === 'Submitted' ? 'selected' : ''; ?>>Submitted</option>
                                    <option <?php echo $submission['status'] === 'Pending Verification' ? 'selected' : ''; ?>>Pending Verification</option>
                                    <option <?php echo $submission['status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                </select>
                                <button type="submit" name="verify_grade" class="btn btn-sm btn-outline-success fw-bold">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($submissions)): ?><tr><td colspan="6" class="text-center text-muted py-5">No grade submissions found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php elseif ($module === 'viewer'): ?>
<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">STUDENT GRADE VIEWER</span></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">STUDENT</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">TERM</th><th class="group-label-grey py-3">FINAL GRADE</th><th class="group-label-grey py-3">REMARKS</th></tr></thead>
            <tbody>
                <?php foreach ($postedRecords as $record): ?>
                    <tr>
                        <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($record['student_name']); ?></div><div class="row-text"><?php echo htmlspecialchars($record['student_id']); ?></div></td>
                        <td class="py-3"><div class="row-val"><?php echo htmlspecialchars($record['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($record['description']); ?></div></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($record['academic_year'] . ' / ' . $record['semester']); ?></td>
                        <td class="py-3 row-val"><?php echo htmlspecialchars((string) $record['final_grade']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($record['remarks'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($postedRecords)): ?><tr><td colspan="5" class="text-center text-muted py-5">No posted academic records yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php elseif ($module === 'corrections'): ?>
<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">GRADE CORRECTION / REQUEST HANDLING</span></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">SUBJECT</th><th class="group-label-grey py-3">OLD</th><th class="group-label-grey py-3">NEW</th><th class="group-label-grey py-3">REASON</th><th class="group-label-grey py-3 px-4">ACTION</th></tr></thead>
            <tbody>
                <?php foreach ($changeRequests as $request): ?>
                    <tr>
                        <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($request['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($request['description']); ?></div></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars((string) $request['old_grade']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars((string) $request['new_grade']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($request['reason']); ?></td>
                        <td class="py-3 px-4">
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                <select name="resolution_status" class="form-select form-select-sm row-text" style="min-width:130px;"><option>Pending</option><option>Approved</option><option>Rejected</option></select>
                                <button type="submit" name="resolve_change_request" class="btn btn-sm btn-outline-success fw-bold">Resolve</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($changeRequests)): ?><tr><td colspan="5" class="text-center text-muted py-5">No grade change requests found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php elseif ($module === 'reports'): ?>
<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">GRADE SUBMISSIONS</div><div class="val-total-amount fs-2 mt-2 text-success"><?php echo count($submissions); ?></div></div></div>
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">PENDING VERIFICATION</div><div class="val-total-amount fs-2 mt-2" style="color:#f59e0b;"><?php echo count(array_filter($submissions, fn($s) => in_array($s['status'], ['Submitted', 'Pending Verification'], true))); ?></div></div></div>
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">POSTED RECORDS</div><div class="val-total-amount fs-2 mt-2" style="color:#0ea5e9;"><?php echo $recordsCount; ?></div></div></div>
</div>
<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">GRADE REPORTS & SUMMARY</span></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">STUDENT</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">TERM</th><th class="group-label-grey py-3">GRADE</th><th class="group-label-grey py-3">STATUS</th></tr></thead>
            <tbody>
                <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($submission['student_name']); ?></div><div class="row-text"><?php echo htmlspecialchars($submission['student_id']); ?></div></td>
                        <td class="py-3"><div class="row-val"><?php echo htmlspecialchars($submission['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($submission['description']); ?></div></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($submission['term_label']); ?></td>
                        <td class="py-3 row-val"><?php echo htmlspecialchars((string) $submission['grade_value']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($submission['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($submissions)): ?><tr><td colspan="5" class="text-center text-muted py-5">No grade report data found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php elseif ($module === 'audit'): ?>
<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">USER ACTIVITY LOGS & AUDIT TRAIL</span></div>
    <div class="card-body p-4">
        <?php if (!empty($recentAuditLogs)): ?>
            <?php foreach ($recentAuditLogs as $log): ?>
                <div class="border rounded-3 p-3 mb-3" style="border-color:#e2e8f0 !important;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="row-val"><?php echo htmlspecialchars($log['action']); ?></div>
                        <div class="group-label-grey"><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($log['created_at']))); ?></div>
                    </div>
                    <div class="row-text mt-2"><?php echo htmlspecialchars($log['student_id'] ?? 'General event'); ?></div>
                    <div class="group-label-grey mt-2">By: <?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-muted">No recent subsystem activity found.</div>
        <?php endif; ?>
        <a href="audit_trail.php" class="btn btn-outline-success fw-bold mt-3" style="border-radius:8px;">Open Full Audit Trail</a>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
