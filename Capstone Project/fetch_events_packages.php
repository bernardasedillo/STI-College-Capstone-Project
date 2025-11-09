<?php
require 'includes/connect.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['date'])) {
    echo json_encode(['error' => 'No date provided']);
    exit;
}

$date = $_GET['date'];
if (!$date || strtotime($date) === false) {
    echo json_encode(['error' => 'Invalid date']);
    exit;
}

// Determine weekday or weekend
$dayOfWeek = date('N', strtotime($date)); // 1=Mon, 7=Sun
$dayType = ($dayOfWeek >= 6) ? 'Weekends' : 'Weekdays';

// Fetch packages (excluding excess rate)
$sqlPackages = "
    SELECT id, venue, name, day_type, duration, price, notes, duration_hours, 
           affiliate_catering, affiliate_lights, inclusions
    FROM prices
    WHERE venue IN ('Mini Function Hall', 'Renatos Hall', 'Renatos Pavilion')
      AND name <> 'Guest Excess Rate'
      AND name <> 'Excess Rate'
      AND is_archived = 0
      AND (day_type = ? OR day_type = 'Any_Day')
    ORDER BY venue, id ASC
";

$stmt = $conn->prepare($sqlPackages);
if (!$stmt) {
    echo json_encode(['error' => 'SQL prepare failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param('s', $dayType);
$stmt->execute();
$result = $stmt->get_result();

$packages = [];
while ($row = $result->fetch_assoc()) {
    $packages[] = $row;
}
$stmt->close();

// Fetch excess rates (per venue)
$sqlExcess = "
    SELECT venue, price
    FROM prices
    WHERE venue IN ('Mini Function Hall', 'Renatos Hall', 'Renatos Pavilion')
      AND name = 'Excess Rate'
      AND is_archived = 0
";
$resultExcess = $conn->query($sqlExcess);

$excessRates = [];
while ($row = $resultExcess->fetch_assoc()) {
    $excessRates[$row['venue']] = (int)$row['price'];
}
$conn->close();

// Final response
if (empty($packages)) {
    echo json_encode(['message' => 'No packages available for this date']);
} else {
    echo json_encode([
        'packages' => $packages,
        'excessRates' => $excessRates
    ]);
}