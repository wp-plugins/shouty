jQuery(document).ready(function($) {
	$('.shouty-button').on('click', function(e) {
		e.preventDefault();
		var $btn = $(this);
		var $opts = $('.shouty-options');
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
		$btn.addClass('shouty-button-disabled');
		$.post(shouty_ajax.ajaxUrl, data, function(r) {
				var response = parseInt(r);
				if (response != 0) {
					if (data.messages == 'show') {
						$('.shouty-messages').html(r);
					}
					$('.shouty-textarea').val('');
					$('.shouty-button').removeClass('shouty-button-disabled');					
				}
			}
		);
	});
	$('.shouty-button-more').on('click', function(e) {
		e.preventDefault();
		var $btn = $(this);
		var $opts = $('.shouty-options');
		var data = {
			'action': 'shouty_show_more',
			'category': $opts.attr('data-category'),
			'look': $opts.attr('data-look'),
			'messages': $opts.attr('data-messages'),
			'messages_number': $opts.attr('data-messages-number'),
			'messages_users_avatar_size': $opts.attr('data-messages-users-avatar-size'),
			'factor': parseInt($opts.attr('data-count'))
		};
		$btn.addClass('shouty-button-more-disabled');
		$.post(shouty_ajax.ajaxUrl, data, function(r) {
				$opts.attr('data-count', data.factor + 1);
				if (data.messages == 'show') {
					$('.shouty-messages').append(r);
				}
				var count = r.match(/\/div/g) || [];
				if (count.length != 0) {
					$btn.removeClass('shouty-button-more-disabled'); 
				} else {
					$btn.addClass('shouty-hidden');
				}				
			}
		);
	});
});