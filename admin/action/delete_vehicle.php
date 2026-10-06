<?php
session_start();

if (!isset($_SESSION['adminonline'])) {
    header("location:../index.php");
    exit;
}

if (isset($_POST['delete_vehicle'])) {
    require_once "../../classes/Admin.php";

    $admin = new Admin();
    $vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;

    if ($vehicle_id > 0) {
        $res = $admin->delete_vehicle($vehicle_id);
        if ($res) {
            $_SESSION['feedback'] = "Vehicle item and all its pictures were successfully deleted.";
        } else {
            $_SESSION['errormsg'] = "Failed to delete vehicle item.";
        }
    } else {
        $_SESSION['errormsg'] = "Invalid vehicle ID.";
    }
}

header("location:../admindashboard.php");
exit;
?>
