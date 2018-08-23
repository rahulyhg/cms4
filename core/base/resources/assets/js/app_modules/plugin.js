$(document).ready(function () {
    $('#plugin-list').on('click', '.btn-trigger-change-status', function (event) {
        event.preventDefault();
        var _self = $(this);
        _self.addClass('button-loading');

        $.ajax({
            url: Botble.routes.change_plugin_status + '?alias=' + _self.data('plugin'),
            type: 'GET',
            success: function (data) {
                if (data.error) {
                    Botble.showNotice('error', data.message);
                } else {
                    Botble.showNotice('success', data.message);
                    $('#plugin-list #app-' + _self.data('plugin')).load(window.location.href + ' #plugin-list #app-' + _self.data('plugin') + ' > *');
                    window.location.reload();
                }
                _self.removeClass('button-loading');
            },
            error: function (data) {
                Botble.handleError(data);
                _self.removeClass('button-loading');
            }
        });
    });
});