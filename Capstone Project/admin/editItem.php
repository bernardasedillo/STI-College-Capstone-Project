<?php
require_once '../includes/validate.php';
require '../includes/name.php';
require_once 'log_activity.php';

$index = isset($_GET['index']) ? (int)$_GET['index'] : -1;
$file = '../admin/Json/InventoryList.json';
$inventory = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

if ($index < 0 || $index >= count($inventory)) {
    die("Invalid item index.");
}

$oldItem = $inventory[$index];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inventory[$index] = [
        "itemName" => $_POST['itemName'],
        "itemType" => $_POST['itemType'],
        "dateAdded" => $_POST['dateAdded'],
        "quantity" => (int)$_POST['quantity'],
        "description" => $_POST['description']
    ];

    file_put_contents($file, json_encode($inventory, JSON_PRETTY_PRINT));
    log_activity($_SESSION['admin_id'], 'Inventory Management', 
        'Edited item from: ' . $oldItem['itemName'] . ' (Type: ' . $oldItem['itemType'] . ', Quantity: ' . $oldItem['quantity'] . ') to: ' . 
        $inventory[$index]['itemName'] . ' (Type: ' . $inventory[$index]['itemType'] . ', Quantity: ' . $inventory[$index]['quantity'] . ')');

    $success = true;
}
$item = $inventory[$index];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit Item - Renato's Place</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <!-- ✅ Bootstrap 3 (matching JS + CSS) -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <link rel="stylesheet" href="../assets/css/admin/style.css" />
  <link rel="stylesheet" href="../assets/css/admin/panel.css" />  
  <link rel="stylesheet" href="../admin/css/inventorymodule.css" />
  <link rel="icon" href="../assets/favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <style>
    body { overflow-x: hidden; }
    #wrapper {
      display: flex;
      width: 100%;
      align-items: stretch;
      transition: all 0.3s ease;
    }
    #sidebar {
      width: 250px;
      transition: all 0.3s ease;
    }
    #sidebar.active { margin-left: -250px; }
    #content {
      width: 100%;
      padding: 20px;
      transition: all 0.3s ease;
    }
    #sidebar.active ~ #content { margin-left: 0; width: 100%; }

    .modal-header-success {
      background-color: #5cb85c;
      color: white;
    }
  </style>
</head>
<body>
<div id="wrapper">
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

        <!-- Edit Item Panel -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Edit Item</h3>
            </div>
            <div class="panel-body">
                <form method="POST" id="editForm">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input name="itemName" value="<?= htmlspecialchars($item['itemName']) ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Item Type</label>
                        <input name="itemType" value="<?= htmlspecialchars($item['itemType']) ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Date Added</label>
                        <input name="dateAdded" type="date" value="<?= htmlspecialchars($item['dateAdded']) ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input name="quantity" type="number" min="1" value="<?= (int)$item['quantity'] ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"><?= htmlspecialchars($item['description']) ?></textarea>
                    </div>

                    <button type="button" class="btn btn-success" id="saveBtn">Save Changes</button>
                    <a href="inventory.php" class="btn btn-primary">
                        <i class="glyphicon glyphicon-arrow-left"></i> Back to Inventory
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirm Save</h4>
      </div>
      <div class="modal-body">
        Are you sure you want to save these changes?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirmSave">Yes, Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header modal-header-success">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="glyphicon glyphicon-ok"></i> Success</h4>
      </div>
      <div class="modal-body">
        Changes saved successfully!
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
    // Sidebar toggle
    $("#sidebarCollapse").click(function () {
        $("#sidebar").toggleClass("active");
        $("#content").toggleClass("active");
    });

    // Show confirm modal
    $("#saveBtn").click(function () {
        $("#confirmModal").modal("show");
    });

    // Confirm Save → Submit form
    $("#confirmSave").click(function () {
        $("#confirmModal").modal("hide");
        $("#editForm").submit();
    });

    // Show success toastr if saved
    <?php if ($success): ?>
        toastr.success('Changes saved successfully!');
        setTimeout(function () {
            window.location.href = "inventory.php";
        }, 2000);
    <?php endif; ?>
});
</script>
</body>
</html>
