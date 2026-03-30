<?php
require 'payment(sub6)/config/db_connect.php';
$stmt = $pdo->query('SHOW TABLES LIKE "users"');
var_export((bool)$stmt->fetchColumn());
echo PHP_EOL;
?>
