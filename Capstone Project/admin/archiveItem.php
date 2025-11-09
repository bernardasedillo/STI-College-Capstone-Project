<?php
session_start();
require_once '../includes/connect.php';
require_once 'log_activity.php';

header('Content-Type: application/json');

$inventoryFile = '../admin/Json/InventoryList.json';
$archivedInventoryFile = '../admin/Json/ArchivedInventory.json';

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['index'])) {
    $index = (int)$_POST['index'];

    // Load current inventory
    $inventory = file_exists($inventoryFile) ? json_decode(file_get_contents($inventoryFile), true) : [];

    if (isset($inventory[$index])) {
        $itemToArchive = $inventory[$index];

        // Check if quantity is 0 before archiving
        if ((int)$itemToArchive['quantity'] === 0) {
            // Load archived inventory
            $archivedInventory = file_exists($archivedInventoryFile) ? json_decode(file_get_contents($archivedInventoryFile), true) : [];

            // Add item to archived inventory
            $archivedInventory[] = $itemToArchive;

            // Remove item from current inventory
            array_splice($inventory, $index, 1);

            // Save updated inventories
            if (file_put_contents($inventoryFile, json_encode($inventory, JSON_PRETTY_PRINT)) &&
                file_put_contents($archivedInventoryFile, json_encode($archivedInventory, JSON_PRETTY_PRINT))) {
                
                log_activity($_SESSION['admin_id'], 'Inventory Management', 'Archived item: ' . $itemToArchive['itemName']);
                $response = ['success' => true, 'message' => 'Item archived successfully.'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to save inventory data.'];
            }
        } else {
            $response = ['success' => false, 'message' => 'Item quantity must be 0 to archive.'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Item not found.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request.'];
}

echo json_encode($response);
?>