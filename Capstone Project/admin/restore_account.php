<?php
require_once '../includes/validate.php';
require '../includes/connect.php';
require_once 'log_activity.php';

if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin'])){
    header("location:home.php");
    exit();
}

if(isset($_GET['admin_id'])){
    $admin_id = intval($_GET['admin_id']);
    
    // Restore account
    $query = $conn->query("UPDATE `admin` SET `status` = 'active' WHERE `admin_id` = '$admin_id'");
    header("location: account_archive.php?success=restored");
    exit();
} else {
    header("location: account_archive.php");
    exit();
}
?>