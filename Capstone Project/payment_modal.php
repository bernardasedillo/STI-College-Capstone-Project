<div id="paymentModal" class="modal-overlay">
    <div class="modal-content">
        <form id="payment-form" action="submit_reservation.php" method="POST" enctype="multipart/form-data">
            <h3>Online Payment</h3>
            <div class="payment-details">
                <p><strong>Total Amount:</strong> ₱ <span id="payment-total-amount">0</span></p>
                <p><strong>50% Down Payment (due immediately):</strong> <span id="payment-down-payment">0</span></p>
                <p><strong>Remaining Balance: after initial payment:</strong> <span id="payment-balance">0</span></p>
            </div>
            <div class="payment-methods">
                <h4>Choose a Payment Method:</h4>
                <label>
                    <input type="radio" name="payment_method" value="GCash" checked> GCash
                </label>
                <label>
                    <input type="radio" name="payment_method" value="BDO"> BDO QR Code
                </label>
            </div>
            <div id="qr-code-container">
                <img id="qr-code-image" src="" alt="QR Code">
            </div>
            <div class="reservation-details-overview">
                <h4>Reservation Details</h4>
                <div id="overview-details"></div>
            </div>
            <div class="proof-of-payment">
                <h4>Proof of Payment</h4>
                <input type="file" name="proof_of_payment" id="proof_of_payment" accept="image/*">
                <div id="proof-of-payment-error"></div>
            </div>
            <div class="modal-buttons">
                <button type="button" onclick="confirmPayment()">Submit</button>
                <button type="button" onclick="closePaymentModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: none; /* Initially hidden */
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background: #fefefe;
        margin: auto; /* Centering */
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
        border-radius: 10px;
        max-height: 90vh;
        overflow-y: auto;
    }

    #payment-form h3 {
        text-align: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .payment-details {
        margin-bottom: 20px;
    }

    .payment-methods {
        margin-bottom: 20px;
    }

    .payment-methods h4 {
        margin-bottom: 10px;
    }

    .payment-methods label {
        display: block;
        margin-bottom: 10px;
    }

    #qr-code-container {
        text-align: center;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    #qr-code-image {
        max-width: 200px;
        border: 1px solid #ddd;
        padding: 5px;
    }

    .reservation-details-overview {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .reservation-details-overview h4 {
        margin-top: 0;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }

    .proof-of-payment {
        margin-bottom: 20px;
    }

    .proof-of-payment h4 {
        margin-bottom: 10px;
    }

    #proof_of_payment {
        display: block;
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    #proof-of-payment-error {
        color: red;
        display: none;
        font-size: 13px;
        margin-top: 5px;
    }

    .modal-buttons {
        text-align: right;
    }

    .modal-buttons button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 10px;
    }

    .modal-buttons button:first-of-type {
        background-color: #4CAF50;
        color: white;
    }

    .modal-buttons button:last-of-type {
        background-color: #f44336;
        color: white;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .modal-content {
            width: 90%;
        }

        .modal-buttons {
            text-align: center;
            display: flex;
            flex-direction: column-reverse;
        }

        .modal-buttons button {
            width: 100%;
            margin: 0 0 10px 0;
        }
    }

    @media (max-width: 480px) {
        .modal-content {
            padding: 15px;
        }

        #payment-form h3 {
            font-size: 1.2em;
        }

        .payment-details p {
            font-size: 0.9em;
        }
    }
</style>

