<?php
	require_once 'log_activity.php';

	if(ISSET($_POST['login'])){
		$username = $_POST['username'];
		$password = $_POST['password'];
		$query = $conn->query("SELECT * FROM `admin` WHERE `username` = '$username' and `password` = '$password'");
		$fetch = $query->fetch_array();
		$row = $query->num_rows;
		
		if($row > 0){
			$_SESSION['admin_id'] = $fetch['admin_id'];
			log_activity($fetch['admin_id'], 'Login', 'Successful login.');
			echo ('<script>location.replace("home.php")</script>');
		}else{
			log_activity(0, 'Login Failed', 'Attempted login with username: ' . $username);
			echo "<center><labe style = 'color:red;'>Invalid username or password</label></center>";
		}
	}
?>