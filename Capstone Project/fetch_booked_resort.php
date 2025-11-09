<?php
//fetch_booked_resort.php
require 'includes/connect.php';
header('Content-Type: application/json; charset=utf-8');

// Basic error handling
ini_set('display_errors', 0);
error_reporting(0);

$response = [
    'packageOptions' => [],
    'durations' => [],
    'excessRates' => ['default' => 0, '66k' => 0],
    'error' => null,
    'blockedPackages' => [],
    'blockedDurations' => [],
    'confirmedReservations' => [],
    'existingBookings' => [] // Detailed booking info with times
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

// ===== Fetch all confirmed resort AND event package reservations =====
$confirmedReservationsSql = "SELECT * FROM reservations 
                             WHERE (reservation_type = 'Resort' OR reservation_type = 'Event Package') 
                             AND status = 'confirmed'";
$confirmedResult = $conn->query($confirmedReservationsSql);

if ($confirmedResult) {
    while ($row = $confirmedResult->fetch_assoc()) {
        $response['confirmedReservations'][] = $row;
    }
} else {
    $response['error'] = "Error fetching confirmed reservations: " . $conn->error;
}

// ===== Fetch existing CONFIRMED bookings on this date WITH FULL item details =====
$bookedPackageIds = [];
$bookedDurationTexts = [];
$existingBookings = [];
$isFullyBooked = false;

// Query confirmed reservations - get resort_package (ID) and duration (TEXT)
// NOW INCLUDING Event Package reservations
$bookingSql = "SELECT 
    r.id as reservation_id,
    r.reservation_type,
    r.resort_package, 
    r.duration,
    r.checkin_time
    FROM reservations r
    WHERE r.checkin_date = ? 
    AND r.status = 'confirmed' 
    AND (r.reservation_type = 'Resort' OR r.reservation_type = 'Event Package')";

$stmt = $conn->prepare($bookingSql);
if ($stmt) {
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        while ($booking = $result->fetch_assoc()) {
            $itemName = '';
            $itemType = '';
            $durationHours = '';
            
            // Check if resort_package exists (it's an ID)
            // This applies to BOTH Resort reservations AND Event Package reservations with resort_package = 57
            if (!empty($booking['resort_package']) && is_numeric($booking['resort_package'])) {
                $bookedPackageIds[] = (int)$booking['resort_package'];
                $isFullyBooked = true; // Package booking = full resort booking
                
                // Fetch package details to get the name with time info
                $pkgDetailSql = "SELECT name, duration_hours FROM prices WHERE id = ?";
                $pkgStmt = $conn->prepare($pkgDetailSql);
                if ($pkgStmt) {
                    $pkgStmt->bind_param("i", $booking['resort_package']);
                    $pkgStmt->execute();
                    $pkgResult = $pkgStmt->get_result();
                    if ($pkgRow = $pkgResult->fetch_assoc()) {
                        $itemName = $pkgRow['name'];
                        $durationHours = $pkgRow['duration_hours'];
                        $itemType = 'package';
                    }
                    $pkgStmt->close();
                }
            }
            // Check if duration exists (it's TEXT like "7:00pm - 5:00pm")
            elseif (!empty($booking['duration'])) {
                $durationText = $booking['duration'];
                $bookedDurationTexts[] = $durationText;
                
                // The duration column already contains the time text
                $itemName = $durationText; // e.g., "7:00pm - 5:00pm"
                $durationHours = $durationText;
                $itemType = 'duration';
                
                // Optionally fetch the full name from prices table
                $durDetailSql = "SELECT name, duration_hours FROM prices 
                                WHERE duration_hours = ? 
                                AND venue = 'Resort' 
                                AND notes NOT LIKE '%package%'
                                LIMIT 1";
                $durStmt = $conn->prepare($durDetailSql);
                if ($durStmt) {
                    $durStmt->bind_param("s", $durationText);
                    $durStmt->execute();
                    $durResult = $durStmt->get_result();
                    if ($durRow = $durResult->fetch_assoc()) {
                        // Use the full name from database if found
                        $itemName = $durRow['name'];
                    }
                    $durStmt->close();
                }
            }
            
            // Store booking with item name for time conflict checking
            if ($itemName) {
                $existingBookings[] = [
                    'reservation_id' => $booking['reservation_id'],
                    'reservation_type' => $booking['reservation_type'], // Added for tracking
                    'item_name' => $itemName,
                    'item_type' => $itemType,
                    'duration_hours' => $durationHours, // Include the time text
                    'checkin_time' => $booking['checkin_time']
                ];
            }
        }
    } else {
        $response['error'] = "Error fetching booking results: " . $conn->error;
    }
    $stmt->close();
} else {
    $response['error'] = "Error checking existing bookings: " . $conn->error;
}

// Store existing bookings for client-side time conflict checking
$response['existingBookings'] = $existingBookings;

// If resort is fully booked (package booking exists), mark it
if ($isFullyBooked) {
    $response['blockedPackages'] = $bookedPackageIds;
}
$response['blockedDurations'] = $bookedDurationTexts;

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

// Fetch ALL packages (don't filter by booked ones - let JavaScript handle blocking)
if (!$response['error']) {
    $packageSql = "SELECT id, name, price, duration_hours, max_guest 
                   FROM prices 
                   WHERE venue = 'Resort' 
                   AND notes LIKE '%package%' 
                   AND is_archived = 0";
    
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

// Fetch ALL durations (don't filter by booked ones - let JavaScript handle blocking)
if (!$response['error']) {
    $durationSql = "SELECT id, name, duration_hours, price, max_guest 
                    FROM prices 
                    WHERE venue = 'Resort' 
                    AND day_type = ? 
                    AND duration = ? 
                    AND notes NOT LIKE '%package%' 
                    AND is_archived = 0";
    
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