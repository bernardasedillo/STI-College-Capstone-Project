<?php
require 'includes/connect.php';
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['category'])) {
    $category = $_GET['category'];

    if (strcasecmp($category, "Additional Venue Fee") === 0) {
        // Fetch additional venue fees with price
        $stmt = $conn->prepare("
            SELECT id, name, price 
            FROM prices 
            WHERE venue = 'Affiliates' 
              AND LOWER(notes) = LOWER(?) 
              AND is_archived = 0 
            ORDER BY id ASC
        ");
    } else {
        // Normal affiliates
        $stmt = $conn->prepare("
            SELECT id, name 
            FROM prices 
            WHERE venue = 'Affiliates' 
              AND notes = ? 
              AND is_archived = 0 
            ORDER BY id ASC
        ");
    }

    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

if (strcasecmp($category, "Additional Venue Fee") === 0) {
    $output = "";
    while ($row = $result->fetch_assoc()) {
        $priceFormatted = "₱" . number_format($row['price'], 2);
        $output .= "
        <label style='display:block; margin-bottom:5px;'>
            <input type='checkbox' name='additional_fee[]' value='{$row['id']}' data-price='{$row['price']}'>
            {$row['name']} - ₱".number_format($row['price'], 2)."
        </label>

        ";
    }
    echo $output;

    } else {
        // Dropdown options
        $options = "<option value='' disabled selected>Select $category (Optional)</option>";
        $options .= "<option value='none'>None</option>";

        while ($row = $result->fetch_assoc()) {
            $options .= "<option value='{$row['id']}'>{$row['name']}</option>";
        }

        echo $options;
    }
    exit;
}
?>