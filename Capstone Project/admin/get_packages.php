<?php
require '../includes/connect.php';

if (isset($_GET['type'])) {
    $type = $_GET['type'];
    $packages = [];

    if ($type == 'Room') {
        $res = $conn->query("SELECT id, name, price FROM prices WHERE venue='Room' AND is_archived=0");
    } elseif ($type == 'Resort') {
        $res = $conn->query("SELECT id, name, price FROM prices WHERE venue='Resort' AND is_archived=0");
    } elseif ($type == 'Event Package') {
        $res = $conn->query("SELECT id, name, price FROM prices WHERE (venue LIKE '%Hall%' OR venue LIKE '%Pavilion%') AND is_archived=0");
    }

    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $packages[] = $row;
        }
    }

    echo json_encode($packages);
}
?>
