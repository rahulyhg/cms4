var Botble = Botble || {};

Botble.blockUI = function (options) {
    options = $.extend(true, {}, options);
    var html = '';
    if (options.animate) {
        html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '">' + '<div class="block-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>' + '</div>';
    } else if (options.iconOnly) {
        html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="/vendor/core/images/loading-spinner-blue.gif" align=""></div>';
    } else if (options.textOnly) {
        html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><span>&nbsp;&nbsp;' + (options.message ? options.message : 'LOADING...') + '</span></div>';
    } else {
        html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="/vendor/core/images/loading-spinner-blue.gif" align=""><span>&nbsp;&nbsp;' + (options.message ? options.message : 'LOADING...') + '</span></div>';
    }

    if (options.target) { // element blocking
        var el = $(options.target);
        if (el.height() <= ($(window).height())) {
            options.cenrerY = true;
        }
        el.block({
            message: html,
            baseZ: options.zIndex ? options.zIndex : 1000,
            centerY: options.cenrerY !== undefined ? options.cenrerY : false,
            css: {
                top: '10%',
                border: '0',
                padding: '0',
                backgroundColor: 'none'
            },
            overlayCSS: {
                backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                opacity: options.boxed ? 0.05 : 0.1,
                cursor: 'wait'
            }
        });
    } else { // page blocking
        $.blockUI({
            message: html,
            baseZ: options.zIndex ? options.zIndex : 1000,
            css: {
                border: '0',
                padding: '0',
                backgroundColor: 'none'
            },
            overlayCSS: {
                backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                opacity: options.boxed ? 0.05 : 0.1,
                cursor: 'wait'
            }
        });
    }
};

Botble.unblockUI = function (target) {
    if (target) {
        $(target).unblock({
            onUnblock: function () {
                $(target).css('position', '');
                $(target).css('zoom', '');
            }
        });
    } else {
        $.unblockUI();
    }
};

Botble.showNotice = function (messageType, message) {
    toastr.clear();

    toastr.options = {
        closeButton: true,
        positionClass: 'toast-bottom-right',
        onclick: null,
        showDuration: 1000,
        hideDuration: 1000,
        timeOut: 10000,
        extendedTimeOut: 1000,
        showEasing: 'swing',
        hideEasing: 'linear',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
    };

    var messageHeader = '';

    switch (messageType) {
        case 'error':
            messageHeader = Botble.languages.notices_msg.error;
            break;
        case 'success':
            messageHeader = Botble.languages.notices_msg.success;
            break;
    }
    toastr[messageType](message, messageHeader);
};

Botble.handleError = function (data) {
    if (typeof (data.responseJSON) !== 'undefined') {
        if (typeof (data.responseJSON.errors) !== 'undefined') {
            var message = '';
            $.each(data.responseJSON.errors, function (index, item) {
                message += item + '<br />';
                if (data.status === 422) {
                    var $input = $('*[name="' + index + '"]');
                    if ($input.closest('.next-input--stylized').length) {
                        $input.closest('.next-input--stylized').addClass('field-has-error');
                    } else {
                        $input.addClass('field-has-error');
                    }

                    var $input_array = $('*[name$="[' + index + ']"]');

                    if ($input_array.closest('.next-input--stylized').length) {
                        $input_array.closest('.next-input--stylized').addClass('field-has-error');
                    } else {
                        $input_array.addClass('field-has-error');
                    }
                }
            });
            Botble.showNotice('error', message);
        } else if (typeof (data.responseJSON.message) !== 'undefined') {
            Botble.showNotice('error', data.responseJSON.message);
        } else {
            $.each(data.responseJSON, function (index, el) {
                $.each(el, function (key, item) {
                    Botble.showNotice('error', item);
                });
            });
        }
    } else {
        Botble.showNotice('error', data.statusText);
    }
};

