<!DOCTYPE html>
<?php
    require_once '../includes/validate.php';
    require '../includes/name.php';
    require '../includes/connect.php';

    // Fetch Monthly Revenue
    ob_start();
    include 'fetch_monthly_revenue.php';
    $totalMonthlyRevenue = ob_get_clean();

    // Fetch Total Reservations for the Month
    ob_start();
    include 'fetch_monthly_reservations.php';
    $totalMonthlyReservations = ob_get_clean();

    // Fetch Total Re-Scheduled Reservations
    $rescheduledQuery = $conn->query("SELECT COUNT(*) as total FROM reservations WHERE status = 'rescheduled'");
    $rescheduledReservations = $rescheduledQuery->fetch_array();
    $totalRescheduledReservations = $rescheduledReservations['total'] ? $rescheduledReservations['total'] : 0;
?>
<html lang="en">
<head>
    <title>Renato's Place Private Resort and Events</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/admin/bootstrap.css" />
    <link rel="stylesheet" href="../assets/css/admin/panel.css" />
    <link rel="stylesheet" href="../assets/css/admin/style.css" />
    <link rel="stylesheet" href="../assets/css/admin/calendar.css" />
    <link rel="icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body>
<?php
if (isset($_SESSION['success'])) {
    echo '<script type="text/javascript">toastr.success("' . $_SESSION['success'] . '")</script>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<script type="text/javascript">toastr.error("' . $_SESSION['error'] . '")</script>';
    unset($_SESSION['error']);
}
?>
<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header"><h3>Renato's Place</h3></div>
        <ul class="list-unstyled components">
            <li><a href="home.php">Dashboard</a></li>
            <li><a href="reserve.php">Reservation</a></li>
            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Super Admin', 'Admin'])) { ?>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="SalesRecord.php">Sales Record</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="Allarchive.php">Archive</a></li>
            <?php } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'Event Manager') { ?>
            <li><a href="inventory.php">Inventory</a></li>
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

                <!-- Dashboard Panels (Compact Version) -->
        <div class="panel panel-default" style="margin-bottom: 15px;">
            <div class="panel-body" style="padding: 10px;">
                <div class="row">
                    <!-- Revenue for the Month -->
                    <div class="col-md-4">
                        <div class="panel panel-primary" style="margin-bottom: 10px;">
                            <div class="panel-heading">
                                <h4 class="panel-title" style="margin:0; font-size:16px;">Revenue for the Month</h4>
                            </div>
                            <div class="panel-body" style="padding:10px;">
                                <?php
                                    $monthRevenueQuery = $conn->query("
                                        SELECT SUM(b.total_amount) as total
                                        FROM reservations r
                                        JOIN billing b ON r.id = b.reservation_id
                                        WHERE MONTH(r.checkin_date) = MONTH(CURRENT_DATE())
                                        AND YEAR(r.checkin_date) = YEAR(CURRENT_DATE())
                                        AND r.status = 'Checked-out'
                                    ");
                                    $monthRevenueResult = $monthRevenueQuery->fetch_array();
                                    $totalMonthlyRevenue = $monthRevenueResult['total'] ?? 0;
                                ?>
                                <h3 style="margin:5px 0;">₱ <?php echo number_format($totalMonthlyRevenue, 2); ?></h3>
                                <a href="monthly_revenue.php" class="btn btn-success btn-xs">View Monthly Revenue</a>
                            </div>
                        </div>
                    </div>

                    <!-- Total Reservations for the Month -->
                    <div class="col-md-4">
                        <div class="panel panel-info" style="margin-bottom: 10px;">
                            <div class="panel-heading">
                                <h4 class="panel-title" style="margin:0; font-size:16px;">Reservations for the Month</h4>
                            </div>
                            <div class="panel-body" style="padding:10px;">
                                <h3 style="margin:5px 0;"><?php echo $totalMonthlyReservations; ?></h3>
                                <a href="monthly_reservations.php" class="btn btn-info btn-xs">View Monthly Reservation</a>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Reservations -->
                    <div class="col-md-4">
                        <div class="panel panel-info" style="margin-bottom: 10px;">
                            <div class="panel-heading">
                                <h4 class="panel-title" style="margin:0; font-size:16px;">Pending</h4>
                            </div>
                            <div class="panel-body" style="padding:10px;">
                                <?php
                                    $query = $conn->query("SELECT COUNT(*) as total FROM reservations WHERE status = 'pending'");
                                    $fetch = $query->fetch_array();
                                ?>
                                <h3 style="margin:5px 0;"><?php echo $fetch['total']?></h3>
                                <a href="reserve.php?view=pending" class="btn btn-primary btn-xs">Pending reservation</a>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmed Reservations -->
                    <div class="col-md-4">
                        <div class="panel panel-success" style="margin-bottom: 10px;">
                            <div class="panel-heading">
                                <h4 class="panel-title" style="margin:0; font-size:16px;">Confirmed</h4>
                            </div>
                            <div class="panel-body" style="padding:10px;">
                                <?php
                                    $query = $conn->query("SELECT COUNT(*) as total FROM reservations WHERE status = 'confirmed'");
                                    $fetch = $query->fetch_array();
                                ?>
                                <h3 style="margin:5px 0;"><?php echo $fetch['total']?></h3>
                                <a href="reserve.php?view=checkin" class="btn btn-primary btn-xs">Confirmed Reservation</a>
                            </div>
                        </div>
                    </div>

                    <!-- Re-Scheduled Reservations -->
                    <div class="col-md-4">
                        <div class="panel panel-warning" style="margin-bottom: 10px;">
                            <div class="panel-heading">
                                <h4 class="panel-title" style="margin:0; font-size:16px;">Re-Scheduled</h4>
                            </div>
                            <div class="panel-body" style="padding:10px;">
                                <h3 style="margin:5px 0;"><?php echo $totalRescheduledReservations; ?></h3>
                                <a href="reserve.php?view=rescheduled" class="btn btn-primary btn-xs">View</a>
                            </div>
                        </div>
                    </div>

                    <!-- Sales -->
                    <div class="col-md-4">
                        <div class="panel panel-success" style="margin-bottom: 10px;">
                            <div class="panel-heading">
                                <h4 class="panel-title" style="margin:0; font-size:16px;">Sales</h4>
                            </div>
                            <div class="panel-body" style="padding:10px;">
                                <?php
                                    $query = $conn->query("
                                        SELECT SUM(b.total_amount) as total_sales 
                                        FROM reservations r
                                        JOIN billing b ON r.id = b.reservation_id
                                        WHERE r.status = 'Checked-out'
                                    ");
                                    $fetch = $query->fetch_array();
                                    $total_sales = $fetch['total_sales'] ?? 0;
                                ?>
                                <h3 style="margin:5px 0;">₱ <?php echo number_format($total_sales, 2); ?></h3>
                                <a href="SalesRecord.php" class="btn btn-primary btn-xs">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rest Day Form (Super Admin only) -->
        <?php if ($_SESSION['role'] == 'Super Admin') { ?>
        <div class="panel panel-warning">
            <div class="panel-heading"><h3 class="panel-title">Set Rest Day</h3></div>
            <div class="panel-body">
                <form method="POST" action="add_rest_day.php" class="form-inline">
                    <input type="date" name="date" class="form-control" required>
                    <input type="text" name="reason" class="form-control" placeholder="Reason (optional)">
                    <button type="submit" class="btn btn-danger">Add Rest Day</button>
                </form>
            </div>
        </div>
        <?php } ?>

        <!-- Calendar Legend badges -->
        <div class="text-center mt-3">
            <span class="badge" style="background-color:#3788d8;">Room</span>
            <span class="badge" style="background-color:#f0ad4e;">Resort</span>
            <span class="badge" style="background-color:#5cb85c;">Event Package</span>
            <span class="badge" style="background-color:#e6323a;">Rest Day</span>
        </div>

        <!-- Calendar -->
        <div id="calendar"></div>

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
                        Are you sure you want to proceed with this action?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reservation Modal -->
        <div class="modal fade" id="reservationModal" tabindex="-1">
          <div class="modal-dialog"><div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="reservationModalLabel">Reservation Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
              </div>
              <div class="modal-body" id="modalBody"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
          </div></div>
        </div>

        <!-- Footer -->
        <br><br><br>
        <div class="navbar navbar-default navbar-fixed-bottom text-right" style="padding:10px; margin-right:10px;">
            <label>&copy; Renato's Place Private Resort and Events</label>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
function showConfirmationModal(message, callback) {
    $('#confirmationModal .modal-body').text(message);
    $('#confirmActionBtn').off('click').on('click', function() {
        $('#confirmationModal').modal('hide');
        callback();
    });
    $('#confirmationModal').modal('show');
}

document.addEventListener('DOMContentLoaded', function() {
    var today = new Date();
    today.setHours(0,0,0,0);

    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        themeSystem: 'bootstrap',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        selectable: true,
        eventSources: [
            {
                url: 'fetch_confirmed_reservations.php',
                method: 'GET',
                failure: function() { toastr.error('Error fetching reservations!'); }
            },
            {
                url: 'fetch_rest_days.php',
                method: 'GET',
                failure: function() { toastr.error('Error fetching rest days!'); }
            }
        ],
        dateClick: function(info) {
            var clickedDate = new Date(info.dateStr);
            clickedDate.setHours(0,0,0,0);

            // Block past dates except today
            if (clickedDate < today && clickedDate.toDateString() !== today.toDateString()) return;

            var events = calendar.getEvents();

            // Check if clicked date is a rest day
            var restDayEvent = events.find(event =>
                event.display === 'background' &&
                new Date(event.start).getTime() === clickedDate.getTime()
            );

            if (restDayEvent) {
                // Show Rest Day modal
                $('#reservationModal .modal-header')
                    .css('background-color', '#e6323a')
                    .css('color', '#fff');
                $('#modalBody').html(`
                    <p><strong>Rest Day:</strong> ${clickedDate.toLocaleDateString()}</p>
                    <p><strong>Reason:</strong> ${restDayEvent.title}</p>
                    <button id="removeRestDayBtn" class="btn btn-danger btn-sm">Remove Rest Day</button>
                `);

                $('#removeRestDayBtn').on('click', function() {
                    $('#reservationModal').modal('hide'); // Close reservation modal first
                    showConfirmationModal("Are you sure you want to remove this rest day?", function() {
                        var date = info.dateStr;
                        $.ajax({
                            url: 'remove_rest_day.php',
                            type: 'POST',
                            data: { date: date },
                            success: function(response) {
                                var res = JSON.parse(response);
                                if (res.success) {
                                    restDayEvent.remove();
                                    $('#reservationModal').modal('hide');
                                    toastr.success("Rest day removed successfully.");
                                } else {
                                    toastr.error("Failed to remove rest day: " + res.message);
                                }
                            },
                            error: function() {
                                toastr.error("An error occurred while removing the rest day.");
                            }
                        });
                    });
                    });

                $('#reservationModal').modal('show');
                return;
            }

            // Available date modal
            $('#reservationModal .modal-header')
                .css('background-color', '#3788d8')
                .css('color', '#fff');

            // Include "Make Reservation" button
            $('#modalBody').html(`
                <p><strong>Date Selected:</strong> ${clickedDate.toLocaleDateString()}</p>
                <p>You can add a reservation for this date.</p>
                <button id="makeReservationBtn" class="btn btn-success btn-sm">Make Reservation</button>
            `);

            // Button click handler
            $('#makeReservationBtn').on('click', function() {
                // Redirect to reservation page with date preselected (example)
                window.location.href = `reserve.php?view=manual_reserve?date=${info.dateStr}`;
            });

            $('#reservationModal').modal('show');
        },
        eventClick: function(info) {
            if (info.event.display === 'background') {
                var clickedDate = new Date(info.event.start);
                clickedDate.setHours(0,0,0,0);

                // Show Rest Day modal
                $('#reservationModal .modal-header')
                    .css('background-color', '#e6323a')
                    .css('color', '#fff');
                $('#modalBody').html(`
                    <p><strong>Rest Day:</strong> ${clickedDate.toLocaleDateString()}</p>
                    <p><strong>Reason:</strong> ${info.event.title}</p>
                    <button id="removeRestDayBtn" class="btn btn-danger btn-sm">Remove Rest Day</button>
                `);

                $('#removeRestDayBtn').on('click', function() {
                    $('#reservationModal').modal('hide'); // Close reservation modal first
                    showConfirmationModal("Are you sure you want to remove this rest day?", function() {
                        var date = info.event.start.toISOString().slice(0, 10);
                        $.ajax({
                            url: 'remove_rest_day.php',
                            type: 'POST',
                            data: { date: date },
                            success: function(response) {
                                var res = JSON.parse(response);
                                if (res.success) {
                                    info.event.remove();
                                    $('#reservationModal').modal('hide');
                                    toastr.success("Rest day removed successfully.");
                                } else {
                                    toastr.error("Failed to remove rest day: " + res.message);
                                }
                            },
                            error: function() {
                                toastr.error("An error occurred while removing the rest day.");
                            }
                        });
                    });
                });

                $('#reservationModal').modal('show');
                return;
            }

            var props = info.event.extendedProps;
            var headerColor = '#3788d8';
            if (props.reservation_type === 'Resort') headerColor = '#f0ad4e';
            else if (props.reservation_type === 'Event Package') headerColor = '#5cb85c';

            $('#reservationModal .modal-header')
                .css('background-color', headerColor)
                .css('color', '#fff');

            var content = `<div class="container-fluid">`;

            content += `<p><strong>${info.event.title}</strong></p>`;
            content += `<p><strong>Reservation Type:</strong> ${props.reservation_type}</p>`;
            content += `<p><strong>Email:</strong> ${props.email}</p>`;
            content += `<p><strong>Phone:</strong> ${props.phone}</p>`;
            content += `<p><strong>Check-in:</strong> ${info.event.start.toLocaleDateString()} ${props.checkin_time || ''}</p>`;
            content += `<p><strong>Guests:</strong> ${props.guests}</p>`;
            if (props.room_number) content += `<p><strong>Room:</strong> ${props.room_number}</p>`;
            if (props.resort_package) content += `<p><strong>Package:</strong> ${props.resort_package}</p>`;
            if (props.events_package) content += `<p><strong>Package:</strong> ${props.events_package}</p>`;
            if (props.event_type) content += `<p><strong>Event:</strong> ${props.event_type}</p>`;
            content += '</div>';
            $('#modalBody').html(content);
            $('#reservationModal').modal('show');
        },
        dayCellClassNames: function(arg) {
            var date = arg.date;
            date.setHours(0,0,0,0);

            var events = calendar.getEvents();

            if (date < today) return ['fc-past-day'];

            var isRestDay = events.some(event =>
                event.display === 'background' &&
                new Date(event.start).getTime() === date.getTime()
            );
            if (isRestDay) return ['fc-rest-day'];
        }
    });

    calendar.render();

    // Sidebar toggle
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar, #content').toggleClass('active');
        setTimeout(() => calendar.updateSize(), 310);
    });
});

</script>
</body>
</html>
