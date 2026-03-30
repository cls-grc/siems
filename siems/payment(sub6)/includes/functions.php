<?php
/**
 * FINAL CLEAN College Payment Functions - 100% Scholarship FIXED
 */
require_once __DIR__ . '/../config/db_connect.php';

function getDocumentRequestFeeAmount($document_type, $urgency = 'Regular') {
    $base_fees = [
        'Certificate of Enrollment' => 100,
        'Transcript of Records (TOR)' => 200,
        'Good Moral Certificate' => 50,
        'Diploma' => 300,
        'Authenticated TOR' => 500,
        'Certificate of Graduation' => 150,
    ];

    $base_amount = $base_fees[$document_type] ?? 0;
    $rush_fee = $urgency === 'Rush' ? 100 : 0;

    return $base_amount + $rush_fee;
}

function getStoreSizeOptions($size_group) {
    $sizes = [
        'Clothing' => ['XS', 'S', 'M', 'L', 'XL', '2XL'],
        'Book' => [],
        'One Size' => ['One Size'],
    ];

    return $sizes[$size_group] ?? [];
}

function getStoreItems($active_only = true) {
    global $pdo;

    $sql = "SELECT * FROM store_items";
    if ($active_only) {
        $sql .= " WHERE active = 1";
    }
    $sql .= " ORDER BY category ASC, item_name ASC";

    $items = $pdo->query($sql)->fetchAll();
    foreach ($items as &$item) {
        $item['size_options'] = getStoreSizeOptions($item['size_group'] ?? 'One Size');
    }

    return $items;
}

function generateClaimCode() {
    return 'CLM-' . strtoupper(substr(uniqid(), -8));
}

function createStoreOrder($student_id, array $item, $size_option, $quantity, $payment_mode, $payment_method, $verification_status = 'Pending') {
    global $pdo;

    $quantity = max(1, (int)$quantity);
    $unit_price = (float)$item['price'];
    $total_amount = round($unit_price * $quantity, 2);
    $order_status = $verification_status === 'Verified' ? 'Paid' : 'Pending Payment';
    $claim_code = generateClaimCode();

    $stmt = $pdo->prepare("
        INSERT INTO item_orders
            (student_id, item_id, item_name, category, size_option, quantity, unit_price, total_amount, payment_mode, order_status, claim_code)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $student_id,
        $item['id'],
        $item['item_name'],
        $item['category'],
        $size_option ?: null,
        $quantity,
        $unit_price,
        $total_amount,
        $payment_mode,
        $order_status,
        $claim_code,
    ]);

    $order_id = (int)$pdo->lastInsertId();
    $remarks = 'Store Purchase [ITEMORDER:' . $order_id . '] - ' . $item['item_name'];
    if (!empty($size_option)) {
        $remarks .= ' [' . $size_option . ']';
    }

    $receipt_no = postPayment($student_id, $total_amount, $remarks, $payment_method, null, null, $verification_status);
    $stmt = $pdo->prepare("SELECT id FROM payments WHERE receipt_no = ? LIMIT 1");
    $stmt->execute([$receipt_no]);
    $payment_id = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("UPDATE item_orders SET payment_id = ? WHERE id = ?");
    $stmt->execute([$payment_id, $order_id]);

    return [
        'order_id' => $order_id,
        'receipt_no' => $receipt_no,
        'payment_id' => $payment_id,
        'total_amount' => $total_amount,
        'claim_code' => $claim_code,
    ];
}

function getStudentItemOrders($student_id) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT io.*, p.receipt_no, p.payment_date, p.payment_method, p.verification_status
        FROM item_orders io
        LEFT JOIN payments p ON io.payment_id = p.id
        WHERE io.student_id = ?
        ORDER BY io.ordered_at DESC, io.id DESC
    ");
    $stmt->execute([$student_id]);
    return $stmt->fetchAll();
}

function getAllItemOrders() {
    global $pdo;

    return $pdo->query("
        SELECT io.*, u.full_name, u.program, p.receipt_no, p.payment_date, p.payment_method, p.verification_status
        FROM item_orders io
        JOIN users u ON io.student_id = u.student_id
        LEFT JOIN payments p ON io.payment_id = p.id
        ORDER BY io.ordered_at DESC, io.id DESC
    ")->fetchAll();
}

function getStudentAccountPaymentTotal($student_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount_paid), 0)
        FROM payments
        WHERE student_id = ?
          AND verification_status = 'Verified'
          AND (
              remarks IS NULL
              OR (
                  remarks NOT LIKE 'Document Request Payment [DOCREQ:%'
                  AND remarks NOT LIKE 'Store Purchase [ITEMORDER:%'
              )
          )
    ");
    $stmt->execute([$student_id]);
    return $stmt->fetchColumn();
}

