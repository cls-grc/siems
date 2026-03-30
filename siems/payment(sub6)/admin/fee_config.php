<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireSubsystemAccess('payment_accounting');

$page_title = 'Fee Configuration';

// Auto-migrate schema if needed (runs once)
try {
    $pdo->exec("ALTER TABLE fee_configs ADD COLUMN IF NOT EXISTS unit_count INT NULL DEFAULT NULL AFTER type");
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate') === false) {
        error_log("Schema migration warning: " . $e->getMessage());
    }
}

$program_units = $pdo->query("
    SELECT program, COALESCE(SUM(units), 0) AS total_units
    FROM subjects
    GROUP BY program
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Handle form actions
$message = '';
if ($_POST) {
    if (isset($_POST['add_fee'])) {
        $program = $_POST['program'];
        $type = $_POST['type'];
        $unit_count = null;
        if ($type === 'Tuition') {
            $unit_count = intval($program_units[$program] ?? 0);
        }

        if ($type === 'Tuition') {
            $stmt = $pdo->prepare("INSERT INTO fee_configs (fee_name, amount, program, type, unit_count, active) VALUES (?, ?, ?, ?, ?, 1)");
            $success = $stmt->execute([
                $_POST['fee_name'],
                $_POST['amount'],
                $program,
                $type,
                $unit_count
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO fee_configs (fee_name, amount, program, type, active) VALUES (?, ?, ?, ?, 1)");
            $success = $stmt->execute([
                $_POST['fee_name'],
                $_POST['amount'],
                $program,
                $type
            ]);
        }
        if ($success) {
            $message = 'Fee added successfully!';
            // Recalculate all assessments
            $stmt = $pdo->query("SELECT student_id FROM users WHERE role = 'student'");
            foreach ($stmt->fetchAll() as $student) {
                calculateAssessment($student['student_id']);
            }
        }
    } elseif (isset($_POST['edit_fee'])) {
        $program = $_POST['program'];
        $type = $_POST['type'];
        $unit_count = null;
        if ($type === 'Tuition') {
            $unit_count = intval($program_units[$program] ?? 0);
        }

        if ($type === 'Tuition') {
            $stmt = $pdo->prepare("UPDATE fee_configs SET fee_name = ?, amount = ?, program = ?, type = ?, unit_count = ?, active = ? WHERE id = ?");
            $success = $stmt->execute([
                $_POST['fee_name'],
                $_POST['amount'],
                $program,
                $type,
                $unit_count,
                isset($_POST['active']) ? 1 : 0,
                $_POST['id']
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE fee_configs SET fee_name = ?, amount = ?, program = ?, type = ?, active = ? WHERE id = ?");
            $success = $stmt->execute([
                $_POST['fee_name'],
                $_POST['amount'],
                $program,
                $type,
                isset($_POST['active']) ? 1 : 0,
                $_POST['id']
            ]);
        }
        if ($success) {
            $message = 'Fee updated! All student assessments recalculated.';
        }
    } elseif (isset($_POST['delete_fee'])) {
        $stmt = $pdo->prepare("DELETE FROM fee_configs WHERE id = ?");
        $success = $stmt->execute([$_POST['id']]);
        if ($success) {
            $message = 'Fee deleted!';
        }
    }
}

// Get edit fee
$edit_fee = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM fee_configs WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_fee = $stmt->fetch();
}

// Get all fees
$fees = $pdo->query("SELECT * FROM fee_configs ORDER BY type, fee_name")->fetchAll();
$program_options = ['All','BSIT','BSHM','BSBA','BSED','BSCRIM','BSComEng','BSTM','BSAIS','BSPsych','BSOA','BSEntreP'];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title"><i class="bi bi-gear-wide-connected text-success me-2"></i>Fee Configuration</h3>
    <div>
        <a href="dashboard.php" class="btn btn-outline-secondary fw-bold me-2" style="border-radius: 8px;"><i class="bi bi-arrow-left"></i> Dashboard</a>
        <button class="btn btn-success fw-bold" style="border-radius: 8px;" onclick="recalculateAll()"><i class="bi bi-calculator me-1"></i>Recalculate All</button>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Add/Edit Form -->
<div class="card-outline bg-white mb-5" <?php echo $edit_fee ? 'style="border-color:#22c55e !important;"' : ''; ?>>
    <div class="balance-header py-3 px-4">
        <span class="section-title"><?php echo $edit_fee ? 'EDIT FEE' : 'ADD NEW FEE'; ?></span>
    </div>
    <div class="card-body p-4">
        <form method="POST">
            <?php if ($edit_fee): ?>
                <input type="hidden" name="id" value="<?php echo $edit_fee['id']; ?>">
            <?php endif; ?>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label row-val mb-2">Fee Name</label>
                    <input type="text" class="form-control row-text" name="fee_name" required
                           style="border: 2px solid #e2e8f0; border-radius: 8px;"
                           value="<?php echo $edit_fee ? htmlspecialchars($edit_fee['fee_name']) : ''; ?>"
                           placeholder="Library Fee, Tuition per Unit, etc.">
                </div>
                <div class="col-md-3">
                    <label class="form-label row-val mb-2">Amount (&#8369;)</label>
                    <input type="number" step="0.01" class="form-control row-text" name="amount" required min="0"
                           style="border: 2px solid #e2e8f0; border-radius: 8px;"
                           value="<?php echo $edit_fee ? $edit_fee['amount'] : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label row-val mb-2">Program</label>
                    <select class="form-select row-text" name="program" id="program_select" style="border: 2px solid #e2e8f0; border-radius: 8px;" required onchange="syncProgramUnits()">
                        <?php foreach ($program_options as $program): ?>
                            <option value="<?php echo $program; ?>" <?php echo ($edit_fee && $edit_fee['program'] === $program) ? 'selected' : ''; ?>>
                                <?php echo $program === 'All' ? 'All Programs' : $program; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label row-val mb-2">Type</label>
                    <select class="form-select row-text" name="type" style="border: 2px solid #e2e8f0; border-radius: 8px;" required onchange="toggleUnitCount()">
                        <option value="Tuition" <?php echo ($edit_fee && $edit_fee['type']=='Tuition') ? 'selected' : ''; ?>>Tuition</option>
                        <option value="Misc" <?php echo ($edit_fee && $edit_fee['type']=='Misc') ? 'selected' : ''; ?>>Miscellaneous</option>
                        <option value="Document" <?php echo ($edit_fee && $edit_fee['type']=='Document') ? 'selected' : ''; ?>>Document Fee</option>
                    </select>
                </div>
                <div class="col-md-3 mt-3" id="unit_count_group" style="display: none;">
                    <label class="form-label row-val mb-2">Units (From Manage Subjects)</label>
                    <input type="text" class="form-control row-text" id="unit_count_display" readonly style="border: 2px solid #e2e8f0; border-radius: 8px; background-color: #f8fafc;" value="">
                    <input type="hidden" name="unit_count" id="unit_count" value="">
                    <div class="form-text group-label-grey mt-2">Auto-based on the total units configured for the selected program.</div>
                </div>
                <div class="col-md-3 mt-3">
                    <label class="form-label row-val mb-2">&nbsp;</label>
                    <div class="d-flex align-items-center h-50">
                        <div class="form-check pt-1">
                            <input class="form-check-input" type="checkbox" name="active" id="active" <?php echo (!$edit_fee || $edit_fee['active']) ? 'checked' : ''; ?> style="transform: scale(1.3); margin-right: 5px;">
                            <label class="form-check-label row-val mt-1 ms-1" for="active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" name="<?php echo $edit_fee ? 'edit_fee' : 'add_fee'; ?>" class="btn btn-success fw-bold px-4 pt-2 pb-2" style="border-radius: 8px;">
                    <i class="bi bi-check-lg me-1"></i> <?php echo $edit_fee ? 'Update Fee' : 'Add Fee'; ?>
                </button>
                <?php if ($edit_fee): ?>
                    <button type="submit" name="delete_fee" class="btn btn-outline-danger fw-bold ms-2 px-4 pt-2 pb-2" style="border-radius: 8px;" onclick="return confirm('Delete this fee? All assessments will be recalculated.');">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Fees Table -->
<div class="card-outline bg-white mb-5">
    <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="section-title">CURRENT FEES</span>
        <span class="badge bg-success rounded-pill px-3 py-2"><?php echo $pdo->query("SELECT COUNT(*) FROM fee_configs WHERE active=1")->fetchColumn(); ?> Active</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                <thead style="background-color: #f8fafc;">
                    <tr>
                        <th class="group-label-grey py-3 px-4">NAME</th>
                        <th class="group-label-grey py-3">AMOUNT</th>
                        <th class="group-label-grey py-3">PROGRAM</th>
                        <th class="group-label-grey py-3">TYPE</th>
                        <th class="group-label-grey py-3 text-center">UNITS</th>
                        <th class="group-label-grey py-3">STATUS</th>
                        <th class="group-label-grey py-3 px-4 text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fees as $fee): ?>
                        <tr>
                            <td class="py-3 px-4 row-val"><?php echo htmlspecialchars($fee['fee_name']); ?></td>
                            <td class="py-3 row-text">&#8369; <?php echo number_format($fee['amount'], 2); ?></td>
                            <td class="py-3">
                                <span class="badge" style="background-color: #e2e8f0; color: #475569;">
                                    <?php echo $fee['program']; ?>
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge" style="background-color: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;"><?php echo $fee['type']; ?></span>
                            </td>
<?php echo $fee['unit_count'] ?? '-'; ?>
                            <td class="py-3">
                                <span class="badge px-3 py-2" style="<?php echo $fee['active'] ? 'background-color: #f1f8f4; color: #16a34a; border: 1px solid #bbf7d0;' : 'background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca;'; ?>">
                                    <?php echo $fee['active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="?edit=<?php echo $fee['id']; ?>" class="btn btn-sm btn-outline-success fw-bold" style="border-radius: 6px; border-width: 2px;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const programUnits = <?php echo json_encode($program_units, JSON_UNESCAPED_SLASHES); ?>;

function toggleUnitCount() {
    const type = document.querySelector('[name="type"]').value;
    document.getElementById('unit_count_group').style.display = type === 'Tuition' ? 'block' : 'none';
    syncProgramUnits();
}

function syncProgramUnits() {
    const type = document.querySelector('[name="type"]').value;
    const program = document.getElementById('program_select').value;
    const unitCount = type === 'Tuition' ? (programUnits[program] ?? 0) : '';
    document.getElementById('unit_count_display').value = unitCount === '' ? '' : unitCount + ' units';
    document.getElementById('unit_count').value = unitCount;
}

function recalculateAll() {
    if (confirm('Recalculate assessments for ALL students? This ensures fee changes apply everywhere.')) {
        fetch('ajax_recalc.php', {method: 'POST'})
            .then(response => response.json())
            .then(data => alert(data.message));
    }
}

toggleUnitCount();
</script>

<?php include '../includes/footer.php'; ?>

