<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/validate.php';
require '../includes/name.php';
require '../includes/connect.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin'])) {
    header("location:home.php");
    exit();
}

$archiveFile = '../json files/archive-questions-answers.json';
$mainFile = '../json files/questions-answers.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore'])) {
    $selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : [];

    if (!empty($selectedCategories)) {
        $archiveData = json_decode(file_get_contents($archiveFile), true);
        $mainData = json_decode(file_get_contents($mainFile), true);

        foreach ($selectedCategories as $category) {
            if (isset($archiveData[$category])) {
                $mainData[$category] = $archiveData[$category];
                unset($archiveData[$category]);
            }
        }

        file_put_contents($archiveFile, json_encode($archiveData, JSON_PRETTY_PRINT));
        file_put_contents($mainFile, json_encode($mainData, JSON_PRETTY_PRINT));

        header("location: archive_adminchatbot.php?success=restored");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Renato's Place Private Resort and Events</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/bootstrap.css "/>
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css"/>
    <link rel="icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
    <script>
    $(document).ready(function(){
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === 'restored') {
            toastr.success('Chatbot content restored successfully!');
                      // ✅ Redirect to inventory after short delay
          setTimeout(function() {
            window.location.href = 'archive_adminchatbot.php';
          }, 1500);
        }
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
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-body">
      <a class="btn btn-primary" href="Allarchive.php"> <i class="glyphicon glyphicon-arrow-left"></i> Back to Archive Menu </a>  
      <br>
      <br> 
                    <div class="alert alert-info">Archived Chatbot Contents</div>
                    <br/><br/>
                    <form method="POST" action="">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Select</th>
                                <th>Category</th>
                                <th>Questions</th>
                                <th>Answer</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $archiveData = json_decode(file_get_contents($archiveFile), true);
                            if (json_last_error() === JSON_ERROR_NONE && !empty($archiveData)) {
                                foreach ($archiveData as $category => $data) {
                                    echo '<tr>';
                                    echo '<td><input type="checkbox" name="categories[]" value="' . htmlspecialchars($category) . '"></td>';
                                    echo '<td>' . htmlspecialchars($category) . '</td>';
                                    echo '<td>' . htmlspecialchars(implode(', ', $data['questions'])) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['answer']) . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4">No archived categories found.</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                        <button type="submit" name="restore" class="btn btn-primary">Restore</button>
                    </form>
                </div>
            </div>
        </div>
        <br/>
        <br/>
        <br/>
        <div style="text-align:right; margin-right:10px;" class="navbar navbar-default navbar-fixed-bottom">
            <label>&copy; Renato's Place Private Resort and Events </label>
        </div>
    </div>
</div>
</body>
</html> 