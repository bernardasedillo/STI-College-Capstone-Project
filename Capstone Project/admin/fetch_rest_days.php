<?php
require '../includes/connect.php';
$events = [];

$query = $conn->query("SELECT * FROM rest_days");
while ($row = $query->fetch_assoc()) {
    $events[] = [
        'title' => $row['reason'],
        'start' => $row['date'],
        'allDay' => true,
        'display' => 'background',
        'backgroundColor' => '#ff0000',
        'borderColor' => '#ff0000'
    ];
}

header('Content-Type: application/json');
echo json_encode($events);