<?php
session_start();

if (isset($_POST['login'])) {
    require_once "../../classes/Admin.php";

    $admin = new Admin();

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $rsp = $admin->login($email, $password);

    if ((int)$rsp > 0) {
        $_SESSION['adminonline'] = $rsp;
        header("location:../admindashboard.php");
        exit;
    } else {
        $_SESSION['errormsg'] = is_string($rsp) ? $rsp : "Invalid credentials";
        header("location:../index.php");
        exit;
    }
} else {
    $_SESSION['errormsg'] = "Kindly fill the form";
    header("location:../index.php");
    exit;
}
?>
