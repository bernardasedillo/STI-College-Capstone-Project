<!DOCTYPE html>
<?php
	require_once '../includes/validate.php';
    require '../includes/name.php';
    require '../includes/connect.php';

	if(!isset($_SESSION['role']) || $_SESSION['role'] != 'Super Admin'){
		header("location:home.php");
		exit();
	}
?>
<html lang = "en">
	<head>
		<title>Renato's Place Private Resort and Events</title>
		<meta charset = "utf-8" />
		<meta name = "viewport" content = "width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<link rel = "stylesheet" type = "text/css" href = "../assets/css/admin/style.css" />
		<link rel = "stylesheet" type = "text/css" href = "../assets/css/admin/panel.css" />
		<link rel="icon" href="../assets/favicon.ico">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

		<style>
		/* Fixed sidebar */
		#sidebar {
			width: 250px;
			height: 100%;
			position: fixed;
			left: 0;
			top: 0;
			background: #2a334b;
			transition: all 0.3s ease;
			overflow: hidden;
			z-index: 1000;
		}

		#sidebar.active {
			width: 0;
		}

		/* Page content */
		#content {
			padding: 20px;
			margin-left: 250px; /* same as sidebar width */
			transition: all 0.3s ease;
			width: calc(100% - 250px); /* auto shrink when sidebar open */
		}

		/* When sidebar is collapsed */
		#sidebar.active ~ #content {
			margin-left: 0;
			width: 100%;
		}

		/* Panel buttons spacing */
		.panel-body .btn {
			margin: 5px 5px 5px 0;
		}

		/* Smooth bottom navbar */
		.navbar-fixed-bottom {
			padding: 10px 15px;
			background: #f8f8f8;
		}

		/* Sidebar header */
		.sidebar-header {
			padding: 20px;
			color: #fff;
			background: #1f2738;
		}

		/* Sidebar links */
		#sidebar ul.components {
			padding: 0;
			list-style: none;
		}
		#sidebar ul li a {
			display: block;
			padding: 10px 20px;
			color: #fff;
			text-decoration: none;
		}
		#sidebar ul li a:hover {
			background: #1f2738;
		}
	</style>
	</head>
  <body>
	<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header"><h3>Renato's Place</h3></div>
        <ul class="list-unstyled components">
            <li><a href="home.php">Dashboard</a></li>
            <li><a href="reserve.php">Reservation</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <?php if ($_SESSION['role'] == 'Super Admin') { ?>
            <li><a href="SalesRecord.php">Sales Record</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="Allarchive.php">Archive</a></li>
            <?php } ?>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" id="sidebarCollapse" class="btn btn-info navbar-btn">
                        <i class="glyphicon glyphicon-align-left"></i> Menu
                    </button>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <i class="glyphicon glyphicon-user"></i> <?php echo $name; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="logout.php"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
	<br />
	<div class = "container-fluid">
		<div class = "panel panel-default">
			<div class = "panel-body">
				<div class = "alert alert-info">Accounts</div>
				<a class = "btn btn-success" href = "add_account.php"><i class = "glyphicon glyphicon-plus"></i> Create New Account</a>
				<a class = "btn btn-primary" href = "settings.php"><i class = "glyphicon glyphicon-arrow-left"></i> Back to Settings</a>
				<br /><br />
				<table id = "table" class = "table table-bordered">
					<thead>
						<tr>
							<th>Name</th>
							<th>Username</th>
							<th>Role</th>
							<th>Password</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php  
							// Show only active accounts
							$query = $conn->query("SELECT * FROM `admin` WHERE `status` = 'active'");
							while($fetch = $query->fetch_array()){
						?>
						<tr>
							<td><?php echo $fetch['name']?></td>
							<td><?php echo $fetch['username']?></td>
							<td><?php echo isset($fetch['role']) ? $fetch['role'] : 'N/A'?></td>
							<td>********</td> <!-- Hide real password -->
							<td>
								<center>
									<a class = "btn btn-warning" href = "edit_account.php?admin_id=<?php echo $fetch['admin_id']?>">
										<i class = "glyphicon glyphicon-edit"></i> Edit
									</a> 
									<a class = "btn btn-danger" onclick = "return showToastrConfirm(this, 'Are you sure you want to archive this account?');" href = "archive_account.php?admin_id=<?php echo $fetch['admin_id']?>">
										<i class = "glyphicon glyphicon-remove"></i> Archive
									</a>
								</center>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<br /><br />
	<div style = "text-align:right; margin-right:10px;" class = "navbar navbar-default navbar-fixed-bottom">
		<label>&copy; Renato's Place Private Resort and Events </label>
	</div>
</body>
<script src = "../admin/js/jquery.dataTables.js"></script>
<script src = "../admin/js/dataTables.bootstrap.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="../assets/js/admin/common.js"></script>	
<script type = "text/javascript">
	$(document).ready(function(){
		$("#table").DataTable();
	});
</script>
<script>
	$(document).ready(function(){
		// Sidebar toggle
		$('#sidebarCollapse').on('click', function () {
			$('#sidebar').toggleClass('active');
		});
	});
	</script>
	<script>
	$(document).ready(function(){
		const urlParams = new URLSearchParams(window.location.search);
		if (urlParams.get('success') === 'archived') {
			toastr.success('Account archived successfully!');
		}
	});
	</script>
</html>
