//Here we will write down all the common js functions which will be used across the application
function validateRequiredFields(formSelector) {
    let invalidFields = [];
    let messages = [];

    // Clear old states & messages
    $(formSelector).find(".is-invalid").removeClass("is-invalid");
    $(formSelector).find(".required-error").remove();

    // Loop all required fields
    $(formSelector)
        .find("[required]")
        .each(function () {
            let field = $(this);
            let type = field.attr("type");
            let name = field.attr("name");
            let label =
                $('label[for="' + field.attr("id") + '"]')
                    .text()
                    .trim() || name;

            let isEmpty = false;

            if (type === "radio" || type === "checkbox") {
                if ($('input[name="' + name + '"]:checked').length === 0) {
                    isEmpty = true;
                }
            } else if (field.is("select")) {
                if (!field.val() || field.val() === "") {
                    isEmpty = true;
                }
            } else {
                if (!field.val().trim()) {
                    isEmpty = true;
                }
            }

            if (isEmpty) {
                invalidFields.push(field);
                messages.push(label);

                // Add red border
                field.addClass("is-invalid");

                // Add inline message
                field.after(
                    '<small class="text-danger required-error">This field is required</small>'
                );

                // For radio/checkbox, mark first input
                if (type === "radio" || type === "checkbox") {
                    $('input[name="' + name + '"]').first().addClass("is-invalid");
                }
            }
        });

    // If invalid fields found
    if (invalidFields.length > 0) {
        let msg = messages.join(", ");

        let firstInvalid = invalidFields[0];

        $("html, body").animate(
            {
                scrollTop: firstInvalid.offset().top - 100,
            },
            400
        );

        firstInvalid.focus();

        return false;
    }

    return true;
}
async function sendRequest(url, method = "POST", formSelector = null, extraData = {}) {
    try {
        let headers = {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        };

        let bodyData = null;

        // If a form is passed, convert to FormData
        if (formSelector) {
            let form = document.querySelector(formSelector);
            bodyData = new FormData(form);
        }

        // Add extra data if provided
        if (extraData && Object.keys(extraData).length > 0) {
            if (!bodyData) bodyData = new FormData();
            for (const key in extraData) {
                bodyData.append(key, extraData[key]);
            }
        }

        // Fetch Call
        let response = await fetch(url, {
            method: method,
            headers: headers,
            body: (method !== "GET" ? bodyData : null)
        });

        let result = await response.json();

        // Handle Validation (422)
        if (response.status === 422) {
            let messages = [];
            Object.values(result.errors).forEach(err => messages.push(err[0]));
            throw new Error(messages.join("\n"));
        }

        // Handle Other Errors
        if (!response.ok) {
            throw new Error(result.message || "Something went wrong.");
        }

        return result;

    } catch (error) {
        console.error("Fetch Error:", error);
        return null;
    }
}
function showAlert_({
    type = 'info',     // success | error | warning | info | validation
    message = '',
    messages = []
}) {
    return new Promise(resolve => {

        const config = {
            success:    { label: 'SUCCESS',    icon: 'bx-check-circle', bg: 'bg-success', text: 'text-success' },
            error:      { label: 'ERROR',      icon: 'bx-error',        bg: 'bg-danger',  text: 'text-danger' },
            warning:    { label: 'WARNING',    icon: 'bx-error-circle', bg: 'bg-warning', text: 'text-dark'  },
            info:       { label: 'INFO',       icon: 'bx-info-circle',  bg: 'bg-info',    text: 'text-primary' },
            validation: { label: 'VALIDATION', icon: 'bx-shield-x',     bg: 'bg-danger',  text: 'text-danger' }
        };

        if (!config[type]) type = 'info';

        const $modal  = $('#globalAlertModal');
        const modal   = new bootstrap.Modal($modal[0]);

        const $header = $('#globalAlertHeader');
        const $title  = $('#globalAlertTitle');

        const $icon   = $('#globalAlertIcon');
        const $type   = $('#globalAlertType');
        const $msg    = $('#globalAlertMessage');
        const $list   = $('#globalAlertList');

        const $okBtn      = $('#globalAlertOkBtn');
        const $cancelBtn  = $('#globalAlertCancelBtn');
        const $confirmBtn = $('#globalAlertConfirmBtn');

        /* ================= RESET ================= */
        $header.removeClass('bg-success bg-danger bg-warning bg-info');
        $title.removeClass('text-white text-dark text-success text-danger text-primary');

        $okBtn.addClass('d-none');
        $cancelBtn.addClass('d-none');
        $confirmBtn.addClass('d-none');

        $list.addClass('d-none').empty();

        /* ================= APPLY TYPE ================= */
        const { label, icon, bg, text } = config[type];

        $header.addClass(bg);
        $title.addClass(text).text(label);

        $icon
            .attr('class', '')
            .addClass(`bx ${icon} fs-1 ${text}`);

        $type
            .attr('class', '')
            .addClass(`fw-bold ${text}`)
            .text(label);

        $msg.text(message);

        if (messages.length) {
            $list.removeClass('d-none');
            $.each(messages, function (_, err) {
                $('<li>').text(err).appendTo($list);
            });
        }

        /* ================= ALERT MODE ================= */
        $okBtn.removeClass('d-none').off('click').on('click', function () {
            modal.hide();
        });

        $modal
            .off('hidden.bs.modal')
            .one('hidden.bs.modal', function () {
                resolve(true);
            });

        modal.show();
    });
}
function showAlert({
    type = 'info',     // success | error | warning | info | validation | confirm
    message = '',
    messages = []
}) {
    return new Promise(resolve => {

        let action = null; // <-- track user intent

        const config = {
            success:    { label: 'SUCCESS',    icon: 'bx-check-circle', bg: 'bg-success', text: 'text-success' },
            error:      { label: 'ERROR',      icon: 'bx-error',        bg: 'bg-danger',  text: 'text-danger' },
            warning:    { label: 'WARNING',    icon: 'bx-error-circle', bg: 'bg-warning', text: 'text-dark'  },
            info:       { label: 'INFO',       icon: 'bx-info-circle',  bg: 'bg-info',    text: 'text-primary' },
            validation: { label: 'VALIDATION', icon: 'bx-shield-x',     bg: 'bg-danger',  text: 'text-danger' },
            confirm:    { label: 'CONFIRM',    icon: 'bx-help-circle',  bg: 'bg-secondary', text: 'text-dark' }
        };

        if (!config[type]) type = 'info';

        const $modal  = $('#globalAlertModal');
        const modal   = new bootstrap.Modal($modal[0], {
            backdrop: 'static',
            keyboard: false
        });

        const $header = $('#globalAlertHeader');
        const $title  = $('#globalAlertTitle');
        const $icon   = $('#globalAlertIcon');
        const $type   = $('#globalAlertType');
        const $msg    = $('#globalAlertMessage');
        const $list   = $('#globalAlertList');

        const $okBtn      = $('#globalAlertOkBtn');
        const $cancelBtn  = $('#globalAlertCancelBtn');
        const $confirmBtn = $('#globalAlertConfirmBtn');

        /* ========= RESET ========= */
        $header.removeClass('bg-success bg-danger bg-warning bg-info bg-secondary');
        $title.removeClass('text-white text-dark text-success text-danger text-primary');

        $okBtn.addClass('d-none').off('click');
        $cancelBtn.addClass('d-none').off('click');
        $confirmBtn.addClass('d-none').off('click');

        $list.addClass('d-none').empty();

        /* ========= APPLY TYPE ========= */
        const { label, icon, bg, text } = config[type];

        $header.addClass(bg);
        $title.text(label);

        $icon.attr('class', `bx ${icon} fs-1 ${text}`);
        $type.attr('class', `fw-bold ${text}`).text(label);
        $msg.text(message);

        if (messages.length) {
            $list.removeClass('d-none');
            messages.forEach(m => $('<li>').text(m).appendTo($list));
        }

        /* ========= MODES ========= */
        if (type === 'warning') {

            $cancelBtn.removeClass('d-none').on('click', () => {
                action = false;
                modal.hide();
            });

            $confirmBtn.removeClass('d-none').on('click', () => {
                action = true;
                modal.hide();
            });

        } else {

            $okBtn.removeClass('d-none').on('click', () => {
                action = true;
                modal.hide();
            });
        }

        /* ========= FINAL RESOLVE ========= */
        $modal
            .off('hidden.bs.modal')
            .one('hidden.bs.modal', () => {
                resolve(action);   // <-- ONLY resolves what user actually did
            });

        modal.show();
    });
}




