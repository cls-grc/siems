<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Student Information';
$studentId = $_SESSION['student_id'];
$bundle = siemsGetStudentProfileBundle($studentId);
$user = $bundle['user'] ?? null;
$profile = $bundle['profile'] ?? null;
$card = $bundle['student_id_card'] ?? null;
$statusHistory = $bundle['status_history'] ?? [];
$academicRecords = $bundle['academic_records'] ?? [];
$latestApplication = $bundle['latest_application'] ?? null;
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-person-vcard text-success me-2"></i>My Student Information</h3>
        <div class="row-text">Unified view of your profile, school ID, academic records, and current status.</div>
    </div>
    <a href="siems_portal.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left me-1"></i> SIEMS Portal
    </a>
</div>

<?php if ($user): ?>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card-outline bg-white p-4 text-center h-100">
                <div class="group-label-grey">PROGRAM</div>
                <div class="val-total-amount fs-3 text-success mt-2"><?php echo htmlspecialchars($user['program'] ?? 'N/A'); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-outline bg-white p-4 text-center h-100">
                <div class="group-label-grey">YEAR LEVEL</div>
                <div class="val-total-amount fs-3" style="color: #0ea5e9;"><?php echo htmlspecialchars((string) ($user['year_level'] ?? 'N/A')); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-outline bg-white p-4 text-center h-100">
                <div class="group-label-grey">STUDENT STATUS</div>
                <div class="val-total-amount fs-5 mt-3"><?php echo htmlspecialchars($user['student_status'] ?? 'N/A'); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-outline bg-white p-4 text-center h-100">
                <div class="group-label-grey">ENROLLMENT</div>
                <div class="val-total-amount fs-5 mt-3"><?php echo htmlspecialchars($user['enrollment_status'] ?? 'N/A'); ?></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card-outline bg-white h-100">
                <div class="balance-header py-3 px-4">
                    <span class="section-title">PROFILE DETAILS</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="group-label-grey">FULL NAME</div>
                            <div class="row-val"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="group-label-grey">STUDENT ID</div>
                            <div class="row-val"><?php echo htmlspecialchars($user['student_id']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="group-label-grey">EMAIL</div>
                            <div class="row-text"><?php echo htmlspecialchars($user['email'] ?? 'Not set'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="group-label-grey">PHONE</div>
                            <div class="row-text"><?php echo htmlspecialchars($user['phone'] ?? 'Not set'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="group-label-grey">BIRTHDATE</div>
                            <div class="row-text"><?php echo htmlspecialchars($profile['birthdate'] ?? 'Not set'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="group-label-grey">SEX</div>
                            <div class="row-text"><?php echo htmlspecialchars($profile['sex'] ?? 'Not set'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="group-label-grey">CIVIL STATUS</div>
                            <div class="row-text"><?php echo htmlspecialchars($profile['civil_status'] ?? 'Not set'); ?></div>
                        </div>
                        <div class="col-md-12">
                            <div class="group-label-grey">ADDRESS</div>
                            <div class="row-text"><?php echo htmlspecialchars($profile['address'] ?? 'Not set'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="group-label-grey">GUARDIAN</div>
                            <div class="row-text"><?php echo htmlspecialchars($profile['guardian_name'] ?? 'Not set'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="group-label-grey">GUARDIAN CONTACT</div>
                            <div class="row-text"><?php echo htmlspecialchars($profile['guardian_contact'] ?? 'Not set'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card-outline bg-white h-100">
                <div class="balance-header py-3 px-4">
                    <span class="section-title">STUDENT ID</span>
                </div>
                <div class="card-body p-4">
                    <?php if ($card): ?>
                        <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border: 1px solid #bbf7d0;">
                            <div class="group-label-grey">CARD NUMBER</div>
                            <div class="row-val mb-3"><?php echo htmlspecialchars($card['card_number']); ?></div>
                            <div class="group-label-grey">QR VALUE</div>
                            <div class="row-text mb-3"><?php echo htmlspecialchars($card['qr_code_value'] ?? 'Not available'); ?></div>
                            <div class="group-label-grey">VALID UNTIL</div>
                            <div class="row-text mb-3"><?php echo htmlspecialchars($card['valid_until'] ?? 'Not set'); ?></div>
                            <div class="group-label-grey">STATUS</div>
                            <div class="row-val"><?php echo htmlspecialchars($card['status']); ?></div>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">No student ID record has been issued yet.</div>
                    <?php endif; ?>

                    <?php if ($latestApplication): ?>
                        <div class="mt-4 p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="group-label-grey">LATEST ENROLLMENT APPLICATION</div>
                            <div class="row-val mt-2"><?php echo htmlspecialchars($latestApplication['academic_year'] . ' / ' . $latestApplication['semester']); ?></div>
                            <div class="row-text"><?php echo htmlspecialchars($latestApplication['status']); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card-outline bg-white h-100">
                <div class="balance-header py-3 px-4">
                    <span class="section-title">STATUS HISTORY</span>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($statusHistory)): ?>
                        <?php foreach ($statusHistory as $history): ?>
                            <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="row-val"><?php echo htmlspecialchars($history['status_value']); ?></div>
                                    <div class="group-label-grey"><?php echo date('M j, Y', strtotime($history['effective_date'])); ?></div>
                                </div>
                                <div class="row-text mt-2"><?php echo htmlspecialchars($history['remarks'] ?? 'No remarks'); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted">No status history available yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card-outline bg-white h-100">
                <div class="balance-header py-3 px-4">
                    <span class="section-title">ACADEMIC RECORDS</span>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($academicRecords)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="group-label-grey py-3">SUBJECT</th>
                                        <th class="group-label-grey py-3">UNITS</th>
                                        <th class="group-label-grey py-3">TERM</th>
                                        <th class="group-label-grey py-3">GRADE</th>
                                        <th class="group-label-grey py-3">REMARKS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($academicRecords as $record): ?>
                                        <tr>
                                            <td class="py-3">
                                                <div class="row-val"><?php echo htmlspecialchars($record['code']); ?></div>
                                                <div class="row-text"><?php echo htmlspecialchars($record['description']); ?></div>
                                            </td>
                                            <td class="py-3 row-text"><?php echo htmlspecialchars((string) $record['units']); ?></td>
                                            <td class="py-3 row-text"><?php echo htmlspecialchars($record['academic_year'] . ' / ' . $record['semester']); ?></td>
                                            <td class="py-3 row-val"><?php echo htmlspecialchars((string) ($record['final_grade'] ?? '-')); ?></td>
                                            <td class="py-3 row-text"><?php echo htmlspecialchars($record['remarks'] ?? '-'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">No academic records found in the unified database yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card-outline bg-white p-5 text-center">
        <i class="bi bi-exclamation-triangle fs-1 text-warning d-block mb-3"></i>
        <div class="row-text">Your student information record is not available in the active database.</div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
