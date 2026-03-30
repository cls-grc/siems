<?php
session_start();
$_SESSION = array();
$_SESSION['message'] = 'You have been logged out successfully.';
$_SESSION['msg_type'] = 'info';
header('Location: index.php');
exit;
?>
