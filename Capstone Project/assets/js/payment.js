$(document).ready(function() {
    // Handle payment method change
    $('input[name="paymentMethod"]').on('change', function() {
        if ($('#gcash').is(':checked')) {
            $('#gcashDetails').show();
            $('#bdoDetails').hide();
        } else if ($('#bdo').is(':checked')) {
            $('#gcashDetails').hide();
            $('#bdoDetails').show();
        }
    });

    // Initialize default view
    $('#gcashDetails').show();
    $('#bdoDetails').hide();
});