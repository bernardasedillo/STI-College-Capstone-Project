<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../includes/connect.php'; 
require_once 'log_activity.php';

// Fetch rest days
$restDays = [];
$restDaysQuery = $conn->query("SELECT date FROM rest_days");
if ($restDaysQuery) {
    while ($row = $restDaysQuery->fetch_assoc()) {
        $restDays[] = $row['date'];
    }
}
$restDaysJson = json_encode($restDays);

// Fetch packages
$rooms = $conn->query("SELECT * FROM prices WHERE venue='Room' AND is_archived=0");
$resorts = $conn->query("SELECT * FROM prices WHERE venue='Resort' AND is_archived=0");
$events = $conn->query("SELECT * FROM prices WHERE (venue LIKE '%Hall%' OR venue LIKE '%Pavilion%') AND is_archived=0");
?>
<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Admin Reservation</h4>
    </div>
    <div class="card-body">

        <form method="POST" id="reservationForm" action="process_admin_reservation.php">
            <input type="hidden" name="final_submit" value="1">

            <div class="form-group">
                <label>Reservation Type</label>
                <select name="reservation_type" id="reservation_type" class="form-control" required>
                    <option value="">-- Select Reservation Type --</option>
                    <option value="Room">Room</option>
                    <option value="Resort">Resort</option>
                    <option value="Event Package">Events Place</option>
                </select>
            </div>

            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <!-- ✅ Fixed Mobile Number Input -->
            <div class="form-group">
                <label>Mobile Number</label>
                <input 
                    type="text" 
                    name="phone" 
                    id="phone" 
                    class="form-control" 
                    maxlength="11" 
                    pattern="\d{11}" 
                    title="Please enter exactly 11 digits (numbers only, starting with 09)" 
                    required
                    inputmode="numeric"
                >
                <small class="form-text text-muted">Enter 11-digit mobile number (e.g., 09XXXXXXXXX)</small>
                <small id="phoneWarning" class="text-danger" style="display:none;">Number must be exactly 11 digits.</small>
            </div>

            <div class="form-group">
                <label>Full Address</label>
                <input type="text" name="full_address" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Check-in Date</label>
               <input type="date" name="checkin_date" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" required>
            </div>
            <div class="form-group">
                <label>Guests</label>
                <input type="number" name="guests" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Choose Package</label>
                <select name="package_id" id="package_id" class="form-control" required>
                    <option value="">-- Select Package --</option>
                    <optgroup label="Rooms" data-type="Room">
                        <?php while ($r = $rooms->fetch_assoc()): ?>
                            <option value="<?= $r['id']; ?>" data-price="<?= $r['price']; ?>">
                                <?= $r['name']; ?> - ₱<?= number_format($r['price'],2); ?> (<?= $r['duration_hours']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </optgroup>
                    <optgroup label="Resorts" data-type="Resort">
                        <?php while ($r = $resorts->fetch_assoc()): ?>
                            <option value="<?= $r['id']; ?>" data-price="<?= $r['price']; ?>">
                                <?= $r['name']; ?> - ₱<?= number_format($r['price'],2); ?> <?= $r['day_type'] ? "($r[day_type])" : ""; ?>
                            </option>
                        <?php endwhile; ?>
                    </optgroup>
                    <optgroup label="Events" data-type="Event Package">
                        <?php while ($r = $events->fetch_assoc()): ?>
                            <option value="<?= $r['id']; ?>" data-price="<?= $r['price']; ?>">
                                <?= $r['venue']; ?> - <?= $r['name']; ?> - ₱<?= number_format($r['price'],2); ?>
                            </option>
                        <?php endwhile; ?>
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <label>Down Payment (₱)</label>
                <input type="number" step="0.01" name="down_payment" id="down_payment" class="form-control" required>
                <small id="down_hint" class="form-text text-muted"></small>
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" class="form-control" required>
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash</option>
                    <option value="BDO">BDO</option>
                </select>
            </div>

            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">Book Reservation</button>

            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Confirm Reservation</h5>
                  </div>
                  <div class="modal-body">
                    <p><strong>Reservation Type:</strong> <span id="conf_reservation_type"></span></p>
                    <p><strong>Name:</strong> <span id="conf_name"></span></p>
                    <p><strong>Email:</strong> <span id="conf_email"></span></p>
                    <p><strong>Phone:</strong> <span id="conf_phone"></span></p>
                    <p><strong>Address:</strong> <span id="conf_address"></span></p>
                    <p><strong>Check-in Date:</strong> <span id="conf_checkin"></span></p>
                    <p><strong>Guests:</strong> <span id="conf_guests"></span></p>
                    <p><strong>Package:</strong> <span id="conf_package"></span></p>
                    <p><strong>Down Payment:</strong> ₱<span id="conf_down"></span></p>
                    <p><strong>Payment Method:</strong> <span id="conf_method"></span></p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm</button>
                  </div>
                </div>
              </div>
            </div>
        </form>

        <!-- Notification Modal -->
        <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notificationModalTitle"></h5>
                    </div>
                    <div class="modal-body" id="notificationModalBody">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

<script>
const restDays = <?php echo $restDaysJson; ?>;

document.addEventListener("DOMContentLoaded", function() {
    const checkinInput = document.querySelector("[name=checkin_date]");

    checkinInput.addEventListener('input', function() {
        const selectedDate = this.value;
        if (restDays.includes(selectedDate)) {
            toastr.warning('This date is a designated rest day and cannot be booked. Please choose another date.');
            this.value = ''; // Clear invalid date
        }
    });

    const reservationType = document.getElementById("reservation_type");
    const packageSelect = document.getElementById("package_id");
    const optgroups = packageSelect.querySelectorAll("optgroup");
    const downInput = document.getElementById("down_payment");
    const hint = document.getElementById("down_hint");

    function updatePackages() {
        let selectedType = reservationType.value;
        optgroups.forEach(group => {
            group.style.display = (group.dataset.type === selectedType) ? "block" : "none";
        });
        packageSelect.value = "";
        downInput.value = "";
        hint.textContent = "";
    }

    packageSelect.addEventListener("change", function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute("data-price");
        if (price) {
            const minDown = price * 0.5;
            downInput.min = minDown;
            hint.textContent = "Minimum down payment: ₱" + parseFloat(minDown).toLocaleString();
        } else {
            downInput.removeAttribute("min");
            hint.textContent = "";
        }
    });

    reservationType.addEventListener("change", updatePackages);
    updatePackages();

    // ✅ Phone number validation: digits only, max 11, auto-add "09"
    const phoneInput = document.getElementById("phone");
    const phoneWarning = document.getElementById("phoneWarning");

    phoneInput.addEventListener("input", function() {
        this.value = this.value.replace(/\D/g, '');
        if (this.value.startsWith("9") && !this.value.startsWith("09")) {
            this.value = "0" + this.value;
        }
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }

        // Show warning if less than 11 digits
        phoneWarning.style.display = (this.value.length > 0 && this.value.length < 11) ? 'block' : 'none';
    });

    // Fill modal before showing
    $('#confirmModal').on('show.bs.modal', function () {
        document.getElementById("conf_reservation_type").innerText = reservationType.value;
        document.getElementById("conf_name").innerText = document.querySelector("[name=full_name]").value;
        document.getElementById("conf_email").innerText = document.querySelector("[name=email]").value;
        document.getElementById("conf_phone").innerText = document.querySelector("[name=phone]").value;
        document.getElementById("conf_address").innerText = document.querySelector("[name=full_address]").value;
        document.getElementById("conf_checkin").innerText = document.querySelector("[name=checkin_date]").value;
        document.getElementById("conf_guests").innerText = document.querySelector("[name=guests]").value;
        document.getElementById("conf_package").innerText = packageSelect.options[packageSelect.selectedIndex].text;
        document.getElementById("conf_down").innerText = document.querySelector("[name=down_payment]").value;
        document.getElementById("conf_method").innerText = document.querySelector("[name=payment_method]").value;
    });

    <?php if(isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
    $(document).ready(function() {
        const modalTitle = document.getElementById('notificationModalTitle');
        const modalBody = document.getElementById('notificationModalBody');

        <?php if(isset($_SESSION['success'])): ?>
            modalTitle.innerHTML = 'Success';
            modalBody.innerHTML = '<?php echo $_SESSION['success']; ?>';
            <?php unset($_SESSION['success']); ?>
        <?php elseif(isset($_SESSION['error'])): ?>
            modalTitle.innerHTML = 'Error';
            modalBody.innerHTML = '<?php echo $_SESSION['error']; ?>';
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        $('#notificationModal').modal('show');
    });
    <?php endif; ?>
});
</script>
