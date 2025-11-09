<?php
// This file contains the database connection logic.

require_once '../includes/connect.php';
require_once 'log_activity.php';

if (isset($_POST['add_account'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Check if passwords match
    if ($password != $confirm_password) {
        echo "<center><label style='color:red;'>Passwords do not match</label></center>";
    } else {
        // Check if username already exists
        $stmt_check = $conn->prepare("SELECT * FROM `admin` WHERE `username` = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo "<center><label style='color:red;'>Username already taken</label></center>";
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO `admin` (name, username, password, role) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $name, $username, $password, $role);

            if ($stmt_insert->execute()) {
                log_activity($_SESSION['admin_id'], 'Account Management', 'Created new account for user: ' . $username . ' with role: ' . $role);
                header("Location: account.php");
                exit();
            } else {
                echo "<script>alert('An unexpected database error occurred. Please try again later.'); window.location.href = 'account.php';</script>";
            }

            $stmt_insert->close();
        }

        $stmt_check->close();
    }
}
?>
