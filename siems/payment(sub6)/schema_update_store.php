<?php
require_once __DIR__ . '/config/db_connect.php';

$sql = file_get_contents(__DIR__ . '/schema_update_store.sql');
if ($sql === false) {
    throw new RuntimeException('Failed to load schema_update_store.sql');
}

$pdo->exec($sql);
echo "Store schema update completed.\n";
