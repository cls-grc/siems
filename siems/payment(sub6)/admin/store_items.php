<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireSubsystemAccess('payment_accounting');

$page_title = 'Store Items';
$edit_item = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = trim($_POST['item_name'] ?? '');
    $category = $_POST['category'] ?? 'Uniform';
    $size_group = $_POST['size_group'] ?? 'Clothing';
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $active = isset($_POST['active']) ? 1 : 0;
    $image_path = $_POST['existing_image_path'] ?? null;

    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/store_items/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $tmp_name = $_FILES['item_image']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $_SESSION['message'] = 'Item image must be JPG, PNG, or WEBP.';
            $_SESSION['msg_type'] = 'danger';
            header('Location: store_items.php' . (!empty($_POST['item_id']) ? '?edit=' . (int)$_POST['item_id'] : ''));
            exit;
        }

        $filename = uniqid('store_item_') . '.' . $ext;
        $destination = $upload_dir . $filename;
        if (!move_uploaded_file($tmp_name, $destination)) {
            $_SESSION['message'] = 'Failed to upload item image.';
            $_SESSION['msg_type'] = 'danger';
            header('Location: store_items.php' . (!empty($_POST['item_id']) ? '?edit=' . (int)$_POST['item_id'] : ''));
            exit;
        }

        $image_path = 'assets/uploads/store_items/' . $filename;
    }

    if (isset($_POST['save_item']) && $item_name !== '' && $price > 0) {
        if (!empty($_POST['item_id'])) {
            $stmt = $pdo->prepare("UPDATE store_items SET item_name = ?, category = ?, size_group = ?, description = ?, price = ?, image_path = ?, active = ? WHERE id = ?");
            $stmt->execute([$item_name, $category, $size_group, $description, $price, $image_path, $active, (int)$_POST['item_id']]);
            $_SESSION['message'] = 'Store item updated.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO store_items (item_name, category, size_group, description, price, image_path, active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$item_name, $category, $size_group, $description, $price, $image_path, $active]);
            $_SESSION['message'] = 'Store item added.';
        }
        $_SESSION['msg_type'] = 'success';
        header('Location: store_items.php');
        exit;
    }
}

if (isset($_GET['toggle'])) {
    $item_id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE store_items SET active = 1 - active WHERE id = ?")->execute([$item_id]);
    $_SESSION['message'] = 'Item availability updated.';
    $_SESSION['msg_type'] = 'success';
    header('Location: store_items.php');
    exit;
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM store_items WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_item = $stmt->fetch();
}

$items = getStoreItems(false);
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title"><i class="bi bi-bag text-success me-2"></i>Store Items</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4">
                <span class="section-title"><?php echo $edit_item ? 'EDIT ITEM' : 'ADD ITEM'; ?></span>
            </div>
            <div class="card-body p-4">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="item_id" value="<?php echo (int)($edit_item['id'] ?? 0); ?>">
                    <input type="hidden" name="existing_image_path" value="<?php echo htmlspecialchars($edit_item['image_path'] ?? ''); ?>">
                    <div class="mb-3">
                        <label class="form-label row-val mb-2">Item Name</label>
                        <input type="text" name="item_name" class="form-control row-text" required value="<?php echo htmlspecialchars($edit_item['item_name'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label row-val mb-2">Category</label>
                        <select name="category" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            <?php foreach (['Uniform', 'PE Uniform', 'NSTP Uniform', 'Books'] as $category): ?>
                                <option value="<?php echo $category; ?>" <?php echo (($edit_item['category'] ?? '') === $category) ? 'selected' : ''; ?>><?php echo $category; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label row-val mb-2">Size Group</label>
                        <select name="size_group" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            <?php foreach (['Clothing', 'Book', 'One Size'] as $size_group): ?>
                                <option value="<?php echo $size_group; ?>" <?php echo (($edit_item['size_group'] ?? 'Clothing') === $size_group) ? 'selected' : ''; ?>><?php echo $size_group; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label row-val mb-2">Price</label>
                        <input type="number" step="0.01" min="1" name="price" class="form-control row-text" required value="<?php echo htmlspecialchars($edit_item['price'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label row-val mb-2">Description</label>
                        <textarea name="description" rows="3" class="form-control row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;"><?php echo htmlspecialchars($edit_item['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label row-val mb-2">Item Image</label>
                        <input type="file" name="item_image" class="form-control row-text" accept=".jpg,.jpeg,.png,.webp,image/*" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                        <div class="form-text group-label-grey mt-2">Recommended: square image, JPG/PNG/WEBP.</div>
                    </div>
                    <?php if (!empty($edit_item['image_path'])): ?>
                        <div class="mb-3">
                            <img src="../<?php echo htmlspecialchars($edit_item['image_path']); ?>" alt="<?php echo htmlspecialchars($edit_item['item_name']); ?>" class="img-fluid rounded-3 border" style="max-height: 220px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="active" id="active" <?php echo !isset($edit_item['active']) || $edit_item['active'] ? 'checked' : ''; ?>>
                        <label class="form-check-label row-text" for="active">Available for ordering</label>
                    </div>
                    <button type="submit" name="save_item" class="btn btn-success w-100 fw-bold" style="border-radius: 8px;">
                        <i class="bi bi-save me-1"></i> Save Item
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="section-title">AVAILABLE CATALOG</span>
                <span class="badge bg-success rounded-pill px-3"><?php echo count($items); ?> Items</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 0.92rem;">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th class="group-label-grey py-3 px-4">ITEM</th>
                            <th class="group-label-grey py-3">IMAGE</th>
                            <th class="group-label-grey py-3">CATEGORY</th>
                            <th class="group-label-grey py-3">SIZE</th>
                            <th class="group-label-grey py-3">PRICE</th>
                            <th class="group-label-grey py-3">STATUS</th>
                            <th class="group-label-grey py-3 px-4 text-end">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="row-val"><?php echo htmlspecialchars($item['item_name']); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($item['description']); ?></div>
                                </td>
                                <td class="py-3">
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" class="rounded-3 border" style="width: 72px; height: 72px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted small">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($item['category']); ?></td>
                                <td class="py-3 row-text"><?php echo htmlspecialchars($item['size_group']); ?></td>
                                <td class="py-3 row-val text-success">&#8369; <?php echo number_format($item['price'], 2); ?></td>
                                <td class="py-3">
                                    <span class="badge px-3 py-2" style="<?php echo $item['active'] ? 'background-color:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background-color:#fee2e2;color:#b91c1c;border:1px solid #fecaca;'; ?>">
                                        <?php echo $item['active'] ? 'Active' : 'Hidden'; ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <a href="?edit=<?php echo (int)$item['id']; ?>" class="btn btn-sm btn-outline-success fw-bold me-1" style="border-radius: 6px;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?toggle=<?php echo (int)$item['id']; ?>" class="btn btn-sm btn-outline-secondary fw-bold" style="border-radius: 6px;">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">No store items configured.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
