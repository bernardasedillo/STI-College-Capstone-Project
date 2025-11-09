<!DOCTYPE html>
<html lang="en">
<head>
  <!--reservation-room.php-->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Renato's Place Private Resort and Events</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <!-- Custom Styles -->
  <link rel="stylesheet" href="assets/css/modern-form.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="assets/css/homeStyle.css">
  <link rel="icon" href="assets/favicon.ico">
  
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<style>
/* Styling for disabled dates in Flatpickr calendar */
.flatpickr-calendar .flatpickr-day.flatpickr-disabled,
.flatpickr-calendar .flatpickr-day.flatpickr-disabled:hover {
  background-color: #f8d7da !important;
  color: #721c24 !important;
  cursor: not-allowed !important;
  text-decoration: line-through !important;
  opacity: 0.6 !important;
  border: 1px solid #f5c6cb !important;
  position: relative;
}

/* Add a diagonal line across disabled dates */
.flatpickr-calendar .flatpickr-day.flatpickr-disabled::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 2px;
  background-color: #dc3545;
  transform: translateY(-50%) rotate(-45deg);
  pointer-events: none;
}

/* Tooltip for disabled dates */
.flatpickr-calendar .flatpickr-day.flatpickr-disabled::after {
  content: 'Unavailable';
  position: absolute;
  bottom: -20px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #dc3545;
  color: white;
  padding: 2px 6px;
  border-radius: 3px;
  font-size: 10px;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s;
  z-index: 1000;
}

.flatpickr-calendar .flatpickr-day.flatpickr-disabled:hover::after {
  opacity: 1;
}

/* Ensure disabled dates are clearly different from available dates */
.flatpickr-calendar .flatpickr-day:not(.flatpickr-disabled) {
  background-color: #fff;
  color: #333;
}

.flatpickr-calendar .flatpickr-day:not(.flatpickr-disabled):hover {
  background-color: #e6e6e6;
  border-color: #959ea9;
}

/* ===== ROOM BLOCKING VISUAL STYLES ===== */

/* Unavailable/blocked room checkbox */
.room-checkbox.room-unavailable {
  cursor: not-allowed !important;
  opacity: 0.5 !important;
  filter: grayscale(100%) !important;
}

/* Unavailable room label styling */
.room-unavailable-label {
  color: #721c24 !important;
  background-color: #f8d7da !important;
  opacity: 0.7 !important;
  cursor: not-allowed !important;
  text-decoration: line-through !important;
  padding: 8px 12px !important;
  border-radius: 4px !important;
  border: 1px solid #f5c6cb !important;
  position: relative !important;
  pointer-events: none !important;
}

/* Add a red "X" icon or indicator for blocked rooms */
.room-unavailable-label::before {
  content: '🚫 ';
  margin-right: 5px;
  font-size: 14px;
}

/* Alternative: Add diagonal line across blocked room labels */
.room-unavailable-label::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 5%;
  right: 5%;
  height: 2px;
  background-color: #dc3545;
  transform: translateY(-50%);
  pointer-events: none;
}

/* Disabled checkbox styling */
.room-checkbox:disabled {
  cursor: not-allowed !important;
  opacity: 0.5 !important;
}

/* Room warning message styling (already in JS, but adding here for consistency) */
#room-warning-msg {
  display: none;
  color: #721c24;
  background-color: #fff3cd;
  border: 1px solid #ffc107;
  padding: 10px 15px;
  margin-top: 10px;
  border-radius: 5px;
  font-weight: bold;
  font-size: 14px;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Container for room checkboxes (if you have one) */
.room-checkbox-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin: 10px 0;
}

/* Individual room item styling */
.room-item {
  display: flex;
  align-items: center;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 5px;
  transition: all 0.3s ease;
}

.room-item:has(.room-unavailable) {
  background-color: #f8d7da;
  border-color: #f5c6cb;
  opacity: 0.6;
}

.room-item:has(.room-checkbox:not(:disabled):not(.room-unavailable)) {
  cursor: pointer;
}

.room-item:has(.room-checkbox:not(:disabled):not(.room-unavailable)):hover {
  background-color: #e7f3ff;
  border-color: #007bff;
  transform: translateY(-2px);
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

label:has(+ input[required])::after,
label:has(+ select[required])::after,
label:has(+ textarea[required])::after,
label[data-required="true"]::after {
    content: " *";
    color: #cc4b4b;
    font-weight: bold;
}
</style>


<body>  
  <?php include 'navbar.php'; ?>

  <!-- main -->
  <main>
    <section class="reservation-section">
      <div class="reservation-form-container">
        <h2>Make a Reservation</h2>
        <form action="submit_reservation.php" method="POST" class="reservation-form">

            <div class="form-group full-width">
                <label  class="help-note"> For us to further accommodate you with your queries and concerns, kindly fill-out the following information below.</label>
                <br>
                <label for="reservation_type">Reservation Type</label>
                <select name="reservation_type" id="reservation_type">
                    <option value="Room" selected>Room</option>
                    <option value="Resort">Resort</option>
                    <option value="Event Package">Events Place</option>
                </select>
            </div>

                <!-- Customer details (common for all types) -->
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email address" required>
            </div>

            <div class="form-group">
                <label for="phone">Mobile Number</label>
                <input type="number" id="phone" name="phone" placeholder="09XXXXXXXXX" maxlength="11" required />
                <div class="error-message" id="phone-error"></div>
            </div>

            <div class="form-group">
                <label for="full_address">Full Address</label>
                <input type="text" name="full_address" id="full_address" placeholder="Enter your full address" required>
            </div>

            <!-- Common fields for Room, Resort, and Event Package -->
            <div class="form-group">
                <label for="checkin">Check-In Date</label>
                <input type="text" name="checkin" id="checkin" placeholder="Select a date" required>
            </div>

            <div class="form-group">
                <label for="check_in_time">Duration of Stay</label>
                <select id="check_in_time" name="check_in_time" required>
                    <option value="" disabled selected>Select duration of stay</option>
                </select>
            </div>

            <div class="form-group">
                <label for="time">Check-in Time</label>
                <input type="text" id="time" name="time" placeholder="Select a time" required>
            </div>

            <div class="form-group">
                <label for="guests">Number of Guests</label>
                <input type="number" name="guests" id="guests" min="1" placeholder="Enter the number of guests" required>
                <div id="guest-error-msg" class="error-message" style="display: none;">Maximum 24 guests is allowed</div>
            </div>

            <div class="form-group">
                <label for="event_package_selection_room" data-required="true">Room Selection</label>
                <div class="custom-dropdown" id="room-specific-fields">
                    <button type="button" class="roombutton" id="dropdownBtn" onclick="toggleDropdown(this)" required>Select Rooms</button>
                    <div class="dropdown-content" id="dropdown-content-rooms"></div>
                    <div id="room-warning-msg" class="warning-message" style="display:none; color:black; font-size:14px; margin-top:4px;"></div>
                    <div id="room-warning-msg-one" class="warning-message" style="display:none; color:black; font-size:14px; margin-top:4px;"></div>
                </div>
            </div>


        
            
            
                <div class="form-group full-width">
                    <label for="message">Special Requests</label>
                    <textarea name="message" id="message" rows="4" placeholder="Any special requests or notes..."></textarea>
                    <br>
                    <label>Rest assured that those will be attended to on the following day.</label>
                    <label>Thank you & God Bless!</label>
                </div>

                <div class="form-group full-width">
                    <label for="total-payment"><strong>Total Payment:</strong></label>
                    <div id="total-payment-box" style="font-size: 20px; font-weight: bold; color: #000;">
                        ₱ <span id="total-payment">0</span>
                    </div>
                    <input type="hidden" name="total_amount" id="total_amount" value="0">
                </div>

                <div class="form-group full-width">
                    <button type="button" id="payment-button">Proceed to payment</button>
                    <input type="hidden" name="selected_rooms" id="selected_rooms" value="">
<input type="hidden" name="checkin_time" id="checkin_time_hidden" value="">
<input type="hidden" name="duration" id="duration_hidden" value="">
                </div>
            </div>
        </form>
      </div>

    </section>
  </main>
  <!-- end main -->


<script>

document.addEventListener("DOMContentLoaded", function () {
  fetch("fetch_rooms.php")
    .then(res => res.json())
    .then(data => {
      // Populate durations
      const durationSelect = document.getElementById("check_in_time");
      data.durations.forEach(dur => {
        const option = document.createElement("option");
        option.value = dur;
        if (window.innerWidth < 600) {
            option.textContent = `${dur}`.substring(0, 10) + '...';
        } else {
            option.textContent = `${dur}`;
        }
        durationSelect.appendChild(option);
      });

      // Populate room dropdown
      const dropdownContent = document.getElementById("dropdown-content-rooms");
      dropdownContent.innerHTML = "";

      if (!data.rooms || data.rooms.length === 0) {
        dropdownContent.innerHTML = "<div>No rooms available</div>";
      } else {
        data.rooms.forEach(room => {
          const wrapper = document.createElement("div");
          wrapper.classList.add("dropdown-item");

          const safeId = `room-${String(room).replace(/\s+/g, '_').replace(/[^\w\-]/g, '')}`;

          const checkbox = document.createElement("input");
          checkbox.type = "checkbox";
          checkbox.name = "room_number[]";
          checkbox.value = room;
          checkbox.id = safeId;
          checkbox.classList.add("room-checkbox");

          const label = document.createElement("label");
          label.setAttribute("for", safeId);
          label.textContent = room;

          wrapper.appendChild(checkbox);
          wrapper.appendChild(label);
          dropdownContent.appendChild(wrapper);
        });
      }

      const roomCheckboxes = document.querySelectorAll('.room-checkbox');
      roomCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
          validateRoomSelection();
          calculatePrice(); // keep your price update
        });
      });

      // Store prices globally for calculation
      window.roomPrices = data.roomPrices.reduce((acc, row) => {
        acc[`${row.name}_${row.duration_hours}`] = row.price;
        return acc;
      }, {});
    })
    .catch(err => console.error("Error fetching rooms:", err));
});


