jQuery(document).ready(function($) {
    $('.shouty-button').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this),
            $btn_more = $(this).parent().parent().find('.shouty-button-more'),
            $opts = $(this).parent().parent().find('.shouty-options'),
            $msgs = $(this).parent().parent().find('.shouty-messages'),
            data = {
                'action': 'shouty_share',
                'email': $(this).parent().find('.shouty-email').val(),
                'textarea': $(this).parent().find('.shouty-textarea').val(),
                'category': $opts.attr('data-category'),
                'look': $opts.attr('data-look'),
                'share_links': $opts.attr('data-share-links'),
                'messages': $opts.attr('data-messages'),
                'messages_links': $opts.attr('data-messages-links'),
                'messages_number': parseInt($opts.attr('data-messages-number')),
                'messages_users_avatar_size': parseInt($opts.attr('data-messages-users-avatar-size'))
                };
        $btn.addClass('shouty-disabled');
        $.post(shouty_ajax.ajaxUrl, data, function(r) {
                var response = parseInt(r);;
                if (response != 0) {
                    r = $.parseJSON(r);
                    if (data.messages == 'show') {
                        $msgs.html(r.shouts);
                    }
                    if ($btn_more.length == 0) {
                        var count = $msgs.children().length;
                        if (count < r.count) {
                            $msgs.after(r.btn_more);
                        }
                    }
                    $('.shouty-textarea').val('');
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
        var $btn = $(this),
            $opts = $(this).parent().find('.shouty-options'),
            $msgs = $(this).parent().find('.shouty-messages'),
            data = {
                'action': 'shouty_show_more',
                'category': $opts.attr('data-category'),
                'look': $opts.attr('data-look'),
                'share_links': $opts.attr('data-share-links'),
                'messages': $opts.attr('data-messages'),
                'messages_number': parseInt($opts.attr('data-messages-number')),
                'messages_users_avatar_size': parseInt($opts.attr('data-messages-users-avatar-size')),
                'factor': parseInt($opts.attr('data-count'))
            };
        $btn.addClass('shouty-disabled');
        $.post(shouty_ajax.ajaxUrl, data, function(r) {
                r = $.parseJSON(r);
                if (data.messages == 'show') {
                    $msgs.append(r.shouts);
                }
                var count = $msgs.children().length;
                if (count < r.count) {
                    $btn.removeClass('shouty-disabled');
                    $btn.blur();
                } else {
                    $btn.addClass('shouty-hidden');
                }
                $opts.attr('data-count', data.factor + 1);
            }
        );
    });
});