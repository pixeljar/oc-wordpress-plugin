<?php
/**
 * Sample of posting to a webhook.
 *
 * @package ocwp
 */

namespace ocwp;

/**
 * This class posts some data to a WebService.
 */
class Post_To_Webservice {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public static function hooks() {

		/*
		 * There are many different actions you might
		 * hook into to trigger a post to your microservice
		 */
		add_action( 'init', [ __CLASS__, 'post_to_slack' ], 10 );

	}

	/**
	 * Send the data to Slack.
	 *
	 * @return void
	 */
	public static function post_to_slack() {

		// Change to actual slack webhook.
		$slack_webhook = 'https://post-to-slack.dev/url';

		$response = wp_remote_post(
			$slack_webhook,
			array(
				'body' => wp_json_encode(
					[
						'channel'    => '#general', // or #general
						'username'   => 'ocwordpress',
						'icon_emoji' => ':wordpress:',
						'text'       => 'The init action was triggered in WordPress! <https://trunk.local/|Click here> for details!',
					]
				),
			)
		);

		if ( is_wp_error( $response ) ) {

			// Better error handling.
			error_log( print_r( [ $response ], true ) );
			return;
		}

		// Handle a successful response here
		error_log( print_r( [ $response ], true ) );

	}

}
Post_To_Webservice::hooks();