function getRequiredRooms(guests) {
  if (guests <= 6) return 1;
  if (guests <= 12) return 2;
  if (guests <= 18) return 3;
  if (guests <= 24) return 4;
  return 0; // over limit
}

function validateRoomSelection() {
  const guestInput = document.getElementById("guests");
  const guestError = document.getElementById("room-warning-msg");
  const roomWarningOne = document.getElementById("room-warning-msg-one");

  const value = guestInput.value.trim();
  const guests = parseInt(value, 10) || 0;
  const roomCheckboxes = document.querySelectorAll('.room-checkbox');
  const checkedRooms = document.querySelectorAll('.room-checkbox:checked').length;

  // ✅ Always reset first
  guestError.style.display = "none";
  roomWarningOne.style.display = "none";
  roomCheckboxes.forEach(cb => cb.disabled = false);

  // ✅ If input is empty, clear everything and stop
  if (value === "" || guests === 0) {
    roomCheckboxes.forEach(cb => {
      cb.checked = false;
      cb.disabled = false;
    });
    return;
  }

  // Guest > 24 not allowed
  if (guests > 24) {
    guestError.textContent = "Maximum 24 guests is allowed";
    guestError.style.display = "block";
    roomCheckboxes.forEach(cb => cb.disabled = true);
    return;
  }

const requiredRooms = getRequiredRooms(guests);

// Reset error
guestError.style.display = "none";
guestError.textContent = "";

// Smart pluralization
const guestLabel = guests > 1 ? "guests" : "guest";
const roomLabel = requiredRooms > 1 ? "rooms" : "room";

// ONE consistent error sentence, but grammatically correct
const errorText = `You must select exactly ${requiredRooms} ${roomLabel} for ${guests} ${guestLabel}.`;

// Special case: 1 room required
if (requiredRooms === 1) {

  if (checkedRooms !== 1) {
    guestError.textContent = errorText;
    guestError.style.display = "block";
  }

  // Disable other checkboxes once 1 is selected
  if (checkedRooms >= 1) {
    roomCheckboxes.forEach(cb => {
      if (!cb.checked) cb.disabled = true;
    });
  }

} else {
  // 2+ rooms required
  if (checkedRooms !== requiredRooms) {
    guestError.textContent = errorText;
    guestError.style.display = "block";
  }

  // Lock checkboxes once limit reached
  if (checkedRooms >= requiredRooms) {
    roomCheckboxes.forEach(cb => {
      if (!cb.checked) cb.disabled = true;
    });
  }
}


}



document.getElementById("guests").addEventListener("input", () => {
  validateRoomSelection();
  calculatePrice();
});



function calculatePrice() {
  const duration = document.getElementById("check_in_time").value;
  const selectedRooms = Array.from(document.querySelectorAll('.room-checkbox:checked')).map(cb => cb.value);
  let basePrice = 0;

  if (duration && selectedRooms.length > 0) {
    selectedRooms.forEach(room => {
      const key = `${room}_${duration}`;
      if (window.roomPrices[key]) {
        basePrice += parseFloat(window.roomPrices[key]);
      }
    });
  }

  document.getElementById("total-payment").textContent = basePrice.toLocaleString();
  document.getElementById("total_amount").value = basePrice;
}

