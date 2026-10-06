<?php
session_start();

if (!isset($_SESSION['adminonline'])) {
    header("location:../index.php");
    exit;
}

if (isset($_POST['delete_media'])) {
    require_once "../../classes/Admin.php";

    $admin = new Admin();
    $vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;
    $media_type = isset($_POST['media_type']) ? trim($_POST['media_type']) : '';
    $media_id   = isset($_POST['media_id']) ? (int)$_POST['media_id'] : 0;

    if ($vehicle_id <= 0 || $media_id <= 0 || !in_array($media_type, ['image', 'video'], true)) {
        $_SESSION['errormsg'] = "Invalid media deletion request.";
        header("location:../edit_vehicle.php?id=" . $vehicle_id);
        exit;
    }

    if ($media_type === 'image') {
        $res = $admin->delete_vehicle_image($media_id);
    } else {
        $res = $admin->delete_vehicle_video($media_id);
    }

    if ($res === true) {
        $_SESSION['feedback'] = "Media removed successfully.";
    } else {
        $_SESSION['errormsg'] = is_string($res) ? $res : "Failed to remove the media.";
    }

    header("location:../edit_vehicle.php?id=" . $vehicle_id);
    exit;
}

header("location:../admindashboard.php");
exit;
