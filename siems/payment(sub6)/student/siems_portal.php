<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Student SIEMS Portal';
$studentId = $_SESSION['student_id'];
$summary = getStudentIntegratedSummary($studentId);
$cards = getStudentSubsystemStatusCards($studentId);
$subsystems = getSubsystemRegistry();
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-mortarboard text-success me-2"></i>Student SIEMS Portal</h3>
        <div class="row-text"><?php echo htmlspecialchars($summary['student_name']); ?> | <?php echo htmlspecialchars($summary['program']); ?> | Year <?php echo htmlspecialchars((string) $summary['year_level']); ?></div>
    </div>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left me-1"></i> Student Dashboard
    </a>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($cards as $card): ?>
        <div class="col-md-4 col-lg-2">
            <div class="card-outline bg-white p-3 h-100 text-center">
                <i class="bi <?php echo htmlspecialchars($card['icon']); ?> fs-3 mb-2 text-<?php echo htmlspecialchars($card['tone']); ?>"></i>
                <div class="group-label-grey mb-1"><?php echo htmlspecialchars($card['label']); ?></div>
                <div class="row-val"><?php echo htmlspecialchars((string) $card['value']); ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4">
                <span class="section-title">AVAILABLE SERVICES</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <?php foreach ($subsystems as $subsystem): ?>
                        <div class="col-md-6">
                            <div class="subsystem-card h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="subsystem-icon-wrap">
                                        <i class="bi <?php echo htmlspecialchars($subsystem['icon']); ?>"></i>
                                    </div>
                                    <span class="subsystem-pill">Student View</span>
                                </div>
                                <h5 class="section-title mb-2"><?php echo htmlspecialchars($subsystem['name']); ?></h5>
                                <p class="row-text mb-3"><?php echo htmlspecialchars($subsystem['description']); ?></p>
                                <?php if ($subsystem['student_url'] !== '#'): ?>
                                    <a href="<?php echo htmlspecialchars($subsystem['student_url']); ?>" class="btn btn-sm btn-outline-success fw-bold" style="border-radius: 8px;">
                                        Open Service
                                    </a>
                                <?php else: ?>
                                    <span class="group-label-grey">Ready to connect once the module page is added.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-outline bg-white h-100">
            <div class="balance-header py-3 px-4">
                <span class="section-title">INTEGRATION STATUS</span>
            </div>
            <div class="card-body p-4">
                <div class="snapshot-item">
                    <span class="row-text">Enrollment Status</span>
                    <span class="row-val"><?php echo htmlspecialchars($summary['enrollment_status']); ?></span>
                </div>
                <div class="snapshot-item">
                    <span class="row-text">Outstanding Balance</span>
                    <span class="row-val">PHP <?php echo number_format($summary['latest_balance'], 2); ?></span>
                </div>
                <div class="snapshot-item">
                    <span class="row-text">Document Requests</span>
                    <span class="row-val"><?php echo $summary['requested_documents']; ?></span>
                </div>
                <div class="snapshot-item">
                    <span class="row-text">Approved Grades</span>
                    <span class="row-val"><?php echo $summary['approved_grades']; ?></span>
                </div>
                <div class="snapshot-item">
                    <span class="row-text">Medical Clearance</span>
                    <span class="row-val"><?php echo htmlspecialchars($summary['medical_clearance']); ?></span>
                </div>
                <div class="snapshot-item border-bottom-0 pb-0">
                    <span class="row-text">Payments Posted</span>
                    <span class="row-val"><?php echo $summary['recent_payments']; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
