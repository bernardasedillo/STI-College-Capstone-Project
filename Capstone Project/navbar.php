<!-- Navbar -->
<header class="header">
    <a href="index.php" class="logo">
        <img src="assets/images/RenatosLOGO.png" alt="Renato's Place Logo">
        <span>Renato's Place</span>
    </a>

    <nav class="navbar">
        <ul>
            <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
            <li><a href="about.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About</a></li>
            <li><a href="gallery.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>">Gallery</a></li>
            <li><a href="room.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'room.php' ? 'active' : ''; ?>">Rooms</a></li>
            <li><a href="venues.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'venues.php' ? 'active' : ''; ?>">Venues</a></li>
            <li><a href="reservation.php" class="btn <?php echo in_array(basename($_SERVER['PHP_SELF']), ['reservation.php', 'reservation-room.php', 'reservation-resort.php', 'reservation-events.php']) ? 'active' : ''; ?>">Book Now</a></li>
        </ul>
    </nav>

    <div id="menu-btn" class="fas fa-bars"></div>
</header>
<!-- End Navbar -->

