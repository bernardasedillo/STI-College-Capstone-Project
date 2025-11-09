<?php
require_once '../includes/connect.php';

$events = [];

$query = $conn->query("SELECT id, full_name, checkin_date, reservation_type, email, phone, checkin_time, guests, room_number, resort_package, events_package, event_type 
                       FROM `reservations` 
                       WHERE `status` = 'confirmed'");

while ($row = $query->fetch_assoc()) {
    // Set event color based on reservation_type
    $color = '#3788d8'; // default: Room
    if ($row['reservation_type'] === 'Resort') {
        $color = '#f0ad4e'; // Yellow for Resort
    } elseif ($row['reservation_type'] === 'Event Package') {
        $color = '#5cb85c'; // Green for Event Package
    }

    $events[] = [
        'id'    => $row['id'],
        'title' => $row['full_name'],
        'start' => $row['checkin_date'],
        'color' => $color, // <-- Added color property
        'extendedProps' => [
            'reservation_type' => $row['reservation_type'],
            'email'            => $row['email'],
            'phone'            => $row['phone'],
            'checkin_time'     => $row['checkin_time'],
            'guests'           => $row['guests'],
            'room_number'      => $row['room_number'],
            'resort_package'   => $row['resort_package'],
            'events_package'   => $row['events_package'],
            'event_type'       => $row['event_type']
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
