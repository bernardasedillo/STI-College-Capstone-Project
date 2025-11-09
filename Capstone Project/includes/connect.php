<?php
	// Replace the following with your Hostinger database credentials
	$hostname = "localhost";
	$username = "u760753075_renatos_db";
	$password = "Renatosplace#1920";
	$database = "u760753075_renatos_db";

	$conn = new mysqli($hostname, $username, $password, $database);
	
	if ($conn->connect_error) {
		die("We are currently experiencing technical difficulties. Please try again later.");
	}
?>