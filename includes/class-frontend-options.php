<?php

class Frontend_Options {

	/**
	 * This method hooks into WordPress to possibly process a form submission on the front-end.
	 *
	 * @return void
	 */
	public static function hooks() {

		add_action( 'init', [ __CLASS__, 'maybe_process_form' ] );

	}

	/**
	 * If the form is being submitted, validate the data and update the option.
	 *
	 * @return void
	 */
	public static function maybe_process_form() {

		if (
			isset( $_POST['_wpnonce'] ) &&
			wp_verify_nonce(
				sanitize_text_field(
					wp_unslash( $_POST['_wpnonce'] )
				),
				'ocwp_update_option_frontend'
			) &&
			current_user_can( 'manage_options' )
		) {

			// Get the option using the Options API.
			$ocwp_meetup = get_option(
				'ocwp_meetup',
				[
					'name' => '',
					'url'  => '',
				]
			);

			if ( isset( $_POST['ocwp_meetup'], $_POST['ocwp_meetup']['name'] ) ) {

				$ocwp_meetup['name'] = sanitize_text_field(
					wp_unslash(
						$_POST['ocwp_meetup']['name']
					)
				);

			}

			if ( isset( $_POST['ocwp_meetup'], $_POST['ocwp_meetup']['url'] ) ) {

				$ocwp_meetup['url'] = sanitize_text_field(
					wp_unslash(
						$_POST['ocwp_meetup']['url']
					)
				);

			}

			// Save the option to the database.
			update_option( 'ocwp_meetup', $ocwp_meetup );

		}

	}

}
Frontend_Options::hooks();
