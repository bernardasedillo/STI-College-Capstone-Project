<?php
require_once '../includes/connect.php';
require_once 'log_activity.php';

// ✅ Start session before using $_SESSION
session_start();

// ✅ Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    // Optional: redirect to login if session expired
    header("Location: index.php?error=unauthorized");
    exit();
}

if (isset($_REQUEST['id'])) {
    $id = intval($_REQUEST['id']); // sanitize input

    // --- Delete the reservation ---
    if ($conn->query("DELETE FROM `reservations` WHERE `id` = '$id'")) {

        // --- Log activity (with valid user_id from session) ---
        log_activity($_SESSION['admin_id'], 'Reservation Management', 'Deleted reservation with ID: ' . $id);

        // --- Redirect back with success message ---
        header("Location: reserve.php?success=deleted");
        exit();
    } else {
        // Optional: handle deletion error
        header("Location: reserve.php?error=delete_failed");
        exit();
    }
} else {
    header("Location: reserve.php?error=missing_id");
    exit();
}
?>
