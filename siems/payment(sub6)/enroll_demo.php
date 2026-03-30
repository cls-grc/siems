<?php
/**
 * One-click demo enrollment for students 2024001-2024006
 * Run once: http://localhost/payment/enroll_demo.php
 */
require_once 'config/db_connect.php';
require_once 'includes/functions.php';

$students = ['2024001', '2024002', '2024003', '2024004', '2024005', '2024006'];

foreach ($students as $student_id) {
    // Ensure assessment exists
    calculateAssessment($student_id);
    
    // Add enrollment payment
    $receipt_no = 'DEMO-' . strtoupper(substr(md5($student_id), 0, 5));
    $stmt = $pdo->prepare("INSERT IGNORE INTO payments (student_id, amount_paid, receipt_no, remarks) VALUES (?, 1500.00, ?, 'Demo Enrollment Payment')");
    $stmt->execute([$student_id, $receipt_no]);
    
    echo "✅ $student_id: Enrolled! Receipt: $receipt_no<br>";
}

echo '<a href="index.php" class="btn btn-success mt-3">← Back to Login</a>';
?>

