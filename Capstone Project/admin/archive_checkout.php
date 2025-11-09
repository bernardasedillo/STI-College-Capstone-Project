<?php
require_once '../includes/validate.php';
require '../includes/name.php';
require '../includes/connect.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin'])) {
    header("location:home.php");
    exit();
}

// ✅ Auto-archive all checked-out reservations older than 3 months
$conn->query("
    UPDATE reservations 
    SET is_archived = 1 
    WHERE status = 'checked-out' 
      AND (is_archived = 0 OR is_archived IS NULL)
      AND checkout_date <= DATE_SUB(NOW(), INTERVAL 3 MONTH)
");

// ✅ Fetch archived checked-out reservations
$reservations = [];
$query = $conn->query("
    SELECT r.*, 
           b.total_amount, 
           b.down_payment, 
           b.balance, 
           b.status AS billing_status 
    FROM reservations r 
    LEFT JOIN billing b ON r.id = b.reservation_id 
    WHERE r.status = 'checked-out' AND r.is_archived = 1
    ORDER BY r.checkout_date DESC
");

if ($query) {
    while ($row = $query->fetch_assoc()) {
        $reservations[] = $row;
    }
} else {
    echo "<div class='alert alert-danger'>Failed to fetch records: " . htmlspecialchars($conn->error) . "</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Archived Check-out | Renato's Place Private Resort and Events</title>
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
    <style>
        #wrapper { display: flex; }
        #sidebar { width: 250px; transition: all 0.3s ease; }
        #sidebar.active { width: 0; overflow: hidden; }
        #content { width: 100%; transition: margin-left 0.3s; padding: 20px; }
        #sidebar.active + #content { margin-left: 0; }

        .search-wrapper { position: relative; width: 300px; margin-bottom: 15px; }
        .search-wrapper input { padding-left: 30px; }
        .search-wrapper .glyphicon-search {
            position: absolute; left: 8px; top: 50%; transform: translateY(-50%);
            color: #555;
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

<div class="container-fluid">
  <div class="panel panel-default">
    <div class="panel-body">
      <a class="btn btn-primary" href="Allarchive.php"> 
        <i class="glyphicon glyphicon-arrow-left"></i> Back to Archive Menu 
      </a>  
      <br><br>
      <div class="alert alert-info">Archived Check-out (Auto-archived after 3 months)</div>

      <div class="search-wrapper">
          <input type="text" id="searchInput" class="form-control input-sm" placeholder="Search reservations...">
          <span class="glyphicon glyphicon-search"></span>
      </div>

      <?php if (empty($reservations)): ?>
        <div class="alert alert-info">No archived check-out records found.</div>
      <?php else: ?>
        <table id="table" class="table table-bordered archive-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Reservation Type</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Status</th>
                    <th>Total Amount</th>
                    <th>Billing Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $fetch): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fetch['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($fetch['reservation_type']); ?></td>
                    <td><?php echo date("M d, Y", strtotime($fetch['checkin_date'])); ?></td>
                    <td><?php echo date("M d, Y", strtotime($fetch['checkout_date'])); ?></td>
                    <td><?php echo htmlspecialchars($fetch['status']); ?></td>
                    <td><?php echo $fetch['total_amount'] ? '₱ ' . number_format($fetch['total_amount'], 2) : ''; ?></td>
                    <td><?php echo htmlspecialchars($fetch['billing_status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script>
$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () { $('#sidebar').toggleClass('active'); });

    // 🔍 Search functionality
    $("#searchInput").on("keyup", function(){
        const value = $(this).val().toLowerCase();
        $(".archive-table tbody tr").filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
</script>
</body>
</html>