document.addEventListener("change", function(e) {
  if (e.target.id === "check_in_time" || e.target.classList.contains("room-checkbox")) {
    calculatePrice();
  }
});



        document.addEventListener('DOMContentLoaded', function () {
        const reservationSelect = document.getElementById('reservation_type');
            reservationSelect.value = 'Room';
            reservationSelect.dispatchEvent(new Event('change'));
        });

    document.addEventListener('DOMContentLoaded', function () {
        const reservationSelect = document.getElementById('reservation_type');

    reservationSelect.addEventListener('change', function () {
      const value = this.value;

      if (value === 'Resort') {
                window.location.href = 'reservation-resort.php';
            } else if (value === 'Event Package') {
                window.location.href = 'reservation-events.php';
            } 
            });
        });


    

        document.getElementById("reservation_type").addEventListener("change", function () {
        const selectedType = this.value;

        // Clear all input, select, and textarea fields EXCEPT the reservation type
        document.querySelectorAll("input, select, textarea").forEach(el => {
            if (el.id !== "reservation_type") {
            if (el.type === "checkbox" || el.type === "radio") {
                el.checked = false;
            } else if (el.tagName.toLowerCase() === "select") {
                el.selectedIndex = 0;
            } else {
                el.value = "";
            }
            }
        });

        });




        
        // Optional: Automatically set checkout to match or follow checkin
         document.getElementById('checkin').addEventListener('change', function() {
          const checkinDate = this.value;
        });



        document.addEventListener('DOMContentLoaded', function () {
            const phoneInput = document.getElementById('phone');
            const phoneError = document.getElementById('phone-error');

            // Always start with "09" when empty
            phoneInput.addEventListener('focus', () => {
                if (phoneInput.value === '') {
                    phoneInput.value = '09';
                }
            });

            // Allow normal backspace but stop if user tries to delete past "09"
            phoneInput.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    // If only "09" is left, block deletion
                    if (phoneInput.value === '09') {
                        e.preventDefault();
                    }
                }
            });

            // Input validation
            phoneInput.addEventListener('input', () => {
                // Remove non-digits
                phoneInput.value = phoneInput.value.replace(/\D/g, '');

                // Ensure prefix stays
                if (!phoneInput.value.startsWith('09')) {
                    phoneInput.value = '09' + phoneInput.value.replace(/^0+/, '');
                }

                // Limit to 11 digits
                if (phoneInput.value.length > 11) {
                    phoneInput.value = phoneInput.value.slice(0, 11);
                }

                // Show error if not 11 digits
                if (phoneInput.value.length !== 11) {
                    phoneError.textContent = 'Mobile number must be exactly 11 digits.';
                    phoneError.style.display = 'inline';
                    phoneInput.style.borderColor = 'red';
                } else {
                    phoneError.textContent = '';
                    phoneError.style.display = 'none';
                    phoneInput.style.borderColor = '';
                }
            });
        });


     document.getElementById('guests').addEventListener('input', function () {
        const error = document.getElementById('guest-error-msg');
        let value = parseInt(this.value, 10);

    if (value < 1 || isNaN(value)) { //NEW CODE!!!!!!!!!!!!!!!
        error.textContent = "Minimum 1 guest is required";
        error.style.display = 'inline';
        this.value = ''; 
        this.style.borderColor = 'red';

        setTimeout(() => {
            error.style.display = 'none';
            this.style.borderColor = '';
        }, 1500);

    } else if (value > 24) {
        error.textContent = "Maximum 24 guests is allowed";
        error.style.display = 'inline';
        this.value = ''; 
        this.style.borderColor = 'red';

        setTimeout(() => {
            error.style.display = 'none';
            this.style.borderColor = '';
        }, 1500);

    } else {
        error.style.display = 'none';
        this.style.borderColor = '';
    } 
    });





function toggleDropdown() {
  const dropdown = document.getElementById("dropdown-content-rooms");
  dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}


        // Close dropdown if clicking outside
        document.addEventListener("click", function(event) {
        const dropdown = document.getElementById("dropdown-content-rooms");
        const button = event.target.closest(".custom-dropdown button");
        if (!button && !event.target.closest("#dropdown-content-rooms")) {
            dropdown.style.display = "none";
        }
        });



// Prevent invalid characters (e, +, -, .) in guest input
document.getElementById("guests").addEventListener("keydown", function (e) {
  if (["e", "E", "+", "-", "."].includes(e.key)) {
    e.preventDefault();
  }
});

// Disable room selection when guests is empty
function toggleRoomSelection() {
  const guestInput = document.getElementById("guests");
  const value = guestInput.value.trim();
  const roomCheckboxes = document.querySelectorAll(".room-checkbox");
  const dropdownBtn = document.getElementById("dropdownBtn");

  if (value === "" || parseInt(value, 10) === 0) {
    // Disable all rooms + button
    roomCheckboxes.forEach(cb => {
      cb.checked = false;
      cb.disabled = true;
    });
    dropdownBtn.disabled = true;
    dropdownBtn.textContent = "Select Rooms";
  } else {
    // Enable button and rooms
    roomCheckboxes.forEach(cb => cb.disabled = false);
    dropdownBtn.disabled = false;
    dropdownBtn.textContent = "Select Rooms";
  }
}

// Run on guests input
document.getElementById("guests").addEventListener("input", () => {
  toggleRoomSelection();
  validateRoomSelection();
  calculatePrice();
});

// Run on page load too
document.addEventListener("DOMContentLoaded", toggleRoomSelection);







  const form = document.querySelector('.reservation-form');
const paymentButton = document.getElementById('payment-button');

