<?php
session_start();

if (!isset($_SESSION['adminonline'])) {
    header("location:../index.php");
    exit;
}

if (isset($_POST['btn_upload'])) {
    require_once "../../classes/Admin.php";

    $admin = new Admin();

    $name       = trim($_POST['vehicle_name']);
    $year       = trim($_POST['vehicle_year']);
    $color      = trim($_POST['vehicle_color']);
    $price      = trim($_POST['vehicle_price'] ?? '');
    $entry_year = trim($_POST['vehicle_entry_year'] ?? '');
    $condition  = trim($_POST['vehicle_condition'] ?? '');
    $features   = isset($_POST['features']) && is_array($_POST['features']) ? $_POST['features'] : [];
    $files      = $_FILES;

    if (empty($name) || empty($year) || empty($color)) {
        $_SESSION['errormsg'] = "Please fill in all required vehicle fields (Name, Make Year, Color).";
        header("location:../upload_vehicle.php");
        exit;
    }

    $res = $admin->upload_vehicle($name, $year, $color, $price, $entry_year, $condition, $features, $files);

    if ($res === true) {
        $_SESSION['feedback'] = "Vehicle item '" . htmlspecialchars($name) . "' uploaded successfully!";
        header("location:../admindashboard.php");
        exit;
    } else {
        $_SESSION['errormsg'] = is_string($res) ? $res : "Failed to upload vehicle.";
        header("location:../upload_vehicle.php");
        exit;
    }
} else {
    $_SESSION['errormsg'] = "Invalid form submission.";
    header("location:../admindashboard.php");
    exit;
}
?>
