<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';

requireSubsystemAccess('class_scheduling');

$page_title = 'Class Scheduling';
$facultyProgram = null;
if ($_SESSION['role'] === 'faculty') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT faculty_program FROM users WHERE student_id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $facultyProgram = $stmt->fetchColumn();
}

$teacherQuery = "SELECT id, full_name FROM users WHERE role IN ('faculty', 'admin', 'registrar')";
$params = [];
if ($_SESSION['role'] === 'faculty' && $facultyProgram) {
    $teacherQuery .= " AND (faculty_program = ? OR role != 'faculty')";
    $params[] = $facultyProgram;
}
$teacherQuery .= " ORDER BY full_name ASC";
$teachers = siemsFetchAll($teacherQuery, $params);
$overview = siemsGetAdminScheduleOverview();
$rooms = $overview['rooms'];
$sections = $overview['sections'];
$schedules = $overview['schedules'];
$subjects = siemsFetchAll("SELECT id, code, description FROM subjects ORDER BY code ASC");

$module = $_GET['module'] ?? '';
$allowedModules = ['sections', 'timetable', 'rooms', 'loading', 'conflicts', 'audit'];
if (!in_array($module, $allowedModules, true)) {
    $module = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['save_room'])) {
            $stmt = $pdo->prepare("
                INSERT INTO rooms (room_code, building_name, capacity, room_type, active)
                VALUES (?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                trim($_POST['room_code']),
                trim($_POST['building_name']),
                (int) $_POST['capacity'],
                trim($_POST['room_type']),
            ]);
            siemsLogSubsystemEvent('Class Scheduling', 'Created room', null, null, trim($_POST['room_code']));
            $_SESSION['message'] = 'Room saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: class_scheduling.php?module=rooms');
            exit;
        } elseif (isset($_POST['save_section'])) {
            $stmt = $pdo->prepare("
                INSERT INTO sections (section_code, program_code, year_level, adviser_user_id, capacity, status)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                trim($_POST['section_code']),
                trim($_POST['program_code']),
                (int) $_POST['year_level'],
                (int) $_POST['adviser_user_id'] ?: null,
                (int) $_POST['section_capacity'],
                trim($_POST['section_status']),
            ]);
            siemsLogSubsystemEvent('Class Scheduling', 'Created section', null, null, trim($_POST['section_code']));
            $_SESSION['message'] = 'Section saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: class_scheduling.php?module=sections');
            exit;
        } elseif (isset($_POST['save_schedule'])) {
            $conflict = siemsFetchOne("
                SELECT id
                FROM class_schedules
                WHERE room_id = ?
                  AND day_of_week = ?
                  AND time_start < ?
                  AND time_end > ?
                LIMIT 1
            ", [
                (int) $_POST['room_id'],
                trim($_POST['day_of_week']),
                trim($_POST['time_end']),
                trim($_POST['time_start']),
            ]);

            $stmt = $pdo->prepare("
                INSERT INTO class_schedules (section_id, subject_id, teacher_user_id, room_id, day_of_week, time_start, time_end, conflict_flag)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                (int) $_POST['section_id'],
                (int) $_POST['subject_id'],
                (int) $_POST['teacher_user_id'] ?: null,
                (int) $_POST['room_id'] ?: null,
                trim($_POST['day_of_week']),
                trim($_POST['time_start']),
                trim($_POST['time_end']),
                $conflict ? 1 : 0,
            ]);

            $scheduleId = (int) $pdo->lastInsertId();
            if (!empty($_POST['teacher_user_id'])) {
                $loadStmt = $pdo->prepare("
                    INSERT IGNORE INTO teacher_loads (teacher_user_id, class_schedule_id, load_units)
                    VALUES (?, ?, ?)
                ");
                $loadStmt->execute([
                    (int) $_POST['teacher_user_id'],
                    $scheduleId,
                    (float) ($_POST['load_units'] ?: 3),
                ]);
            }

            siemsLogSubsystemEvent('Class Scheduling', 'Created class schedule', null, null, 'Schedule #' . $scheduleId);
            $_SESSION['message'] = $conflict ? 'Schedule saved, but a room time conflict was detected and flagged.' : 'Schedule saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: class_scheduling.php?module=timetable');
            exit;
        }
    } catch (Throwable $e) {
        $_SESSION['message'] = 'Unable to save scheduling data. It may already exist or contain invalid values.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: class_scheduling.php' . ($module !== '' ? '?module=' . urlencode($module) : ''));
        exit;
    }
}

