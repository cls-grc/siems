<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';

requireSubsystemAccess('curriculum_course');

$page_title = 'Curriculum & Course';
$programs = siemsGetPrograms();

$module = $_GET['module'] ?? '';
$allowedModules = ['setup', 'catalog', 'dependencies', 'schedule', 'revisions', 'audit'];
if (!in_array($module, $allowedModules, true)) {
    $module = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['save_subject'])) {
            $stmt = $pdo->prepare("
                INSERT INTO subjects (code, description, units, program, year_level, semester, category, active)
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                trim($_POST['code']),
                trim($_POST['description']),
                (float) $_POST['units'],
                trim($_POST['program']),
                (int) $_POST['year_level'],
                trim($_POST['semester']),
                trim($_POST['category']),
            ]);
            siemsLogSubsystemEvent('Curriculum & Course', 'Created subject', null, null, trim($_POST['code']));
            $_SESSION['message'] = 'Subject saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: curriculum_course.php?module=catalog');
            exit;
        } elseif (isset($_POST['save_curriculum'])) {
            $stmt = $pdo->prepare("
                INSERT INTO curricula (program_code, curriculum_name, effective_academic_year, status, created_by)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                trim($_POST['program_code']),
                trim($_POST['curriculum_name']),
                trim($_POST['effective_academic_year']),
                trim($_POST['status']),
                $_SESSION['user_id'],
            ]);
            siemsLogSubsystemEvent('Curriculum & Course', 'Created curriculum', null, null, trim($_POST['curriculum_name']));
            $_SESSION['message'] = 'Curriculum saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: curriculum_course.php?module=setup');
            exit;
        } elseif (isset($_POST['map_curriculum_subject'])) {
            $stmt = $pdo->prepare("
                INSERT INTO curriculum_courses (curriculum_id, subject_id, year_level, semester)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                (int) $_POST['curriculum_id'],
                (int) $_POST['subject_id'],
                (int) $_POST['map_year_level'],
                trim($_POST['map_semester']),
            ]);
            siemsLogSubsystemEvent('Curriculum & Course', 'Mapped subject to curriculum', null, null, 'Curriculum #' . (int) $_POST['curriculum_id']);
            $_SESSION['message'] = 'Subject mapped to curriculum successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: curriculum_course.php?module=setup');
            exit;
        } elseif (isset($_POST['save_dependency'])) {
            $stmt = $pdo->prepare("
                INSERT INTO course_dependencies (subject_id, dependency_subject_id, dependency_type)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                (int) $_POST['subject_id'],
                (int) $_POST['dependency_subject_id'],
                trim($_POST['dependency_type']),
            ]);
            siemsLogSubsystemEvent('Curriculum & Course', 'Saved course dependency', null, null, trim($_POST['dependency_type']));
            $_SESSION['message'] = 'Course dependency saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: curriculum_course.php?module=dependencies');
            exit;
        }
    } catch (Throwable $e) {
        $_SESSION['message'] = 'Unable to save curriculum data. It may already exist or contain invalid values.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: curriculum_course.php' . ($module !== '' ? '?module=' . urlencode($module) : ''));
        exit;
    }
}

$overview = siemsGetAdminCurriculumOverview();
$curricula = $overview['curriculum_rows'];
$subjects = $overview['subject_rows'];
$scheduleOverview = siemsGetAdminScheduleOverview();
$scheduleRows = $scheduleOverview['schedules'];
$sections = $scheduleOverview['sections'];
$rooms = $scheduleOverview['rooms'];

