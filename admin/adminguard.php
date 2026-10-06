<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['adminonline'])) {
    $_SESSION['errormsg'] = "You need to be logged in to access this page";
    header("location:index.php");
    exit;
}
?>
