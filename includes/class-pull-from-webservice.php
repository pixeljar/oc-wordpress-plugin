<?php
/**
 * Sample of pulling from an external content source.
 *
 * @package ocwp
 */

namespace ocwp;

/**
 * This class pulls some data from a WebService.
 */
class Pull_From_Webservice {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public static function hooks() {

		/*
		 * There are many different actions you might
		 * hook into to trigger a pull from a microservice
		 */
		add_action( 'init', [ __CLASS__, 'pull_from_rss' ], 10 );

	}

	/**
	 * Grab some content from a Google Alert.
	 *
	 * @return void
	 */
	public static function pull_from_rss() {

		// Google alert feed for "WordPress".
		$rss_feed = 'https://www.google.com/alerts/feeds/03330858117083132927/14637194953648254169';
		$response = wp_remote_get( $rss_feed );

		if ( is_wp_error( $response ) ) {

			// Better error handling.
			error_log( print_r( [ $response ], true ) );
			return;
		}

		$xml_source = wp_remote_retrieve_body( $response );
		$xml        = simplexml_load_string( $xml_source );

		foreach ( $xml->entry as $entry ) {

			/* Capture data to local variables */

			// Unique ID for the article.
			$id = (string) $entry->id;

			// Mention Title.
			$title = (string) $entry->title;

			// URL.
			$google_link = (string) $entry->link->attributes()->href;
			$url_parts = [];
			preg_match( '/https:\/\/www.google.com\/url\?rct=j&sa=t&url=(.*)&ct=ga&cd=.*&usg=.*/', $google_link, $url_parts );
			if ( count( $url_parts ) > 0 ) {
				$link = $url_parts[1];
			} else {
				$link = $google_link;
			}

			// Publish Date.
			$published = gmdate( 'Y-m-d H:i:s', strtotime( (string) $entry->published ) );

			// Post Content.
			$excerpt = (string) $entry->content;

			// Check to see if post already exists.
			$post_exists = get_posts(
				array(
					'posts_per_page' => 1,
					'meta_key'       => 'google-alert-id',
					'meta_value'     => $id,
					'post_type'      => 'post',
					'post_status'    => 'any',
				)
			);

			if ( ! $post_exists ) {

				// Insert the post.
				$post_id = wp_insert_post(
					[
						'post_title'   => wp_strip_all_tags( $title ),
						'post_author'  => 1,
						'post_date'    => sanitize_text_field( $published ),
						'post_content' => wp_kses_post( $excerpt ),
						'post_status'  => 'draft',
						'post_type'    => 'post',
						'meta_input'   => [
							'google-alert-id' => sanitize_text_field( $id ),
							'original_url'    => sanitize_text_field( $link ),
						],
					]
				);

				// Something went wrong, return the feed to the queue for additional processing.
				if ( is_wp_error( $post_id ) ) {

					// Handle a successful response here.
					error_log( print_r( [ $post_id ], true ) );

				}

				// Handle a successful response here.
				error_log( print_r( [ 'post_id' => $post_id ], true ) );

			}

		}

		// Handle a successful response here.
		error_log( print_r( [ $response ], true ) );

	}

}
Pull_From_Webservice::hooks();
