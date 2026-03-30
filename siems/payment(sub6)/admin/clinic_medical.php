<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';

requireSubsystemAccess('clinic_medical');

$page_title = 'Clinic & Medical';
$students = siemsFetchAll("SELECT student_id, full_name, program FROM users WHERE role = 'student' ORDER BY full_name ASC");

$module = $_GET['module'] ?? '';
$allowedModules = ['medical', 'consultation', 'inventory', 'clearance', 'incidents', 'audit'];
if (!in_array($module, $allowedModules, true)) {
    $module = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['save_medical_record'])) {
            $pdo->prepare("
                INSERT INTO medical_records (student_id, blood_type, allergies, medical_history)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    blood_type = VALUES(blood_type),
                    allergies = VALUES(allergies),
                    medical_history = VALUES(medical_history)
            ")->execute([trim($_POST['student_id']), trim($_POST['blood_type']) ?: null, trim($_POST['allergies']) ?: null, trim($_POST['medical_history']) ?: null]);
            siemsLogSubsystemEvent('Clinic & Medical', 'Saved medical record', trim($_POST['student_id']), null, trim($_POST['blood_type']));
            $_SESSION['message'] = 'Medical record saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: clinic_medical.php?module=medical');
            exit;
        } elseif (isset($_POST['save_consultation'])) {
            $pdo->prepare("
                INSERT INTO clinic_consultations (student_id, consultation_date, chief_complaint, treatment_given)
                VALUES (?, ?, ?, ?)
            ")->execute([trim($_POST['student_id']), $_POST['consultation_date'] ? date('Y-m-d H:i:s', strtotime($_POST['consultation_date'])) : date('Y-m-d H:i:s'), trim($_POST['chief_complaint']), trim($_POST['treatment_given']) ?: null]);
            siemsLogSubsystemEvent('Clinic & Medical', 'Saved consultation log', trim($_POST['student_id']), null, trim($_POST['chief_complaint']));
            $_SESSION['message'] = 'Consultation saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: clinic_medical.php?module=consultation');
            exit;
        } elseif (isset($_POST['save_medicine'])) {
            $pdo->prepare("
                INSERT INTO medicines (medicine_name, quantity_on_hand, reorder_level)
                VALUES (?, ?, ?)
            ")->execute([trim($_POST['medicine_name']), (int) $_POST['quantity_on_hand'], (int) $_POST['reorder_level']]);
            siemsLogSubsystemEvent('Clinic & Medical', 'Saved medicine inventory item', null, null, trim($_POST['medicine_name']));
            $_SESSION['message'] = 'Medicine saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: clinic_medical.php?module=inventory');
            exit;
        } elseif (isset($_POST['save_dispensing'])) {
            $pdo->beginTransaction();
            $consultationId = (int) $_POST['consultation_id'];
            $medicineId = (int) $_POST['medicine_id'];
            $quantityDispensed = (int) $_POST['quantity_dispensed'];

            $pdo->prepare("
                INSERT INTO medicine_dispensings (consultation_id, medicine_id, quantity_dispensed)
                VALUES (?, ?, ?)
            ")->execute([$consultationId, $medicineId, $quantityDispensed]);
            $pdo->prepare("UPDATE medicines SET quantity_on_hand = quantity_on_hand - ? WHERE id = ?")->execute([$quantityDispensed, $medicineId]);
            $pdo->commit();

            siemsLogSubsystemEvent('Clinic & Medical', 'Saved medicine dispensing', null, null, 'Consultation #' . $consultationId);
            $_SESSION['message'] = 'Medicine dispensing recorded successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: clinic_medical.php?module=inventory');
            exit;
        } elseif (isset($_POST['save_clearance'])) {
            $pdo->prepare("
                INSERT INTO medical_clearances (student_id, status, remarks, issued_at)
                VALUES (?, ?, ?, ?)
            ")->execute([trim($_POST['student_id']), trim($_POST['clearance_status']), trim($_POST['clearance_remarks']) ?: null, $_POST['issued_at'] ? date('Y-m-d H:i:s', strtotime($_POST['issued_at'])) : date('Y-m-d H:i:s')]);
            siemsLogSubsystemEvent('Clinic & Medical', 'Saved medical clearance', trim($_POST['student_id']), null, trim($_POST['clearance_status']));
            $_SESSION['message'] = 'Medical clearance saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: clinic_medical.php?module=clearance');
            exit;
        } elseif (isset($_POST['save_incident'])) {
            $pdo->prepare("
                INSERT INTO health_incidents (student_id, incident_date, incident_type, details)
                VALUES (?, ?, ?, ?)
            ")->execute([trim($_POST['student_id']), $_POST['incident_date'] ? date('Y-m-d H:i:s', strtotime($_POST['incident_date'])) : date('Y-m-d H:i:s'), trim($_POST['incident_type']), trim($_POST['incident_details']) ?: null]);
            siemsLogSubsystemEvent('Clinic & Medical', 'Saved health incident', trim($_POST['student_id']), null, trim($_POST['incident_type']));
            $_SESSION['message'] = 'Health incident saved successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: clinic_medical.php?module=incidents');
            exit;
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['message'] = 'Unable to save clinic data.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: clinic_medical.php' . ($module !== '' ? '?module=' . urlencode($module) : ''));
        exit;
    }
}

