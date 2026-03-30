<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireSubsystemAccess('payment_accounting');

$page_title = 'Reports';
$method_filter = $_GET['method'] ?? 'All';

$where = "WHERE verification_status = 'Verified'";
$params = [];
if ($method_filter !== 'All') {
    $where .= " AND payment_method = ?";
    $params[] = $method_filter;
}

$stmt = $pdo->prepare("SELECT DATE(payment_date) as date, SUM(amount_paid) as total FROM payments $where GROUP BY DATE(payment_date) ORDER BY date DESC LIMIT 30");
$stmt->execute($params);
$daily_collections = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h3 class="dashboard-title"><i class="bi bi-file-earmark-bar-graph text-success me-2"></i>Reports</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;"><i class="bi bi-arrow-left"></i> Dashboard</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card-outline bg-white h-100 mb-0">
            <div class="balance-header py-3 px-4">
                <span class="section-title">DAILY COLLECTIONS (LAST 30 DAYS)</span>
            </div>
            <div class="card-body p-4">
                <canvas id="collectionsChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-outline bg-white h-100 mb-0">
            <div class="balance-header py-3 px-4">
                <span class="section-title">QUICK REPORTS</span>
            </div>
            <div class="card-body p-4 d-flex flex-column gap-3">
                <form method="GET" class="mb-2">
                    <label class="group-label-grey mb-2">STATEMENT OF ACCOUNT</label>
                    <div class="input-group">
                        <input type="text" class="form-control row-text" name="soa_student" placeholder="Student ID" style="border: 2px solid #e2e8f0; border-right: none;" required>
                        <button class="btn btn-outline-success fw-bold" type="submit" style="border: 2px solid #22c55e;">Generate</button>
                    </div>
                </form>
                
                <div class="mt-2 pt-3" style="border-top: 2px dashed #e2e8f0;">
                    <label class="group-label-grey mb-2">EXPORT DATA</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success w-50 fw-bold py-2 shadow-sm" onclick="exportCollectionsPDF()" style="border-radius: 8px;">
                            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                        </button>
                        <button class="btn btn-outline-success w-50 fw-bold py-2 shadow-sm" onclick="exportCollectionsCSV()" style="border-radius: 8px;">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card-outline bg-white">
            <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="section-title">COLLECTIONS BREAKDOWN</span>
                <form method="GET" class="d-flex align-items-center m-0">
                    <span class="group-label-grey me-2 mt-1">FILTER METHOD:</span>
                    <select class="form-select form-select-sm row-text fw-bold" name="method" onchange="this.form.submit()" style="border: 2px solid #e2e8f0; border-radius: 6px; width: auto; background-color: white;">
                        <option value="All" <?php echo $method_filter === 'All' ? 'selected' : ''; ?>>All Methods</option>
                        <option value="Cash" <?php echo $method_filter === 'Cash' ? 'selected' : ''; ?>>Cash</option>
                        <option value="GCash" <?php echo $method_filter === 'GCash' ? 'selected' : ''; ?>>GCash</option>
                        <option value="Maya" <?php echo $method_filter === 'Maya' ? 'selected' : ''; ?>>Maya</option>
                        <option value="Online Banking" <?php echo $method_filter === 'Online Banking' ? 'selected' : ''; ?>>Online Banking</option>
                        <option value="Credit/Debit Card" <?php echo $method_filter === 'Credit/Debit Card' ? 'selected' : ''; ?>>Credit/Debit Card</option>
                    </select>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="collectionsTable" style="font-size: 0.9rem;">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th class="group-label-grey py-3 px-4">DATE</th>
                            <th class="group-label-grey py-3">TOTAL COLLECTIONS</th>
                            <th class="group-label-grey py-3 px-4">TRANSACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daily_collections as $day): ?>
                            <tr>
                                <td class="py-3 px-4 row-text"><?php echo date('M j, Y', strtotime($day['date'])); ?></td>
                                <td class="py-3 row-val text-success">&#8369; <?php echo number_format($day['total'], 2); ?></td>
                                <td class="py-3 px-4 row-text"><?php 
                                    $t_stmt = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE DATE(payment_date) = ? AND verification_status = 'Verified'" . ($method_filter !== 'All' ? " AND payment_method = ?" : ""));
                                    if ($method_filter !== 'All') {
                                        $t_stmt->execute([$day['date'], $method_filter]);
                                    } else {
                                        $t_stmt->execute([$day['date']]);
                                    }
                                    echo $t_stmt->fetchColumn();
                                ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($daily_collections)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                    No collection records for the last 30 days
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- PDF Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const { jsPDF } = window.jspdf;
const ctx = document.getElementById('collectionsChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php foreach($daily_collections as $d) echo "'".date('M j', strtotime($d['date']))."',"; ?>],
        datasets: [{
            label: 'Collections',
            data: [<?php foreach($daily_collections as $d) echo $d['total'].","; ?>],
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderRadius: 4
        }]
    },
    options: { scales: { y: { beginAtZero: true } } }
});

async function exportCollectionsPDF() {
    const table = document.getElementById('collectionsTable');
    const canvas = await html2canvas(table, { 
        scale: 2,
        useCORS: true,
        backgroundColor: '#ffffff'
    });
    
    const imgData = canvas.toDataURL('image/png');
    const pdf = new jsPDF('l', 'mm', 'a4');
    
    const imgWidth = 277;
    const pageHeight = 200;
    const imgHeight = (canvas.height * imgWidth) / canvas.width;
    let heightLeft = imgHeight;
    
    let position = 10;
    pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;
    
    while (heightLeft >= 0) {
        position = heightLeft - imgHeight;
        pdf.addPage();
        pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
    }
    
    pdf.save('daily-collections-' + new Date().toISOString().slice(0,10) + '.pdf');
}

function exportCollectionsCSV() {
    let csv = 'Date,Amount\n';
    <?php foreach($daily_collections as $d): ?>
        csv += '<?php echo date('Y-m-d', strtotime($d['date'])); ?>,<?php echo $d['total']; ?>\n';
    <?php endforeach; ?>
    const blob = new Blob([csv], {type: 'text/csv'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'collections.csv';
    a.click();
}
</script>

<?php include '../includes/footer.php'; ?>

