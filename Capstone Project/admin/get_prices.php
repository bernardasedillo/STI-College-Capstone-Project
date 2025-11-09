<?php
require_once '../includes/connect.php';

$prices = [];

// Room prices
$result = $conn->query("SELECT * FROM room_prices");
$prices['room_prices'] = $result->fetch_all(MYSQLI_ASSOC);

// Resort prices
$result = $conn->query("SELECT * FROM resort_prices");
$prices['resort_prices'] = $result->fetch_all(MYSQLI_ASSOC);

// Event prices
$result = $conn->query("SELECT * FROM event_prices");
$prices['event_prices'] = $result->fetch_all(MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode($prices);
?>