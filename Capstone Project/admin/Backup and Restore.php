<!DOCTYPE html>
<?php
    require_once '../includes/validate.php';
    require '../includes/name.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin'])) {
    header("location:home.php");
    exit();
}

// Database config
$host = "localhost"; 
$user = "u760753075_renatos_db";      
$pass = "Renatosplace#1920";          
$db   = "u760753075_renatos_db"; 

$message = "";

// Ensure backups folder exists
if (!file_exists("backups")) {
    mkdir("backups", 0777, true);
}

/* AUTO BACKUP EVERY 3 MONTHS */
$currentMonth = date("n"); 
$currentYear  = date("Y");
if ($currentMonth % 3 === 0) {
    $autoBackupFile = "auto_backup_{$currentYear}-{$currentMonth}.sql";
    $backupPath = "backups/" . $autoBackupFile;
    if (!file_exists($backupPath)) {
        $command = "mysqldump --user={$user} --password={$pass} --host={$host} {$db} > {$backupPath}";
        system($command, $output);
        if ($output === 0) $message = "✅ Automatic backup created: {$autoBackupFile}";
        else $message = "❌ Automatic backup failed!";
    }
}

/* MANUAL BACKUP */
if (isset($_POST['backup'])) {
    $backup_file = 'backup_' . date("Y-m-d_H-i-s") . '.sql';
    $command = "mysqldump --user={$user} --password={$pass} --host={$host} {$db} > backups/{$backup_file}";
    system($command, $output);
    if ($output === 0) $message = "✅ Backup created successfully: {$backup_file}";
    else $message = "❌ Backup failed!";
}

/* RESTORE DATABASE */
if (isset($_POST['restore'])) {
    if (!empty($_FILES['restore_file']['tmp_name'])) {
        $restore_file = $_FILES['restore_file']['tmp_name'];
        $command = "mysql --user={$user} --password={$pass} --host={$host} {$db} < {$restore_file}";
        system($command, $output);
        if ($output === 0) $message = "✅ Database restored successfully!";
        else $message = "❌ Restore failed!";
    } else {
        $message = "⚠️ Please upload a SQL file to restore.";
    }
}

/* DELETE BACKUP FILE */
if (isset($_POST['delete_file'])) {
    $file_to_delete = basename($_POST['delete_file']);
    $file_path = "backups/" . $file_to_delete;
    if (file_exists($file_path)) {
        unlink($file_path);
        $message = "🗑 Backup deleted: {$file_to_delete}";
    } else {
        $message = "❌ File not found!";
    }
}

// List backups
$backup_files = [];
if (file_exists("backups")) {
    $backup_files = array_diff(scandir("backups", SCANDIR_SORT_DESCENDING), ['.', '..']);
}
?>
<html lang="en">
<head>
    <title>Renato's Place Private Resort and Events</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/admin/bootstrap.css" />
    <link rel="stylesheet" href="../assets/css/admin/style.css" />
    <link rel="stylesheet" href="../assets/css/admin/panel.css" />
    <link rel="icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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

        <br><br>

        <div class="container-fluid">
            <div class="panel panel-primary">
                <div class="panel-heading"><h3 class="panel-title">Backup & Restore Database</h3></div>
                <div class="panel-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-info"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <a class="btn btn-primary" href="settings.php">
                            <i class="glyphicon glyphicon-arrow-left"></i> Back to Settings
                        </a>
                        <!-- ✅ MANUAL BACKUP BUTTON -->
                        <button type="submit" name="backup" class="btn btn-success pull-right">
                            <i class="glyphicon glyphicon-hdd"></i> Create Manual Backup
                        </button>
                    </form>

                    <hr>

                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Choose SQL file to restore:</label>
                            <input type="file" name="restore_file" class="form-control" required>
                        </div>
                        <button type="submit" name="restore" class="btn btn-warning">
                            <i class="glyphicon glyphicon-upload"></i> Restore Database
                        </button>
                    </form>

                    <hr>

                    <h4>📂 Available Backups</h4>
                    <?php if (!empty($backup_files)): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Size</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backup_files as $file): ?>
                                    <tr>
                                        <td><?php echo $file; ?></td>
                                        <td><?php echo round(filesize("backups/".$file)/1024, 2)." KB"; ?></td>
                                        <td><?php echo date("Y-m-d H:i:s", filemtime("backups/".$file)); ?></td>
                                        <td>
                                            <a class="btn btn-info btn-sm" href="backups/<?php echo $file; ?>" download>
                                                <i class="glyphicon glyphicon-download"></i> Download
                                            </a>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="delete_file" value="<?php echo $file; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this backup?')">
                                                    <i class="glyphicon glyphicon-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No backups found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <br><br><br>
        <div style="text-align:right; margin-right:10px;" class="navbar navbar-default navbar-fixed-bottom">
            <label>&copy; Renato's Place Private Resort and Events</label>
        </div>
    </div>
</div>

<script src="../admin/js/jquery-3.5.1.min.js"></script>
<script src="../admin/js/bootstrap.min.js"></script>
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
</body>
</html>
