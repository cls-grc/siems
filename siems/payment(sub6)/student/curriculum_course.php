<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Curriculum & Course';
$studentId = $_SESSION['student_id'];
$overview = siemsGetStudentCurriculumOverview($studentId);
$user = $overview['user'];
$curriculum = $overview['curriculum'];
$curriculumCourses = $overview['curriculum_courses'];
$subjects = $overview['subjects'];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-diagram-3 text-success me-2"></i>Curriculum & Course</h3>
        <div class="row-text">Your program structure, subject lineup, and prerequisite guide.</div>
    </div>
    <a href="siems_portal.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left me-1"></i> SIEMS Portal
    </a>
</div>

<?php if ($user): ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">PROGRAM</div><div class="val-total-amount fs-3 text-success mt-2"><?php echo htmlspecialchars($user['program']); ?></div></div></div>
        <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">YEAR LEVEL</div><div class="val-total-amount fs-3 mt-2" style="color:#0ea5e9;"><?php echo htmlspecialchars((string) $user['year_level']); ?></div></div></div>
        <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">ACTIVE CURRICULUM</div><div class="val-total-amount fs-5 mt-3"><?php echo htmlspecialchars($curriculum['curriculum_name'] ?? 'Not assigned'); ?></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card-outline bg-white h-100">
                <div class="balance-header py-3 px-4"><span class="section-title">CURRICULUM PROFILE</span></div>
                <div class="card-body p-4">
                    <?php if ($curriculum): ?>
                        <div class="group-label-grey">CURRICULUM NAME</div>
                        <div class="row-val mb-3"><?php echo htmlspecialchars($curriculum['curriculum_name']); ?></div>
                        <div class="group-label-grey">ACADEMIC YEAR</div>
                        <div class="row-text mb-3"><?php echo htmlspecialchars($curriculum['effective_academic_year']); ?></div>
                        <div class="group-label-grey">STATUS</div>
                        <div class="row-text mb-3"><?php echo htmlspecialchars($curriculum['status']); ?></div>
                        <div class="group-label-grey">RECOMMENDED FLOW</div>
                        <div class="row-text"><?php echo count($curriculumCourses); ?> mapped subject(s) in your curriculum plan.</div>
                    <?php else: ?>
                        <div class="text-muted">No curriculum is assigned to your program yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card-outline bg-white h-100">
                <div class="balance-header py-3 px-4"><span class="section-title">CURRICULUM MAP</span></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color:#f8fafc;">
                            <tr><th class="group-label-grey py-3 px-4">LEVEL</th><th class="group-label-grey py-3">SEM</th><th class="group-label-grey py-3">SUBJECT</th><th class="group-label-grey py-3">UNITS</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($curriculumCourses as $course): ?>
                                <tr>
                                    <td class="py-3 px-4 row-text"><?php echo htmlspecialchars((string) $course['year_level']); ?></td>
                                    <td class="py-3 row-text"><?php echo htmlspecialchars($course['semester']); ?></td>
                                    <td class="py-3"><div class="row-val"><?php echo htmlspecialchars($course['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($course['description']); ?></div></td>
                                    <td class="py-3 row-text"><?php echo htmlspecialchars((string) $course['units']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($curriculumCourses)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-5">No curriculum map available yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card-outline bg-white mt-4">
        <div class="balance-header py-3 px-4"><span class="section-title">PROGRAM SUBJECT GUIDE</span></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color:#f8fafc;">
                    <tr><th class="group-label-grey py-3 px-4">SUBJECT</th><th class="group-label-grey py-3">LEVEL</th><th class="group-label-grey py-3">SEM</th><th class="group-label-grey py-3">CATEGORY</th><th class="group-label-grey py-3">PREREQUISITE</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($subject['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($subject['description']); ?></div></td>
                            <td class="py-3 row-text"><?php echo htmlspecialchars((string) $subject['year_level']); ?></td>
                            <td class="py-3 row-text"><?php echo htmlspecialchars($subject['semester']); ?></td>
                            <td class="py-3 row-text"><?php echo htmlspecialchars($subject['category']); ?></td>
                            <td class="py-3 row-text"><?php echo htmlspecialchars($subject['prerequisite_codes'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($subjects)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-5">No subjects found for your program.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
