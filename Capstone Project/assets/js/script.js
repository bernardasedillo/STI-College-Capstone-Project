let menu = document.querySelector('#menu-btn');
let navbar = document.querySelector('.header .navbar');

menu.onclick = () => {
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
}

window.onscroll = () => {
    menu.classList.remove('fa-times');
    navbar.classList.remove('active');
}

var swiper = new Swiper(".home-slider",{
    grabCursor:true,
    loop:true,
    centeredSlides:true,
    autoplay:{
        delay: 7500,
        disableOnInteraction: false,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
});

var swiper = new Swiper(".room-slider",{
  spaceBetween: 20,
  grabCursor:true,
  loop:true,
  centeredSlides:true,
  autoplay:{
      delay: 7500,
      disableOnInteraction: false,
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    768: {
      slidesPerView: 2,
    },
    991: {
      slidesPerView: 3,
    },
  },
});

var swiper = new Swiper(".gallery-slider", {
  spaceBetween: 10,
  grabCursor:true,
  loop:true,
  centeredSlides:true,
  autoplay:{
      delay: 1500,
      disableOnInteraction: false,
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    768: {
      slidesPerView: 3,
    },
    991: {
      slidesPerView: 4,
    },
  },
  
});

var swiper = new Swiper(".offer-slider", {
  spaceBetween: 10,
  grabCursor:true,
  loop:true,
  centeredSlides:true,
  autoplay:{
      delay: 1500,
      disableOnInteraction: false,
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    768: {
      slidesPerView: 3,
    },
    991: {
      slidesPerView: 4,
    },
  },
});

let accordions = document.querySelectorAll('.faqs .row .content .box');

accordions.forEach(acco =>{
    acco.onclick = () =>{
      accordions.forEach(subAcco => {subAcco.classList.remove('active')});
      acco.classList.add('active');
    }
});

document.getElementById('roomOptionSelect').addEventListener('change', function() {
  var selectedOption = this.value;
  var roomCards = document.querySelectorAll('.room-card');

  roomCards.forEach(function(card) {
      var priceElement = card.querySelector('.room-price');
      var price = '';

      // Get the price based on the selected option
      switch (selectedOption) {
          case '1': // Daytime
              price = card.getAttribute('data-daytime-price');
              break;
          case '2': // Overnight
              price = card.getAttribute('data-overnight-price');
              break;
          case '3': // Staycation
              price = card.getAttribute('data-staycation-price');
              break;
      }

      // Update the price display
      if (priceElement) {
          priceElement.textContent = 'PHP ' + price + '.00';
      }
  });
});

document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('reservationModal');
    var viewDetailsButton = document.getElementById('viewDetailsButton');
    var closeModalButtons = document.querySelectorAll('.close, .close-modal');

    // Function to populate the modal with reservation details
    function showReservationDetails() {
        // Retrieve the room card details
        var roomCard = document.querySelector('.room-card[data-title="' + '<?php echo $room_option_text; ?>' + '"]');
        var roomTitle = roomCard.getAttribute('data-title');
        var roomPrice = roomCard.getAttribute('data-price');
        
        // Populate the modal with the room details
        document.getElementById('modalRoomTitle').textContent = roomTitle;
        document.getElementById('modalRoomPrice').textContent = roomPrice;
        document.getElementById('modalFullName').textContent = '<?php echo $fname; ?>';
        document.getElementById('modalEmail').textContent = '<?php echo $email; ?>';
        document.getElementById('modalContactNumber').textContent = '<?php echo $phone; ?>';
        document.getElementById('modalAddress').textContent = '<?php echo $address; ?>';
        document.getElementById('modalDateOfStay').textContent = '<?php echo $dateOfStay; ?>';
        document.getElementById('modalNumberOfGuests').textContent = '<?php echo $total_guests; ?>';
        
        // Show the modal
        modal.style.display = 'block';
    }

    // Open the modal when the button is clicked
    viewDetailsButton.addEventListener('click', function(event) {
        event.preventDefault();
        showReservationDetails();
    });

    // Close the modal when any close button is clicked
    closeModalButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    });

    // Close the modal when clicking outside the modal content
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
});



