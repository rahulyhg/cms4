var BFilter = BFilter || {};

BFilter.loadData = function ($element) {
    $.ajax({
        type: 'GET',
        url: $('.filter-data-url').val(),
        data: {
            'class': $('.filter-data-class').val(),
            'key': $element.val(),
            'value': $element.closest('.filter-item').find('.filter-column-value').val(),
        },
        success: function (res) {
            var data = $.map(res.data, function (value, key) {
                return {id: key, name: value};
            });
            $element.closest('.filter-item').find('.filter-column-value-wrap').html(res.html);

            var $input = $element.closest('.filter-item').find('.filter-column-value');
            if ($input.length) {
                $input.typeahead({source: data});
                $input.data('typeahead').source = data;
            }

            Botble.initResources();

            $(document).find('.datetimepicker').datetimepicker({
                format: 'YYYY/MM/DD',
            });
        },
        error: function (error) {
            Botble.handleError(error);
        }
    });
};

$(document).ready(function () {
    $.each($('.filter-items-wrap .filter-column-key'), function (index, element) {
        if ($(element).val()) {
            BFilter.loadData($(element));
        }
    });

    $(document).on('change', '.filter-column-key', function () {
        BFilter.loadData($(this));
    });

    $(document).on('click', '.btn-reset-filter-item', function (event) {
        event.preventDefault();
        $(this).closest('.filter-item').find('.filter-column-key').val('').trigger('change');
        $(this).closest('.filter-item').find('.filter-column-operator').val('=');
        $(this).closest('.filter-item').find('.filter-column-value').val('');
    });

    $(document).on('click', '.add-more-filter', function () {
        var $template = $(document).find('.sample-filter-item-wrap');
        $template.find('.filter-column-key').addClass('select-full');
        $template.find('.filter-operator').addClass('select-full');
        var html = $template.html();
        $template.find('.filter-column-key').removeClass('select-full');
        $template.find('.filter-operator').removeClass('select-full');

        $(document).find('.filter-items-wrap').append(html);
        Botble.initResources();

        var element = $(document).find('.filter-items-wrap .filter-item:last-child').find('.filter-column-key');
        if ($(element).val()) {
            BFilter.loadData(element);
        }
    });

    $(document).on('click', '.btn-remove-filter-item', function (event) {
        event.preventDefault();
        $(this).closest('.filter-item').remove();
    });
});