<?php
require_once __DIR__ . '/../config/db_connect.php';

/**
 * Log audit trail
 */
function logAudit($user_id, $student_id, $action, $old_value = null, $new_value = null) {
    global $pdo;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $stmt = $pdo->prepare("
        INSERT INTO audit_log (user_id, student_id, action, old_value, new_value, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $student_id, $action, $old_value, $new_value, $ip, $agent]);
}

/**
 * Calculate assessment with GPA/stacking
 */
function calculateAssessment($student_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT u.program, s.discount_type, s.gpa, s.stackable 
        FROM users u 
        LEFT JOIN scholarships s ON u.student_id = s.student_id
        WHERE u.student_id = ? AND u.role = 'student'
    ");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        return false;
    }
    
    $program = $student['program'];
    
    // GPA tiered
    $gpa_discount = 0;
    if ($student['gpa']) {
        if ($student['gpa'] <= 1.2) $gpa_discount = 1.0;
        elseif ($student['gpa'] <= 1.5) $gpa_discount = 0.5;
    }
    
    // Tuition
    $stmt = $pdo->prepare("
        SELECT amount, unit_count 
        FROM fee_configs 
        WHERE type = 'Tuition' AND (program = ? OR program = 'All') AND active = 1 
        LIMIT 1
    ");
    $stmt->execute([$program]);
    $tuition = $stmt->fetch();
    
    $total_tuition = $tuition ? $tuition['amount'] * ($tuition['unit_count'] ?? 21) : 0;
    
    // Misc fees
    $stmt = $pdo->prepare("
        SELECT SUM(amount) as total_misc 
        FROM fee_configs 
        WHERE type = 'Misc' AND (program = ? OR program = 'All') AND active = 1
    ");
    $stmt->execute([$program]);
    $misc = $stmt->fetch();
    $total_misc = $misc['total_misc'] ?? 0;
    
    $subtotal = $total_tuition + $total_misc;
    
    // Discounts
    $discount_type = $student['discount_type'] ?? 'None';
    $discounts = [
        'None' => 0,
        'Academic 50%' => 0.5,
        'QC Foundation 100%' => 1.0,
        'Sibling' => 0.25,
        'Valedictorian' => 1.0
    ];
    
    $discount_total = $discounts[$discount_type] ?? 0;
    $stacked_discount = ($student['stackable'] ?? 1) ? $discount_total + $gpa_discount : max($discount_total, $gpa_discount);
    
    $grand_total = $subtotal * (1 - $stacked_discount);
    
    logAudit(null, $student_id, 'Assessment Calculated', null, "$discount_type ($stacked_discount)");
    
    $stmt = $pdo->prepare("
        INSERT INTO student_assessments (student_id, total_tuition, total_misc, grand_total, balance) 
        VALUES (?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
            total_tuition=VALUES(total_tuition),
            total_misc=VALUES(total_misc),
            grand_total=VALUES(grand_total),
            balance=VALUES(grand_total)
    ");
    $stmt->execute([$student_id, $total_tuition, $total_misc, $grand_total, $grand_total]);
    
    return ['total_tuition' => $total_tuition, 'total_misc' => $total_misc, 'grand_total' => $grand_total, 'discount' => round($stacked_discount*100).'%'];
}

function getStudentBalance($student_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT grand_total FROM student_assessments WHERE student_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$student_id]);
    $grand_total = $stmt->fetch()['grand_total'] ?? 0;
    
    $stmt = $pdo->prepare("SELECT SUM(amount_paid) as total FROM payments WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $total_paid = $stmt->fetch()['total'] ?? 0;
    
    return $grand_total - $total_paid;
}

function checkEnrollmentStatus($student_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(amount_paid) as total FROM payments WHERE student_id = ?");
    $stmt->execute([$student_id]);
    return ($stmt->fetch()['total'] ?? 0) >= 1000;
}

function getStudentFees($student_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT program FROM users WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $program = $stmt->fetch()['program'] ?? 'All';
    
    $stmt = $pdo->prepare("
        SELECT id, fee_name, amount 
        FROM fee_configs 
        WHERE (program = ? OR program = 'All') AND active = 1 
        ORDER BY type, fee_name
    ");
    $stmt->execute([$program]);
    return $stmt->fetchAll();
}

function postPayment($student_id, $amount_paid, $selected_fees = [], $remarks = '') {
    global $pdo;
    $receipt_no = 'OPN-' . strtoupper(substr(uniqid(), -5));
    
    $stmt = $pdo->prepare("
        INSERT INTO payments (student_id, amount_paid, receipt_no, remarks, qr_code) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $qr_code = 'receipt_' . $receipt_no;
    $success = $stmt->execute([$student_id, $amount_paid, $receipt_no, $remarks, $qr_code]);
    
    calculateAssessment($student_id);
    return $success ? $receipt_no : false;
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
    $stmt = $pdo->prepare("SELECT SUM(amount_paid) as total FROM payments WHERE student_id = ?");
    $stmt->execute([$student_id]);
    return $stmt->fetch()['total'] ?? 0;
}
?>

