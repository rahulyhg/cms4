(function ($, DataTable) {
    'use strict';

    DataTable.ext.buttons.reload = {
        className: 'buttons-reload',

        text: function (dt) {
            return '<i class="fa fa-refresh"></i> ' + dt.i18n('buttons.reload', Botble.languages.tables.reload);
        },

        action: function (e, dt) {

            dt.draw(false);
        }
    };

    var BTable = BTable || {};

    BTable.init = function () {
        if (typeof window.LaravelDataTables !== 'undefined') {

            $('.group-checkable').uniform();

            $(document).on('change', '.group-checkable', function () {
                let set = $(this).attr('data-set');
                let checked = $(this).prop('checked');
                $(set).each(function () {
                    if (checked) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
                $.uniform.update(set);
                $(this).uniform();
            });

            $(document).on('change', '.checkboxes', function () {
                let table = $(this).closest('.table-wrapper').find('.table').prop('id');

                let ids = [];
                let $table = $('#' + table);
                $table.find('.checkboxes:checked').each(function (i) {
                    ids[i] = $(this).val();
                });

                if (ids.length !== $table.find('.checkboxes').length) {
                    $(this).closest('.table-wrapper').find('.group-checkable').prop('checked', false).uniform();
                } else {
                    $(this).closest('.table-wrapper').find('.group-checkable').prop('checked', true).uniform();
                }
            });
        }
    };

    BTable.handleActionsRow = function () {
        $(document).ready(function () {

            $(document).on('click', 'table.dataTable .action-delete', function (e) {
                e.preventDefault();
                $('.modal-confirm-delete button[type=submit]').data({
                    href: $(this).prop('href'),
                    'table-name': $(this).closest('table').attr('id'),
                    class: $(this).data('class')
                });
                $('.modal-confirm-delete').modal('show');
            });

            $(document).on('click', '.modal-confirm-delete button[type=submit]', function (e) {
                e.preventDefault();
                let url = $(this).data('href');
                let table_name = $(this).data('table-name');

                if (!url) {
                    return;
                }
                $.ajax({
                    url: url,
                    type: 'delete',
                    dataType: 'json',
                    data: {
                        class: $(this).data('class')
                    },
                    success: function success(response) {
                        if (!response.error) {
                            Botble.showNotice('success', response.message);
                            $('.modal-confirm-delete').modal('hide');
                            window.LaravelDataTables[table_name].draw(false);
                        }
                    },
                    error: function error(response) {
                        if (response.status === 422) {
                            Botble.showNotice('error', 'Cannot delete');
                        }
                    }
                });
            });

            $(document).on('click', '.delete-many-entry-trigger', function (event) {
                event.preventDefault();
                let table = $(this).closest('.table-wrapper').find('.table').prop('id');

                let ids = [];
                $('#' + table).find('.checkboxes:checked').each(function (i) {
                    ids[i] = $(this).val();
                });

                if (ids.length === 0) {
                    Botble.showNotice('error', 'Please select at least one record to perform this action!');
                    return false;
                }

                $('.delete-many-entry-button')
                    .data('href', $(this).prop('href'))
                    .data('parent-table', table)
                    .data('class', $(this).data('class'));
                $('.delete-many-modal').modal('show');
            });

            $('.delete-many-entry-button').on('click', function (event) {
                event.preventDefault();
                $('.delete-many-modal').modal('hide');

                let _self = $(this);

                let $table = $('#' + _self.data('parent-table'));

                let ids = [];
                $table.find('.checkboxes:checked').each(function (i) {
                    ids[i] = $(this).val();
                });

                $.ajax({
                    url: $(this).data('href'),
                    type: 'POST',
                    data: {
                        ids: ids,
                        class: _self.data('class')
                    },
                    success: function (data) {
                        if (data.error) {
                            Botble.showNotice('error', data.message);
                        } else {
                            $table.find('.group-checkable').prop('checked', false);
                            $.uniform.update($table.find('.group-checkable'));
                            window.LaravelDataTables[_self.data('parent-table')].draw();
                            Botble.showNotice('success', data.message);
                        }
                    },
                    error: function (data) {
                        Botble.handleError(data);
                    }
                });
            });

            $(document).on('click', '.bulk-change-item', function (event) {
                event.preventDefault();

                let table = $(this).closest('.table-wrapper').find('.table').prop('id');

                let ids = [];
                $('#' + table).find('.checkboxes:checked').each(function (i) {
                    ids[i] = $(this).val();
                });

                if (ids.length === 0) {
                    Botble.showNotice('error', 'Please select at least one record to perform this action!');
                    return false;
                }

                BTable.loadBulkChangeData($(this));

                $('.confirm-bulk-change-button')
                    .data('parent-table', table)
                    .data('class', $(this).data('class'))
                    .data('key', $(this).data('key'))
                    .data('url', $(this).data('save-url'));
                $('.modal-bulk-change-items').modal('show');
            });

            $(document).on('click', '.confirm-bulk-change-button', function (event) {
                event.preventDefault();
                let _self = $(this);
                let value = _self.closest('.modal').find('.input-value').val();
                let input_key = _self.data('key');

                let $table = $('#' + _self.data('parent-table'));

                let ids = [];
                $table.find('.checkboxes:checked').each(function (i) {
                    ids[i] = $(this).val();
                });

                let text = _self.text();
                _self.text('Processing...');

                $.ajax({
                    url: _self.data('url'),
                    type: 'POST',
                    data: {
                        ids: ids,
                        key: input_key,
                        value: value,
                        class: _self.data('class')
                    },
                    success: function (data) {
                        if (data.error) {
                            Botble.showNotice('error', data.message);
                        } else {
                            $table.find('.group-checkable').prop('checked', false);
                            $.uniform.update($table.find('.group-checkable'));
                            $.each(ids, function (index, item) {
                                window.LaravelDataTables[_self.data('parent-table')].row($table.find('.checkboxes[value="' + item + '"]').closest('tr')).remove().draw();
                            });
                            Botble.showNotice('success', data.message);

                            $('.modal-bulk-change-items').modal('hide');
                        }
                        _self.text(text);
                    },
                    error: function (data) {
                        Botble.handleError(data);
                        _self.text(text);
                        $('.modal-bulk-change-items').modal('hide');
                    }
                });

            });
        });
    };

    BTable.loadBulkChangeData = function ($element) {
        let $modal = $('.modal-bulk-change-items');
        $.ajax({
            type: 'GET',
            url: $modal.find('.confirm-bulk-change-button').data('load-url'),
            data: {
                'class': $element.data('class'),
                'key': $element.data('key'),
            },
            success: function (res) {
                let data = $.map(res.data, function (value, key) {
                    return {id: key, name: value};
                });
                let $parent = $('.modal-bulk-change-content');
                $parent.html(res.html);

                let $input = $modal.find('input[type=text].input-value');
                if ($input.length) {
                    $input.typeahead({source: data});
                    $input.data('typeahead').source = data;
                }

                Botble.initResources();

                $parent.find('.datetimepicker').datetimepicker({
                    format: 'YYYY/MM/DD',
                });
            },
            error: function (error) {
                Botble.handleError(error);
            }
        });
    };

    BTable.reload = function (id) {
        window.LaravelDataTables[id].draw(false);
    };

    BTable.handleActionsExport = function () {
        $(document).ready(function () {
            $(document).on('click', '.export-data', function (event) {
                let table = $(this).closest('.table-wrapper').find('.table').prop('id');

                let ids = [];
                $('#' + table).find('.checkboxes:checked').each(function (i) {
                    ids[i] = $(this).val();
                });

                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: $(this).prop('href'),
                    data: {
                        'ids-checked': ids,
                    },
                    success: function (response) {
                        let a = document.createElement('a');
                        a.href = response.file;
                        a.download = response.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                    },
                    error: function (error) {
                        Botble.handleError(error);
                    }
                });
            });

        });
    };

    $(document).ready(function () {
        BTable.init();
        BTable.handleActionsRow();
        BTable.handleActionsExport();

        $(document).on('click', '.btn-show-table-options', function (event) {
            event.preventDefault();
            $(this).closest('.table-wrapper').find('.table-configuration-wrap').slideToggle(500);
        });
    });

})(jQuery, jQuery.fn.dataTable);
