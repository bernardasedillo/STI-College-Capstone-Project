<?php
	require '../includes/connect.php';
	$query = $conn->query("SELECT * FROM `admin` WHERE `admin_id` = '$_SESSION[admin_id]'");
	$fetch = $query->fetch_array();

	if ($fetch) {
		$name = $fetch['name'];
		$_SESSION['role'] = isset($fetch['role']) ? $fetch['role'] : 'Super Admin';
	} else {
		// Handle case where admin_id is not found or session is invalid
		$name = 'Doms'; // Default name
		$_SESSION['role'] = 'Super Admin'; // Default role
		// Optionally, redirect to login page if not logged in
		// header("location: login.php");
		// exit();
	}
?>