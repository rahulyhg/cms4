$(document).ready(function () {
    $('input[type=checkbox]').uniform();

    $('#auto-checkboxes li').tree({
        onCheck: {
            node: 'expand'
        },
        onUncheck: {
            node: 'expand'
        },
        dnd: false,
        selectable: false
    });

    $('#mainNode .checker').change(function () {
        var set = $(this).attr('data-set');
        var checked = $(this).is(':checked');
        $(set).each(function () {
            if (checked) {
                $(this).attr('checked', true);
            } else {
                $(this).attr('checked', false);
            }
        });
        $.uniform.update(set);
    });

});