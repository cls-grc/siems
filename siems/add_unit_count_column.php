<?php
require_once 'payment(sub6)/config/db_connect.php';

try {
    $pdo->exec("ALTER TABLE fee_configs ADD COLUMN unit_count INT NULL DEFAULT NULL AFTER type");
    echo "SUCCESS: Added unit_count column to fee_configs table.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column already exists. Good!";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>

