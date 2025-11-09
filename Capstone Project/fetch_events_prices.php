<?php
require 'includes/connect.php';

// Always return JSON
header("Content-Type: application/json; charset=UTF-8");

// If package_id is provided → return package details + venue excess + guest excess
if (isset($_GET['package_id'])) {
    $packageId = intval($_GET['package_id']);

    // Fetch selected package
    $stmt = $conn->prepare("
    SELECT id, venue, price, name, max_guest 
    FROM prices 
    WHERE id = ? AND is_archived = 0
");
    $stmt->bind_param("i", $packageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $package = $result->fetch_assoc();
    $stmt->close();

    if ($package) {
        // Fetch excess hours rates
        $sqlExcess = "SELECT venue, price 
                      FROM prices 
                      WHERE name = 'Excess Rate' AND is_archived = 0";
        $res = $conn->query($sqlExcess);

        $excessRates = [];
        while ($row = $res->fetch_assoc()) {
            $excessRates[$row['venue']] = (float)$row['price'];
        }

// ✅ Fetch guest excess rates
$sqlGuestExcess = "
    SELECT venue, name, price, notes 
    FROM prices 
    WHERE name = 'Guest Excess Rate' 
    AND is_archived = 0
";
$resGuest = $conn->query($sqlGuestExcess);

$guestExcessRates = [];
while ($row = $resGuest->fetch_assoc()) {
    $guestExcessRates[$row['venue']][] = [
        "name"  => $row['name'],
        "price" => (float)$row['price'],
        "notes" => $row['notes']
    ];
}

        echo json_encode([
            "success" => true,
            "package" => $package,
            "excessRates" => $excessRates,
            "guestExcessRates" => $guestExcessRates
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Package not found"]);
    }

    $conn->close();
    exit;
}

// Otherwise return all packages (for populating dropdown)
$sql = "SELECT id, venue, name, day_type, duration, price, notes 
        FROM prices 
        WHERE is_archived = 0";
$result = $conn->query($sql);

$prices = [];
while ($row = $result->fetch_assoc()) {
    $prices[] = $row;
}

$conn->close();
echo json_encode($prices);
exit;