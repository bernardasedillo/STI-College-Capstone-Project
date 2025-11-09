<?php
require_once '../includes/connect.php';
require_once 'log_activity.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_account'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'] ?? '';
    $admin_id = $_REQUEST['admin_id'];

    if ($password != $confirm_password) {
        echo "<center><label style='color:red;'>Passwords do not match</label></center>";
    } else {
        $role = $_POST['role'];
        $stmt = $conn->prepare("UPDATE `admin` SET `name` = ?, `username` = ?, `password` = ?, `role` = ? WHERE `admin_id` = ?");
        $stmt->bind_param("ssssi", $name, $username, $password, $role, $admin_id);

        if ($stmt->execute()) {
            log_activity($_SESSION['admin_id'], 'Account Management', 'Updated account for admin_id: ' . $admin_id . ', username: ' . $username . ', role: ' . $role);
            header("Location: account.php");
            exit();
        } else {
            die("Query Failed: " . $stmt->error);
        }

        $stmt->close();
    }
}
?>
