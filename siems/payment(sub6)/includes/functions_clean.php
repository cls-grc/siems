<?php
require_once __DIR__ . '/../config/db_connect.php';

/**
 * CLEAN Scholarship Assessment - 100% = ZERO BALANCE
 */
function calculateAssessment($student_id) {
    global $pdo;
    
    // Get student + scholarship
    $stmt = $pdo->prepare("SELECT u.program, s.discount_type FROM users u LEFT JOIN scholarships s ON u.student_id = s.student_id WHERE u.student_id = ? AND u.role = 'student'");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
    
    if (!$student) return ['grand_total' => 0, 'discount' => 'No Student'];
    
    $program = $student['program'];
    $discount_type = $student['discount_type'] ?? 'None';
    
    // Calculate GROSS fees
    $stmt = $pdo->prepare("SELECT amount, unit_count FROM fee_configs WHERE type = 'Tuition' AND (program = ? OR program = 'All') AND active = 1 LIMIT 1");
    $stmt->execute([$program]);
    $tuition = $stmt->fetch();
    $total_tuition = $tuition ? ($tuition['amount'] * ($tuition['unit_count'] ?? 21)) : 52500;
    
    $stmt = $pdo->prepare("SELECT SUM(amount) as misc FROM fee_configs WHERE type = 'Misc' AND (program = ? OR program = 'All') AND active = 1");
    $stmt->execute([$program]);
    $total_misc = $stmt->fetch()['misc'] ?? 0;
    
    $total_gross = $total_tuition + $total_misc;
    
    // Scholarship discounts
    $discounts = [
        'None' => 0,
        'Academic 50%' => 0.5,
        'QC Foundation 75%' => 0.75,
        'Sibling' => 0.25,
        'Valedictorian' => 1.0
    ];
    
    $discount_rate = $discounts[$discount_type] ?? 0;
    
    // 100% = FULL WAIVER (ZERO)
    if ($discount_rate == 1.0) {
        $grand_total = 0.00;
        $discount_display = '100% FULL WAIVER';
    } else {
        $grand_total = $total_gross * (1 - $discount_rate);
        $discount_display = $discount_rate * 100 . '% WAIVED';
    }
    
    // Save to DB
    $stmt = $pdo->prepare("INSERT INTO student_assessments (student_id, total_tuition, total_misc, grand_total) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE total_tuition = ?, total_misc = ?, grand_total = ?");
    $stmt->execute([$student_id, $total_tuition, $total_misc, $grand_total, $total_tuition, $total_misc, $grand_total]);
    
    return [
        'total_tuition' => $total_tuition,
        'total_misc' => $total_misc,
        'gross_total' => $total_gross,
        'discount_rate' => $discount_rate,
        'discount_display' => $discount_display,
        'grand_total' => $grand_total
    ];
}

function getStudentBalance($student_id) {
    $assessment = calculateAssessment($student_id);
    $grand_total = $assessment['grand_total'];
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount_paid), 0) as paid FROM payments WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $total_paid = $stmt->fetch()['paid'];
    
    return $grand_total - $total_paid;
}

function checkEnrollmentStatus($student_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount_paid), 0) as total FROM payments WHERE student_id = ?");
    $stmt->execute([$student_id]);
    return $stmt->fetch()['total'] >= 1000;
}

function getStudentSOA($student_id) {
    $assessment = calculateAssessment($student_id);
    return [
        'assessment' => $assessment,
        'balance' => getStudentBalance($student_id),
        'enrolled' => checkEnrollmentStatus($student_id),
        'total_paid' => getTotalPayments($student_id)
    ];
}

function getTotalPayments($student_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount_paid), 0) as total FROM payments WHERE student_id = ?");
    $stmt->execute([$student_id]);
    return $stmt->fetch()['total'];
}

function getStudentFees($student_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT program FROM users WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $prog = $stmt->fetch()['program'] ?? 'All';
    
    $stmt = $pdo->prepare("SELECT id, fee_name, amount FROM fee_configs WHERE (program = ? OR program = 'All') AND active = 1 ORDER BY fee_name");
    $stmt->execute([$prog]);
    return $stmt->fetchAll();
}

function postPayment($student_id, $amount_paid, $remarks = '') {
    global $pdo;
    $receipt_no = 'OPN-' . strtoupper(substr(uniqid(), -5));
    
    $stmt = $pdo->prepare("INSERT INTO payments (student_id, amount_paid, receipt_no, remarks) VALUES (?, ?, ?, ?)");
    $success = $stmt->execute([$student_id, $amount_paid, $receipt_no, $remarks]);
    
    calculateAssessment($student_id);
    return $success ? $receipt_no : false;
}
?>

