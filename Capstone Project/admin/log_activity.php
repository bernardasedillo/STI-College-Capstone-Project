<?php
function log_activity($user_id, $action, $details) {
    require '../includes/connect.php';

    if (!$conn) {
        error_log("Database connection not established in log_activity.");
        return;
    }

    // ✅ Prevent logging null user IDs
    if (empty($user_id)) {
        error_log("Attempted to log activity with null user_id. Action: $action");
        return;
    }

    date_default_timezone_set('Asia/Manila'); // Ensure timezone is set for PHP's date functions
    $current_timestamp = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        INSERT INTO activity_logs (user_id, action, details, timestamp)
        VALUES (?, ?, ?, ?)
    ");

    if (!$stmt) {
        error_log("Failed to prepare activity log statement: " . $conn->error);
        return;
    }

    $stmt->bind_param("isss", $user_id, $action, $details, $current_timestamp);

    if (!$stmt->execute()) {
        error_log("Failed to execute activity log: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>
