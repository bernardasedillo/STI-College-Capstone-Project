<?php
require '../includes/connect.php';
session_start();
require_once 'log_activity.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $reason = !empty($_POST['reason']) ? $_POST['reason'] : 'Closed';

    $stmt = $conn->prepare("INSERT IGNORE INTO rest_days (date, reason) VALUES (?, ?)");
    $stmt->bind_param("ss", $date, $reason);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Rest day added!";
        log_activity($_SESSION['admin_id'], 'Content Management', 'Added rest day: ' . $date . ' with reason: ' . $reason);
    } else {
        $_SESSION['error'] = "Failed to add rest day.";
    }
    header("Location: home.php");
    exit();
}
