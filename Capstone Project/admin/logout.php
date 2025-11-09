<?php
	session_start();
	require_once 'log_activity.php';

	if(isset($_SESSION['admin_id'])) {
		log_activity($_SESSION['admin_id'], 'Logout', 'Successful logout.');
	}
	unset($_SESSION['admin_id']);
	header(header: "location:index.php");
?>