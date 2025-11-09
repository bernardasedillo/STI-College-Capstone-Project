<?php
require '../includes/connect.php';
$rest_days = [];

$query = $conn->query("SELECT date FROM rest_days");
while ($row = $query->fetch_assoc()) {
    $rest_days[] = $row['date'];
}

header('Content-Type: application/json');
echo json_encode($rest_days);
?>