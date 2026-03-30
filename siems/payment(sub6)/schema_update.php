<?php
require_once __DIR__ . '/includes/functions.php';

try {
    // Modify fee_configs
    $pdo->exec("ALTER TABLE fee_configs MODIFY type ENUM('Tuition', 'Misc', 'Registration', 'Document') NOT NULL");
    
    // Attempt to add columns to student_assessments
    $pdo->exec("ALTER TABLE student_assessments ADD COLUMN total_registration DECIMAL(10,2) DEFAULT 0.00 AFTER total_misc");
    $pdo->exec("ALTER TABLE student_assessments ADD COLUMN discount_amount DECIMAL(10,2) DEFAULT 0.00 AFTER total_registration");
    $pdo->exec("ALTER TABLE student_assessments ADD COLUMN gross_total DECIMAL(10,2) DEFAULT 0.00 AFTER total_registration");

    echo "Schema updated successfully.\n";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
         echo "Columns already exist.\n";
    } else {
         echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
