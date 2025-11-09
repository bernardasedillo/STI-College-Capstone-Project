<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Renato's Place Private Resort and Events</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/homeStyle.css">
  <link rel="icon" href="assets/favicon.ico">
</head>

<body>
    <?php include 'customer/Chatbot.php'; ?>
    <?php include 'navbar.php'; ?>

    <!--about-->

    <?php
    // Enable error reporting
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Load content from JSON file with error checking
    $content_config = json_decode(file_get_contents('admin/content_config.json'), true);

    // Check if content is properly loaded, add debug output
    if (!$content_config) {
        echo "Error loading content: " . json_last_error_msg(); // Show JSON error if applicable
        $content_config = [
            'about' => [
                'title' => 'About Us',
                'content' => ['Content not available.'],
                'image' => 'assets/images/about.jpg'
            ],
            'mission' => [
                'title' => 'Mission',
                'content' => ['Content not available.']
            ],
            'vision' => [
                'title' => 'Vision',
                'content' => ['Content not available.']
            ]
        ];
    }
    $about_content = $content_config['about'];
    $mission_content = $content_config['about']['mission'];
    $vision_content = $content_config['about']['vision'];
    ?>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="row">
            <div class="image">
                <img src="assets/images/about.jpg" alt="">
            </div>
            <div class="content">
                <h3><?php echo isset($about_content['title']) ? $about_content['title'] : 'About Us'; ?></h3>
                <?php if (isset($about_content['content']) && is_array($about_content['content'])): ?>
                    <?php foreach ($about_content['content'] as $paragraph): ?>
                        <p><?php echo $paragraph; ?></p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Content not available.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission" id="mission">
        <div class="row1">
            <div class="content1">
                <h5>Mission</h5>
                <?php if (isset($mission_content) && is_array($mission_content)): ?>
                    <?php foreach ($mission_content as $paragraph): ?>
                        <p><?php echo $paragraph; ?></p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Content not available.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Vision Section -->
    <section class="vision" id="vision">
        <div class="row2">
            <div class="content2">
                <h5>Vision</h5>
                <?php if (isset($vision_content) && is_array($vision_content)): ?>
                    <?php foreach ($vision_content as $paragraph): ?>
                        <p><?php echo $paragraph; ?></p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Content not available.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!--end-->
    
    <br>
    <br>

     <!-- Modal Structure -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById("myModal");
            const modalBody = document.getElementById("modal-body");
            const closeButton = document.querySelector(".close");

            // Modal content for each section
            const modalContents = {
                modal1: `
                    <h3><a href="https://www.facebook.com/densolscatering" target="_blank">DENSOL'S CATERING</a></h3>
                    <p>Contact-Number:</p>
                    <p>
                        <a href="tel:09656051741"><i class="fas fa-phone"></i> 09656051741</a><br>
                        <a href="tel:09983941602"><i class="fas fa-phone"></i> 09983941602</a><br>
                        <a href="tel:09361732001"><i class="fas fa-phone"></i> 09361732001</a><br>
                        <a href="tel:0282869256"><i class="fas fa-phone"></i> HOTLINE 02-82869256</a>
                    </p>
                    <h3><a href="https://www.facebook.com/chefmariascatering" target="_blank">CHEF MARIA'S CATERING</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:09285211340"><i class="fas fa-phone"></i> 0928-5211340</a></p>
                    <h3><a href="https://www.facebook.com/madriagacatering" target="_blank">MADRIAGA CATERING</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:09109477722"><i class="fas fa-phone"></i> 0910-9477722</a></p>
                    <h3><a href="https://www.facebook.com/sacbcateringservice" target="_blank">SAC-B CATERING</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:279589260"><i class="fas fa-phone"></i> 279589260</a></p>
                    <h3><a href="https://www.facebook.com/weddingcateringbyhizons?mibextid=ZbWKwL" target="_blank">HIZON'S CATERING</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:09665046540"><i class="fas fa-phone"></i> 0966-5046540</a></p>
                `,
                modal2: `
                    <h3><a href="https://www.facebook.com/orangeandstringsstudio?mibextid=ZbWKwL" target="_blank">Orange and string studio</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:09194184035"><i class="fas fa-phone"></i> 09194184035</a></p>
                    <h3><a href="https://www.facebook.com/profile.php?id=100064086667821&mibextid=ZbWKwL" target="_blank">JGR</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:09194184035"><i class="fas fa-phone"></i> 0915-7964743</a></p>
                    <h3><a href="https://www.facebook.com/profile.php?id=100083036905420&mibextid=ZbWKwL" target="_blank">Rave</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:09194184035"><i class="fas fa-phone"></i> 0945-5141763</a></p>
                `,
                modal3: `
                    <h3><a href="https://www.facebook.com/Renatoscafefoodandbar?mibextid=ZbWKwL" target="_blank">Renato's Cafe</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:09194184035"><i class="fas fa-phone"></i> 0961-0203503</a></p>
                `,
                modal4: `
                    <h3><a href="https://www.facebook.com/donutwallbyhazel?mibextid=ZbWKwL" target="_blank">Donut wall by hazel</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:09194184035"><i class="fas fa-phone"></i> 09056671086</a></p>
                    <h3><a href="https://www.facebook.com/TheYolkk?mibextid=ZbWKwL" target="_blank">The Yolk Coffee and Snack Bar</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:09194184035"><i class="fas fa-phone"></i> 0955-7496398</a></p>
                    <h3><a href="https://www.facebook.com/kountakoyaki?mibextid=ZbWKwL" target="_blank">(Koun Takoyaki)</a></h3>
                    <p>Contact-Number:</p>
                    <p><a href="tel:09194184035"><i class="fas fa-phone"></i> 0915-2356078</a></p>
                `
            };

            // Event listeners for opening modals - now on the entire box
            document.querySelectorAll('.modal-trigger').forEach(trigger => {
                trigger.addEventListener('click', () => {
                    const modalId = trigger.getAttribute('data-modal');
                    modalBody.innerHTML = modalContents[modalId];
                    modal.style.display = "block";
                });
            });

            // Event listener for closing the modal
            closeButton.addEventListener('click', () => {
                modal.style.display = "none";
            });

            // Close the modal when clicking outside of it
            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        });
    </script>
      <script>
  document.addEventListener("DOMContentLoaded", function () {
    const offer_image_modal = document.getElementById("imageModal");
    const offer_modal_image = document.getElementById("modalImage");
    const offer_close_modal = document.querySelector(".close-image-modal");
    const OFFER_ZOOM_FACTOR = 2; // change to increase/decrease zoom
    let offer_is_dragging = false;
    let offer_startX = 0, offer_startY = 0;
    let offer_currentX = 0, offer_currentY = 0;

    // helper: open modal with src
    function offer_openModal(src) {
      offer_modal_image.src = src;
      // reset transform and state
      offer_currentX = 0;
      offer_currentY = 0;
      offer_modal_image.style.transform = 'translate(0px, 0px) scale(1)';
      offer_image_modal.classList.remove('zoomed');
      offer_image_modal.style.display = 'flex';
      offer_image_modal.setAttribute('aria-hidden', 'false');
    }

    // attach click to images and magnifier icons
    document.querySelectorAll('.offer .swiper-slide').forEach(slide => {
      const img = slide.querySelector('img');
      const icon = slide.querySelector('.icon i');
      if (img) {
        img.addEventListener('click', function () {
          offer_openModal(this.src);
        });
      }
      if (icon) {
        icon.addEventListener('click', function () {
          const src = this.getAttribute('data-image') || (img ? img.src : null);
          if (src) offer_openModal(src);
        });
      }
    });

    // Toggle zoom on image click
    offer_modal_image.addEventListener('click', function (e) {
      e.stopPropagation(); // prevent click bubbling to modal container

      if (!offer_image_modal.classList.contains('zoomed')) {
        // zoom in
        offer_image_modal.classList.add('zoomed');
        offer_currentX = 0;
        offer_currentY = 0;
        offer_modal_image.style.transition = 'transform 0.15s ease';
        offer_modal_image.style.transform = `translate(0px, 0px) scale(${OFFER_ZOOM_FACTOR})`;
      } else {
        // zoom out
        offer_image_modal.classList.remove('zoomed');
        offer_currentX = 0;
        offer_currentY = 0;
        offer_modal_image.style.transition = 'transform 0.25s ease';
        offer_modal_image.style.transform = 'translate(0px, 0px) scale(1)';
      }
    });

    // Start dragging (mouse)
    offer_modal_image.addEventListener('mousedown', function (e) {
      if (!offer_image_modal.classList.contains('zoomed')) return;
      offer_is_dragging = true;
      offer_startX = e.clientX;
      offer_startY = e.clientY;
      offer_modal_image.style.transition = 'none';
      e.preventDefault();
    });

    // Move drag (mouse)
    window.addEventListener('mousemove', function (e) {
      if (!offer_is_dragging) return;
      const dx = e.clientX - offer_startX;
      const dy = e.clientY - offer_startY;
      offer_modal_image.style.transform = `translate(${offer_currentX + dx}px, ${offer_currentY + dy}px) scale(${OFFER_ZOOM_FACTOR})`;
    });

    // End drag (mouse)
    window.addEventListener('mouseup', function (e) {
      if (!offer_is_dragging) return;
      const dx = e.clientX - offer_startX;
      const dy = e.clientY - offer_startY;
      offer_currentX += dx;
      offer_currentY += dy;
      offer_is_dragging = false;
      offer_modal_image.style.transition = 'transform 0.25s ease';
    });

    // Touch start
    offer_modal_image.addEventListener('touchstart', function (e) {
      if (!offer_image_modal.classList.contains('zoomed')) return;
      if (e.touches.length === 1) {
        offer_is_dragging = true;
        offer_startX = e.touches[0].clientX;
        offer_startY = e.touches[0].clientY;
        offer_modal_image.style.transition = 'none';
      }
    }, {passive: true});

    // Touch move
    window.addEventListener('touchmove', function (e) {
      if (!offer_is_dragging || !offer_image_modal.classList.contains('zoomed')) return;
      if (e.touches.length === 1) {
        const dx = e.touches[0].clientX - offer_startX;
        const dy = e.touches[0].clientY - offer_startY;
        offer_modal_image.style.transform = `translate(${offer_currentX + dx}px, ${offer_currentY + dy}px) scale(${OFFER_ZOOM_FACTOR})`;
      }
    }, {passive: true});

    // Touch end
    window.addEventListener('touchend', function () {
      if (!offer_is_dragging) return;
      offer_is_dragging = false;
      offer_modal_image.style.transition = 'transform 0.25s ease';
    });

    // Click outside image closes modal
    offer_image_modal.addEventListener('click', function (e) {
      if (e.target === offer_image_modal) {
        offer_closeModal();
      }
    });

    // Close button
    offer_close_modal.addEventListener('click', offer_closeModal);

    // Helper close function
    function offer_closeModal() {
      offer_image_modal.style.display = 'none';
      offer_image_modal.classList.remove('zoomed');
      offer_modal_image.style.transition = 'transform 0.2s ease';
      offer_modal_image.style.transform = 'translate(0px, 0px) scale(1)';
      offer_currentX = 0;
      offer_currentY = 0;
      offer_image_modal.setAttribute('aria-hidden', 'true');
    }

    // ESC key closes modal
    window.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && offer_image_modal.style.display === 'flex') {
        offer_closeModal();
      }
    });
  });
</script>
    <!--end-->

   <?php include 'footer.php'; ?>

    <script>
    const menuBtn = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');

    menuBtn.onclick = () => {
        navbar.classList.toggle('active');
    };
</script>


    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    
</body>
</html>