paymentButton.addEventListener('click', function (e) {
    e.preventDefault();
    let isValid = true;
    const validatedFields = new Set(); // Track which fields we've already validated

    // Reset previous error styles/messages
    document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(field => {
        field.style.borderColor = '';
        // Remove ALL existing error messages
        const existingErrors = field.parentElement.querySelectorAll('.field-error');
        existingErrors.forEach(err => err.remove());
    });

    // Validate visible required fields (ignore total-payment area)
    const visibleFields = document.querySelectorAll(
        '.reservation-form .form-group input:not([type="checkbox"]):not([type="hidden"]), .reservation-form .form-group select, .reservation-form .form-group textarea'
    );

    visibleFields.forEach(field => {
        // Skip if already validated this field
        if (validatedFields.has(field.id)) return;
        validatedFields.add(field.id);

        const parent = field.closest('.form-group');
        const isHidden =
            window.getComputedStyle(parent).display === 'none' ||
            parent.offsetParent === null;

        // Skip validation for total-payment fields
        if (parent && parent.querySelector('#total-payment-box')) return;

        // SPECIAL VALIDATION FOR PHONE NUMBER
        if (field.id === 'phone' && !isHidden) {
            const phoneValue = field.value.trim();
            
            // Check if empty or just "09"
            if (phoneValue === '' || phoneValue === '09') {
                isValid = false;
                field.style.borderColor = 'red';
                
                const errorMsg = document.createElement('div');
                errorMsg.classList.add('field-error');
                errorMsg.style.color = 'red';
                errorMsg.style.fontSize = '13px';
                errorMsg.textContent = 'This field is required.';
                field.parentElement.appendChild(errorMsg);
            } 
            // Check if not 11 digits
            else if (phoneValue.length !== 11) {
                isValid = false;
                field.style.borderColor = 'red';
                
                const errorMsg = document.createElement('div');
                errorMsg.classList.add('field-error');
                errorMsg.style.color = 'red';
                errorMsg.style.fontSize = '13px';
                errorMsg.textContent = 'Mobile number must be exactly 11 digits.';
                field.parentElement.appendChild(errorMsg);
            }
            // Check if it starts with 09
            else if (!phoneValue.startsWith('09')) {
                isValid = false;
                field.style.borderColor = 'red';
                
                const errorMsg = document.createElement('div');
                errorMsg.classList.add('field-error');
                errorMsg.style.color = 'red';
                errorMsg.style.fontSize = '13px';
                errorMsg.textContent = 'Mobile number must start with 09.';
                field.parentElement.appendChild(errorMsg);
            }
        }
        // GENERAL VALIDATION FOR OTHER FIELDS
        else if (!isHidden && field.id !== 'message' && field.value.trim() === '') {
            isValid = false;
            field.style.borderColor = 'red';

            const errorMsg = document.createElement('div');
            errorMsg.classList.add('field-error');
            errorMsg.style.color = 'red';
            errorMsg.style.fontSize = '13px';
            errorMsg.textContent = 'This field is required.';
            field.parentElement.appendChild(errorMsg);
        }
    });

    // If valid, show Terms modal, otherwise scroll to top
    if (isValid) {
        document.getElementById('termsModal').style.display = 'flex';
    } else {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});




// Remove error styling on user focus
document.querySelectorAll('.reservation-form .form-group input, .reservation-form .form-group select, .reservation-form .form-group textarea').forEach(field => {
    field.addEventListener('focus', function () {
        this.style.borderColor = '';
        const errorMsg = this.parentElement.querySelector('.field-error');
        if (errorMsg) {
            errorMsg.remove();
        }
    });
});


function closeTermsModal() {
    document.getElementById('termsModal').style.display = 'none';
    document.getElementById('agreeCheckbox').checked = false;
    document.getElementById('confirmSubmitBtn').disabled = true;
}




    </script>
    
    <script> // NEW CODE!!!!!!
document.addEventListener("DOMContentLoaded", function () {
    // Select the fields you want to restrict
    const restrictedFields = [
        document.getElementById("full_name"),
        document.getElementById("full_address"),
        document.getElementById("message")
    ];

    restrictedFields.forEach(field => {
        if (!field) return;

        // Prevent typing unwanted characters in real-time
        field.addEventListener("input", function () {
            // Allow: letters, numbers, spaces, basic punctuation (. , ' -)
            const cleanValue = this.value.replace(/[^a-zA-ZñÑ0-9\s.,'-]/g, '');
            if (this.value !== cleanValue) {
                this.value = cleanValue;
            }
        });
    });
});
</script>

  <?php include 'footer.php'; ?>
  <?php include 'payment_modal.php'; ?>

<div id="termsModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h3>Terms and Conditions</h3>
        <div class="terms-text">
            <p><strong>1. Reservation:</strong> Reservations can be made online through our website or by contacting our reservation team via phone or email. A confirmation email will be sent upon successful booking.</p>
            <p><strong>2. Payment:</strong> A deposit of 50% of the total booking cost must be made at the time of reservation. The remaining balance must be paid upon arrival at the resort or at the end of accommodation.</p>
            <p><strong>3. Cancellation/refund Policy:</strong> No Refund, and Cancellations made within 30 days prior to the check-in date will receive a re-schedule options. Cancellations made more than 30 days will result in forfeiture of the reservation.</p>
            <p><strong>4. Guest Responsibilities:</strong> Guests are expected to respect the property and other guests. Any damage to the property caused by the guest will be charged to the guest's account.</p>
            <p><strong>5. Liability:</strong> The resort is not responsible for any loss, damage, or injury sustained by guests during their stay.</p>
            <p><strong>6. Privacy Policy:</strong> We respect your privacy and will not share your personal information with third parties without your consent.</p>
            <p><strong>7. Changes of Terms and Conditions:</strong> We reserve the right to modify these terms and conditions at any time, any changes will be posted on our page.</p>
            <p>By making a reservation at Renato’s Place Private Resort and Events, you agree to abide by these terms and conditions.</p>
        </div>
        <label style="display: block; margin-top: 15px;">
            <input type="checkbox" id="agreeCheckbox"> I agree with the Terms and Conditions
        </label>
        <div class="modal-buttons">
            <button type="button" id="agreeButton" disabled>Agree</button>
            <button type="button" onclick="closeTermsModal()">Cancel</button>
        </div>
    </div>
</div>

<script>

    document.getElementById('agreeCheckbox').addEventListener('change', function() {
        document.getElementById('agreeButton').disabled = !this.checked;
    });

    document.getElementById('agreeButton').addEventListener('click', function() {
        closeTermsModal();
        openPaymentModal();
    });

    function closeTermsModal() {
        document.getElementById('termsModal').style.display = 'none';
    }
</script>

<script>
    const menuBtn = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');

    menuBtn.onclick = () => {
        navbar.classList.toggle('active');
    };
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize check-in date picker (with rest days)
  fetch('admin/get_rest_days.php')
    .then(response => response.json())
    .then(restDays => {
      const checkinPicker = flatpickr("#checkin", {
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        minDate: new Date().fp_incr(1),
        disable: restDays,
      });

      // Initialize time picker AFTER date picker is ready
      const timePicker = flatpickr("#time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour: 0,
        defaultMinute: 0,
        minuteIncrement: 1,
        onOpen: function(selectedDates, dateStr, instance) {
          const selectedDate = checkinPicker.selectedDates[0];
          const now = new Date();

          if (selectedDate) {
            // Check if the selected date is today
            const isToday =
              selectedDate.getFullYear() === now.getFullYear() &&
              selectedDate.getMonth() === now.getMonth() &&
              selectedDate.getDate() === now.getDate();

            if (isToday) {
              // Restrict to current or later times
              const minTime =
                now.getHours().toString().padStart(2, '0') + ":" +
                now.getMinutes().toString().padStart(2, '0');
              instance.set('minTime', minTime);
            } else {
              // Future date → allow full day
              instance.set('minTime', "00:00");
            }
          } else {
            // No date selected → allow full day
            instance.set('minTime', "00:00");
          }
        }
      });
    });
});
</script>




<script>

// ===== HIDDEN FIELDS UPDATE FUNCTIONS =====
function updateSelectedRooms() {
    const selectedRooms = Array.from(document.querySelectorAll('.room-checkbox:checked'))
        .map(cb => cb.value);
    document.getElementById('selected_rooms').value = selectedRooms.join(',');
    console.log('✓ Selected rooms updated:', selectedRooms.join(','));
}

function updateCheckinTime() {
    const timeValue = document.getElementById('time').value;
    document.getElementById('checkin_time_hidden').value = timeValue;
    console.log('✓ Check-in time updated:', timeValue);
}

function updateDuration() {
    const durationValue = document.getElementById('check_in_time').value;
    document.getElementById('duration_hidden').value = durationValue;
    console.log('✓ Duration updated:', durationValue);
}

// ===== ROOM BLOCKING INTEGRATION WITH REST DAYS AND RESORT ROOMS =====
let bookedRoomsData = [];
let allRoomsList = [];
let restDaysData = []; // Store rest days globally

// Fetch rest days data
function loadRestDays() {
  return fetch('admin/get_rest_days.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      restDaysData = data || [];
      console.log('✓ Loaded rest days:', restDaysData);
      return restDaysData;
    })
    .catch(error => {
      console.error('Error fetching rest days:', error);
      return [];
    });
}

// Fetch booked rooms data on page load
function loadBookedRooms() {
  return fetch('fetch_booked_rooms.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.text();
    })
    .then(text => {
      console.log('Raw response:', text.substring(0, 200));
      try {
        const data = JSON.parse(text);
        if (data.success) {
          bookedRoomsData = data.all_bookings; // ✅ Use all_bookings to get ALL reservation types
          console.log('✓ Loaded all bookings:', bookedRoomsData.length + ' total');
          console.log('  - Room bookings:', data.room_count);
          console.log('  - Resort bookings:', data.resort_count);
          console.log('  - Event bookings:', data.event_count);
          return data.all_bookings;
        } else {
          console.error('Failed to fetch booked rooms:', data.error);
          return [];
        }
      } catch (e) {
        console.error('JSON Parse Error:', e);
        console.error('Response was:', text);
        return [];
      }
    })
    .catch(error => {
      console.error('Error fetching booked rooms:', error);
      return [];
    });
}

// Parse time string to minutes
function timeToMinutes(timeStr) {
  if (!timeStr) return 0;
  const [hours, minutes] = timeStr.split(':').map(Number);
  return hours * 60 + minutes;
}

// Check if two time ranges overlap
function timeRangesOverlap(start1, end1, start2, end2) {
  return start1 < end2 && start2 < end1;
}

// Get next day's date string
function getNextDay(dateStr) {
  const date = new Date(dateStr);
  date.setDate(date.getDate() + 1);
  return date.toISOString().split('T')[0];
}

