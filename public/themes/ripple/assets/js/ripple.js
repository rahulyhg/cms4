var search_input = $('.search-input');
var super_search = $('.super-search');
var close_search = $('.close-search');
var search_result = $('.search-result');
var timeoutID = null;

var Ripple = {
    searchFunction: function (keyword) {
        clearTimeout(timeoutID);
        timeoutID = setTimeout(function () {
            super_search.removeClass('search-finished');
            search_result.fadeOut();
            $.ajax({
                type: 'GET',
                cache: false,
                url: '/api/search',
                data: {
                    'q': keyword
                },
                success: function (res) {
                    if (!res.error) {
                        var html = '<p class="search-result-title">Search from: </p>';
                        $.each(res.data.items, function (index, el) {
                            html += '<p class="search-item">' + index + '</p>';
                            html += el;
                        });
                        html += '<div class="clearfix"></div>';
                        search_result.html(html);
                        super_search.addClass('search-finished');
                    } else {
                        search_result.html(res.message);
                    }
                    search_result.fadeIn(500);
                },
                error: function (res) {
                    search_result.html(res.responseText);
                    search_result.fadeIn(500);
                }
            });
        }, 500);
    },
    bindActionToElement: function () {
        close_search.on('click', function (event) {
            event.preventDefault();
            if (close_search.hasClass('active')) {
                super_search.removeClass('active');
                search_result.hide();
                close_search.removeClass('active');
                $('body').removeClass('overflow');
                $('.quick-search > .form-control').focus();
            } else {
                super_search.addClass('active');
                if (search_input.val() != '') {
                    Ripple.searchFunction(search_input.val());
                }
                $('body').addClass('overflow');
                close_search.addClass('active');
            }
        });

        search_input.keyup(function (e) {
            search_input.val(e.target.value);
            Ripple.searchFunction(e.target.value);

        });
    }
};

$(document).ready(function () {
    Ripple.bindActionToElement();
});