Botble.countCharacter = function () {
    $.fn.charCounter = function (max, settings) {
        max = max || 100;
        settings = $.extend({
            container: '<span></span>',
            classname: 'charcounter',
            format: '(%1 ' + Botble.languages.system.character_remain + ')',
            pulse: true,
            delay: 0
        }, settings);
        var p, timeout;

        function count(el, container) {
            el = $(el);
            if (el.val().length > max) {
                el.val(el.val().substring(0, max));
                if (settings.pulse && !p) {
                    pulse(container, true);
                }
            }
            if (settings.delay > 0) {
                if (timeout) {
                    window.clearTimeout(timeout);
                }
                timeout = window.setTimeout(function () {
                    container.html(settings.format.replace(/%1/, (max - el.val().length)));
                }, settings.delay);
            } else {
                container.html(settings.format.replace(/%1/, (max - el.val().length)));
            }
        }

        function pulse(el, again) {
            if (p) {
                window.clearTimeout(p);
                p = null;
            }
            el.animate({
                opacity: 0.1
            }, 100, function () {
                $(this).animate({
                    opacity: 1.0
                }, 100);
            });
            if (again) {
                p = window.setTimeout(function () {
                    pulse(el)
                }, 200);
            }
        }

        return this.each(function () {
            var container;
            if (!settings.container.match(/^<.+>$/)) {
                // use existing element to hold counter message
                container = $(settings.container);
            } else {
                // append element to hold counter message (clean up old element first)
                $(this).next("." + settings.classname).remove();
                container = $(settings.container)
                    .insertAfter(this)
                    .addClass(settings.classname);
            }
            $(this)
                .unbind('.charCounter')
                .bind('keydown.charCounter', function () {
                    count(this, container);
                })
                .bind('keypress.charCounter', function () {
                    count(this, container);
                })
                .bind('keyup.charCounter', function () {
                    count(this, container);
                })
                .bind('focus.charCounter', function () {
                    count(this, container);
                })
                .bind('mouseover.charCounter', function () {
                    count(this, container);
                })
                .bind('mouseout.charCounter', function () {
                    count(this, container);
                })
                .bind('paste.charCounter', function () {
                    var me = this;
                    setTimeout(function () {
                        count(me, container);
                    }, 10);
                });
            if (this.addEventListener) {
                this.addEventListener('input', function () {
                    count(this, container);
                }, false);
            }
            count(this, container);
        });
    };

    $(document).on('click', 'input[data-counter], textarea[data-counter]', function () {
        $(this).charCounter($(this).data('counter'), {
            container: '<small></small>'
        });
    });
};

Botble.manageSidebar = function () {

    var body = $('body');
    var navigation = $('.navigation');
    var sidebar_content = $('.sidebar-content');

    navigation.find('li.active').parents('li').addClass('active');
    navigation.find('li').has('ul').children('a').parent('li').addClass('has-ul');


    $(document).on('click', '.sidebar-toggle.hidden-xs', function (e) {
        e.preventDefault();

        body.toggleClass('sidebar-narrow');
        body.toggleClass('page-sidebar-closed');

        if (body.hasClass('sidebar-narrow')) {
            navigation.children('li').children('ul').css('display', '');

            sidebar_content.hide().delay().queue(function () {
                $(this).show().addClass('animated fadeIn').clearQueue();
            });
        } else {
            navigation.children('li').children('ul').css('display', 'none');
            navigation.children('li.active').children('ul').css('display', 'block');

            sidebar_content.hide().delay().queue(function () {
                $(this).show().addClass('animated fadeIn').clearQueue();
            });
        }
    });
};

Botble.initDatepicker = function (element) {
    if (jQuery().bootstrapDP) {
        $(document).find(element).bootstrapDP({
            maxDate: 0,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
        });
    }
};

Botble.initResources = function () {
    if (jQuery().select2) {
        $(document).find('.select-multiple').select2({
            width: '100%',
            allowClear: true,
            placeholder: $(this).data('placeholder')
        });
        $(document).find('.select-search-full').select2({
            width: '100%'
        });
        $(document).find('.select-full').select2({
            width: '100%',
            minimumResultsForSearch: -1
        });
    }

    if (jQuery().timepicker) {
        if (jQuery().timepicker) {

            $('.timepicker-default').timepicker({
                autoclose: true,
                showSeconds: true,
                minuteStep: 1,
                defaultTime: false
            });

            $('.timepicker-no-seconds').timepicker({
                autoclose: true,
                minuteStep: 5,
                defaultTime: false,
            });

            $('.timepicker-24').timepicker({
                autoclose: true,
                minuteStep: 5,
                showSeconds: false,
                showMeridian: false,
                defaultTime: false
            });
        }
    }

    if (jQuery().inputmask) {
        $(document).find('.input-mask-number').inputmask({
            alias: 'numeric',
            rightAlign: false,
            digits: 2,
            groupSeparator: ',',
            placeholder: '0',
            autoGroup: true,
            autoUnmask: true,
            removeMaskOnSubmit: true,
        });
    }

    if (jQuery().colorpicker) {
        $('.color-picker').colorpicker({});
    }

    this.initDatepicker('.datepicker');

    if (jQuery().fancybox) {
        $('.iframe-btn').fancybox({
            'width': '900px',
            'height': '700px',
            'type': 'iframe',
            'autoScale': false,
            openEffect: 'none',
            closeEffect: 'none',
            overlayShow: true,
            overlayOpacity: 0.7
        });
        $('.fancybox').fancybox({
            openEffect: 'none',
            closeEffect: 'none',
            overlayShow: true,
            overlayOpacity: 0.7,
            helpers: {
                media: {}
            }
        });
    }
    $('.styled').uniform();
    $('.tip').tooltip({placement: 'top'});

    if (jQuery().areYouSure) {
        $('form').areYouSure();
    }

    Botble.callScroll($('.list-item-checkbox'));
};

