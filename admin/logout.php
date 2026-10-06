<?php
session_start();
require_once "../classes/Admin.php";

$admin = new Admin();
$admin->logout();

header("location:index.php");
exit;
?>
