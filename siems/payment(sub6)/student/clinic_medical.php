<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Clinic & Medical';
$studentId = $_SESSION['student_id'];
$overview = siemsGetStudentClinicOverview($studentId);
$medicalRecord = $overview['medical_record'];
$consultations = $overview['consultations'];
$clearance = $overview['clearance'];
$incidents = $overview['incidents'];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-heart-pulse text-success me-2"></i>Clinic & Medical</h3>
        <div class="row-text">Your medical profile, clinic visits, health incidents, and clearance status.</div>
    </div>
    <a href="siems_portal.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;"><i class="bi bi-arrow-left me-1"></i> SIEMS Portal</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">BLOOD TYPE</div><div class="val-total-amount fs-3 mt-2 text-success"><?php echo htmlspecialchars($medicalRecord['blood_type'] ?? 'N/A'); ?></div></div></div>
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">MEDICAL CLEARANCE</div><div class="val-total-amount fs-5 mt-3"><?php echo htmlspecialchars($clearance['status'] ?? 'Pending'); ?></div></div></div>
    <div class="col-md-4"><div class="card-outline bg-white p-4 text-center h-100"><div class="group-label-grey">CLINIC VISITS</div><div class="val-total-amount fs-2 mt-2" style="color:#0ea5e9;"><?php echo count($consultations); ?></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">MEDICAL PROFILE</span></div>
            <div class="card-body p-4">
                <div class="group-label-grey">ALLERGIES</div>
                <div class="row-text mb-3"><?php echo htmlspecialchars($medicalRecord['allergies'] ?? 'None recorded'); ?></div>
                <div class="group-label-grey">MEDICAL HISTORY</div>
                <div class="row-text mb-3"><?php echo htmlspecialchars($medicalRecord['medical_history'] ?? 'No medical history on file'); ?></div>
                <div class="group-label-grey">CLEARANCE REMARKS</div>
                <div class="row-text"><?php echo htmlspecialchars($clearance['remarks'] ?? 'No remarks'); ?></div>
            </div>
        </div>

        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">HEALTH INCIDENTS</span></div>
            <div class="card-body p-4">
                <?php foreach ($incidents as $incident): ?>
                    <div class="border rounded-3 p-3 mb-3" style="border-color:#e2e8f0 !important;">
                        <div class="row-val"><?php echo htmlspecialchars($incident['incident_type']); ?></div>
                        <div class="row-text mt-2"><?php echo htmlspecialchars($incident['details'] ?? 'No details'); ?></div>
                        <div class="group-label-grey mt-2"><?php echo htmlspecialchars($incident['incident_date']); ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($incidents)): ?><div class="text-muted">No health incidents on record.</div><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">CONSULTATION HISTORY</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">DATE</th><th class="group-label-grey py-3">COMPLAINT</th><th class="group-label-grey py-3">TREATMENT</th></tr></thead>
                    <tbody>
                        <?php foreach ($consultations as $consultation): ?>
                            <tr>
                                <td class="py-3 px-4 row-text"><?php echo htmlspecialchars($consultation['consultation_date']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($consultation['chief_complaint']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($consultation['treatment_given'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($consultations)): ?><tr><td colspan="3" class="text-center text-muted py-5">No clinic consultations found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
