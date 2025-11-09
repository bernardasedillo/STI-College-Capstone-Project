        <?php
        //resrvation-events.php
        require 'includes/connect.php';

        function getAffiliateOptions($category, $selected = '') {
            global $conn; // your DB connection

            $stmt = $conn->prepare("SELECT id, name FROM prices WHERE venue = 'Affiliates' AND notes = ? ORDER BY id ASC");
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();

            $options = "<option value='' disabled selected>Select $category (Optional)</option>";
            while ($row = $result->fetch_assoc()) {
                $isSelected = ($row['name'] == $selected) ? "selected" : "";
                $options .= "<option value='{$row['id']}' $isSelected>{$row['name']}</option>";
            }
            return $options;
        }

        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Renato's Place Private Resort and Events</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <!-- Custom Styles -->
        <link rel="stylesheet" href="assets/css/navbar.css">
        <link rel="stylesheet" href="assets/css/homeStyle.css">
        <link rel="stylesheet" href="assets/css/modern-form.css">
        <link rel="icon" href="assets/favicon.ico">
        
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <style>
        
/* ===== ENHANCED BLOCKING STYLES FOR EVENT PACKAGES ===== */

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

/* ===== EVENT PACKAGE BLOCKING STYLES ===== */

/* Disabled/blocked event package options */
#events_package_selection_package option:disabled {
  background-color: #f8d7da !important;
  color: #721c24 !important;
  cursor: not-allowed !important;
  opacity: 0.6 !important;
  font-style: italic;
  position: relative;
}

/* Diagonal strikethrough line for disabled options - visual effect */
#events_package_selection_package option:disabled::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 2px;
  background-color: #dc3545;
  transform: translateY(-50%) rotate(-5deg);
  pointer-events: none;
  z-index: 1;
}

/* Add blocked icon/indicator to disabled event packages */
#events_package_selection_package option:disabled::after {
  content: '🚫';
  position: absolute;
  left: 5px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 12px;
}

/* Warning message styling for events */
#duration-room-warning-msg,
.availability-note {
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

/* Venue time restriction message */
.venue-time-restriction {
  color: #666;
  font-size: 13px;
  margin-top: 4px;
  font-style: italic;
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

/* Container for room checkboxes */
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

/* Locked/read-only select styling for affiliate services */
.locked-select {
  background-color: #f0f0f0 !important;
  cursor: not-allowed !important;
}

/* Dropdown content styling for rooms and additional fees */
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 200px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
  max-height: 300px;
  overflow-y: auto;
  border-radius: 4px;
  padding: 8px;
}

.dropdown-item {
  padding: 8px 12px;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  border-radius: 3px;
  transition: background-color 0.2s;
}

.dropdown-item:hover {
  background-color: #e7f3ff;
}

.dropdown-item input[type="checkbox"] {
  cursor: pointer;
}

.dropdown-item label {
  cursor: pointer;
  margin: 0;
  user-select: none;
}

/* Add to your existing <style> section */

/* Renatos locked time input styling */
#time[data-renatos-locked="true"] {
    border: 2px solid #17a2b8 !important;
    background-color: #f0f0f0 !important;
    color: #666 !important;
    -webkit-user-select: none;
    user-select: none;
    pointer-events: none !important;
}

/* Override any pointer events on parent */
#time[data-renatos-locked="true"]::-webkit-calendar-picker-indicator {
    display: none !important;
}

#time[data-renatos-locked="true"]::-webkit-inner-spin-button,
#time[data-renatos-locked="true"]::-webkit-clear-button {
    display: none !important;
}

@keyframes slideUp {
    from {
        bottom: -50px;
        opacity: 0;
    }
    to {
        bottom: 20px;
        opacity: 1;
    }
}

