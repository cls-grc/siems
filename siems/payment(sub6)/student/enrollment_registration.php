<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Enrollment & Registration';
$studentId = $_SESSION['student_id'];
$periods = siemsGetOpenAcademicPeriods();
$availableSubjects = siemsGetEnrollmentSubjectsForStudent($studentId);
$history = siemsGetStudentEnrollmentHistory($studentId);
$currentApp = $history[0] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enrollment'])) {
    $academicPeriodId = (int) ($_POST['academic_period_id'] ?? 0);
    $selectedSubjects = array_map('intval', $_POST['subject_ids'] ?? []);

    try {
        if (!$academicPeriodId || empty($selectedSubjects)) {
            throw new RuntimeException('Please choose a term and at least one subject.');
        }

        $existingActive = siemsFetchOne("
            SELECT id
            FROM enrollment_applications
            WHERE student_id = ?
              AND academic_period_id = ?
              AND status IN ('Pending', 'Submitted', 'For Review', 'Validated', 'Approved', 'Paid', 'Enrolled')
            LIMIT 1
        ", [$studentId, $academicPeriodId]);

        if ($existingActive) {
            throw new RuntimeException('You already have an active enrollment application for the selected term.');
        }

        $studentRow = siemsFetchOne("SELECT program, year_level FROM users WHERE student_id = ? LIMIT 1", [$studentId]);
        $sectionCode = null;
        if ($studentRow && siemsTableExists('sections')) {
            $section = siemsFetchOne("
                SELECT section_code
                FROM sections
                WHERE program_code = ?
                  AND year_level = ?
                  AND status = 'Open'
                ORDER BY section_code ASC
                LIMIT 1
            ", [$studentRow['program'], $studentRow['year_level']]);
            $sectionCode = $section['section_code'] ?? null;
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO enrollment_applications
                (student_id, academic_period_id, status, remarks)
            VALUES
                (?, ?, 'Submitted', ?)
        ");
        $stmt->execute([
            $studentId,
            $academicPeriodId,
            trim($_POST['remarks'] ?? 'Submitted through student portal') ?: null,
        ]);
        $applicationId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare("
            INSERT INTO enrollment_application_items
                (application_id, subject_id, section_code, status)
            VALUES
                (?, ?, ?, 'Selected')
        ");
        foreach ($selectedSubjects as $subjectId) {
            $itemStmt->execute([$applicationId, $subjectId, $sectionCode]);
        }

        if (siemsTableExists('enrollment_validations')) {
            $stmt = $pdo->prepare("
                INSERT INTO enrollment_validations
                    (application_id, validation_status, validation_notes)
                VALUES
                    (?, 'Pending', ?)
            ");
            $stmt->execute([$applicationId, 'Awaiting registrar/admin review']);
        }

        $pdo->prepare("UPDATE users SET enrollment_status = 'Submitted' WHERE student_id = ?")->execute([$studentId]);

        siemsLogSubsystemEvent(
            'Enrollment & Registration',
            'Submitted enrollment application',
            $studentId,
            null,
            'Application #' . $applicationId . ' with ' . count($selectedSubjects) . ' subject(s)'
        );

        $pdo->commit();
        $_SESSION['message'] = 'Enrollment application submitted successfully.';
        $_SESSION['msg_type'] = 'success';
        header('Location: enrollment_registration.php');
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $_SESSION['message'] = $e->getMessage();
        $_SESSION['msg_type'] = 'danger';
        header('Location: enrollment_registration.php');
        exit;
    }
}

$currentItems = [];
if ($currentApp && siemsTableExists('enrollment_application_items')) {
    $currentItems = siemsFetchAll("
        SELECT eai.*, s.code, s.description, s.units
        FROM enrollment_application_items eai
        INNER JOIN subjects s ON s.id = eai.subject_id
        WHERE eai.application_id = ?
        ORDER BY s.code ASC
    ", [$currentApp['id']]);
}

$officialEnrollments = [];
if (siemsTableExists('enrollments') && siemsTableExists('subjects')) {
    $officialEnrollments = siemsFetchAll("
        SELECT e.*, s.code, s.description, s.units
        FROM enrollments e
        INNER JOIN subjects s ON s.id = e.subject_id
        WHERE e.student_id = ?
        ORDER BY e.academic_year DESC, e.semester DESC, s.code ASC
    ", [$studentId]);
}
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-journal-check text-success me-2"></i>Enrollment & Registration</h3>
        <div class="row-text">Submit your subject selection and track validation progress in one place.</div>
    </div>
    <a href="siems_portal.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left me-1"></i> SIEMS Portal
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="group-label-grey">CURRENT STATUS</div>
            <div class="val-total-amount fs-4 mt-3"><?php echo htmlspecialchars($currentApp['status'] ?? ($_SESSION['enrollment_status'] ?? 'Pending')); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="group-label-grey">OPEN TERMS</div>
            <div class="val-total-amount fs-2 text-success mt-2"><?php echo count($periods); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="group-label-grey">AVAILABLE SUBJECTS</div>
            <div class="val-total-amount fs-2 mt-2" style="color: #0ea5e9;"><?php echo count($availableSubjects); ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4">
                <span class="section-title">NEW ENROLLMENT APPLICATION</span>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($periods) && !empty($availableSubjects)): ?>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label row-val">Academic Period</label>
                                <select name="academic_period_id" class="form-select row-text" required style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                    <option value="">Select term</option>
                                    <?php foreach ($periods as $period): ?>
                                        <option value="<?php echo (int) $period['id']; ?>">
                                            <?php echo htmlspecialchars($period['academic_year'] . ' / ' . $period['semester'] . ' (' . $period['period_status'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label row-val">Student ID</label>
                                <input type="text" class="form-control row-text" value="<?php echo htmlspecialchars($studentId); ?>" disabled style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label row-val">Select Subjects</label>
                                <div class="border rounded-3 p-3" style="border-color: #e2e8f0 !important; max-height: 360px; overflow-y: auto;">
                                    <?php foreach ($availableSubjects as $subject): ?>
                                        <label class="d-flex align-items-start gap-3 p-3 mb-2 rounded-3" style="background-color: #f8fafc; cursor: pointer;">
                                            <input type="checkbox" name="subject_ids[]" value="<?php echo (int) $subject['id']; ?>" class="form-check-input mt-1">
                                            <div>
                                                <div class="row-val"><?php echo htmlspecialchars($subject['code']); ?> - <?php echo htmlspecialchars($subject['description']); ?></div>
                                                <div class="row-text"><?php echo htmlspecialchars((string) $subject['units']); ?> units | <?php echo htmlspecialchars($subject['semester'] ?? 'N/A'); ?></div>
                                                <?php if (!empty($subject['prerequisite_codes'])): ?>
                                                    <div class="group-label-grey mt-1">Prerequisite: <?php echo htmlspecialchars($subject['prerequisite_codes']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label row-val">Remarks</label>
                                <textarea name="remarks" rows="3" class="form-control row-text" placeholder="Optional note for the registrar/admin reviewer" style="border: 2px solid #e2e8f0; border-radius: 8px;"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="submit_enrollment" class="btn btn-success fw-bold px-4" style="border-radius: 8px;">
                                    <i class="bi bi-send-check me-1"></i> Submit Enrollment Application
                                </button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-muted">Open periods or subject offerings are not available yet in the active database.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4">
                <span class="section-title">CURRENT APPLICATION DETAILS</span>
            </div>
            <div class="card-body p-4">
                <?php if ($currentApp): ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="group-label-grey">TERM</div>
                            <div class="row-val"><?php echo htmlspecialchars($currentApp['academic_year'] . ' / ' . $currentApp['semester']); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="group-label-grey">STATUS</div>
                            <div class="row-val"><?php echo htmlspecialchars($currentApp['status']); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="group-label-grey">VALIDATION</div>
                            <div class="row-val"><?php echo htmlspecialchars($currentApp['validation_status'] ?? 'Pending'); ?></div>
                        </div>
                    </div>
                    <div class="row-text mb-3"><?php echo htmlspecialchars($currentApp['validation_notes'] ?? $currentApp['remarks'] ?? 'No remarks yet.'); ?></div>
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
                                <?php foreach ($currentItems as $item): ?>
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
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-muted">No enrollment application submitted yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4">
                <span class="section-title">APPLICATION HISTORY</span>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($history)): ?>
                    <?php foreach ($history as $application): ?>
                        <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="row-val"><?php echo htmlspecialchars($application['academic_year'] . ' / ' . $application['semester']); ?></div>
                                <div class="group-label-grey"><?php echo date('M j, Y', strtotime($application['application_date'])); ?></div>
                            </div>
                            <div class="row-text mt-2"><?php echo htmlspecialchars($application['status']); ?> | Validation: <?php echo htmlspecialchars($application['validation_status'] ?? 'Pending'); ?></div>
                            <div class="group-label-grey mt-2"><?php echo htmlspecialchars($application['validation_notes'] ?? $application['remarks'] ?? 'No remarks'); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted">No enrollment history yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4">
                <span class="section-title">OFFICIAL ENROLLMENTS</span>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($officialEnrollments)): ?>
                    <?php foreach ($officialEnrollments as $enrollment): ?>
                        <div class="p-3 rounded-3 mb-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="row-val"><?php echo htmlspecialchars($enrollment['code']); ?> - <?php echo htmlspecialchars($enrollment['description']); ?></div>
                            <div class="row-text"><?php echo htmlspecialchars($enrollment['academic_year'] . ' / ' . $enrollment['semester']); ?> | Section <?php echo htmlspecialchars($enrollment['section_code'] ?? 'TBA'); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted">No official enrollment records posted yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
