<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Renato's Place Private Resort and Events</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/modern-venues.css">
  <link rel="stylesheet" href="assets/css/homeStyle.css">
  <link rel="icon" href="assets/favicon.ico">

</head>

<body>
      <?php include 'navbar.php'; ?>

    <?php
        $json_file = __DIR__ . '/admin/content_config.json';
        $venues_content = ['heading' => 'Our Venues', 'list' => []];

        if (file_exists($json_file) && is_readable($json_file)) {
            $json_data = file_get_contents($json_file);
            $content_config = json_decode($json_data, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($content_config['venues'])) {
                $venues_content = $content_config['venues'];
            }
        }

        // Function to convert stored format to display format
        function formatTextForDisplay($text) {
            if (empty($text)) {
                return $text;
            }
            // Convert <br> tags to newlines for proper HTML display
            $text = preg_replace('/<br\s*\/?>/i', '<br>', $text);
            // Convert &nbsp; to regular spaces
            $text = str_replace('&nbsp;', ' ', $text);
            return $text;
        }
    ?>

 <!--venues-->

<section class="venues-section" id="venues">

    <h1 class="heading"><?php echo htmlspecialchars($venues_content['heading']); ?></h1>

    <div class="venues-grid">

        <?php if (!empty($venues_content['list'])): ?>
            <?php foreach($venues_content['list'] as $venue): ?>
            <div class="venue-card">
                <div class="image">
                    <img src="assets/images/<?php echo htmlspecialchars($venue['img']); ?>" alt="<?php echo htmlspecialchars($venue['title']); ?>">
                </div>
                <div class="content">
                    <h3><?php echo htmlspecialchars($venue['title']); ?></h3>
                    <p><?php echo formatTextForDisplay($venue['desc']); ?></p>
                    <div class="details-btn open-event-modal" data-event="<?php echo strtolower(str_replace(' ', '_', htmlspecialchars($venue['title']))); ?>">More Details</div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No venues to display.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Event Modal -->
<div id="event-modal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEventModal">&times;</span>
        <h3 id="event-title"></h3>
        <div id="event-description"></div>
    </div>
</div>

<?php include 'customer/Chatbot.php'; ?>
<?php include 'footer.php'; ?>

<script>
    // Function to format text from storage format (with <br> and &nbsp;) to display format
    function formatTextForDisplay(text) {
        if (!text) return '';
        
        // Create a temporary div to safely parse HTML entities
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = text;
        
        // Get the decoded text
        let formattedText = tempDiv.innerHTML;
        
        // Convert &nbsp; to regular spaces (already decoded by innerHTML)
        formattedText = formattedText.replace(/&nbsp;/g, ' ');
        
        // Keep <br> tags as they work in HTML
        // But ensure they're properly formatted
        formattedText = formattedText.replace(/<br\s*\/?>/gi, '<br>');
        
        return formattedText;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const eventModal = document.getElementById('event-modal');
        const closeEventModal = document.getElementById('closeEventModal');
        const eventTitle = document.getElementById('event-title');
        const eventDescription = document.getElementById('event-description');

        const venuesData = <?php echo json_encode($venues_content['list'] ?? []); ?>;
        const venuesMap = {};
        if (Array.isArray(venuesData)) {
            venuesData.forEach(venue => {
                if (venue && venue.title) {
                    venuesMap[venue.title.toLowerCase().replace(/ /g, '_')] = venue;
                }
            });
        }

        document.querySelectorAll('.open-event-modal').forEach(button => {
            button.addEventListener('click', function() {
                const eventKey = this.getAttribute('data-event');
                const venue = venuesMap[eventKey];
                if (venue) {
                    eventTitle.textContent = venue.title;
                    // Use innerHTML to allow <br> tags and format the text properly
                    eventDescription.innerHTML = formatTextForDisplay(venue.modal_content || '');
                    eventModal.style.display = 'block';
                }
            });
        });

        if(closeEventModal) {
            closeEventModal.onclick = function() {
                eventModal.style.display = 'none';
            };
        }

        window.onclick = function(event) {
            if (event.target == eventModal) {
                eventModal.style.display = 'none';
            }
        };
    });
</script>
    
<script>
    const menuBtn = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');

    menuBtn.onclick = () => {
        navbar.classList.toggle('active');
    };
</script>
</body>
</html>