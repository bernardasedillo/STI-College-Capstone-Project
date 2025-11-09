<?php
    // Load content from JSON file with error checking
    $content_config = json_decode(file_get_contents('admin/content_config.json'), true);

    // Check if content is properly loaded
    if (!$content_config) {
        echo "Error loading content: " . json_last_error_msg();
        // Fallback to empty array to prevent errors
        $content_config = ['gallery' => ['heading' => 'Our Gallery', 'images_dir' => 'admin/image/gallery/', 'images' => []]];
    }
    $gallery_content = $content_config['gallery'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Renato's Place Private Resort and Events</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/modern-gallery.css">
  <link rel="stylesheet" href="assets/css/homeStyle.css">
  <link rel="icon" href="assets/favicon.ico">
  
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main>
        <section class="gallery" id="gallery" aria-label="Photo Gallery">
            <h1 class="heading"><?php echo $gallery_content['heading']; ?></h1>

            <!-- SUB MENU FOR CATEGORIES -->
            <div class="gallery-menu">
                <button class="filter-btn active" data-category="all">All</button>
                <button class="filter-btn" data-category="pool">Pool</button>
                <button class="filter-btn" data-category="event">Event</button>
                <button class="filter-btn" data-category="room">Room</button>
                <button class="filter-btn" data-category="garden">Garden</button>
            </div>

            <!-- GALLERY GRID -->
            <div class="gallery-grid">
                <?php if (count($gallery_content['images']) > 0): ?>
                    <?php foreach ($gallery_content['images'] as $index => $image): 
                        $image_path = $gallery_content['images_dir'] . $image['filename'];
                    ?>
                        <figure class="gallery-item" 
                                data-category="<?= $image['category'] ?>" 
                                data-caption="<?= htmlspecialchars($image['caption']) ?>"
                                data-highres="<?= $image_path ?>">
                            <img 
                                src="<?= $image_path ?>" 
                                alt="<?= $image['caption'] ?>" 
                                loading="lazy" 
                                class="gallery-thumb"
                            />
                            <figcaption class="gallery-overlay">
                                <span><?= $image['caption'] ?></span>
                            </figcaption>
                        </figure>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No images found in the gallery.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- LIGHTBOX -->
    <div id="lightbox" class="lightbox" aria-hidden="true" role="dialog" aria-label="Image preview">
        <button class="lightbox__close" aria-label="Close">&times;</button>
        <button class="lightbox__prev" aria-label="Previous Image">&#10094;</button>
        <button class="lightbox__next" aria-label="Next Image">&#10095;</button>
        <div class="lightbox__content">
            <img id="lightbox-image" src="" alt="" />
            <p id="lightbox-caption"  class="lightbox__caption"></p>
        </div>
    </div>

    <?php include 'customer/Chatbot.php'; ?>
    <?php include 'footer.php'; ?>


<script>
    const menuBtn = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');

    menuBtn.onclick = () => {
        navbar.classList.toggle('active');
    };
</script>

  <script src="assets/js/gallery.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="assets/js/script.js"></script>

</body>
</html>
