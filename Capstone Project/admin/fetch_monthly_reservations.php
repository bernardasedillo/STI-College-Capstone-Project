<?php
require_once '../includes/connect.php';

// Get current month and year
$currentMonth = date('Y-m');

// Fetch Total Reservations for the Month
$reservationsQuery = $conn->query("
    SELECT COUNT(*) as total_reservations 
    FROM reservations
    WHERE DATE_FORMAT(checkin_date, '%Y-%m') = '$currentMonth'
");
$monthlyReservations = $reservationsQuery->fetch_array();
$totalMonthlyReservations = $monthlyReservations['total_reservations'] ? $monthlyReservations['total_reservations'] : 0;

echo $totalMonthlyReservations;
?>