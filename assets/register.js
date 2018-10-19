var $registerFormModal = $('#register-form-modal');
var $registerForm = $registerFormModal.find('#register-form');
var $registerFormErrorSummary = $registerForm.find('.error-summary ul');
var $registerFormFullname = $registerForm.find('#register-fullname');
var $registerFormEmail = $registerForm.find('#register-email');
var $registerFormOptin = $registerForm.find('#register-optin');

function resetRegisterForm() {
    $registerForm.trigger('reset');
    $registerFormErrorSummary.empty();
}

function resolveErrors(errors) {
    $registerFormErrorSummary.empty();
    var errorsHtml = '';
    $.each(errors, function (index1, error) {
        errorsHtml += ('<li>' + error + '</li>');
    });
    $registerFormErrorSummary.append(errorsHtml);
}

$(document).ready(function () {
    $(document).on('scroll', function (event) {
        if ($(document).scrollTop() > ($(document).innerHeight() / 2) && ($registerFormModal.data('shown') == false)) {
            $registerFormModal.modal('show');
        }
    });

    $registerForm.on('submit', function (event) {
        event.preventDefault();
        var data = $registerForm.serializeArray();

        var request = $.ajax({
            type: 'POST',
            url: $registerForm.attr('action'),
            dataType: 'json',
            data: $.param(data)
        });
        request.done(function (response, textStatus, jqXHR) {
            if (response.status === 'ok') {
                resetRegisterForm();
            } else {
                resolveErrors(response.errors);
            }
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown + ': ' + textStatus);
        });
    });

    $registerFormModal.on('show.bs.modal', function (event) {
        $registerFormModal.data('shown', true);
    });

    $registerFormModal.on('hidden.bs.modal', function (event) {
        resetRegisterForm();
    });
});