<?php
require 'includes/connect.php';
header('Content-Type: application/json; charset=utf-8');

// Disable error display in JSON output
ini_set('display_errors', 0);
error_reporting(0);

try {
    // Prepare SQL query to fetch ALL confirmed reservations
    $sql = "SELECT 
                id,
                full_name,
                email,
                phone,
                reservation_type,
                checkin_date,
                checkin_time,
                duration,
                guests,
                room_number,
                resort_room,
                resort_room_duration,
                resort_package,
                status,
                total_amount,
                created_at
            FROM reservations 
            WHERE status = 'confirmed'
            AND checkin_date >= CURDATE()
            ORDER BY reservation_type ASC, checkin_date ASC, checkin_time ASC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception($conn->error);
    }
    
    // Initialize separate arrays for different reservation types
    $roomBookings = [];
    $resortBookings = [];
    $eventBookings = [];
    $allBookings = [];
    
    while ($row = $result->fetch_assoc()) {
        // Process room_number field to split multiple rooms into array
        $rooms_array = [];
        if (!empty($row['room_number'])) {
            // Split room numbers by comma and trim whitespace
            $rooms_array = array_map('trim', explode(',', $row['room_number']));
        }
        
        // Process resort_room field to split multiple rooms into array
        $resort_rooms_array = [];
        if (!empty($row['resort_room'])) {
            // Split room numbers by comma and trim whitespace
            $resort_rooms_array = array_map('trim', explode(',', $row['resort_room']));
        }
        
        // Extract duration hours from string (e.g., "12 Hours Stay" -> 12)
        $duration_hours = 0;
        if (!empty($row['duration'])) {
            if (preg_match('/(\d+)\s*Hour/i', $row['duration'], $matches)) {
                $duration_hours = intval($matches[1]);
            }
        }
        
        // Extract resort_room_duration hours if it exists
        $resort_duration_hours = 0;
        if (!empty($row['resort_room_duration'])) {
            if (preg_match('/(\d+)\s*Hour/i', $row['resort_room_duration'], $matches)) {
                $resort_duration_hours = intval($matches[1]);
            }
        }
        
        // Calculate checkout datetime for room bookings
        $checkout_date = null;
        $checkout_time = null;
        $checkout_datetime = null;
        
        if (!empty($row['checkin_date']) && !empty($row['checkin_time']) && $duration_hours > 0) {
            try {
                $checkinDateTime = new DateTime($row['checkin_date'] . ' ' . $row['checkin_time']);
                $checkoutDateTime = clone $checkinDateTime;
                $checkoutDateTime->add(new DateInterval('PT' . $duration_hours . 'H'));
                
                $checkout_date = $checkoutDateTime->format('Y-m-d');
                $checkout_time = $checkoutDateTime->format('H:i:s');
                $checkout_datetime = $checkoutDateTime->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                // Skip datetime calculation if there's an error
            }
        }
        
        // Calculate checkout datetime for resort room bookings
        $resort_checkout_date = null;
        $resort_checkout_time = null;
        $resort_checkout_datetime = null;
        
        if (!empty($row['checkin_date']) && !empty($row['checkin_time']) && $resort_duration_hours > 0) {
            try {
                $resortCheckinDateTime = new DateTime($row['checkin_date'] . ' ' . $row['checkin_time']);
                $resortCheckoutDateTime = clone $resortCheckinDateTime;
                $resortCheckoutDateTime->add(new DateInterval('PT' . $resort_duration_hours . 'H'));
                
                $resort_checkout_date = $resortCheckoutDateTime->format('Y-m-d');
                $resort_checkout_time = $resortCheckoutDateTime->format('H:i:s');
                $resort_checkout_datetime = $resortCheckoutDateTime->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                // Skip datetime calculation if there's an error
            }
        }
        
        // Build the booking object
        $booking = [
            'id' => $row['id'],
            'full_name' => $row['full_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'reservation_type' => $row['reservation_type'],
            'checkin_date' => $row['checkin_date'],
            'checkin_time' => $row['checkin_time'],
            'duration' => $row['duration'],
            'duration_hours' => $duration_hours,
            'guests' => $row['guests'],
            'room_number' => $row['room_number'],
            'rooms_array' => $rooms_array,
            'resort_room' => $row['resort_room'] ?? null,
            'resort_room_duration' => $row['resort_room_duration'] ?? null,
            'resort_rooms_array' => $resort_rooms_array,
            'resort_duration_hours' => $resort_duration_hours,
            'resort_package' => $row['resort_package'] ?? null,
            'status' => $row['status'],
            'total_amount' => $row['total_amount'],
            'created_at' => $row['created_at'],
            'checkout_date' => $checkout_date,
            'checkout_time' => $checkout_time,
            'checkout_datetime' => $checkout_datetime,
            'resort_checkout_date' => $resort_checkout_date,
            'resort_checkout_time' => $resort_checkout_time,
            'resort_checkout_datetime' => $resort_checkout_datetime
        ];
        
        // Add to all bookings
        $allBookings[] = $booking;
        
        // Categorize by reservation type
        switch ($row['reservation_type']) {
            case 'Room':
                $roomBookings[] = $booking;
                break;
            case 'Resort':
                $resortBookings[] = $booking;
                break;
            case 'Event':
                $eventBookings[] = $booking;
                break;
        }
    }
    
    // Return success response with categorized data
    echo json_encode([
        'success' => true,
        'total_count' => count($allBookings),
        'room_count' => count($roomBookings),
        'resort_count' => count($resortBookings),
        'event_count' => count($eventBookings),
        'all_bookings' => $allBookings,
        'room_bookings' => $roomBookings,
        'resort_bookings' => $resortBookings,
        'event_bookings' => $eventBookings
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>