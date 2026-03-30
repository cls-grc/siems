<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';

requireSubsystemAccess('human_resources');

$page_title = 'Human Resource Management';
$staffUsers = siemsFetchAll("SELECT id, student_id, full_name, employee_id, role, email FROM users WHERE employee_id IS NOT NULL ORDER BY full_name ASC");

$module = $_GET['module'] ?? '';
$allowedModules = ['preemployment', 'recruitment', 'onboarding', 'performance', 'clearance', 'audit'];
if (!in_array($module, $allowedModules, true)) {
    $module = '';
}

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            if (isset($_POST['save_job_posting'])) {
                $pdo->prepare("
                    INSERT INTO job_postings (job_title, department_name, employment_type, posting_status)
                    VALUES (?, ?, ?, ?)
                ")->execute([trim($_POST['job_title']), trim($_POST['department_name']), trim($_POST['employment_type']), trim($_POST['posting_status'])]);
                siemsLogSubsystemEvent('Human Resources', 'Saved job posting', null, null, trim($_POST['job_title']));
                $_SESSION['message'] = 'Job posting saved successfully.';
                $_SESSION['msg_type'] = 'success';
                header('Location: human_resources.php?module=preemployment');
                exit;
            } elseif (isset($_POST['save_applicant'])) {
                $pdo->prepare("
                    INSERT INTO applicants (
                        job_posting_id, applicant_name, email, phone, address, highest_education,
                        specialization, teaching_experience, portfolio_link, cover_letter, resume_file,
                        supporting_documents, requirements_notes, application_status
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    (int) $_POST['job_posting_id'],
                    trim($_POST['applicant_name']),
                    trim($_POST['email']) ?: null,
                    trim($_POST['phone']) ?: null,
                    trim($_POST['address']) ?: null,
                    trim($_POST['highest_education']) ?: null,
                    trim($_POST['specialization']) ?: null,
                    trim($_POST['teaching_experience']) ?: null,
                    trim($_POST['portfolio_link']) ?: null,
                    trim($_POST['cover_letter']) ?: null,
                    trim($_POST['resume_file']) ?: null,
                    trim($_POST['supporting_documents']) ?: null,
                    trim($_POST['requirements_notes']) ?: null,
                    trim($_POST['application_status'])
                ]);
                siemsLogSubsystemEvent('Human Resources', 'Saved applicant profile', null, null, trim($_POST['applicant_name']));
                $_SESSION['message'] = 'Applicant saved successfully.';
                $_SESSION['msg_type'] = 'success';
                header('Location: human_resources.php?module=recruitment');
                exit;
            } elseif (isset($_POST['update_status'])) {
                $applicant_id = (int)$_POST['applicant_id'];
                $new_status = trim($_POST['new_status']);
                if ($applicant_id && $new_status && siemsTableExists('applicants')) {
                    $stmt = $pdo->prepare("UPDATE applicants SET application_status = ? WHERE id = ?");
                    $stmt->execute([$new_status, $applicant_id]);
                    $applicant_name = siemsFetchOne("SELECT applicant_name FROM applicants WHERE id = ?", [$applicant_id])['applicant_name'] ?? 'Unknown';
                    siemsLogSubsystemEvent('Human Resources', 'Applicant status updated', null, null, "$applicant_name -> $new_status");
                    $_SESSION['message'] = 'Applicant status updated successfully.';
                    $_SESSION['msg_type'] = 'success';
                }
                header('Location: human_resources.php?module=recruitment');
                exit;
            } elseif (isset($_POST['save_employee_record'])) {
            $pdo->prepare("
                INSERT INTO applicants (
                    job_posting_id, applicant_name, email, phone, address, highest_education,
                    specialization, teaching_experience, portfolio_link, cover_letter, resume_file,
                    supporting_documents, requirements_notes, application_status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ")->execute([
                (int) $_POST['job_posting_id'],
                trim($_POST['applicant_name']),
                trim($_POST['email']) ?: null,
                trim($_POST['phone']) ?: null,
                trim($_POST['address']) ?: null,
                trim($_POST['highest_education']) ?: null,
                trim($_POST['specialization']) ?: null,
                trim($_POST['teaching_experience']) ?: null,
                trim($_POST['portfolio_link']) ?: null,
                trim($_POST['cover_letter']) ?: null,
                trim($_POST['resume_file']) ?: null,
                trim($_POST['supporting_documents']) ?: null,
                trim($_POST['requirements_notes']) ?: null,
                trim($_POST['application_status'])
            ]);
            siemsLogSubsystemEvent('Human Resources', 'Saved applicant profile', null, null, trim($_POST['applicant_name']));
            $_SESSION['message'] = 'Applicant saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: human_resources.php?module=recruitment');
            exit;
        } elseif (isset($_POST['save_employee_record'])) {
            $pdo->prepare("
                INSERT INTO employee_records (user_id, department_name, position_title, employment_status, hired_at)
                VALUES (?, ?, ?, ?, ?)
            ")->execute([(int) $_POST['user_id'], trim($_POST['emp_department_name']), trim($_POST['position_title']), trim($_POST['employment_status']), $_POST['hired_at'] ?: null]);
            siemsLogSubsystemEvent('Human Resources', 'Saved employee record', null, null, trim($_POST['position_title']));
            $_SESSION['message'] = 'Employee record saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: human_resources.php?module=onboarding');
            exit;
        } elseif (isset($_POST['save_medical_clearance'])) {
            $staffUser = siemsFetchOne("SELECT student_id, full_name FROM users WHERE id = ? LIMIT 1", [(int) $_POST['medical_user_id']]);
            if ($staffUser && siemsTableExists('medical_clearances')) {
                $pdo->prepare("
                    INSERT INTO medical_clearances (student_id, status, remarks, issued_at)
                    VALUES (?, ?, ?, ?)
                ")->execute([
                    $staffUser['student_id'],
                    trim($_POST['medical_status']),
                    trim($_POST['medical_remarks']) ?: null,
                    $_POST['medical_issued_at'] ? date('Y-m-d H:i:s', strtotime($_POST['medical_issued_at'])) : date('Y-m-d H:i:s'),
                ]);
                siemsLogSubsystemEvent('Human Resources', 'Saved pre-employment medical clearance', $staffUser['student_id'], null, trim($_POST['medical_status']));
                $_SESSION['message'] = 'Medical clearance saved successfully.';
                $_SESSION['msg_type'] = 'success';
            }
            header('Location: human_resources.php?module=preemployment');
            exit;
        } elseif (isset($_POST['save_performance'])) {
            $pdo->prepare("
                INSERT INTO employee_performance (employee_record_id, review_period, rating, review_notes)
                VALUES (?, ?, ?, ?)
            ")->execute([(int) $_POST['employee_record_id'], trim($_POST['review_period']), (float) $_POST['rating'], trim($_POST['review_notes']) ?: null]);
            siemsLogSubsystemEvent('Human Resources', 'Saved employee performance review', null, null, trim($_POST['review_period']));
            $_SESSION['message'] = 'Performance review saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: human_resources.php?module=performance');
            exit;
        } elseif (isset($_POST['save_clearance'])) {
            $pdo->prepare("
                INSERT INTO employee_clearances (employee_record_id, clearance_status, exit_reason)
                VALUES (?, ?, ?)
            ")->execute([(int) $_POST['clearance_employee_record_id'], trim($_POST['clearance_status']), trim($_POST['exit_reason']) ?: null]);
            siemsLogSubsystemEvent('Human Resources', 'Saved employee clearance', null, null, trim($_POST['clearance_status']));
            $_SESSION['message'] = 'Employee clearance saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: human_resources.php?module=clearance');
            exit;
        }
    } catch (Throwable $e) {
        $_SESSION['message'] = 'Unable to save HR data. Please check for duplicates or invalid values.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: human_resources.php' . ($module !== '' ? '?module=' . urlencode($module) : ''));
        exit;
    }
}

