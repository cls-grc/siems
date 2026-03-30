<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireSubsystemAccess('payment_accounting');

$page_title = 'Scholarship Management';

$discounts = [
    'None' => 0,
    'Academic 50%' => 50,
    'QC Foundation 100%' => 100,
    'Sibling' => 25,
    'Valedictorian' => 100,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate_scholarship'], $_POST['student_id'], $_POST['decision'])) {
    $student_id = trim($_POST['student_id']);
    $decision = $_POST['decision'] === 'approve' ? 'Approved' : 'Rejected';

    $stmt = $pdo->prepare("
        UPDATE scholarships
        SET status = ?, validated_by = ?, validated_at = NOW()
        WHERE student_id = ? AND status = 'Pending'
    ");
    $stmt->execute([$decision, $_SESSION['user_id'], $student_id]);

    if ($stmt->rowCount() > 0) {
        logAudit(
            $_SESSION['user_id'],
            $student_id,
            $decision === 'Approved' ? 'Approved Scholarship' : 'Rejected Scholarship',
            null,
            'Scholarship validation updated via admin module'
        );

        if ($decision === 'Approved') {
            calculateAssessment($student_id);
        }

        $_SESSION['message'] = $decision === 'Approved'
            ? 'Scholarship approved. Assessment recalculated.'
            : 'Scholarship rejected.';
        $_SESSION['msg_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Scholarship validation could not be updated.';
        $_SESSION['msg_type'] = 'danger';
    }

    header('Location: scholarships.php');
    exit;
}

$pending_scholarships = $pdo->query("
    SELECT s.*, u.full_name, u.program, u.year_level, validator.full_name AS validated_by_name
    FROM scholarships s
    JOIN users u ON s.student_id = u.student_id
    LEFT JOIN users validator ON s.validated_by = validator.id
    WHERE u.role = 'student' AND s.status = 'Pending'
    ORDER BY s.assigned_at DESC
")->fetchAll();

$approved_scholarships = $pdo->query("
    SELECT s.*, u.full_name, u.program, u.year_level, validator.full_name AS validated_by_name
    FROM scholarships s
    JOIN users u ON s.student_id = u.student_id
    LEFT JOIN users validator ON s.validated_by = validator.id
    WHERE u.role = 'student' AND s.status = 'Approved'
    ORDER BY s.assigned_at DESC
")->fetchAll();

$rejected_scholarships = $pdo->query("
    SELECT s.*, u.full_name, u.program, u.year_level, validator.full_name AS validated_by_name
    FROM scholarships s
    JOIN users u ON s.student_id = u.student_id
    LEFT JOIN users validator ON s.validated_by = validator.id
    WHERE u.role = 'student' AND s.status = 'Rejected'
    ORDER BY s.validated_at DESC, s.assigned_at DESC
")->fetchAll();
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title">
        <i class="bi bi-award text-success me-2"></i>
        Scholarship Validation
    </h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-1 mb-1" style="color: #f59e0b;"><?php echo count($pending_scholarships); ?></div>
            <div class="group-label-grey">PENDING VALIDATION</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-1 mb-1" style="color: #16a34a;"><?php echo count($approved_scholarships); ?></div>
            <div class="group-label-grey">APPROVED</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-outline bg-white p-4 text-center h-100">
            <div class="val-total-amount fs-1 mb-1" style="color: #dc2626;"><?php echo count($rejected_scholarships); ?></div>
            <div class="group-label-grey">REJECTED</div>
        </div>
    </div>
</div>

<div class="card-outline bg-white mb-5">
    <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="section-title">PENDING SCHOLARSHIPS</span>
        <span class="badge rounded-pill px-3 py-2" style="background-color: #fef3c7; color: #b45309;"><?php echo count($pending_scholarships); ?> Pending</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size: 0.9rem;">
            <thead style="background-color: #f8fafc;">
                <tr>
                    <th class="group-label-grey py-3 px-4">STUDENT</th>
                    <th class="group-label-grey py-3">TYPE</th>
                    <th class="group-label-grey py-3 text-center">GPA</th>
                    <th class="group-label-grey py-3 text-center">STACKABLE</th>
                    <th class="group-label-grey py-3 text-center">DISCOUNT</th>
                    <th class="group-label-grey py-3 text-center">SUBMITTED</th>
                    <th class="group-label-grey py-3 px-4 text-center">VALIDATION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_scholarships as $sch): ?>
                    <tr>
                        <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($sch['student_id'] . ' - ' . $sch['full_name']); ?></td>
                        <td class="py-3"><span class="badge" style="background-color: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;"><?php echo htmlspecialchars($sch['discount_type']); ?></span></td>
                        <td class="py-3 row-text text-center"><?php echo $sch['gpa'] ?: '-'; ?></td>
                        <td class="py-3 text-center"><?php echo $sch['stackable'] ? '<i class="bi bi-check-lg fs-5 text-success"></i>' : '<i class="bi bi-x-lg text-danger"></i>'; ?></td>
                        <td class="py-3 row-val text-center text-success"><?php echo '+' . ($discounts[$sch['discount_type']] ?? 0) . '%'; ?></td>
                        <td class="py-3 row-text text-center"><?php echo date('M j, Y g:i A', strtotime($sch['assigned_at'])); ?></td>
                        <td class="py-3 px-4 text-center">
                            <form method="POST" class="d-inline-flex gap-2">
                                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($sch['student_id']); ?>">
                                <input type="hidden" name="decision" value="approve">
                                <button type="submit" name="validate_scholarship" value="1" class="btn btn-sm btn-success fw-bold" style="border-radius: 6px;">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                            </form>
                            <form method="POST" class="d-inline-flex">
                                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($sch['student_id']); ?>">
                                <input type="hidden" name="decision" value="reject">
                                <button type="submit" name="validate_scholarship" value="1" class="btn btn-sm btn-outline-danger fw-bold" style="border-radius: 6px;" onclick="return confirm('Reject this scholarship request?');">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($pending_scholarships)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-patch-check fs-1 d-block mb-3 opacity-50"></i>
                            No pending scholarship requests
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card-outline bg-white mb-5">
    <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="section-title">APPROVED SCHOLARSHIPS</span>
        <span class="badge bg-success rounded-pill px-3 py-2"><?php echo count($approved_scholarships); ?> Approved</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size: 0.9rem;">
            <thead style="background-color: #f8fafc;">
                <tr>
                    <th class="group-label-grey py-3 px-4">STUDENT</th>
                    <th class="group-label-grey py-3">TYPE</th>
                    <th class="group-label-grey py-3 text-center">GPA</th>
                    <th class="group-label-grey py-3 text-center">STACKABLE</th>
                    <th class="group-label-grey py-3 text-center">DISCOUNT</th>
                    <th class="group-label-grey py-3 text-center">APPROVED BY</th>
                    <th class="group-label-grey py-3 px-4 text-center">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($approved_scholarships as $sch): ?>
                    <tr>
                        <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($sch['student_id'] . ' - ' . $sch['full_name']); ?></td>
                        <td class="py-3"><span class="badge" style="background-color: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;"><?php echo htmlspecialchars($sch['discount_type']); ?></span></td>
                        <td class="py-3 row-text text-center"><?php echo $sch['gpa'] ?: '-'; ?></td>
                        <td class="py-3 text-center"><?php echo $sch['stackable'] ? '<i class="bi bi-check-lg fs-5 text-success"></i>' : '<i class="bi bi-x-lg text-danger"></i>'; ?></td>
                        <td class="py-3 row-val text-center text-success"><?php echo '+' . ($discounts[$sch['discount_type']] ?? 0) . '%'; ?></td>
                        <td class="py-3 row-text text-center">
                            <?php echo htmlspecialchars($sch['validated_by_name'] ?: 'System'); ?>
                            <div class="small text-muted"><?php echo $sch['validated_at'] ? date('M j, Y g:i A', strtotime($sch['validated_at'])) : '-'; ?></div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="badge px-3 py-2" style="background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0;">APPROVED</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($approved_scholarships)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-award fs-1 d-block mb-3 opacity-50"></i>
                            No approved scholarships found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card-outline bg-white mb-5">
    <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="section-title">REJECTED SCHOLARSHIPS</span>
        <span class="badge rounded-pill px-3 py-2" style="background-color: #fee2e2; color: #b91c1c;"><?php echo count($rejected_scholarships); ?> Rejected</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size: 0.9rem;">
            <thead style="background-color: #f8fafc;">
                <tr>
                    <th class="group-label-grey py-3 px-4">STUDENT</th>
                    <th class="group-label-grey py-3">TYPE</th>
                    <th class="group-label-grey py-3 text-center">GPA</th>
                    <th class="group-label-grey py-3 text-center">STACKABLE</th>
                    <th class="group-label-grey py-3 text-center">VALIDATED BY</th>
                    <th class="group-label-grey py-3 px-4 text-center">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rejected_scholarships as $sch): ?>
                    <tr>
                        <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($sch['student_id'] . ' - ' . $sch['full_name']); ?></td>
                        <td class="py-3"><span class="badge" style="background-color: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;"><?php echo htmlspecialchars($sch['discount_type']); ?></span></td>
                        <td class="py-3 row-text text-center"><?php echo $sch['gpa'] ?: '-'; ?></td>
                        <td class="py-3 text-center"><?php echo $sch['stackable'] ? '<i class="bi bi-check-lg fs-5 text-success"></i>' : '<i class="bi bi-x-lg text-danger"></i>'; ?></td>
                        <td class="py-3 row-text text-center">
                            <?php echo htmlspecialchars($sch['validated_by_name'] ?: 'System'); ?>
                            <div class="small text-muted"><?php echo $sch['validated_at'] ? date('M j, Y g:i A', strtotime($sch['validated_at'])) : '-'; ?></div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="badge px-3 py-2" style="background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">REJECTED</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rejected_scholarships)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-x-circle fs-1 d-block mb-3 opacity-50"></i>
                            No rejected scholarships found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
