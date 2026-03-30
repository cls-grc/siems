<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';

requireSubsystemAccess('siems_hub');

$page_title = 'Integrated SIEMS Hub';
$summary = getAdminIntegratedSummary();
$subsystems = getSubsystemRegistry();
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-grid-1x2-fill text-success me-2"></i>Integrated SIEMS Hub</h3>
        <div class="row-text">Shared entry point for all subsystems using the existing payment UI.</div>
    </div>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left me-1"></i> Payment Dashboard
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-1 mb-1" style="color: #10b981;"><?php echo $summary['students']; ?></div>
            <div class="group-label-grey">TOTAL STUDENTS</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-1 mb-1" style="color: #f59e0b;"><?php echo $summary['pending_enrollments']; ?></div>
            <div class="group-label-grey">PENDING ENROLLMENTS</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-2 mb-1 text-success">PHP <?php echo number_format($summary['verified_collection'], 2); ?></div>
            <div class="group-label-grey">VERIFIED COLLECTIONS</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-1 mb-1" style="color: #0ea5e9;"><?php echo $summary['audit_events']; ?></div>
            <div class="group-label-grey">AUDIT EVENTS</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card-outline bg-white h-100">
            <div class="balance-header py-3 px-4">
                <span class="section-title">SUBSYSTEM MAP</span>
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
                                    <span class="subsystem-pill">Integrated</span>
                                </div>
                                <h5 class="section-title mb-2"><?php echo htmlspecialchars($subsystem['name']); ?></h5>
                                <p class="row-text mb-3"><?php echo htmlspecialchars($subsystem['description']); ?></p>
                                <?php if ($subsystem['admin_url'] !== '#'): ?>
                                    <a href="<?php echo htmlspecialchars($subsystem['admin_url']); ?>" class="btn btn-sm btn-outline-success fw-bold" style="border-radius: 8px;">
                                        Open Module
                                    </a>
                                <?php else: ?>
                                    <span class="group-label-grey">Uses unified database schema and is ready for page-level build-out.</span>
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
                <span class="section-title">CROSS-SUBSYSTEM SNAPSHOT</span>
            </div>
            <div class="card-body p-4">
                <div class="snapshot-item">
                    <span class="row-text">Active Curricula</span>
                    <span class="row-val"><?php echo $summary['active_curricula']; ?></span>
                </div>
                <div class="snapshot-item">
                    <span class="row-text">Open Sections</span>
                    <span class="row-val"><?php echo $summary['open_sections']; ?></span>
                </div>
                <div class="snapshot-item">
                    <span class="row-text">Pending Grade Reviews</span>
                    <span class="row-val"><?php echo $summary['pending_grades']; ?></span>
                </div>
                <div class="snapshot-item">
                    <span class="row-text">Pending Document Requests</span>
                    <span class="row-val"><?php echo $summary['pending_documents']; ?></span>
                </div>
                <div class="snapshot-item">
                    <span class="row-text">Active Employees</span>
                    <span class="row-val"><?php echo $summary['employees']; ?></span>
                </div>
                <div class="snapshot-item border-bottom-0 pb-0">
                    <span class="row-text">Clinic Visits Today</span>
                    <span class="row-val"><?php echo $summary['clinic_visits_today']; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