Botble.numberFormat = function (number, decimals, dec_point, thousands_sep) {
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        toFixedFix = function (n, prec) {
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            var k = Math.pow(10, prec);
            return Math.round(n * k) / k;
        },
        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

Botble.callScroll = function (obj) {
    obj.mCustomScrollbar({
        axis: 'yx',
        theme: 'minimal-dark',
        scrollButtons: {
            enable: true
        },
        callbacks: {
            whileScrolling: function () {
                obj.find('.tableFloatingHeaderOriginal').css({
                    'top': -this.mcs.top + 'px'
                });
            }
        }
    });
    obj.stickyTableHeaders({scrollableArea: obj, "fixedOffset": 2});
};

Botble.handleWaypoint = function () {
    if ($('#waypoint').length > 0) {
        new Waypoint({
            element: document.getElementById('waypoint'),
            handler: function (direction) {
                if (direction === 'down') {
                    $('.form-actions-fixed-top').removeClass('hidden');
                } else {
                    $('.form-actions-fixed-top').addClass('hidden');
                }
            }
        });
    }
};

// Handles counterup plugin wrapper
Botble.handleCounterup = function () {
    if (!$().counterUp) {
        return;
    }

    $('[data-counter="counterup"]').counterUp({
        delay: 10,
        time: 1000
    });
};

if (jQuery().datepicker.noConflict) {
    $.fn.bootstrapDP = $.fn.datepicker.noConflict();
}

Botble.initMediaIntegrate = function () {

    if (jQuery().rvMedia) {

        $('[data-type="rv-media-standard-alone-button"]').rvMedia({
            multiple: false,
            onSelectFiles: function (files, $el) {
                $($el.data('target')).val(files[0].url);
            }
        });

        $(document).find('.btn_gallery').rvMedia({
            multiple: false,
            onSelectFiles: function (files, $el) {
                switch ($el.data('action')) {
                    case 'media-insert-ckeditor':
                        $.each(files, function (index, file) {
                            var link = file.url;
                            if (file.type === 'youtube') {
                                link = link.replace('watch?v=', 'embed/');
                                CKEDITOR.instances[$el.data('result')].insertHtml('<iframe width="420" height="315" src="' + link + '" frameborder="0" allowfullscreen></iframe>');
                            } else if (file.type === 'image') {
                                CKEDITOR.instances[$el.data('result')].insertHtml('<img src="' + link + '" alt="' + file.name + '" />');
                            } else {
                                CKEDITOR.instances[$el.data('result')].insertHtml('<a href="' + link + '">' + file.name + '</a>');
                            }
                        });

                        break;
                    case 'media-insert-tinymce':
                        $.each(files, function (index, file) {
                            var link = file.url;
                            var html = '';
                            if (file.type === 'youtube') {
                                link = link.replace('watch?v=', 'embed/');
                                html = '<iframe width="420" height="315" src="' + link + '" frameborder="0" allowfullscreen></iframe>';
                            } else if (file.type === 'image') {
                                html = '<img src="' + link + '" alt="' + file.name + '" />';
                            } else {
                                html = '<a href="' + link + '">' + file.name + '</a>';
                            }
                            tinymce.activeEditor.execCommand('mceInsertContent', false, html);
                        });
                        break;
                    case 'select-image':
                        var firstItem = _.first(files);
                        $el.closest('.image-box').find('.image-data').val(firstItem.url);
                        $el.closest('.image-box').find('.preview_image').attr('src', firstItem.thumb);
                        $el.closest('.image-box').find('.preview-image-wrapper').show();
                        break;
                    case 'attachment':
                        var firstItem = _.first(files);
                        $el.closest('.attachment-wrapper').find('.attachment-url').val(firstItem.url);
                        $('.attachment-details').html('<a href="' + firstItem.url + '" target="_blank">' + firstItem.url + '</a>');
                        break;
                }
            }
        });

        $(document).on('click', '.btn_remove_image', function (event) {
            event.preventDefault();
            $(this).closest('.image-box').find('.preview-image-wrapper').hide();
            $(this).closest('.image-box').find('.image-data').val('');
        });

        $(document).on('click', '.btn_remove_attachment', function (event) {
            event.preventDefault();
            $(this).closest('.attachment-wrapper').find('.attachment-details a').remove();
            $(this).closest('.attachment-wrapper').find('.attachment-url').val('');
        });
    }
};

Botble.getViewPort = function () {
    var e = window,
        a = 'inner';
    if (!('innerWidth' in window)) {
        a = 'client';
        e = document.documentElement || document.body;
    }

    return {
        width: e[a + 'Width'],
        height: e[a + 'Height']
    };
};

// Handles portlet tools & actions
Botble.handlePortletTools = function () {
    // handle portlet remove

    // handle portlet fullscreen
    $('body').on('click', '.portlet > .portlet-title .fullscreen', function (e) {
        e.preventDefault();
        var portlet = $(this).closest(".portlet");
        if (portlet.hasClass('portlet-fullscreen')) {
            $(this).removeClass('on');
            portlet.removeClass('portlet-fullscreen');
            $('body').removeClass('page-portlet-fullscreen');
            portlet.children('.portlet-body').css('height', 'auto');
        } else {
            var height = Botble.getViewPort().height -
                portlet.children('.portlet-title').outerHeight() -
                parseInt(portlet.children('.portlet-body').css('padding-top')) -
                parseInt(portlet.children('.portlet-body').css('padding-bottom'));

            $(this).addClass('on');
            portlet.addClass('portlet-fullscreen');
            $('body').addClass('page-portlet-fullscreen');
            portlet.children('.portlet-body').css('height', height);
        }
    });

    $('body').on('click', '.portlet > .portlet-title > .tools > .collapse, .portlet .portlet-title > .tools > .expand', function (e) {
        e.preventDefault();
        var el = $(this).closest('.portlet').children('.portlet-body');
        if ($(this).hasClass('collapse')) {
            $(this).removeClass('collapse').addClass('expand');
            el.slideUp(200);
        } else {
            $(this).removeClass('expand').addClass('collapse');
            el.slideDown(200);
        }
    });
};

Botble.initCodeEditor = function(id) {
    $(document).find('#' + id).wrap('<div id="wrapper_' + id + '"><div class="container_content_codemirror"></div> </div>');
    $('#wrapper_' + id).append('<div class="handle-tool-drag" id="tool-drag_' + id + '"></div>');
    CodeMirror.fromTextArea(document.getElementById(id), {
        extraKeys: {'Ctrl-Space': 'autocomplete'},
        lineNumbers: true,
        mode: 'css',
        autoRefresh: true,
        lineWrapping: true,
    });

    $('.handle-tool-drag').mousedown(function (e) {
        $(this).attr('data-start_h', $(this).parent().find('.CodeMirror').height()).attr('data-start_y', e.pageY);
        $('body').attr('data-dragtool', $(this).attr('id')).on('mousemove', Botble.onDragTool);
        $(window).on('mouseup', Botble.onReleaseTool);
    });
};

Botble.onDragTool = function(e) {
    ele = '#' + $('body').attr('data-dragtool');
    container = $(ele).parent().find('.CodeMirror');
    start_h = parseInt($(ele).attr('data-start_h'));

    $(ele).parent().find('.CodeMirror').css('height', Math.max(200, start_h + e.pageY - $(ele).attr('data-start_y')));
};

Botble.onReleaseTool = function() {
    $('body').off('mousemove', Botble.onDragTool);
    $(window).off('mouseup', Botble.onReleaseTool);
};

$(document).ready(function () {
    Botble.countCharacter();
    Botble.manageSidebar();
    Botble.initResources();
    Botble.handleWaypoint();
    Botble.handleCounterup();
    Botble.initMediaIntegrate();
    Botble.handlePortletTools();
});
