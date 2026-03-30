<?php
require_once __DIR__ . '/config/db_connect.php';

$stmt = $pdo->query("SELECT * FROM audit_log");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt = $pdo->query("SHOW TRIGGERS");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
