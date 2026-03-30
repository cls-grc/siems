<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$items = getStoreItems(true);
$orders = getStudentItemOrders($student_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_item'])) {
    $item_id = (int)($_POST['item_id'] ?? 0);
    $size_option = trim($_POST['size_option'] ?? '');
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    $payment_method = $_POST['payment_method'] ?? 'GCash';

    $selected_item = null;
    foreach ($items as $item) {
        if ((int)$item['id'] === $item_id) {
            $selected_item = $item;
            break;
        }
    }

    if (!$selected_item) {
        $_SESSION['message'] = 'Selected item was not found.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: store.php');
        exit;
    }

    if (!empty($selected_item['size_options']) && !in_array($size_option, $selected_item['size_options'], true)) {
        $_SESSION['message'] = 'Please select a valid size for this item.';
        $_SESSION['msg_type'] = 'warning';
        header('Location: store.php');
        exit;
    }

    try {
        $pdo->beginTransaction();
        $result = createStoreOrder($student_id, $selected_item, $size_option, $quantity, 'Online', $payment_method, 'Pending');
        $pdo->commit();

        $_SESSION['message'] = 'Order submitted. Receipt ' . htmlspecialchars($result['receipt_no']) . ' is waiting for payment validation.';
        $_SESSION['msg_type'] = 'success';
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['message'] = 'Failed to submit item order.';
        $_SESSION['msg_type'] = 'danger';
    }

    header('Location: store.php');
    exit;
}

$page_title = 'Uniforms and Books';
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title"><i class="bi bi-bag-check text-success me-2"></i>Uniforms and Books</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4">
        <span class="section-title">STUDENT STORE</span>
    </div>
    <div class="card-body p-4">
        <div class="p-3 rounded-3 mb-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
            <div class="row-text">These items are separate from your tuition balance. Online orders stay pending until cashier validation is complete.</div>
        </div>

        <div class="row g-4">
            <?php foreach ($items as $item): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card-outline bg-white h-100 p-4">
                        <?php if (!empty($item['image_path'])): ?>
                            <div class="mb-3">
                                <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" class="w-100 rounded-3 border" style="height: 220px; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div class="mb-3 rounded-3 border d-flex align-items-center justify-content-center text-muted" style="height: 220px; background-color: #f8fafc;">
                                <div class="text-center">
                                    <i class="bi bi-image fs-1 d-block mb-2"></i>
                                    No image
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="row-val fs-5"><?php echo htmlspecialchars($item['item_name']); ?></div>
                                <div class="group-label-grey"><?php echo htmlspecialchars($item['category']); ?></div>
                            </div>
                            <span class="badge" style="background-color: #ecfeff; color: #0f766e; border: 1px solid #a5f3fc;">
                                <?php echo htmlspecialchars($item['size_group']); ?>
                            </span>
                        </div>
                        <div class="val-total-amount fs-4 mb-3">&#8369; <?php echo number_format($item['price'], 2); ?></div>
                        <?php if (!empty($item['description'])): ?>
                            <div class="row-text mb-3"><?php echo htmlspecialchars($item['description']); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="item_id" value="<?php echo (int)$item['id']; ?>">
                            <?php if (!empty($item['size_options'])): ?>
                                <div class="mb-3">
                                    <label class="form-label row-val mb-2">Size</label>
                                    <select class="form-select row-text" name="size_option" required style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                        <option value="">Select size</option>
                                        <?php foreach ($item['size_options'] as $size): ?>
                                            <option value="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($size); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="row g-3">
                                <div class="col-4">
                                    <label class="form-label row-val mb-2">Qty</label>
                                    <input type="number" name="quantity" min="1" value="1" class="form-control row-text" required style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                </div>
                                <div class="col-8">
                                    <label class="form-label row-val mb-2">Online Method</label>
                                    <select class="form-select row-text" name="payment_method" required style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                        <option value="GCash">GCash</option>
                                        <option value="Maya">Maya</option>
                                        <option value="Online Banking">Online Banking</option>
                                        <option value="Credit/Debit Card">Credit/Debit Card</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" name="buy_item" class="btn btn-success w-100 mt-4 fw-bold" style="border-radius: 8px;">
                                <i class="bi bi-cart-plus me-2"></i>Buy Online
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <div class="col-12">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-bag-x fs-1 d-block mb-3"></i>
                        No store items available right now.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card-outline bg-white mb-5">
    <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="section-title">MY ITEM ORDERS</span>
        <span class="badge bg-success rounded-pill px-3"><?php echo count($orders); ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size: 0.92rem;">
            <thead style="background-color: #f8fafc;">
                <tr>
                    <th class="group-label-grey py-3 px-4">DATE</th>
                    <th class="group-label-grey py-3">ITEM</th>
                    <th class="group-label-grey py-3">DETAILS</th>
                    <th class="group-label-grey py-3">PAYMENT</th>
                    <th class="group-label-grey py-3">STATUS</th>
                    <th class="group-label-grey py-3 px-4 text-center">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="py-3 px-4 row-text"><?php echo date('M j, Y', strtotime($order['ordered_at'])); ?></td>
                        <td class="py-3">
                            <div class="row-val"><?php echo htmlspecialchars($order['item_name']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($order['category']); ?></div>
                        </td>
                        <td class="py-3 row-text">
                            Qty: <?php echo (int)$order['quantity']; ?><br>
                            <?php echo !empty($order['size_option']) ? 'Size: ' . htmlspecialchars($order['size_option']) . '<br>' : ''; ?>
                            Total: &#8369; <?php echo number_format($order['total_amount'], 2); ?>
                        </td>
                        <td class="py-3 row-text">
                            <?php echo htmlspecialchars($order['payment_mode']); ?><br>
                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($order['payment_method'] ?? 'Pending'); ?></span>
                        </td>
                        <td class="py-3">
                            <?php
                            $status_styles = [
                                'Pending Payment' => 'background-color:#fef3c7;color:#b45309;border:1px solid #fde68a;',
                                'Payment Rejected' => 'background-color:#fee2e2;color:#b91c1c;border:1px solid #fecaca;',
                                'Paid' => 'background-color:#dcfce7;color:#166534;border:1px solid #bbf7d0;',
                                'Claimed' => 'background-color:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;',
                            ];
                            ?>
                            <span class="badge px-3 py-2" style="<?php echo $status_styles[$order['order_status']] ?? $status_styles['Pending Payment']; ?>">
                                <?php echo htmlspecialchars(strtoupper($order['order_status'])); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <?php if ($order['payment_mode'] === 'Online' && ($order['verification_status'] ?? '') === 'Pending'): ?>
                                <a href="validate_payment.php" class="btn btn-sm btn-outline-warning fw-bold" style="border-radius: 6px;">
                                    <i class="bi bi-upload"></i> Validate
                                </a>
                            <?php elseif ($order['order_status'] === 'Paid'): ?>
                                <a href="claim_slips.php" class="btn btn-sm btn-outline-success fw-bold" style="border-radius: 6px;">
                                    <i class="bi bi-ticket-perforated"></i> View Slip
                                </a>
                            <?php elseif ($order['order_status'] === 'Claimed'): ?>
                                <span class="text-primary fw-bold small">Already claimed</span>
                            <?php else: ?>
                                <span class="text-muted small">Waiting</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-bag fs-1 d-block mb-3"></i>
                            No item orders yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
