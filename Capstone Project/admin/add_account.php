<!DOCTYPE html>
<?php
	require_once '../includes/validate.php';
    require '../includes/name.php';
	require_once 'add_query_account.php'
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
		
		<script>
		$(document).ready(function(){
			$('#save-button').on('click', function(){
				$('#confirmModal').modal('show');
			});

			$('#confirm-save').on('click', function(){
				$('#add_account_form').submit();
			});
		});
		</script>
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
        <!-- Top Navbar -->
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
				<div class = "alert alert-info">Account / Create Account</div>
				<br />
				<div class = "col-md-4">	
					<form method="POST" id="add_account_form">
						<input type="hidden" name="add_account" value="1">
						<div class = "form-group">
							<label>Name </label>
							<input type = "text" class = "form-control" name = "name" />
						</div>
						<div class = "form-group">
							<label>Username </label>
							<input type = "text" class = "form-control" name = "username" />
						</div>
						<div class = "form-group">
							<label>Password </label>
							<input type = "password" class = "form-control" name = "password" id="password" />
						</div>
						<div class = "form-group">
							<label>Confirm Password </label>
							<input type = "password" class = "form-control" name = "confirm_password" id="confirm_password" />
						</div>
						            <div class = "form-group">
							<label>Role </label>
							<select class = "form-control" name = "role" required>
								<option value="Staff">Staff</option>
								<option value="Event Manager">Event Manager</option>
							</select>
						</div>
						<br />
						<div class = "form-group">
							<button type="button" class="btn btn-info" id="save-button"><i class="glyphicon glyphicon-save"></i> Saved</button>
							<a class = "btn btn-primary" href = "account.php"><i class = "glyphicon glyphicon"></i> cancel</a>
						</div>
					</form>

					<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="confirmModalLabel">Confirm Account Creation</h4>
						  </div>
						  <div class="modal-body">
							Are you sure you want to create this account?
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-primary" id="confirm-save">Confirm</button>
						  </div>
						</div>
					  </div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<br />
	<br />
	<div style = "text-align:right; margin-right:10px;" class = "navbar navbar-default navbar-fixed-bottom">
		<label>&copy; Renato's Place Private Resort and Events </label>
	</div>
	<script>
	$(document).ready(function(){
		// Sidebar toggle
		$('#sidebarCollapse').on('click', function () {
			$('#sidebar').toggleClass('active');
		});
	});
	</script>
</body>
</html>