<?php
session_start(); // ✅ Must be at the top before accessing $_SESSION

require_once '../includes/connect.php';
require_once 'log_activity.php';

// Validate session
if (!isset($_SESSION['admin_id'])) {
    die("Error: Admin not logged in.");
}

// Validate and sanitize admin_id
if (!isset($_REQUEST['admin_id']) || !is_numeric($_REQUEST['admin_id'])) {
    die("Error: Invalid admin ID.");
}

$admin_id_to_archive = intval($_REQUEST['admin_id']); // ✅ Prevent SQL injection

// --- Archive the account ---
$query = "UPDATE `admin` SET `status` = 'archived' WHERE `admin_id` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id_to_archive);

if ($stmt->execute()) {
    // --- Log the activity ---
    log_activity($_SESSION['admin_id'], 'Account Management', 'Archived account with admin_id: ' . $admin_id_to_archive);
    header("Location: account.php?success=archived");
    exit;
} else {
    die("Error archiving account: " . $stmt->error);
}
?>