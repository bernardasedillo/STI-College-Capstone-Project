<?php
require_once '../includes/connect.php';

// Get current month and year
$currentMonth = date('Y-m');

// Fetch Monthly Revenue
$revenueQuery = $conn->query("
    SELECT SUM(b.total_amount) as monthly_revenue 
    FROM reservations r
    JOIN billing b ON r.id = b.reservation_id
    WHERE r.status = 'Checked-out' AND DATE_FORMAT(r.checkin_date, '%Y-%m') = '$currentMonth'
");
$monthlyRevenue = $revenueQuery->fetch_array();
$totalMonthlyRevenue = $monthlyRevenue['monthly_revenue'] ? $monthlyRevenue['monthly_revenue'] : 0;

echo $totalMonthlyRevenue;
?>