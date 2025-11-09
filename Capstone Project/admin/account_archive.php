<!DOCTYPE html>
<?php
	require_once '../includes/validate.php';
    require '../includes/name.php';
    require '../includes/connect.php';

	if (isset($_SESSION['role']) && $_SESSION['role'] == 'Staff') {
		header("location:home.php");
		exit();
	}

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
	<link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
	<link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css" />
	<link rel="stylesheet" type="text/css" href="../assets/css/admin/dataTables.bootstrap.css" />
	<link rel="icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<script src="../assets/js/admin/common.js"></script>

	<style>
    /* Flex wrapper for sidebar + content */
    #wrapper {
        display: flex;
    }

    /* Sidebar */
    #sidebar {
        width: 250px;
        transition: all 0.3s ease;
        
    }
    #sidebar.active {
        width: 0; /* shrink sidebar completely */
        overflow: hidden; /* hide links when collapsed */
    }
    

    /* Page content fills remaining space */
    #content {
        width: 100%;
		transition: margin-left 0.3s;
		padding: 20px;
    }
    #sidebar.active + #content {
    margin-left: 0;       /* auto expand when sidebar hidden */
}

    /* Optional: panel max width */
    .container .panel {
        width: 100%;
    }

    /*new  */
    /* Wrapper: make children flex items */
#wrapper {
  display: flex;
  width: 100%;
  height: 100vh;      /* optional: keeps layout full height */
  overflow: hidden;
}

/* Sidebar (flex item) */
#sidebar {
  flex: 0 0 250px;            /* fixed 250px when open */
  width: 250px;
  transition: flex-basis .25s ease, width .25s ease, padding .25s ease;
  background: #2a334b;
  border-right: 1px solid #ddd;
}

/* Collapsed sidebar */
#sidebar.active {
  flex: 0 0 0;                /* shrink to 0 */
  width: 0;
  min-width: 0;
  padding: 0;                 /* hide internal paddings */
  overflow: hidden;
}

/* Content (critical: min-width:0 allows it to shrink/grow) */
#content {
  flex: 1 1 auto;
  min-width: 0;               /* <<-- very important for flex children */
  padding: 20px;
  transition: all .25s ease;
  box-sizing: border-box;
}

/* Make panel/table use full available width */
.container .panel {
  width: 100%;
  box-sizing: border-box;
}

/* If you keep .container, remove its max-width so it can expand */
.container {
  max-width: 100% !important;
  width: 100% !important;
  padding-left: 15px;
  padding-right: 15px;
}

/* Optional: keep tables scrollable when narrow */
.table-responsive { overflow: auto; }
</style>

</head>
<body>
<div class="wrapper" id="wrapper">
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
					<div class="alert alert-info">Archived Accounts</div>
					<table id="table" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Name</th>
								<th>Username</th>
								<th>Role</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$query = $conn->query("SELECT * FROM `admin` WHERE `status` = 'archived'");
								while($fetch = $query->fetch_array()){
							?>
							<tr>
								<td><?php echo $fetch['name']?></td>
								<td><?php echo $fetch['username']?></td>
								<td><?php echo isset($fetch['role']) ? $fetch['role'] : 'N/A'?></td>
								<td>
									<a class="btn btn-success" onclick="return showToastrConfirm(this, 'Are you sure you want to restore this account?');" href="restore_account.php?admin_id=<?php echo $fetch['admin_id']?>">
										<i class="glyphicon glyphicon-refresh"></i> Restore
									</a>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<a class="btn btn-primary" href="Allarchive.php"><i class="glyphicon glyphicon-arrow-left"></i> Back to Archive Menu</a>
				</div>
			</div>
		</div>
		<br /><br />

		<div style="text-align:right; margin-right:10px;" class="navbar navbar-default navbar-fixed-bottom">
			<label>&copy; Renato's Place Private Resort and Events </label>
		</div>
    </div>
</div>

<script src="../admin/js/jquery.dataTables.js"></script>
<script src="../admin/js/dataTables.bootstrap.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#table").DataTable();
	});
</script>
<script>
$(document).ready(function() {
    $('#sidebarCollapse').on('click', function() {
        $('#sidebar').toggleClass('active');

        // trigger window resize after the transition so components (charts, datatables) recalc
        setTimeout(function() {
            $(window).trigger('resize');
        }, 300); // match transition duration
    });
	});
</script>
<script>
$(document).ready(function(){
	const urlParams = new URLSearchParams(window.location.search);
	if (urlParams.get('success') === 'restored') {
		toastr.success('Account restored successfully!');
	}
});
</script>
</body></html>
