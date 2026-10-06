<?php
session_start();

if (!isset($_SESSION['adminonline'])) {
    header("location:../index.php");
    exit;
}

if (isset($_POST['btn_update'])) {
    require_once "../../classes/Admin.php";

    $admin = new Admin();
    $vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;
    $name       = trim($_POST['vehicle_name'] ?? '');
    $year       = trim($_POST['vehicle_year'] ?? '');
    $color      = trim($_POST['vehicle_color'] ?? '');
    $price      = trim($_POST['vehicle_price'] ?? '');
    $entry_year = trim($_POST['vehicle_entry_year'] ?? '');
    $condition  = trim($_POST['vehicle_condition'] ?? '');
    $features   = isset($_POST['features']) && is_array($_POST['features']) ? $_POST['features'] : [];
    $files      = $_FILES;

    if ($vehicle_id <= 0 || empty($name) || empty($year) || empty($color)) {
        $_SESSION['errormsg'] = "Please complete the vehicle name, make year and color fields.";
        header("location:../edit_vehicle.php?id=" . $vehicle_id);
        exit;
    }

    $result = $admin->update_vehicle($vehicle_id, $name, $year, $color, $price, $entry_year, $condition, $features);

    if ($result === true) {
        $admin->add_vehicle_images($vehicle_id, $files);
        $admin->add_vehicle_videos($vehicle_id, $files);

        $_SESSION['feedback'] = "Vehicle listing updated successfully.";
    } else {
        $_SESSION['errormsg'] = is_string($result) ? $result : "Failed to update the vehicle listing.";
    }

    header("location:../edit_vehicle.php?id=" . $vehicle_id);
    exit;
}

header("location:../admindashboard.php");
exit;