function getCurrentAcademicPeriodLabels() {
    global $pdo;

    try {
        $stmt = $pdo->query("
            SELECT academic_year, semester
            FROM academic_periods
            WHERE period_status IN ('Open', 'Upcoming')
            ORDER BY
                CASE period_status
                    WHEN 'Open' THEN 1
                    WHEN 'Upcoming' THEN 2
                    ELSE 3
                END,
                id DESC
            LIMIT 1
        ");
        $period = $stmt->fetch();
        if ($period) {
            return [
                'academic_year' => $period['academic_year'],
                'semester' => $period['semester'],
            ];
        }
    } catch (Throwable $e) {
    }

    return [
        'academic_year' => '2026-2027',
        'semester' => '1st',
    ];
}

function syncStudentBillingStatement($student_id, $balance, $net_total = null) {
    global $pdo;

    $period = getCurrentAcademicPeriodLabels();
    $status = 'Open';
    if ($balance <= 0) {
        $status = 'Paid';
    } elseif ($net_total !== null && $balance < $net_total) {
        $status = 'Partially Paid';
    }

    $statementNo = 'SOA-' . preg_replace('/[^A-Za-z0-9]/', '', $student_id) . '-' . str_replace('-', '', $period['academic_year']) . '-' . strtoupper($period['semester']);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO billing_statements
                (student_id, statement_no, statement_date, due_date, amount_due, status)
            VALUES
                (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), ?, ?)
            ON DUPLICATE KEY UPDATE
                amount_due = VALUES(amount_due),
                status = VALUES(status)
        ");
        $stmt->execute([$student_id, $statementNo, $balance, $status]);
    } catch (Throwable $e) {
    }
}

function recordFinancialTransaction($student_id, $payment_id, $type, $reference_code, $amount) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO financial_transactions
                (student_id, payment_id, transaction_type, reference_code, amount, transaction_date)
            VALUES
                (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$student_id, $payment_id, $type, $reference_code, $amount]);
    } catch (Throwable $e) {
    }
}

/**
 * PERFECT Scholarship Logic - 100% = ZERO FOREVER
 */
function calculateAssessment($student_id) {
    global $pdo;
    
    // Student + scholarship
    $stmt = $pdo->prepare("SELECT u.program, s.discount_type FROM users u LEFT JOIN scholarships s ON u.student_id = s.student_id AND s.status = 'Approved' WHERE u.student_id = ? AND u.role = 'student'");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
    
    if (!$student) return ['grand_total' => 54300, 'discount' => 'No Data'];
    
    $program = $student['program'];
    $discount_type = $student['discount_type'] ?? 'None';
    $is_enrolled = checkEnrollmentStatus($student_id);
    
    // Get dynamically enrolled units
    $period = getCurrentAcademicPeriodLabels();
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(s.units), 0)
        FROM enrollments e
        JOIN subjects s ON e.subject_id = s.id
        WHERE e.student_id = ? AND e.academic_year = ? AND e.semester = ?
    ");
    $stmt->execute([$student_id, $period['academic_year'], $period['semester']]);
    $dynamic_units = $stmt->fetchColumn() ?: 0;

    $total_tuition = 0;
    $total_misc = 0;
    $total_registration = 0;
    if ($is_enrolled) {
        // GROSS fees (before discount)
        $stmt = $pdo->prepare("SELECT amount FROM fee_configs WHERE type = 'Tuition' AND (program = ? OR program = 'All') AND active = 1 LIMIT 1");
        $stmt->execute([$program]);
        $tuition = $stmt->fetch();
        $amount_per_unit = $tuition ? floatval($tuition['amount']) : 1000;
        
        // The tuition is now based fully on their actual enrolled units
        $total_tuition = $dynamic_units * $amount_per_unit;
        
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM fee_configs WHERE type = 'Misc' AND (program = ? OR program = 'All') AND active = 1");
        $stmt->execute([$program]);
        $total_misc = $stmt->fetchColumn() ?: 0;
        
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM fee_configs WHERE type = 'Registration' AND (program = ? OR program = 'All') AND active = 1");
        $stmt->execute([$program]);
        $total_registration = $stmt->fetchColumn() ?: 0;

    }
    
    $gross_total = $total_tuition + $total_misc + $total_registration;
    
    // DISCOUNTS
    $discounts = [
        'None' => 0.00,
        'Sibling' => 0.25,
        'Academic 50%' => 0.50,
        'QC Foundation 75%' => 0.75,
        'QC Foundation 100%' => 1.00,
        'Valedictorian' => 1.00
    ];
    
    $discount_rate = $discounts[$discount_type] ?? 0.00;
    
    // 100% = FULL WAIVER (ZERO CHARGE)
    $discount_amount = round($gross_total * $discount_rate, 2);
    $net_total = round($gross_total - $discount_amount, 2);
    $discount_text = $discount_rate == 1.00 ? '100% FULL WAIVER' : number_format($discount_rate * 100, 0) . '% DISCOUNT';
    
    // Balance
    $paid = getStudentAccountPaymentTotal($student_id);
    $balance = max(0, round($net_total - $paid, 2));
    
    // SAVE
    $stmt = $pdo->prepare("
        INSERT INTO student_assessments
            (student_id, semester, academic_year, total_tuition, total_misc, total_registration, gross_total, discount_amount, grand_total, balance, assessed_at)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            total_tuition = VALUES(total_tuition),
            total_misc = VALUES(total_misc),
            total_registration = VALUES(total_registration),
            gross_total = VALUES(gross_total),
            discount_amount = VALUES(discount_amount),
            grand_total = VALUES(grand_total),
            balance = VALUES(balance),
            assessed_at = NOW()
    ");
    $stmt->execute([$student_id, $period['semester'], $period['academic_year'], $total_tuition, $total_misc, $total_registration, $gross_total, $discount_amount, $net_total, $balance]);

    syncStudentBillingStatement($student_id, $balance, $net_total);
    
    $user_id = $_SESSION['user_id'] ?? null;
    $disc_str = $discount_type !== 'None' ? "$discount_type - " : "";
    logAudit($user_id, $student_id, 'Assessment Recalculated', null, $disc_str . "Net Assessment: ₱" . number_format($net_total, 2));
    
    return [
        'gross_total' => $gross_total,
        'total_tuition' => $total_tuition,
        'total_misc' => $total_misc,
        'total_registration' => $total_registration,
        'total_document_fees' => 0,
        'discount_amount' => $discount_amount,
        'discount_rate' => $discount_rate,
        'discount_text' => $discount_text,
        'net_total' => $net_total,
        'scholarship' => $discount_type
    ];
}

