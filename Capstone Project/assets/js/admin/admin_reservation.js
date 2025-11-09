document.addEventListener("DOMContentLoaded", function () {
    // === Fetch Rooms & Durations ===
    fetch("fetch_rooms.php")
        .then(res => res.json())
        .then(data => {
            // Populate durations
            const durationSelect = document.getElementById("duration_of_stay");
            data.durations.forEach(dur => {
                const option = document.createElement("option");
                option.value = dur;
                option.textContent = `${dur} hours`;
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

            // Add listeners for room selection
            const roomCheckboxes = document.querySelectorAll(".room-checkbox");
            roomCheckboxes.forEach(cb => {
                cb.addEventListener("change", () => {
                    validateRoomSelection();
                    calculatePrice();
                });
            });

            // Store prices globally
            window.roomPrices = data.roomPrices.reduce((acc, row) => {
                acc[`${row.name}_${row.duration_hours}`] = row.price;
                return acc;
            }, {});
        })
        .catch(err => console.error("Error fetching rooms:", err));

    // === Reservation Type Logic ===
    const reservationSelect = document.getElementById("reservation_type");
    reservationSelect.value = "Room"; // Default
    reservationSelect.dispatchEvent(new Event("change"));

    reservationSelect.addEventListener("change", function () {
        const value = this.value;

        // Redirect for Resort & Events
        if (value === "Resort") {
            window.location.href = "reservation-resort.php";
            return;
        } else if (value === "Event Package") {
            window.location.href = "reservation-events.php";
            return;
        }

        // Reset all fields (except type)
        document.querySelectorAll("input, select, textarea").forEach(el => {
            if (el.id !== "reservation_type") {
                if (el.type === "checkbox" || el.type === "radio") el.checked = false;
                else if (el.tagName.toLowerCase() === "select") el.selectedIndex = 0;
                else el.value = "";
            }
        });

        // Show/hide sections
        const commonFields = document.getElementById("common-date-time-guests");
        const roomSpecificFields = document.getElementById("room-specific-fields");
        const customerDetails = document.getElementById("customer-details");
        const finalSubmissionFields = document.getElementById("final-submission-fields");

        commonFields.style.display = "none";
        roomSpecificFields.style.display = "none";
        customerDetails.style.display = "none";
        finalSubmissionFields.style.display = "none";

        if (value === "Room") {
            commonFields.style.display = "grid";
            roomSpecificFields.style.display = "grid";
            customerDetails.style.display = "grid";
            finalSubmissionFields.style.display = "grid";
        }
    });

    // === Phone Number Validation ===
    const phoneInput = document.getElementById("phone");
    const phoneError = document.getElementById("phone-error");

    phoneInput.addEventListener("focus", () => {
        if (phoneInput.value === "") {
            phoneInput.value = "09";
        }
    });

    phoneInput.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && phoneInput.value === "09") {
            e.preventDefault();
        }
    });

    phoneInput.addEventListener("input", () => {
        phoneInput.value = phoneInput.value.replace(/\D/g, "");
        if (!phoneInput.value.startsWith("09")) {
            phoneInput.value = "09" + phoneInput.value.replace(/^0+/, "");
        }
        if (phoneInput.value.length > 11) {
            phoneInput.value = phoneInput.value.slice(0, 11);
        }
        if (phoneInput.value.length !== 11) {
            phoneError.textContent = "Mobile number must be exactly 11 digits.";
            phoneError.style.display = "inline";
            phoneInput.style.borderColor = "red";
        } else {
            phoneError.textContent = "";
            phoneError.style.display = "none";
            phoneInput.style.borderColor = "";
        }
    });

    // === Guest Validation ===
    document.getElementById("guests").addEventListener("input", function () {
        const error = document.getElementById("guest-error-msg");
        let value = parseInt(this.value, 10);
        if (value > 24) {
            error.textContent = "Maximum 24 guests is allowed";
            error.style.display = "inline";
            this.value = "";
            setTimeout(() => { error.style.display = "none"; }, 1500);
        } else {
            error.style.display = "none";
        }
        validateRoomSelection();
        calculatePrice();
    });

    // === Set Minimum Check-in Date ===
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("checkin").setAttribute("min", today);

    // === Form Validation ===
    const form = document.querySelector(".reservation-form");
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        let isValid = true;

        // Reset styles
        document.querySelectorAll(".form-group input, .form-group select, .form-group textarea").forEach(field => {
            field.style.borderColor = "";
            const existingError = field.parentElement.querySelector(".field-error");
            if (existingError) existingError.remove();
        });

        // Validate required visible fields
        const visibleFields = document.querySelectorAll(".reservation-form .form-group input:not([type='checkbox']), .reservation-form .form-group select, .reservation-form .form-group textarea");
        visibleFields.forEach(field => {
            const parent = field.closest(".form-group");
            const isHidden = window.getComputedStyle(parent).display === "none" || parent.offsetParent === null;
            if (!isHidden && field.value.trim() === "") {
                isValid = false;
                field.style.borderColor = "red";
                const errorMsg = document.createElement("div");
                errorMsg.classList.add("field-error");
                errorMsg.style.color = "red";
                errorMsg.style.fontSize = "13px";
                errorMsg.textContent = "This field is required.";
                parent.appendChild(errorMsg);
            }
        });

        // Mobile must be 11 digits
        const phoneValue = phoneInput.value.trim();
        if (!/^\d{11}$/.test(phoneValue)) {
            isValid = false;
            phoneInput.style.borderColor = "red";
            const phoneErrorMsg = document.createElement("div");
            phoneErrorMsg.classList.add("field-error");
            phoneErrorMsg.style.color = "red";
            phoneErrorMsg.style.fontSize = "13px";
            phoneErrorMsg.textContent = "Mobile number must be exactly 11 digits.";
            phoneInput.parentElement.appendChild(phoneErrorMsg);
        }

        if (isValid) {
            // ✅ Normally submit
            form.submit();
        } else {
            window.scrollTo({ top: 0, behavior: "smooth" });
        }
    });

    // Remove error styling on focus
    document.querySelectorAll(".reservation-form .form-group input, .reservation-form .form-group select, .reservation-form .form-group textarea").forEach(field => {
        field.addEventListener("focus", function () {
            this.style.borderColor = "";
            const errorMsg = this.parentElement.querySelector(".field-error");
            if (errorMsg) errorMsg.remove();
        });
    });
});