$curriculumDetails = [];
if (!empty($curricula)) {
    $curriculumDetails = siemsFetchAll("
        SELECT c.id, c.curriculum_name, c.program_code, c.effective_academic_year, c.status,
               cc.year_level, cc.semester, s.code, s.description, s.units
        FROM curriculum_courses cc
        INNER JOIN curricula c ON c.id = cc.curriculum_id
        INNER JOIN subjects s ON s.id = cc.subject_id
        ORDER BY c.curriculum_name ASC, cc.year_level ASC, cc.semester ASC, s.code ASC
    ");
}

$dependencyRows = siemsTableExists('course_dependencies') ? siemsFetchAll("
    SELECT cd.*, target.code AS target_code, target.description AS target_description,
           dep.code AS dependency_code, dep.description AS dependency_description
    FROM course_dependencies cd
    INNER JOIN subjects target ON target.id = cd.subject_id
    INNER JOIN subjects dep ON dep.id = cd.dependency_subject_id
    ORDER BY target.code ASC, cd.dependency_type ASC, dep.code ASC
") : [];

$recentAuditLogs = siemsTableExists('audit_log') ? siemsFetchAll("
    SELECT al.*, u.full_name AS user_name
    FROM audit_log al
    LEFT JOIN users u ON u.id = al.user_id
    WHERE " . (siemsColumnExists('audit_log', 'subsystem') ? "al.subsystem = 'Curriculum & Course'" : "al.action LIKE '%curriculum%' OR al.action LIKE '%subject%' OR al.action LIKE '%dependency%'") . "
    ORDER BY al.created_at DESC, al.id DESC
    LIMIT 20
") : [];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-diagram-3 text-success me-2"></i>Curriculum & Course</h3>
        <div class="row-text">Subsystem 3 for curriculum setup, subject catalog, dependencies, scheduling references, revisions, and audit visibility.</div>
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
                <a href="curriculum_course.php?module=setup" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-diagram-2 d-block fs-4 mb-2"></i>Curriculum Setup
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="curriculum_course.php?module=catalog" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-journal-bookmark d-block fs-4 mb-2"></i>Subject Catalog
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="curriculum_course.php?module=dependencies" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-link-45deg d-block fs-4 mb-2"></i>Prereq / Coreq
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="curriculum_course.php?module=schedule" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-calendar3-week d-block fs-4 mb-2"></i>Scheduling
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="curriculum_course.php?module=revisions" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-arrow-repeat d-block fs-4 mb-2"></i>Revisions
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="curriculum_course.php?module=audit" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-clock-history d-block fs-4 mb-2"></i>Audit Logs
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($module === ''): ?>
<div class="card-outline bg-white p-5 text-center">
    <i class="bi bi-grid-1x2 fs-1 text-success d-block mb-3"></i>
    <h4 class="dashboard-title mb-2">Subsystem 3 Module Launcher</h4>
    <div class="row-text">Choose a quick action button above to open one module at a time.</div>
</div>
<?php elseif ($module === 'setup'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">CURRICULUM SETUP</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12">
                        <select name="program_code" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select program</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?php echo htmlspecialchars($program['program_code']); ?>"><?php echo htmlspecialchars($program['program_code'] . ' - ' . $program['program_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12"><input type="text" name="curriculum_name" class="form-control row-text" placeholder="Curriculum Name" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="text" name="effective_academic_year" class="form-control row-text" placeholder="2026-2027" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><select name="status" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Draft</option><option>Active</option><option>Archived</option></select></div>
                    <div class="col-12"><button type="submit" name="save_curriculum" class="btn btn-success fw-bold" style="border-radius:8px;">Save Curriculum</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">MAP SUBJECT TO CURRICULUM</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-md-5">
                        <select name="curriculum_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select curriculum</option>
                            <?php foreach ($curricula as $curriculum): ?>
                                <option value="<?php echo (int) $curriculum['id']; ?>"><?php echo htmlspecialchars($curriculum['curriculum_name'] . ' (' . $curriculum['program_code'] . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="subject_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo (int) $subject['id']; ?>"><?php echo htmlspecialchars($subject['code'] . ' - ' . $subject['description']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1"><input type="number" min="1" max="6" name="map_year_level" class="form-control row-text" placeholder="Yr" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-2"><select name="map_semester" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>1st</option><option>2nd</option><option>Summer</option></select></div>
                    <div class="col-12"><button type="submit" name="map_curriculum_subject" class="btn btn-outline-success fw-bold" style="border-radius:8px;">Add to Curriculum</button></div>
                </form>
            </div>
        </div>
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">CURRICULUM SETUP MAP</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;">
                        <tr><th class="group-label-grey py-3 px-4">CURRICULUM</th><th class="group-label-grey py-3">LEVEL</th><th class="group-label-grey py-3">SEM</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">UNITS</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($curriculumDetails as $detail): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($detail['curriculum_name']); ?></div><div class="row-text"><?php echo htmlspecialchars($detail['program_code'] . ' | ' . $detail['effective_academic_year']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars((string) $detail['year_level']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($detail['semester']); ?></td>
                                <td class="py-3 row-val"><?php echo htmlspecialchars($detail['code'] . ' - ' . $detail['description']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars((string) $detail['units']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($curriculumDetails)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-5">No curriculum mappings yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'catalog'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">COURSE / SUBJECT CATALOG MANAGEMENT</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><input type="text" name="code" class="form-control row-text" placeholder="Subject Code" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="text" name="description" class="form-control row-text" placeholder="Subject Description" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-4"><input type="number" step="0.5" min="1" name="units" class="form-control row-text" placeholder="Units" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-4"><input type="text" name="program" class="form-control row-text" placeholder="Program" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-4"><input type="number" min="1" max="6" name="year_level" class="form-control row-text" placeholder="Year" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><select name="semester" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>1st</option><option>2nd</option><option>Summer</option></select></div>
                    <div class="col-md-6"><input type="text" name="category" value="Lecture" class="form-control row-text" placeholder="Category" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><button type="submit" name="save_subject" class="btn btn-success fw-bold" style="border-radius:8px;">Save Subject</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">SUBJECT CATALOG</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;">
                        <tr><th class="group-label-grey py-3 px-4">SUBJECT</th><th class="group-label-grey py-3">PROGRAM</th><th class="group-label-grey py-3">YEAR</th><th class="group-label-grey py-3">SEM</th><th class="group-label-grey py-3">PREREQUISITE</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($subject['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($subject['description']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($subject['program']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars((string) $subject['year_level']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($subject['semester']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($subject['prerequisite_codes'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($subjects)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-5">No subjects found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'dependencies'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">PREREQUISITE & CO-REQUISITE CONFIGURATION</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-md-5">
                        <select name="subject_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Target subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo (int) $subject['id']; ?>"><?php echo htmlspecialchars($subject['code'] . ' - ' . $subject['description']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <select name="dependency_subject_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Dependency subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo (int) $subject['id']; ?>"><?php echo htmlspecialchars($subject['code'] . ' - ' . $subject['description']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2"><select name="dependency_type" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Prerequisite</option><option>Co-requisite</option></select></div>
                    <div class="col-12"><button type="submit" name="save_dependency" class="btn btn-outline-success fw-bold" style="border-radius:8px;">Save Dependency</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">DEPENDENCY MAP</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;">
                        <tr><th class="group-label-grey py-3 px-4">TARGET SUBJECT</th><th class="group-label-grey py-3">TYPE</th><th class="group-label-grey py-3">DEPENDENCY</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dependencyRows as $row): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($row['target_code']); ?></div><div class="row-text"><?php echo htmlspecialchars($row['target_description']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($row['dependency_type']); ?></td>
                                <td class="py-3"><div class="row-val"><?php echo htmlspecialchars($row['dependency_code']); ?></div><div class="row-text"><?php echo htmlspecialchars($row['dependency_description']); ?></div></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($dependencyRows)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-5">No dependency configuration found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'schedule'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">COURSE SCHEDULING</span></div>
            <div class="card-body p-4">
                <div class="row-text mb-3">Scheduling data is surfaced here so curriculum planning can align with section and room availability.</div>
                <div class="border rounded-3 p-3 mb-3" style="border-color:#e2e8f0 !important;"><div class="group-label-grey">SECTIONS</div><div class="row-val mt-1"><?php echo count($sections); ?></div></div>
                <div class="border rounded-3 p-3 mb-3" style="border-color:#e2e8f0 !important;"><div class="group-label-grey">ROOMS</div><div class="row-val mt-1"><?php echo count($rooms); ?></div></div>
                <div class="border rounded-3 p-3" style="border-color:#e2e8f0 !important;"><div class="group-label-grey">SCHEDULE ROWS</div><div class="row-val mt-1"><?php echo count($scheduleRows); ?></div></div>
                <a href="class_scheduling.php" class="btn btn-outline-success fw-bold mt-4" style="border-radius:8px;">Open Detailed Scheduling Module</a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">SCHEDULE OVERVIEW</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;">
                        <tr><th class="group-label-grey py-3 px-4">SECTION</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">DAY / TIME</th><th class="group-label-grey py-3">ROOM</th><th class="group-label-grey py-3">TEACHER</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scheduleRows as $row): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($row['section_code']); ?></div><div class="row-text"><?php echo htmlspecialchars($row['program_code'] . ' / Year ' . $row['year_level']); ?></div></td>
                                <td class="py-3"><div class="row-val"><?php echo htmlspecialchars($row['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($row['description']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars(($row['day_of_week'] ?? '') . ' ' . ($row['time_start'] ?? '') . ' - ' . ($row['time_end'] ?? '')); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($row['room_code'] ?? 'TBA'); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($row['teacher_name'] ?? 'TBA'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($scheduleRows)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-5">No scheduling records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'revisions'): ?>
<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">CURRICULUM REVISION MANAGEMENT</span></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#f8fafc;">
                <tr><th class="group-label-grey py-3 px-4">CURRICULUM</th><th class="group-label-grey py-3">PROGRAM</th><th class="group-label-grey py-3">EFFECTIVE AY</th><th class="group-label-grey py-3">STATUS</th><th class="group-label-grey py-3">SUBJECT COUNT</th></tr>
            </thead>
            <tbody>
                <?php foreach ($curricula as $curriculum): ?>
                    <tr>
                        <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($curriculum['curriculum_name']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($curriculum['program_code']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($curriculum['effective_academic_year']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($curriculum['status']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars((string) $curriculum['subject_count']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($curricula)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-5">No curriculum revisions found.</td></tr>
                <?php endif; ?>
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
