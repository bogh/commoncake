jQuery(function ($) {
    //On Click Event

    $(document).on('click', '[rel~=scroll-to-top]', function () {
        $('body').animate({
            scrollTop: 0
        }, 100);
    });

    $(document).on('scroll', function () {
        var top = $(this).scrollTop();
        if (top) {
            $("#header #can-scroll").fadeIn(100);
        } else {
            $("#header #can-scroll").fadeOut(100);
        }
    })

    $(document).on('click', 'ul.tabs li', function (e) {
        $('ul.tabs li').removeClass('active');
        $(this).addClass('active');
        $('.tab_content').hide();

        var activeTab = $(this).find('a').attr('href');
        $(activeTab).fadeIn();
    });
});

jQuery(function ($) {

    window.common = {};

    $.ajaxSetup({
        beforeSend: function (jqXHR, settings) {
            if ('modal' in settings && settings.modal) {
                jqXHR.setRequestHeader('X-LAYOUT', 'modal');
            }
        }
    });

    window.common.reload = function (t) {
        t = t || 0;
        _.delay(function () {
            window.location.reload(true);
        }, t * 1000);
    };

    $('[rel~=tooltip]').tooltip();
    $('[rel~=popover]').popover();

    $("[rel~=auto-content]").each(function () {
        var $container = $(this);
        var o = $container.data();
        function updateContent() {
            $.ajax({
                url: o.url,
                success: function (r) {
                    $container.html(r);
                }
            });
        }

        updateContent();
        if (o.autoRefresh) {
            setInterval(updateContent, o.autoRefresh);
        }
    });

    window.common.bodyClick = function (cb) {
        var bind = function (e) {
            e.stopImmediatePropagation();
            $('body').off('click', bind);
            if (_.isFunction(cb)) {
                cb();
            }
        };
        _.delay(function () {
            $('body').on('click', bind);
        });
    };

    window.common.center = function (elem, holder, top) {
        var $holder = null;
        var $elem = $(elem);
        if (top === undefined) {
            top = true;
        }

        if (holder) {
            $holder = $(holder);
        } else {
            $holder = $elem.parent();
        }

        function center() {
            var w = $holder.width(),
                h = $holder.height();

            if ($elem.height() < h) {
                if ($elem.css('position') != 'absolute') {
                    if (top) {
                        $elem.css('margin-top', (h - $elem.height()) / 2);
                    }

                    if ($holder.css('text-align') == 'left') {
                        $elem.css('margin-left', (w - $elem.width()) / 2);
                    } else if ($holder.css('text-align') == 'right') {
                        $elem.css('margin-right', (w - $elem.width()) / 2);
                    }
                } else {
                    if (top) {
                        $elem.css('top', (h - $elem.height()) / 2);
                    }
                    $elem.css('left', (w - $elem.width()) / 2);
                }
            }
        }

        center();

        return window.common;
    };

    // ajax content
    window.common.content = {

        first: true,

        push: true,

        xhr: null,

        options: {},

        init: function () {
            var self = this;

            $(document).on('click', 'a[rel=content]', function (e) {
                e.preventDefault();
                self.load($(this).attr('href'));
            });
        },

        load: function (url, push) {
            if (push === false) {
                this.push = false;
            }

            this.options.url = url;

            if (this.xhr) {
                // abort prev request if exists
                this.xhr.abort();
                this.clear();
            }

            this.xhr = $.ajax({
                url: url,
                beforeSend: this.before,
                error: this.error,
                success: this.success,
                complete: this.complete
            });

            if (this.first) {
                this.first = false;
                $(window).on('popstate', this.pop);
            }
        },

        before: function (jqXHR, options) {
            // show loader
            jqXHR.setRequestHeader('X-LAYOUT', 'content');
            this.loader.show();
            $('body').animate({
                scrollTop: 0
            }, 100);
        },

        error: function (e, jqXHR, options, err) {
            // hide loader
            $('<h4>')
                .addClass('alert_error')
                .html('There was an error proccessing the request.')
                .prependTo($('#main'));
            this.loader.hide();
        },

        success: function (res, req, options) {
            if (this.push && window.history && window.history.pushState) {
                window.history.pushState({
                    url: this.options.url
                }, '', this.options.url);
            }
            $('#main').html(res);
        },

        pop: function (e) {
            this.load(document.location.href, false);
        },

        clear: function () {
            this.options = {};
            this.push = true;
            this.xhr = null;
        },

        complete: function () {
            this.clear();
        },

        loader: {
            show: function (holder, center) {
                holder = holder || "#main";

                $('<div>')
                    .addClass('overlay-loader')
                    .attr('id', "overlay-loader")
                    .html($('<div>').addClass('img'))
                    .appendTo($(holder));

                if (center) {
                    window.common.center('#overlay-loader .img');
                }
            },

            hide: function () {
                $("#overlay-loader").remove();
            }
        }
    };
    _.bindAll(window.common.content);
    window.common.content.init();

    $(document).on('click mouseenter mouseleave', '[rel~=helper]', function (e) {
        var $this = $(this);
        var $tooltip = $this.find('.helper-tooltip');
        var state = false;

        function toggle() {
            if (state) {
                $tooltip.stop(true).fadeOut(150);
            } else {
                $tooltip.stop(true).fadeIn(150);
            }
            state = !state;
            return state;
        }

        var hoverTypes = ['mouseover', 'mouseout'];
        switch ($this.data('trigger')) {
            case 'hover':
                if (e.type == 'mouseout' || e.type == 'mouseover') {
                    toggle();
                }
                break;
            case 'click':
                if (e.type == 'click') {
                    if (toggle()) {
                        window.common.bodyClick(toggle);
                    }
                }
                break;
        }
    });

    // coaie nu comenta ca nu am facut eu codu asta :D e luat de la un fraier, easy shit
    $(document).on('click', 'a[data-confirm]', function (e) {
        var href = $(this).attr('href');
        e.preventDefault();
        if (!$('#dataConfirmModal').length) {
            $('body').append('<div id="dataConfirmModal" class="modal fade fast" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="dataConfirmLabel">Please Confirm</h3></div><div class="modal-body"></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button><a class="btn btn-primary" id="dataConfirmOK">OK</a></div></div>');
        }

        $('#dataConfirmModal').find('.modal-body')
            .text($(this).attr('data-confirm'));
        $('#dataConfirmOK').attr('href', href);

        $('#dataConfirmModal').modal({show:true});

        return false;
    });

    $(document).on('focus', '.input.datepicker-h input[type="text"]', function () {
        $(this).datepicker({
            format: 'yyyy-mm-dd'
        });
    });

    // togglers
    $('[rel~=toggler]').click(function () {
        var $this = $(this),
            id = $this.attr('href');
        $(id).slideToggle();
    });

    // modals
    $(document).on('click', 'a[rel=modal]', function (e) {
        e.preventDefault();

        var $this = $(this);

        // Send a custom header to be tell the app this is a modal
        $this.button('loading');
        $.ajax({
            url: $this.attr('href'),
            type: 'GET',
            modal: true,
            success: function (data) {
                var $modal = $(data),
                    wW = $(window).width(),
                    mW = $modal.data('width');

                $modal.appendTo('body');

                if (mW) {
                    if (mW > wW) {
                        mW = wW - 40;
                    }
                    $modal.css({
                        width: mW,
                        marginLeft: 0,
                        left: wW / 2 - mW / 2
                    });
                }

                $this.button('reset');
                $modal.modal();
            }
        });
    });

    // General money input
    $(document).on('change', 'input.money[type=number]', function (e) {
        var v = parseFloat($(this).val());
        $(this).val(v.toFixed(2));
    });


    // AJAX forms
    $(document).on('submit', 'form[rel~=ajax]', function (e) {
        e.preventDefault();

        var $this = $(this),

            options = $.extend({
                holder: 'modal',
                active: false
            }, $this.data()),

            ajax = {
                url: $this.attr('action'),
                type: $this.attr('method'),
                data: $this.serializeArray(),
                beforeSend: function () {
                    $this.data('active', true);
                },
                success: function (res) {
                    var $holder;
                    switch (options.holder) {
                        case 'modal':
                            $holder = $this.find('.modal-body');
                            break;
                    }

                    if ($holder) {
                        $holder.html(res);
                    }
                },
                error: function () {
                    $this.data('active', false);
                },
                complete: function () {
                    $this.data('active', false);
                }
            };

        if (options.active) {
            return; // means form is already in execution
        }
        $.ajax(ajax);
    });
});
