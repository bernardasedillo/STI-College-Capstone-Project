function showToastrConfirm(element, message, actionType = 'redirect', fetchOptions = {}) {
    let buttons = `<br /><button type='button' class='btn btn-info btn-sm' onclick='confirmAction(true, "${element.href || ''}", "${actionType}", ${JSON.stringify(fetchOptions)})'>Yes</button><button type='button' class='btn btn-default btn-sm' onclick='confirmAction(false)'>No</button>`;
    toastr.warning(buttons, message, {
        allowHtml: true,
        closeButton: true,
        timeOut: 0,
        extendedTimeOut: 0,
        positionClass: "toast-top-center",
        preventDuplicates: true,
        tapToDismiss: false,
        onShown: function() {
            $("#toast-container").css("width", "100%");
        }
    });
    return false;
}

function confirmAction(isConfirmed, url, actionType, options) {
    if (isConfirmed) {
        if (actionType === 'fetch') {
            fetch(url, options)
                .then(res => res.json())
                .then(() => location.reload());
        } else if (actionType === 'form_submit') {
            var form = $('<form action="archive_websiteContent.php" method="post"></form>');
            for (var key in options) {
                form.append('<input type="hidden" name="' + key + '" value="' + options[key] + '" />');
            }
            $("body").append(form);
            form.submit();
        } else {
            window.location.href = url;
        }
    }
    toastr.clear();
}
