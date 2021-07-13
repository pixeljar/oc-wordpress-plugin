jQuery( document ).ready( function( $ ) {

	var send_notification = function( event ) {

		// Don't submit the form yet.
		event.preventDefault();

		// Send a notification email via admin ajax.
		$.post(
			window.OCWP.ajaxurl,
			{
				'action' : 'ocwp_save_options',
				'ocwp_nonce' : window.OCWP.ocwp_nonce,
				'meetup_name' : $('#ocwp-options-form input#name').val(),
				'meetup_url' : $('#ocwp-options-form input#url').val(),
			},
			function( data ) {

				location.href = window.OCWP.returnurl;

			}
		)

	};

	$( '#ocwp-options-form' ).on( 'submit', send_notification );

});