$teacherLoads = siemsFetchAll("
    SELECT tl.load_units, teacher.full_name AS teacher_name, sec.section_code, sub.code
    FROM teacher_loads tl
    INNER JOIN class_schedules cs ON cs.id = tl.class_schedule_id
    INNER JOIN users teacher ON teacher.id = tl.teacher_user_id
    INNER JOIN sections sec ON sec.id = cs.section_id
    INNER JOIN subjects sub ON sub.id = cs.subject_id
    ORDER BY teacher.full_name ASC, sec.section_code ASC
");

$conflictSchedules = array_values(array_filter($schedules, fn($row) => (int) $row['conflict_flag'] === 1));
$recentAuditLogs = siemsTableExists('audit_log') ? siemsFetchAll("
    SELECT al.*, u.full_name AS user_name
    FROM audit_log al
    LEFT JOIN users u ON u.id = al.user_id
    WHERE " . (siemsColumnExists('audit_log', 'subsystem') ? "al.subsystem = 'Class Scheduling'" : "al.action LIKE '%schedule%' OR al.action LIKE '%section%' OR al.action LIKE '%room%'") . "
    ORDER BY al.created_at DESC, al.id DESC
    LIMIT 20
") : [];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-calendar3-week text-success me-2"></i>Class Scheduling</h3>
        <div class="row-text">Subsystem 4 for sections, timetables, room checking, teacher loading, conflict detection, and audit visibility.</div>
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
                <a href="class_scheduling.php?module=sections" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-collection d-block fs-4 mb-2"></i>Sections
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="class_scheduling.php?module=timetable" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-table d-block fs-4 mb-2"></i>Timetable
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="class_scheduling.php?module=rooms" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-door-open d-block fs-4 mb-2"></i>Rooms
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="class_scheduling.php?module=loading" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-person-lines-fill d-block fs-4 mb-2"></i>Teacher Loading
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="class_scheduling.php?module=conflicts" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-exclamation-triangle d-block fs-4 mb-2"></i>Conflicts
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="class_scheduling.php?module=audit" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-clock-history d-block fs-4 mb-2"></i>Audit Logs
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($module === ''): ?>
<div class="card-outline bg-white p-5 text-center">
    <i class="bi bi-grid-1x2 fs-1 text-success d-block mb-3"></i>
    <h4 class="dashboard-title mb-2">Subsystem 4 Module Launcher</h4>
    <div class="row-text">Choose a quick action button above to open one module at a time.</div>
</div>
<?php elseif ($module === 'sections'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">SECTION CREATION & ASSIGNMENT</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><input type="text" name="section_code" class="form-control row-text" placeholder="Section Code" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="text" name="program_code" class="form-control row-text" placeholder="Program" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="number" min="1" max="6" name="year_level" class="form-control row-text" placeholder="Year" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6">
                        <select name="adviser_user_id" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select adviser</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo (int) $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3"><input type="number" min="1" name="section_capacity" value="40" class="form-control row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-3"><select name="section_status" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Open</option><option>Closed</option><option>Archived</option></select></div>
                    <div class="col-12"><button type="submit" name="save_section" class="btn btn-success fw-bold" style="border-radius:8px;">Save Section</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">SECTION DIRECTORY</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;">
                        <tr><th class="group-label-grey py-3 px-4">SECTION</th><th class="group-label-grey py-3">PROGRAM</th><th class="group-label-grey py-3">YEAR</th><th class="group-label-grey py-3">ADVISER</th><th class="group-label-grey py-3">STATUS</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sections as $section): ?>
                            <tr>
                                <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($section['section_code']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($section['program_code']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars((string) $section['year_level']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($section['adviser_name'] ?? 'Unassigned'); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($section['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($sections)): ?><tr><td colspan="5" class="text-center text-muted py-5">No sections found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'timetable'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">CLASS TIMETABLE GENERATION</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12">
                        <select name="section_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select section</option>
                            <?php foreach ($sections as $section): ?>
                                <option value="<?php echo (int) $section['id']; ?>"><?php echo htmlspecialchars($section['section_code'] . ' (' . $section['program_code'] . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <select name="subject_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo (int) $subject['id']; ?>"><?php echo htmlspecialchars($subject['code'] . ' - ' . $subject['description']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <select name="teacher_user_id" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select teacher</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo (int) $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <select name="room_id" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;">
                            <option value="">Select room</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?php echo (int) $room['id']; ?>"><?php echo htmlspecialchars($room['room_code']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12"><select name="day_of_week" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option></select></div>
                    <div class="col-12"><input type="time" name="time_start" class="form-control row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="time" name="time_end" class="form-control row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="number" step="0.5" min="0" name="load_units" value="3" class="form-control row-text" placeholder="Load" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><button type="submit" name="save_schedule" class="btn btn-outline-success fw-bold" style="border-radius:8px;">Save Schedule</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">MASTER TIMETABLE</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;">
                        <tr><th class="group-label-grey py-3 px-4">SECTION</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">DAY/TIME</th><th class="group-label-grey py-3">ROOM</th><th class="group-label-grey py-3">TEACHER</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($schedule['section_code']); ?></div><div class="row-text"><?php echo htmlspecialchars($schedule['program_code'] . ' Year ' . $schedule['year_level']); ?></div></td>
                                <td class="py-3"><div class="row-val"><?php echo htmlspecialchars($schedule['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($schedule['description']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($schedule['day_of_week'] . ' ' . substr($schedule['time_start'], 0, 5) . '-' . substr($schedule['time_end'], 0, 5)); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($schedule['room_code'] ?? 'TBA'); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($schedule['teacher_name'] ?? 'TBA'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($schedules)): ?><tr><td colspan="5" class="text-center text-muted py-5">No schedules found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'rooms'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">ROOM ASSIGNMENT & AVAILABILITY CHECKING</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><input type="text" name="room_code" class="form-control row-text" placeholder="Room Code" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="text" name="building_name" class="form-control row-text" placeholder="Building" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="number" min="1" name="capacity" class="form-control row-text" placeholder="Capacity" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="text" name="room_type" value="Lecture" class="form-control row-text" placeholder="Type" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><button type="submit" name="save_room" class="btn btn-success fw-bold" style="border-radius:8px;">Save Room</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">ROOM AVAILABILITY LIST</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;">
                        <tr><th class="group-label-grey py-3 px-4">ROOM</th><th class="group-label-grey py-3">BUILDING</th><th class="group-label-grey py-3">CAPACITY</th><th class="group-label-grey py-3">TYPE</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($room['room_code']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($room['building_name'] ?? 'N/A'); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars((string) $room['capacity']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($room['room_type'] ?? 'Lecture'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($rooms)): ?><tr><td colspan="4" class="text-center text-muted py-5">No rooms found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'loading'): ?>
<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">TEACHER LOADING MANAGEMENT</span></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#f8fafc;">
                <tr><th class="group-label-grey py-3 px-4">TEACHER</th><th class="group-label-grey py-3">SECTION</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">LOAD UNITS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($teacherLoads as $load): ?>
                    <tr>
                        <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($load['teacher_name']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($load['section_code']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($load['code']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars((string) $load['load_units']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($teacherLoads)): ?><tr><td colspan="4" class="text-center text-muted py-5">No teacher loads recorded yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php elseif ($module === 'conflicts'): ?>
<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">SCHEDULE CONFLICT DETECTION</span></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#f8fafc;">
                <tr><th class="group-label-grey py-3 px-4">SECTION</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">DAY/TIME</th><th class="group-label-grey py-3">ROOM</th><th class="group-label-grey py-3">FLAG</th></tr>
            </thead>
            <tbody>
                <?php foreach ($conflictSchedules as $schedule): ?>
                    <tr>
                        <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($schedule['section_code']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($schedule['code']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($schedule['day_of_week'] . ' ' . substr($schedule['time_start'], 0, 5) . '-' . substr($schedule['time_end'], 0, 5)); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($schedule['room_code'] ?? 'TBA'); ?></td>
                        <td class="py-3 row-val">Conflict</td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($conflictSchedules)): ?><tr><td colspan="5" class="text-center text-muted py-5">No schedule conflicts detected.</td></tr><?php endif; ?>
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
