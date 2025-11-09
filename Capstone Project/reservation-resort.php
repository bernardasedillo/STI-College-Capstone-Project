<?php
//reservation-resort.php
require 'includes/connect.php';

function getAffiliateOptions($category, $selected = '') {
    global $conn; // your DB connection

    $stmt = $conn->prepare("SELECT id, name FROM prices WHERE venue = 'Affiliates' AND notes = ? ORDER BY id ASC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    // First, the placeholder
    $options = "<option value='' disabled selected>Select $category (Optional)</option>";

    // Then, the real 'None' option
    $isNoneSelected = ($selected === 'none') ? "selected" : "";
    $options .= "<option value='none' $isNoneSelected>None</option>";

    // Then the DB rows
    while ($row = $result->fetch_assoc()) {
        $isSelected = ($row['id'] == $selected) ? "selected" : "";
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
/* ===== ENHANCED BLOCKING STYLES FOR PACKAGES AND DURATIONS ===== */

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

/* ===== PACKAGE & DURATION BLOCKING STYLES (MATCHING ROOM BLOCKING) ===== */

/* Disabled/blocked package options */
select option:disabled {
  background-color: #f8d7da !important;
  color: #721c24 !important;
  cursor: not-allowed !important;
  opacity: 0.6 !important;
  position: relative;
}

/* Additional styling for better visibility in select dropdowns */
#resort_package_selection_package option:disabled,
#duration option:disabled {
  background-color: #f8d7da !important;
  color: #721c24 !important;
  font-style: italic;
  position: relative;
}

/* Diagonal strikethrough line for disabled options - visual effect */
#resort_package_selection_package option:disabled::before,
#duration option:disabled::before {
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

/* Add blocked icon/indicator to disabled options */
#resort_package_selection_package option:disabled::after,
#duration option:disabled::after {
  content: '🚫';
  position: absolute;
  left: 5px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 12px;
}

/* Warning message styling */
#room-warning-msg,
#duration-room-warning-msg {
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

label:has(+ input[required])::after,
label:has(+ select[required])::after,
label:has(+ textarea[required])::after,
label[data-required="true"]::after {
    content: " *";
    color: #cc4b4b; /* soft red */
    font-weight: bold;
}
</style>
</head>

<body>
  <?php include 'navbar.php'; ?>

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

            <div class="form-group">
                <label for="full_name" data-required="true">Full Name</label>
                <input type="text" name="full_name" id="full_name" placeholder="Enter your full name">
            </div>

            <div class="form-group">
                <label for="email" data-required="true">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email address">
            </div>

            <div class="form-group">
                <label for="mobile" data-required="true">Mobile Number</label>
                <input type="number"  id="mobile" name="mobile" placeholder="09XXXXXXXXX" maxlength="11"/>
                <span class="error-message" id="mobile-error" style="display:none; color:red;"></span>
            </div>

            <div class="form-group">
                <label for="full_address" data-required="true">Full Address</label>
                <input type="text" name="full_address" id="full_address" placeholder="Enter your full address" >
            </div>

            <div class="form-group">
                <label for="checkin" data-required="true">Check-In Date</label>
                <input type="text" name="checkin" id="checkin" placeholder="Select a date">
            </div>

            <div class="form-group">
                <label for="resort_package_selection">Resort Pacakge (Optional)</label>
                <select name="resort_package_selection" id="resort_package_selection_package">
                    <option value="" disabled selected>Select a package</option>
                </select>
            </div>

            <div class="form-group">
                <label for="duration" data-required="true">Duration of Stay</label>
                <select id="duration" name="duration" >
                <option value="" disabled selected>Select stay duration</option>
            </select>
            </div>
            

            <div class="form-group">
                <label for="guests" data-required="true">Number of Guests</label>
                <input type="number" name="guests" id="guests" min="1" max="50"placeholder="Enter the number of guests">
                <span id="guests-warning-daytime" style="color: black; font-size: 13px; display: none;">
                     Note: Additional Php. 150.00 per head in excess of 25 persons and above.
                </span>
                <span id="guests-warning-overnight" style="color: black; font-size: 13px; display: none;">
                     Note: Additional Php. 150.00 per head in excess of 30 persons and above.
                </span>
                <span id="guests-warning-66k" style="color: black; font-size: 13px; display: none;">
                     Note: Additional Php. 600.00 per head for food in excess of 50 persons and above.
                </span>
                <span id="guests-max" style="color: red; font-size: 13px; display: none;">
                    Maximum of 80 persons only.
                </span>
            </div>

            <div class="form-group">
                <label for="catering">Catering</label>
                <select id="affiliate_selection" name="catering" data-category="Catering option">
                    <?php echo getAffiliateOptions("Catering option"); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="lights">Lights and Sound</label>
                <select id="affiliate_selection" name="lights" data-category="Lights & Sound option">
                    <?php echo getAffiliateOptions("Lights & Sound option"); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="mobile-bar">Mobile Bar</label>
                <select id="affiliate_selection" name="mobile_bar" data-category="Mobile Bar option">
                    <?php echo getAffiliateOptions("Mobile Bar option"); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="grazing-table">Grazing Table</label>
                <select id="affiliate_selection" name="grazing_table" data-category="Grazing Table option">
                    <?php echo getAffiliateOptions("Grazing Table option"); ?>
                </select>
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
                              <input type="hidden" name="selected_resort_rooms" id="selected_resort_rooms" value="">  
                </div>
                <input type="hidden" name="total_amount" id="total_amount" value="0">
                                <input type="hidden" name="duration_text" id="duration_text" value="">
            </div>

            <div class="form-group full-width">
                <button type="button" id="payment-button">Proceed to payment</button>
            </div>
        </form>
      </div>
    </section>
  </main>

<script>
        document.addEventListener('DOMContentLoaded', function () {
        const reservationSelect = document.getElementById('reservation_type');
            reservationSelect.value = 'Resort';
            reservationSelect.dispatchEvent(new Event('change'));
        });

    document.addEventListener('DOMContentLoaded', function () {
        const reservationSelect = document.getElementById('reservation_type');

    reservationSelect.addEventListener('change', function () {
      const value = this.value;

      if (value === 'Room') {
                window.location.href = 'reservation-room.php';
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



        
        // Optional: Automatically set checkout to match or follow checkin
         document.getElementById('checkin').addEventListener('change', function() {
          const checkinDate = this.value;
        });

        document.addEventListener("DOMContentLoaded", function () {
        const mobileInput = document.getElementById("mobile");
        const mobileError = document.getElementById("mobile-error");

        function validateMobile() {
            const value = mobileInput.value.trim();
            mobileError.style.display = "none";
            mobileError.textContent = "";
            mobileInput.style.borderColor = ""; 

            if (value === "") {
            mobileError.textContent = "This field is required.";
            mobileError.style.display = "block";
            mobileInput.style.borderColor = "red";
            return false;
            }

            if (!/^\d{11}$/.test(value)) {
            mobileError.textContent = "Mobile number must be exactly 11 digits.";
            mobileError.style.display = "block";
            mobileInput.style.borderColor = "red";
            return false;
            }

            return true;
        }

        mobileInput.addEventListener("focus", function () {
            mobileError.style.display = "none";
            mobileInput.style.borderColor = "";

            if (!mobileInput.value.startsWith("09")) {
            mobileInput.value = "09";
            }

            setTimeout(() => {
            mobileInput.setSelectionRange(mobileInput.value.length, mobileInput.value.length);
            }, 0);
        });

        mobileInput.addEventListener("input", function () {
            let val = mobileInput.value;

            if (!val.startsWith("09")) {
            val = "09" + val.replace(/\D/g, ""); 
            }

        
            val = val.replace(/\D/g, "");

            
            if (val.length > 11) {
            val = val.slice(0, 11);
            }

            
            mobileInput.value = val;
        });

        mobileInput.addEventListener("blur", validateMobile);
        document.querySelector("form").addEventListener("submit", function (e) {
            if (!validateMobile()) e.preventDefault();
        });
        });

document.addEventListener("DOMContentLoaded", function () {
    const packageSelect = document.getElementById("resort_package_selection_package");
    const roomCheckboxes = document.querySelectorAll("#dropdown-content-rooms input[type='checkbox']");
    const durationGroup = document.getElementById("check_in_time").closest(".form-group");
    const roomLabel = document.querySelector("label[for='event_package_selection_room']");

    function toggleUI() {
        const hasPackage = packageSelect.value !== "" && packageSelect.value !== "none";
        const hasRoomSelected = Array.from(roomCheckboxes).some(cb => cb.checked);

        // --- Show/hide duration field (for rooms)
        if (!hasPackage && hasRoomSelected) {
            durationGroup.style.display = "block";
        } else {
            durationGroup.style.display = "none";
            document.getElementById("check_in_time").value = ""; // reset if hidden
        }

        // --- Remove or restore (Optional) on Room label ---
        if (hasPackage) {
            roomLabel.textContent = roomLabel.textContent.replace(/\(Optional\)/i, "").trim();
        } else {
            if (!roomLabel.textContent.includes("(Optional)")) {
                roomLabel.textContent += " (Optional)";
            }
        }
    }

    // Event listeners
    packageSelect.addEventListener("change", toggleUI);
    roomCheckboxes.forEach(cb => cb.addEventListener("change", toggleUI));
});

const form = document.querySelector('.reservation-form');
const paymentButton = document.getElementById('payment-button');

paymentButton.addEventListener('click', function (e) {
    e.preventDefault();
    let isValid = true;
    let firstInvalidField = null;
    const validatedFields = new Set(); // ✅ Track validated fields

    // Reset previous error styles/messages
    document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(field => {
        field.style.borderColor = '';
        // Remove ALL existing error messages
        const existingErrors = field.parentElement.querySelectorAll('.field-error');
        existingErrors.forEach(err => err.remove());
    });

    // Validate visible required fields
    const visibleFields = document.querySelectorAll(
        '.reservation-form .form-group input:not([type="checkbox"]):not([type="hidden"]), .reservation-form .form-group select, .reservation-form .form-group textarea'
    );

    visibleFields.forEach(field => {
        // ✅ Skip if already validated
        if (validatedFields.has(field.id)) return;
        validatedFields.add(field.id);

        const parent = field.closest('.form-group');
        const isHidden =
            window.getComputedStyle(parent).display === 'none' ||
            parent.offsetParent === null;

        // ✅ Skip optional fields and sections we don't validate
        if (
            field.id === 'resort_package_selection_food' ||
            field.id === 'event_package_selection_room' ||
            field.id === 'affiliate_selection' ||
            field.id === 'resort_package_selection_package' ||
            field.id === 'check_in_time' ||
            (parent && (parent.querySelector('#total-payment-box') || parent.querySelector('#payment-button')))
        ) {
            return;
        }

        // 📱 SPECIAL VALIDATION FOR MOBILE NUMBER
        if (field.id === 'mobile' && !isHidden) {
            const mobileValue = field.value.trim();
            
            if (mobileValue === '' || mobileValue === '09') {
                isValid = false;
                if (!firstInvalidField) firstInvalidField = field;

                field.style.borderColor = 'red';
                const errorMsg = document.createElement('div');
                errorMsg.classList.add('field-error');
                errorMsg.style.color = 'red';
                errorMsg.style.fontSize = '13px';
                errorMsg.textContent = 'This field is required.';
                field.parentElement.appendChild(errorMsg);
            } 
            else if (!/^\d{11}$/.test(mobileValue)) {
                isValid = false;
                if (!firstInvalidField) firstInvalidField = field;

                field.style.borderColor = 'red';
                const errorMsg = document.createElement('div');
                errorMsg.classList.add('field-error');
                errorMsg.style.color = 'red';
                errorMsg.style.fontSize = '13px';
                errorMsg.textContent = '';
                field.parentElement.appendChild(errorMsg);
            }
            else if (!mobileValue.startsWith('09')) {
                isValid = false;
                if (!firstInvalidField) firstInvalidField = field;

                field.style.borderColor = 'red';
                const errorMsg = document.createElement('div');
                errorMsg.classList.add('field-error');
                errorMsg.style.color = 'red';
                errorMsg.style.fontSize = '13px';
                errorMsg.textContent = 'Mobile number must start with 09.';
                field.parentElement.appendChild(errorMsg);
            }
            return; // Skip general validation for mobile
        }

        // 🔴 GENERAL REQUIRED FIELD VALIDATION (excluding message field)
        if (!isHidden && field.id !== 'message' && field.value.trim() === '') {
            isValid = false;
            if (!firstInvalidField) firstInvalidField = field;

            field.style.borderColor = 'red';
            const errorMsg = document.createElement('div');
            errorMsg.classList.add('field-error');
            errorMsg.style.color = 'red';
            errorMsg.style.fontSize = '13px';
            errorMsg.textContent = 'This field is required.';
            field.parentElement.appendChild(errorMsg);
        }
    });

    // 🚫 Scroll to first invalid field if any
    if (!isValid && firstInvalidField) {
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    // ✅ If valid, show Terms modal
    const modal = document.getElementById('termsModal');
    if (modal) {
        modal.style.display = 'flex';
    } else {
        console.warn('Terms modal not found.');
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


document.addEventListener("DOMContentLoaded", () => {
    const checkinInput = document.getElementById("checkin");
    const packageSelect = document.getElementById("resort_package_selection_package");
    const durationSelect = document.getElementById("duration");
    const guestsInput = document.getElementById("guests");
    const totalPaymentSpan = document.getElementById("total-payment");
    const hiddenTotalInput = document.getElementById("total_amount");

    const guestsWarning = document.getElementById("guests-warning-daytime");
    const guestsWarning1 = document.getElementById("guests-warning-overnight");
    const guestsWarning2 = document.getElementById("guests-warning-66k");
    const guestsMax = document.getElementById("guests-max");

    let resortData = null; // cache fetched data

    // 🔹 Load resort packages + durations when date changes
    checkinInput.addEventListener("change", () => {
        const date = checkinInput.value;
        if (!date) return;

        fetch(`fetch_resort.php?date=${encodeURIComponent(date)}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                console.log("RESORT DATA:", data);
                if (data.message) {
                    console.warn(data.message);
                    return;
                }

                resortData = data;

                // Resort packages
                packageSelect.innerHTML = `
                    <option value="" disabled selected>Select a package</option>
                    <option value="none">None</option>
                `;
                data.packageOptions.forEach(pkg => {
                    packageSelect.innerHTML += `
                        <option value="${pkg.id}" data-price="${pkg.price}" data-hours="${pkg.duration_hours}">
                            ${pkg.name} (${pkg.duration_hours})
                        </option>
                    `;
                });

                // Default durations (only for "None" package)
                populateDurations(data.durations);

                // Reset total
                totalPaymentSpan.textContent = "0";
                hiddenTotalInput.value = 0;
            })
            .catch(err => console.error("Error fetching resort data:", err));
    });

function populateDurations(durations, packageSelected = false, pkg = null) {
    durationSelect.innerHTML = `
        <option value="" disabled selected>Select stay duration</option>
    `;

    if (packageSelected && pkg) {
        // Locked package option
        durationSelect.innerHTML += `
            <option value="package_duration" selected disabled>
                ${pkg.name} (${pkg.duration_hours})
            </option>
        `;
    } else {
        // Normal durations
        durations.forEach(d => {
            durationSelect.innerHTML += `
                <option value="${d.id}">
                     ${d.name} (${d.hours}) - ₱${d.price.toLocaleString()}
                </option>
            `;
        });
        // Ensure dropdown is enabled
        durationSelect.disabled = false;
    }
}


    // Helper finders (assumes resortData is the object returned by the PHP endpoint)
function getSelectedPackageObj() {
    const pkgId = packageSelect.value;
    if (!pkgId || pkgId === 'none') return null;
    return resortData.packageOptions?.find(p => String(p.id) === String(pkgId)) || null;
}
function getSelectedDurationObj() {
    const durationId = durationSelect.value;
    if (!durationId) return null;
    return resortData.durations?.find(d => String(d.id) === String(durationId)) || null;
}

window.calculatePrice = function() {
    if (!resortData && !roomPricesData) return;

    const pkg = getSelectedPackageObj();
    const dur = getSelectedDurationObj();
    let guests = parseInt(guestsInput.value, 10);

    if (isNaN(guests) || guests < 0) {
        guests = null;
    }
    if (guests !== null && guests > 80) {
        guests = 80;
        guestsInput.value = 80;
    }

    let total = 0;
    let basePrice = 0;
    let usedMaxGuest = 0;
    let excessRate = resortData?.excessRates?.default ?? 0;

    // --- 1. PACKAGE SELECTED ---
    if (pkg) {
        basePrice = parseFloat(pkg.price) || 0;
        usedMaxGuest = pkg.max_guest ? parseInt(pkg.max_guest, 10) : 60;

        if ((pkg.name || "").toLowerCase().includes("66k")) {
            excessRate = resortData.excessRates?.["66k"] ?? excessRate;
        }

    // --- 2. DURATION SELECTED ---
    } else if (dur) {
        basePrice = parseFloat(dur.price) || 0;

        if (dur.max_guest) {
            usedMaxGuest = parseInt(dur.max_guest, 10);
        } else {
            const nameLower = (dur.name || "").toLowerCase();
            if (nameLower.includes("staycation")) usedMaxGuest = 30;
            else if (nameLower.includes("day") || nameLower.includes("overnight")) usedMaxGuest = 25;
            else usedMaxGuest = 80;
        }

    // --- 3. NO PACKAGE / NO DURATION ---
    } else {
        basePrice = 0;
    }

    // add base price
    total += basePrice;

    // --- ROOMS (always optional add-on) ---
    const selectedRooms = Array.from(
        document.querySelectorAll("#dropdown-content-rooms input[type=checkbox]:checked")
    ).map(cb => cb.value);

    const duration = document.getElementById("check_in_time")?.value || "";

    selectedRooms.forEach(roomName => {
        const match = roomPricesData.find(
            rp => rp.name === roomName && rp.duration_hours === duration
        );
        if (match) {
            total += parseFloat(match.price) || 0;
        }
    });

    // --- EXCESS GUEST HANDLING ---
    if ((pkg || dur) && guests !== null && guests > usedMaxGuest) {
        const excessGuests = guests - usedMaxGuest;
        total += excessGuests * excessRate;
    }

    // --- UPDATE UI ---
    totalPaymentSpan.textContent = total.toLocaleString();
    hiddenTotalInput.value = total;
}


// Guest warnings (shows dynamic message based on the max_guest/excess_rate)
function updateGuestWarnings() {
    let guests = parseInt(guestsInput.value, 10) || 0;

    // Reset/hide warnings
    guestsWarning.style.display = "none";
    guestsWarning1.style.display = "none";
    guestsWarning2.style.display = "none";
    guestsMax.style.display = "none";
    
            // NEW CODE!!!!!!!!
        // NEW CODE!!!!!!!!
        // NEW CODE!!!!!!!!
    if (guests < 1) {
        guestsInput.value = "";
        guestsMax.style.display = "block";
        guestsMax.textContent = "Minimum of 1 guest is required.";
        guestsInput.style.borderColor = "red";

        setTimeout(() => {
            guestsMax.style.display = "none";
            guestsInput.style.borderColor = "";
        }, 1500);

        return; // stop further processing
    }

    // Absolute max clamp
    if (guests > 80) {
        guestsMax.style.display = "block";
        guests = 80;
        guestsInput.value = 80;
    }

    const pkg = getSelectedPackageObj();
    const dur = getSelectedDurationObj();

    let usedMaxGuest = null;
    let excessRate = resortData.excessRates?.default ?? 0; // ✅ FIXED
    let labelName = "";

    if (pkg) {
        usedMaxGuest = (pkg.max_guest !== null && pkg.max_guest !== undefined) 
            ? parseInt(pkg.max_guest, 10) 
            : 60;

        if ((pkg.name || "").toLowerCase().includes("66k")) {
            // ✅ use backend’s 66k rate
            excessRate = resortData.excessRates?.["66k"] ?? excessRate;
        } else {
            excessRate = (pkg.excess_rate !== null && pkg.excess_rate !== undefined) 
                ? parseFloat(pkg.excess_rate) 
                : excessRate;
        }

        labelName = pkg.name || "Selected package";
    } else if (dur) {
        if (dur.max_guest !== null && dur.max_guest !== undefined) {
            usedMaxGuest = parseInt(dur.max_guest, 10);
        } else {
            const nameLower = (dur.name || "").toLowerCase();
            if (nameLower.includes("staycation")) usedMaxGuest = 30;
            else if (nameLower.includes("day") || nameLower.includes("overnight")) usedMaxGuest = 25;
            else usedMaxGuest = 80;
        }

        excessRate = (dur.excess_rate !== null && dur.excess_rate !== undefined) 
            ? parseFloat(dur.excess_rate) 
            : (resortData.excessRates?.default ?? 0);

        labelName = dur.name || "Selected duration";
    }

    if (usedMaxGuest && guests > usedMaxGuest) {
        const msg = `For ${labelName}, additional ₱${Number(excessRate).toLocaleString()} per guest will be charged for guests over ${usedMaxGuest}.`;

        if (pkg) {
            guestsWarning2.textContent = msg;
            guestsWarning2.style.display = "block";
        } else {
            const nameLower = (labelName || "").toLowerCase();
            if (nameLower.includes("staycation")) {
                guestsWarning1.textContent = msg;
                guestsWarning1.style.display = "block";
            } else {
                guestsWarning.textContent = msg;
                guestsWarning.style.display = "block";
            }
        }
    }

    calculatePrice();
}


// Attach listeners (preserve your existing attachments)
guestsInput.addEventListener("input", updateGuestWarnings);
packageSelect.addEventListener("change", updateGuestWarnings);
durationSelect.addEventListener("change", updateGuestWarnings);
durationSelect.addEventListener("change", calculatePrice);
guestsInput.addEventListener("input", calculatePrice);



packageSelect.addEventListener("change", () => {
    const selectedVal = packageSelect.value;

    if (selectedVal && selectedVal !== "none") {
        const option = packageSelect.options[packageSelect.selectedIndex];

        // Populate locked duration dropdown
        populateDurations([], true, {
            name: option.text.split(" - ₱")[0], // extract name
            duration_hours: option.dataset.hours
        });

        // Disable duration dropdown
        durationSelect.disabled = true;
    } else if (resortData) {
        populateDurations(resortData.durations);
        // Enable duration dropdown
        durationSelect.disabled = false;
    }

    calculatePrice();
});
    durationSelect.addEventListener("change", calculatePrice);
});

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
    
document.addEventListener("DOMContentLoaded", function () {

    const packageSelect = document.getElementById("resort_package_selection_package");
    const roomCheckboxes = document.querySelectorAll("#dropdown-content-rooms input[type=checkbox]");
    const durationGroup = document.getElementById("check_in_time").closest(".form-group");

    fetch("fetch_rooms.php")
        .then(response => response.json())
        .then(data => {
            roomPricesData = data.roomPrices || [];
            console.log("roomPricesData loaded:", roomPricesData);

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

                    const label = document.createElement("label");
                    label.setAttribute("for", safeId);
                    label.textContent = room;

                    wrapper.appendChild(checkbox);
                    wrapper.appendChild(label);
                    dropdownContent.appendChild(wrapper);
                });
            }

            // Grab the full duration form-group (label + select + warning msg)
            const durationGroup = document.querySelector("#check_in_time").closest(".form-group");
            const durationSelect = document.getElementById("check_in_time");

            // Populate durations
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

            // Hide initially
            durationGroup.style.display = "none";

// Function to toggle visibility
function toggleDurationVisibility() {
    const hasRooms = document.querySelector("#dropdown-content-rooms input[type=checkbox]:checked");
    if (hasRooms) {
        durationGroup.style.display = "block";
    } else {
        durationGroup.style.display = "none";
        document.getElementById("check_in_time").value = ""; // reset
    }
}

    packageSelect.addEventListener("change", () => {
        calculatePrice();
        toggleDurationVisibility();
    });

    // ✅ Move this inside the fetch block, after rendering rooms:
    dropdownContent.addEventListener("change", () => {
        calculatePrice();
        toggleDurationVisibility();
    });

    document.getElementById("check_in_time").addEventListener("change", calculatePrice);

    // initial state
    toggleDurationVisibility();


    roomCheckboxes.forEach(cb => cb.addEventListener("change", () => {
        calculatePrice();
        toggleDurationVisibility();
    }));

    document.getElementById("check_in_time").addEventListener("change", calculatePrice);

    // initial
    toggleDurationVisibility();
        })
        .catch(error => console.error("Error fetching room data:", error));
});

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

    <script> // NEW CODE!!!!!!!!!!!
    document.addEventListener("DOMContentLoaded", function () {
        const guestsInput = document.getElementById("guests");

        // Prevent typing anything that's not 0-9
        guestsInput.addEventListener("keydown", function (e) {
            // Allow: backspace, delete, tab, arrow keys
            if (
                ["Backspace", "Delete", "Tab", "ArrowLeft", "ArrowRight", "Home", "End"].includes(e.key)
            ) {
                return;
            }

            // Prevent letters, "e", "+", "-", etc.
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Optional: sanitize on paste
        guestsInput.addEventListener("paste", function (e) {
            const pasted = (e.clipboardData || window.clipboardData).getData("text");
            if (!/^\d+$/.test(pasted)) {
                e.preventDefault();
            }
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
    fetch('admin/get_rest_days.php')
      .then(response => response.json())
      .then(restDays => {
        flatpickr("#checkin", {
          altInput: true,
          altFormat: "F j, Y",
          dateFormat: "Y-m-d",
          minDate: new Date().fp_incr(1),
          disable: restDays,
        });
      });
  });
</script>

<script>
  // Make sure this hidden input exists in your HTML form:
// <input type="hidden" name="duration_text" id="duration_text" value="">

// ✅ UPDATED: Better duration text capture logic
document.addEventListener("DOMContentLoaded", () => {
    const durationSelect = document.getElementById("duration");
    const packageSelect = document.getElementById("resort_package_selection_package");
    const durationTextInput = document.getElementById("duration_text");

    // Function to update hidden duration_text field
    function updateDurationText() {
        if (!durationTextInput) {
            console.error("duration_text hidden input not found!");
            return;
        }

        const packageVal = packageSelect.value;
        
        // If package is selected (not "none" or empty)
        if (packageVal && packageVal !== "none") {
            const packageOption = packageSelect.options[packageSelect.selectedIndex];
            const hours = packageOption.dataset.hours || packageOption.getAttribute("data-hours");
            
            if (hours) {
                durationTextInput.value = hours;
                console.log("Duration from package:", hours);
            }
        } 
        // If duration dropdown has a value selected
        else if (durationSelect.value && durationSelect.value !== "" && durationSelect.value !== "package_duration") {
            const durationOption = durationSelect.options[durationSelect.selectedIndex];
            const optionText = durationOption.textContent || durationOption.innerText;
            
            // Try to extract hours from text like "Day Use (8AM-5PM) - ₱6,000"
            const hoursMatch = optionText.match(/\(([^)]+)\)/);
            
            if (hoursMatch && hoursMatch[1]) {
                durationTextInput.value = hoursMatch[1];
                console.log("Duration from dropdown:", hoursMatch[1]);
            } else {
                // Fallback: use the full option text
                durationTextInput.value = optionText.split(" - ")[0].trim();
                console.log("Duration fallback:", durationTextInput.value);
            }
        } else {
            durationTextInput.value = "";
            console.log("Duration cleared");
        }
    }

    // Attach event listeners
    if (packageSelect) {
        packageSelect.addEventListener("change", () => {
            updateDurationText();
            // Your existing package change logic here...
        });
    }

    if (durationSelect) {
        durationSelect.addEventListener("change", () => {
            updateDurationText();
            // Your existing duration change logic here...
        });
    }

    // Also update before form submission as a safety measure
    const form = document.querySelector('.reservation-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            updateDurationText();
            console.log("Final duration_text value:", durationTextInput ? durationTextInput.value : "input not found");
        });
    }
});

// ✅ Also integrate into your existing packageSelect.addEventListener("change")
// Replace or merge with your existing code:
packageSelect.addEventListener("change", () => {
    const selectedVal = packageSelect.value;
    const durationTextInput = document.getElementById("duration_text");

    if (selectedVal && selectedVal !== "none") {
        const option = packageSelect.options[packageSelect.selectedIndex];
        const hoursValue = option.dataset.hours || option.getAttribute("data-hours");

        // Store duration_hours text in hidden input
        if (durationTextInput && hoursValue) {
            durationTextInput.value = hoursValue;
            console.log("✅ Package duration set:", hoursValue);
        }

        // Populate locked duration dropdown
        populateDurations([], true, {
            name: option.text.split(" - ")[0],
            duration_hours: hoursValue
        });

        durationSelect.disabled = true;
    } else {
        if (durationTextInput) {
            durationTextInput.value = "";
        }
        
        if (resortData) {
            populateDurations(resortData.durations);
            durationSelect.disabled = false;
        }
    }

    calculatePrice();
});

// ✅ Also integrate into your existing durationSelect.addEventListener("change")
durationSelect.addEventListener("change", () => {
    const durationTextInput = document.getElementById("duration_text");
    const selectedOption = durationSelect.options[durationSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value && selectedOption.value !== "package_duration" && selectedOption.value !== "") {
        const optionText = selectedOption.textContent || selectedOption.innerText;
        
        // Extract hours from text like "Day Use (8AM-5PM) - ₱6,000"
        const hoursMatch = optionText.match(/\(([^)]+)\)/);
        
        if (hoursMatch && hoursMatch[1] && durationTextInput) {
            durationTextInput.value = hoursMatch[1];
            console.log("✅ Duration set:", hoursMatch[1]);
        } else if (durationTextInput) {
            // Fallback: store the full text
            durationTextInput.value = optionText.split(" - ")[0].trim();
            console.log("✅ Duration fallback set:", durationTextInput.value);
        }
    } else if (durationTextInput) {
        durationTextInput.value = "";
    }

    calculatePrice();
});
    </script>



<script>
// Update hidden field when room checkboxes change
document.addEventListener('DOMContentLoaded', function() {
    const roomCheckboxes = document.querySelectorAll('input[name="room_number[]"]');
    const hiddenInput = document.getElementById('selected_resort_rooms');
    
    if (hiddenInput) {
        function updateSelectedRooms() {
            const selectedRooms = Array.from(roomCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            hiddenInput.value = selectedRooms.join(',');
            console.log('✅ Resort rooms updated:', hiddenInput.value);
        }
        
        // Listen to checkbox changes
        roomCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedRooms);
        });
        
        // Update before form submission
        const form = document.querySelector('.reservation-form');
        if (form) {
            form.addEventListener('submit', function() {
                updateSelectedRooms();
                console.log('📤 Final resort rooms on submit:', hiddenInput.value);
            });
        }
    }
});
</script>




<script>
// ===== ROOM BLOCKING - SHOW BLOCKED ROOMS WITHOUT PACKAGE/DURATION REQUIREMENT ===== 
let bookedRoomsData = [];
let restDaysData = [];

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
          bookedRoomsData = data.all_bookings;
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

// Parse time string to minutes (e.g., "14:00" -> 840)
function timeToMinutes(timeStr) {
  if (!timeStr) return 0;
  const [hours, minutes] = timeStr.split(':').map(Number);
  return hours * 60 + minutes;
}

// Extract time range from duration text
function extractTimeFromDuration(durationText) {
  if (!durationText) return null;
  
  // Pattern 1: Match time ranges like "7:00pm - 5:00pm", "8AM-5PM"
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
    
    // Calculate minutes from start of day
    let startMinutes = startHour * 60 + startMin;
    let endMinutes = endHour * 60 + endMin;
    
    // If end time is before start time, it's overnight
    const isOvernight = endMinutes <= startMinutes;
    if (isOvernight) {
      endMinutes += 1440; // Add 24 hours (1440 minutes)
    }
    
    return { 
      startMinutes: startMinutes, 
      endMinutes: endMinutes,
      isOvernight: isOvernight,
      displayStart: String(startHour).padStart(2, '0') + ':' + String(startMin).padStart(2, '0'),
      displayEnd: String(endHour).padStart(2, '0') + ':' + String(endMin).padStart(2, '0')
    };
  }
  
  return null;
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

// Helper function to convert minutes back to time string
function minutesToTime(minutes) {
  const actualMinutes = minutes % 1440;
  const hours = Math.floor(actualMinutes / 60);
  const mins = actualMinutes % 60;
  return hours.toString().padStart(2, '0') + ':' + mins.toString().padStart(2, '0');
}

// Get unavailable rooms for a specific date (regardless of package/duration selection)
function getUnavailableRoomsForDate(checkinDate) {
  console.log('🔎 Getting ALL unavailable rooms for date:', checkinDate);
  
  const unavailableRooms = new Set();
  
  if (!checkinDate) {
    console.log('⚠️ Missing date');
    return unavailableRooms;
  }
  
  // ✅ CHECK REST DAYS - Block ALL rooms if today is a rest day
  if (restDaysData.includes(checkinDate)) {
    console.log('🚫 TODAY IS A REST DAY - Blocking ALL rooms');
    const allRoomCheckboxes = document.querySelectorAll('#dropdown-content-rooms input[type="checkbox"]');
    allRoomCheckboxes.forEach(cb => {
      unavailableRooms.add(cb.value);
      console.log('    🚫 Blocked (rest day):', cb.value);
    });
    return unavailableRooms;
  }
  
  // Check each existing booking on this date
  bookedRoomsData.forEach((booking, index) => {
    console.log(`\n📅 Checking booking ${index + 1}:`, {
      id: booking.id,
      type: booking.reservation_type,
      date: booking.checkin_date,
      time: booking.checkin_time,
      duration: booking.duration_hours || booking.resort_duration_hours,
      rooms: [...(booking.rooms_array || []), ...(booking.resort_rooms_array || [])]
    });
    
    // Skip if different date
    if (booking.checkin_date !== checkinDate) {
      console.log('  ⏩ Different date, skipping');
      return;
    }
    
    // Get all rooms from this booking
    const bookingRooms = [...(booking.rooms_array || []), ...(booking.resort_rooms_array || [])];
    
    if (bookingRooms.length === 0) {
      console.log('  ⏩ No rooms in booking, skipping');
      return;
    }
    
    // If booking exists on this date, mark these rooms as unavailable
    console.log('  🚫 Booking exists - blocking rooms:', bookingRooms);
    bookingRooms.forEach(room => {
      unavailableRooms.add(room);
      console.log('    🚫 Blocked:', room);
    });
  });
  
  console.log('\n🎯 Final unavailable rooms for date:', Array.from(unavailableRooms));
  return unavailableRooms;
}

// Store globally which rooms are blocked by time conflicts
window.conflictBlockedRooms = new Set();

// Enhanced checkRoomAvailability function - NOW BYPASSES PACKAGE/DURATION REQUIREMENT
function checkRoomAvailability() {
  console.log('🔍 Checking room availability for selected date...');
  
  const checkinDate = document.getElementById('checkin').value;
  const roomCheckboxes = document.querySelectorAll('#dropdown-content-rooms input[type="checkbox"]');
  
  console.log('📋 Form values:', {
    date: checkinDate
  });
  
  const warningMsg = document.getElementById('room-warning-msg-resort');
  
  // If no date selected, don't block anything
  if (!checkinDate) {
    console.log('⚠️ No date selected - room blocking disabled');
    
    window.conflictBlockedRooms = new Set();
    
    // Re-enable all rooms
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
    
    return;
  }
  
  // ✅ Check for conflicts based on date only - NO PACKAGE/DURATION REQUIRED
  console.log('✅ Date available - checking all bookings on this date...');
  const unavailableRooms = getUnavailableRoomsForDate(checkinDate);
  console.log('🚫 Unavailable rooms:', Array.from(unavailableRooms));
  
  window.conflictBlockedRooms = unavailableRooms;
  
  // Apply booking conflict blocking
  roomCheckboxes.forEach(checkbox => {
    const roomName = checkbox.value;
    
    if (unavailableRooms.has(roomName)) {
      console.log('❌ Blocking room due to existing booking:', roomName);
      
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
          label.title = 'This room is already booked on this date';
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
      warningText = `⚠️ ${unavailableRooms.size} room(s) already booked on this date`;
    }
    
    warningMsg.innerHTML = warningText;
    warningMsg.style.display = 'block';
    warningMsg.style.color = '#721c24';
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
  
  if (typeof window.calculatePrice === 'function') {
    window.calculatePrice();
  }
}

// Add a MutationObserver to prevent other code from re-enabling blocked rooms
function protectBlockedRooms() {
  const roomCheckboxes = document.querySelectorAll('#dropdown-content-rooms input[type="checkbox"]');
  
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
  console.log('🔄 Loading rest days and booked rooms for room blocking...');
  
  const checkinInput = document.getElementById('checkin');
  const packageSelect = document.getElementById('resort_package_selection_package');
  const resortDurationSelect = document.getElementById('duration');
  const roomCheckboxesContainer = document.getElementById('dropdown-content-rooms');
  
  // ✅ Load rest days first, then booked rooms
  loadRestDays().then(() => {
    return loadBookedRooms();
  }).then(() => {
    const checkRoomsLoaded = setInterval(() => {
      if (roomCheckboxesContainer && roomCheckboxesContainer.querySelectorAll('input[type="checkbox"]').length > 0) {
        clearInterval(checkRoomsLoaded);
        
        console.log('✓ Rooms loaded, system ready');
        
        protectBlockedRooms();
      }
    }, 100);
  });
  
  // Add event listeners for date changes
  if (checkinInput) {
    checkinInput.addEventListener('change', checkRoomAvailability);
  }
  
  // Optional: Still recheck when package/duration changes (for future enhancements)
  if (packageSelect) {
    packageSelect.addEventListener('change', function() {
      console.log('🔄 Package selection changed, rechecking rooms...');
      checkRoomAvailability();
    });
  }
  
  if (resortDurationSelect) {
    resortDurationSelect.addEventListener('change', function() {
      console.log('🔄 Resort duration selection changed, rechecking rooms...');
      checkRoomAvailability();
    });
  }
});
</script>

<script>
    // ============================================
// RESORT PACKAGE & DURATION BLOCKING SYSTEM
// Blocks packages/durations based on existing Resort AND Event Package bookings
// ============================================

(function() {
    console.log("🏖️ [RESORT BLOCKING] Script initialized");

    let existingBookings = [];
    let blockedPackageIds = [];
    let blockedDurationTexts = [];
    let currentDate = null;

    // Parse time string (HH:MM or H:MM) to minutes since midnight
    function timeToMinutes(timeStr) {
        if (!timeStr) return 0;
        const [hours, minutes] = timeStr.split(':').map(Number);
        return hours * 60 + minutes;
    }

    // Extract time range from duration text (e.g., "7:00pm - 5:00pm")
    function extractTimeFromDuration(durationText) {
        if (!durationText) return null;
        
        // Match patterns like "7:00pm - 5:00pm" or "7pm - 5pm" or "8AM-5PM"
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

    // Convert minutes to HH:MM format
    function minutesToTime(minutes) {
        const actualMinutes = minutes % 1440;
        const hours = Math.floor(actualMinutes / 60);
        const mins = actualMinutes % 60;
        return `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;
    }

    // Fetch resort bookings for the selected date
    function fetchResortBookings(selectedDate) {
        console.log("\n🏖️ [RESORT BLOCKING] 📅 Fetching bookings for:", selectedDate);
        
        return fetch(`fetch_booked_resort.php?date=${encodeURIComponent(selectedDate)}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    console.error("🏖️ [RESORT BLOCKING] ❌ Error:", data.error);
                    return { bookings: [], blockedPackages: [], blockedDurations: [] };
                }

                console.log("🏖️ [RESORT BLOCKING] ✅ Data received:", {
                    existingBookings: data.existingBookings?.length || 0,
                    blockedPackages: data.blockedPackages?.length || 0,
                    blockedDurations: data.blockedDurations?.length || 0,
                    confirmedReservations: data.confirmedReservations?.length || 0
                });

                // Process existing bookings with time details
                const processedBookings = [];
                
                if (data.existingBookings && data.existingBookings.length > 0) {
                    data.existingBookings.forEach(booking => {
                        console.log(`\n🏖️ [RESORT BLOCKING] Processing booking:`, {
                            id: booking.reservation_id,
                            type: booking.reservation_type,
                            itemType: booking.item_type,
                            itemName: booking.item_name,
                            durationHours: booking.duration_hours
                        });

                        const timeInfo = extractTimeFromDuration(booking.duration_hours);
                        
                        if (timeInfo) {
                            processedBookings.push({
                                ...booking,
                                timeInfo: timeInfo
                            });
                            
                            console.log(`   ✅ Time extracted:`, {
                                start: timeInfo.displayStart,
                                end: timeInfo.displayEnd,
                                overnight: timeInfo.isOvernight
                            });
                        } else {
                            console.log(`   ⚠️ Could not extract time from: "${booking.duration_hours}"`);
                        }
                    });
                }

                return {
                    bookings: processedBookings,
                    blockedPackages: data.blockedPackages || [],
                    blockedDurations: data.blockedDurations || []
                };
            })
            .catch(err => {
                console.error("🏖️ [RESORT BLOCKING] ❌ Fetch error:", err);
                return { bookings: [], blockedPackages: [], blockedDurations: [] };
            });
    }

    // Block conflicting packages
    function blockConflictingPackages() {
        const packageSelect = document.getElementById("resort_package_selection_package");
        if (!packageSelect) {
            console.log("🏖️ [RESORT BLOCKING] ⚠️ Package select not found");
            return;
        }

        console.log("\n🏖️ [RESORT BLOCKING] 🔍 Checking package conflicts...");
        console.log("   Existing bookings:", existingBookings.length);
        console.log("   Blocked package IDs:", blockedPackageIds);

        const packageOptions = packageSelect.querySelectorAll("option");
        
        packageOptions.forEach(option => {
            if (!option.value || option.value === "" || option.value === "none") return;

            const packageId = parseInt(option.value);
            const packageName = option.textContent;
            const durationHours = option.getAttribute('data-hours');

            console.log(`\n🏖️ [RESORT BLOCKING] Checking package:`, {
                id: packageId,
                name: packageName,
                duration: durationHours
            });

            // Check if this package ID is explicitly blocked (full resort booking)
            if (blockedPackageIds.includes(packageId)) {
                console.log(`   🚫 BLOCKED (Direct Match) - Package ID ${packageId} is booked`);
                
                option.disabled = true;
                option.style.color = '#999';
                option.style.textDecoration = 'line-through';
                option.style.backgroundColor = '#f8d7da';
                
                if (!option.textContent.includes('(Unavailable')) {
                    option.textContent += ' (Unavailable)';
                }
                
                if (option.selected) {
                    packageSelect.value = "";
                }
                return;
            }

            // Check for time conflicts with existing bookings
            if (durationHours) {
                const packageTimeInfo = extractTimeFromDuration(durationHours);
                
                if (packageTimeInfo) {
                    console.log(`   Package time range:`, {
                        start: packageTimeInfo.displayStart,
                        end: packageTimeInfo.displayEnd
                    });

                    let hasConflict = false;
                    let conflictDetails = null;

                    for (let booking of existingBookings) {
                        if (!booking.timeInfo) continue;

                        console.log(`   Comparing with booking:`, {
                            type: booking.reservation_type,
                            itemType: booking.item_type,
                            name: booking.item_name,
                            time: `${booking.timeInfo.displayStart} - ${booking.timeInfo.displayEnd}`
                        });

                        if (timeRangesOverlap(
                            packageTimeInfo.startMinutes,
                            packageTimeInfo.endMinutes,
                            booking.timeInfo.startMinutes,
                            booking.timeInfo.endMinutes
                        )) {
                            hasConflict = true;
                            conflictDetails = booking;
                            console.log(`   ❌ TIME CONFLICT DETECTED!`);
                            break;
                        } else {
                            console.log(`   ✅ No overlap`);
                        }
                    }

                    if (hasConflict) {
                        console.log(`🏖️ [RESORT BLOCKING] 🚫 BLOCKING: ${packageName}`);
                        
                        option.disabled = true;
                        option.style.color = '#999';
                        option.style.textDecoration = 'line-through';
                        option.style.backgroundColor = '#f8d7da';
                        
                        const conflictType = conflictDetails.reservation_type === 'Event Package' 
                            ? 'Event Package' 
                            : 'Resort';
                        
                        if (!option.textContent.includes('(Unavailable')) {
                            option.textContent += ` (Unavailable)`;
                        }
                        
                        if (option.selected) {
                            packageSelect.value = "";
                        }
                    } else {
                        // Unblock if no conflict
                        option.disabled = false;
                        option.style.color = '';
                        option.style.textDecoration = '';
                        option.style.backgroundColor = '';
                        
                        const cleanText = option.textContent.replace(/\s*\(Unavailable[^)]*\)/g, '');
                        if (option.textContent !== cleanText) {
                            option.textContent = cleanText;
                        }
                        
                        console.log(`🏖️ [RESORT BLOCKING] ✅ AVAILABLE: ${packageName}`);
                    }
                }
            }
        });

        console.log("🏖️ [RESORT BLOCKING] === Package blocking complete ===\n");
    }

    // Block conflicting durations
    function blockConflictingDurations() {
        const durationSelect = document.getElementById("duration");
        if (!durationSelect) {
            console.log("🏖️ [RESORT BLOCKING] ⚠️ Duration select not found");
            return;
        }

        console.log("\n🏖️ [RESORT BLOCKING] 🔍 Checking duration conflicts...");
        console.log("   Existing bookings:", existingBookings.length);
        console.log("   Blocked duration texts:", blockedDurationTexts);

        const durationOptions = durationSelect.querySelectorAll("option");
        
        durationOptions.forEach(option => {
            if (!option.value || option.value === "" || option.value === "package_duration") return;

            const durationText = option.textContent;
            
            // Extract the time portion (e.g., "8AM-5PM" from "Day Use (8AM-5PM) - ₱6,000")
            const hoursMatch = durationText.match(/\(([^)]+)\)/);
            const durationHours = hoursMatch ? hoursMatch[1] : null;

            console.log(`\n🏖️ [RESORT BLOCKING] Checking duration:`, {
                text: durationText,
                hours: durationHours
            });

            // Check if this duration text is explicitly blocked
            if (durationHours && blockedDurationTexts.includes(durationHours)) {
                console.log(`   🚫 BLOCKED (Direct Match) - Duration "${durationHours}" is booked`);
                
                option.disabled = true;
                option.style.color = '#999';
                option.style.textDecoration = 'line-through';
                option.style.backgroundColor = '#f8d7da';
                
                if (!option.textContent.includes('(Unavailable')) {
                    option.textContent += ' (Unavailable)';
                }
                
                if (option.selected) {
                    durationSelect.value = "";
                }
                return;
            }

            // Check for time conflicts with existing bookings
            if (durationHours) {
                const durationTimeInfo = extractTimeFromDuration(durationHours);
                
                if (durationTimeInfo) {
                    console.log(`   Duration time range:`, {
                        start: durationTimeInfo.displayStart,
                        end: durationTimeInfo.displayEnd
                    });

                    let hasConflict = false;
                    let conflictDetails = null;

                    for (let booking of existingBookings) {
                        if (!booking.timeInfo) continue;

                        console.log(`   Comparing with booking:`, {
                            type: booking.reservation_type,
                            itemType: booking.item_type,
                            name: booking.item_name,
                            time: `${booking.timeInfo.displayStart} - ${booking.timeInfo.displayEnd}`
                        });

                        if (timeRangesOverlap(
                            durationTimeInfo.startMinutes,
                            durationTimeInfo.endMinutes,
                            booking.timeInfo.startMinutes,
                            booking.timeInfo.endMinutes
                        )) {
                            hasConflict = true;
                            conflictDetails = booking;
                            console.log(`   ❌ TIME CONFLICT DETECTED!`);
                            break;
                        } else {
                            console.log(`   ✅ No overlap`);
                        }
                    }

                    if (hasConflict) {
                        console.log(`🏖️ [RESORT BLOCKING] 🚫 BLOCKING: ${durationText}`);
                        
                        option.disabled = true;
                        option.style.color = '#999';
                        option.style.textDecoration = 'line-through';
                        option.style.backgroundColor = '#f8d7da';
                        
                        const conflictType = conflictDetails.reservation_type === 'Event Package' 
                            ? 'Event Package' 
                            : 'Resort';
                        
                        if (!option.textContent.includes('(Unavailable')) {
                            option.textContent += ` (Unavailable)`;
                        }
                        
                        if (option.selected) {
                            durationSelect.value = "";
                        }
                    } else {
                        // Unblock if no conflict
                        option.disabled = false;
                        option.style.color = '';
                        option.style.textDecoration = '';
                        option.style.backgroundColor = '';
                        
                        const cleanText = option.textContent.replace(/\s*\(Unavailable[^)]*\)/g, '');
                        if (option.textContent !== cleanText) {
                            option.textContent = cleanText;
                        }
                        
                        console.log(`🏖️ [RESORT BLOCKING] ✅ AVAILABLE: ${durationText}`);
                    }
                }
            }
        });

        console.log("🏖️ [RESORT BLOCKING] === Duration blocking complete ===\n");
    }

    // Main blocking function
    function applyResortBlocking() {
        blockConflictingPackages();
        blockConflictingDurations();
    }

    // Initialize when date changes
    document.addEventListener('DOMContentLoaded', function() {
        const checkinInput = document.getElementById("checkin");
        const packageSelect = document.getElementById("resort_package_selection_package");
        const durationSelect = document.getElementById("duration");

        if (!checkinInput) {
            console.log("🏖️ [RESORT BLOCKING] ⚠️ Check-in input not found");
            return;
        }

        // When date changes
        checkinInput.addEventListener("change", function() {
            const selectedDate = this.value;
            if (!selectedDate) {
                console.log("🏖️ [RESORT BLOCKING] ⚠️ No date selected");
                existingBookings = [];
                blockedPackageIds = [];
                blockedDurationTexts = [];
                currentDate = null;
                return;
            }

            currentDate = selectedDate;
            console.log("\n🏖️ [RESORT BLOCKING] 📅 Date changed:", selectedDate);

            // Fetch bookings and apply blocking
            fetchResortBookings(selectedDate).then(result => {
                existingBookings = result.bookings;
                blockedPackageIds = result.blockedPackages;
                blockedDurationTexts = result.blockedDurations;

                console.log("🏖️ [RESORT BLOCKING] 📊 Summary:", {
                    processedBookings: existingBookings.length,
                    blockedPackages: blockedPackageIds.length,
                    blockedDurations: blockedDurationTexts.length
                });

                // Wait for dropdowns to be populated, then apply blocking
                setTimeout(() => {
                    applyResortBlocking();
                }, 500);

                // Also recheck after a longer delay (in case of slow loading)
                setTimeout(() => {
                    applyResortBlocking();
                }, 1500);
            });
        });

        // Monitor dropdown changes to reapply blocking
        if (packageSelect) {
            const packageObserver = new MutationObserver(function() {
                if (currentDate && existingBookings.length > 0) {
                    console.log("🏖️ [RESORT BLOCKING] 🔄 Package dropdown updated, rechecking...");
                    blockConflictingPackages();
                }
            });
            
            packageObserver.observe(packageSelect, {
                childList: true,
                subtree: false
            });
        }

        if (durationSelect) {
            const durationObserver = new MutationObserver(function() {
                if (currentDate && existingBookings.length > 0) {
                    console.log("🏖️ [RESORT BLOCKING] 🔄 Duration dropdown updated, rechecking...");
                    blockConflictingDurations();
                }
            });
            
            durationObserver.observe(durationSelect, {
                childList: true,
                subtree: false
            });
        }

        console.log("🏖️ [RESORT BLOCKING] ✅ System ready - monitoring date changes");
    });
})();
</script>

<script>
// ===== RESORT ROOM SELECTION DISPLAY SYSTEM =====
// Updates the button text to show selected rooms for resort bookings

document.addEventListener('DOMContentLoaded', function() {
  // Wait for room checkboxes to be loaded
  setTimeout(function() {
    const roomCheckboxes = document.querySelectorAll('#dropdown-content-rooms input[type="checkbox"]');
    const dropdownBtn = document.getElementById('dropdownBtn');
    
    if (!dropdownBtn) {
      console.error('[RESORT ROOMS DISPLAY] Dropdown button not found');
      return;
    }
    
    console.log('[RESORT ROOMS DISPLAY] ✓ Initialized with', roomCheckboxes.length, 'rooms');
    
    // Function to update button text based on selected rooms
    function updateRoomSelectionDisplay() {
      const selectedRooms = Array.from(document.querySelectorAll('#dropdown-content-rooms input[type="checkbox"]:checked'))
        .map(cb => cb.value);
      
      if (selectedRooms.length === 0) {
        dropdownBtn.textContent = 'Select Rooms (Optional)';
        console.log('[RESORT ROOMS DISPLAY] No rooms selected - showing default text');
      } else if (selectedRooms.length === 1) {
        dropdownBtn.textContent = selectedRooms[0];
        console.log('[RESORT ROOMS DISPLAY] ✓ 1 room selected:', selectedRooms[0]);
      } else {
        // Show all selected rooms separated by comma
        dropdownBtn.textContent = selectedRooms.join(', ');
        console.log('[RESORT ROOMS DISPLAY] ✓', selectedRooms.length, 'rooms selected:', selectedRooms.join(', '));
      }
    }
    
    // Add event listeners to all room checkboxes
    roomCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        updateRoomSelectionDisplay();
        console.log('[RESORT ROOMS DISPLAY] Room checkbox changed:', this.value, 'Checked:', this.checked);
      });
    });
    
    // Also update when date changes (rooms might get auto-deselected due to blocking)
    const checkinInput = document.getElementById('checkin');
    if (checkinInput) {
      checkinInput.addEventListener('change', function() {
        // Small delay to let blocking script run first
        setTimeout(updateRoomSelectionDisplay, 600);
        console.log('[RESORT ROOMS DISPLAY] Date changed - will update display after blocking check');
      });
    }
    
    // Also update when package or duration changes (rooms might get auto-deselected)
    const packageSelect = document.getElementById('resort_package_selection_package');
    if (packageSelect) {
      packageSelect.addEventListener('change', function() {
        setTimeout(updateRoomSelectionDisplay, 100);
        console.log('[RESORT ROOMS DISPLAY] Package changed - updating display');
      });
    }
    
    const durationSelect = document.getElementById('duration');
    if (durationSelect) {
      durationSelect.addEventListener('change', function() {
        setTimeout(updateRoomSelectionDisplay, 100);
        console.log('[RESORT ROOMS DISPLAY] Duration changed - updating display');
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
    
    // Monitor for dynamically added room checkboxes
    if (roomContainer) {
      const containerObserver = new MutationObserver(function() {
        const newCheckboxes = document.querySelectorAll('#dropdown-content-rooms input[type="checkbox"]');
        newCheckboxes.forEach(checkbox => {
          if (!checkbox.hasAttribute('data-listener-added')) {
            checkbox.setAttribute('data-listener-added', 'true');
            checkbox.addEventListener('change', function() {
              updateRoomSelectionDisplay();
              console.log('[RESORT ROOMS DISPLAY] New room checkbox changed:', this.value);
            });
          }
        });
      });
      
      containerObserver.observe(roomContainer, {
        childList: true,
        subtree: true
      });
    }
    
    console.log('[RESORT ROOMS DISPLAY] ✅ Resort Room Selection Display System Loaded');
  }, 800); // Wait for rooms to load
});
</script>


</body>
</html>
