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
    
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css" />
    <link rel="icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>    
    <script>
        $(document).ready(function(){
            $('#save-button').on('click', function(){
                $('#confirmModal').modal('show');
            });

            $('#confirm-save').on('click', function(){
                $('#edit_account_form').submit();
            });
        });
    </script>
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

.glyphicon {
  font-family: 'Glyphicons Halflings' !important;
  font-style: normal;
  font-weight: normal;
  line-height: 1;
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
                <div class="alert alert-info">Account / Change Account</div>
                <?php
                    require '../includes/connect.php';
                    $query = $conn->query("SELECT * FROM `admin` WHERE `admin_id` = '$_REQUEST[admin_id]'");
                    $fetch = $query->fetch_array();
                ?>
                <br />
                <div class="col-md-4">    
                    <form method="POST" id="edit_account_form" action="edit_query_account.php?admin_id=<?php echo $fetch['admin_id']?>">
                        <div class="form-group">
                            <label>Name </label>
                            <input type="text" class="form-control" value="<?php echo $fetch['name']?>" name="name" />
                        </div>
                        <div class="form-group">
                            <label>Username </label>
                            <input type="text" class="form-control" value="<?php echo $fetch['username']?>" name="username" />
                        </div>
                        <div class="form-group">
                            <label>Password </label>
                            <input type="password" class="form-control" value="<?php echo $fetch['password']?>" name="password" />
                        </div>
                        <div class="form-group">
                            <label>Confirm Password </label>
                            <input type="password" class="form-control" name="confirm_password" />
                        </div>
                        <div class="form-group">
                            <label>Role </label>
                            <select class="form-control" name="role" required>
                                <option value="Staff" <?php if($fetch['role'] == 'Staff') echo 'selected'; ?>>Staff</option>
                                <option value="Event Manager" <?php if($fetch['role'] == 'Event Manager') echo 'selected'; ?>>Event Manager</option>
                            </select>
                        </div>
                        <!-- Hidden field to trigger PHP check -->
                        <input type="hidden" name="edit_account" value="1">
                        <br />
                        <div class="form-group">
                            <button type="button" class="btn btn-warning" id="save-button">
                                <i class="glyphicon glyphicon-edit"></i> Save Changes
                            </button>
                            <a class="btn btn-primary" href="account.php">
                                <i class="glyphicon glyphicon"></i> Cancel
                            </a>
                        </div>
                    </form>

                    <!-- Confirmation Modal -->
                    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="confirmModalLabel">Confirm Account Edit</h4>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to save these changes?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="confirm-save">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->

                </div>
            </div>
        </div>
    </div>

    <br /><br />
    <div style="text-align:right; margin-right:10px;" class="navbar navbar-default navbar-fixed-bottom">
        <label>&copy; Renato's Place Private Resort and Events </label>
    </div>
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
