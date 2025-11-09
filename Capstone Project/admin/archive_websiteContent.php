<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    require_once '../includes/validate.php';
    require '../includes/name.php';
    require_once 'log_activity.php';

    if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin'])){
        header("location:home.php");
        exit();
    }

    $content_archive_file = '../admin/content_config_archive.json';

    // Function to read content from JSON file
    function readContent($file) {
        if (file_exists($file) && is_readable($file)) {
            $json_content = file_get_contents($file);
            if ($json_content === false) {
                error_log("Failed to read content from $file.");
                return [];
            }
            $data = json_decode($json_content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decoding error in $file: " . json_last_error_msg());
                return [];
            }
            return $data;
        }
        error_log("Content file $file does not exist or is not readable.");
        return [];
    }

    // Function to write content to JSON file
    function writeContent($file, $data) {
        if (file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)) === false) {
            error_log("Failed to write content to $file.");
        }
    }

    // Function to get correct image path based on content type
function getImagePath($image, $type = 'default') {
    switch($type) {
        case 'gallery':
            // Gallery images are stored in admin/image/gallery/
            return '../assets/images/admin/gallery/' . $image;
        case 'room':
        case 'venue':
            return '../' . $image;
        case 'slide':
        case 'service':
        case 'offer':
        case 'affiliate':
        default:
            return '../assets/images/' . $image;
    }
}



    $archived_content = readContent($content_archive_file);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];
        $index = $_POST['index'] ?? null;
        $content_file = '../admin/content_config.json';
        $content_data = readContent($content_file);

        switch ($action) {
            case 'restore_service':
                $content_data['home']['services'][] = $archived_content['home']['services'][$index];
                unset($archived_content['home']['services'][$index]);
                $archived_content['home']['services'] = array_values($archived_content['home']['services']);
                break;

            case 'restore_slide':
                $content_data['home']['slides'][] = $archived_content['home']['slides'][$index];
                unset($archived_content['home']['slides'][$index]);
                $archived_content['home']['slides'] = array_values($archived_content['home']['slides']);
                break;

            case 'restore_offer':
                $content_data['home']['offers'][] = $archived_content['home']['offers'][$index];
                unset($archived_content['home']['offers'][$index]);
                $archived_content['home']['offers'] = array_values($archived_content['home']['offers']);
                break;

            case 'restore_affiliate':
                $content_data['home']['affiliates'][] = $archived_content['home']['affiliates'][$index];
                unset($archived_content['home']['affiliates'][$index]);
                $archived_content['home']['affiliates'] = array_values($archived_content['home']['affiliates']);
                break;

            case 'restore_room':
                $content_data['rooms']['list'][] = $archived_content['rooms']['list'][$index];
                unset($archived_content['rooms']['list'][$index]);
                $archived_content['rooms']['list'] = array_values($archived_content['rooms']['list']);
                break;

            case 'restore_venue':
                $content_data['venues']['list'][] = $archived_content['venues']['list'][$index];
                unset($archived_content['venues']['list'][$index]);
                $archived_content['venues']['list'] = array_values($archived_content['venues']['list']);
                break;

            case 'restore_gallery_image':
                $content_data['gallery']['images'][] = $archived_content['gallery']['images'][$index];
                unset($archived_content['gallery']['images'][$index]);
                $archived_content['gallery']['images'] = array_values($archived_content['gallery']['images']);
                break;
        }

        writeContent($content_file, $content_data);
        writeContent($content_archive_file, $archived_content);
        header("Location: archive_websiteContent.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Renato's Place Private Resort and Events</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css" />
    <link rel="icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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
                <div class="alert alert-info">Archived Website Content</div>
                <a class="btn btn-primary" href="Allarchive.php"><i class="glyphicon glyphicon-arrow-left"></i> Back to Archives</a>
                <br><br>
                <?php if (empty($archived_content)): ?>
                    <p>No archived content found.</p>
                <?php else: ?>

                    <?php if (!empty($archived_content['home']['slides'])): ?>
                        <h4>Archived Slides</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr><th>Slide Text</th><th>Image</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($archived_content['home']['slides'] as $index => $slide): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($slide['text']); ?></td>
                                        <td>
                                            <?php 
                                            $imgPath = getImagePath($slide['image'], 'slide');
                                            if (file_exists($imgPath)): ?>
                                                <img src="<?php echo htmlspecialchars($imgPath); ?>" style="max-width: 100px; max-height: 100px;" alt="Slide Image">
                                            <?php else: ?>
                                                <span class="text-danger">Image not found: <?php echo htmlspecialchars($imgPath); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><button class="btn btn-success btn-sm restore-slide" data-index="<?php echo $index; ?>">Restore</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (!empty($archived_content['home']['services'])): ?>
                        <h4>Archived Services</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr><th>Title</th><th>Image</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($archived_content['home']['services'] as $index => $service): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($service['title']); ?></td>
                                        <td>
                                            <?php 
                                            $imgPath = getImagePath($service['image'], 'service');
                                            if (file_exists($imgPath)): ?>
                                                <img src="<?php echo htmlspecialchars($imgPath); ?>" style="max-width: 100px; max-height: 100px;" alt="Service Image">
                                            <?php else: ?>
                                                <span class="text-danger">Image not found: <?php echo htmlspecialchars($imgPath); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><button class="btn btn-success btn-sm restore-service" data-index="<?php echo $index; ?>">Restore</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (!empty($archived_content['home']['offers'])): ?>
                        <h4>Archived Offers</h4>
                        <table class="table table-bordered">
                            <thead><tr><th>Image</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($archived_content['home']['offers'] as $index => $offer): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $imgPath = getImagePath($offer['image'], 'offer');
                                            if (file_exists($imgPath)): ?>
                                                <img src="<?php echo htmlspecialchars($imgPath); ?>" style="max-width: 100px; max-height: 100px;" alt="Offer Image">
                                            <?php else: ?>
                                                <span class="text-danger">Image not found: <?php echo htmlspecialchars($imgPath); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><button class="btn btn-success btn-sm restore-offer" data-index="<?php echo $index; ?>">Restore</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (!empty($archived_content['home']['affiliates'])): ?>
                        <h4>Archived Affiliates</h4>
                        <table class="table table-bordered">
                            <thead><tr><th>Title</th><th>Image</th><th>Modal ID</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($archived_content['home']['affiliates'] as $index => $affiliate): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($affiliate['title']); ?></td>
                                        <td>
                                            <?php 
                                            $imgPath = getImagePath($affiliate['image'], 'affiliate');
                                            if (file_exists($imgPath)): ?>
                                                <img src="<?php echo htmlspecialchars($imgPath); ?>" style="max-width: 100px; max-height: 100px;" alt="Affiliate Image">
                                            <?php else: ?>
                                                <span class="text-danger">Image not found: <?php echo htmlspecialchars($imgPath); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($affiliate['modal_id']); ?></td>
                                        <td><button class="btn btn-success btn-sm restore-affiliate" data-index="<?php echo $index; ?>">Restore</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (!empty($archived_content['rooms']['list'])): ?>
                        <h4>Archived Rooms</h4>
                        <table class="table table-bordered">
                            <thead><tr><th>Title</th><th>Image</th><th>Description</th><th>Features</th><th>Price</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($archived_content['rooms']['list'] as $index => $room): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($room['title']); ?></td>
                                        <td>
                                            <?php 
                                            $imgPath = getImagePath($room['img'], 'room');
                                            if (file_exists($imgPath)): ?>
                                                <img src="<?php echo htmlspecialchars($imgPath); ?>" style="max-width: 100px; max-height: 100px;" alt="Room Image">
                                            <?php else: ?>
                                                <span class="text-danger">Image not found: <?php echo htmlspecialchars($imgPath); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($room['desc']); ?></td>
                                        <td><?php echo htmlspecialchars(implode(', ', $room['features'])); ?></td>
                                        <td><?php echo htmlspecialchars($room['price']); ?></td>
                                        <td><button class="btn btn-success btn-sm restore-room" data-index="<?php echo $index; ?>">Restore</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (!empty($archived_content['venues']['list'])): ?>
                        <h4>Archived Venues</h4>
                        <table class="table table-bordered">
                            <thead><tr><th>Title</th><th>Image</th><th>Description</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($archived_content['venues']['list'] as $index => $venue): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($venue['title']); ?></td>
                                        <td>
                                            <?php 
                                            $imgPath = getImagePath($venue['img'], 'venue');
                                            if (file_exists($imgPath)): ?>
                                                <img src="<?php echo htmlspecialchars($imgPath); ?>" style="max-width: 100px; max-height: 100px;" alt="Venue Image">
                                            <?php else: ?>
                                                <span class="text-danger">Image not found: <?php echo htmlspecialchars($imgPath); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($venue['desc']); ?></td>
                                        <td><button class="btn btn-success btn-sm restore-venue" data-index="<?php echo $index; ?>">Restore</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (!empty($archived_content['gallery']['images'])): ?>
                        <h4>Archived Gallery Images</h4>
                        <table class="table table-bordered">
                            <thead><tr><th>Image</th><th>Caption</th><th>Category</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($archived_content['gallery']['images'] as $index => $image): ?>
                                    <?php if (is_array($image)): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $imgPath = getImagePath($image['filename'] ?? '', 'gallery');
                                            if (file_exists($imgPath)): ?>
                                                <img src="<?php echo htmlspecialchars($imgPath); ?>" style="max-width: 100px; max-height: 100px;" alt="Gallery Image">
                                            <?php else: ?>
                                                <span class="text-danger">Image not found: <?php echo htmlspecialchars($imgPath); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($image['caption'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($image['category'] ?? ''); ?></td>
                                        <td><button class="btn btn-success btn-sm restore-gallery-image" data-index="<?php echo $index; ?>">Restore</button></td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to restore this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmActionButton">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#sidebarCollapse").on("click", function () {
                $("#sidebar").toggleClass("active");
                $("#content").toggleClass("active");
            });

            // New restore confirmation logic
            $(document).on("click", '[class*="restore-"]', function(e) {
                e.preventDefault();
                var button = $(this);
                var index = button.data("index");
                var action = "";

                if (button.hasClass("restore-service")) {
                    action = "restore_service";
                } else if (button.hasClass("restore-slide")) {
                    action = "restore_slide";
                } else if (button.hasClass("restore-offer")) {
                    action = "restore_offer";
                } else if (button.hasClass("restore-affiliate")) {
                    action = "restore_affiliate";
                } else if (button.hasClass("restore-room")) {
                    action = "restore_room";
                } else if (button.hasClass("restore-venue")) {
                    action = "restore_venue";
                } else if (button.hasClass("restore-gallery-image")) {
                    action = "restore_gallery_image";
                }

                if (action) {
                    // Store data on the modal's confirm button
                    $("#confirmActionButton").data("action", action).data("index", index);
                    $("#confirmationModal").modal("show");
                }
            });

            function sendRestore(action, index) {
                var form = $("<form action=\"archive_websiteContent.php\" method=\"post\"></form>");
                form.append("<input type=\"hidden\" name=\"action\" value=\"" + action + "\" />");
                form.append("<input type=\"hidden\" name=\"index\" value=\"" + index + "\" />");
                $("body").append(form);
                form.submit();
            }

            // Handle click on the 'Yes' button in the modal
            $("#confirmActionButton").on("click", function() {
                var action = $(this).data("action");
                var index = $(this).data("index");

                if (action !== undefined && index !== undefined) {
                    sendRestore(action, index);
                }
            });
        });
    </script>
</body>
</html>