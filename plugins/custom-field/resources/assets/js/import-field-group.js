export class Helpers {
    static jsonDecode(jsonString, defaultValue) {
        if (typeof jsonString === 'string') {
            let result;
            try {
                result = $.parseJSON(jsonString);
            } catch (err) {
                result = defaultValue;
            }
            return result;
        }
        return null;
    }
}

(function ($) {
    let $body = $('body');

    $body.on('click', 'form.import-field-group a.btn.btn-default.action-item:nth-child(2)', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let $form = $(this).closest('form');
        $form.find('input[type=file]').val('').trigger('click');
    });

    $body.on('change', 'form.import-field-group input[type=file]', function (event) {
        let $form = $(this).closest('form');
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.readAsText(file);
            reader.onload = function (e) {
                let json = Helpers.jsonDecode(e.target.result);
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: {
                        json_data: json,
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        Botble.blockUI();
                    },
                    success: function (res) {
                        Botble.showNotice((res.error ? 'error' : 'success'), res.messages, res.error ? Botble.languages.notices_msg.error : Botble.languages.notices_msg.success);
                        if (!res.error) {
                            if (window.LaravelDataTables['dataTableBuilder']) {
                                window.LaravelDataTables['dataTableBuilder'].draw();
                            }
                        }
                        Botble.unblockUI();
                    },
                    complete: function (data) {
                        Botble.unblockUI();
                    },
                    error: function (data) {
                        Botble.showNotice('error', 'Some error occurred');
                    }
                });
            };
        }
    });
}(jQuery));