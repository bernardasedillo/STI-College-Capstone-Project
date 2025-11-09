<!DOCTYPE html>
<?php
	require_once '../includes/validate.php';
    require '../includes/name.php';
    require '../includes/connect.php';

	if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin'])){
		header("location:home.php");
		exit();
	}
?>
<html lang = "en">
	<head>
		<title>Renato's Place Private Resort and Events</title>
		<meta charset = "utf-8" />
		<meta name = "viewport" content = "width=device-width, initial-scale=1.0" />
		<link rel = "stylesheet" type = "text/css" href = "../assets/css/admin/bootstrap.css " />
		<link rel = "stylesheet" type = "text/css" href = "../assets/css/admin/style.css" />
		<link rel = "stylesheet" type = "text/css" href = "../assets/css/admin/panel.css" />
		<link rel="icon" href="../assets/favicon.ico">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function () {

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

});
</script>
<style>
		/* Ensure content resizes when sidebar is toggled */
		#wrapper {
			display: flex;
		}

		#sidebar {
			width: 250px;
			transition: all 0.3s ease;
		}

		#sidebar.active {
			width: 0;
			overflow: hidden;
		}

		#content {
			width: 100%;
			transition: margin-left 0.3s;
			padding: 20px;
		}

		#sidebar.active + #content {
			margin-left: 0;
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
            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Super Admin', 'Admin'])) { ?>
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
	<br>
	<div class = "container-fluid">
		<div class = "panel panel-default">
			<div class = "panel-body">
				<div class = "alert alert-info">Archived</div>
				<a href = "archive.php" class = "btn btn-info">Archived Item</a>
				<a href = "prices_archive.php" class = "btn btn-info">Archived Prices</a>
				<a href = "archive_adminchatbot.php" class = "btn btn-info">Archived Chatbot Contents</a>
				<a href = "archive_websiteContent.php" class = "btn btn-info">Archived Website Contents</a>
				<a href = "archive_checkout.php" class = "btn btn-info">Archived Check-out</a>
				<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'Super Admin') { ?>
				<a href = "account_archive.php" class = "btn btn-info">Archived Account</a>
				<?php } ?>
			</div>
		</div>
	</div>
	<br />
	<br />
	<br />
	<div style = "text-align:right; margin-right:10px;" class = "navbar navbar-default navbar-fixed-bottom">
		<label>&copy; Renato's Place Private Resort and Events </label>
		</div>
</body>
</html>