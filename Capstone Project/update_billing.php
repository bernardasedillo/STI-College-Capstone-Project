<?php
session_start();
require_once '../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = (int)$_POST['reservation_id'];
    $payment = (float)$_POST['payment'];
    $method = $_POST['payment_method'];

    // Get current billing
    $stmt = $conn->prepare("SELECT total_amount, down_payment, balance FROM billing WHERE reservation_id=?");
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $billing = $stmt->get_result()->fetch_assoc();

    if (!$billing) {
        $_SESSION['error'] = "Billing record not found.";
        header("Location: reservation_overview.php");
        exit;
    }

    $new_dp = $billing['down_payment'] + $payment;
    $new_balance = $billing['total_amount'] - $new_dp;
    $status = ($new_balance <= 0) ? "Paid" : "Pending";

    $stmt = $conn->prepare("UPDATE billing SET down_payment=?, balance=?, payment_method=?, status=? WHERE reservation_id=?");
    $stmt->bind_param("ddssi", $new_dp, $new_balance, $method, $status, $reservation_id);
    $stmt->execute();

    $_SESSION['success'] = "Payment updated successfully.";
    header("Location: reservation_overview.php");
    exit;
}
?>
