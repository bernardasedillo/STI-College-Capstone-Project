   // Set minimum datetime for input
   function setMinDateTime() {
    var now = new Date();
    var year = now.getFullYear();
    var month = ('0' + (now.getMonth() + 1)).slice(-2);
    var day = ('0' + now.getDate()).slice(-2);
    var hours = ('0' + now.getHours()).slice(-2);
    var minutes = ('0' + now.getMinutes()).slice(-2);

    var minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

    document.getElementById('preferredDateTime').setAttribute('min', minDateTime);
}

setMinDateTime();

// Modal handling for Terms and Conditions
var termsModal = document.getElementById('termsModal');
var termsLink = document.querySelector('.terms-link');
var closeModalButtons = document.querySelectorAll('.close-modal');

// Show the modal when the terms link is clicked
termsLink.onclick = function() {
    termsModal.style.display = 'block';
};

// Close the modal when any close button is clicked
closeModalButtons.forEach(button => {
    button.onclick = function() {
        termsModal.style.display = 'none';
    }
});

// Close the modal when the user clicks outside of the modal content
window.onclick = function(event) {
    if (event.target == termsModal) {
        termsModal.style.display = 'none';
    }
};

// Enable "View Reservation Details" button when terms are agreed in the modal
var termsAgreeMain = document.getElementById('termsAgreeMain');
var termsAgreeModal = document.getElementById('termsAgreeModal');

termsAgreeModal.addEventListener('change', function() {
    if (termsAgreeModal.checked) {
        termsAgreeMain.checked = true;
        document.getElementById('viewDetailsButton').style.display = 'inline-block';
        termsModal.style.display = 'none'; // Automatically close the modal
    } else {
        termsAgreeMain.checked = false;
        document.getElementById('viewDetailsButton').style.display = 'none';
    }
});

// Modal handling for Reservation Details
var reservationModal = document.getElementById("reservationModal");
var viewDetailsButton = document.getElementById("viewDetailsButton");
var closeDetailsButton = reservationModal.querySelector(".close");

// When the user clicks the button, open the modal 
viewDetailsButton.onclick = function() {
    reservationModal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
closeDetailsButton.onclick = function() {
    reservationModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == reservationModal) {
        reservationModal.style.display = "none";
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var termsCheckbox = document.getElementById('termsAgreeModal');
    var viewDetailsButton = document.getElementById('viewDetailsButton');

    // Function to toggle button visibility based on checkbox state
    function toggleButtonVisibility() {
        if (termsCheckbox.checked) {
            viewDetailsButton.style.display = 'inline-block';
        } else {
            viewDetailsButton.style.display = 'none';
        }
    }

    // Add event listener to checkbox
    termsCheckbox.addEventListener('change', toggleButtonVisibility);

    // Initial check in case the checkbox is already checked
    toggleButtonVisibility();
});

document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('reservationModal');
    var btn = document.getElementById('viewDetailsButton');
    var span = document.getElementsByClassName('close')[0];

    // Show the modal when the button is clicked
    btn.onclick = function() {
        modal.style.display = 'block';
    }

    // Close the modal when <span> (x) is clicked
    span.onclick = function() {
        modal.style.display = 'none';
    }

    // Close the modal when clicking outside of the modal content
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('reservationModal');
    var viewDetailsButton = document.getElementById('viewDetailsButton');
    var span = document.getElementsByClassName('close')[0];
    var submitButton = document.getElementById('submitReservation');

    // Show the modal when the "View Reservation Details" button is clicked
    viewDetailsButton.onclick = function() {
        modal.style.display = 'block';
    }

    // Close the modal when clicking outside of the modal content
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Handle submit button click
    submitButton.onclick = function() {
        // Redirect to admin/reservation_confirmation.php
        window.location.href = 'admin/reservation_confirmation.php';
    }
});


// Qr
function showQRCode(selectedMethod) {
    // List of all payment methods
    var paymentMethods = ['gcash', 'bdo'];

    // Hide all QR codes
    paymentMethods.forEach(function(method) {
        var qrElement = document.getElementById(method + '-qr');
        if (qrElement) {
            qrElement.style.display = 'none';
        }
    });

    // Show the QR code for the selected method
    var selectedQRCode = document.getElementById(selectedMethod + '-qr');
    if (selectedQRCode) {
        selectedQRCode.style.display = 'block';
    }
}

document.getElementById('termsAgreeMain').addEventListener('change', function() {
    var viewDetailsButton = document.getElementById('viewDetailsButton');
    if (this.checked) {
        viewDetailsButton.style.display = 'inline-block';
    } else {
        viewDetailsButton.style.display = 'none';
    }
});

