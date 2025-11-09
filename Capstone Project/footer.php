<?php
// Detect current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!--footer-->
<section class="footer">
    <div class="box-container">
        <div class="box">
            <h3>Contact Information</h3>
            <a href="tel:09173270833"><i class="fas fa-phone"></i> 0917-327-0833 - JULS</a>
            <a href="tel:+63285514405"><i class="fas fa-phone"></i> +63 285514405</a>
            <a href="mailto:reservationrenatosplace@gmail.com">
                <i class="fas fa-envelope"></i> reservationrenatosplace@gmail.com
            </a>
            <a href="https://www.google.com/maps/search/?api=1&query=Noel's+Village+Cabrera+Road+Brgy+Dolores+Taytay+Rizal+Philippines" 
               target="_blank">
                <i class="fas fa-map"></i> Noel's Village, Cabrera Road, Brgy. Dolores Taytay, Rizal, Taytay, Philippines
            </a>
        </div>

        <div class="box">
            <h3>Quick Links</h3>
            <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
                <i class="fas fa-arrow-right"></i> Home
            </a>
            <a href="about.php" class="<?= ($current_page == 'about.php') ? 'active' : '' ?>">
                <i class="fas fa-arrow-right"></i> About
            </a>
            <a href="room.php" class="<?= ($current_page == 'room.php') ? 'active' : '' ?>">
                <i class="fas fa-arrow-right"></i> Rooms
            </a>
            <a href="gallery.php" class="<?= ($current_page == 'gallery.php') ? 'active' : '' ?>">
                <i class="fas fa-arrow-right"></i> Gallery
            </a>
            <a href="venues.php" class="<?= ($current_page == 'venues.php') ? 'active' : '' ?>">
                <i class="fas fa-arrow-right"></i> Venues
            </a>
        </div>

        <div class="box">
            <h3>Location map</h3>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.467970015322!2d121.14877065747002!3d14.572389786502475!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c77658906ef5%3A0x1a8766e8bc567d0d!2sRenato&#39;s%20Place%20Private%20Resort%20and%20Events!5e0!3m2!1sen!2sph!4v1724817564370!5m2!1sen!2sph"
                width="300" 
                height="200" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>

    <div class="share">
        <a href="https://www.facebook.com/profile.php?id=100063738415086" class="fab fa-facebook-f" target="_blank"></a>
    </div>

    <div class="credit">&copy; copyright @ <?= date("Y") ?> <span>renatos events place</span></div>
</section>
<!--end-->
