<?php
$inventoryFile = '../admin/Json/InventoryList.json';
$inventory = file_exists($inventoryFile) ? json_decode(file_get_contents($inventoryFile), true) : [];
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
  <link rel="stylesheet" href="../assets/css/admin/bootstrap.css" />
  <link rel="stylesheet" href="../assets/css/admin/style.css" />
  <link rel="stylesheet" href="../assets/css/admin/panel.css" />
  <link rel="stylesheet" href="../assets/css/admin/inventorymodule.css" />
  <link rel="icon" href="../assets/favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
        <br>
      <!-- Inventory Panel -->
       <div class="panel panel-default">
           <div class="panel-body">
           <h2 class="mb-4">Inventory</h2>
        <div class="mb-3">
            <a href="addItem.php" class="btn btn-primary">Add New Item</a>
            <a href="archive.php" class="btn btn-success">View Archived Items</a>
        </div>
        <br>

        <!-- Search bar with icon -->
        <div class="mb-3" style="width: 250px; position: relative;">
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search inventory..." style="padding-left: 30px;">
            <i class="bi bi-search" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: #555;"></i>
        </div>
        <br>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Item Name</th>
                    <th>Item Type</th>
                    <th>Date Added</th>
                    <th>Quantity</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($inventory)): ?>
                    <?php foreach ($inventory as $index => $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['itemName']) ?></td>
                            <td><?= htmlspecialchars($item['itemType']) ?></td>
                            <td><?= htmlspecialchars($item['dateAdded']) ?></td>
                            <td><?= (int)$item['quantity'] ?></td>
                            <td><?= htmlspecialchars($item['description']) ?></td>
                            <td>
                                <form action="editItem.php" method="GET" style="display:inline;">
                                    <input type="hidden" name="index" value="<?= $index ?>">
                                    <button type="submit" class="btn btn-sm btn-warning">Edit</button>
                                </form>
                                <button class="btn btn-sm btn-danger archive-btn" data-index="<?= $index ?>" <?php if ((int)$item['quantity'] > 0) { echo 'disabled title="Item quantity must be 0 to archive"'; } ?>>Archive</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No inventory items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="modal fade" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirm Archive</h4>
      </div>
      <div class="modal-body">
        Are you sure you want to archive this item?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmArchiveBtn">Archive</button>
      </div>
    </div>
  </div>
</div>

<!-- Sidebar Toggle Script -->
<script>
$(document).ready(function () {
    $("#sidebarCollapse").on("click", function () {
        $("#sidebar").toggleClass("active");
    });
});
</script>
<!-- JavaScript for dynamic search -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.querySelector('.panel-body table');
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
    <script>
    $(document).ready(function(){
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === 'added') {
            toastr.success('Item added successfully!');
        }

        var confirmCallback; // To store the callback function for the confirmation modal

        function showConfirmationModal(message, callback) {
            $('#confirmationModal .modal-body').text(message);
            $('#confirmArchiveBtn').off('click').on('click', function() {
                $('#confirmationModal').modal('hide');
                callback();
            });
            $('#confirmationModal').modal('show');
        }

        $('.archive-btn').on('click', function() {
    var button = $(this);
    var index = button.data('index');
    var itemName = button.closest('tr').find('td:first').text();

    showConfirmationModal("Are you sure you want to archive " + itemName + "? This action cannot be undone.", function() {
        $.ajax({
            url: 'archiveItem.php',
            type: 'POST',
            data: { index: index },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Remove the row from the table
                    button.closest('tr').remove();

                    // ✅ Redirect after a short delay
                    setTimeout(function() {
                        window.location.href = 'inventory.php';
                    }, 1500);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error("An error occurred while trying to archive the item.");
            }
        });
    });
});
    });
    </script>
</body>
</html>
