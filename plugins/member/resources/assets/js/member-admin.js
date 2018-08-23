$(document).ready(function () {
    $(document).on('click', '#is_change_password', function () {
        if ($(this).is(':checked')) {
            $('input[type=password]').closest('.form-group').removeClass('hidden').fadeIn();
        } else {
            $('input[type=password]').closest('.form-group').addClass('hidden').fadeOut();
        }
    });
});