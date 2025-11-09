<?php
require_once '../includes/connect.php';

if(ISSET($_POST['reservation_id'])){
    $reservation_id = $_POST['reservation_id'];
    $down_payment = $_POST['down_payment'];
    $status = $_POST['status'];

    // Get total amount from reservation
    $query = $conn->query("SELECT `total_amount` FROM `reservations` WHERE `id` = '$reservation_id'");
    $fetch = $query->fetch_array();
    $total_amount = $fetch['total_amount'];
    $balance = $total_amount - $down_payment;

    $conn->query("INSERT INTO `billing` (reservation_id, down_payment, balance, status) VALUES('$reservation_id', '$down_payment', '$balance', '$status') ON DUPLICATE KEY UPDATE down_payment = '$down_payment', balance = '$balance', status = '$status'");
    header("location:checkin.php");
}
?>