// Check if a booking extends into the next day
function bookingExtendsToNextDay(checkinTime, durationHours) {
  const checkinMinutes = timeToMinutes(checkinTime);
  const checkoutMinutes = checkinMinutes + (durationHours * 60);
  return checkoutMinutes > 1440; // 1440 minutes = 24 hours
}

// Get unavailable rooms for a specific date/time/duration (with rest day blocking and resort rooms)
function getUnavailableRooms(checkinDate, checkinTime, duration) {
  console.log('🔎 Getting unavailable rooms for:', { checkinDate, checkinTime, duration });
  
  const unavailableRooms = new Set();
  
  if (!checkinDate || !checkinTime || !duration) {
    console.log('⚠️ Missing required fields');
    return unavailableRooms;
  }
  
  // Parse the new booking's time and duration
  const newCheckinMinutes = timeToMinutes(checkinTime);
  const durationMatch = duration.match(/(\d+)\s*Hour/i);
  if (!durationMatch) {
    console.log('⚠️ Could not parse duration:', duration);
    return unavailableRooms;
  }
  
  const newDurationHours = parseInt(durationMatch[1], 10);
  const newCheckoutMinutes = newCheckinMinutes + (newDurationHours * 60);
  
  console.log('📊 New booking window:', {
    date: checkinDate,
    checkinTime: checkinTime,
    checkinMinutes: newCheckinMinutes,
    duration: newDurationHours + ' hours',
    checkoutMinutes: newCheckoutMinutes,
    checkoutTime: minutesToTime(newCheckoutMinutes)
  });
  
  // ✅ CHECK REST DAYS - Block ALL rooms if today is a rest day
  if (restDaysData.includes(checkinDate)) {
    console.log('🚫 TODAY IS A REST DAY - Blocking ALL rooms');
    allRoomsList.forEach(room => {
      unavailableRooms.add(room);
      console.log('    🚫 Blocked (rest day):', room);
    });
    return unavailableRooms; // Return immediately, everything is blocked
  }
  
  // ✅ CHECK IF BOOKING EXTENDS TO NEXT DAY (overnight)
  const nextDay = getNextDay(checkinDate);
  const extendsToNextDay = bookingExtendsToNextDay(checkinTime, newDurationHours);
  
  if (extendsToNextDay && restDaysData.includes(nextDay)) {
    console.log('🚫 BOOKING EXTENDS INTO NEXT DAY WHICH IS A REST DAY - Blocking ALL rooms');
    allRoomsList.forEach(room => {
      unavailableRooms.add(room);
      console.log('    🚫 Blocked (overnight into rest day):', room);
    });
    return unavailableRooms; // Return immediately, everything is blocked
  }
  
  // Check each existing booking for time conflicts
  bookedRoomsData.forEach((booking, index) => {
    console.log(`\n📅 Checking booking ${index + 1}:`, {
      id: booking.id,
      type: booking.reservation_type,
      date: booking.checkin_date,
      time: booking.checkin_time,
      duration: booking.duration,
      rooms: booking.rooms_array,
      resort_rooms: booking.resort_rooms_array,
      resort_duration: booking.resort_room_duration
    });
    
    // Skip if different date
    if (booking.checkin_date !== checkinDate) {
      console.log('  ⏩ Different date, skipping');
      return;
    }
    
    // ✅ CHECK REGULAR ROOM CONFLICTS (Room type bookings)
    if (booking.rooms_array && booking.rooms_array.length > 0) {
      // ⚠️ CRITICAL: If booking has no time/duration data, block entire day for safety
      if (!booking.checkin_time || !booking.duration_hours || booking.duration_hours === 0) {
        console.log('  ⚠️ WARNING: Room booking missing time/duration - BLOCKING ENTIRE DAY');
        booking.rooms_array.forEach(room => {
          unavailableRooms.add(room);
          console.log('    🚫 Blocked (entire day - room):', room);
        });
      } else {
        // Parse existing booking's time and duration
        const bookedCheckinMinutes = timeToMinutes(booking.checkin_time);
        const bookedDurationHours = parseInt(booking.duration_hours, 10);
        const bookedCheckoutMinutes = bookedCheckinMinutes + (bookedDurationHours * 60);
        
        console.log('  ⏰ Existing ROOM booking window:', {
          checkinTime: booking.checkin_time,
          checkinMinutes: bookedCheckinMinutes,
          duration: bookedDurationHours + ' hours',
          checkoutMinutes: bookedCheckoutMinutes,
          checkoutTime: minutesToTime(bookedCheckoutMinutes)
        });
        
        // ✅ SMART CONFLICT DETECTION
        const hasConflict = timeRangesOverlap(
          newCheckinMinutes, 
          newCheckoutMinutes, 
          bookedCheckinMinutes, 
          bookedCheckoutMinutes
        );
        
        if (hasConflict) {
          console.log('  ⚠️ ROOM TIME CONFLICT DETECTED!');
          console.log('    New booking:', minutesToTime(newCheckinMinutes), '-', minutesToTime(newCheckoutMinutes));
          console.log('    Existing room booking:', minutesToTime(bookedCheckinMinutes), '-', minutesToTime(bookedCheckoutMinutes));
          
          // Block all rooms in this booking
          booking.rooms_array.forEach(room => {
            unavailableRooms.add(room);
            console.log('    🚫 Blocked due to room conflict:', room);
          });
        } else {
          console.log('  ✅ No room conflict - time slots don\'t overlap');
        }
      }
    }
    
    // ✅ CHECK RESORT ROOM CONFLICTS (Resort type bookings)
    if (booking.resort_rooms_array && booking.resort_rooms_array.length > 0) {
      // ⚠️ CRITICAL: If booking has no time/duration data, block entire day for safety
      if (!booking.checkin_time || !booking.resort_duration_hours || booking.resort_duration_hours === 0) {
        console.log('  ⚠️ WARNING: Resort booking missing time/duration - BLOCKING ENTIRE DAY');
        booking.resort_rooms_array.forEach(room => {
          unavailableRooms.add(room);
          console.log('    🚫 Blocked (entire day - resort):', room);
        });
      } else {
        // Parse existing resort booking's time and duration
        const resortCheckinMinutes = timeToMinutes(booking.checkin_time);
        const resortDurationHours = parseInt(booking.resort_duration_hours, 10);
        const resortCheckoutMinutes = resortCheckinMinutes + (resortDurationHours * 60);
        
        console.log('  ⏰ Existing RESORT booking window:', {
          checkinTime: booking.checkin_time,
          checkinMinutes: resortCheckinMinutes,
          duration: resortDurationHours + ' hours',
          checkoutMinutes: resortCheckoutMinutes,
          checkoutTime: minutesToTime(resortCheckoutMinutes)
        });
        
        // ✅ SMART CONFLICT DETECTION
        const hasConflict = timeRangesOverlap(
          newCheckinMinutes, 
          newCheckoutMinutes, 
          resortCheckinMinutes, 
          resortCheckoutMinutes
        );
        
        if (hasConflict) {
          console.log('  ⚠️ RESORT TIME CONFLICT DETECTED!');
          console.log('    New booking:', minutesToTime(newCheckinMinutes), '-', minutesToTime(newCheckoutMinutes));
          console.log('    Existing resort booking:', minutesToTime(resortCheckinMinutes), '-', minutesToTime(resortCheckoutMinutes));
          
          // Block all resort rooms in this booking
          booking.resort_rooms_array.forEach(room => {
            unavailableRooms.add(room);
            console.log('    🚫 Blocked due to resort conflict:', room);
          });
        } else {
          console.log('  ✅ No resort conflict - time slots don\'t overlap');
        }
      }
    }
  });
  
  console.log('\n🎯 Final unavailable rooms (including resort conflicts):', Array.from(unavailableRooms));
  return unavailableRooms;
}

