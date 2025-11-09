<?php
require_once '../includes/connect.php';
require_once 'log_activity.php';
require_once 'send_checkout_email.php';
session_start();

if (isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];
    $total_amount = $_POST['total_amount_hidden'];
    $initial_down_payment = $_POST['down_payment_hidden'];
    $amount_paid_at_checkout = $_POST['amount_paid_at_checkout'];

    // Calculate total paid and final balance
    $total_paid = $initial_down_payment + $amount_paid_at_checkout;
    $final_balance = $total_amount - $total_paid;

    // Determine billing status
    $billing_status = ($final_balance <= 0) ? 'Paid' : 'Pending';

    //  Update billing table (removed checkout_date)
    $conn->query("UPDATE `billing` 
                  SET `down_payment` = '$total_paid', 
                      `balance` = '$final_balance', 
                      `status` = '$billing_status' 
                  WHERE `reservation_id` = '$reservation_id'");

    // Update reservation status and checkout date
    $conn->query("UPDATE `reservations` 
              SET `status` = 'checked-out', 
                  `checkout_date` = NOW() 
              WHERE `id` = '$reservation_id'");

    // Fetch customer details for email
    $stmt_fetch = $conn->prepare("SELECT full_name, email FROM reservations WHERE id = ?");
    $stmt_fetch->bind_param("i", $reservation_id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();
    $reservation = $result_fetch->fetch_assoc();
    $stmt_fetch->close();

    // Log activity
    log_activity(
        $_SESSION['admin_id'],
        'Reservation Management',
        'Processed checkout for reservation ID: ' . $reservation_id .
        '. Total paid: ' . $total_paid .
        ', Final Balance: ' . $final_balance .
        ', Billing Status: ' . $billing_status
    );

    // Send checkout email
    if ($reservation) {
        $reservation_details = "Total Amount: ₱ $total_amount<br>" .
                               "Total Paid: ₱ $total_paid<br>" .
                               "Balance: ₱ $final_balance<br>" .
                               "Status: $billing_status";
        sendCheckoutEmail($reservation['email'], $reservation['full_name'], $reservation_details);
    }

    // Redirect back to checkout list
    header("location:reserve.php?view=checkout");
    exit();
}
?>
