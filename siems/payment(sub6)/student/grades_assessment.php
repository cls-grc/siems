<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Grades & Assessment';
$studentId = $_SESSION['student_id'];
$overview = siemsGetStudentGradesOverview($studentId);
$submissions = $overview['submissions'];
$records = $overview['records'];
$changeRequests = $overview['change_requests'];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-clipboard-data text-success me-2"></i>Grades & Assessment</h3>
        <div class="row-text">View posted grades, assessment records, and any change-request activity.</div>
    </div>
    <a href="siems_portal.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left me-1"></i> SIEMS Portal
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">POSTED RECORDS</div><div class="val-total-amount fs-2 mt-2 text-success"><?php echo count($records); ?></div></div></div>
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">CURRENT SUBMISSIONS</div><div class="val-total-amount fs-2 mt-2" style="color:#0ea5e9;"><?php echo count($submissions); ?></div></div></div>
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">CHANGE REQUESTS</div><div class="val-total-amount fs-2 mt-2" style="color:#f59e0b;"><?php echo count($changeRequests); ?></div></div></div>
</div>

<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4"><span class="section-title">POSTED ACADEMIC RECORDS</span></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">SUBJECT</th><th class="group-label-grey py-3">TERM</th><th class="group-label-grey py-3">GRADE</th><th class="group-label-grey py-3">REMARKS</th></tr></thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($record['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($record['description']); ?></div></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($record['academic_year'] . ' / ' . $record['semester']); ?></td>
                        <td class="py-3 row-val"><?php echo htmlspecialchars((string) $record['final_grade']); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($record['remarks'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($records)): ?><tr><td colspan="4" class="text-center text-muted py-5">No academic records posted yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-outline bg-white h-100">
            <div class="balance-header py-3 px-4"><span class="section-title">GRADE SUBMISSION TRACKER</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">SUBJECT</th><th class="group-label-grey py-3">TERM</th><th class="group-label-grey py-3">GRADE</th><th class="group-label-grey py-3">STATUS</th></tr></thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($submission['code']); ?></div><div class="row-text"><?php echo htmlspecialchars($submission['description']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($submission['term_label']); ?></td>
                                <td class="py-3 row-val"><?php echo htmlspecialchars((string) $submission['grade_value']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($submission['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($submissions)): ?><tr><td colspan="4" class="text-center text-muted py-5">No grade submissions found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-outline bg-white h-100">
            <div class="balance-header py-3 px-4"><span class="section-title">CHANGE REQUEST HISTORY</span></div>
            <div class="card-body p-4">
                <?php foreach ($changeRequests as $request): ?>
                    <div class="border rounded-3 p-3 mb-3" style="border-color:#e2e8f0 !important;">
                        <div class="row-val"><?php echo htmlspecialchars($request['code']); ?> - <?php echo htmlspecialchars($request['description']); ?></div>
                        <div class="row-text mt-2">Requested change: <?php echo htmlspecialchars((string) $request['old_grade']); ?> to <?php echo htmlspecialchars((string) $request['new_grade']); ?></div>
                        <div class="group-label-grey mt-2"><?php echo htmlspecialchars($request['status']); ?> | <?php echo htmlspecialchars($request['reason']); ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($changeRequests)): ?><div class="text-muted">No grade change requests on file.</div><?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
