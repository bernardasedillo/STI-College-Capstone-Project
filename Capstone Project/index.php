<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Renato's Place Private Resort and Events</title>

  <!-- External Libraries -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

  <!-- Custom Styles -->
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/homeStyle.css">
  <link rel="icon" href="assets/favicon.ico">
  <style>
    .swiper-button-next,
    .swiper-button-prev{
        height: 5rem;
        width: 5rem;
        line-height: 5rem;
        background: var(--white);
        color: var(--black);
        border-radius: 25%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .swiper-button-next:hover,
    .swiper-button-prev:hover{
        background: var(--primary);
        color: var(--white);
    }

    .swiper-button-next::after,
    .swiper-button-prev::after{
        font-size: 2rem;
    }

    /* Image Modal Styles - centered flex layout so scaled image stays centered */
.image-modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.85);
  align-items: center;
  justify-content: center;
  overflow: hidden;
  cursor: default;
}

/* Prevent stretched images and preserve aspect ratio */
.modal-content-image {
  width: auto;              /* don’t force width to 100% */
  height: auto;             /* don’t force height to 100% */
  max-width: 90vw;          /* limit width to 90% of viewport */
  max-height: 90vh;         /* limit height to 90% of viewport */
  object-fit: contain;      /* maintain aspect ratio */
  transition: transform 0.3s ease;
  cursor: zoom-in;
  user-select: none;
  -webkit-user-drag: none;
  display: block;
  will-change: transform;
  touch-action: none;
}


/* When zoomed, still keep aspect ratio */
.image-modal.zoomed .modal-content-image {
  cursor: zoom-out;
  object-fit: contain;  /* make sure it stays proportional */
}

/* Close button */
.close-image-modal {
  position: absolute;
  top: 20px;
  right: 35px;
  color: #fff;
  font-size: 40px;
  font-weight: bold;
  cursor: pointer;
  z-index: 1010;
}
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <?php
    // Load content from JSON file with error checking
    $content_config = json_decode(file_get_contents('admin/content_config.json'), true);

    if (!$content_config) {
        echo "Error loading content: " . json_last_error_msg();
        $content_config = [
            'home' => [
                'slides' => [],
                'offers' => [],
                'services' => [],
                'affiliates' => [],
                'affiliate_modals' => []
            ]
        ];
    }
    $home_content = $content_config['home'];
  ?>

  <!-- Home Section -->
  <section class="home" id="home">
    <div class="swiper home-slider">
      <div class="swiper-wrapper">
        <?php 
          foreach ($home_content['slides'] as $slide) {
            echo "
            <div class='swiper-slide slide' style='background: url(assets/images/{$slide['image']}) no-repeat'>
              <div class='content'>
                <h3>{$slide['text']}</h3>
                <a href='#offer' class='btn'> visit our offer</a>
              </div>
            </div>";
          }
        ?>
      </div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
  </section>

  <!-- Offer Section -->
  <section class="offer" id="offer">
    <h1 class="heading">Offers</h1>
    <div class="swiper offer-slider">
      <div class="swiper-wrapper">
        <?php 
          foreach ($home_content['offers'] as $offer) {
            echo "
            <div class='swiper-slide slide'>
              <img src='assets/images/{$offer['image']}' alt=''>
              <div class='icon'>
                <i class='fas fa-magnifying-glass-plus' data-image='assets/images/{$offer['image']}' title='View'></i>
              </div>
            </div>";
          }
        ?>
      </div>
    </div>
  </section>

  <!-- Modal for Image View -->
  <div id="imageModal" class="image-modal" aria-hidden="true" role="dialog">
    <span class="close-image-modal" aria-label="Close image modal">&times;</span>
    <img class="modal-content-image" id="modalImage" src="" alt="Offer image">
  </div>

  <!-- Services Section -->
  <section class="services">
    <h1 class="heading">Services</h1>
    <div class="box-container">
      <?php 
        foreach ($home_content['services'] as $service) {
          echo "
          <div class='box'>
            <img src='assets/images/{$service['image']}' alt=''>
            <h3>{$service['title']}</h3>
          </div>";
        }
      ?>
    </div>
  </section>

<!-- Affiliate Caterers -->
<section class="affiliates">
  <h1 class="heading">List of Accredited Affiliates</h1>
  <div class="box-container">
    <?php 
      foreach ($home_content['affiliates'] as $affiliate) {
        echo "
        <div class='box'>
          <img src='assets/images/{$affiliate['image']}' alt='' class='modal-trigger' data-modal='{$affiliate['modal_id']}'>
          <h3 class='modal-trigger' data-modal='{$affiliate['modal_id']}'>{$affiliate['title']}</h3>
        </div>";
      }
    ?>
  </div>
</section>

<!-- Modal Structure -->
<div id='myModal' class='modal' style='display:none;'>
  <div class='modal-content'>
    <span class='close' style='cursor:pointer;'>&times;</span>
    <div id='modal-body'></div>
  </div>
</div>


  <!-- Scripts -->
  <script>
  // ---------- Affiliate modal (unchanged logic but use addEventListener to avoid overwrite) ----------
  document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("myModal");
    const modalBody = document.getElementById("modal-body");
    const closeBtn = document.querySelector(".close");

    const affiliateModalsData = <?php echo json_encode($home_content['affiliate_modals']); ?>;

    document.querySelectorAll(".modal-trigger").forEach(trigger => {
      trigger.addEventListener("click", function () {
        const modalId = this.getAttribute("data-modal");
        if (!affiliateModalsData || !affiliateModalsData[modalId]) return;
        let modalHtml = `<h2>${affiliateModalsData[modalId].title}</h2>`;
        affiliateModalsData[modalId].content.forEach(item => {
            modalHtml += `<h3><a href="${item.facebook}" target="_blank">${item.name}</a></h3>`;
            modalHtml += `<p>Contact-Number:</p><p>`;
            item.contacts.forEach(contact => {
                modalHtml += `<a href="tel:${contact.replace(/[^0-9]/g, '')}"><i class="fas fa-phone"></i> ${contact}</a><br>`;
            });
            modalHtml += `</p>`;
        });
        modalBody.innerHTML = modalHtml;
        modal.style.display = "flex";
      });
    });

    closeBtn.addEventListener('click', function () {
      modal.style.display = "none";
    });

    // close when clicking outside modal content
    window.addEventListener('click', function (event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    });
  });
  </script>

  <!-- Offer Image Modal & Zoom/Pan Script -->
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


  <?php include 'customer/Chatbot.php'; ?>
  <?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="assets/js/script.js"></script>
  
</body>
</html>
