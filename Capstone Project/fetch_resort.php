<?php
require 'includes/connect.php';
header('Content-Type: application/json; charset=utf-8');

// Basic error handling
ini_set('display_errors', 1);
error_reporting(E_ALL);

$response = [
    'packageOptions' => [],
    'durations' => [],
    'excessRates' => ['default' => 0, '66k' => 0],
    'error' => null
];

if (!isset($_GET['date'])) {
    $response['error'] = 'Date not provided.';
    echo json_encode($response);
    exit;
}

$date = $_GET['date'];
$dayOfWeek = date('N', strtotime($date));
$month = date('n', strtotime($date));
$dayType = ($dayOfWeek >= 1 && $dayOfWeek <= 5) ? 'Weekdays' : 'Weekends';

// Determine month-based duration string
$duration_string = '';
if ($month >= 2 && $month <= 11) { // Feb - November
    $duration_string = 'Feb - November';
} else { // Dec - Jan
    $duration_string = 'Dec - Jan';
}

// Fetch excess rates
$excessRateSql = "SELECT name, price FROM prices WHERE venue = 'Resort' AND name LIKE '%Excess Rate%'";
$excessResult = $conn->query($excessRateSql);
if ($excessResult) {
    while ($row = $excessResult->fetch_assoc()) {
        if (strpos($row['name'], '66K') !== false) {
            $response['excessRates']['66k'] = (float)$row['price'];
        } else {
            $response['excessRates']['default'] = (float)$row['price'];
        }
    }
} else {
    $response['error'] = "Error fetching excess rates: " . $conn->error;
}

// Fetch packages
if (!$response['error']) {
    $packageSql = "SELECT id, name, price, duration_hours, max_guest FROM prices WHERE venue = 'Resort' AND notes LIKE '%package%' AND is_archived = 0";
    $packageResult = $conn->query($packageSql);
    if ($packageResult) {
        while ($row = $packageResult->fetch_assoc()) {
            $response['packageOptions'][] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => (float)$row['price'],
                'duration_hours' => $row['duration_hours'],
                'max_guest' => (int)$row['max_guest']
            ];
        }
    } else {
        $response['error'] = "Error fetching packages: " . $conn->error;
    }
}

// Fetch durations based on day type and month
if (!$response['error']) {
    $durationSql = "SELECT id, name, duration_hours, price, max_guest FROM prices WHERE venue = 'Resort' AND day_type = ? AND duration = ? AND notes NOT LIKE '%package%' AND is_archived = 0";
    $stmt = $conn->prepare($durationSql);
    if ($stmt) {
        $stmt->bind_param("ss", $dayType, $duration_string);
        $stmt->execute();
        $durationResult = $stmt->get_result();
        if ($durationResult) {
            while ($row = $durationResult->fetch_assoc()) {
                $response['durations'][] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'hours' => $row['duration_hours'],
                    'price' => (float)$row['price'],
                    'max_guest' => (int)$row['max_guest']
                ];
            }
        } else {
            $response['error'] = "Error fetching durations: " . $conn->error;
        }
        $stmt->close();
    } else {
        $response['error'] = "Error preparing statement for durations: " . $conn->error;
    }
}

$conn->close();

echo json_encode($response);
?>
