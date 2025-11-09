<?php
require_once 'log_activity.php';
$inventoryFile = '../admin/Json/InventoryList.json';
$archiveFile = '../admin/Json/ArchivedInventory.json';

// Archive logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['archiveIndex'])) {
        $inventory = file_exists($inventoryFile) ? json_decode(file_get_contents($inventoryFile), true) : [];
        $archived = file_exists($archiveFile) ? json_decode(file_get_contents($archiveFile), true) : [];

        $index = $input['archiveIndex'];

        if (isset($inventory[$index])) {
            $itemToArchive = $inventory[$index];
            $archived[] = $itemToArchive;
            array_splice($inventory, $index, 1);

            file_put_contents($inventoryFile, json_encode($inventory, JSON_PRETTY_PRINT));
            file_put_contents($archiveFile, json_encode($archived, JSON_PRETTY_PRINT));
            log_activity($_SESSION['admin_id'], 'Inventory Management', 'Archived item: ' . $itemToArchive['itemName'] . ' (Type: ' . $itemToArchive['itemType'] . ')');
            echo json_encode(['success' => true]);
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Invalid index']);
        exit;
    }

    if (isset($input['restoreIndex'])) {
        $inventory = file_exists($inventoryFile) ? json_decode(file_get_contents($inventoryFile), true) : [];
        $archived = file_exists($archiveFile) ? json_decode(file_get_contents($archiveFile), true) : [];

        $index = $input['restoreIndex'];

        if (isset($archived[$index])) {
            $restoredItem = $archived[$index];
            $inventory[] = $restoredItem;
            array_splice($archived, $index, 1);

            file_put_contents($inventoryFile, json_encode($inventory, JSON_PRETTY_PRINT));
            file_put_contents($archiveFile, json_encode($archived, JSON_PRETTY_PRINT));
            log_activity($_SESSION['admin_id'], 'Inventory Management', 'Restored item: ' . $restoredItem['itemName'] . ' (Type: ' . $restoredItem['itemType'] . ')');

            echo json_encode(['success' => true]);
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Invalid index']);
        exit;
    }
}

$archived = file_exists($archiveFile) ? json_decode(file_get_contents($archiveFile), true) : [];
?>

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
  
  <link rel="stylesheet" type="text/css" href="../assets/css/admin/bootstrap.css" />
  <link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css" /> 
  <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
  <link rel="icon" href="../assets/favicon.ico">
  <link rel="stylesheet" type="text/css" href="../assets/css/admin/inventorymodule.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

  <script>
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
  </script>
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
<br/>
<div class="container-fluid">
  <div class="panel panel-default">
    <div class="panel-heading d-flex justify-content-between align-items-center">
      <h3 class="panel-title mb-0">Archived Inventory Items</h3>
      <a href="Allarchive.php" class="btn btn-primary btn-sm">Back to Archive Menu</a>
    </div>
    <div class="panel-body">

      <!-- Search bar with icon -->
      <div class="mb-3" style="width: 300px; position: relative;">
          <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search archived items..." style="padding-left: 30px;">
          <i class="glyphicon glyphicon-search" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: #555;"></i>
      </div>
      <br>
      <!-- Table responsive wrapper -->
      <div class="table-responsive">
        <table class="table table-bordered" id="archiveTable">
          <thead class="thead-dark">
            <tr>
              <th>Item Name</th>
              <th>Item Type</th>
              <th>Date Added</th>
              <th>Quantity</th>
              <th>Description</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($archived)): ?>
              <?php foreach ($archived as $index => $item): ?>
                <tr>
                  <td><?= htmlspecialchars($item['itemName']) ?></td>
                  <td><?= htmlspecialchars($item['itemType']) ?></td>
                  <td><?= htmlspecialchars($item['dateAdded']) ?></td>
                  <td><?= (int)$item['quantity'] ?></td>
                  <td><?= htmlspecialchars($item['description']) ?></td>
                  <td>
                    <button class="btn btn-sm btn-success restore-btn" data-index="<?= $index ?>">Restore</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center">No archived items found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div id="restoreConfirmationModal" class="modal fade" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirm Restore</h4>
      </div>
      <div class="modal-body">
        Are you sure you want to restore this item to the inventory?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmRestoreBtn">Restore</button>
      </div>
    </div>
  </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="../assets/js/admin/common.js"></script>
<script>
$(document).ready(function () {
    // Sidebar toggle
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar, #content').toggleClass('active');
    });

    // Restore button
    var restoreIndex = null;

  // Show modal when restore is clicked
  $('.restore-btn').on('click', function() {
    restoreIndex = $(this).data('index');
    var itemName = $(this).closest('tr').find('td:first').text();

    $('#restoreConfirmationModal .modal-body').text(
      "Are you sure you want to restore " + itemName + " back to inventory?"
    );
    $('#restoreConfirmationModal').modal('show');
  });

  // Confirm restore
  $('#confirmRestoreBtn').on('click', function() {
    if (restoreIndex === null) return;

    $.ajax({
      url: 'restoreItem.php',
      type: 'POST',
      data: { index: restoreIndex },
      dataType: 'json',
      success: function(response) {
        $('#restoreConfirmationModal').modal('hide');

        if (response.success) {
          toastr.success(response.message);
          $('button[data-index="' + restoreIndex + '"]').closest('tr').remove();

          // ✅ Redirect to inventory after short delay
          setTimeout(function() {
            window.location.href = 'archive.php';
          }, 1500);
        } else {
          toastr.error(response.message);
        }
      },
      error: function() {
        $('#restoreConfirmationModal').modal('hide');
        toastr.error("An error occurred while restoring the item.");
      }
    });
  });

    // Table search filter
    $('#searchInput').on('keyup', function () {
        const filter = $(this).val().toLowerCase();
        $('#archiveTable tbody tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(filter) > -1)
        });
    });
});
</script>
</body>
</html>