$overview = siemsGetHumanResourcesOverview();
$jobs = $overview['jobs'];
$applicants = $overview['applicants'];
$employees = $overview['employees'];
$performance = $overview['performance'];
$clearances = $overview['clearances'];
$medicalClearances = siemsTableExists('medical_clearances') ? siemsFetchAll("
    SELECT mc.*, u.full_name, u.employee_id, u.role
    FROM medical_clearances mc
    INNER JOIN users u ON u.student_id = mc.student_id
    WHERE u.employee_id IS NOT NULL
    ORDER BY mc.issued_at DESC, mc.id DESC
") : [];
$recentAuditLogs = siemsTableExists('audit_log') ? siemsFetchAll("
    SELECT al.*, u.full_name AS user_name
    FROM audit_log al
    LEFT JOIN users u ON u.id = al.user_id
    WHERE " . (siemsColumnExists('audit_log', 'subsystem') ? "al.subsystem = 'Human Resources'" : "al.action LIKE '%employee%' OR al.action LIKE '%applicant%' OR al.action LIKE '%job posting%'") . "
    ORDER BY al.created_at DESC, al.id DESC
    LIMIT 20
") : [];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-people text-success me-2"></i>Human Resource Management</h3>
        <div class="row-text">Subsystem 8 for pre-employment, recruitment, onboarding, performance management, medical clearance, post-employment processing, and audit visibility.</div>
    </div>
    <a href="siems_hub.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;"><i class="bi bi-arrow-left me-1"></i> SIEMS Hub</a>
</div>

<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4">
        <span class="section-title">QUICK ACTION BUTTONS</span>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-4 col-lg-2">
                <a href="human_resources.php?module=preemployment" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-briefcase d-block fs-4 mb-2"></i>Pre-Employment
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="human_resources.php?module=recruitment" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-person-lines-fill d-block fs-4 mb-2"></i>Recruitment
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="human_resources.php?module=onboarding" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-person-badge d-block fs-4 mb-2"></i>Onboarding
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="human_resources.php?module=performance" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-graph-up-arrow d-block fs-4 mb-2"></i>Performance
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="human_resources.php?module=clearance" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-file-earmark-check d-block fs-4 mb-2"></i>Post-Employment
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="human_resources.php?module=audit" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-clock-history d-block fs-4 mb-2"></i>Audit Logs
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($module === ''): ?>
<div class="card-outline bg-white p-5 text-center">
    <i class="bi bi-grid-1x2 fs-1 text-success d-block mb-3"></i>
    <h4 class="dashboard-title mb-2">Subsystem 8 Module Launcher</h4>
    <div class="row-text">Choose a quick action button above to open one module at a time.</div>
</div>
<?php elseif ($module === 'preemployment'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">PRE-EMPLOYMENT MANAGEMENT</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><input type="text" name="job_title" class="form-control row-text" placeholder="Job title" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="text" name="department_name" class="form-control row-text" placeholder="Department" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="text" name="employment_type" class="form-control row-text" placeholder="Full-time/Part-time" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><select name="posting_status" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Open</option><option>Closed</option><option>Filled</option></select></div>
                    <div class="col-12"><button type="submit" name="save_job_posting" class="btn btn-success fw-bold" style="border-radius:8px;">Save Posting</button></div>
                </form>
            </div>
        </div>
        <div class="card-outline bg-white mt-4">
            <div class="balance-header py-3 px-4"><span class="section-title">PRE-EMPLOYMENT MEDICAL CLEARANCE</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="medical_user_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select staff account</option><?php foreach ($staffUsers as $user): ?><option value="<?php echo (int) $user['id']; ?>"><?php echo htmlspecialchars($user['full_name'] . ' (' . $user['employee_id'] . ')'); ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><select name="medical_status" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Pending</option><option>Cleared</option><option>Not Cleared</option></select></div>
                    <div class="col-md-6"><input type="datetime-local" name="medical_issued_at" class="form-control row-text" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><textarea name="medical_remarks" rows="3" class="form-control row-text" placeholder="Clinic remarks / fit-to-work notes" style="border:2px solid #e2e8f0;border-radius:8px;"></textarea></div>
                    <div class="col-12"><button type="submit" name="save_medical_clearance" class="btn btn-outline-success fw-bold" style="border-radius:8px;">Save Medical Clearance</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">JOB POSTINGS</div><div class="val-total-amount fs-2 mt-2 text-success"><?php echo count($jobs); ?></div></div></div>
            <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">OPEN POSITIONS</div><div class="val-total-amount fs-2 mt-2" style="color:#0ea5e9;"><?php echo count(array_filter($jobs, static fn($job) => ($job['posting_status'] ?? '') === 'Open')); ?></div></div></div>
            <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">MEDICALLY CLEARED</div><div class="val-total-amount fs-2 mt-2" style="color:#f59e0b;"><?php echo count(array_filter($medicalClearances, static fn($clearance) => ($clearance['status'] ?? '') === 'Cleared')); ?></div></div></div>
        </div>
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">JOB POSTING LIST</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">POSITION</th><th class="group-label-grey py-3">DEPARTMENT</th><th class="group-label-grey py-3">TYPE</th><th class="group-label-grey py-3">STATUS</th></tr></thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($job['job_title']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($job['department_name']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($job['employment_type']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($job['posting_status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($jobs)): ?><tr><td colspan="4" class="text-center text-muted py-5">No job postings found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-outline bg-white mt-4">
            <div class="balance-header py-3 px-4"><span class="section-title">MEDICAL CLEARANCE CHECKLIST</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">STAFF</th><th class="group-label-grey py-3">EMPLOYEE ID</th><th class="group-label-grey py-3">STATUS</th><th class="group-label-grey py-3">ISSUED</th><th class="group-label-grey py-3">REMARKS</th></tr></thead>
                    <tbody>
                        <?php foreach ($medicalClearances as $clearance): ?>
                            <tr>
                                <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($clearance['full_name']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($clearance['employee_id'] ?? '-'); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($clearance['status']); ?></td>
                                <td class="py-3 row-text"><?php echo !empty($clearance['issued_at']) ? date('M j, Y g:i A', strtotime($clearance['issued_at'])) : '-'; ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($clearance['remarks'] ?? 'No remarks'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($medicalClearances)): ?><tr><td colspan="5" class="text-center text-muted py-5">No pre-employment medical clearance records yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'recruitment'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
<div class="balance-header py-3 px-4"><span class="section-title">NEW APPLICANT</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="job_posting_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select job posting</option><?php foreach ($jobs as $job): ?><option value="<?php echo (int) $job['id']; ?>"><?php echo htmlspecialchars($job['job_title'] . ' - ' . $job['department_name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><input type="text" name="applicant_name" class="form-control row-text" placeholder="Applicant name" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="email" name="email" class="form-control row-text" placeholder="Email" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="text" name="phone" class="form-control row-text" placeholder="Mobile number" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="text" name="highest_education" class="form-control row-text" placeholder="Highest education" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="text" name="address" class="form-control row-text" placeholder="Address" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="text" name="specialization" class="form-control row-text" placeholder="Specialization" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="text" name="teaching_experience" class="form-control row-text" placeholder="Teaching/work experience" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="url" name="portfolio_link" class="form-control row-text" placeholder="Portfolio or LinkedIn link" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="text" name="resume_file" class="form-control row-text" placeholder="Resume file path or note" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="text" name="supporting_documents" class="form-control row-text" placeholder="Supporting documents path or note" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><textarea name="cover_letter" rows="3" class="form-control row-text" placeholder="Cover letter" style="border:2px solid #e2e8f0;border-radius:8px;"></textarea></div>
                    <div class="col-12"><textarea name="requirements_notes" rows="2" class="form-control row-text" placeholder="Requirement notes" style="border:2px solid #e2e8f0;border-radius:8px;"></textarea></div>
                    <div class="col-12"><select name="application_status" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Screening</option><option>Interview</option><option>For Offer</option><option>Hired</option><option>Rejected</option></select></div>
                    <div class="col-12"><button type="submit" name="save_applicant" class="btn btn-success fw-bold" style="border-radius:8px;">Save Applicant</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">APPLICANT PIPELINE</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label row-val mb-2">Applicant ID</label>
                        <select name="applicant_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select applicant</option>
                            <?php foreach ($applicants as $applicant): ?>
                                <option value="<?php echo (int) $applicant['id']; ?>"><?php echo htmlspecialchars($applicant['applicant_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label row-val mb-2">New Status</label>
                        <select name="new_status" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select status</option>
                            <option>Screening</option>
                            <option>Interview</option>
                            <option>For Offer</option>
                            <option>Hired</option>
                            <option>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" name="update_status" class="btn btn-primary fw-bold" style="border-radius:8px;">Update Status</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">APPLICANT</th><th class="group-label-grey py-3">OPENING</th><th class="group-label-grey py-3">QUALIFICATIONS</th><th class="group-label-grey py-3">DOCUMENTS</th><th class="group-label-grey py-3">STATUS</th></tr></thead>
                    <tbody>
                        <?php foreach ($applicants as $applicant): ?>
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="row-val"><?php echo htmlspecialchars($applicant['applicant_name']); ?></div>
                                    <div class="row-text"><?php echo htmlspecialchars(($applicant['email'] ?? 'No email') . ' | ' . ($applicant['phone'] ?? 'No contact')); ?></div>
                                    <div class="group-label-grey mt-1"><?php echo htmlspecialchars($applicant['address'] ?? 'No address'); ?></div>
                                </td>
                                <td class="py-3 row-text"><div class="row-val"><?php echo htmlspecialchars($applicant['job_title']); ?></div><div><?php echo htmlspecialchars($applicant['department_name']); ?></div></td>
                                <td class="py-3 row-text"><div><?php echo htmlspecialchars($applicant['highest_education'] ?? 'Education not provided'); ?></div><div><?php echo htmlspecialchars($applicant['specialization'] ?? 'No specialization'); ?></div><div class="group-label-grey mt-1"><?php echo htmlspecialchars($applicant['teaching_experience'] ?? 'No experience note'); ?></div></td>
                                <td class="py-3 row-text"><div><?php echo htmlspecialchars($applicant['resume_file'] ?? 'No resume path'); ?></div><div class="group-label-grey mt-1"><?php echo htmlspecialchars($applicant['supporting_documents'] ?? 'No supporting document'); ?></div><div class="group-label-grey mt-1"><?php echo htmlspecialchars($applicant['requirements_notes'] ?? 'No requirement notes'); ?></div></td>
                                <td class="py-3 row-text"><div class="row-val"><?php echo htmlspecialchars($applicant['application_status']); ?></div><div class="group-label-grey mt-1"><?php echo htmlspecialchars($applicant['submitted_at'] ?? ''); ?></div></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($applicants)): ?><tr><td colspan="5" class="text-center text-muted py-5">No applicants found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'onboarding'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">EMPLOYMENT RECORDS & ONBOARDING</span></div>
            <div class="card-body p-4">
                <div class="alert alert-light border mb-4" style="border-color:#d1d5db !important;">
                    <strong>Hiring reminder:</strong> Pre-employment medical clearance should be completed before a staff member proceeds to onboarding and final employee record creation.
                </div>
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="user_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select staff account</option><?php foreach ($staffUsers as $user): ?><option value="<?php echo (int) $user['id']; ?>"><?php echo htmlspecialchars($user['full_name'] . ' (' . $user['employee_id'] . ')'); ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><input type="text" name="emp_department_name" class="form-control row-text" placeholder="Department" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="text" name="position_title" class="form-control row-text" placeholder="Position" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="date" name="hired_at" class="form-control row-text" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><select name="employment_status" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Active</option><option>Probationary</option><option>Regular</option><option>Resigned</option><option>Retired</option><option>Terminated</option></select></div>
                    <div class="col-12"><button type="submit" name="save_employee_record" class="btn btn-success fw-bold" style="border-radius:8px;">Save Employee Record</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">EMPLOYEE RECORDS</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">EMPLOYEE</th><th class="group-label-grey py-3">DEPARTMENT</th><th class="group-label-grey py-3">POSITION</th><th class="group-label-grey py-3">STATUS</th></tr></thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($employee['full_name']); ?></div><div class="row-text"><?php echo htmlspecialchars(($employee['employee_id'] ?? 'N/A') . ' | ' . ($employee['email'] ?? 'No email')); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($employee['department_name']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($employee['position_title']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($employee['employment_status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($employees)): ?><tr><td colspan="4" class="text-center text-muted py-5">No employee records found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'performance'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">EMPLOYEE PERFORMANCE & SERVICE</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="employee_record_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select employee</option><?php foreach ($employees as $employee): ?><option value="<?php echo (int) $employee['id']; ?>"><?php echo htmlspecialchars($employee['full_name'] . ' - ' . $employee['position_title']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><input type="text" name="review_period" class="form-control row-text" placeholder="2026 Q3" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="number" step="0.01" min="1" max="5" name="rating" class="form-control row-text" placeholder="Rating" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><textarea name="review_notes" rows="3" class="form-control row-text" placeholder="Review notes" style="border:2px solid #e2e8f0;border-radius:8px;"></textarea></div>
                    <div class="col-12"><button type="submit" name="save_performance" class="btn btn-success fw-bold" style="border-radius:8px;">Save Review</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">PERFORMANCE REVIEW LOG</span></div>
            <div class="card-body p-4">
                <?php foreach ($performance as $review): ?>
                    <div class="border rounded-3 p-3 mb-3" style="border-color:#e2e8f0 !important;">
                        <div class="row-val"><?php echo htmlspecialchars($review['full_name']); ?> - <?php echo htmlspecialchars($review['review_period']); ?></div>
                        <div class="row-text mt-2">Rating: <?php echo htmlspecialchars((string) $review['rating']); ?></div>
                        <div class="group-label-grey mt-2"><?php echo htmlspecialchars($review['review_notes'] ?? 'No notes'); ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($performance)): ?><div class="text-muted">No performance reviews yet.</div><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'clearance'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">POST-EMPLOYMENT & CLEARANCE</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="clearance_employee_record_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select employee</option><?php foreach ($employees as $employee): ?><option value="<?php echo (int) $employee['id']; ?>"><?php echo htmlspecialchars($employee['full_name'] . ' - ' . $employee['position_title']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><select name="clearance_status" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Pending</option><option>Processing</option><option>Cleared</option></select></div>
                    <div class="col-md-6"><input type="text" name="exit_reason" class="form-control row-text" placeholder="Exit reason (optional)" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><button type="submit" name="save_clearance" class="btn btn-success fw-bold" style="border-radius:8px;">Save Clearance</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">CLEARANCE PROCESSING LOG</span></div>
            <div class="card-body p-4">
                <?php foreach ($clearances as $clearance): ?>
                    <div class="border rounded-3 p-3 mb-3" style="border-color:#e2e8f0 !important;">
                        <div class="row-val"><?php echo htmlspecialchars($clearance['full_name']); ?></div>
                        <div class="row-text mt-2"><?php echo htmlspecialchars($clearance['clearance_status']); ?></div>
                        <div class="group-label-grey mt-2"><?php echo htmlspecialchars($clearance['exit_reason'] ?? 'No exit reason'); ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($clearances)): ?><div class="text-muted">No employee clearance records yet.</div><?php endif; ?>
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
                <?php if (empty($recentAuditLogs)): ?><tr><td colspan="5" class="text-center text-muted py-5">No HR audit trail records found yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
