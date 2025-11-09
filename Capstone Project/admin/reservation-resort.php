<?php
require_once '../includes/connect.php';

// Fetch resort prices
$prices = [];
$sql = "SELECT type, price FROM prices WHERE category = 'resort'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $prices[$row['type']] = $row['price'];
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort Reservation</title>
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
        <h1>Resort Reservation</h1>
        <form action="process_resort_reservation.php" method="POST" class="reservation-form">
             <div class="form-group">
                <label for="reservation_type">Reservation Type</label>
                <select id="reservation_type" name="reservation_type">
                    <option value="Room">Room</option>
                    <option value="Resort" selected>Resort</option>
                    <option value="Event Package">Events Place</option>
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
                    <label for="checkin">Check-in Date</label>
                    <input type="date" id="checkin" name="checkin" required>
                </div>
                <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <input type="number" id="guests" name="guests" min="1" required>
                </div>
                <div class="form-group">
                    <label for="resort_type">Stay Type</label>
                    <select id="resort_type" name="resort_type" required>
                        <option value="">Select Stay Type</option>
                        <option value="Day">Day (8am-5pm)</option>
                        <option value="Night">Night (7pm-6am)</option>
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

        const resortPrices = <?php echo json_encode($prices); ?>;

        document.getElementById('resort_type').addEventListener('change', function() {
            const selectedType = this.value;
            const guests = document.getElementById('guests').value;
            let totalAmount = 0;

            if (selectedType && guests > 0) {
                totalAmount = resortPrices[selectedType] * guests;
            }

            document.getElementById('total-payment').textContent = totalAmount.toLocaleString();
            document.getElementById('total_amount').value = totalAmount;
        });

        document.getElementById('guests').addEventListener('input', function() {
            document.getElementById('resort_type').dispatchEvent(new Event('change'));
        });

         document.addEventListener('DOMContentLoaded', function () {
        const reservationSelect = document.getElementById('reservation_type');

    reservationSelect.addEventListener('change', function () {
      const value = this.value;

      if (value === 'Room') {
                window.location.href = 'admin_reservation.php';
            } else if (value === 'Event Package') {
                window.location.href = 'reservation-events.php';
            } 
            });
        });
    </script>
</body>
</html>