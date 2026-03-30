<?php
require_once __DIR__ . '/includes/functions.php';

// Keep only the highest ID for each student_id
$pdo->exec("
    DELETE t1 FROM student_assessments t1
    INNER JOIN student_assessments t2 
    WHERE t1.id < t2.id AND t1.student_id = t2.student_id
");

// Add unique constraint
try {
    $pdo->exec("ALTER TABLE student_assessments ADD UNIQUE KEY unique_student_semester (student_id, semester)");
    echo "Added unique constraint.\n";
} catch (Exception $e) {
    echo "Constraint may already exist: " . $e->getMessage() . "\n";
}

// Recalculate everyone
$stmt = $pdo->query("SELECT student_id FROM users WHERE role = 'student'");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($students as $s) {
    calculateAssessment($s['student_id']);
}
echo "Recalculated all students.\n";
?>
