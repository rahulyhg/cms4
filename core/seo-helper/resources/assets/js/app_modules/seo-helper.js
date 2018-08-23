var BSEO = BSEO || {};

BSEO.handleMetaBox = function () {
    $('.page-url-seo p').text($(document).find('#sample-permalink a').prop('href'));
    $(document).on('click', '.btn-trigger-show-seo-detail', function (event) {
        event.preventDefault();
        $('.seo-edit-section').toggleClass('hidden');
    });

    $(document).on('keyup', 'input[name=name]', function () {
        BSEO.updateSEOTitle($(this).val());
    });

    $(document).on('keyup', 'input[name=title]', function () {
        BSEO.updateSEOTitle($(this).val());
    });

    $(document).on('keyup', 'textarea[name=description]', function () {
        BSEO.updateSEODescription($(this).val());
    });

    $(document).on('keyup', '#seo_title', function () {
        if ($(this).val()) {
            $('.page-title-seo').text($(this).val());
            $('.default-seo-description').addClass('hidden');
            $('.existed-seo-meta').removeClass('hidden');
        } else {
            if ($('input[name=name]').val()) {
                $('.page-title-seo').text($('input[name=name]').val());
            } else {
                $('.page-title-seo').text($('input[name=title]').val());
            }
        }
    });

    $(document).on('keyup', '#seo_description', function () {
        if ($(this).val()) {
            $('.page-description-seo').text($(this).val());
        }  else {
            $('.page-title-seo').text($('textarea[name=description]').val());
        }
    });
};

BSEO.updateSEOTitle = function (value) {
    if (value) {
        if (!$('#seo_title').val()) {
            $('.page-title-seo').text(value);
        }
        $('.default-seo-description').addClass('hidden');
        $('.existed-seo-meta').removeClass('hidden');
    } else {
        $('.default-seo-description').removeClass('hidden');
        $('.existed-seo-meta').addClass('hidden');
    }
};

BSEO.updateSEODescription = function (value) {
    if (value) {
        if (!$('#seo_description').val()) {
            $('.page-description-seo').text(value);
        }
    }
};

$(document).ready(function () {
    BSEO.handleMetaBox();
});
