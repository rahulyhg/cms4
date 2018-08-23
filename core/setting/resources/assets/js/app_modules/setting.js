$(document).ready(function () {
    $('input[data-key=email-config-status-btn]').on('change', function () {
        let _self = $(this);
        let key = _self.prop('id');
        let url = _self.data('change-url');

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                key: key,
                value: _self.prop('checked') ? 1 : 0
            },
            success: function (res) {
                if (!res.error) {
                    Botble.showNotice('success', res.message);
                } else {
                    Botble.showNotice('error', res.message);
                }
            },
            error: function (res) {
                Botble.handleError(res);
            }
        });
    });

    $(document).on('change', '#email_driver', function () {
        if ($(this).val() === 'mailgun') {
            $('.setting-mail-password').addClass('hidden');
            $('.setting-mail-mail-gun').removeClass('hidden');
        } else {
            $('.setting-mail-password').removeClass('hidden');
            $('.setting-mail-mail-gun').addClass('hidden');
        }
    });
    
    $('#send-test-email-btn').on('click', function (event) {
        event.preventDefault();
        let _self = $(this);
        let url = _self.data('send-url');

        _self.addClass('button-loading');

        $.ajax({
            type: 'POST',
            url: url,
            success: function (res) {
                if (!res.error) {
                    Botble.showNotice('success', res.message);
                } else {
                    Botble.showNotice('error', res.message);
                }
                _self.removeClass('button-loading');
            },
            error: function (res) {
                Botble.handleError(res);
                _self.removeClass('button-loading');
            }
        });
    });

    if (typeof CodeMirror !== 'undefined') {
        Botble.initCodeEditor('mail-template-editor');
    }

    $(document).on('click', '.btn-trigger-reset-to-default', function (event) {
        event.preventDefault();
        $('#reset-template-to-default-button').data('target', $(this).data('target'));
        $('#reset-template-to-default-modal').modal('show');
    });

    $(document).on('click', '#reset-template-to-default-button', function (event) {
        event.preventDefault();
        var _self = $(this);

        _self.addClass('button-loading');

        $.ajax({
            type: 'POST',
            cache: false,
            url: _self.data('target'),
            data: {
                email_subject_key: $('input[name=email_subject_key]').val(),
                template_path: $('input[name=template_path]').val(),
            },
            success: function (res) {
                if (!res.error) {
                    Botble.showNotice('success', res.message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                } else {
                    Botble.showNotice('error', res.message);
                }
                _self.removeClass('button-loading');
                $('#reset-template-to-default-modal').modal('hide');
            },
            error: function (res) {
                Botble.handleError(res);
                _self.removeClass('button-loading');
            }
        });
    });
});