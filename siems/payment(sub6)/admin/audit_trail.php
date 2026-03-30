<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireSubsystemAccess('user_management');

$page_title = 'Audit Trail';

// Filter logs
$where = '1=1';
$params = [];
if (!empty($_GET['user_id'])) {
    $where .= ' AND al.user_id = ?';
    $params[] = $_GET['user_id'];
}
if (!empty($_GET['student_id'])) {
    $where .= ' AND al.student_id = ?';
    $params[] = $_GET['student_id'];
}

$stmt = $pdo->prepare("
    SELECT al.*, u.full_name as user_name 
    FROM audit_log al 
    LEFT JOIN users u ON al.user_id = u.id 
    WHERE $where 
    ORDER BY created_at DESC 
    LIMIT 1000
");
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title">
        <i class="bi bi-shield-check text-success me-2"></i>
        Audit Trail (<?php echo count($logs); ?> records)
    </h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<div class="card-outline bg-white mb-5">
    <div class="balance-header py-4 px-4 bg-light" style="border-radius: 12px 12px 0 0;">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="group-label-grey mb-2">STUDENT ID</label>
                <input type="text" name="student_id" class="form-control row-text" placeholder="e.g. STU-1234" value="<?php echo $_GET['student_id'] ?? ''; ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
            </div>
            <div class="col-md-3">
                <label class="group-label-grey mb-2">ADMIN ID</label>
                <input type="text" name="user_id" class="form-control row-text" placeholder="Admin ID" value="<?php echo $_GET['user_id'] ?? ''; ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
            </div>
            <div class="col-md-3">
                <button class="btn btn-success fw-bold px-4 pt-2 pb-2" style="border-radius: 8px;" type="submit">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size: 0.9rem;">
            <thead style="background-color: #f8fafc;">
                <tr>
                    <th class="group-label-grey py-3 px-4">DATE & TIME</th>
                    <th class="group-label-grey py-3">USER</th>
                    <th class="group-label-grey py-3">STUDENT</th>
                    <th class="group-label-grey py-3">ACTION</th>
                    <th class="group-label-grey py-3">IP ADDRESS</th>
                    <th class="group-label-grey py-3 px-4">DEVICE</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="py-3 px-4 row-text text-muted"><?php echo date('M j, Y H:i', strtotime($log['created_at'])); ?></td>
                        <td class="py-3 row-val"><?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></td>
                        <td class="py-3 row-text"><?php echo htmlspecialchars($log['student_id'] ?? '-'); ?></td>
                        <td class="py-3">
                            <span class="badge" style="background-color: #f1f8f4; color: #16a34a; border: 1px solid #bbf7d0;"><?php echo htmlspecialchars($log['action']); ?></span>
                            <?php if ($log['old_value']): ?>
                                <br><small class="text-muted d-block mt-1">Old: <?php echo htmlspecialchars($log['old_value']); ?></small>
                            <?php endif; ?>
                            <?php if ($log['new_value']): ?>
                                <br><small class="text-success d-block">New: <?php echo htmlspecialchars($log['new_value']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 row-text"><code style="color: #64748b; background-color: #f1f5f9; padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($log['ip_address']); ?></code></td>
                        <td class="py-3 px-4 small text-muted" title="<?php echo htmlspecialchars($log['user_agent']); ?>"><?php echo htmlspecialchars(substr($log['user_agent'], 0, 50)) . '...'; ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                            No audit records found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