<script>
    function openPaymentModal() {
        const totalAmount = parseFloat(document.getElementById('total_amount').value);
        const downPayment = totalAmount * 0.5;
        const balance = totalAmount - downPayment;

        document.getElementById('payment-total-amount').textContent = totalAmount.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
        document.getElementById('payment-down-payment').textContent = downPayment.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
        document.getElementById('payment-balance').textContent = balance.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });

        const overviewDetails = document.getElementById('overview-details');
        const reservationForm = document.querySelector('.reservation-form');
        const paymentForm = document.getElementById('payment-form');
        const formData = new FormData(reservationForm);
        let detailsHtml = '<ul>';

        const fieldOrder = [
            'reservation_type', 'full_name', 'email', 'phone', 'checkin',
            'events_package_selection', 'time', 'hours', 'minutes', 'guests',
            'event_type', 'room_number[]', 'check_in_time', 'additional_fee[]'
        ];

        const labelMap = {
            reservation_type: 'Reservation Type',
            full_name: 'Full Name',
            email: 'Email Address',
            phone: 'Phone Number',
            checkin: 'Check In Date',
            events_package_selection: 'Package',
            time: 'Check In Time',
            hours: 'Hours',
            minutes: 'Minutes',
            guests: 'Number of Guests',
            event_type: 'Event Type',
            'room_number[]': 'Room Number',
            check_in_time: 'Room Duration',
            'additional_fee[]': 'Additional Fee'
        };

        const existingHiddenInputs = paymentForm.querySelectorAll('input[type="hidden"]');
        existingHiddenInputs.forEach(input => input.remove());

        fieldOrder.forEach(key => {
            if (!formData.has(key)) return;

            let displayValue;
            const value = formData.get(key);
            if (!value && !key.endsWith('[]')) return;

            const displayKey = labelMap[key] || key.replace(/_/g, ' ').replace(/\[\]/g, '');

            if (key.endsWith('[]')) {
                const values = formData.getAll(key);
                if (values.length === 0) return;
                const labels = values.map(val => {
                    const input = reservationForm.querySelector(`[name="${key}"][value="${val}"]`);
                    if (input && input.labels.length > 0) {
                        return input.labels[0].textContent;
                    }
                    const feeCheckbox = reservationForm.querySelector(`input[type="checkbox"][value="${val}"]`);
                    if (feeCheckbox) {
                        return feeCheckbox.parentElement.textContent.trim();
                    }
                    return val;
                });
                displayValue = labels.join(', ');
            } else {
                const element = reservationForm.elements[key];
                if (element && element.tagName === 'SELECT') {
                    const selectedOption = element.options[element.selectedIndex];
                    displayValue = selectedOption ? selectedOption.textContent : value;
                } else {
                    displayValue = value;
                }
            }

            if (displayValue) {
                detailsHtml += `<li><strong>${displayKey}:</strong> ${displayValue}</li>`;
            }
        });

        detailsHtml += `<li><strong>Total Amount:</strong> ${totalAmount.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</li>`;
        detailsHtml += '</ul>';
        overviewDetails.innerHTML = detailsHtml;

        for (const [key, value] of formData.entries()) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = key;
            hiddenInput.value = value;
            paymentForm.appendChild(hiddenInput);
        }
        const totalAmountInput = document.createElement('input');
        totalAmountInput.type = 'hidden';
        totalAmountInput.name = 'total_amount';
        totalAmountInput.value = totalAmount;
        paymentForm.appendChild(totalAmountInput);

        document.getElementById('paymentModal').style.display = 'flex';
        updateQRCode();
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
    }

    function confirmPayment() {
        const form = document.getElementById('payment-form');
        const proofOfPayment = document.getElementById('proof_of_payment').files[0];
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');

        const errorDiv = document.getElementById('proof-of-payment-error');
        let hasError = false;
        let errorMessage = '';

        // Check payment method
        if (!selectedPaymentMethod) {
            errorMessage += 'Please select a payment method.\n';
            hasError = true;
        }

        // Check proof of payment
        if (!proofOfPayment) {
            errorMessage += 'Please upload a proof of payment.';
            hasError = true;
        }

        if (hasError) {
            errorDiv.textContent = errorMessage;
            errorDiv.style.display = 'block';
            return;
        } else {
            errorDiv.style.display = 'none';
        }

        form.submit();
    }

    function updateQRCode() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const qrCodeImage = document.getElementById('qr-code-image');
        if (paymentMethod === 'GCash') {
            qrCodeImage.src = 'assets/images/Gcash.jpg';
        } else if (paymentMethod === 'BDO') {
            qrCodeImage.src = 'assets/images/BDO.jpg';
        }
    }

    document.getElementById('proof_of_payment').addEventListener('change', () => {
        document.getElementById('proof-of-payment-error').style.display = 'none';
    });

    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', updateQRCode);
    });
</script>
