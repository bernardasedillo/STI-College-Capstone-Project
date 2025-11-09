<?php
require '../includes/connect.php';

if (isset($_POST['date'])) {
    $date = $_POST['date'];

    $stmt = $conn->prepare("DELETE FROM rest_days WHERE date = ?");
    $stmt->bind_param("s", $date);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove rest day.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No date provided.']);
}
?>