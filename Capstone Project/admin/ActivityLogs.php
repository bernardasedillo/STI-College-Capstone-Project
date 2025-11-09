<!-- ActivityLogs.php -->
<!DOCTYPE html>
<?php
    require_once '../includes/validate.php';
    require '../includes/name.php';

    if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin'])){
        header("location:home.php");
        exit();
    }
?>
<html lang="en">
<head>
    <title>Renato's Place Private Resort and Events</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
    <link rel="icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />

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
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-header">Activity Logs</h2>
                        <a href="settings.php" class="btn btn-primary" style="margin-bottom: 15px;">
                            <i class="glyphicon glyphicon-arrow-left"></i> Back to Settings
                        </a>
                        <div class="table-responsive">
                            <table id="activityLogTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    require '../includes/connect.php';
                                    date_default_timezone_set('Asia/Manila');
                                    $query = $conn->query("SELECT activity_logs.*, admin.name, reservations.full_name FROM `activity_logs` LEFT JOIN `admin` ON activity_logs.user_id = admin.admin_id LEFT JOIN `reservations` ON activity_logs.details LIKE CONCAT('%ID: ', reservations.id, '%') ORDER BY `timestamp` DESC");
                                    while($fetch = $query->fetch_array()){
                                        $details = $fetch['details'];
                                        if ($fetch['full_name']) {
                                            $details .= "<br>Guest: " . $fetch['full_name'];
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <?php 
                                                echo date('Y-m-d h:i:s A', strtotime($fetch['timestamp'] ?? 'now')); 
                                            ?>
                                        </td>
                                        <td><?php echo $fetch['name']?></td>
                                        <td><?php echo $fetch['action']?></td>
                                        <td><?php echo $details?></td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br />
    <br />
    <div style="text-align:right; margin-right:10px;" class="navbar navbar-default navbar-fixed-bottom">
        <label>&copy; Renato's Place Private Resort and Events </label>
    </div>
</body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#activityLogTable').DataTable();

    // Sidebar toggle
		$('#sidebarCollapse').on('click', function () {
			$('#sidebar').toggleClass('active');
		});
});
</script>
</html>
