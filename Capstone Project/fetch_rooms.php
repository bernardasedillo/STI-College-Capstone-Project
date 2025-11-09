<?php
require 'includes/connect.php';
header('Content-Type: application/json; charset=utf-8');

// Basic error handling
ini_set('display_errors', 1);
error_reporting(E_ALL);

$response = [
    'rooms' => [],
    'durations' => [],
    'roomPrices' => []
];

// Fetch unique room names
$roomSql = "SELECT DISTINCT name FROM prices WHERE venue = 'Room' AND is_archived = 0 ORDER BY name ASC";
$roomResult = $conn->query($roomSql);
if ($roomResult) {
    while ($row = $roomResult->fetch_assoc()) {
        $response['rooms'][] = $row['name'];
    }
}

// Fetch all room prices and durations
$pricesSql = "SELECT name, duration_hours, price FROM prices WHERE venue = 'Room' AND is_archived = 0";
$pricesResult = $conn->query($pricesSql);
if ($pricesResult) {
    $durations = [];
    while ($row = $pricesResult->fetch_assoc()) {
        $duration = $row['duration_hours'];

        $response['roomPrices'][] = [
            'name' => $row['name'],
            'duration_hours' => $duration,
            'price' => (float)$row['price']
        ];
        if (!in_array($duration, $durations) && $duration) {
            $durations[] = $duration;
        }
    }
    sort($durations, SORT_NUMERIC);
    $response['durations'] = $durations;
}

$conn->close();

echo json_encode($response);
?>