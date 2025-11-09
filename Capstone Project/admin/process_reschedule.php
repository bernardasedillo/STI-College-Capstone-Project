<?php
require_once '../includes/connect.php';
require_once 'log_activity.php';
require_once 'send_reschedule_email.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reschedule_id'])) {
    $reservation_id = $_POST['reschedule_id'];
    $new_checkin_date = $_POST['new_checkin_date'];

    // Fetch current reservation details
    $stmt_fetch = $conn->prepare("SELECT checkin_date, original_checkin_date, full_name, email FROM reservations WHERE id = ?");
    $stmt_fetch->bind_param("i", $reservation_id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();
    $reservation = $result_fetch->fetch_assoc();
    $stmt_fetch->close();

    if ($reservation) {
        $original_checkin_date = $reservation['original_checkin_date'] ?? $reservation['checkin_date'];

        // Update reservation
        $stmt_update = $conn->prepare("UPDATE reservations SET checkin_date = ?, original_checkin_date = ?, status = 'rescheduled' WHERE id = ?");
        $stmt_update->bind_param("ssi", $new_checkin_date, $original_checkin_date, $reservation_id);

        if ($stmt_update->execute()) {
            // Log the activity
            log_activity($_SESSION['admin_id'], 'Reservation Management', 
                'Re-scheduled reservation ID: ' . $reservation_id . 
                ' from ' . $original_checkin_date . ' to ' . $new_checkin_date);

            // Send reschedule email
            sendRescheduleEmail($reservation['email'], $reservation['full_name'], $new_checkin_date, $original_checkin_date);

            // ✅ Redirect back to the Check-in view in reserve.php
            header("Location: reserve.php?view=checkin&reschedule=success");
            exit();
        } else {
            header("Location: reserve.php?view=checkin&reschedule=error");
            exit();
        }

        $stmt_update->close();
    } else {
        header("Location: reserve.php?view=checkin&reschedule=notfound");
        exit();
    }

    $conn->close();
} else {
    header("Location: reserve.php?view=checkin&reschedule=invalid");
    exit();
}
?>
