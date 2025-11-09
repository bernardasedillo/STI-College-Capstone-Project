<?php
//submit_reservation.php
session_start();
require_once 'includes/connect.php';
ob_start(); // Prevent header output errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $proof_of_payment_path = null;

    // Handle file upload
    if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] == 0) {
        $target_dir = "uploads/";
        $imageFileType = strtolower(pathinfo($_FILES["proof_of_payment"]["name"], PATHINFO_EXTENSION));
        $new_file_name = uniqid('proof_', true) . '.' . $imageFileType;
        $proof_of_payment_path = $target_dir . $new_file_name;

        if (!move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $proof_of_payment_path)) {
            $_SESSION['toast_type'] = 'error';
            $_SESSION['toast_message'] = 'Error uploading proof of payment.';
            header("Location: reservation_overview.php");
            exit();
        }
    }

    // Extract common form data
    $reservation_type = $_POST['reservation_type'] ?? null;
    $full_name        = $_POST['full_name'] ?? null;
    $email            = $_POST['email'] ?? null;
    $phone            = $_POST['phone'] ?? $_POST['mobile'] ?? null;
    $full_address     = $_POST['full_address'] ?? '';
    $checkin_date     = $_POST['checkin'] ?? null;
    $special_requests = $_POST['message'] ?? null;
    $total_amount     = $_POST['total_amount'] ?? 0;
    $guests           = $_POST['guests'] ?? null;
    $payment_method   = $_POST['payment_method'] ?? null;

    // Initialize all possible variables
    $checkin_time          = null;
    $duration_hours        = null;
    $duration_minutes      = null;
    $room_duration         = null;
    $selected_rooms        = null;
    $resort_room           = null;
    $resort_room_duration  = null;
    $resort_package        = null;
    $events_package        = null;
    $event_type            = null;
    
    // Debug logging
    error_log("=== RESERVATION SUBMISSION DEBUG ===");
    error_log("Reservation Type: " . ($reservation_type ?? 'NULL'));
    error_log("RAW POST DATA: " . print_r($_POST, true));
    
    // ===== PROCESS BASED ON RESERVATION TYPE =====
    if ($reservation_type === 'Room') {
        // ===== ROOM RESERVATIONS =====
        $checkin_time = $_POST['checkin_time'] ?? null;
        $room_duration = $_POST['duration'] ?? $_POST['check_in_time'] ?? null;
        $selected_rooms = $_POST['selected_rooms'] ?? null;
        
        // Extract hours and minutes from duration if available
        if ($room_duration && preg_match('/(\d+)\s*hour/i', $room_duration, $matches)) {
            $duration_hours = (int)$matches[1];
            $duration_minutes = 0;
        }
        
        error_log("Room Reservation Data:");
        error_log("  - checkin_time: " . ($checkin_time ?? 'NULL'));
        error_log("  - duration: " . ($room_duration ?? 'NULL'));
        error_log("  - room_number: " . ($selected_rooms ?? 'NULL'));
        
    } else if ($reservation_type === 'Resort') {
        // ===== RESORT RESERVATIONS =====
        // Resort doesn't have a separate time field - time is in the package/duration text
        $checkin_time = null;
        
        // ✅ FIX 1: Get resort package from correct POST field
        $resort_package = $_POST['resort_package_selection'] ?? null;
        
        // Handle "none" selection
        if ($resort_package === 'none' || $resort_package === '') {
            $resort_package = null;
        }
        
        // ✅ FIX 2: Get duration from the form
        // The form sends duration ID in $_POST['duration']
        // We need to fetch the actual duration text/hours from database
        $duration_id = $_POST['duration'] ?? null;
        
        if ($duration_id && $duration_id !== '' && $duration_id !== 'package_duration') {
            // Fetch duration details from database
            $duration_stmt = $conn->prepare("SELECT duration_hours FROM prices WHERE id = ? AND venue = 'Resort'");
            $duration_stmt->bind_param("i", $duration_id);
            $duration_stmt->execute();
            $duration_result = $duration_stmt->get_result();
            
            if ($duration_row = $duration_result->fetch_assoc()) {
                $room_duration = $duration_row['duration_hours'];
                
                // Extract hours and minutes from duration text
                if (preg_match('/(\d+)\s*hour/i', $room_duration, $matches)) {
                    $duration_hours = (int)$matches[1];
                    $duration_minutes = 0;
                }
            }
            $duration_stmt->close();
        } else if ($resort_package) {
            // ✅ FIX 3: If package is selected, get duration from package
            $pkg_stmt = $conn->prepare("SELECT duration_hours FROM prices WHERE id = ? AND venue = 'Resort'");
            $pkg_stmt->bind_param("i", $resort_package);
            $pkg_stmt->execute();
            $pkg_result = $pkg_stmt->get_result();
            
            if ($pkg_row = $pkg_result->fetch_assoc()) {
                $room_duration = $pkg_row['duration_hours'];
                
                // Extract hours from package duration
                if (preg_match('/(\d+)\s*hour/i', $room_duration, $matches)) {
                    $duration_hours = (int)$matches[1];
                    $duration_minutes = 0;
                }
            }
            $pkg_stmt->close();
        }
        
        // ✅ FIX 4: Handle optional rooms for resort
        if (isset($_POST['selected_resort_rooms']) && !empty($_POST['selected_resort_rooms'])) {
            $resort_room = $_POST['selected_resort_rooms'];
            
            // Get room duration if available
            $resort_room_duration = $_POST['check_in_time'] ?? null;
        } else if (isset($_POST['room_number']) && is_array($_POST['room_number'])) {
            $resort_room = implode(',', $_POST['room_number']);
            $resort_room_duration = $_POST['check_in_time'] ?? null;
        }
        
        error_log("Resort Reservation Data:");
        error_log("  - checkin_time: " . ($checkin_time ?? 'NULL'));
        error_log("  - duration_hours: " . ($duration_hours ?? 'NULL'));
        error_log("  - duration_minutes: " . ($duration_minutes ?? 'NULL'));
        error_log("  - room_duration: " . ($room_duration ?? 'NULL'));
        error_log("  - resort_package: " . ($resort_package ?? 'NULL'));
        error_log("  - resort_room: " . ($resort_room ?? 'NULL'));
        error_log("  - resort_room_duration: " . ($resort_room_duration ?? 'NULL'));
        
} else if ($reservation_type === 'Event Package') {
        // ===== EVENT PACKAGE RESERVATIONS =====
        $events_package = $_POST['events_package_selection'] ?? $_POST['hidden_events_package'] ?? null;
        $event_type = $_POST['event_type'] ?? null;
        
        // Handle "none" selection
        if ($events_package === 'none' || $events_package === '') {
            $events_package = null;
        }
        
        // ✅ GET USER-INPUTTED TIME AND DURATION
        $checkin_time = $_POST['time'] ?? null;
        $duration_hours = isset($_POST['hours']) ? (int)$_POST['hours'] : (isset($_POST['hidden_duration_hours']) ? (int)$_POST['hidden_duration_hours'] : null);
        $duration_minutes = isset($_POST['minutes']) ? (int)$_POST['minutes'] : (isset($_POST['hidden_duration_minutes']) ? (int)$_POST['hidden_duration_minutes'] : null);
        
        // ✅ NEW FIX: Check if this event package includes resort (IDs: 22, 25-29, 32-43)
        $RESORT_INCLUSION_PACKAGE_IDS = [22, 25, 26, 27, 28, 29];
        for ($i = 32; $i <= 43; $i++) {
            $RESORT_INCLUSION_PACKAGE_IDS[] = $i;
        }
        
        if ($events_package && in_array((int)$events_package, $RESORT_INCLUSION_PACKAGE_IDS)) {
            // This event package includes resort - set resort_package to 57 and duration text
            $resort_package = 57;
            $room_duration = "9:00am - 7:00am"; // Overnight duration TEXT ONLY
            
            // ✅ DO NOT OVERRIDE duration_hours and duration_minutes
            // Keep the user's inputted values for checkin_time, duration_hours, duration_minutes
            
            error_log("✅ EVENT PACKAGE WITH RESORT INCLUSION DETECTED!");
            error_log("  - events_package: " . $events_package);
            error_log("  - AUTO-ADDED resort_package: 57");
            error_log("  - AUTO-ADDED duration TEXT: 9:00am - 7:00am");
            error_log("  - USER checkin_time: " . ($checkin_time ?? 'NULL'));
            error_log("  - USER duration_hours: " . ($duration_hours ?? 'NULL'));
            error_log("  - USER duration_minutes: " . ($duration_minutes ?? 'NULL'));
        }
        
        // Handle optional rooms for event package
        if (isset($_POST['selected_resort_rooms']) && !empty($_POST['selected_resort_rooms'])) {
            $resort_room = $_POST['selected_resort_rooms'];
            $resort_room_duration = $_POST['check_in_time'] ?? null;
        } else if (isset($_POST['room_number']) && is_array($_POST['room_number'])) {
            $resort_room = implode(',', $_POST['room_number']);
            $resort_room_duration = $_POST['check_in_time'] ?? null;
        }
        
        error_log("Event Package Reservation Data:");
        error_log("  - events_package: " . ($events_package ?? 'NULL'));
        error_log("  - event_type: " . ($event_type ?? 'NULL'));
        error_log("  - checkin_time: " . ($checkin_time ?? 'NULL'));
        error_log("  - duration_hours: " . ($duration_hours ?? 'NULL'));
        error_log("  - duration_minutes: " . ($duration_minutes ?? 'NULL'));
        error_log("  - resort_package: " . ($resort_package ?? 'NULL'));
        error_log("  - duration (text): " . ($room_duration ?? 'NULL'));
        error_log("  - resort_room: " . ($resort_room ?? 'NULL'));
        error_log("  - resort_room_duration: " . ($resort_room_duration ?? 'NULL'));
    }
    
    // ===== AFFILIATE SERVICES - FIXED TO GET NAME FROM DATABASE =====
    error_log("=== PROCESSING AFFILIATE SERVICES ===");
    error_log("Catering POST: " . ($_POST['catering'] ?? 'NOT SET'));
    error_log("Lights POST: " . ($_POST['lights'] ?? 'NOT SET'));
    error_log("Mobile Bar POST: " . ($_POST['mobile_bar'] ?? 'NOT SET'));
    error_log("Grazing Table POST: " . ($_POST['grazing_table'] ?? 'NOT SET'));
    
    $affiliate_caterer = null;
    $affiliate_lights = null;
    $affiliate_mobilebar = null;
    $affiliate_grazingtable = null;
    $additional_fee = null;
    $fee_names = [];
    
    // Helper function to get affiliate name by ID
    function getAffiliateName($conn, $id) {
        if (empty($id) || $id === 'none' || $id === '') {
            return null;
        }
        
        // If it's already a name (string), return it
        if (!is_numeric($id)) {
            error_log("Affiliate value is already a name: $id");
            return $id;
        }
        
        $stmt = $conn->prepare("SELECT name FROM prices WHERE id = ? AND venue = 'Affiliates'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $stmt->close();
            error_log("Found affiliate name from ID $id: " . $row['name']);
            return $row['name'];
        }
        
        $stmt->close();
        error_log("No affiliate found for ID: $id");
        return null;
    }
    
    // Process catering
    if (isset($_POST['catering']) && $_POST['catering'] !== '' && $_POST['catering'] !== 'none') {
        $affiliate_caterer = getAffiliateName($conn, $_POST['catering']);
        error_log("Catering - Input: " . $_POST['catering'] . " -> Result: " . ($affiliate_caterer ?? 'NULL'));
    }
    
    // Process lights
    if (isset($_POST['lights']) && $_POST['lights'] !== '' && $_POST['lights'] !== 'none') {
        $affiliate_lights = getAffiliateName($conn, $_POST['lights']);
        error_log("Lights - Input: " . $_POST['lights'] . " -> Result: " . ($affiliate_lights ?? 'NULL'));
    }
    
    // Process mobile bar
    if (isset($_POST['mobile_bar']) && $_POST['mobile_bar'] !== '' && $_POST['mobile_bar'] !== 'none') {
        $affiliate_mobilebar = getAffiliateName($conn, $_POST['mobile_bar']);
        error_log("Mobile Bar - Input: " . $_POST['mobile_bar'] . " -> Result: " . ($affiliate_mobilebar ?? 'NULL'));
    }
    
    // Process grazing table
    if (isset($_POST['grazing_table']) && $_POST['grazing_table'] !== '' && $_POST['grazing_table'] !== 'none') {
        $affiliate_grazingtable = getAffiliateName($conn, $_POST['grazing_table']);
        error_log("Grazing Table - Input: " . $_POST['grazing_table'] . " -> Result: " . ($affiliate_grazingtable ?? 'NULL'));
    }
    
    // Process manual additional fee checkboxes
    if (isset($_POST['additional_fee']) && is_array($_POST['additional_fee']) && count($_POST['additional_fee']) > 0) {
        // Filter out empty values
        $fees = array_filter($_POST['additional_fee'], function($value) {
            return !empty($value) && $value !== 'none';
        });
        
        if (!empty($fees)) {
            // Get names from database for each ID
            foreach ($fees as $fee_id) {
                $stmt_fee = $conn->prepare("SELECT name FROM prices WHERE id = ?");
                $stmt_fee->bind_param("i", $fee_id);
                $stmt_fee->execute();
                $result = $stmt_fee->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $fee_names[] = $row['name'];
                    error_log("Additional Fee - ID: $fee_id -> Name: " . $row['name']);
                }
                $stmt_fee->close();
            }
        }
    }
    
    // Combine all fees and remove duplicates
    if (!empty($fee_names)) {
        $fee_names = array_unique($fee_names); // Remove duplicates
        $additional_fee = implode(',', $fee_names);
        error_log("Final Additional Fee String: " . $additional_fee);
    }
    
    error_log("Additional Fee (final): " . ($additional_fee ?? 'NULL'));

    // Calculate payment
    $down_payment = $total_amount * 0.5;
    $balance = $total_amount - $down_payment;

    if (!$reservation_type) {
        $_SESSION['toast_type'] = 'error';
        $_SESSION['toast_message'] = 'Missing reservation type. Please check your form.';
        header("Location: reservation_overview.php");
        exit();
    }

    // Check if it's a rest day
    $rest_check = $conn->prepare("SELECT * FROM rest_days WHERE date = ?");
    $rest_check->bind_param("s", $checkin_date);
    $rest_check->execute();
    $rest_result = $rest_check->get_result();

    if ($rest_result->num_rows > 0) {
        $rest_day = $rest_result->fetch_assoc();
        $_SESSION['toast_type'] = 'error';
        $_SESSION['toast_message'] = "Sorry, the resort is closed on {$rest_day['date']} (Reason: {$rest_day['reason']}). Please choose another date.";
        header("Location: reservation_overview.php");
        exit();
    }

    // ===== INSERT RESERVATION =====
    $sql = "INSERT INTO reservations (
        reservation_type, full_name, email, phone, full_address,
        checkin_date, checkin_time, duration_hours, duration_minutes,
        guests, total_amount, proof_of_payment, payment_method,
        room_number, duration, resort_package, resort_room, resort_room_duration,
        events_package, event_type, additional_fee,
        affiliate_caterer, affiliate_lights, affiliate_mobilebar, affiliate_grazingtable,
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssiisdssssssssssssss",
        $reservation_type,        // s - string
        $full_name,               // s - string
        $email,                   // s - string
        $phone,                   // s - string
        $full_address,            // s - string
        $checkin_date,            // s - string
        $checkin_time,            // s - string
        $duration_hours,          // i - integer
        $duration_minutes,        // i - integer
        $guests,                  // s - string
        $total_amount,            // d - double
        $proof_of_payment_path,   // s - string
        $payment_method,          // s - string
        $selected_rooms,          // s - string (room_number for Room reservations)
        $room_duration,           // s - string (duration for Resort/Event with resort)
        $resort_package,          // s - string (resort_package for Resort + auto-added for Events)
        $resort_room,             // s - string (optional rooms for Resort/Event)
        $resort_room_duration,    // s - string (duration for optional rooms)
        $events_package,          // s - string (events_package for Event reservations)
        $event_type,              // s - string (event_type for Event reservations)
        $additional_fee,          // s - string (FIXED: now properly captured)
        $affiliate_caterer,       // s - string (FIXED: now gets name from DB)
        $affiliate_lights,        // s - string (FIXED: now gets name from DB)
        $affiliate_mobilebar,     // s - string (FIXED: now gets name from DB)
        $affiliate_grazingtable   // s - string (FIXED: now gets name from DB)
    );

    try {
        if ($stmt->execute()) {
            $reservation_id = $stmt->insert_id;
            $_SESSION['last_reservation_id'] = $reservation_id;

            // Insert billing record
            $billing_sql = "INSERT INTO billing (reservation_id, total_amount, down_payment, balance, status) 
                            VALUES (?, ?, ?, ?, 'Pending')";
            $billing_stmt = $conn->prepare($billing_sql);
            $billing_stmt->bind_param("iddd", $reservation_id, $total_amount, $down_payment, $balance);
            $billing_stmt->execute();

            $_SESSION['toast_type'] = 'success';
            $_SESSION['toast_message'] = 'Your reservation has been successfully submitted!';
            
            error_log("Reservation submitted successfully. ID: " . $reservation_id);
        } else {
            $_SESSION['toast_type'] = 'error';
            $_SESSION['toast_message'] = 'Error saving reservation. Please try again.';
            error_log("Error executing statement: " . $stmt->error);
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['toast_type'] = 'error';
        $_SESSION['toast_message'] = 'A database error occurred. Please try again later.';
        error_log("Reservation submission error: " . $e->getMessage());
    }

    $rest_check->close();
    $stmt->close();
    if (isset($billing_stmt)) $billing_stmt->close();
    $conn->close();

    header("Location: reservation_overview.php");
    exit();
}
?>