label:has(+ input[required])::after,
label:has(+ select[required])::after,
label:has(+ textarea[required])::after,
label[data-required="true"]::after {
    content: " *";
    color: #cc4b4b; /* soft red */
    font-weight: bold;
}
</style>
        </style>
        </head>

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
                            <option value="" disabled selected>Select a reservation type</option>
                            <option value="Room">Room</option>
                            <option value="Resort">Resort</option>
                            <option value="Event Package">Events Place</option>
                        </select>
                    </div>

                       <!-- Customer details (common for all types) -->
                    <div class="form-group">
                        <label for="full_name" data-required="true">Full Name</label>
                        <input type="text" name="full_name" id="full_name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email" data-required="true">Email</label>
                        <input type="email" name="email" id="email" placeholder="Enter your email address" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" data-required="true">Mobile Number</label>
                        <input type="text" id="phone" name="phone" placeholder="09XXXXXXXXX" maxlength="11" required>
                        <span class="error-message" id="phone-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="full_address" data-required="true">Full Address</label>
                        <input type="text" name="full_address" id="full_address" placeholder="Enter your full address" required>
                    </div>

                    <!-- Common fields for Room, Resort, and Event Package -->
                    <div class="form-group">
                        <label for="checkin" data-required="true">Check-In Date</label>
                        <input type="text" name="checkin" id="checkin" placeholder="Select a date" required>
                    </div>

                    <div class="form-group">
                        <label for="events_package_selection" data-required="true">Event Package</label>
                        <select name="events_package_selection" id="events_package_selection_package">
                            <option value="" disabled selected>Select a package</option>
                        </select>
                    </div>

                    <div class="form-group" style="display: none">
                        <label for="inclusions">Inclusions</label>
                        <ul id="inclusions" class="inclusions-list"></ul>
                    </div>

                    <div class="form-group">
                        <label data-required="true">Preferred Duration of Event</label>
                        <div style="display:flex; align-items:center; gap:6px;" class="flex-container">
                            <input type="number" id="hours" name="hours" min="0" max="48" placeholder="0" required>                    
                            <p style="font-size:15px; font-weight:200; margin:0 4px;">Hours</p>
                            <input type="number" id="minutes" name="minutes" min="0" max="59" value="00" placeholder="00" required>
                            <p style="font-size:15px; font-weight:200; margin:0 4px;">Minutes</p>
                        </div>
                        <div id="duration-error-msg" class="error-message" style="display:none;">This field is required.</div>
                        <div id="venue-warning-msg" class="warning-message" style="display:none; color:black; font-size:14px; margin-top:4px;"></div>
                    </div>

                    <div class="form-group">
                        <label for="time" data-required="true">Check-in Time</label>
                        <input type="text" id="time" name="time" placeholder="Select a time" required>
                    </div>

                    <div class="form-group">
                        <label for="guests" data-required="true">Number of Guests</label>
                        <input type="number" name="guests" id="guests" min="1" placeholder="Enter the number of guests" required>
                        <div id="guest-error-msg" class="warning-message" style="color:red; font-size:14px; margin-top:4px;"></div>
                        <div id="guest-warning-msg" class="warning-message" style="color:black; font-size:14px; margin-top:4px;"></div>
                    </div>

                    <!-- Event Package specific fields -->
                    <div class="form-group">
                        <label for="event_type" data-required="true">Event Type</label>
                        <input type="text" name="event_type" id="event_type" placeholder="e.g., Wedding, Birthday, Conference">
                    </div>


                        <div class="form-group">
                        <label for="event_package_selection_room">Room</label>
                        <div class="custom-dropdown" id="event_package_selection_room">
                            <button type="button" class="roombutton" id="dropdownBtn" onclick="toggleDropdown(this)">Select Rooms (Optional)</button>
                            <div class="dropdown-content" id="dropdown-content-rooms">
                            </div>
                        </div>
                        </div>

                        <div class="form-group">
                            <label for="check_in_time">Preferred duration of stay (Room)</label>
                            <select id="check_in_time" name="check_in_time" > 
                        </select>
                        <div id="duration-room-warning-msg" class="warning-message" style="display:none; color:black; font-size:14px; margin-top:4px;"></div>
                        </div>

                        
                        <div class="form-group">
                            <label for="catering">Catering</label>
                            <select id="catering" name="catering" data-category="Catering option"></select>
                        </div>

                        <div class="form-group">
                            <label for="lights">Lights and Sound</label>
                            <select id="lights" name="lights" data-category="Lights & Sound option"></select>
                        </div>

                        <div class="form-group">
                            <label for="mobile-bar">Mobile Bar</label>
                            <select id="mobile-bar" name="mobile_bar" data-category="Mobile Bar option"></select>
                        </div>

                        <div class="form-group">
                            <label for="grazing-table">Grazing Table</label>
                            <select id="grazing-table" name="grazing_table" data-category="Grazing Table option"></select>
                        </div>

                    <div class="form-group">
                        <label for="additional-fee">Additional Venue Fees</label>
                        <div class="custom-dropdown" id="event_package_selection_additional_fee">
                            <button type="button" class="roombutton" onclick="toggleDropdown(this)">
                                Select Additional Venue Fees (Optional)
                            </button>
                            <div class="dropdown-content" id="additional-fee" data-category="Additional Venue Fee"></div>                
                        </div>
                    </div>
                

                    
                        <div class="form-group full-width">
                            <label for="message">Special Requests</label>
                            <textarea name="message" id="request-message" rows="4" placeholder="Any special requests or notes..."></textarea>
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

                        <input type="hidden" name="duration_hours" id="hidden_duration_hours" value="0">
                        <input type="hidden" name="duration_minutes" id="hidden_duration_minutes" value="0">
                        <input type="hidden" name="events_package_selection" id="hidden_events_package" value="">
                        <input type="hidden" name="selected_resort_rooms" id="hidden_selected_rooms" value="">
                        <input type="hidden" name="additional_fee_values" id="hidden_additional_fee" value="">
                        </div>

                        <div class="form-group full-width">
                            <button type="button" id="payment-button">Proceed to payment</button>
                        </div>
        </div>
                </form>

            </div>

            </section>
        </main>
        <!-- end main -->

            <script>


                // Initial state on page load
        function toggleDropdown(button) {
            const dropdown = button.nextElementSibling;

            // Close all dropdowns first
            document.querySelectorAll(".dropdown-content").forEach(dd => {
                if (dd !== dropdown) dd.style.display = "none";
            });

            // Toggle the one linked to this button
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

            // ✅ Close when clicking outside
            document.addEventListener("click", function (event) {
                if (!event.target.closest(".custom-dropdown")) {
                    document.querySelectorAll(".dropdown-content").forEach(dd => {
                        dd.style.display = "none";
                    });
                }
            });



                // REDIRECTING TO ANOTHER RESERVATION PAGE (RESORT AND ROOMS)
                // REDIRECTING TO ANOTHER RESERVATION PAGE (RESORT AND ROOMS)
                // REDIRECTING TO ANOTHER RESERVATION PAGE (RESORT AND ROOMS)
                document.addEventListener('DOMContentLoaded', function () {
                const reservationSelect = document.getElementById('reservation_type');
                    reservationSelect.value = 'Event Package';
                    reservationSelect.dispatchEvent(new Event('change'));
                });

            document.addEventListener('DOMContentLoaded', function () {
                const reservationSelect = document.getElementById('reservation_type');

            reservationSelect.addEventListener('change', function () {
            const value = this.value;

            if (value === 'Resort') {
                        window.location.href = 'reservation-resort.php';
                    } else if (value === 'Room') {
                        window.location.href = 'reservation-room.php';
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

                // Hide all conditional sections
                document.querySelectorAll(".type-room, .type-resort, .type-events").forEach(el => {
                    el.style.display = "none";
                });

                // Show only selected type
                if (selectedType === "room") {
                    document.querySelectorAll(".type-room").forEach(el => el.style.display = "block");
                } else if (selectedType === "resort") {
                    document.querySelectorAll(".type-resort").forEach(el => el.style.display = "block");
                } else if (selectedType === "events") {
                    document.querySelectorAll(".type-events").forEach(el => el.style.display = "block");
                }
                });
                



            //Displaying Reservation Field
            //Displaying Reservation Field
            //Displaying Reservation Field
            document.getElementById('reservation_type').addEventListener('change', function () {
            const eventFields = document.querySelectorAll('[data-event-only]');
            if (this.value === 'Event Package') {
                eventFields.forEach(el => el.style.display = 'block');
            } else {
                eventFields.forEach(el => el.style.display = 'none');
            }
            });


            //FETCH DATA TO THE DATABASE AND DISPLAY ON PACKAGE SELECTION
            //FETCH DATA TO THE DATABASE AND DISPLAY ON PACKAGE SELECTION
            // FETCH DATA FROM DATABASE AND DISPLAY ON PACKAGE SELECTION
            document.getElementById("checkin").addEventListener("change", function () {
            const date = this.value;
            if (!date) return;

            fetch("fetch_events_packages.php?date=" + encodeURIComponent(date))
                .then(res => res.json())
                .then(data => {
                const select = document.getElementById("events_package_selection_package");
                if (!select) return console.error("No package select found (events_package_selection_package)");

                // Clear old options
                select.innerHTML = "";

                // Default option
                const defaultOpt = document.createElement('option');
                defaultOpt.value = "";
                defaultOpt.disabled = true;
                defaultOpt.selected = true;
                defaultOpt.textContent = "Select a package";
                select.appendChild(defaultOpt);

                if (data.error) {
                    const errOpt = document.createElement('option');
                    errOpt.disabled = true;
                    errOpt.textContent = data.error;
                    select.appendChild(errOpt);
                    return;
                }

                if (!data.packages || data.packages.length === 0) {
                    const none = document.createElement('option');
                    none.disabled = true;
                    none.textContent = "No packages available for this date";
                    select.appendChild(none);
                    return;
                }

                // Populate package options safely
                data.packages.forEach(pkg => {
                    const opt = document.createElement('option');
                    opt.value = pkg.id;
                    opt.textContent = `${pkg.venue}-${pkg.name}- ₱${Number(pkg.price).toLocaleString()}`;

                    // attributes
                    opt.setAttribute('data-venue', pkg.venue || '');
                    opt.setAttribute('data-name', pkg.name || '');
                    opt.setAttribute('data-day_type', pkg.day_type || '');
                    opt.setAttribute('data-duration', pkg.duration || '');
                    opt.setAttribute('data-duration_hours', pkg.duration_hours || '');
                    opt.setAttribute('data-price', pkg.price || '');
                    opt.setAttribute('data-affiliate_catering', pkg.affiliate_catering || '');
                    opt.setAttribute('data-affiliate_lights', pkg.affiliate_lights || '');
                    opt.setAttribute('data-affiliate_mobile_bar', pkg.affiliate_mobile_bar || '');
                    opt.setAttribute('data-affiliate_grazing_table', pkg.affiliate_grazing_table || '');
                    opt.setAttribute('data-inclusions', pkg.inclusions || '');

                    select.appendChild(opt);
                });

                // Attach onchange ONCE
                select.onchange = function () {
                    const selectedOption = select.selectedOptions[0];
                    if (!selectedOption) return;

                    // --- Affiliate autofill ---
                    setAffiliateValue("catering", selectedOption.getAttribute('data-affiliate_catering') || '');
                    setAffiliateValue("lights", selectedOption.getAttribute('data-affiliate_lights') || '');
                    setAffiliateValue("mobile-bar", selectedOption.getAttribute('data-affiliate_mobile_bar') || '');
                    setAffiliateValue("grazing-table", selectedOption.getAttribute('data-affiliate_grazing_table') || '');

                    // --- Inclusions ---
                    const inclusionsWrapper = document.querySelector('#inclusions').parentElement;
                    const inclusionsList = document.getElementById('inclusions');
                    inclusionsList.innerHTML = "";

                    const inclusionsRaw = selectedOption.getAttribute('data-inclusions') || "";
                    if (inclusionsRaw.trim() !== "") {
                    inclusionsWrapper.style.display = "block";
                    const items = inclusionsRaw.split(/[,;]+/).map(i => i.trim()).filter(Boolean);
                    items.forEach(text => {
                        const li = document.createElement("li");
                        li.textContent = text;
                        inclusionsList.appendChild(li);
                    });
                    } else {
                    inclusionsWrapper.style.display = "none";
                    }

                    // --- Hours (hardcoded by venue) ---
                    let includedHours = 0;
                    switch (selectedOption.getAttribute('data-venue')) {
                    case "Mini Function Hall":
                        includedHours = 3;
                        break;
                    case "Renatos Hall":
                    case "Renatos Pavilion":
                        includedHours = 4;
                        break;
                    }
                    const hoursInput = document.getElementById("hours");
                    const minutesInput = document.getElementById("minutes");
                    if (hoursInput) hoursInput.value = includedHours;
                    if (minutesInput) minutesInput.value = "00";
                };
                })
                .catch(err => console.error(err));
            });



            // Helper: Set and lock affiliate dropdowns
// REPLACE THE setAffiliateValue FUNCTION IN YOUR reservation-events.php WITH THIS:

// Helper: Set and lock affiliate dropdowns - FIXED VERSION
function setAffiliateValue(selectId, value) {
    const select = document.getElementById(selectId);
    if (!select) {
        console.warn(`Select element not found: ${selectId}`);
        return;
    }

    console.log(`Setting affiliate ${selectId} to value: ${value}`);

    if (value && value.trim() !== "") {
        const target = value.trim().toLowerCase();
        let matched = false;

        for (let option of select.options) {
            if (option.value.trim().toLowerCase() === target ||
                option.text.trim().toLowerCase() === target) {
                option.selected = true;
                matched = true;
                console.log(`✓ Matched ${selectId}: ${option.text} (ID: ${option.value})`);
                break;
            }
        }

        if (matched) {
            // ✅ FIX: Don't disable! Instead, add readonly styling and prevent changes
            select.classList.add('locked-select');
            select.dataset.locked = 'true';
            
            // Add visual indication that it's locked
            select.style.backgroundColor = '#f0f0f0';
            select.style.cursor = 'not-allowed';
            
            // Prevent user from changing it (but still submits!)
            select.addEventListener('mousedown', preventChange);
            select.addEventListener('keydown', preventChange);
            
        } else {
            console.warn(`No match found for ${selectId} with value: ${value}`);
            select.value = "";
            unlockSelect(select);
        }
    } else {
        // Clear selection
        select.value = "";
        unlockSelect(select);
    }
}

// Helper function to prevent changes to locked selects
function preventChange(e) {
    if (e.target.dataset.locked === 'true') {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
}

// Helper function to unlock a select
function unlockSelect(select) {
    select.classList.remove('locked-select');
    delete select.dataset.locked;
    select.style.backgroundColor = '';
    select.style.cursor = '';
    select.removeEventListener('mousedown', preventChange);
    select.removeEventListener('keydown', preventChange);
}

// When package changes, unlock all affiliates first
document.getElementById("checkin").addEventListener("change", function () {
    const date = this.value;
    if (!date) return;

    // Unlock all affiliate dropdowns before fetching new package data
    ['catering', 'lights', 'mobile-bar', 'grazing-table'].forEach(id => {
        const select = document.getElementById(id);
        if (select) unlockSelect(select);
    });

    fetch("fetch_events_packages.php?date=" + encodeURIComponent(date))
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById("events_package_selection_package");
            if (!select) return console.error("No package select found (events_package_selection_package)");

            // Clear old options
            select.innerHTML = "";

            // Default option
            const defaultOpt = document.createElement('option');
            defaultOpt.value = "";
            defaultOpt.disabled = true;
            defaultOpt.selected = true;
            defaultOpt.textContent = "Select a package";
            select.appendChild(defaultOpt);

            if (data.error) {
                const errOpt = document.createElement('option');
                errOpt.disabled = true;
                errOpt.textContent = data.error;
                select.appendChild(errOpt);
                return;
            }

            if (!data.packages || data.packages.length === 0) {
                const none = document.createElement('option');
                none.disabled = true;
                none.textContent = "No packages available for this date";
                select.appendChild(none);
                return;
            }

            // Populate package options safely
            data.packages.forEach(pkg => {
                const opt = document.createElement('option');
                opt.value = pkg.id;
                opt.textContent = `${pkg.venue}-${pkg.name}- ₱${Number(pkg.price).toLocaleString()}`;

                // attributes
                opt.setAttribute('data-venue', pkg.venue || '');
                opt.setAttribute('data-name', pkg.name || '');
                opt.setAttribute('data-day_type', pkg.day_type || '');
                opt.setAttribute('data-duration', pkg.duration || '');
                opt.setAttribute('data-duration_hours', pkg.duration_hours || '');
                opt.setAttribute('data-price', pkg.price || '');
                opt.setAttribute('data-affiliate_catering', pkg.affiliate_catering || '');
                opt.setAttribute('data-affiliate_lights', pkg.affiliate_lights || '');
                opt.setAttribute('data-affiliate_mobile_bar', pkg.affiliate_mobile_bar || '');
                opt.setAttribute('data-affiliate_grazing_table', pkg.affiliate_grazing_table || '');
                opt.setAttribute('data-inclusions', pkg.inclusions || '');

                select.appendChild(opt);
            });

            // Attach onchange ONCE
            select.onchange = function () {
                const selectedOption = select.selectedOptions[0];
                if (!selectedOption) return;

                console.log("=== PACKAGE SELECTED ===");
                console.log("Package ID:", selectedOption.value);
                console.log("Affiliate Catering:", selectedOption.getAttribute('data-affiliate_catering'));
                console.log("Affiliate Lights:", selectedOption.getAttribute('data-affiliate_lights'));
                console.log("Affiliate Mobile Bar:", selectedOption.getAttribute('data-affiliate_mobile_bar'));
                console.log("Affiliate Grazing Table:", selectedOption.getAttribute('data-affiliate_grazing_table'));

                // --- Affiliate autofill ---
                setAffiliateValue("catering", selectedOption.getAttribute('data-affiliate_catering') || '');
                setAffiliateValue("lights", selectedOption.getAttribute('data-affiliate_lights') || '');
                setAffiliateValue("mobile-bar", selectedOption.getAttribute('data-affiliate_mobile_bar') || '');
                setAffiliateValue("grazing-table", selectedOption.getAttribute('data-affiliate_grazing_table') || '');

                // --- Inclusions ---
                const inclusionsWrapper = document.querySelector('#inclusions').parentElement;
                const inclusionsList = document.getElementById('inclusions');
                inclusionsList.innerHTML = "";

                const inclusionsRaw = selectedOption.getAttribute('data-inclusions') || "";
                if (inclusionsRaw.trim() !== "") {
                    inclusionsWrapper.style.display = "block";
                    const items = inclusionsRaw.split(/[,;]+/).map(i => i.trim()).filter(Boolean);
                    items.forEach(text => {
                        const li = document.createElement("li");
                        li.textContent = text;
                        inclusionsList.appendChild(li);
                    });
                } else {
                    inclusionsWrapper.style.display = "none";
                }

                // --- Hours (hardcoded by venue) ---
                let includedHours = 0;
                switch (selectedOption.getAttribute('data-venue')) {
                    case "Mini Function Hall":
                        includedHours = 3;
                        break;
                    case "Renatos Hall":
                    case "Renatos Pavilion":
                        includedHours = 4;
                        break;
                }
                const hoursInput = document.getElementById("hours");
                const minutesInput = document.getElementById("minutes");
                if (hoursInput) hoursInput.value = includedHours;
                if (minutesInput) minutesInput.value = "00";
            };
        })
        .catch(err => console.error(err));
});

// ===== FORM SUBMISSION DEBUG - Add before form submits =====
document.querySelector('.reservation-form').addEventListener('submit', function(e) {
    console.log("=== FORM SUBMISSION - AFFILIATE VALUES ===");
    console.log("Catering:", document.getElementById("catering")?.value || "EMPTY");
    console.log("Lights:", document.getElementById("lights")?.value || "EMPTY");
    console.log("Mobile Bar:", document.getElementById("mobile-bar")?.value || "EMPTY");
    console.log("Grazing Table:", document.getElementById("grazing-table")?.value || "EMPTY");
});


            //FETCH AFFILIATES
            //FETCH AFFILIATES
            //FETCH AFFILIATES
            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll("[data-category]").forEach(el => {
                    const category = el.getAttribute("data-category");

                    fetch(`fetch_affiliates.php?category=${encodeURIComponent(category)}`)
                        .then(response => response.text())
                        .then(data => {
                            // If it's a select element, fill options
                            if (el.tagName === "SELECT") {
                                el.innerHTML = data;
                            } else {
                                // Otherwise assume div (checkboxes)
                                el.innerHTML = data;
                            }
                        })
                        .catch(error => console.error("Error fetching options:", error));
                });
            });




                

                



                //MOBILE NUMBER FIELD FUNCTIONS
                //MOBILE NUMBER FIELD FUNCTIONS
                //MOBILE NUMBER FIELD FUNCTIONS
                document.addEventListener('DOMContentLoaded', function () {
                    const phoneInput = document.getElementById('phone');
                    const phoneError = document.getElementById('phone-error');

                    phoneInput.addEventListener('focus', () => {
                        if (phoneInput.value === '') {
                            phoneInput.value = '09';
                        }
                    });

                    phoneInput.addEventListener('input', () => {
                        // Remove non-digit characters
                        phoneInput.value = phoneInput.value.replace(/\D/g, '');

                        // Enforce "09" prefix
                        if (!phoneInput.value.startsWith('09')) {
                            phoneInput.value = '09';
                        }

                        // Limit to 11 digits
                        if (phoneInput.value.length > 11) {
                            phoneInput.value = phoneInput.value.slice(0, 11);
                        }

                        // Red border if not exactly 11 digits
                        if (phoneInput.value.length < 11) {
                            phoneInput.style.borderColor = '';
                        } else {
                            phoneInput.style.borderColor = '';
                            phoneError.textContent = '';
                        }
                    });

                    phoneInput.addEventListener('keydown', (e) => {
                        // Prevent deleting or typing before '09'
                        if (phoneInput.selectionStart <= 2 && (e.key === 'Backspace' || e.key === 'Delete')) {
                            e.preventDefault();
                        }
                    });
                });






        // ERROR MESSAGES IF THE USER SUBMIT WITHOUT FILLING THE REQUIRED FILEDS     
        // ERROR MESSAGES IF THE USER SUBMIT WITHOUT FILLING THE REQUIRED FILEDS     
        // ERROR MESSAGES IF THE USER SUBMIT WITHOUT FILLING THE REQUIRED FILEDS     
const form = document.querySelector('.reservation-form');
const paymentButton = document.getElementById('payment-button');

paymentButton.addEventListener('click', function (e) {
    e.preventDefault();
    let isValid = true;
    const validatedFields = new Set(); // ✅ Track validated fields to prevent duplicates

    // Reset previous error styles/messages
    document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(field => {
        field.style.borderColor = '';
        field.style.boxShadow = '';
        // Remove ALL existing error messages
        const existingErrors = field.parentElement.querySelectorAll('.field-error');
        existingErrors.forEach(err => err.remove());
    });

    // Validate visible required fields (exclude optional + total payment)
    const visibleFields = document.querySelectorAll(
        '.reservation-form .form-group input:not([type="checkbox"]):not([type="hidden"]), ' +
        '.reservation-form .form-group select, ' +
        '.reservation-form .form-group textarea'
    );

    visibleFields.forEach(field => {
        // ✅ Skip if already validated
        if (validatedFields.has(field.id)) return;
        validatedFields.add(field.id);

        const parent = field.closest('.form-group');
        const isHidden = window.getComputedStyle(parent).display === 'none' || parent.offsetParent === null;

        // Skip total payment area
        if (parent && parent.querySelector('#total-payment-box')) return;

        // Skip optional fields
        if (
            field.id === 'request-message' ||
            field.id === 'affiliate_selection' ||
            field.id === 'hours' ||
            field.id === 'minutes' ||
            field.id === 'additional-fee' ||
            field.id === 'event_package_selection_room' ||
            field.id === 'catering' ||
            field.id === 'lights' ||
            field.id === 'check_in_time' ||
            field.id === 'mobile-bar' ||
            field.id === 'grazing-table'
        ) {
            return;
        }

        // 📱 SPECIAL VALIDATION FOR PHONE NUMBER
        if (field.id === 'phone' && !isHidden) {
            const phoneValue = field.value.trim();
            
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
            return; // Skip general validation for phone
        }

        // 🔴 GENERAL REQUIRED FIELD VALIDATION
        if (!isHidden && field.value.trim() === '') {
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

    // ✅ Room duration validation (only required if a room is selected)
    const roomCheckboxes = document.querySelectorAll('#event_package_selection_room input[type="checkbox"]');
    const checkInTimeSelect = document.getElementById('check_in_time');
    const durationWarning = document.getElementById('duration-room-warning-msg');

    const anyRoomSelected = Array.from(roomCheckboxes).some(cb => cb.checked);

    if (anyRoomSelected) {
        if (!checkInTimeSelect.value || checkInTimeSelect.value === "") {
            isValid = false;
            checkInTimeSelect.style.borderColor = 'red';
            durationWarning.style.display = "block";
            durationWarning.style.color = "red";
            durationWarning.textContent = "Please select a duration of stay for the room.";
        } else {
            checkInTimeSelect.style.borderColor = '';
            durationWarning.style.display = "none";
        }
    } else {
        checkInTimeSelect.style.borderColor = '';
        durationWarning.style.display = "none";
    }

    // ✅ Duration of Stay (hours + minutes)
    const hoursField = document.getElementById("hours");
    const minutesField = document.getElementById("minutes");
    const hours = parseInt(hoursField.value) || 0;
    const minutes = parseInt(minutesField.value) || 0;
    const durationError = document.getElementById("duration-error-msg");

    if (hours === 0 && minutes === 0) {
        durationError.style.display = "block";
        isValid = false;
        hoursField.style.borderColor = 'red';
        minutesField.style.borderColor = 'red';
    } else {
        durationError.style.display = "none";
        hoursField.style.borderColor = '';
        minutesField.style.borderColor = '';
        hoursField.style.boxShadow = '';
        minutesField.style.boxShadow = '';
    }

    // ✅ If valid → show terms modal, else scroll to top
    if (isValid) {
        document.getElementById('termsModal').style.display = 'flex';
    } else {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

// Remove error styles when user starts typing/changing value
document.querySelectorAll('.reservation-form .form-group input, .reservation-form .form-group select, .reservation-form .form-group textarea')
    .forEach(field => {
        field.addEventListener('input', () => {
            field.style.borderColor = '';
            field.style.boxShadow = '';
            const existingErrors = field.parentElement.querySelectorAll('.field-error');
            existingErrors.forEach(err => err.remove());

            if (field.id === 'hours' || field.id === 'minutes') {
                document.getElementById("duration-error-msg").style.display = "none";
            }
        });
    });

            // Remove error styling on user focus
            // Remove error styling on user focus
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




       document.addEventListener("DOMContentLoaded", function () {
    const packageSelect = document.getElementById("events_package_selection_package");
    const totalPaymentSpan = document.getElementById("total-payment");
    const hoursInput = document.getElementById("hours");
    const minutesInput = document.getElementById("minutes");
    const venueWarning = document.getElementById("venue-warning-msg");
    const dateInput = document.getElementById("checkin");
    const guestsInput = document.getElementById("guests");
    const guestErrorMsg = document.getElementById("guest-error-msg");
    const guestWarningMsg = document.getElementById("guest-warning-msg");

    let basePrice = 0;
    let excessRates = {};
    let guestExcessRates = {};
    let currentVenue = "";
    let currentPackageName = "";
    let showWarning = false;
    let showGuestWarning = false; // track guest warning visibility
    let roomPricesData = [];
    let currentPackage = null;

    let presetDurations = {
        "Mini Function Hall": 3,
        "Renatos Hall": 4,
        "Renatos Pavilion": 4
    };

    let guestLimits = {
        "Mini Function Hall": 50,
        "Renatos Hall": 80,
        "Renatos Pavilion": 250
    };

    // Helper: calculate total hours
    function getTotalHours() {
        const hrs = parseInt(hoursInput.value) || 0;
        const mins = parseInt(minutesInput.value) || 0;
        return hrs + mins / 60;
    }

    // --- Calculate room cost ---
    function calculateRoomCost() {
        const checkedBoxes = Array.from(document.querySelectorAll("#dropdown-content-rooms input[type=checkbox]:checked"));
        const selectedRooms = checkedBoxes.map(cb => cb.value);
        const selectedDuration = document.getElementById("check_in_time").value;

        if (selectedRooms.length === 0 || !selectedDuration) return 0;

        let total = 0;
        selectedRooms.forEach(roomName => {
            const match = roomPricesData.find(r =>
                String(r.name) === String(roomName) &&
                String(r.duration_hours) === String(selectedDuration)
            );

            if (match) {
                const p = parseFloat(match.price);
                total += (isFinite(p) ? p : 0);
            } else {
                console.warn("No room-price match for:", roomName, "duration:", selectedDuration);
            }
        });

        return total;
    }


    function getExcessRate(venueName, packageNotes) {
        if (!venueExcessRates[venueName]) return null;

        // Find the matching entry for the current venue + package notes
        return venueExcessRates[venueName].find(rate => {
            return rate.notes.toLowerCase() === packageNotes.toLowerCase();
        }) || null;
    }

    function updateTotalPayment() {
        let totalHours = getTotalHours();
        let excessCharge = 0;
        let guestExcessCharge = 0;

        // --- Base package price ---
        let packagePrice = parseFloat(currentPackage?.price) || 0;

        // --- Calculate room cost ---
        const roomCost = calculateRoomCost();

        // --- Recalculate additional venue fee ---
        let additionalVenueFeeTotal = 0;
        document.querySelectorAll("#additional-fee input[type=checkbox]:checked").forEach(cb => {
            const price = parseFloat(cb.getAttribute("data-price")) || 0;
            additionalVenueFeeTotal += price;
        });

        // --- Hours excess ---
        if (currentVenue && presetDurations[currentVenue] !== undefined) {
            const preset = presetDurations[currentVenue];
            const rate = excessRates[currentVenue] || 0;

            if (totalHours > preset) {
                const excessHours = totalHours - preset;
                excessCharge = excessHours * rate;

                if (showWarning) {
                    venueWarning.textContent = `For ${currentVenue}, additional ₱${rate.toLocaleString("en-PH", { minimumFractionDigits: 2 })} per hour will be charged.`;
                    venueWarning.style.display = "block";
                }
            } else {
                venueWarning.style.display = "none";
            }
        } else {
            venueWarning.style.display = "none";
        }

        // --- Guest Excess Logic ---
        let guests = parseInt(guestsInput.value) || 0;
        guestErrorMsg.textContent = "";
        guestWarningMsg.textContent = "";

        if (guests > 0 && currentVenue) {
            let warnings = [];

            const maxGuests = guestLimits[currentVenue] || null;
            if (maxGuests && guests > maxGuests) {
                warnings.push(`Maximum guests allowed for ${currentVenue} is ${maxGuests}.`);
                guestsInput.value = maxGuests;
                guests = maxGuests;
            }

            let includedGuests = currentPackage?.max_guest || 0; 
            let guestRate = 0;
            let matchedNote = "";

            if (currentVenue && guestExcessRates[currentVenue]) {
                const rates = guestExcessRates[currentVenue];

                function normalizeString(str) {
                    return str
                        .toLowerCase()
                        .replace(/for /g, "")
                        .replace(/package/g, "")
                        .replace(/exclusive/g, "")
                        .replace(/intimate/g, "")
                        .replace(/catering/g, "")
                        .replace(/[₱,]/g, "")
                        .replace(/[^a-z0-9]/g, " ") 
                        .replace(/\s+/g, " ")
                        .trim();
                }

                // ✅ FIX: ensure currentPackageName comes from DB package
                const pkgCheck = normalizeString(currentPackage?.name || "");
                // 🔍 Find matching rate using notes
                let bestMatch = null;
                for (let rate of rates) {
                    const noteCheck = normalizeString(rate.notes || "");
                    if (pkgCheck.includes(noteCheck) || noteCheck.includes(pkgCheck)) {
                        bestMatch = rate;
                        break;
                    }
                }

                if (bestMatch) {
                    guestRate = parseFloat(bestMatch.price) || 0;
                    matchedNote = bestMatch.notes;
                } else {
                }
            }

            // --- Apply excess logic
            if (includedGuests > 0 && guests > includedGuests && guestRate > 0) {
                const extraGuests = guests - includedGuests;
                guestExcessCharge = extraGuests * guestRate;

                warnings.push(
                    `For ${currentVenue} (${matchedNote}), additional ₱${guestRate.toLocaleString("en-PH")} per head. Extra guests: ${extraGuests}`
                );
            }

            if (warnings.length > 0 && showGuestWarning) {
                guestWarningMsg.innerHTML = warnings.join("<br>");
                guestWarningMsg.style.display = "block";
            } else {
                guestWarningMsg.style.display = "none";
            }
        }


        // --- FINAL TOTAL (package + room + fees + excesses) ---
        let totalPayment = packagePrice + roomCost + additionalVenueFeeTotal + excessCharge + guestExcessCharge;
        totalPaymentSpan.textContent = totalPayment.toLocaleString("en-PH", {
            minimumFractionDigits: 2,
        });
        document.getElementById('total_amount').value = totalPayment;
    }

    // Function to reset duration when date changes
    function resetDurationOnDateChange() {
        console.log("📅 Date changed - resetting duration");
        hoursInput.value = "";
        minutesInput.value = "";
        venueWarning.style.display = "none";
        updateTotalPayment();
    }

    // When user selects a package
    packageSelect.addEventListener("change", function () {
        const packageId = this.value;

        if (!packageId) {
            totalPaymentSpan.textContent = "0.00";
            venueWarning.style.display = "none";
            guestWarningMsg.style.display = "none";
            return;
        }

        fetch(`fetch_events_prices.php?package_id=${packageId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.package) {
                    basePrice = parseFloat(data.package.price);
                    currentVenue = data.package.venue;
                    currentPackage = data.package;
                    currentPackageName = data.package.name;

                    // Set preset duration
                    const preset = presetDurations[currentVenue] || 0;
                    hoursInput.value = preset;
                    minutesInput.value = "00";

                    excessRates = data.excessRates || {};
                    guestExcessRates = data.guestExcessRates || {};

                    updateTotalPayment();
                } else {
                    totalPaymentSpan.textContent = "0.00";
                    venueWarning.style.display = "none";
                    guestWarningMsg.style.display = "none";
                }
            })
            .catch(err => {
                console.error("Error fetching package:", err);
                totalPaymentSpan.textContent = "0.00";
                venueWarning.style.display = "none";
                guestWarningMsg.style.display = "none";
            });
    });

    // --- Show/hide venue warning on focus ---
    function showWarningOnFocus() {
        showWarning = true;
        updateTotalPayment();
    }
    function hideWarningOnBlur() {
        showWarning = false;
        venueWarning.style.display = "none";
    }

    // --- Show/hide guest warning on focus ---
    function showGuestWarningOnFocus() {
        showGuestWarning = true;
        updateTotalPayment();
    }
    function hideGuestWarningOnBlur() {
        showGuestWarning = false;
        guestWarningMsg.style.display = "none";
    }

    // Fetching Rooms and Durations
    fetch("fetch_rooms.php")
    .then(response => response.json())
    .then(data => {
        // IMPORTANT: assign to outer variable (do NOT use `let` here)
        roomPricesData = data.roomPrices || [];

        // Populate room dropdown
        const dropdownContent = document.getElementById("dropdown-content-rooms");
        dropdownContent.innerHTML = "";

        if (!data.rooms || data.rooms.length === 0) {
            dropdownContent.innerHTML = "<div>No rooms available</div>";
        } else {
            data.rooms.forEach(room => {
                const wrapper = document.createElement("div");
                wrapper.classList.add("dropdown-item");

                // make id safe (no spaces/special chars)
                const safeId = `room-${String(room).replace(/\s+/g, '_').replace(/[^\w\-]/g, '')}`;

                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.name = "room_number[]";
                checkbox.value = room;
                checkbox.id = safeId;

                const label = document.createElement("label");
                label.setAttribute("for", safeId);
                label.textContent = room;

                wrapper.appendChild(checkbox);
                wrapper.appendChild(label);
                dropdownContent.appendChild(wrapper);
            });
        }

        // Populate duration of stay
        const durationSelect = document.getElementById("check_in_time");
        durationSelect.innerHTML = `<option value="" disabled selected>Select duration of stay</option>`;

        if (!data.durations || data.durations.length === 0) {
            const option = document.createElement("option");
            option.value = "";
            option.textContent = "No durations available";
            durationSelect.appendChild(option);
        } else {
            data.durations.forEach(duration => {
                if (duration !== null && String(duration).trim() !== "") {
                    const option = document.createElement("option");
                    option.value = duration;
                    option.textContent = duration;
                    durationSelect.appendChild(option);
                }
            });
        }

        // Hook listeners so totals recalc when user changes duration or rooms
        durationSelect.addEventListener("change", updateTotalPayment);
        dropdownContent.addEventListener("change", () => {
            updateTotalPayment();

            // check if any room is selected
            const anyChecked = dropdownContent.querySelectorAll("input[type='checkbox']:checked").length > 0;

            if (anyChecked) {
                durationSelect.parentElement.style.display = "block"; // show
            } else {
                durationSelect.parentElement.style.display = "none"; // hide
                durationSelect.value = ""; // reset duration when hidden
            }
        });

        // initially hide until a room is selected
        durationSelect.parentElement.style.display = "none";

    })
    .catch(err => console.error("Error fetching rooms:", err));

    // Event bindings
    hoursInput.addEventListener("input", updateTotalPayment);
    minutesInput.addEventListener("input", updateTotalPayment);
    guestsInput.addEventListener("input", updateTotalPayment);

    hoursInput.addEventListener("focus", showWarningOnFocus);
    minutesInput.addEventListener("focus", showWarningOnFocus);
    hoursInput.addEventListener("blur", hideWarningOnBlur);
    minutesInput.addEventListener("blur", hideWarningOnBlur);

    guestsInput.addEventListener("focus", showGuestWarningOnFocus);
    guestsInput.addEventListener("blur", hideGuestWarningOnBlur);

    document.getElementById("check_in_time").addEventListener("change", updateTotalPayment);
    document.getElementById("dropdown-content-rooms").addEventListener("change", updateTotalPayment);

    document.addEventListener("change", function (e) {
        if (e.target.matches("#additional-fee input[type=checkbox]")) {
            updateTotalPayment();
        }
    });

    // Listen for date change to reset duration
    if (dateInput) {
        dateInput.addEventListener("change", resetDurationOnDateChange);
    }

});

            </script>
            
                <script> // NEW CODE!!!!!!!!!!!!!!!!!!!!!
        document.addEventListener("DOMContentLoaded", function () {
            const guestsInput = document.getElementById("guests");
            const guestErrorMsg = document.getElementById("guest-error-msg");
            const packageSelect = document.getElementById("events_package_selection_package");

            // Minimum guests per venue
            const guestMinimum = {
                "Mini Function Hall": 1,
                "Renatos Hall": 1,
                "Renatos Pavilion": 1
            };

            // Helper: get current selected venue
            function getCurrentVenue() {
                const selectedOption = packageSelect.selectedOptions[0];
                return selectedOption?.getAttribute("data-venue") || "";
            }

            // Validate minimum guests on input
            guestsInput.addEventListener("input", function () {
                const venue = getCurrentVenue();
                if (!venue) return;

                const minGuests = guestMinimum[venue] || 1;
                let value = parseInt(this.value) || 0;

                if (value < minGuests) {
                    guestErrorMsg.textContent = `Minimum of ${minGuests} guest${minGuests > 1 ? 's' : ''} required for ${venue}.`;
                    guestErrorMsg.style.display = "block";
                    this.style.borderColor = "red";
                } else {
                    guestErrorMsg.style.display = "none";
                    guestErrorMsg.textContent = "";
                    this.style.borderColor = "";
                }
            });

            // Auto-correct to minimum on blur
            guestsInput.addEventListener("blur", function () {
                const venue = getCurrentVenue();
                if (!venue) return;

                const minGuests = guestMinimum[venue] || 1;
                let value = parseInt(this.value) || 0;

                if (value < minGuests) this.value = minGuests;

                guestErrorMsg.style.display = "none";
                this.style.borderColor = "";
            });

            // Reset guest input when package changes
            packageSelect.addEventListener("change", function () {
                guestsInput.value = "";
                guestErrorMsg.style.display = "none";
                guestErrorMsg.textContent = "";
                guestsInput.style.borderColor = "";
            });
        });
        </script>

        <script>
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
// Update hidden fields when hours/minutes change
document.getElementById("hours").addEventListener("input", function() {
    document.getElementById("hidden_duration_hours").value = this.value || 0;
});

document.getElementById("minutes").addEventListener("input", function() {
    document.getElementById("hidden_duration_minutes").value = this.value || 0;
});

// Update hidden field when package is selected
document.getElementById("events_package_selection_package").addEventListener("change", function() {
    document.getElementById("hidden_events_package").value = this.value;
});

// Update hidden field when rooms are selected
document.getElementById("dropdown-content-rooms").addEventListener("change", function() {
    const checkedBoxes = Array.from(document.querySelectorAll("#dropdown-content-rooms input[type=checkbox]:checked"));
    const selectedRooms = checkedBoxes.map(cb => cb.value).join(',');
    document.getElementById("hidden_selected_rooms").value = selectedRooms;
});

// Update hidden field when additional fees are selected
document.getElementById("additional-fee").addEventListener("change", function() {
    const checkedFees = Array.from(document.querySelectorAll("#additional-fee input[type=checkbox]:checked"));
    const selectedFees = checkedFees.map(cb => cb.value).join(',');
    document.getElementById("hidden_additional_fee").value = selectedFees;
});

// Initialize duration values on page load
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("hidden_duration_hours").value = document.getElementById("hours").value || 0;
    document.getElementById("hidden_duration_minutes").value = document.getElementById("minutes").value || 0;
    
    // Set name attribute for additional fee checkboxes to be array
    const additionalFeeCheckboxes = document.querySelectorAll("#additional-fee input[type=checkbox]");
    additionalFeeCheckboxes.forEach(cb => {
        cb.name = "additional_fee[]";
    });
});
</script>

<script>
// ============================================
// IMMEDIATE PAGE LOAD DEBUG
// ============================================
document.addEventListener("DOMContentLoaded", function() {
    console.log("=== PAGE LOADED - CHECKING ELEMENTS ===");
    
    // Check if form exists
    const form = document.querySelector('.reservation-form');
    console.log("Form found:", form ? "YES" : "NO");
    
    // Check affiliate dropdowns
    const catering = document.getElementById("catering");
    const lights = document.getElementById("lights");
    const mobileBar = document.getElementById("mobile-bar");
    const grazingTable = document.getElementById("grazing-table");
    
    console.log("\n--- Affiliate Dropdowns ---");
    console.log("Catering element:", catering ? "EXISTS" : "MISSING");
    console.log("Lights element:", lights ? "EXISTS" : "MISSING");
    console.log("Mobile Bar element:", mobileBar ? "EXISTS" : "MISSING");
    console.log("Grazing Table element:", grazingTable ? "EXISTS" : "MISSING");
    
    // Check additional fee container
    const additionalFeeContainer = document.getElementById("additional-fee");
    console.log("\n--- Additional Fees ---");
    console.log("Container found:", additionalFeeContainer ? "YES" : "NO");
    
    if (additionalFeeContainer) {
        const checkboxes = additionalFeeContainer.querySelectorAll("input[type=checkbox]");
        console.log("Checkboxes found immediately:", checkboxes.length);
        
        if (checkboxes.length > 0) {
            checkboxes.forEach((cb, i) => {
                console.log(`  Checkbox ${i + 1}: name="${cb.name}", value="${cb.value}"`);
            });
        } else {
            console.log("  No checkboxes yet - will monitor for dynamic loading");
        }
        
        // Monitor for when checkboxes are added
        const observer = new MutationObserver(function(mutations) {
            const newCheckboxes = additionalFeeContainer.querySelectorAll("input[type=checkbox]");
            if (newCheckboxes.length > 0) {
                console.log("\n✅ Additional fee checkboxes loaded:", newCheckboxes.length);
                newCheckboxes.forEach((cb, i) => {
                    console.log(`  Checkbox ${i + 1}: name="${cb.name}", value="${cb.value}"`);
                });
            }
        });
        
        observer.observe(additionalFeeContainer, {
            childList: true,
            subtree: true
        });
    }
    
    console.log("\n=== END PAGE LOAD DEBUG ===\n");
});

// ============================================
// FORM SUBMISSION DEBUG
// ============================================
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector('.reservation-form');
    if (!form) {
        console.error("⚠️ Form not found!");
        return;
    }
    
    form.addEventListener('submit', function(e) {
        console.log("\n\n=== FORM SUBMISSION DEBUG ===");
        
        // Check affiliate dropdowns
        const catering = document.getElementById("catering");
        const lights = document.getElementById("lights");
        const mobileBar = document.getElementById("mobile-bar");
        const grazingTable = document.getElementById("grazing-table");
        
        console.log("\n--- Affiliate Services at Submit ---");
        console.log("Catering:", {
            exists: !!catering,
            value: catering?.value || "EMPTY",
            name: catering?.name || "NO NAME",
            disabled: catering?.disabled
        });
        
        console.log("Lights:", {
            exists: !!lights,
            value: lights?.value || "EMPTY",
            name: lights?.name || "NO NAME",
            disabled: lights?.disabled
        });
        
        console.log("Mobile Bar:", {
            exists: !!mobileBar,
            value: mobileBar?.value || "EMPTY",
            name: mobileBar?.name || "NO NAME",
            disabled: mobileBar?.disabled
        });
        
        console.log("Grazing Table:", {
            exists: !!grazingTable,
            value: grazingTable?.value || "EMPTY",
            name: grazingTable?.name || "NO NAME",
            disabled: grazingTable?.disabled
        });
        
        // Check additional fees
        const additionalFees = document.querySelectorAll("#additional-fee input[type=checkbox]");
        console.log("\n--- Additional Fees at Submit ---");
        console.log("Total checkboxes found:", additionalFees.length);
        
        const checkedFees = [];
        additionalFees.forEach((cb, index) => {
            const info = {
                index: index + 1,
                name: cb.name || "NO NAME",
                value: cb.value,
                checked: cb.checked,
                dataPrice: cb.getAttribute('data-price')
            };
            console.log(`Checkbox ${index + 1}:`, info);
            if (cb.checked) {
                checkedFees.push(cb.value);
            }
        });
        
        console.log("Checked fees:", checkedFees.length > 0 ? checkedFees.join(", ") : "NONE");
        
        // Get all form data
        const formData = new FormData(this);
        console.log("\n--- ALL FORM DATA BEING SUBMITTED ---");
        let formDataObj = {};
        for (let [key, value] of formData.entries()) {
            if (!formDataObj[key]) {
                formDataObj[key] = value;
            } else {
                if (Array.isArray(formDataObj[key])) {
                    formDataObj[key].push(value);
                } else {
                    formDataObj[key] = [formDataObj[key], value];
                }
            }
        }
        console.table(formDataObj);
        
        console.log("\n=== END FORM SUBMISSION DEBUG ===\n");
    });
});
</script>







































<script>
// ============================================
// EVENT PACKAGE RESORT CONFLICT BLOCKING (FIXED)
// Blocks event packages with resort inclusions when resort OR event package is booked
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const checkinDateInput = document.getElementById("checkin");
    const packageSelect = document.getElementById("events_package_selection_package");
    
    // Event package IDs that include resort (resort_package = 57)
    const RESORT_PACKAGE_IDS = [22, 25, 26, 27, 28, 29];
    for (let i = 32; i <= 43; i++) {
        RESORT_PACKAGE_IDS.push(i);
    }
    
    let resortBookings = [];
    let eventPackages = [];
    let currentDate = null; // Track current date being checked
    
    console.log("🔧 Resort Conflict Script Initialized");
    console.log("📋 Monitoring Package IDs:", RESORT_PACKAGE_IDS);

    // Parse time string (HH:MM) to minutes since midnight
    function timeToMinutes(timeStr) {
        if (!timeStr) return 0;
        const [hours, minutes] = timeStr.split(':').map(Number);
        return hours * 60 + minutes;
    }

    // Extract time range from duration text (e.g., "7:00pm - 5:00pm")
    function extractTimeFromDuration(durationText) {
        if (!durationText) return null;
        
        // Match patterns like "7:00pm - 5:00pm" or "7pm - 5pm"
        const timeMatch = durationText.match(/(\d{1,2}):?(\d{2})?\s*(am|pm)\s*-\s*(\d{1,2}):?(\d{2})?\s*(am|pm)/i);
        
        if (timeMatch) {
            let startHour = parseInt(timeMatch[1]);
            const startMin = parseInt(timeMatch[2] || '0');
            const startPeriod = timeMatch[3].toLowerCase();
            
            let endHour = parseInt(timeMatch[4]);
            const endMin = parseInt(timeMatch[5] || '0');
            const endPeriod = timeMatch[6].toLowerCase();
            
            // Convert to 24-hour format
            if (startPeriod === 'pm' && startHour !== 12) startHour += 12;
            if (startPeriod === 'am' && startHour === 12) startHour = 0;
            
            if (endPeriod === 'pm' && endHour !== 12) endHour += 12;
            if (endPeriod === 'am' && endHour === 12) endHour = 0;
            
            let startMinutes = startHour * 60 + startMin;
            let endMinutes = endHour * 60 + endMin;
            
            // Handle overnight bookings (end time is next day)
            const isOvernight = endMinutes <= startMinutes;
            if (isOvernight) {
                endMinutes += 1440; // Add 24 hours in minutes
            }
            
            return { 
                startMinutes: startMinutes, 
                endMinutes: endMinutes,
                isOvernight: isOvernight,
                displayStart: `${String(startHour).padStart(2, '0')}:${String(startMin).padStart(2, '0')}`,
                displayEnd: `${String(endHour).padStart(2, '0')}:${String(endMin).padStart(2, '0')}`
            };
        }
        
        return null;
    }

    // Check if two time ranges overlap
    function timeRangesOverlap(start1, end1, start2, end2) {
        return start1 < end2 && start2 < end1;
    }

    // Get next day's date string
    function getNextDay(dateString) {
        const date = new Date(dateString);
        date.setDate(date.getDate() + 1);
        return date.toISOString().split('T')[0];
    }

    // Fetch resort bookings for selected date and next day
    function fetchResortBookings(selectedDate) {
        const nextDate = getNextDay(selectedDate);
        
        console.log("📅 Fetching resort bookings for:", selectedDate, "and", nextDate);
        
        return Promise.all([
            fetch(`fetch_booked_resort.php?date=${encodeURIComponent(selectedDate)}`).then(res => res.json()),
            fetch(`fetch_booked_resort.php?date=${encodeURIComponent(nextDate)}`).then(res => res.json())
        ])
        .then(([todayData, tomorrowData]) => {
            // CRITICAL FIX: Clear previous bookings array
            const newResortBookings = [];
            
            // Process TODAY's bookings (Resort AND Event Package types)
            if (todayData.confirmedReservations && todayData.confirmedReservations.length > 0) {
                todayData.confirmedReservations.forEach(booking => {
                    // Only process bookings for the SELECTED DATE
                    if (booking.checkin_date !== selectedDate) return;
                    
                    // ✅ NEW: Check BOTH Resort and Event Package reservation types
                    if ((booking.reservation_type === 'Resort' || booking.reservation_type === 'Event Package') 
                        && booking.status === 'confirmed') {
                        
                        let timeInfo = null;
                        let durationText = '';
                        
                        // Check if it has resort_package (could be Resort type OR Event Package with resort inclusion)
                        if (booking.resort_package && booking.resort_package !== '0') {
                            durationText = booking.duration || '';
                            timeInfo = extractTimeFromDuration(durationText);
                            
                            if (timeInfo) {
                                timeInfo.type = booking.reservation_type === 'Event Package' 
                                    ? 'event_package_with_resort' 
                                    : 'resort_package';
                                timeInfo.bookingDate = selectedDate;
                                timeInfo.bookingId = booking.id;
                                timeInfo.reservationType = booking.reservation_type;
                                newResortBookings.push(timeInfo);
                                
                                console.log(`🏖️ ${booking.reservation_type.toUpperCase()} with RESORT (today, ID: ${booking.id}): ${durationText}`, timeInfo);
                            }
                        } 
                        // Check if it's a resort duration (has duration text but no package)
                        else if (booking.duration && booking.reservation_type === 'Resort') {
                            durationText = booking.duration;
                            timeInfo = extractTimeFromDuration(durationText);
                            
                            if (timeInfo) {
                                timeInfo.type = 'resort_duration';
                                timeInfo.bookingDate = selectedDate;
                                timeInfo.bookingId = booking.id;
                                timeInfo.reservationType = booking.reservation_type;
                                newResortBookings.push(timeInfo);
                                
                                console.log(`🏖️ Resort DURATION (today, ID: ${booking.id}): ${durationText}`, timeInfo);
                            }
                        }
                    }
                });
            }
            
            // Process TOMORROW's bookings (for overnight conflicts)
            if (tomorrowData.confirmedReservations && tomorrowData.confirmedReservations.length > 0) {
                tomorrowData.confirmedReservations.forEach(booking => {
                    // Only process bookings for the NEXT DATE
                    if (booking.checkin_date !== nextDate) return;
                    
                    // ✅ NEW: Check BOTH Resort and Event Package reservation types
                    if ((booking.reservation_type === 'Resort' || booking.reservation_type === 'Event Package') 
                        && booking.status === 'confirmed') {
                        
                        let timeInfo = null;
                        let durationText = '';
                        
                        if (booking.resort_package && booking.resort_package !== '0') {
                            durationText = booking.duration || '';
                            timeInfo = extractTimeFromDuration(durationText);
                            
                            if (timeInfo) {
                                // Shift time to tomorrow's timeline (add 24 hours)
                                timeInfo.startMinutes += 1440;
                                timeInfo.endMinutes += 1440;
                                timeInfo.type = booking.reservation_type === 'Event Package' 
                                    ? 'event_package_with_resort' 
                                    : 'resort_package';
                                timeInfo.bookingDate = nextDate;
                                timeInfo.bookingId = booking.id;
                                timeInfo.reservationType = booking.reservation_type;
                                newResortBookings.push(timeInfo);
                                
                                console.log(`🏖️ ${booking.reservation_type.toUpperCase()} with RESORT (tomorrow, ID: ${booking.id}): ${durationText}`, timeInfo);
                            }
                        } 
                        else if (booking.duration && booking.reservation_type === 'Resort') {
                            durationText = booking.duration;
                            timeInfo = extractTimeFromDuration(durationText);
                            
                            if (timeInfo) {
                                timeInfo.startMinutes += 1440;
                                timeInfo.endMinutes += 1440;
                                timeInfo.type = 'resort_duration';
                                timeInfo.bookingDate = nextDate;
                                timeInfo.bookingId = booking.id;
                                timeInfo.reservationType = booking.reservation_type;
                                newResortBookings.push(timeInfo);
                                
                                console.log(`🏖️ Resort DURATION (tomorrow, ID: ${booking.id}): ${durationText}`, timeInfo);
                            }
                        }
                    }
                });
            }
            
            console.log("📊 Total resort/event bookings loaded:", newResortBookings.length);
            
            // Return the new array instead of modifying global
            return newResortBookings;
        })
        .catch(err => {
            console.error("❌ Error fetching resort bookings:", err);
            return [];
        });
    }

    // Fetch event packages data
    function fetchEventPackages(selectedDate) {
        console.log("📦 Fetching event packages for:", selectedDate);
        
        return fetch(`fetch_events_packages.php?date=${encodeURIComponent(selectedDate)}`)
            .then(res => res.json())
            .then(data => {
                eventPackages = data.packages || [];
                console.log("✅ Event packages loaded:", eventPackages.length);
                return eventPackages;
            })
            .catch(err => {
                console.error("❌ Error fetching event packages:", err);
                return [];
            });
    }

    // Get venue-specific start time (venues are exclusive for the day)
    function getVenueStartTime(venue) {
        const venueDefaults = {
            "Renatos Hall": "09:00",
            "Renatos Pavilion": "09:00",
            "Mini Function Hall": "09:00"
        };
        
        return venueDefaults[venue] || null;
    }

    // Block conflicting event packages with resort inclusions
    function blockConflictingResortPackages() {
        const packageOptions = packageSelect.querySelectorAll("option");
        
        console.log("\n🔍 === CHECKING RESORT CONFLICTS ===");
        console.log("Current date:", currentDate);
        console.log("Resort/Event bookings to check against:", resortBookings.length);
        
        // CRITICAL FIX: If no resort bookings, unblock everything
        if (resortBookings.length === 0) {
            console.log("✅ No resort/event bookings found - unblocking all packages");
            packageOptions.forEach(option => {
                if (option.value === "") return;
                
                const packageId = parseInt(option.value);
                if (!RESORT_PACKAGE_IDS.includes(packageId)) return;
                
                // Remove all blocking
                option.disabled = false;
                option.style.color = '';
                option.style.textDecoration = '';
                option.style.backgroundColor = '';
                
                const cleanText = option.textContent.replace(/\s*\(Unavailable[^)]*\)/g, '');
                if (option.textContent !== cleanText) {
                    option.textContent = cleanText;
                }
            });
            console.log("=== RESORT CONFLICT CHECK COMPLETE (No Bookings) ===\n");
            return;
        }
        
        packageOptions.forEach(option => {
            if (option.value === "") return; // Skip default option
            
            const packageId = parseInt(option.value);
            
            // Only check packages with resort inclusions
            if (!RESORT_PACKAGE_IDS.includes(packageId)) return;
            
            const packageName = option.getAttribute('data-name') || option.textContent;
            const venue = option.getAttribute('data-venue');
            
            console.log(`\n🎫 Checking package: ${packageName} (ID: ${packageId})`);
            console.log(`   Venue: "${venue}"`);
            
            // Get start time based on venue
            const startTime = getVenueStartTime(venue);
            
            if (!startTime) {
                console.warn(`⚠️ Unknown venue for package ${packageId}: "${venue}"`);
                return;
            }
            
            // Event packages with resort start at 9:00 AM and run for entire day
            const startMinutes = timeToMinutes(startTime);
            const endMinutes = 1440; // End of day
            
            const packageTimeInfo = {
                startMinutes: startMinutes,
                endMinutes: endMinutes,
                displayStart: startTime,
                displayEnd: "24:00 (End of Day)",
                isOvernight: false
            };
            
            console.log(`   Package time range:`, {
                display: `${packageTimeInfo.displayStart} - ${packageTimeInfo.displayEnd}`,
                minutes: `${packageTimeInfo.startMinutes} - ${packageTimeInfo.endMinutes}`
            });
            
            let hasConflict = false;
            let conflictDetails = null;
            
            // Check against all resort/event bookings
            for (let resortBooking of resortBookings) {
                console.log(`   📊 Comparing with booking (${resortBooking.bookingDate}, ID: ${resortBooking.bookingId}):`, {
                    type: resortBooking.type,
                    reservationType: resortBooking.reservationType,
                    minutes: `${resortBooking.startMinutes} - ${resortBooking.endMinutes}`
                });
                
                if (timeRangesOverlap(
                    packageTimeInfo.startMinutes,
                    packageTimeInfo.endMinutes,
                    resortBooking.startMinutes,
                    resortBooking.endMinutes
                )) {
                    hasConflict = true;
                    conflictDetails = resortBooking;
                    console.log(`   🚫 ❌ CONFLICT DETECTED!`);
                    break;
                } else {
                    console.log(`   ✅ No overlap - times don't conflict`);
                }
            }
            
            // Apply blocking if conflict found
            if (hasConflict) {
                option.disabled = true;
                option.style.color = '#999';
                option.style.textDecoration = 'line-through';
                option.style.backgroundColor = '#f8d7da';
                
                // Determine conflict type for display
                let conflictType = '';
                if (conflictDetails.reservationType === 'Event Package') {
                    conflictType = '';
                } else if (conflictDetails.type === 'resort_package') {
                    conflictType = '';
                } else if (conflictDetails.type === 'resort_duration') {
                    conflictType = '';
                }
                
                const currentText = option.textContent;
                
                if (!currentText.includes('(Unavailable')) {
                    option.textContent = currentText + ` (Unavailable)`;
                }
                
                if (option.selected) {
                    packageSelect.value = "";
                }
                
                console.log(`🚫 BLOCKED: ${packageName} - Conflicts with ${conflictType}`);
            } else {
                // Remove blocking if no conflict
                option.disabled = false;
                option.style.color = '';
                option.style.textDecoration = '';
                option.style.backgroundColor = '';
                
                const cleanText = option.textContent.replace(/\s*\(Unavailable[^)]*\)/g, '');
                if (option.textContent !== cleanText) {
                    option.textContent = cleanText;
                }
                
                console.log(`✅ AVAILABLE: ${packageName}`);
            }
        });
        
        console.log("=== RESORT CONFLICT CHECK COMPLETE ===\n");
    }

    // Main handler when date changes
    checkinDateInput.addEventListener("change", function() {
        const selectedDate = this.value;
        if (!selectedDate) return;
        
        // CRITICAL FIX: Update current date and clear old bookings
        currentDate = selectedDate;
        resortBookings = []; // Clear old bookings immediately
        
        console.log("\n\n📅 === DATE CHANGED: " + selectedDate + " ===");
        
        // Fetch both resort bookings and event packages
        Promise.all([
            fetchResortBookings(selectedDate),
            fetchEventPackages(selectedDate)
        ])
        .then(([newBookings]) => {
            // CRITICAL FIX: Update global resortBookings with new data
            resortBookings = newBookings;
            
            console.log("🔄 Updated resortBookings array:", resortBookings.length, "bookings");
            
            // Wait for packages to be populated in the select dropdown
            setTimeout(() => {
                blockConflictingResortPackages();
            }, 500);
        })
        .catch(err => {
            console.error("❌ Error in main handler:", err);
            resortBookings = []; // Clear on error too
            blockConflictingResortPackages(); // Unblock everything
        });
    });

    // Also check when package dropdown is populated/changed
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.target === packageSelect) {
                if (currentDate && resortBookings !== undefined) {
                    console.log("🔄 Package dropdown updated, rechecking conflicts...");
                    blockConflictingResortPackages();
                }
            }
        });
    });
    
    observer.observe(packageSelect, {
        childList: true,
        subtree: false
    });
    
    console.log("✅ Resort conflict monitoring active");
});
</script>









