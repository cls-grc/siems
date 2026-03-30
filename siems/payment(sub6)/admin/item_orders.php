<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireSubsystemAccess('payment_accounting');

$page_title = 'Item Orders';
$items = getStoreItems(true);
$selected_student = null;
$search_results = [];
$error_msg = '';

if (isset($_GET['claim'])) {
    $order_id = (int)$_GET['claim'];
    $stmt = $pdo->prepare("UPDATE item_orders SET order_status = 'Claimed', claimed_at = NOW() WHERE id = ? AND order_status = 'Paid'");
    $stmt->execute([$order_id]);
    $_SESSION['message'] = 'Order marked as claimed.';
    $_SESSION['msg_type'] = 'success';
    header('Location: item_orders.php');
    exit;
}

if (!empty($_GET['student_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ? AND role = 'student'");
    $stmt->execute([$_GET['student_id']]);
    $selected_student = $stmt->fetch();
}

if (isset($_POST['search_student'])) {
    $search_term = trim($_POST['search'] ?? '');
    if ($search_term !== '') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'student' AND (student_id LIKE ? OR full_name LIKE ?) ORDER BY full_name ASC LIMIT 10");
        $like = '%' . $search_term . '%';
        $stmt->execute([$like, $like]);
        $search_results = $stmt->fetchAll();
    }
}

if (isset($_POST['select_student'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ? AND role = 'student'");
    $stmt->execute([$_POST['select_student']]);
    $selected_student = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_cashier_sale'])) {
    $student_id = $_POST['student_id'] ?? '';
    $item_id = (int)($_POST['item_id'] ?? 0);
    $size_option = trim($_POST['size_option'] ?? '');
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ? AND role = 'student'");
    $stmt->execute([$student_id]);
    $selected_student = $stmt->fetch();

    $selected_item = null;
    foreach ($items as $item) {
        if ((int)$item['id'] === $item_id) {
            $selected_item = $item;
            break;
        }
    }

    if (!$selected_student || !$selected_item) {
        $error_msg = 'Please choose a valid student and item.';
    } elseif (!empty($selected_item['size_options']) && !in_array($size_option, $selected_item['size_options'], true)) {
        $error_msg = 'Please select a valid size.';
    } else {
        try {
            $pdo->beginTransaction();
            createStoreOrder($student_id, $selected_item, $size_option, $quantity, 'Cashier', 'Cash', 'Verified');
            $pdo->commit();

            $_SESSION['message'] = 'Cashier sale recorded. Claim slip is ready for release.';
            $_SESSION['msg_type'] = 'success';
            header('Location: item_orders.php?student_id=' . urlencode($student_id));
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error_msg = 'Failed to record cashier sale.';
        }
    }
}

$orders = getAllItemOrders();
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title"><i class="bi bi-box-seam text-success me-2"></i>Item Orders</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<?php if ($error_msg): ?>
    <div class="alert alert-warning"><?php echo htmlspecialchars($error_msg); ?></div>
<?php endif; ?>

<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4">
        <span class="section-title">CASHIER STORE SALE</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" class="row g-3 mb-4">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control form-control-lg row-text" placeholder="Search student by ID or name" style="border: 2px solid #e2e8f0; border-radius: 8px;">
            </div>
            <div class="col-md-2">
                <button type="submit" name="search_student" class="btn btn-success w-100 h-100 fw-bold" style="border-radius: 8px;">Search</button>
            </div>
        </form>

        <?php if (!empty($search_results)): ?>
            <div class="table-responsive mb-4">
                <table class="table table-hover mb-0">
                    <tbody>
                        <?php foreach ($search_results as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['program']); ?></td>
                                <td class="text-end">
                                    <form method="POST">
                                        <input type="hidden" name="select_student" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success fw-bold">Select</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($selected_student): ?>
            <div class="p-3 rounded-3 mb-4" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                <div class="row-val"><?php echo htmlspecialchars($selected_student['full_name']); ?></div>
                <div class="row-text"><?php echo htmlspecialchars($selected_student['student_id']); ?> | <?php echo htmlspecialchars($selected_student['program']); ?></div>
            </div>
            <form method="POST">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($selected_student['student_id']); ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label row-val mb-2">Item</label>
                        <select name="item_id" class="form-select row-text" required style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            <option value="">Select item</option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?php echo (int)$item['id']; ?>"><?php echo htmlspecialchars($item['item_name']); ?> - &#8369;<?php echo number_format($item['price'], 2); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label row-val mb-2">Size</label>
                        <select name="size_option" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            <option value="">Not applicable</option>
                            <?php foreach (getStoreSizeOptions('Clothing') as $size): ?>
                                <option value="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($size); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label row-val mb-2">Qty</label>
                        <input type="number" name="quantity" min="1" value="1" class="form-control row-text" required style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" name="record_cashier_sale" class="btn btn-success w-100 fw-bold" style="border-radius: 8px;">
                            <i class="bi bi-cash-stack me-1"></i> Record Cash Sale
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="card-outline bg-white mb-5">
    <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="section-title">ITEM ORDER LOG</span>
        <span class="badge bg-success rounded-pill px-3"><?php echo count($orders); ?> Orders</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size: 0.92rem;">
            <thead style="background-color: #f8fafc;">
                <tr>
                    <th class="group-label-grey py-3 px-4">DATE</th>
                    <th class="group-label-grey py-3">STUDENT</th>
                    <th class="group-label-grey py-3">ITEM</th>
                    <th class="group-label-grey py-3">TOTAL</th>
                    <th class="group-label-grey py-3">STATUS</th>
                    <th class="group-label-grey py-3 px-4 text-end">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="py-3 px-4 row-text"><?php echo date('M j, Y h:i A', strtotime($order['ordered_at'])); ?></td>
                        <td class="py-3">
                            <div class="row-val"><?php echo htmlspecialchars($order['full_name']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($order['student_id']); ?> - <?php echo htmlspecialchars($order['program']); ?></div>
                        </td>
                        <td class="py-3 row-text">
                            <?php echo htmlspecialchars($order['item_name']); ?><br>
                            <span class="text-muted small">Qty <?php echo (int)$order['quantity']; ?><?php echo !empty($order['size_option']) ? ' | ' . htmlspecialchars($order['size_option']) : ''; ?></span>
                        </td>
                        <td class="py-3 row-val text-success">&#8369; <?php echo number_format($order['total_amount'], 2); ?></td>
                        <td class="py-3">
                            <span class="badge px-3 py-2" style="<?php echo $order['order_status'] === 'Paid' ? 'background-color:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : ($order['order_status'] === 'Claimed' ? 'background-color:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;' : ($order['order_status'] === 'Payment Rejected' ? 'background-color:#fee2e2;color:#b91c1c;border:1px solid #fecaca;' : 'background-color:#fef3c7;color:#b45309;border:1px solid #fde68a;')); ?>">
                                <?php echo htmlspecialchars($order['order_status']); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-end">
                            <?php if ($order['order_status'] === 'Paid'): ?>
                                <a href="?claim=<?php echo (int)$order['id']; ?>" class="btn btn-sm btn-outline-primary fw-bold" style="border-radius: 6px;" onclick="return confirm('Mark this order as claimed?');">
                                    <i class="bi bi-check2-square"></i> Claimed
                                </a>
                            <?php else: ?>
                                <span class="text-muted small"><?php echo htmlspecialchars($order['claim_code']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">No item orders recorded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
