<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount']) && isset($_POST['method'])) {
    $student_id = $_SESSION['student_id'];
    $amount = floatval($_POST['amount']);
    $method = $_POST['method'];
    
    $balance = getStudentBalance($student_id);
    
    // Validate amount
    if ($amount < 100 || $amount > $balance) {
        $_SESSION['message'] = "Invalid payment amount.";
        $_SESSION['msg_type'] = "danger";
        header("Location: pay_online.php");
        exit;
    }

    // Insert as Pending
    $remarks = 'Online Payment via ' . $method;
    $receipt_no = postPayment($student_id, $amount, $remarks, $method, null, null, 'Pending');
    
    if ($receipt_no) {
        $_SESSION['message'] = "<strong>Payment Submitted!</strong> Your payment of ₱" . number_format($amount, 2) . " via " . htmlspecialchars($method) . " is pending validation by the cashier. Receipt No: " . htmlspecialchars($receipt_no);
        $_SESSION['msg_type'] = "success";
        header("Location: receipts.php");
    } else {
        $_SESSION['message'] = "Payment failed to record. Please contact support.";
        $_SESSION['msg_type'] = "danger";
        header("Location: pay_online.php");
    }
    exit;
}

header("Location: pay_online.php");
exit;
