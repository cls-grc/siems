<?php
require_once 'config/db_connect.php';
header('Content-Type: application/json');

if ($_POST['student_id']) {
    // Lookup employee from HR or applicants
    $employee = $pdo->prepare("
        SELECT a.department_name, jp.department_name 
        FROM applicants a 
        LEFT JOIN employee_records er ON er.user_id = (SELECT id FROM users WHERE student_id = ?)
        LEFT JOIN job_postings jp ON jp.id = a.job_posting_id 
        WHERE a.applicant_name LIKE ? OR er.user_id IS NOT NULL
        LIMIT 1
    ");
    $employee->execute([$_POST['student_id'], '%' . $_POST['student_id'] . '%']);
    $dept = $employee->fetchColumn();
    
    $program = $dept === 'IT' || $dept === 'Information Technology' ? 'BSIT' : ($dept === 'General Education' ? 'GENED' : '');
    
    echo json_encode(['program' => $program]);
} else {
    echo json_encode(['program' => '']);
}
?>

