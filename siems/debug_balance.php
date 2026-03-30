<?php
require_once 'payment(sub6)/config/db_connect.php';
echo "=== DEBUG STUDENT BALANCE ===\n";

echo "Fee configs:\n";
$stmt = $pdo->query("SELECT * FROM fee_configs WHERE active = 1");
$fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($fees);

echo "\nStudents:\n";
$stmt = $pdo->query("SELECT student_id, full_name, program FROM users WHERE role = 'student' LIMIT 3");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

if (!empty($fees) && siemsTableExists('users')) {
    $student_id = $pdo->query("SELECT student_id FROM users WHERE role = 'student' LIMIT 1")->fetchColumn();
    if ($student_id) {
        echo "\nFor student $student_id:\n";
        require_once 'payment(sub6)/includes/functions.php';
        $assessment = calculateAssessment($student_id);
        print_r($assessment);
        $balance = getStudentBalance($student_id);
        echo "Balance: $balance\n";
        
        // Check table
        $stmt = $pdo->prepare("SELECT * FROM student_assessments WHERE student_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$student_id]);
        $assess = $stmt->fetch(PDO::FETCH_ASSOC);
        print_r($assess);
    }
}

echo "\n=== END DEBUG ===\n";
?>

