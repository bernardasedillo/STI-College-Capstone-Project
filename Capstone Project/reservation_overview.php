<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Renato's Place Private Resort and Events</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <!-- Custom Styles -->
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/homeStyle.css">
  <link rel="stylesheet" href="assets/css/modern-form.css">
  <link rel="icon" href="assets/favicon.ico">

  <!-- ✅ Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

  <style>
    .reservation-details {
        font-size: 1.5rem;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    .details-list {
        list-style-type: none;
        padding: 0;
    }

    .details-list li {
        font-size: 1.1em;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .reservation-details h3 {
        font-size: 1.8em;
        margin-bottom: 20px;
    }

    .reservation-details h4 {
        font-size: 1.4em;
        margin-top: 30px;
        margin-bottom: 15px;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }

    @media (max-width: 992px) {
        .reservation-details {
            font-size: 1.3rem;
            padding: 15px;
        }
    }

    @media (max-width: 768px) {
        .reservation-details {
            font-size: 1.2rem;
        }
        .reservation-details h3 {
            font-size: 1.6em;
        }
        .reservation-details h4 {
            font-size: 1.3em;
        }
    }

    @media (max-width: 576px) {
        .reservation-details {
            font-size: 1rem;
            padding: 10px;
        }
        .details-list li {
            font-size: 1em;
        }
        .reservation-details h3 {
            font-size: 1.4em;
        }
        .reservation-details h4 {
            font-size: 1.2em;
        }
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <main>
    <section class="reservation-section">
      <div class="reservation-form-container">
        <h2>Reservation Overview</h2>
        <?php
            session_start();
            require_once 'includes/connect.php';

            if (isset($_SESSION['last_reservation_id'])) {
                $last_id = $_SESSION['last_reservation_id'];
                $query = $conn->query("SELECT * FROM `reservations` WHERE `id` = '$last_id'");
                $fetch = $query->fetch_array();

                echo "<div class='reservation-details'>";
                echo "<h3>Thank you for your reservation, " . htmlspecialchars($fetch['full_name']) . "!</h3>";
                echo "<p>Your reservation has been submitted and is now pending confirmation. We will contact you shortly.</p>";
                echo "<h4>Reservation Details:</h4>";
                echo "<ul class='details-list'>";
                echo "<li><strong>Reservation Type:</strong> " . htmlspecialchars($fetch['reservation_type']) . "</li>";
                echo "<li><strong>Full Name:</strong> " . htmlspecialchars($fetch['full_name']) . "</li>";
                echo "<li><strong>Email:</strong> " . htmlspecialchars($fetch['email']) . "</li>";
                echo "<li><strong>Phone:</strong> " . htmlspecialchars($fetch['phone']) . "</li>";
                echo "<li><strong>Check-in Date:</strong> " . htmlspecialchars($fetch['checkin_date']) . "</li>";
                echo "<li><strong>Payment Method:</strong> " . htmlspecialchars($fetch['payment_method']) . "</li>";
                echo "<li><strong>Total Amount:</strong> ₱" . number_format($fetch['total_amount'], 2) . "</li>";
                echo "<li><strong>Down Payment:</strong> ₱" . number_format($fetch['total_amount'] * 0.5, 2) . "</li>";
                echo "<li><strong>Balance:</strong> ₱" . number_format($fetch['total_amount'] * 0.5, 2) . "</li>";
                echo "</ul>";
                echo "<br>";
                echo "<p>Redirecting to homepage in <span id='countdown'>5</span> seconds...</p>";
                echo "</div>";

                unset($_SESSION['last_reservation_id']);
            } else {
                echo "<p>No reservation details to display.</p>";
            }
        ?>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>

  <script>
    (function(){
      var count = 5;
      var countdown = document.getElementById('countdown');
      var interval = setInterval(function(){
        count--;
        countdown.innerHTML = count;
        if (count === 0) {
          clearInterval(interval);
          window.location.href = 'index.php';
        }
      }, 1000);
    })();
  </script>

  <script>
    const menuBtn = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');
    menuBtn.onclick = () => {
        navbar.classList.toggle('active');
    };
  </script>

  <!-- ✅ Toastr JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <?php if (isset($_SESSION['toast_type']) && isset($_SESSION['toast_message'])): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "4000"
      };
      <?php if ($_SESSION['toast_type'] === 'success'): ?>
        toastr.success("<?= addslashes($_SESSION['toast_message']) ?>", "Reservation Confirmed");
      <?php elseif ($_SESSION['toast_type'] === 'error'): ?>
        toastr.error("<?= addslashes($_SESSION['toast_message']) ?>", "Error");
      <?php endif; ?>
    });
  </script>
  <?php 
    unset($_SESSION['toast_type']);
    unset($_SESSION['toast_message']);
  endif; ?>
</body>
</html>