<script>

    // ============================================
// EVENT PACKAGE VENUE-SPECIFIC BLOCKING
// Blocks event packages by venue - ONE booking = ENTIRE DAY blocked for that venue
// ONLY handles: Renatos Hall and Renatos Pavilion
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const checkinDateInput = document.getElementById("checkin");
    const packageSelect = document.getElementById("events_package_selection_package");
    
    let bookedVenues = new Set(); // Simple set of booked venues for the day
    let currentDate = null;
    let packagesData = {}; // Store packages with their venues {id: {name, venue}}
    
    console.log("🔧 Event Venue Blocking Script Initialized");
    console.log("📋 Venues: Renatos Hall, Renatos Pavilion");

    // Fetch available packages to get venue mapping
    function fetchAvailablePackages(selectedDate) {
        console.log("📦 Fetching available packages from fetch_events_packages.php...");
        
        return fetch(`fetch_events_packages.php?date=${encodeURIComponent(selectedDate)}`)
            .then(res => res.json())
            .then(data => {
                packagesData = {};
                
                if (data.packages && data.packages.length > 0) {
                    data.packages.forEach(pkg => {
                        packagesData[pkg.id] = {
                            name: pkg.name,
                            venue: pkg.venue
                        };
                        console.log(`   Package ID ${pkg.id}: ${pkg.name} (Venue: ${pkg.venue})`);
                    });
                    console.log(`✅ Loaded ${data.packages.length} packages`);
                } else {
                    console.log("⚠️ No packages found");
                }
                
                return packagesData;
            })
            .catch(err => {
                console.error("❌ Error fetching packages:", err);
                return {};
            });
    }

    // Fetch event bookings for selected date
    function fetchEventBookings(selectedDate) {
        console.log("\n📅 Fetching event bookings from fetch_booked_events.php...");
        
        return fetch(`fetch_booked_events.php?date=${encodeURIComponent(selectedDate)}`)
            .then(res => res.json())
            .then(data => {
                // Reset booked venues
                bookedVenues.clear();
                
                console.log("📊 Processing event bookings...");
                console.log("Confirmed Reservations:", data.confirmedReservations);
                
                if (data.confirmedReservations && data.confirmedReservations.length > 0) {
                    console.log(`Found ${data.confirmedReservations.length} confirmed reservations`);
                    
                    data.confirmedReservations.forEach(booking => {
                        console.log(`\nChecking booking ID ${booking.id}:`);
                        console.log(`  - Date: ${booking.checkin_date}`);
                        console.log(`  - Type: ${booking.reservation_type}`);
                        console.log(`  - Status: ${booking.status}`);
                        console.log(`  - Events Package ID: ${booking.events_package}`);
                        
                        // Only process bookings for the SELECTED DATE
                        if (booking.checkin_date !== selectedDate) {
                            console.log(`⏩ Skipping - different date (${booking.checkin_date} !== ${selectedDate})`);
                            return;
                        }
                        
                        if (booking.reservation_type === 'Event Package' && booking.status === 'confirmed') {
                            // Get the events_package ID
                            const packageId = parseInt(booking.events_package);
                            
                            if (packageId && packagesData[packageId]) {
                                const pkg = packagesData[packageId];
                                const venue = pkg.venue;
                                
                                console.log(`   Found package: ${pkg.name}`);
                                console.log(`   Venue: ${venue}`);
                                
                                // Only block Renatos Hall and Renatos Pavilion
                                if (venue === 'Renatos Hall' || venue === 'Renatos Pavilion') {
                                    bookedVenues.add(venue);
                                    console.log(`🚫 VENUE BOOKED: ${venue}`);
                                    console.log(`   Booking ID: ${booking.id}`);
                                    console.log(`   Package: ${pkg.name}`);
                                } else {
                                    console.log(`ℹ️ Booking found but not Hall/Pavilion: ${venue}`);
                                }
                            } else {
                                console.log(`⚠️ Package ID ${packageId} not found in available packages`);
                            }
                        }
                    });
                }
                
                console.log("\n📋 Final Booked Venues for", selectedDate, ":", Array.from(bookedVenues));
                return Array.from(bookedVenues);
            })
            .catch(err => {
                console.error("❌ Error fetching event bookings:", err);
                return [];
            });
    }


    // Get venue from package option in dropdown
    function getVenueFromOption(option) {
        // Check data-venue attribute first (most reliable)
        const dataVenue = option.getAttribute('data-venue');
        if (dataVenue) {
            return dataVenue;
        }
        
        // Fall back to parsing the package text (format: "Venue-Name- ₱Price")
        const packageText = option.textContent.replace(/\s*\(Unavailable[^)]*\)/g, '').trim();
        const parts = packageText.split('-');
        
        if (parts.length > 0) {
            return parts[0].trim();
        }
        
        return null;
    }

    // Block packages based on booked venues
    function blockPackagesByVenue() {
        const packageOptions = packageSelect.querySelectorAll("option");
        
        console.log("\n🔍 === CHECKING VENUE CONFLICTS ===");
        console.log("Current date:", currentDate);
        console.log("Booked venues:", Array.from(bookedVenues));
        
        // Check each package option
        packageOptions.forEach(option => {
            if (option.value === "") return; // Skip default option
            
            const packageId = parseInt(option.value);
            const packageName = option.textContent.replace(/\s*\(Unavailable[^)]*\)/g, '').trim();
            
            console.log(`\n🎫 Checking package: ${packageName} (ID: ${packageId})`);
            
            const venue = getVenueFromOption(option);
            
            if (!venue) {
                console.log(`   ⚠️ Could not determine venue`);
                return;
            }
            
            // Only check Renatos Hall and Renatos Pavilion
            if (venue !== 'Renatos Hall' && venue !== 'Renatos Pavilion') {
                console.log(`   ℹ️ Not a Hall/Pavilion package (${venue}) - skipping`);
                return;
            }
            
            console.log(`   Detected venue: "${venue}"`);
            console.log(`   Is venue booked? ${bookedVenues.has(venue)}`);
            
            // Check if this package's venue is booked
            const isVenueBooked = bookedVenues.has(venue);
            
            if (isVenueBooked) {
                // Block this package - venue is fully booked for the day
                option.disabled = true;
                option.style.color = '#999';
                option.style.textDecoration = 'line-through';
                option.style.backgroundColor = '#f8d7da';
                
                const currentText = option.textContent;
                if (!currentText.includes('(Unavailable')) {
                    option.textContent = currentText + ` (Unavailable)`;
                }
                
                // Clear selection if this option was selected
                if (option.selected) {
                    packageSelect.value = "";
                }
                
                console.log(`🚫 BLOCKED: ${packageName} - ${venue} is fully booked for the day`);
            } else {
                // Venue is available - DO NOT unblock, let other blocking scripts handle it
                console.log(`✅ AVAILABLE: ${packageName} - ${venue} is available (no blocking from this script)`);
            }
        });
        
        console.log("=== VENUE CONFLICT CHECK COMPLETE ===\n");
    }

    // Main handler when date changes
    checkinDateInput.addEventListener("change", function() {
        const selectedDate = this.value;
        if (!selectedDate) return;
        
        currentDate = selectedDate;
        bookedVenues.clear();
        packagesData = {};
        
        console.log("\n\n📅 === DATE CHANGED: " + selectedDate + " ===");
        
        // First fetch available packages, then fetch bookings
        fetchAvailablePackages(selectedDate)
            .then(() => fetchEventBookings(selectedDate))
            .then(() => {
                // Wait for packages to be populated in the select dropdown
                // Multiple checks to ensure dropdown is fully populated
                setTimeout(() => blockPackagesByVenue(), 500);
                setTimeout(() => blockPackagesByVenue(), 1000);
                setTimeout(() => blockPackagesByVenue(), 1500);
            })
            .catch(err => {
                console.error("❌ Error in main handler:", err);
                bookedVenues.clear();
            });
    });

    // Also check when package dropdown is populated/changed
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.target === packageSelect) {
                if (currentDate && bookedVenues.size > 0) {
                    console.log("🔄 Package dropdown updated, rechecking venue conflicts...");
                    blockPackagesByVenue();
                }
            }
        });
    });
    
    observer.observe(packageSelect, {
        childList: true,
        subtree: false
    });
    
    console.log("✅ Event venue blocking monitoring active (Hall & Pavilion only)");
});
</script>



