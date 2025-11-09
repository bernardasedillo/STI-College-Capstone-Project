<?php
set_time_limit(300);
session_start();
require '../includes/connect.php';
require_once 'log_activity.php';
require_once 'send_confirmation_email.php';

if (isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);

    // Step 1: Update reservation status
    $update = $conn->query("UPDATE reservations SET status = 'confirmed' WHERE id = '$reservation_id'");

    if ($update) {
        // Step 2: Fetch reservation details
        $result = $conn->query("SELECT r. *,
          CASE
            WHEN r.reservation_type = 'Resort' THEN (SELECT p.name FROM prices p WHERE p.id = r.resort_package)
            WHEN r.reservation_type = 'Event Package' THEN (SELECT p.name FROM prices p WHERE p.id = r.events_package)
            ELSE NULL 
            END AS package_name
            FROM reservations r WHERE r.id = '$reservation_id'"); 
        $reservation = $result->fetch_assoc();

        // Step 3: Send confirmation email
        $reservation_details = 
            '<p><strong>Reservation Type:</strong> ' . $reservation['reservation_type'] . '</p>' .
            (isset($reservation['package_name']) && $reservation['package_name'] ?                             
           '<p><strong>Package Availed:</strong> ' . $reservation['package_name'] . '</p>' : '') .
            '<p><strong>Full Name:</strong> ' . $reservation['full_name'] . '</p>' .
            '<p><strong>Phone:</strong> ' . $reservation['phone'] . '</p>' .
            '<p><strong>Reservation Date:</strong> ' . date("M d, Y", strtotime($reservation['created_at'])) . '</p>' .
            '<p><strong>Check-in Date:</strong> ' . date("M d, Y", strtotime($reservation['checkin_date'])) . '</p>' .
            '<p><strong>Check-in Time:</strong> ' . $reservation['checkin_time'] . '</p>' .
            '<p><strong>Guests:</strong> ' . $reservation['guests'] . '</p>' .
            '<p><strong>Payment Method:</strong> ' . $reservation['payment_method'] . '</p>' .
            '<p><strong>Total Amount:</strong> ₱' . number_format($reservation['total_amount'], 2) . '</p>' .
            '<p><strong>Down Payment:</strong> ₱' . number_format($reservation['total_amount'] * 0.5, 2) . '</p>' .
            '<p><strong>Balance:</strong> ₱' . number_format($reservation['total_amount'] * 0.5, 2) . '</p>';

        if (sendConfirmationEmail($reservation['email'], $reservation['full_name'], $reservation_details)) {
            $_SESSION['success'] = 'Reservation confirmed and email sent successfully!';
        } else {
            $_SESSION['error'] = 'Reservation confirmed, but failed to send email.';
        }

        // Step 4: Ensure billing record exists
        $checkBilling = $conn->query("SELECT * FROM billing WHERE reservation_id = '$reservation_id'");
        if ($checkBilling->num_rows == 0) {
            // Create a new billing record with default values
            $conn->query("INSERT INTO billing (reservation_id, total_amount, down_payment, balance, payment_method, status) 
                          VALUES ('$reservation_id', 0, 0, 0, 'N/A', 'Unpaid')");
        }

        // Step 5: Log activity
        $user_id = $_SESSION['user_id'] ?? 0; // fallback if not set
        log_activity($user_id, "Confirm Reservation", "Confirmed reservation ID $reservation_id");

        // Redirect back to pending reservations
        header("Location: reserve.php?success=confirmed");
        exit;
    } else {
        header("Location: reserve.php?error=failed");
        exit;
    }
} else {
    header("Location: reserve.php?error=invalid_id");
    exit;
}
?>