$overview = siemsGetAdminClinicOverview();
$medicalRecords = $overview['medical_records'];
$consultations = $overview['consultations'];
$medicines = $overview['medicines'];
$dispensings = $overview['dispensings'];
$clearances = $overview['clearances'];
$incidents = $overview['incidents'];
$recentAuditLogs = siemsTableExists('audit_log') ? siemsFetchAll("
    SELECT al.*, u.full_name AS user_name
    FROM audit_log al
    LEFT JOIN users u ON u.id = al.user_id
    WHERE " . (siemsColumnExists('audit_log', 'subsystem') ? "al.subsystem = 'Clinic & Medical'" : "al.action LIKE '%medical%' OR al.action LIKE '%consultation%' OR al.action LIKE '%incident%' OR al.action LIKE '%medicine%'") . "
    ORDER BY al.created_at DESC, al.id DESC
    LIMIT 20
") : [];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-heart-pulse text-success me-2"></i>Clinic & Medical</h3>
        <div class="row-text">Subsystem 9 for student medical records, consultations, medicine inventory, medical clearances, incidents, and audit visibility.</div>
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
                <a href="clinic_medical.php?module=medical" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-file-medical d-block fs-4 mb-2"></i>Medical Records
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="clinic_medical.php?module=consultation" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-clipboard2-pulse d-block fs-4 mb-2"></i>Consultations
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="clinic_medical.php?module=inventory" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-capsule-pill d-block fs-4 mb-2"></i>Medicine Inventory
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="clinic_medical.php?module=clearance" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-shield-check d-block fs-4 mb-2"></i>Medical Clearance
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="clinic_medical.php?module=incidents" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-exclamation-diamond d-block fs-4 mb-2"></i>Health Incidents
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="clinic_medical.php?module=audit" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-clock-history d-block fs-4 mb-2"></i>Audit Logs
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($module === ''): ?>
<div class="card-outline bg-white p-5 text-center">
    <i class="bi bi-grid-1x2 fs-1 text-success d-block mb-3"></i>
    <h4 class="dashboard-title mb-2">Subsystem 9 Module Launcher</h4>
    <div class="row-text">Choose a quick action button above to open one module at a time.</div>
</div>
<?php elseif ($module === 'medical'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">STUDENT MEDICAL RECORDS</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="student_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select student</option><?php foreach ($students as $student): ?><option value="<?php echo htmlspecialchars($student['student_id']); ?>"><?php echo htmlspecialchars($student['full_name'] . ' (' . $student['student_id'] . ')'); ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><input type="text" name="blood_type" class="form-control row-text" placeholder="Blood type" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><textarea name="allergies" rows="2" class="form-control row-text" placeholder="Allergies" style="border:2px solid #e2e8f0;border-radius:8px;"></textarea></div>
                    <div class="col-12"><textarea name="medical_history" rows="3" class="form-control row-text" placeholder="Medical history" style="border:2px solid #e2e8f0;border-radius:8px;"></textarea></div>
                    <div class="col-12"><button type="submit" name="save_medical_record" class="btn btn-success fw-bold" style="border-radius:8px;">Save Record</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">MEDICAL RECORD DIRECTORY</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">STUDENT</th><th class="group-label-grey py-3">PROGRAM</th><th class="group-label-grey py-3">BLOOD TYPE</th><th class="group-label-grey py-3">HISTORY</th></tr></thead>
                    <tbody>
                        <?php foreach ($medicalRecords as $record): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($record['full_name']); ?></div><div class="row-text"><?php echo htmlspecialchars($record['student_id']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($record['program'] ?? '-'); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($record['blood_type'] ?? '-'); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($record['medical_history'] ?? 'No medical history noted'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($medicalRecords)): ?><tr><td colspan="4" class="text-center text-muted py-5">No student medical records yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'consultation'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">CONSULTATION & TREATMENT LOGS</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="student_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select student</option><?php foreach ($students as $student): ?><option value="<?php echo htmlspecialchars($student['student_id']); ?>"><?php echo htmlspecialchars($student['full_name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><input type="datetime-local" name="consultation_date" class="form-control row-text" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="text" name="chief_complaint" class="form-control row-text" placeholder="Chief complaint" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><textarea name="treatment_given" rows="3" class="form-control row-text" placeholder="Treatment given" style="border:2px solid #e2e8f0;border-radius:8px;"></textarea></div>
                    <div class="col-12"><button type="submit" name="save_consultation" class="btn btn-success fw-bold" style="border-radius:8px;">Save Consultation</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">CONSULTATION HISTORY</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">STUDENT</th><th class="group-label-grey py-3">COMPLAINT</th><th class="group-label-grey py-3">TREATMENT</th><th class="group-label-grey py-3">DATE</th></tr></thead>
                    <tbody>
                        <?php foreach ($consultations as $consultation): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($consultation['full_name']); ?></div><div class="row-text"><?php echo htmlspecialchars($consultation['student_id']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($consultation['chief_complaint']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($consultation['treatment_given'] ?? 'No treatment note'); ?></td>
                                <td class="py-3 row-text"><?php echo date('M j, Y g:i A', strtotime($consultation['consultation_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($consultations)): ?><tr><td colspan="4" class="text-center text-muted py-5">No consultation logs yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'inventory'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">MEDICINE INVENTORY</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><input type="text" name="medicine_name" class="form-control row-text" placeholder="Medicine name" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="number" min="0" name="quantity_on_hand" class="form-control row-text" placeholder="Qty on hand" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-md-6"><input type="number" min="0" name="reorder_level" class="form-control row-text" placeholder="Reorder level" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><button type="submit" name="save_medicine" class="btn btn-success fw-bold" style="border-radius:8px;">Save Medicine</button></div>
                </form>
            </div>
        </div>
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">DISPENSE MEDICINE</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="consultation_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select consultation</option><?php foreach ($consultations as $consultation): ?><option value="<?php echo (int) $consultation['id']; ?>"><?php echo htmlspecialchars($consultation['full_name'] . ' - ' . $consultation['chief_complaint']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><select name="medicine_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select medicine</option><?php foreach ($medicines as $medicine): ?><option value="<?php echo (int) $medicine['id']; ?>"><?php echo htmlspecialchars($medicine['medicine_name'] . ' (' . $medicine['quantity_on_hand'] . ')'); ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><input type="number" min="1" name="quantity_dispensed" class="form-control row-text" placeholder="Quantity dispensed" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><button type="submit" name="save_dispensing" class="btn btn-outline-success fw-bold" style="border-radius:8px;">Save Dispensing</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4"><span class="section-title">MEDICINE STOCK LIST</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">MEDICINE</th><th class="group-label-grey py-3">ON HAND</th><th class="group-label-grey py-3">REORDER LEVEL</th></tr></thead>
                    <tbody>
                        <?php foreach ($medicines as $medicine): ?>
                            <tr>
                                <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($medicine['medicine_name']); ?></td>
                                <td class="py-3 row-text"><?php echo (int) $medicine['quantity_on_hand']; ?></td>
                                <td class="py-3 row-text"><?php echo (int) $medicine['reorder_level']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($medicines)): ?><tr><td colspan="3" class="text-center text-muted py-5">No medicines recorded yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">DISPENSING LOG</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">MEDICINE</th><th class="group-label-grey py-3">STUDENT</th><th class="group-label-grey py-3">QTY</th></tr></thead>
                    <tbody>
                        <?php foreach ($dispensings as $dispensing): ?>
                            <tr>
                                <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($dispensing['medicine_name']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($dispensing['full_name']); ?></td>
                                <td class="py-3 row-text"><?php echo (int) $dispensing['quantity_dispensed']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($dispensings)): ?><tr><td colspan="3" class="text-center text-muted py-5">No dispensing records yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'clearance'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">MEDICAL CLEARANCE ISSUANCE</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="student_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select student</option><?php foreach ($students as $student): ?><option value="<?php echo htmlspecialchars($student['student_id']); ?>"><?php echo htmlspecialchars($student['full_name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><select name="clearance_status" class="form-select row-text" style="border:2px solid #e2e8f0;border-radius:8px;"><option>Pending</option><option>Cleared</option><option>Not Cleared</option></select></div>
                    <div class="col-12"><input type="datetime-local" name="issued_at" class="form-control row-text" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><textarea name="clearance_remarks" rows="3" class="form-control row-text" placeholder="Clearance remarks" style="border:2px solid #e2e8f0;border-radius:8px;"></textarea></div>
                    <div class="col-12"><button type="submit" name="save_clearance" class="btn btn-success fw-bold" style="border-radius:8px;">Save Clearance</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">MEDICAL CLEARANCE REGISTER</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">STUDENT</th><th class="group-label-grey py-3">PROGRAM</th><th class="group-label-grey py-3">STATUS</th><th class="group-label-grey py-3">ISSUED</th><th class="group-label-grey py-3">REMARKS</th></tr></thead>
                    <tbody>
                        <?php foreach ($clearances as $clearance): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($clearance['full_name']); ?></div><div class="row-text"><?php echo htmlspecialchars($clearance['student_id']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($clearance['program'] ?? '-'); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($clearance['status']); ?></td>
                                <td class="py-3 row-text"><?php echo !empty($clearance['issued_at']) ? date('M j, Y g:i A', strtotime($clearance['issued_at'])) : '-'; ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($clearance['remarks'] ?? 'No remarks'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($clearances)): ?><tr><td colspan="5" class="text-center text-muted py-5">No medical clearances issued yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($module === 'incidents'): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">HEALTH INCIDENT REPORTING</span></div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12"><select name="student_id" class="form-select row-text" required style="border:2px solid #e2e8f0;border-radius:8px;"><option value="">Select student</option><?php foreach ($students as $student): ?><option value="<?php echo htmlspecialchars($student['student_id']); ?>"><?php echo htmlspecialchars($student['full_name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><input type="datetime-local" name="incident_date" class="form-control row-text" style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><input type="text" name="incident_type" class="form-control row-text" placeholder="Incident type" required style="border:2px solid #e2e8f0;border-radius:8px;"></div>
                    <div class="col-12"><textarea name="incident_details" rows="3" class="form-control row-text" placeholder="Incident details" style="border:2px solid #e2e8f0;border-radius:8px;"></textarea></div>
                    <div class="col-12"><button type="submit" name="save_incident" class="btn btn-success fw-bold" style="border-radius:8px;">Save Incident</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4"><span class="section-title">HEALTH INCIDENT LOG</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc;"><tr><th class="group-label-grey py-3 px-4">STUDENT</th><th class="group-label-grey py-3">INCIDENT</th><th class="group-label-grey py-3">DETAILS</th><th class="group-label-grey py-3">DATE</th></tr></thead>
                    <tbody>
                        <?php foreach ($incidents as $incident): ?>
                            <tr>
                                <td class="py-3 px-4"><div class="row-val"><?php echo htmlspecialchars($incident['full_name']); ?></div><div class="row-text"><?php echo htmlspecialchars($incident['student_id']); ?></div></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($incident['incident_type']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($incident['details'] ?? 'No details'); ?></td>
                                <td class="py-3 row-text"><?php echo date('M j, Y g:i A', strtotime($incident['incident_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($incidents)): ?><tr><td colspan="4" class="text-center text-muted py-5">No health incidents logged yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
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
                <?php if (empty($recentAuditLogs)): ?><tr><td colspan="5" class="text-center text-muted py-5">No clinic audit trail records found yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
