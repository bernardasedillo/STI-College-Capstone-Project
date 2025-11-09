<!DOCTYPE html>
<?php
    require_once '../includes/validate.php';
    require '../includes/name.php';

    if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin', 'Event Manager'])){
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
            padding: 10px;
	        margin-left: 230px;
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
            <?php } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'Event Manager') { ?>
            <li><a href="SalesRecord.php">Sales Record</a></li>
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
    <br /><br />
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="alert alert-info clearfix">
                    <span style="font-size:16px; font-weight:bold;">Sales Record</span>
                    <a href="export_sales.php" class="btn btn-success btn-sm pull-right">
                        <i class="glyphicon glyphicon-download-alt"></i> Export
                    </a>
                </div>
                
                <!-- Search bar -->
                 <div class="mb-3" style="width: 300px; position: relative;">
                     <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search..." style="padding-left: 30px;">
                     <i class="glyphicon glyphicon-search" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: #555;"></i>
                 </div>
                 <br>
                <table id="table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Reservation ID</th>
                            <th>Customer Name</th>
                            <th>Reservation Type</th>
                            <th>Check-in Date</th>
                            <th>Checkout Date</th>
                            <th>Total Amount</th>
                            <th>Balance</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            require '../includes/connect.php';
                            $query = $conn->query("
                                SELECT 
                                    r.id, 
                                    r.full_name, 
                                    r.reservation_type, 
                                    r.checkin_date, 
                                    r.checkout_date,
                                    b.total_amount, 
                                    b.down_payment, 
                                    b.balance, 
                                    b.payment_method,
                                    b.status AS billing_status 
                                FROM `reservations` r 
                                JOIN `billing` b ON r.id = b.reservation_id 
                                WHERE r.status = 'Checked-out' 
                                ORDER BY r.checkin_date DESC
                            ");
                            $total_sales = 0;
                            while($fetch = $query->fetch_array()){
                                $total_sales += $fetch['total_amount'];
                        ?>
                        <tr>
                            <td><?php echo $fetch['id']?></td>
                            <td><?php echo $fetch['full_name']?></td>
                            <td><?php echo $fetch['reservation_type']?></td>
                            <td><?php echo date("M d, Y", strtotime($fetch['checkin_date']))?></td>
                            <td><?php echo date("M d, Y", strtotime($fetch['checkout_date']))?></td>
                            <td>₱ <?php echo number_format($fetch['total_amount'], 2)?></td>
                            <td>₱ <?php echo number_format($fetch['balance'], 2)?></td>
                            <td><?php echo !empty($fetch['payment_method']) ? $fetch['payment_method'] : "N/A"; ?></td>
                            <td><?php echo $fetch['billing_status']?></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
                <div class="well">
                    <h3>Total Sales (Checked-out only): ₱ <?php echo number_format($total_sales, 2); ?></h3>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript for dynamic search -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('table');
    const rows = table.getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();

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
</script>
    <br />
    <br />
    <div style="text-align:right; margin-right:10px;" class="navbar navbar-default navbar-fixed-bottom">
        <label>&copy; Renato's Place Private Resort and Events </label>
    </div>
</body>
</html>
