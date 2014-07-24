jQuery(document).ready(function($) {
	$('.shouty-button').on('click', function(a) {
		a.preventDefault();
		var data = {
			'action': 'shouty_share',
			'email': $('.shouty-email').val(),
			'textarea': $('.shouty-textarea').val()
		};
		$('.shouty-button').attr('disabled', 'disabled');
		$.post(shouty_ajax.ajaxUrl, data, function(r) {
				var response = parseInt(r);
				if (response != 0) {
					$('.shouty-messages').html(r);
					$('.shouty-textarea').val('');
					$('.shouty-button').removeAttr('disabled');					
				}
			}
		);
	});
	$('.shouty-button-more').on('click', function(a) {
		a.preventDefault();
		var data = {
			'action': 'shouty_show_more',
			'offset': $('.shouty-message').size()
		};
		$('.shouty-button-more').attr('disabled', 'disabled');
		$.post(shouty_ajax.ajaxUrl, data, function(r) {
				$('.shouty-messages').append(r);
				$('.shouty-button').removeAttr('disabled'); 
			}
		);
	});
});