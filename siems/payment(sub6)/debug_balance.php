<?php
require_once __DIR__ . '/includes/functions.php';

echo "All Scholarships:\n";
$stmt = $pdo->query("SELECT * FROM scholarships");
$scholarships = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($scholarships);

echo "\nAssessments for students with scholarships:\n";
foreach ($scholarships as $s) {
    echo "Student ID: " . $s['student_id'] . "\n";
    print_r(calculateAssessment($s['student_id']));
    echo "Balance according to getStudentBalance: " . getStudentBalance($s['student_id']) . "\n";
    
    // Select from database to see what was saved
    $stmt2 = $pdo->prepare("SELECT * FROM student_assessments WHERE student_id = ?");
    $stmt2->execute([$s['student_id']]);
    print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
}
?>
