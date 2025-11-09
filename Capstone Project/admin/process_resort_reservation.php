<?php
require_once '../includes/connect.php';
require_once 'log_activity.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_type = $_POST['reservation_type'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $checkin = $_POST['checkin'];
    $guests = $_POST['guests'];
    $resort_type = $_POST['resort_type'];
    $total_amount = $_POST['total_amount'];
    $payment_method = $_POST['payment_method'];

    // Insert into reservation table
    $sql_reservation = "INSERT INTO reservation (firstname, email, phone, address, checkin, guests, reservation_type, resort_type, total_amount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed')";
    $stmt_reservation = $conn->prepare($sql_reservation);
    $stmt_reservation->bind_param("sssssisdss", $fullname, $email, $phone, $address, $checkin, $guests, $reservation_type, $resort_type, $total_amount, $payment_method);

    if ($stmt_reservation->execute()) {
        $reservation_id = $stmt_reservation->insert_id;

        // Insert into billing table
        $sql_billing = "INSERT INTO billing (reservation_id, total_amount, payment_method, status) VALUES (?, ?, ?, 'Paid')";
        $stmt_billing = $conn->prepare($sql_billing);
        $stmt_billing->bind_param("ids", $reservation_id, $total_amount, $payment_method);
        $stmt_billing->execute();

                    log_activity($_SESSION['admin_id'], 'Reservation Management', 'Processed resort reservation for ' . $fullname . ' (Resort Type: ' . $resort_type . ')');
                    echo "<script>toastr.success('Resort reservation successful!'); setTimeout(function(){ window.location.href = 'reservation-resort.php'; }, 2000);</script>";    } else {
        echo "<script>toastr.error('Error: " . $stmt_reservation->error . "'); setTimeout(function(){ window.history.back(); }, 2000);</script>";
    }

    $stmt_reservation->close();
    $conn->close();
}
?>