// === Helpers ===
function getRequiredRooms(guests) {
    if (guests <= 6) return 1;
    if (guests <= 12) return 2;
    if (guests <= 18) return 3;
    if (guests <= 24) return 4;
    return 0;
}

function validateRoomSelection() {
    const guestInput = document.getElementById("guests");
    const guestError = document.getElementById("room-warning-msg");
    const roomWarningOne = document.getElementById("room-warning-msg-one");

    const value = guestInput.value.trim();
    const guests = parseInt(value, 10) || 0;
    const roomCheckboxes = document.querySelectorAll(".room-checkbox");
    const checkedRooms = document.querySelectorAll(".room-checkbox:checked").length;

    guestError.style.display = "none";
    roomWarningOne.style.display = "none";
    roomCheckboxes.forEach(cb => cb.disabled = false);

    if (value === "" || guests === 0) {
        roomCheckboxes.forEach(cb => { cb.checked = false; cb.disabled = false; });
        return;
    }
    if (guests > 24) {
        guestError.textContent = "Maximum 24 guests is allowed";
        guestError.style.display = "block";
        roomCheckboxes.forEach(cb => cb.disabled = true);
        return;
    }

    const requiredRooms = getRequiredRooms(guests);

    if (requiredRooms === 1) {
        if (checkedRooms === 0) {
            roomWarningOne.textContent = "You must select one room.";
            roomWarningOne.style.display = "block";
        } else if (checkedRooms > 1) {
            guestError.textContent = "You can only select 1 room for this number of guests.";
            guestError.style.display = "block";
        }
        roomCheckboxes.forEach(cb => { if (!cb.checked) cb.disabled = true; });
    } else {
        if (checkedRooms >= requiredRooms) {
            roomCheckboxes.forEach(cb => { if (!cb.checked) cb.disabled = true; });
            if (checkedRooms > requiredRooms) {
                guestError.textContent = `You can only select ${requiredRooms} rooms for ${guests} guests.`;
                guestError.style.display = "block";
            }
        } else {
            guestError.textContent = `You must select exactly ${requiredRooms} rooms for ${guests} guests.`;
            guestError.style.display = "block";
        }
    }
}

function calculatePrice() {
    const duration = document.getElementById("duration_of_stay").value;
    const selectedRooms = Array.from(document.querySelectorAll(".room-checkbox:checked")).map(cb => cb.value);
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

function toggleDropdown() {
    const dropdown = document.getElementById("dropdown-content-rooms");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

// Close dropdown if clicking outside
document.addEventListener("click", function (event) {
    const dropdown = document.getElementById("dropdown-content-rooms");
    if (!event.target.closest(".custom-dropdown") && !event.target.closest("#dropdown-content-rooms")) {
        dropdown.style.display = "none";
    }
});
