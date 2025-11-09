<?php
require_once '../includes/connect.php';

// Fetch event packages
$packages = [];
$sql = "SELECT name, price FROM prices WHERE category = 'event'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Reservation</title>
    <link rel="stylesheet" href="../assets/css/admin/modern-manage-content.css">
    <link rel="stylesheet" href="../assets/css/admin/panel.css">
    <link rel="icon" href="../assets/favicon.ico">
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
    <div class="container">
        <h1>Events Place Reservation</h1>
        <form action="process_events_reservation.php" method="POST" class="reservation-form">
            <div class="form-group">
                <label for="reservation_type">Reservation Type</label>
                <select id="reservation_type" name="reservation_type">
                    <option value="Room">Room</option>
                    <option value="Resort">Resort</option>
                    <option value="Event Package" selected>Events Place</option>
                </select>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Mobile Number</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="checkin">Event Date</label>
                    <input type="date" id="checkin" name="checkin" required>
                </div>
                <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <input type="number" id="guests" name="guests" min="1" required>
                </div>
                <div class="form-group">
                    <label for="event_package">Event Package</label>
                    <select id="event_package" name="event_package" required>
                        <option value="">Select a package</option>
                        <?php foreach ($packages as $package): ?>
                            <option value="<?php echo $package['name']; ?>" data-price="<?php echo $package['price']; ?>"><?php echo $package['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Total Payment (PHP)</label>
                    <span id="total-payment">0</span>
                    <input type="hidden" name="total_amount" id="total_amount">
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <div class="payment-options">
                        <input type="radio" id="cash" name="payment_method" value="Cash" required>
                        <label for="cash">Cash</label>
                        <input type="radio" id="gcash" name="payment_method" value="GCash">
                        <label for="gcash">GCash</label>
                        <input type="radio" id="bdo" name="payment_method" value="BDO">
                        <label for="bdo">BDO</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit">Submit Reservation</button>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
            });
        });

        document.getElementById('event_package').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            document.getElementById('total-payment').textContent = parseFloat(price).toLocaleString();
            document.getElementById('total_amount').value = price;
        });

         document.addEventListener('DOMContentLoaded', function () {
        const reservationSelect = document.getElementById('reservation_type');

    reservationSelect.addEventListener('change', function () {
      const value = this.value;

      if (value === 'Room') {
                window.location.href = 'admin_reservation.php';
            } else if (value === 'Resort') {
                window.location.href = 'reservation-resort.php';
            } 
            });
        });
    </script>
</body>
</html>