<script>
    // ===== SIMPLE EVENT ROOM BLOCKING - BLOCKS BOOKED ROOMS ON DATE SELECTION ===== 
(function() {
    let bookedRoomsData = [];
    let restDaysData = [];

    // Fetch rest days
    function loadRestDays() {
        return fetch('admin/get_rest_days.php')
            .then(res => res.json())
            .then(data => {
                restDaysData = data || [];
                console.log('[EVENT ROOMS] ✓ Loaded rest days:', restDaysData);
                return restDaysData;
            })
            .catch(err => {
                console.error('[EVENT ROOMS] Error fetching rest days:', err);
                return [];
            });
    }

    // Fetch booked rooms
    function loadBookedRooms() {
        return fetch('fetch_booked_rooms.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    bookedRoomsData = data.all_bookings;
                    console.log('[EVENT ROOMS] ✓ Loaded bookings:', bookedRoomsData.length);
                    return data.all_bookings;
                }
                return [];
            })
            .catch(err => {
                console.error('[EVENT ROOMS] Error fetching rooms:', err);
                return [];
            });
    }

    // Main function to check room availability based on date only
    function checkRoomsByDate() {
        console.log('[EVENT ROOMS] 🔍 Checking room availability by date...');

        const checkinDate = document.getElementById('checkin')?.value;
        const roomCheckboxes = document.querySelectorAll('#dropdown-content-rooms input[type="checkbox"]');
        const warningMsg = document.getElementById('duration-room-warning-msg');

        // If no date selected, don't block anything
        if (!checkinDate) {
            console.log('[EVENT ROOMS] ⚠️ No date selected - no blocking');
            
            roomCheckboxes.forEach(cb => {
                cb.removeAttribute('data-event-blocked');
                cb.classList.remove('room-unavailable');
                if (!cb.hasAttribute('data-guest-disabled')) {
                    cb.disabled = false;
                }
                const label = cb.nextElementSibling;
                if (label) {
                    label.classList.remove('room-unavailable-label');
                    label.title = '';
                }
            });
            
            if (warningMsg) warningMsg.style.display = 'none';
            return;
        }

        console.log('[EVENT ROOMS] 📅 Selected date:', checkinDate);

        const unavailableRooms = new Set();

        // Check if it's a rest day - block ALL rooms
        if (restDaysData.includes(checkinDate)) {
            console.log('[EVENT ROOMS] 🚫 REST DAY - blocking all rooms');
            roomCheckboxes.forEach(cb => unavailableRooms.add(cb.value));
        } else {
            // Check bookings on this date
            bookedRoomsData.forEach(booking => {
                // Only check bookings on the selected date
                if (booking.checkin_date !== checkinDate) return;

                // Get all rooms from this booking
                const rooms = [...(booking.rooms_array || []), ...(booking.resort_rooms_array || [])];
                
                if (rooms.length > 0) {
                    console.log('[EVENT ROOMS] 📌 Booking ID', booking.id, '- Blocking rooms:', rooms);
                    rooms.forEach(room => unavailableRooms.add(room));
                }
            });
        }

        console.log('[EVENT ROOMS] 🚫 Total unavailable rooms:', Array.from(unavailableRooms));

        // Apply blocking
        roomCheckboxes.forEach(cb => {
            if (unavailableRooms.has(cb.value)) {
                cb.setAttribute('data-event-blocked', 'true');
                cb.disabled = true;
                cb.checked = false;
                cb.classList.add('room-unavailable');
                
                const label = cb.nextElementSibling;
                if (label) {
                    label.classList.add('room-unavailable-label');
                    if (restDaysData.includes(checkinDate)) {
                        label.title = 'Rest day - No bookings allowed';
                    } else {
                        label.title = 'Room already booked on this date';
                    }
                }
                
                console.log('[EVENT ROOMS] 🚫 Blocked:', cb.value);
            } else {
                cb.removeAttribute('data-event-blocked');
                cb.classList.remove('room-unavailable');
                if (!cb.hasAttribute('data-guest-disabled')) {
                    cb.disabled = false;
                }
                const label = cb.nextElementSibling;
                if (label) {
                    label.classList.remove('room-unavailable-label');
                    label.title = '';
                }
            }
        });

        // Show warning message
        if (warningMsg) {
            if (unavailableRooms.size > 0) {
                if (restDaysData.includes(checkinDate)) {
                    warningMsg.textContent = '🚫 Rest day - No room bookings allowed';
                } else {
                    warningMsg.textContent = `⚠️ ${unavailableRooms.size} room(s) unavailable on this date`;
                }
                warningMsg.style.display = 'block';
                warningMsg.style.color = '#721c24';
                warningMsg.style.backgroundColor = '#fff3cd';
                warningMsg.style.padding = '8px';
                warningMsg.style.borderRadius = '4px';
                warningMsg.style.border = '1px solid #ffc107';
            } else {
                warningMsg.style.display = 'none';
            }
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        console.log('[EVENT ROOMS] 🔄 Initializing simple date-based room blocking...');

        // Load data first
        loadRestDays().then(() => loadBookedRooms()).then(() => {
            console.log('[EVENT ROOMS] ✅ Ready - rooms will be blocked when date is selected');

            // Only listen to date changes
            const checkinInput = document.getElementById('checkin');
            if (checkinInput) {
                checkinInput.addEventListener('change', function() {
                    console.log('[EVENT ROOMS] 📅 Date changed:', this.value);
                    
                    // Wait a bit for rooms to be populated
                    setTimeout(checkRoomsByDate, 300);
                });
            }

            // Also check when rooms are dynamically loaded
            const roomContainer = document.getElementById('dropdown-content-rooms');
            if (roomContainer) {
                const observer = new MutationObserver(function() {
                    if (document.getElementById('checkin')?.value) {
                        console.log('[EVENT ROOMS] 🔄 Rooms loaded, rechecking...');
                        checkRoomsByDate();
                    }
                });
                
                observer.observe(roomContainer, { childList: true, subtree: true });
            }
        });
    });
})();
</script>