// Helper function to convert minutes back to time string for display
function minutesToTime(minutes) {
  const actualMinutes = minutes % 1440;
  const hours = Math.floor(actualMinutes / 60);
  const mins = actualMinutes % 60;
  return hours.toString().padStart(2, '0') + ':' + mins.toString().padStart(2, '0');
}

// Check if a date is fully booked (with rest day check and resort rooms)
function isDateFullyBooked(dateStr) {
  // ✅ If it's a rest day, it's fully booked
  if (restDaysData.includes(dateStr)) {
    console.log('🚫 Date is a rest day (fully booked):', dateStr);
    return true;
  }
  
  if (!allRoomsList.length || !bookedRoomsData.length) return false;
  
  const bookingsOnDate = bookedRoomsData.filter(b => b.checkin_date === dateStr);
  
  if (bookingsOnDate.length === 0) return false;
  
  let allRoomsBlockedAllDay = true;
  
  for (const room of allRoomsList) {
    let roomBlockedAllDay = false;
    
    // Check both regular room bookings and resort room bookings
    const roomBookings = bookingsOnDate.filter(b => 
      (b.rooms_array && b.rooms_array.includes(room)) ||
      (b.resort_rooms_array && b.resort_rooms_array.includes(room))
    );
    
    if (roomBookings.length === 0) {
      allRoomsBlockedAllDay = false;
      break;
    }
    
    for (const booking of roomBookings) {
      // Check regular room duration
      if (booking.rooms_array && booking.rooms_array.includes(room)) {
        if (!booking.checkin_time || !booking.duration_hours || booking.duration_hours === 0) {
          roomBlockedAllDay = true;
          break;
        }
        
        if (booking.duration_hours >= 24) {
          roomBlockedAllDay = true;
          break;
        }
      }
      
      // Check resort room duration
      if (booking.resort_rooms_array && booking.resort_rooms_array.includes(room)) {
        if (!booking.checkin_time || !booking.resort_duration_hours || booking.resort_duration_hours === 0) {
          roomBlockedAllDay = true;
          break;
        }
        
        if (booking.resort_duration_hours >= 24) {
          roomBlockedAllDay = true;
          break;
        }
      }
    }
    
    if (!roomBlockedAllDay) {
      allRoomsBlockedAllDay = false;
      break;
    }
  }
  
  if (allRoomsBlockedAllDay) {
    console.log('🚫 Date fully booked (all rooms unavailable including resort):', dateStr);
  }
  
  return allRoomsBlockedAllDay;
}

// Get all fully booked dates (including rest days and resort bookings)
function getFullyBookedDates() {
  const fullyBookedDates = [...restDaysData]; // Start with rest days
  
  if (!allRoomsList.length || !bookedRoomsData.length) return fullyBookedDates;
  
  const uniqueDates = [...new Set(bookedRoomsData.map(b => b.checkin_date))];
  uniqueDates.forEach(dateStr => {
    if (!fullyBookedDates.includes(dateStr) && isDateFullyBooked(dateStr)) {
      fullyBookedDates.push(dateStr);
    }
  });
  
  return fullyBookedDates;
}

// Store globally which rooms are blocked by existing bookings
window.conflictBlockedRooms = new Set();

// Enhanced checkRoomAvailability function with rest day blocking and resort rooms
function checkRoomAvailability() {
  console.log('🔍 Checking room availability (including resort bookings)...');
  
  const checkinDate = document.getElementById('checkin').value;
  const checkinTime = document.getElementById('time').value;
  const duration = document.getElementById('check_in_time').value;
  const roomCheckboxes = document.querySelectorAll('.room-checkbox');
  
  console.log('📋 Form values:', {
    date: checkinDate,
    time: checkinTime,
    duration: duration
  });
  
  const warningMsg = document.getElementById('room-warning-msg');
  
  // ✅ Check for conflicts if date/time/duration are filled
  if (checkinDate && checkinTime && duration) {
    console.log('✅ Date/time/duration filled, checking conflicts...');
    const unavailableRooms = getUnavailableRooms(checkinDate, checkinTime, duration);
    console.log('🚫 Unavailable rooms (incl. resort conflicts):', Array.from(unavailableRooms));
    
    window.conflictBlockedRooms = unavailableRooms;
    
    // Apply booking conflict blocking
    roomCheckboxes.forEach(checkbox => {
      const roomName = checkbox.value;
      
      if (unavailableRooms.has(roomName)) {
        console.log('❌ Blocking room due to conflict:', roomName);
        
        checkbox.setAttribute('data-booking-blocked', 'true');
        checkbox.disabled = true;
        checkbox.checked = false;
        checkbox.classList.add('room-unavailable');
        
        const label = checkbox.nextElementSibling;
        if (label) {
          label.classList.add('room-unavailable-label');
          // ✅ Check if rest day is the reason
          if (restDaysData.includes(checkinDate)) {
            label.title = 'Rest day - No bookings allowed';
          } else {
            const nextDay = getNextDay(checkinDate);
            const durationMatch = duration.match(/(\d+)\s*Hour/i);
            const durationHours = durationMatch ? parseInt(durationMatch[1], 10) : 0;
            if (bookingExtendsToNextDay(checkinTime, durationHours) && restDaysData.includes(nextDay)) {
              label.title = 'Booking would extend into rest day';
            } else {
              label.title = 'This room is already booked for the selected time';
            }
          }
        }
      } else {
        checkbox.removeAttribute('data-booking-blocked');
        checkbox.classList.remove('room-unavailable');
        
        const label = checkbox.nextElementSibling;
        if (label) {
          label.classList.remove('room-unavailable-label');
          label.title = '';
        }
        
        if (!checkbox.hasAttribute('data-guest-disabled')) {
          checkbox.disabled = false;
        }
      }
    });
    
    // Show warning with appropriate message
    if (unavailableRooms.size > 0 && warningMsg) {
      let warningText = `⚠️ ${unavailableRooms.size} room(s) unavailable`;
      
      if (restDaysData.includes(checkinDate)) {
        warningText = '🚫 Rest day - No room bookings allowed';
      } else {
        const nextDay = getNextDay(checkinDate);
        const durationMatch = duration.match(/(\d+)\s*Hour/i);
        const durationHours = durationMatch ? parseInt(durationMatch[1], 10) : 0;
        if (bookingExtendsToNextDay(checkinTime, durationHours) && restDaysData.includes(nextDay)) {
          warningText = '🚫 Booking would extend into rest day - No room bookings allowed';
        } else {
          warningText = `⚠️ ${unavailableRooms.size} room(s) unavailable for selected date/time`;
        }
      }
      
      warningMsg.innerHTML = warningText;
      warningMsg.style.display = 'block';
      warningMsg.style.color = '#dc3545';
      warningMsg.style.fontWeight = 'bold';
      warningMsg.style.backgroundColor = '#fff3cd';
      warningMsg.style.padding = '8px';
      warningMsg.style.borderRadius = '4px';
      warningMsg.style.border = '1px solid #ffc107';
      warningMsg.style.marginTop = '8px';
    } else if (warningMsg) {
      warningMsg.style.display = 'none';
      warningMsg.innerHTML = '';
    }
  } else {
    console.log('⚠️ Not all date/time/duration fields filled yet');
    
    window.conflictBlockedRooms = new Set();
    roomCheckboxes.forEach(checkbox => {
      checkbox.removeAttribute('data-booking-blocked');
      checkbox.classList.remove('room-unavailable');
      
      const label = checkbox.nextElementSibling;
      if (label) {
        label.classList.remove('room-unavailable-label');
        label.title = '';
      }
      
      if (!checkbox.hasAttribute('data-guest-disabled')) {
        checkbox.disabled = false;
      }
    });
    
    if (warningMsg) {
      warningMsg.style.display = 'none';
      warningMsg.innerHTML = '';
    }
  }
  
  if (typeof window.validateRoomSelection === 'function') {
    window.validateRoomSelection();
  }
}

