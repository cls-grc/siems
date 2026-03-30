<?php
/**
 * EMERGENCY 100% Scholarship Fix Script
 * Run once: http://localhost/payment/fix_100percent_scholar.php
 */
require_once 'config/db_connect.php';

echo "<h1>🔧 100% Scholarship Fix - Diagnostics & Repair</h1>";

// Test 2024002 (QC Foundation 100%)
$student_id = '2024002';
echo "<h3>Testing student $student_id</h3>";

$stmt = $pdo->prepare("SELECT discount_type FROM scholarships WHERE student_id = ?");
$stmt->execute([$student_id]);
$scholar = $stmt->fetch();
echo "Scholarship: " . $scholar['discount_type'] . "<br>";

require_once 'includes/functions.php';
$assessment = calculateAssessment($student_id);
echo "Grand Total: ₱" . number_format($assessment['grand_total'], 2) . "<br>";

$total_paid = getTotalPayments($student_id);
echo "Total Paid: ₱" . number_format($total_paid, 2) . "<br>";

$balance = getStudentBalance($student_id);
echo "<strong>FINAL BALANCE: ₱" . number_format($balance, 2) . "</strong><hr>";

if ($balance != 0) {
    echo "<span class='text-danger'>❌ FAILED - Balance should be 0!</span>";
} else {
    echo "<span class='text-success'>✅ PERFECT - Zero balance for 100% scholar!</span>";
}

echo "<hr><a href='index.php'>← Back to Login</a>";
?>

