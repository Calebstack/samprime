<?php
session_start();

if (!isset($_SESSION['adminonline'])) {
    header("location:../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    require_once "../../classes/Admin.php";

    $admin = new Admin();
    $vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;
    $status     = isset($_POST['vehicle_status']) ? trim($_POST['vehicle_status']) : '';
    $redirect   = isset($_POST['redirect_to']) ? trim($_POST['redirect_to']) : '';

    if ($vehicle_id > 0 && !empty($status)) {
        $res = $admin->update_vehicle_status($vehicle_id, $status);
        if ($res === true) {
            $_SESSION['feedback'] = "Vehicle status updated successfully to '" . ucfirst($status) . "'.";
        } else {
            $_SESSION['errormsg'] = is_string($res) ? $res : "Failed to update vehicle status.";
        }
    } else {
        $_SESSION['errormsg'] = "Invalid vehicle ID or status.";
    }

    if (!empty($redirect) && (strpos($redirect, 'view_vehicle.php') !== false || strpos($redirect, 'admindashboard.php') !== false)) {
        header("location:../" . $redirect);
        exit;
    }
}

header("location:../admindashboard.php");
exit;
?>
