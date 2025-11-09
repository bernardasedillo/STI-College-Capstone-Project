<!DOCTYPE html>
<?php
require_once '../includes/validate.php';
require '../includes/connect.php';
require '../includes/name.php';

if(!isset($_SESSION['role']) && $_SESSION['role'] == 'Staff'){
		header("location:home.php");
		exit();
	}

// Get current month and year
$month = date('m');
$year = date('Y');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Fetch total reservations for the month
$reservationQuery = $conn->query("
    SELECT COUNT(*) as total
    FROM reservations
    WHERE MONTH(checkin_date) = '$month' AND YEAR(checkin_date) = '$year'
");
$reservationResult = $reservationQuery->fetch_array();
$totalMonthlyReservations = $reservationResult['total'] ?? 0;

// Fetch daily reservations
$dailyReservations = [];
for ($day = 1; $day <= $daysInMonth; $day++) {
    $date = "$year-$month-" . str_pad($day, 2, "0", STR_PAD_LEFT);
    $query = $conn->query("
        SELECT COUNT(*) as total
        FROM reservations
        WHERE checkin_date = '$date'
    ");
    $result = $query->fetch_array();
    $dailyReservations[$day] = $result['total'] ?? 0;
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Renato's Place Private Resort and Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css" /> 
    <link rel="icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">   
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });
    </script>

    <style>
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
        .chart-container {
            position: relative;
            width: 100%;
            height: 60vh;
            margin-top: 30px;
        }
        .panel-body {
            padding: 20px;
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Reservations Overview</h3>
            </div>

            <div class="panel-body">
                <h3>Total Reservations for the Month: <?php echo $totalMonthlyReservations; ?></h3>

                <!-- Daily Reservations Table -->
                <h3>Daily Reservations</h3>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Number of Reservations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dailyReservations as $day => $total): ?>
                        <tr>
                            <td><?php echo $day; ?></td>
                            <td><?php echo $total; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Daily Reservations Chart -->
                <div class="chart-container">
                    <canvas id="dailyReservationsChart"></canvas>
                </div>

                <!-- Back Button -->
                <a href="home.php" class="btn btn-primary">
                    <i class="glyphicon glyphicon-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('dailyReservationsChart').getContext('2d');
    const dailyReservationsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($dailyReservations)); ?>,
            datasets: [{
                label: 'Reservations',
                data: <?php echo json_encode(array_values($dailyReservations)); ?>,
                backgroundColor: 'rgba(255, 159, 64, 0.6)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Daily Reservations for <?php echo date("F Y"); ?>',
                    font: { size: 18 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Reservations' }
                },
                x: {
                    title: { display: true, text: 'Day of the Month' }
                }
            }
        }
    });
</script>
</body>
</html>
