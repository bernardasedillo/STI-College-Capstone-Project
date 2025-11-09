<?php
require_once 'log_activity.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = 'Json/InventoryList.json';

    $newItem = [
        "itemName" => $_POST['itemName'],
        "itemType" => $_POST['itemType'],
        "dateAdded" => date('Y-m-d'),
        "quantity" => (int)$_POST['quantity'],
        "description" => $_POST['description']
    ];

    $inventory = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    $inventory[] = $newItem;

    file_put_contents($file, json_encode($inventory, JSON_PRETTY_PRINT));
    log_activity($_SESSION['admin_id'], 'Inventory Management', 'Added new item: ' . $newItem['itemName'] . ' (Type: ' . $newItem['itemType'] . ', Quantity: ' . $newItem['quantity'] . ')');

    header("Location: inventory.php?success=added");
    exit;
}
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
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/inventorymodule.css" />
    <link rel="icon" href="../assets/favicon.ico">

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        /* Sidebar & content layout */
        #sidebar {
            width: 250px;
            transition: all 0.3s ease;
        }
        #sidebar.active {
            width: 80px; /* collapsed width */
        }
        #content {
            transition: margin-left 0.3s;
            margin-left: 250px; /* default sidebar width */
            padding: 20px;
        }
        #sidebar.active + #content {
            margin-left: 80px; /* when sidebar collapsed */
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
        <br>
        <br>
        <div class="card mt-3">
            <div class="card-header">
                <h2>Add New Item</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input name="itemName" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Item Type</label>
                        <input name="itemType" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Date Added</label>
                        <input type="date" name="dateAdded" required class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input name="quantity" type="number" min="0" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <button class="btn btn-success" type="submit">Add Item</button>
                    <a href="inventory.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });
});
</script>
</body>
</html>