<script>
// ============================================
// CHECK-IN TIME RESTRICTIONS BY VENUE (MOBILE-FIXED)
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const packageSelect = document.getElementById("events_package_selection_package");
    const timeInput = document.getElementById("time");
    const checkinDateInput = document.getElementById("checkin");
    
    let currentVenue = "";
    let timePicker = null;

    // Track when package is selected to get venue
    packageSelect.addEventListener("change", function() {
        const selectedOption = this.selectedOptions[0];
        if (!selectedOption) {
            // Remove helper text when no package is selected
            removeHelperText();
            // Reset time input
            timeInput.value = "";
            unlockTimeInput();
            return;
        }
        
        currentVenue = selectedOption.getAttribute('data-venue') || "";
        console.log("Selected venue:", currentVenue);
        
        // Remove helper text first
        removeHelperText();
        
        // Apply time restrictions based on venue
        applyTimeRestrictions();
    });

    // Function to remove helper text
    function removeHelperText() {
        const formGroup = timeInput ? timeInput.closest('.form-group') : null;
        if (formGroup) {
            const helperText = formGroup.querySelector('.venue-time-restriction');
            if (helperText) {
                helperText.remove();
                console.log("Renatos helper text removed");
            }
        }
    }

    // Function to completely lock the time input for mobile
    function lockTimeInput() {
        // Set data attribute for CSS styling
        timeInput.setAttribute('data-renatos-locked', 'true');
        
        // Prevent all interaction methods
        timeInput.readOnly = true;
        timeInput.disabled = false; // Keep enabled so it submits with form
        
        // Visual styling
        timeInput.style.backgroundColor = '#f0f0f0';
        timeInput.style.cursor = 'not-allowed';
        timeInput.style.border = '2px solid #17a2b8';
        timeInput.style.color = '#666';
        
        // Prevent click/tap events (mobile native picker)
        timeInput.style.pointerEvents = 'none';
        
        console.log("🔒 Time input LOCKED for Renatos");
    }

    // Function to unlock the time input
    function unlockTimeInput() {
        timeInput.removeAttribute('data-renatos-locked');
        timeInput.readOnly = false;
        timeInput.style.backgroundColor = '';
        timeInput.style.cursor = '';
        timeInput.style.border = '';
        timeInput.style.color = '';
        timeInput.style.pointerEvents = '';
        
        console.log("🔓 Time input UNLOCKED");
    }

    // Function to apply time restrictions
    function applyTimeRestrictions() {
        if (!currentVenue) return;

        // Destroy existing flatpickr instance if it exists
        if (timePicker) {
            timePicker.destroy();
            timePicker = null;
        }

        if (currentVenue === "Renatos Hall" || currentVenue === "Renatos Pavilion") {
            // ===== RENATOS HALL & RENATOS PAVILION: FIXED TO 9:00 AM =====
            console.log(`${currentVenue}: Setting fixed time to 9:00 AM`);
            
            // Set the value to 9:00 AM
            timeInput.value = "09:00";
            
            // COMPLETELY LOCK the input (mobile-compatible)
            lockTimeInput();
            
            // Create flatpickr but completely disabled
            timePicker = flatpickr(timeInput, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: "09:00",
                minuteIncrement: 1,
                clickOpens: false, // Prevent picker from opening
                allowInput: false, // Prevent manual input
                onReady: function(selectedDates, dateStr, instance) {
                    instance.input.readOnly = true;
                }
            });
            
            // Add helper text
            const formGroup = timeInput.closest('.form-group');
            let helperText = formGroup.querySelector('.venue-time-restriction');
            if (!helperText) {
                helperText = document.createElement('div');
                helperText.className = 'venue-time-restriction';
                helperText.style.color = '#666';
                helperText.style.fontSize = '13px';
                helperText.style.marginTop = '4px';
                formGroup.appendChild(helperText);
            }
            helperText.textContent = `${currentVenue} check-in time is fixed at 9:00 AM`;
        } else {
            // Reset time input styling for other venues
            unlockTimeInput();
        }
    }

    // When date changes, remove helper text, reset time, and reapply time restrictions
    checkinDateInput.addEventListener("change", function() {
        console.log("🗓️ [RENATOS] Date changed - cleaning up");
        
        // FORCE remove helper text
        const formGroup = timeInput ? timeInput.closest('.form-group') : null;
        if (formGroup) {
            const helperTexts = formGroup.querySelectorAll('.venue-time-restriction');
            helperTexts.forEach(text => {
                text.remove();
                console.log("🗓️ [RENATOS] Removed helper text on date change");
            });
        }
        
        // Reset time input
        timeInput.value = "";
        
        // Unlock time input
        unlockTimeInput();
        
        // Destroy flatpickr if exists
        if (timePicker) {
            timePicker.destroy();
            timePicker = null;
        }
        
        // Clear currentVenue temporarily to prevent reapplication
        const tempVenue = currentVenue;
        currentVenue = "";
        
        // Wait a bit, then check if we need to reapply
        setTimeout(function() {
            currentVenue = tempVenue;
            if (currentVenue && packageSelect.value) {
                const selectedOption = packageSelect.selectedOptions[0];
                if (selectedOption && selectedOption.getAttribute('data-venue') === currentVenue) {
                    applyTimeRestrictions();
                }
            }
        }, 600);
    });

    // Prevent ANY changes to the time input for Renatos venues
    timeInput.addEventListener("change", function() {
        if (currentVenue === "Renatos Hall" || currentVenue === "Renatos Pavilion") {
            // Force back to 9:00 AM if user somehow changes it
            this.value = "09:00";
            console.log("🔒 Forced time back to 09:00");
        }
    });

    // Prevent focus on locked input (stops mobile keyboard)
    timeInput.addEventListener("focus", function(e) {
        if (currentVenue === "Renatos Hall" || currentVenue === "Renatos Pavilion") {
            this.blur(); // Immediately unfocus
            e.preventDefault();
            console.log("🔒 Prevented focus on locked time input");
        }
    });

    // Prevent touch events on mobile
    timeInput.addEventListener("touchstart", function(e) {
        if (currentVenue === "Renatos Hall" || currentVenue === "Renatos Pavilion") {
            e.preventDefault();
            e.stopPropagation();
            console.log("🔒 Blocked touch on locked time input");
        }
    }, { passive: false });

    // Prevent mousedown (for desktop/tablets)
    timeInput.addEventListener("mousedown", function(e) {
        if (currentVenue === "Renatos Hall" || currentVenue === "Renatos Pavilion") {
            e.preventDefault();
            e.stopPropagation();
            console.log("🔒 Blocked click on locked time input");
        }
    });
});
</script>





