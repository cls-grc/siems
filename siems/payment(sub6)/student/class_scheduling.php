<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Class Scheduling';
$studentId = $_SESSION['student_id'];
$overview = siemsGetStudentScheduleOverview($studentId);
$section = $overview['section'];
$schedules = $overview['schedules'];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-calendar3-week text-success me-2"></i>Class Scheduling</h3>
        <div class="row-text">Your assigned section timetable, room assignments, and teaching schedule.</div>
    </div>
    <a href="siems_portal.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left me-1"></i> SIEMS Portal
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">SECTION</div><div class="val-total-amount fs-3 text-success mt-2"><?php echo htmlspecialchars($overview['section_code'] ?? 'Not Assigned'); ?></div></div></div>
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">ADVISER</div><div class="val-total-amount fs-5 mt-3"><?php echo htmlspecialchars($section['adviser_name'] ?? 'TBA'); ?></div></div></div>
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">SCHEDULE ENTRIES</div><div class="val-total-amount fs-2 mt-2" style="color:#0ea5e9;"><?php echo count($schedules); ?></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white h-100">
            <div class="balance-header py-3 px-4"><span class="section-title">SECTION SUMMARY</span></div>
            <div class="card-body p-4">
                <?php if ($section): ?>
                    <div class="group-label-grey">PROGRAM</div>
                    <div class="row-val mb-3"><?php echo htmlspecialchars($section['program_code']); ?></div>
                    <div class="group-label-grey">YEAR LEVEL</div>
                    <div class="row-text mb-3"><?php echo htmlspecialchars((string) $section['year_level']); ?></div>
                    <div class="group-label-grey">CAPACITY</div>
                    <div class="row-text mb-3"><?php echo htmlspecialchars((string) $section['capacity']); ?></div>
                    <div class="group-label-grey">STATUS</div>
                    <div class="row-text"><?php echo htmlspecialchars($section['status']); ?></div>
                <?php else: ?>
                    <div class="text-muted">No section assignment found yet from enrollment or scheduling records.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">WEEKLY TIMETABLE</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;">
                        <tr><th class="group-label-grey py-3 px-4">DAY</th><th class="group-label-grey py-3">TIME</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">ROOM</th><th class="group-label-grey py-3">TEACHER</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td class="py-3 px-4 row-text"><?php echo htmlspecialchars($schedule['day_of_week']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars(substr($schedule['time_start'], 0, 5) . '-' . substr($schedule['time_end'], 0, 5)); ?></td>
                                <td class="py-3"><div class="row-val"><?php echo htmlspecialchars($schedule['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($schedule['description']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($schedule['room_code'] ?? 'TBA'); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($schedule['teacher_name'] ?? 'TBA'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($schedules)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-5">No class schedule has been posted for your section yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
