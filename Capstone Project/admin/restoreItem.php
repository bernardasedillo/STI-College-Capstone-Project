<?php
require_once 'log_activity.php';

$archiveFile = '../admin/Json/ArchivedInventory.json';
$inventoryFile = '../admin/Json/InventoryList.json';

$archive = file_exists($archiveFile) ? json_decode(file_get_contents($archiveFile), true) : [];
$inventory = file_exists($inventoryFile) ? json_decode(file_get_contents($inventoryFile), true) : [];

if (isset($_POST['index'])) {
    $index = (int)$_POST['index'];

    if (isset($archive[$index])) {
        $item = $archive[$index];
        unset($archive[$index]);

        // Add back to inventory
        $inventory[] = $item;

        file_put_contents($archiveFile, json_encode(array_values($archive), JSON_PRETTY_PRINT));
        file_put_contents($inventoryFile, json_encode($inventory, JSON_PRETTY_PRINT));

        echo json_encode(['success' => true, 'message' => 'Item restored successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found in archive.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>