<script>
// ============================================
// MINI FUNCTION HALL TIME-BASED BLOCKING
// Blocks based on check-in time + duration + 1 hour cleaning
// ============================================

(function() {
    const CLEANING_TIME = 60; // 1 hour cleaning time in minutes
    let existingBookings = [];
    let packagesData = {};

    console.log("🏛️ [MINI HALL] Blocking script initialized");

    // Fetch packages to identify Mini Function Hall packages
    function fetchPackages(selectedDate) {
        return fetch(`fetch_events_packages.php?date=${encodeURIComponent(selectedDate)}`)
            .then(res => res.json())
            .then(data => {
                packagesData = {};
                if (data.packages && data.packages.length > 0) {
                    data.packages.forEach(pkg => {
                        packagesData[pkg.id] = {
                            name: pkg.name,
                            venue: pkg.venue
                        };
                    });
                    console.log(`🏛️ [MINI HALL] Loaded ${data.packages.length} packages`);
                }
                return packagesData;
            })
            .catch(err => {
                console.error("🏛️ [MINI HALL] Error fetching packages:", err);
                return {};
            });
    }

    // Fetch existing bookings for selected date
    function fetchBookings(selectedDate) {
        return fetch(`fetch_booked_events.php?date=${encodeURIComponent(selectedDate)}`)
            .then(res => res.json())
            .then(data => {
                existingBookings = [];
                
                if (data.confirmedReservations && data.confirmedReservations.length > 0) {
                    console.log(`🏛️ [MINI HALL] Found ${data.confirmedReservations.length} confirmed reservations`);
                    
                    data.confirmedReservations.forEach(booking => {
                        // Only process bookings for selected date
                        if (booking.checkin_date !== selectedDate) return;
                        if (booking.reservation_type !== 'Event Package') return;
                        if (booking.status !== 'confirmed') return;
                        
                        const packageId = parseInt(booking.events_package);
                        
                        // Check if this is a Mini Function Hall booking
                        if (packageId && packagesData[packageId]) {
                            const pkg = packagesData[packageId];
                            
                            if (pkg.venue === 'Mini Function Hall') {
                                // Get time and duration from booking
                                if (booking.checkin_time && booking.duration_hours) {
                                    const timeInMinutes = timeToMinutes(booking.checkin_time);
                                    const durationInMinutes = (parseInt(booking.duration_hours) * 60) + 
                                                             (parseInt(booking.duration_minutes || 0));
                                    
                                    // Add cleaning time
                                    const totalOccupiedMinutes = durationInMinutes + CLEANING_TIME;
                                    const endTime = timeInMinutes + totalOccupiedMinutes;
                                    
                                    existingBookings.push({
                                        id: booking.id,
                                        venue: pkg.venue,
                                        packageName: pkg.name,
                                        startMinutes: timeInMinutes,
                                        endMinutes: endTime,
                                        displayStart: booking.checkin_time,
                                        displayEnd: minutesToTime(endTime),
                                        duration: durationInMinutes,
                                        withCleaning: totalOccupiedMinutes
                                    });
                                    
                                    console.log(`🏛️ [MINI HALL] Booking ID ${booking.id}:`, {
                                        package: pkg.name,
                                        start: booking.checkin_time,
                                        duration: `${durationInMinutes} mins`,
                                        cleaning: `${CLEANING_TIME} mins`,
                                        total: `${totalOccupiedMinutes} mins`,
                                        occupied: `${booking.checkin_time} - ${minutesToTime(endTime)}`
                                    });
                                }
                            }
                        }
                    });
                }
                
                console.log(`🏛️ [MINI HALL] Total Mini Hall bookings:`, existingBookings.length);
                return existingBookings;
            })
            .catch(err => {
                console.error("🏛️ [MINI HALL] Error fetching bookings:", err);
                return [];
            });
    }

    // Convert HH:MM to minutes
    function timeToMinutes(timeStr) {
        if (!timeStr) return 0;
        const [hours, minutes] = timeStr.split(':').map(Number);
        return hours * 60 + minutes;
    }

    // Convert minutes to HH:MM
    function minutesToTime(minutes) {
        const actualMinutes = minutes % 1440;
        const hours = Math.floor(actualMinutes / 60);
        const mins = actualMinutes % 60;
        return `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;
    }

    // Check if times overlap
    function timesOverlap(start1, end1, start2, end2) {
        return start1 < end2 && start2 < end1;
    }

    // Block Mini Function Hall packages based on time conflicts
    function blockMiniHallPackages() {
        const packageSelect = document.getElementById("events_package_selection_package");
        const timeInput = document.getElementById("time");
        const hoursInput = document.getElementById("hours");
        const minutesInput = document.getElementById("minutes");
        
        if (!packageSelect || !timeInput || !hoursInput || !minutesInput) {
            console.log("🏛️ [MINI HALL] Required inputs not found");
            return;
        }

        const selectedTime = timeInput.value;
        const selectedHours = parseInt(hoursInput.value) || 0;
        const selectedMinutes = parseInt(minutesInput.value) || 0;
        
        console.log("\n🏛️ [MINI HALL] === CHECKING CONFLICTS ===");
        console.log("User selected time:", selectedTime);
        console.log("User selected duration:", selectedHours, "hrs", selectedMinutes, "mins");

        // If no time or duration selected, don't block anything
        if (!selectedTime || (selectedHours === 0 && selectedMinutes === 0)) {
            console.log("🏛️ [MINI HALL] No time/duration selected - no blocking");
            
            // Unblock all Mini Hall packages
            packageSelect.querySelectorAll("option").forEach(option => {
                const venue = option.getAttribute('data-venue');
                if (venue === 'Mini Function Hall') {
                    option.disabled = false;
                    option.style.color = '';
                    option.style.textDecoration = '';
                    option.style.backgroundColor = '';
                    option.textContent = option.textContent.replace(/\s*\(Unavailable[^)]*\)/g, '');
                }
            });
            
            return;
        }

        // Calculate user's booking time range (including cleaning time)
        const userStartMinutes = timeToMinutes(selectedTime);
        const userDurationMinutes = (selectedHours * 60) + selectedMinutes;
        const userTotalMinutes = userDurationMinutes + CLEANING_TIME;
        const userEndMinutes = userStartMinutes + userTotalMinutes;

        console.log("User's time range (with cleaning):", {
            start: selectedTime,
            end: minutesToTime(userEndMinutes),
            duration: `${userDurationMinutes} mins`,
            cleaning: `${CLEANING_TIME} mins`,
            total: `${userTotalMinutes} mins`
        });

        let hasConflict = false;
        let conflictDetails = null;

        // Check against existing Mini Hall bookings
        existingBookings.forEach(booking => {
            console.log(`\n🏛️ [MINI HALL] Comparing with booking ID ${booking.id}:`);
            console.log(`  Existing: ${booking.displayStart} - ${booking.displayEnd}`);
            console.log(`  User: ${selectedTime} - ${minutesToTime(userEndMinutes)}`);
            
            if (timesOverlap(userStartMinutes, userEndMinutes, booking.startMinutes, booking.endMinutes)) {
                hasConflict = true;
                conflictDetails = booking;
                console.log(`  ❌ CONFLICT DETECTED!`);
            } else {
                console.log(`  ✅ No conflict`);
            }
        });

        // Block or unblock Mini Hall packages
        packageSelect.querySelectorAll("option").forEach(option => {
            const venue = option.getAttribute('data-venue');
            
            if (venue === 'Mini Function Hall') {
                if (hasConflict) {
                    option.disabled = true;
                    option.style.color = '#999';
                    option.style.textDecoration = 'line-through';
                    option.style.backgroundColor = '#f8d7da';
                    
                    const currentText = option.textContent.replace(/\s*\(Unavailable[^)]*\)/g, '');
                    option.textContent = currentText + ` (Unavailable - Time Conflict)`;
                    
                    if (option.selected) {
                        packageSelect.value = "";
                    }
                    
                    console.log(`🏛️ [MINI HALL] 🚫 BLOCKED: ${option.getAttribute('data-name')}`);
                } else {
                    option.disabled = false;
                    option.style.color = '';
                    option.style.textDecoration = '';
                    option.style.backgroundColor = '';
                    option.textContent = option.textContent.replace(/\s*\(Unavailable[^)]*\)/g, '');
                    
                    console.log(`🏛️ [MINI HALL] ✅ AVAILABLE: ${option.getAttribute('data-name')}`);
                }
            }
        });

        // Auto-adjust time if there's a conflict
        if (hasConflict && conflictDetails) {
            // Calculate earliest available time (after the conflict ends)
            const earliestAvailableMinutes = conflictDetails.endMinutes;
            const earliestAvailableTime = minutesToTime(earliestAvailableMinutes);
            
            // Immediately auto-adjust the time input
            timeInput.value = earliestAvailableTime;
            
            console.log(`🏛️ [MINI HALL] ⏰ Auto-adjusted time to: ${earliestAvailableTime}`);
            
            // Recheck immediately after adjustment to unblock packages
            setTimeout(() => blockMiniHallPackages(), 100);
        }

        console.log("🏛️ [MINI HALL] === CHECK COMPLETE ===\n");
    }

    // Set default time for Mini Function Hall when selected
    function setMiniHallDefaultTime() {
        const packageSelect = document.getElementById("events_package_selection_package");
        const timeInput = document.getElementById("time");
        
        if (!packageSelect || !timeInput) return;
        
        const selectedOption = packageSelect.selectedOptions[0];
        if (!selectedOption) return;
        
        const venue = selectedOption.getAttribute('data-venue');
        
        if (venue === 'Mini Function Hall') {
            console.log("🏛️ [MINI HALL] Mini Function Hall selected - setting default time");
            
            // Get form group to add warning message
            const timeFormGroup = timeInput.closest('.form-group');
            let warningMsg = timeFormGroup ? timeFormGroup.querySelector('.mini-hall-time-warning') : null;
            
            // Check if there are any existing bookings
            if (existingBookings.length > 0) {
                // Find the latest end time
                let latestEndTime = 0;
                existingBookings.forEach(booking => {
                    if (booking.endMinutes > latestEndTime) {
                        latestEndTime = booking.endMinutes;
                    }
                });
                
                // Set to earliest available time
                const earliestAvailableTime = minutesToTime(latestEndTime);
                timeInput.value = earliestAvailableTime;
                console.log(`🏛️ [MINI HALL] Set time to earliest available: ${earliestAvailableTime}`);
                
                // Show warning message
                if (timeFormGroup) {
                    if (!warningMsg) {
                        warningMsg = document.createElement('div');
                        warningMsg.className = 'mini-hall-time-warning';
                        warningMsg.style.marginTop = '4px';
                        warningMsg.style.color = '#666';
                        warningMsg.style.fontSize = '13px';
                        timeFormGroup.appendChild(warningMsg);
                    }
                    warningMsg.innerHTML = `Mini Function Hall has existing bookings. Earliest available time is <strong>${earliestAvailableTime}</strong>.`;
                    warningMsg.style.display = 'block';
                    warningMsg.style.marginTop = '4px';
                    warningMsg.style.color = '#666';
                    warningMsg.style.fontSize = '13px';
                    warningMsg.style.backgroundColor = '';
                    warningMsg.style.border = '';
                    warningMsg.style.borderRadius = '';
                    warningMsg.style.padding = '';
                }
                
                // Set minimum time to prevent selecting conflicting times
                if (window.miniHallTimePicker) {
                    window.miniHallTimePicker.set('minTime', earliestAvailableTime);
                    console.log(`🏛️ [MINI HALL] Set minTime to: ${earliestAvailableTime}`);
                }
            } else {
                // No conflicts, set to 9:00 AM
                timeInput.value = "09:00";
                console.log("🏛️ [MINI HALL] No conflicts - set time to 9:00 AM");
                
                // Show info message
                if (timeFormGroup) {
                    if (!warningMsg) {
                        warningMsg = document.createElement('div');
                        warningMsg.className = 'mini-hall-time-warning';
                        warningMsg.style.marginTop = '4px';
                        warningMsg.style.color = '#666';
                        warningMsg.style.fontSize = '13px';
                        timeFormGroup.appendChild(warningMsg);
                    }
                    warningMsg.innerHTML = `Mini Function Hall: Earliest check-in time is <strong>9:00 AM</strong>.`;
                    warningMsg.style.display = 'block';
                    warningMsg.style.marginTop = '4px';
                    warningMsg.style.color = '#666';
                    warningMsg.style.fontSize = '13px';
                    warningMsg.style.backgroundColor = '';
                    warningMsg.style.border = '';
                    warningMsg.style.borderRadius = '';
                    warningMsg.style.padding = '';
                }
                
                // Set minimum time to 9:00 AM
                if (window.miniHallTimePicker) {
                    window.miniHallTimePicker.set('minTime', "09:00");
                    console.log("🏛️ [MINI HALL] Set minTime to: 09:00");
                }
            }
        } else {
            // Remove warning if not Mini Function Hall
            const timeFormGroup = timeInput.closest('.form-group');
            const warningMsg = timeFormGroup ? timeFormGroup.querySelector('.mini-hall-time-warning') : null;
            if (warningMsg) {
                warningMsg.style.display = 'none';
            }
        }
    }
    
    // Enforce time restrictions for Mini Function Hall
    function enforceMiniHallTimeRestrictions() {
        const packageSelect = document.getElementById("events_package_selection_package");
        const timeInput = document.getElementById("time");
        
        if (!packageSelect || !timeInput) return;
        
        const selectedOption = packageSelect.selectedOptions[0];
        if (!selectedOption) return;
        
        const venue = selectedOption.getAttribute('data-venue');
        
        if (venue === 'Mini Function Hall') {
            const currentTime = timeInput.value;
            if (!currentTime) return;
            
            const currentMinutes = timeToMinutes(currentTime);
            
            // Find the minimum allowed time
            let minAllowedMinutes = timeToMinutes("09:00"); // Default 9 AM
            
            if (existingBookings.length > 0) {
                let latestEndTime = 0;
                existingBookings.forEach(booking => {
                    if (booking.endMinutes > latestEndTime) {
                        latestEndTime = booking.endMinutes;
                    }
                });
                
                if (latestEndTime > minAllowedMinutes) {
                    minAllowedMinutes = latestEndTime;
                }
            }
            
            // Force to minimum if user selected earlier time
            if (currentMinutes < minAllowedMinutes) {
                const correctedTime = minutesToTime(minAllowedMinutes);
                timeInput.value = correctedTime;
                console.log(`🏛️ [MINI HALL] ⚠️ Corrected time from ${currentTime} to ${correctedTime}`);
                
                // Update flatpickr if it exists
                if (window.miniHallTimePicker) {
                    window.miniHallTimePicker.setDate(correctedTime, false);
                }
                
                // Recheck conflicts after correction
                setTimeout(() => blockMiniHallPackages(), 100);
            }
        }
    }
    
    // Initialize flatpickr with restrictions for Mini Function Hall
    function initializeMiniHallTimePicker() {
        const packageSelect = document.getElementById("events_package_selection_package");
        const timeInput = document.getElementById("time");
        
        if (!packageSelect || !timeInput) return;
        
        const selectedOption = packageSelect.selectedOptions[0];
        if (!selectedOption) return;
        
        const venue = selectedOption.getAttribute('data-venue');
        
        if (venue === 'Mini Function Hall') {
            // Destroy existing picker if any
            if (window.miniHallTimePicker) {
                window.miniHallTimePicker.destroy();
            }
            
            // Calculate minimum allowed time
            let minAllowedTime = "09:00";
            
            if (existingBookings.length > 0) {
                let latestEndTime = 0;
                existingBookings.forEach(booking => {
                    if (booking.endMinutes > latestEndTime) {
                        latestEndTime = booking.endMinutes;
                    }
                });
                
                if (latestEndTime > timeToMinutes("09:00")) {
                    minAllowedTime = minutesToTime(latestEndTime);
                }
            }
            
            // Create new flatpickr with restrictions
            window.miniHallTimePicker = flatpickr(timeInput, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: minAllowedTime,
                minuteIncrement: 1,
                minTime: minAllowedTime,
                onChange: function(selectedDates, dateStr, instance) {
                    console.log(`🏛️ [MINI HALL] Time changed via picker: ${dateStr}`);
                    enforceMiniHallTimeRestrictions();
                    blockMiniHallPackages();
                }
            });
            
            console.log(`🏛️ [MINI HALL] Initialized time picker with minTime: ${minAllowedTime}`);
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        console.log("🏛️ [MINI HALL] Initializing...");

        const checkinInput = document.getElementById("checkin");
        const timeInput = document.getElementById("time");
        const hoursInput = document.getElementById("hours");
        const minutesInput = document.getElementById("minutes");
        const packageSelect = document.getElementById("events_package_selection_package");

        // When date changes
        if (checkinInput) {
            checkinInput.addEventListener("change", function() {
                const selectedDate = this.value;
                if (!selectedDate) return;

                console.log("\n🏛️ [MINI HALL] 📅 Date changed:", selectedDate);

                // Remove warning message and reset time when date changes
                const timeInput = document.getElementById("time");
                if (timeInput) {
                    // Reset time value
                    timeInput.value = "";
                    console.log("🏛️ [MINI HALL] Time input reset on date change");
                    
                    // Remove Mini Hall warning message
                    const timeFormGroup = timeInput.closest('.form-group');
                    const warningMsg = timeFormGroup ? timeFormGroup.querySelector('.mini-hall-time-warning') : null;
                    if (warningMsg) {
                        warningMsg.style.display = 'none';
                        console.log("🏛️ [MINI HALL] Warning message removed on date change");
                    }
                    
                    // Remove Renatos Hall helper text
                    const helperText = timeFormGroup ? timeFormGroup.querySelector('.venue-time-restriction') : null;
                    if (helperText) {
                        helperText.remove();
                        console.log("🏛️ [MINI HALL] Renatos helper text removed on date change");
                    }
                }

                // Fetch packages and bookings
                fetchPackages(selectedDate)
                    .then(() => fetchBookings(selectedDate))
                    .then(() => {
                        // If Mini Function Hall is already selected, update its time picker
                        const selectedOption = packageSelect.selectedOptions[0];
                        if (selectedOption && selectedOption.getAttribute('data-venue') === 'Mini Function Hall') {
                            setMiniHallDefaultTime();
                            initializeMiniHallTimePicker();
                        }
                        setTimeout(() => blockMiniHallPackages(), 500);
                    });
            });
        }

        // When package changes - set default time for Mini Function Hall
        if (packageSelect) {
            packageSelect.addEventListener("change", function() {
                setMiniHallDefaultTime();
                initializeMiniHallTimePicker();
            });
        }

        // When time, hours, or minutes change
        if (timeInput) {
            timeInput.addEventListener("change", function() {
                console.log("🏛️ [MINI HALL] ⏰ Time changed:", this.value);
                enforceMiniHallTimeRestrictions();
                blockMiniHallPackages();
            });
            
            // Also enforce on input (real-time)
            timeInput.addEventListener("input", function() {
                enforceMiniHallTimeRestrictions();
            });
            
            // Enforce on blur (when user leaves the field)
            timeInput.addEventListener("blur", function() {
                enforceMiniHallTimeRestrictions();
            });
            
            // ✅ NEW: Mobile-specific validation
            // Detect when mobile native picker closes
            timeInput.addEventListener("click", function() {
                // Store the value when picker opens
                this.dataset.oldValue = this.value;
            });
            
            // Validate after mobile picker closes
            document.addEventListener("click", function(e) {
                if (e.target !== timeInput && timeInput.dataset.oldValue !== undefined) {
                    // User clicked away - validate the time
                    setTimeout(() => {
                        enforceMiniHallTimeRestrictions();
                        delete timeInput.dataset.oldValue;
                    }, 100);
                }
            });
            
            // ✅ NEW: Prevent form submission with invalid times
            const form = document.querySelector('.reservation-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const packageSelect = document.getElementById("events_package_selection_package");
                    const selectedOption = packageSelect.selectedOptions[0];
                    
                    if (selectedOption && selectedOption.getAttribute('data-venue') === 'Mini Function Hall') {
                        const currentTime = timeInput.value;
                        const currentMinutes = timeToMinutes(currentTime);
                        
                        let minAllowedMinutes = timeToMinutes("09:00");
                        
                        if (existingBookings.length > 0) {
                            let latestEndTime = 0;
                            existingBookings.forEach(booking => {
                                if (booking.endMinutes > latestEndTime) {
                                    latestEndTime = booking.endMinutes;
                                }
                            });
                            
                            if (latestEndTime > minAllowedMinutes) {
                                minAllowedMinutes = latestEndTime;
                            }
                        }
                        
                        if (currentMinutes < minAllowedMinutes) {
                            e.preventDefault();
                            const correctedTime = minutesToTime(minAllowedMinutes);
                            timeInput.value = correctedTime;
                            
                            alert(`Mini Function Hall earliest available time is ${correctedTime}. Time has been corrected.`);
                            window.scrollTo({ top: timeInput.offsetTop - 100, behavior: 'smooth' });
                            
                            return false;
                        }
                    }
                });
            }
        }

        if (hoursInput) {
            hoursInput.addEventListener("input", function() {
                console.log("🏛️ [MINI HALL] 🕐 Hours changed:", this.value);
                blockMiniHallPackages();
            });
        }

        if (minutesInput) {
            minutesInput.addEventListener("input", function() {
                console.log("🏛️ [MINI HALL] 🕐 Minutes changed:", this.value);
                blockMiniHallPackages();
            });
        }

        // Monitor dropdown changes
        if (packageSelect) {
            const observer = new MutationObserver(function() {
                if (checkinInput && checkinInput.value) {
                    console.log("🏛️ [MINI HALL] 🔄 Dropdown updated, rechecking...");
                    blockMiniHallPackages();
                }
            });

            observer.observe(packageSelect, {
                childList: true,
                subtree: false
            });
        }

        console.log("🏛️ [MINI HALL] ✅ Ready - monitoring time-based conflicts");
    });
})();
</script>

<script>
// ===== EVENT PACKAGE ROOM SELECTION DISPLAY SYSTEM =====
// Updates the button text to show selected rooms for event packages

document.addEventListener('DOMContentLoaded', function() {
  // Wait for room checkboxes to be loaded
  setTimeout(function() {
    const roomCheckboxes = document.querySelectorAll('#dropdown-content-rooms input[type="checkbox"]');
    const dropdownBtn = document.getElementById('dropdownBtn');
    
    if (!dropdownBtn) {
      console.error('[EVENT ROOMS DISPLAY] Dropdown button not found');
      return;
    }
    
    console.log('[EVENT ROOMS DISPLAY] ✓ Initialized with', roomCheckboxes.length, 'rooms');
    
    // Function to update button text based on selected rooms
    function updateRoomSelectionDisplay() {
      const selectedRooms = Array.from(document.querySelectorAll('#dropdown-content-rooms input[type="checkbox"]:checked'))
        .map(cb => cb.value);
      
      if (selectedRooms.length === 0) {
        dropdownBtn.textContent = 'Select Rooms (Optional)';
        console.log('[EVENT ROOMS DISPLAY] No rooms selected - showing default text');
      } else if (selectedRooms.length === 1) {
        dropdownBtn.textContent = selectedRooms[0];
        console.log('[EVENT ROOMS DISPLAY] ✓ 1 room selected:', selectedRooms[0]);
      } else {
        // Show all selected rooms separated by comma
        dropdownBtn.textContent = selectedRooms.join(', ');
        console.log('[EVENT ROOMS DISPLAY] ✓', selectedRooms.length, 'rooms selected:', selectedRooms.join(', '));
      }
    }
    
    // Add event listeners to all room checkboxes
    roomCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        updateRoomSelectionDisplay();
        console.log('[EVENT ROOMS DISPLAY] Room checkbox changed:', this.value, 'Checked:', this.checked);
      });
    });
    
    // Also update when date changes (rooms might get auto-deselected due to blocking)
    const checkinInput = document.getElementById('checkin');
    if (checkinInput) {
      checkinInput.addEventListener('change', function() {
        // Small delay to let blocking script run first
        setTimeout(updateRoomSelectionDisplay, 600);
        console.log('[EVENT ROOMS DISPLAY] Date changed - will update display after blocking check');
      });
    }
    
    // Monitor for when rooms are dynamically blocked/unblocked
    const roomContainer = document.getElementById('dropdown-content-rooms');
    if (roomContainer) {
      const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if (mutation.type === 'attributes' && mutation.attributeName === 'disabled') {
            // Room was blocked/unblocked - update display
            setTimeout(updateRoomSelectionDisplay, 50);
          }
        });
      });
      
      // Observe all checkboxes
      roomCheckboxes.forEach(checkbox => {
        observer.observe(checkbox, { attributes: true });
      });
    }
    
    console.log('[EVENT ROOMS DISPLAY] ✅ Event Package Room Selection Display System Loaded');
  }, 800); // Wait longer for rooms to load in event page
});
</script>


        </body>
        </html>