// Add a MutationObserver to prevent other code from re-enabling blocked rooms
function protectBlockedRooms() {
  const roomCheckboxes = document.querySelectorAll('.room-checkbox');
  
  roomCheckboxes.forEach(checkbox => {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === 'attributes' && mutation.attributeName === 'disabled') {
          if (checkbox.hasAttribute('data-booking-blocked') && !checkbox.disabled) {
            console.log('🛡️ Protecting blocked room from being enabled:', checkbox.value);
            checkbox.disabled = true;
          }
        }
      });
    });
    
    observer.observe(checkbox, { attributes: true });
  });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  console.log('🔄 Loading rest days and all booked rooms (including resort)...');
  
  // Add event listeners for hidden field updates
  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('room-checkbox')) {
      updateSelectedRooms();
      validateRoomSelection();
      calculatePrice();
    }
  });

  const timeInput = document.getElementById('time');
  if (timeInput) {
    timeInput.addEventListener('change', function() {
      updateCheckinTime();
      checkRoomAvailability();
    });
  }

  const durationSelect = document.getElementById('check_in_time');
  if (durationSelect) {
    durationSelect.addEventListener('change', function() {
      updateDuration();
      calculatePrice();
      checkRoomAvailability();
    });
  }
  
  // ✅ Load rest days first, then booked rooms
  loadRestDays().then(() => {
    return loadBookedRooms();
  }).then(() => {
    const checkRoomsLoaded = setInterval(() => {
      if (window.roomPrices && document.querySelectorAll('.room-checkbox').length > 0) {
        clearInterval(checkRoomsLoaded);
        
        allRoomsList = Array.from(document.querySelectorAll('.room-checkbox')).map(cb => cb.value);
        console.log('✓ All rooms loaded:', allRoomsList);
        
        const fullyBookedDates = getFullyBookedDates();
        console.log('✓ Fully booked dates (including rest days & resort bookings):', fullyBookedDates);
        
        // Re-initialize flatpickr with blocked dates
        const checkinInput = document.getElementById('checkin');
        if (checkinInput._flatpickr) {
          checkinInput._flatpickr.destroy();
        }
        
        flatpickr("#checkin", {
          altInput: true,
          altFormat: "F j, Y",
          dateFormat: "Y-m-d",
          minDate: new Date().fp_incr(1),
          disable: fullyBookedDates,
          onChange: function() {
            checkRoomAvailability();
          }
        });
        
        console.log('✓ Date picker updated with blocked dates, rest days, and resort bookings');
        
        const checkinInputEvent = document.getElementById('checkin');
        if (checkinInputEvent) {
          checkinInputEvent.addEventListener('change', checkRoomAvailability);
        }
        
        console.log('✓ Room blocking system with rest days and resort rooms initialized');
        
        protectBlockedRooms();
      }
    }, 100);
  });
});
</script>

<script>
// ===== 1-HOUR BUFFER SYSTEM =====
// Add this script block to your HTML - it will override the existing functions

// Override: Check if two time ranges overlap (WITH 1-HOUR BUFFER)
window.timeRangesOverlap = function(start1, end1, start2, end2) {
  // Add 60 minutes (1 hour) buffer to the end time of existing bookings
  const bufferedEnd2 = end2 + 60;
  console.log('  🕐 Buffer applied: Original end=' + end2 + ', Buffered end=' + bufferedEnd2 + ' (+60 min)');
  return start1 < bufferedEnd2 && start2 < end1;
}

