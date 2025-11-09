<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Renato's Place Private Resort and Events</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/room.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/homeStyle.css">
  <link rel="icon" href="assets/favicon.ico">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <?php
    // Load content from JSON file with error checking
    $content_config = json_decode(file_get_contents('admin/content_config.json'), true);

    // Check if content is properly loaded
    if (!$content_config) {
        echo "Error loading content: " . json_last_error_msg();
        // Fallback to empty array to prevent errors
        $content_config = ['rooms' => ['heading' => 'Our Rooms', 'list' => []]];
    }
    $rooms_content = $content_config['rooms'];
    ?>

    <!-- Rooms Section -->
    <section class="room-section" id="room">
        <h1 class="heading"><?php echo $rooms_content['heading']; ?></h1>
        <div class="room-grid">
            <?php 
            foreach($rooms_content['list'] as $room): ?>
                <div class="room-card">
                    <div class="room-image">
                        <img src="assets/images/<?= $room['img']; ?>" alt="<?= $room['title']; ?>">
                    </div>
                    <div class="room-content">
                        <h3 class="room-title"><?= $room['title']; ?></h3>
                        <p class="room-description"><?= $room['desc']; ?></p>
                        <div class="room-features">
                            <?php foreach($room['features'] as $feature): ?>
                                <span class="feature-badge"><?= $feature; ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="room-footer">
                            <span class="room-price"><?= $room['price']; ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- End Rooms -->

    <?php include 'customer/Chatbot.php'; ?>
    <?php include 'footer.php'; ?>

    <script>
        const menuBtn = document.querySelector('#menu-btn');
        const navbar = document.querySelector('.navbar');
        menuBtn.onclick = () => navbar.classList.toggle('active');
    </script>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
