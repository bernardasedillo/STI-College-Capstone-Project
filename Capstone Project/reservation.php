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
  
</head>

<body>
  <?php include 'navbar.php'; ?>

  <!-- main -->
  <main>
    <section class="reservation-section">
      <div class="reservation-form-container">
        <h2>Make a Reservation</h2>
        <div class="reservation-options">
          <div class="reservation-option" data-value="Room">
            <h3>Room Reservation</h3>
            <p>Book a comfortable room for your stay. Perfect for individuals or small groups looking for a relaxing getaway.</p>
          </div>
          <div class="reservation-option" data-value="Resort">
            <h3>Resort Reservation</h3>
            <p>Reserve the entire resort for your private use. Ideal for family vacations, reunions, or corporate retreats.</p>
          </div>
          <div class="reservation-option" data-value="Event Package">
            <h3>Events Reservation</h3>
            <p>Host your special event with us. We offer packages for weddings, birthdays, and other celebrations.</p>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!-- end main -->

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const reservationOptions = document.querySelectorAll('.reservation-option');

    reservationOptions.forEach(option => {
      option.addEventListener('click', function () {
        const value = this.dataset.value;

        if (value === 'Room') {
          window.location.href = 'reservation-room.php';
        } else if (value === 'Resort') {
          window.location.href = 'reservation-resort.php';
        } else if (value === 'Event Package') {
          window.location.href = 'reservation-events.php';
        }
      });
    });
  });
</script>

  <script>
    const menuBtn = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');

    menuBtn.onclick = () => {
        navbar.classList.toggle('active');
    };
</script>

  <?php include 'footer.php'; ?>
</body>
</html>