/**
 * Balance = Net Total - Payments (SIMPLE)
 */
function getStudentBalance($student_id) {
    $assessment = calculateAssessment($student_id);
    $net_total = $assessment['net_total'];
    
    $paid = getStudentAccountPaymentTotal($student_id);
    
    return max(0, round($net_total - $paid, 2)); // Never negative
}

function checkEnrollmentStatus($student_id) {
    global $pdo;
    
    // Check for manual override first
    $stmt = $pdo->prepare("SELECT enrollment_status FROM users WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $override = $stmt->fetchColumn();
    
    if ($override === 'Enrolled') return true;
    if ($override === 'Pending') return false;

    // If the student already has enrolled subjects, treat the account as active.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ?");
    $stmt->execute([$student_id]);
    if ((int)$stmt->fetchColumn() > 0) return true;
    
    // Fallback to Auto calculation rules
    $total_paid = getStudentAccountPaymentTotal($student_id);
    return $total_paid >= 1000;
}

function getStudentSOA($student_id) {
    $assessment = calculateAssessment($student_id);
    $total_paid = getStudentAccountPaymentTotal($student_id);
    
    $enrolled = checkEnrollmentStatus($student_id);
    
    return [
        'assessment' => $assessment,
        'balance' => getStudentBalance($student_id),
        'total_paid' => $total_paid,
        'enrolled' => $enrolled
    ];
}

function getTotalPayments($student_id) {
    return getStudentAccountPaymentTotal($student_id);
}

function logAudit($user_id, $student_id, $action, $old_value = null, $new_value = null) {
    global $pdo;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_log (user_id, student_id, action, old_value, new_value, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $student_id, $action, $old_value, $new_value, $ip, $agent]);
    } catch (PDOException $e) {
        // Silently fail audit logging to prevent application crash
    }
}

function postPayment($student_id, $amount, $remarks = '', $method = 'Cash', $proof = null, $ref = null, $status = 'Verified') {
    global $pdo;
    $receipt_no = 'OPN-' . strtoupper(uniqid());

    $stmt = $pdo->prepare("
        INSERT INTO payments
            (student_id, amount_paid, receipt_no, remarks, payment_method, proof_file, reference_no, verification_status)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$student_id, $amount, $receipt_no, $remarks, $method, $proof, $ref, $status]);
    $payment_id = (int) $pdo->lastInsertId();

    recordFinancialTransaction($student_id, $payment_id, 'Payment', $receipt_no, $amount);

    if ($status === 'Verified') {
        calculateAssessment($student_id);
    }
    
    $user_id = $_SESSION['user_id'] ?? null;
    $log_action = $status === 'Verified' ? 'Payment Received' : 'Payment Submitted (Pending)';
    logAudit($user_id, $student_id, $log_action, null, "Amount: ₱" . number_format($amount, 2) . " via " . $method . ($remarks ? " ($remarks)" : ""));
    
    return $receipt_no;
}

function getStudentFees($student_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT program FROM users WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $program = $stmt->fetchColumn() ?: 'All';
    
    $stmt = $pdo->prepare("SELECT id, fee_name, amount FROM fee_configs WHERE (program = ? OR program = 'All') AND active = 1");
    $stmt->execute([$program]);
    return $stmt->fetchAll();
}
?>

