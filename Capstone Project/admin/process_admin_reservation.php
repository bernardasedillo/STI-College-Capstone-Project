<?php
session_start();
include '../includes/connect.php'; 
require_once 'log_activity.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['final_submit'])) {
    $reservation_type = $_POST['reservation_type'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $full_address = $_POST['full_address'];
    $checkin_date = $_POST['checkin_date'];
    $guests = $_POST['guests'];
    $package_id = $_POST['package_id'];
    $payment_method = $_POST['payment_method'];
    $down_payment = $_POST['down_payment'] ?? 0;

    // ✅ Validate phone number (must be 11 digits and start with 09)
    if (!preg_match('/^09\d{9}$/', $phone)) {
        $_SESSION['error'] = "Mobile number must start with '09' and contain exactly 11 digits.";
        header("Location: reserve.php?view=manual_reserve");
        exit;
    }

    // Fetch package details
    $packageQuery = $conn->prepare("SELECT * FROM prices WHERE id=?");
    $packageQuery->bind_param("i", $package_id);
    $packageQuery->execute();
    $package = $packageQuery->get_result()->fetch_assoc();

    if (!$package) {
        $_SESSION['error'] = "Invalid package selected.";
        header("Location: reserve.php?view=manual_reserve");
        exit;
    }

    $total_amount = $package['price'];
    $balance = $total_amount - $down_payment;

    // Validate Down Payment (must be at least 50%)
    $min_down = $total_amount * 0.5;
    if ($down_payment < $min_down) {
        $_SESSION['error'] = "Down payment must be at least 50% of the total price (₱" . number_format($min_down, 2) . ").";
        header("Location: reserve.php?view=manual_reserve");
        exit;
    }

    // Insert into reservations
    $stmt = $conn->prepare("INSERT INTO reservations 
        (reservation_type, full_name, email, phone, full_address, checkin_date, guests, total_amount, events_package, events_venue, payment_method, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')");
    $stmt->bind_param("sssssssdsss", 
        $reservation_type, $full_name, $email, $phone, $full_address, 
        $checkin_date, $guests, $total_amount, $package['name'], $package['venue'], $payment_method
    );
    $stmt->execute();
    $reservation_id = $stmt->insert_id;

    // Insert into billing
    $stmtBill = $conn->prepare("INSERT INTO billing (reservation_id, total_amount, down_payment, balance, payment_method, status) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $status = ($balance <= 0) ? "Paid" : "Pending";
    $stmtBill->bind_param("idddss", $reservation_id, $total_amount, $down_payment, $balance, $payment_method, $status);
    $stmtBill->execute();

    // Send confirmation email
    require_once 'send_confirmation_email.php';
    $reservation_details = "
        <p><strong>Reservation Type:</strong> {$reservation_type}</p>
        <p><strong>Check-in Date:</strong> {$checkin_date}</p>
        <p><strong>Guests:</strong> {$guests}</p>
        <p><strong>Package:</strong> {$package['name']}</p>
        <p><strong>Total Amount:</strong> ₱" . number_format($total_amount, 2) . "</p>
        <p><strong>Down Payment:</strong> ₱" . number_format($down_payment, 2) . "</p>
        <p><strong>Balance:</strong> ₱" . number_format($balance, 2) . "</p>
        <p><strong>Payment Method:</strong> {$payment_method}</p>
    ";
    sendConfirmationEmail($email, $full_name, $reservation_details);


    $_SESSION['success'] = "Reservation successfully created for $full_name.";
    header("Location: reserve.php?view=manual_reserve");
    exit;
} else {
    header("Location: reserve.php?view=manual_reserve");
    exit;
}
?>