// Override: Get unavailable rooms for a specific date/time/duration (WITH 1-HOUR BUFFER)
window.getUnavailableRooms = function(checkinDate, checkinTime, duration) {
  console.log('🔎 Getting unavailable rooms for:', { checkinDate, checkinTime, duration });
  console.log('⏰ BUFFER SYSTEM ACTIVE: Adding 1-hour buffer after each booking');
  
  const unavailableRooms = new Set();
  
  if (!checkinDate || !checkinTime || !duration) {
    console.log('⚠️ Missing required fields');
    return unavailableRooms;
  }
  
  // Parse the new booking's time and duration
  const newCheckinMinutes = timeToMinutes(checkinTime);
  const durationMatch = duration.match(/(\d+)\s*Hour/i);
  if (!durationMatch) {
    console.log('⚠️ Could not parse duration:', duration);
    return unavailableRooms;
  }
  
  const newDurationHours = parseInt(durationMatch[1], 10);
  const newCheckoutMinutes = newCheckinMinutes + (newDurationHours * 60);
  
  console.log('📊 New booking window:', {
    date: checkinDate,
    checkinTime: checkinTime,
    checkinMinutes: newCheckinMinutes,
    duration: newDurationHours + ' hours',
    checkoutMinutes: newCheckoutMinutes,
    checkoutTime: minutesToTime(newCheckoutMinutes)
  });
  
  // ✅ CHECK REST DAYS - Block ALL rooms if today is a rest day
  if (restDaysData.includes(checkinDate)) {
    console.log('🚫 TODAY IS A REST DAY - Blocking ALL rooms');
    allRoomsList.forEach(room => {
      unavailableRooms.add(room);
      console.log('    🚫 Blocked (rest day):', room);
    });
    return unavailableRooms;
  }
  
  // ✅ CHECK IF BOOKING EXTENDS TO NEXT DAY (overnight)
  const nextDay = getNextDay(checkinDate);
  const extendsToNextDay = bookingExtendsToNextDay(checkinTime, newDurationHours);
  
  if (extendsToNextDay && restDaysData.includes(nextDay)) {
    console.log('🚫 BOOKING EXTENDS INTO NEXT DAY WHICH IS A REST DAY - Blocking ALL rooms');
    allRoomsList.forEach(room => {
      unavailableRooms.add(room);
      console.log('    🚫 Blocked (overnight into rest day):', room);
    });
    return unavailableRooms;
  }
  
  // Check each existing booking for time conflicts
  bookedRoomsData.forEach((booking, index) => {
    console.log(`\n📅 Checking booking ${index + 1}:`, {
      id: booking.id,
      type: booking.reservation_type,
      date: booking.checkin_date,
      time: booking.checkin_time,
      duration: booking.duration,
      rooms: booking.rooms_array,
      resort_rooms: booking.resort_rooms_array,
      resort_duration: booking.resort_room_duration
    });
    
    // Skip if different date
    if (booking.checkin_date !== checkinDate) {
      console.log('  ⏩ Different date, skipping');
      return;
    }
    
    // ✅ CHECK REGULAR ROOM CONFLICTS (Room type bookings) WITH BUFFER
    if (booking.rooms_array && booking.rooms_array.length > 0) {
      if (!booking.checkin_time || !booking.duration_hours || booking.duration_hours === 0) {
        console.log('  ⚠️ WARNING: Room booking missing time/duration - BLOCKING ENTIRE DAY');
        booking.rooms_array.forEach(room => {
          unavailableRooms.add(room);
          console.log('    🚫 Blocked (entire day - room):', room);
        });
      } else {
        const bookedCheckinMinutes = timeToMinutes(booking.checkin_time);
        const bookedDurationHours = parseInt(booking.duration_hours, 10);
        const bookedCheckoutMinutes = bookedCheckinMinutes + (bookedDurationHours * 60);
        
        // ⏰ ADD 1-HOUR BUFFER (60 minutes)
        const bookedCheckoutWithBuffer = bookedCheckoutMinutes + 60;
        
        console.log('  ⏰ Existing ROOM booking window (WITH 1-HOUR BUFFER):', {
          checkinTime: booking.checkin_time,
          checkinMinutes: bookedCheckinMinutes,
          duration: bookedDurationHours + ' hours',
          checkoutMinutes: bookedCheckoutMinutes,
          checkoutTime: minutesToTime(bookedCheckoutMinutes),
          bufferCheckoutMinutes: bookedCheckoutWithBuffer,
          bufferCheckoutTime: minutesToTime(bookedCheckoutWithBuffer),
          bufferNote: '✅ +1 hour buffer applied'
        });
        
        // ✅ SMART CONFLICT DETECTION WITH BUFFER
        const hasConflict = timeRangesOverlap(
          newCheckinMinutes, 
          newCheckoutMinutes, 
          bookedCheckinMinutes, 
          bookedCheckoutWithBuffer  // ⏰ USE BUFFERED CHECKOUT TIME
        );
        
        if (hasConflict) {
          console.log('  ⚠️ ROOM TIME CONFLICT DETECTED (including 1-hour buffer)!');
          console.log('    New booking:', minutesToTime(newCheckinMinutes), '-', minutesToTime(newCheckoutMinutes));
          console.log('    Existing booking:', minutesToTime(bookedCheckinMinutes), '-', minutesToTime(bookedCheckoutMinutes));
          console.log('    With buffer:', minutesToTime(bookedCheckinMinutes), '-', minutesToTime(bookedCheckoutWithBuffer), '(+1 hour)');
          
          booking.rooms_array.forEach(room => {
            unavailableRooms.add(room);
            console.log('    🚫 Blocked due to room conflict (with buffer):', room);
          });
        } else {
          console.log('  ✅ No room conflict - time slots don\'t overlap (buffer considered)');
        }
      }
    }
    
    // ✅ CHECK RESORT ROOM CONFLICTS (Resort type bookings) WITH BUFFER
    if (booking.resort_rooms_array && booking.resort_rooms_array.length > 0) {
      if (!booking.checkin_time || !booking.resort_duration_hours || booking.resort_duration_hours === 0) {
        console.log('  ⚠️ WARNING: Resort booking missing time/duration - BLOCKING ENTIRE DAY');
        booking.resort_rooms_array.forEach(room => {
          unavailableRooms.add(room);
          console.log('    🚫 Blocked (entire day - resort):', room);
        });
      } else {
        const resortCheckinMinutes = timeToMinutes(booking.checkin_time);
        const resortDurationHours = parseInt(booking.resort_duration_hours, 10);
        const resortCheckoutMinutes = resortCheckinMinutes + (resortDurationHours * 60);
        
        // ⏰ ADD 1-HOUR BUFFER (60 minutes)
        const resortCheckoutWithBuffer = resortCheckoutMinutes + 60;
        
        console.log('  ⏰ Existing RESORT booking window (WITH 1-HOUR BUFFER):', {
          checkinTime: booking.checkin_time,
          checkinMinutes: resortCheckinMinutes,
          duration: resortDurationHours + ' hours',
          checkoutMinutes: resortCheckoutMinutes,
          checkoutTime: minutesToTime(resortCheckoutMinutes),
          bufferCheckoutMinutes: resortCheckoutWithBuffer,
          bufferCheckoutTime: minutesToTime(resortCheckoutWithBuffer),
          bufferNote: '✅ +1 hour buffer applied'
        });
        
        // ✅ SMART CONFLICT DETECTION WITH BUFFER
        const hasConflict = timeRangesOverlap(
          newCheckinMinutes, 
          newCheckoutMinutes, 
          resortCheckinMinutes, 
          resortCheckoutWithBuffer  // ⏰ USE BUFFERED CHECKOUT TIME
        );
        
        if (hasConflict) {
          console.log('  ⚠️ RESORT TIME CONFLICT DETECTED (including 1-hour buffer)!');
          console.log('    New booking:', minutesToTime(newCheckinMinutes), '-', minutesToTime(newCheckoutMinutes));
          console.log('    Existing booking:', minutesToTime(resortCheckinMinutes), '-', minutesToTime(resortCheckoutMinutes));
          console.log('    With buffer:', minutesToTime(resortCheckinMinutes), '-', minutesToTime(resortCheckoutWithBuffer), '(+1 hour)');
          
          booking.resort_rooms_array.forEach(room => {
            unavailableRooms.add(room);
            console.log('    🚫 Blocked due to resort conflict (with buffer):', room);
          });
        } else {
          console.log('  ✅ No resort conflict - time slots don\'t overlap (buffer considered)');
        }
      }
    }
  });
  
  console.log('\n🎯 Final unavailable rooms (with 1-hour buffer applied):', Array.from(unavailableRooms));
  return unavailableRooms;
}

console.log('✅ 1-HOUR BUFFER SYSTEM LOADED - All bookings now have +60 minute buffer');
</script>

<script>
// ===== ROOM SELECTION DISPLAY SYSTEM =====
// This will update the button text to show selected rooms

document.addEventListener('DOMContentLoaded', function() {
  // Wait for room checkboxes to be loaded
  setTimeout(function() {
    const roomCheckboxes = document.querySelectorAll('.room-checkbox');
    const dropdownBtn = document.getElementById('dropdownBtn');
    
    if (!dropdownBtn) {
      console.error('Dropdown button not found');
      return;
    }
    
    // Function to update button text based on selected rooms
    function updateRoomSelectionDisplay() {
      const selectedRooms = Array.from(document.querySelectorAll('.room-checkbox:checked'))
        .map(cb => cb.value);
      
      if (selectedRooms.length === 0) {
        dropdownBtn.textContent = 'Select Rooms';
      } else if (selectedRooms.length === 1) {
        dropdownBtn.textContent = selectedRooms[0];
      } else {
        // Show all selected rooms separated by comma
        dropdownBtn.textContent = selectedRooms.join(', ');
      }
      
      console.log('✓ Button updated with selected rooms:', selectedRooms.join(', '));
    }
    
    // Add event listeners to all room checkboxes
    roomCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        updateRoomSelectionDisplay();
      });
    });
    
    // Also update when guests field changes (in case rooms are auto-deselected)
    const guestsInput = document.getElementById('guests');
    if (guestsInput) {
      guestsInput.addEventListener('input', function() {
        // Small delay to let other validation run first
        setTimeout(updateRoomSelectionDisplay, 50);
      });
    }
    
    console.log('✅ Room Selection Display System Loaded');
  }, 500); // Wait 500ms for rooms to load
});
</script>

</body>
</html>