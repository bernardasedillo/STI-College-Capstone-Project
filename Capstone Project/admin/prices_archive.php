<?php
// admin/prices_archive.php
require_once '../includes/validate.php';
require '../includes/name.php';
require_once 'log_activity.php';
if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin'])){
	header("location:home.php");
	exit();
}

// ✅ Handle restore request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'restore') {
    $response = ["success" => false, "error" => ""];

    if (!empty($_POST['restore_ids']) && is_array($_POST['restore_ids'])) {
        $ids = array_map('intval', $_POST['restore_ids']);
        $idsStr = implode(",", $ids);

        if ($idsStr) {
            $sql = "UPDATE prices SET is_archived = 0 WHERE id IN ($idsStr)";
            if ($conn->query($sql)) {
                $response['success'] = true;
                log_activity($_SESSION['admin_id'], 'Price Management', 'Restored price records with IDs: ' . $idsStr);
            } else {
                $response['error'] = "Database update failed: " . $conn->error;
            }
        }
    } else {
        $response['error'] = "No records selected.";
    }

    echo json_encode($response);
    exit();
}

// ✅ Fetch archived prices grouped by venue
$prices = [];
$query = $conn->query("SELECT * FROM prices WHERE is_archived = 1 ORDER BY venue, id ASC");
if ($query) {
    while ($row = $query->fetch_assoc()) {
        $prices[$row['venue']][] = $row;
    }
} else {
    echo "<div class='alert alert-danger'>Failed to fetch archived prices: " . htmlspecialchars($conn->error) . "</div>";
}
?>
<html lang="en">
<head>
    <title>Renato's Place Private Resort and Events</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/bootstrap.css " />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css" /> 
    <link rel="icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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

        /* Search bar icon */
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
      <a class="btn btn-primary" href="Allarchive.php"> <i class="glyphicon glyphicon-arrow-left"></i> Back to Archive Menu </a>  
      <br><br>
      <div class="alert alert-info">Archived Prices</div>
      <div id="updateMessages"></div>

      <!-- Search bar -->
      <div class="search-wrapper">
          <input type="text" id="searchInput" class="form-control input-sm" placeholder="Search prices...">
          <span class="glyphicon glyphicon-search"></span>
      </div>

      <?php if (empty($prices)): ?>
        <div class="alert alert-info">No archived prices found.</div>
      <?php else: ?>
        <?php foreach ($prices as $venue => $items): ?>
          <h4 class="mt-3"><?php echo htmlspecialchars($venue); ?></h4>
          <table class="table table-bordered archive-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Duration</th>
                <th>Day Type</th>
                <th>Price (₱)</th>
                <th>Notes</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $row): ?>
                <tr data-id="<?php echo (int)$row['id']; ?>">
                  <td><?php echo htmlspecialchars($row['name']); ?></td>
                  <td><?php echo htmlspecialchars($row['duration']); ?></td>
                  <td><?php echo htmlspecialchars($row['day_type']); ?></td>
                  <td><?php echo number_format((float)$row['price'], 2, '.', ''); ?></td>
                  <td><?php echo htmlspecialchars($row['notes']); ?></td>
                  <td>
                    <button type="button" class="btn btn-success btn-sm restoreBtn">Restore</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </div>
</div>

<!-- Modals -->
<div class="modal fade" id="confirmModal"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h4 class="modal-title">Confirm Restore</h4></div>
    <div class="modal-body">Are you sure you want to restore this record?</div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
      <button type="button" class="btn btn-success" id="confirmYes">Yes</button>
    </div>
</div></div></div>

<div class="modal fade" id="successModal"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h4 class="modal-title">Restore Successful</h4></div>
    <div class="modal-body">The record has been restored successfully.</div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" id="reloadPage">OK</button>
    </div>
</div></div></div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="../assets/js/admin/common.js"></script>
<script>
$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () { $('#sidebar').toggleClass('active'); });

    let selectedId = null;
    $(".restoreBtn").click(function(){
        let row = $(this).closest("tr");
        selectedId = row.data("id");
        showToastrConfirm(this, "Are you sure you want to restore this record?");
    });

    function confirmAction(isConfirmed, url, actionType, fetchOptions) {
        if (isConfirmed) {
            if (!selectedId) return;
            $.post("prices_archive.php", { ajax: "restore", restore_ids: [selectedId] }, function(response){
                try {
                    let res = JSON.parse(response);
                    if (res.success) {
                        toastr.success("The record has been restored successfully.");
                        setTimeout(function(){ location.reload(); }, 2000);
                    } else {
                        toastr.error(res.error || "Failed to restore record.");
                    }
                } catch(e) {
                    toastr.error("Unexpected response:<br>"+response);
                }
            });
        }
        toastr.clear();
    }

    // Search functionality
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
