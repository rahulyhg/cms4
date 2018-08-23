$(document).ready(function () {
    $(document).on('click', '.btn-clear-cache', function (event) {
        event.preventDefault();
        let _self = $(this);
        _self.html('<i class="fa fa-spin fa-spinner" aria-hidden="true"></i> ' + _self.text());

        $.ajax({
            url: _self.data('url'),
            type: 'POST',
            data: {
                type: _self.data('type'),
            },
            success: function (data) {
                _self.find('i').remove();

                if (data.error) {
                    Botble.showNotice('error', data.message);
                } else {
                    Botble.showNotice('success', data.message);
                }
            },
            error: function (data) {
                _self.find('i').remove();
                Botble.handleError(data);
            }
        });
    });
});