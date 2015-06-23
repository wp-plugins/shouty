jQuery(document).ready(function($) {
    $('.shouty-button').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $btn_more = $('.shouty-button-more');
        var $opts = $('.shouty-options');
        var $msgs = $('.shouty-messages');
        var data = {
            'action': 'shouty_share',
            'email': $('.shouty-email').val(),
            'textarea': $('.shouty-textarea').val(),
            'category': $opts.attr('data-category'),
            'look': $opts.attr('data-look'),
            'messages': $opts.attr('data-messages'),
            'messages_number': $opts.attr('data-messages-number'),
            'messages_users_avatar_size': $opts.attr('data-messages-users-avatar-size')
        };
        $btn.addClass('shouty-disabled');
        $.post(shouty_ajax.ajaxUrl, data, function(r) {
                var response = parseInt(r);
                if (response != 0) {
                    r = $.parseJSON(r);
                    if (data.messages == 'show') {
                        $msgs.html(r[0]);
                    }
                    $('.shouty-textarea').val('');
                    if ($btn_more.length == 0) {
                        var count = $msgs.children().length;
                        if (count < r[1]) {
                            $msgs.after(r[2]);
                        }
                    }
                    $btn.removeClass('shouty-disabled');
                    if ($btn_more.hasClass('shouty-disabled')) {
                        $btn_more.removeClass('shouty-disabled');
                    }
                    if ($btn_more.hasClass('shouty-hidden')) {
                        $btn_more.removeClass('shouty-hidden');
                    }
                    $opts.attr('data-count', '1')
                }
            }
        );
    });
    $('.shouty').on('click', '.shouty-button-more', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $opts = $('.shouty-options');
        var $msgs = $('.shouty-messages');
        var data = {
            'action': 'shouty_show_more',
            'category': $opts.attr('data-category'),
            'look': $opts.attr('data-look'),
            'messages': $opts.attr('data-messages'),
            'messages_number': $opts.attr('data-messages-number'),
            'messages_users_avatar_size': $opts.attr('data-messages-users-avatar-size'),
            'factor': parseInt($opts.attr('data-count'))
        };
        $btn.addClass('shouty-disabled');
        $.post(shouty_ajax.ajaxUrl, data, function(r) {
                r = $.parseJSON(r);
                if (data.messages == 'show') {
                    $msgs.append(r[0]);
                }
                var count = $msgs.children().length;
                if (count < r[1]) {
                    $btn.removeClass('shouty-disabled');
                } else {
                    $btn.addClass('shouty-hidden');
                }
                $opts.attr('data-count', data.factor + 1);
            }
        );
    });
});