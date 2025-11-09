<!DOCTYPE html>
<?php
	require_once '../includes/validate.php';
	require '../includes/name.php';
?>
<html lang="en">
<head>
	<title>Renato's Place Private Resort and Events</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" type="text/css" href="../assets/css/admin/bootstrap.css " />
	<link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css" />
	<link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
	<link rel="icon" href="../assets/favicon.ico">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

	
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<script src="../assets/js/admin/common.js"></script>
	<!-- Bootstrap Icons -->

	<style>
	/* Sidebar */
    #sidebar {
        width: 250px;
        height: 100%;
        position: fixed;
        left: 0;
        top: 0;
        background: #2a334b;
        transition: all 0.3s ease;
        overflow-x: hidden;
        z-index: 1000;
    }
    
    #sidebar.active {
        width: 0;
    }
    
    /* Page content */
    #content {
        margin-left: 250px;
        width: calc(100% - 250px);
        transition: all 0.3s ease;
        padding: 15px;
    }
    
    #content.expanded {
        margin-left: 0 !important;
        width: 100% !important;
    }
    
    /* Sidebar header */
    .sidebar-header {
        padding: 20px;
        color: #fff;
        background: #1f2738;
    }
    
    /* Button spacing */
    .panel-body .btn {
        margin: 5px 5px 5px 0;
    }
    
    /* Bottom bar */
    .navbar-fixed-bottom {
        padding: 10px 15px;
        background: #f8f8f8;
    }
    
    /* Prevent layout jump during transition */
    html, body {
        height: 100%;
        overflow-x: hidden;
    }
		
	</style>
</head>
<body>
	<!-- Sidebar -->
	<nav id="sidebar">
		<div class="sidebar-header"><h3>Renato's Place</h3></div>
		<ul class="list-unstyled components">
			<li><a href="home.php">Dashboard</a></li>
			<li><a href="reserve.php">Reservation</a></li>
			<?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Super Admin', 'Admin'])) { ?>
			<li><a href="inventory.php">Inventory</a></li>
			<li><a href="SalesRecord.php">Sales Record</a></li>
			<li><a href="settings.php">Settings</a></li>
			<li><a href="Allarchive.php">Archive</a></li>
			<?php } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'Event Manager') { ?>
			<li><a href="inventory.php">Inventory</a></li>
			<li><a href="SalesRecord.php">Sales Record</a></li>
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
						<a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
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

		<!-- Panel -->
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="alert alert-info">Reservation</div>
			     <div class="btn-group">
			     	<a href="reserve.php?view=pending" class="btn btn-info">Pending</a>
			     	<a href="reserve.php?view=checkin" class="btn btn-success">Check-in</a>
			     	<a href="reserve.php?view=checkout" class="btn btn-warning">Check-out</a>
			     	<a href="reserve.php?view=rescheduled" class="btn btn-primary">Re-Scheduled</a>
			     	<a href="reserve.php?view=manual_reserve" class="btn btn-primary">Make Reservation</a>
			     </div>
			     <br /><br />

			     <?php
			     	$view = isset($_GET['view']) ? $_GET['view'] : 'pending';

                    // Only show search for checkin, checkout, and rescheduled
                    if (in_array($view, ['checkin', 'checkout', 'rescheduled'])): ?>
                        <div class="mb-3" style="width: 250px; position: relative;">
                            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search..." style="padding-left: 30px;">
                            <i class="bi bi-search" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: #555;"></i>
                        </div>
                    <?php endif; ?>
					<br>
					
			     <?php
			     	switch($view){
			     		case 'pending':
			     			include 'pending_reservations.php';
			     			break;
			     		case 'checkin':
			     			include 'checkin_reservations.php';
			     			break;
			     		case 'checkout':
			     			include 'checkout_reservations.php';
			     			break;
			     		case 'rescheduled':
			     			include 'rescheduled_reservations.php';
			     			break;
			     		case 'manual_reserve':
			     			include 'admin_reservation.php';
			     			break;
			     		default:
			     			include 'pending_reservations.php';
			     	}
			     ?>			

			</div>
		</div>
	</div>

	<br /><br />
	<div style="text-align:right; margin-right:10px;" class="navbar navbar-default navbar-fixed-bottom">
		<label>&copy; Renato's Place Private Resort and Events </label>
	</div>

	<script>
$(document).ready(function(){
    // Sidebar toggle
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        $('#content').toggleClass('expanded');
    });

    // Automatically adjust content width when sidebar state changes
    function adjustContentWidth() {
        if ($('#sidebar').hasClass('active')) {
            $('#content').addClass('expanded');
        } else {
            $('#content').removeClass('expanded');
        }
    }

    // Observe sidebar changes and re-apply content sizing
    const sidebar = document.getElementById('sidebar');
    const observer = new MutationObserver(adjustContentWidth);
    observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });

    // Fix resizing when navigating between tabs (PHP includes)
    $(window).on('load resize', adjustContentWidth);
    $(document).on('ajaxComplete', adjustContentWidth);

    // Re-adjust when navigating to new tab (since PHP reloads content)
    $('a[href*="reserve.php"]').on('click', function() {
        setTimeout(adjustContentWidth, 400);
    });

    // Search bar filtering
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const tables = document.querySelectorAll('.panel-body table');
            tables.forEach(table => {
                const rows = table.getElementsByTagName('tr');
                for (let i = 1; i < rows.length; i++) { // skip header
                    const cells = rows[i].getElementsByTagName('td');
                    let match = false;
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].innerText.toLowerCase().includes(filter)) {
                            match = true;
                            break;
                        }
                    }
                    rows[i].style.display = match ? '' : 'none';
                }
            });
        });
    }
});
	</script>
	<script>
	$(document).ready(function(){
		const urlParams = new URLSearchParams(window.location.search);
		if (urlParams.has('success') && urlParams.get('success') == 'confirmed') {
			toastr.success('Reservation confirmed successfully!');
		} else if (urlParams.has('success') && urlParams.get('success') == 'deleted') {
			toastr.success('Reservation deleted successfully!');
		} else if (urlParams.has('error')) {
			toastr.error('Failed to confirm reservation.');
		}
	});
	</script>
</body>
</html>
