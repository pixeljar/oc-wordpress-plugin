<?php
/**
 * This file includes examples of how to utilize the WordPress Cron.
 *
 * @package ocwp;
 */

namespace ocwp;

/**
 * Examples of how to use the WordPress Cron.
 */
class Cron {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public static function hooks() {

		// Only run on activation.
		register_activation_hook( OCWP_MAIN_FILE, [ __CLASS__, 'cron_activation' ] );
		register_deactivation_hook( OCWP_MAIN_FILE, [ __CLASS__, 'cron_deactivation' ] );

		// Send an email every hour.
		add_action( 'ocwp_hourly_cron', [ __CLASS__, 'send_email' ] );

		// Clean revisions weekly.
		add_action( 'ocwp_weekly_cron', [ __CLASS__, 'delete_revisions' ] );

		// Create new cron timings.
		add_filter( 'cron_schedules', [ __CLASS__, 'cron_schedules' ] );

	}

	/**
	 * Register any plugin specific cron jobs.
	 *
	 * @return void
	 */
	public static function cron_activation() {

		$args = [
			'example@example.com',
		];

		if ( ! wp_next_scheduled( 'ocwp_hourly_cron', $args ) ) {

			// WordPress default timings - hourly, twicedaily, daily.
			wp_schedule_event( time(), 'hourly', 'ocwp_hourly_cron', $args );

		}

		if ( ! wp_next_scheduled( 'delete_revisions' ) ) {

			wp_schedule_event( time(), 'weekly', 'ocwp_weekly_cron' );

		}

	}

	/**
	 * Remove any scheduled events.
	 *
	 * @return void
	 */
	public static function cron_deactivation() {

		// Email sending.
		$timestamp = wp_next_scheduled( 'ocwp_hourly_cron' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'ocwp_hourly_cron' );
		}

		// Delete revisions.
		$timestamp = wp_next_scheduled( 'delete_revisions' );
		if ( false !== $timestamp ) {
			wp_unschedule_event( $timestamp, 'delete_revisions' );
		}

	}

	/**
	 * Adds two new cron timings.
	 *
	 * @param array $schedules The available timings.
	 * @return array
	 */
	public static function cron_schedules( $schedules ) {

		$schedules['weekly'] = [
			'interval' => WEEK_IN_SECONDS,
			'display'  => __( 'Once Weekly', 'ocwp' )
		];

		$schedules['halfhourly'] = [
			'interval' => ( HOUR_IN_SECONDS / 2 ),
			'display'  => __( 'Every Half Hour', 'ocwp' )
		];

		return $schedules;
	}

	/**
	 * Sends an email on a recurring basis.
	 *
	 * @param string $email An email address.
	 * @return void
	 */
	public static function send_email( $email ) {

		wp_mail(
			sanitize_email( $email ),
			'Reminder',
			'Hey, remember to do that important thing!'
		);

	}

	/**
	 * Delete post revisions from the database.
	 *
	 * @return void
	 */
	public static function delete_revisions() {

		global $wpdb;

		$sql = "DELETE a,b,c
			FROM $wpdb->posts array
			LEFT JOIN $wpdb->term_relationships b ON (a.ID = b.object_id)
			LEFT JOIN $wpdb->postmeta c ON (a.ID = c.post_id)
			WHERE a.post_type = 'revision'
			AND DATEDIFF( now(), a.post_modified )> 7";

		$wpdb->query( $wpdb->prepare( $sql ) );

	}

}

Cron::hooks();
