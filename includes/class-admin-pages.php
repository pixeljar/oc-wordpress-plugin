<?php
/**
 * Creates an admin page.
 *
 * @package ocwp
 */

namespace ocwp;

/**
 * The Admin Pages class creates all admin pages for our plugin.
 */
class Admin_Pages {

	/**
	 * This static method can be called to hook into WordPress to create admin pages.
	 *
	 * @return void
	 */
	public static function hooks() {

		add_action( 'admin_menu', [ __CLASS__, 'admin_menu' ] );
		add_action( 'admin_init', [ __CLASS__, 'settings_init' ] );

		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );

		add_action( 'wp_ajax_ocwp_save_options', [ __CLASS__, 'ajax_save_options' ] );
		add_action( 'wp_ajax_nopriv_ocwp_save_options', [ __CLASS__, 'ajax_save_options' ] );


	}

	/**
	 * This method creates a top-level admin page.
	 *
	 * @return void
	 */
	public static function admin_menu() {

		add_menu_page(
			__( 'OCWP Plugin', 'ocwp' ),
			__( 'OCWP Plugin', 'ocwp' ),
			'manage_options',
			'ocwp',
			[ __CLASS__, 'render_page' ],
			'dashicons-buddicons-community',
		);

	}

	public static function enqueue_scripts() {

		wp_enqueue_script(
			'ocwp-admin-ajax',
			OCWP_URL . 'assets/js/admin-ajax.js',
			[
				'jquery',
			],
			OCWP_VERSION,
			true
		);

		wp_localize_script(
			'ocwp-admin-ajax',
			'OCWP',
			[
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'ocwp_nonce' => wp_create_nonce( 'ocwp_ajax_save_option' ),
				'returnurl'  => admin_url( 'admin.php?page=ocwp&settings-updated=true' ),
			]
		);

	}

	/**
	 * This method is a callback that is triggered from the add_menu_page function.
	 * When called, this method handles the output for the admin page.
	 *
	 * @return void
	 */
	public static function render_page() {

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Add error/update messages.
		// Check if the user have submitted the settings.
		// WordPress will add the "settings-updated" $_GET parameter to the url.
		if ( isset( $_GET['settings-updated'] ) ) {

			// Add settings saved message with the class of "updated".
			add_settings_error(
				'ocwp_messages',
				'ocwp_message',
				__( 'Settings Saved', 'ocwp' ),
				'updated'
			);

		}

		// Show error/update messages.
		settings_errors( 'ocwp_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form action="options.php" method="post" id="ocwp-options-form">
			<?php

				// Output security fields for the registered setting "ocwp_meetup".
				settings_fields( 'ocwp_group' );

				// Output setting sections and their fields.
				// (sections are registered for "wporg", each field is registered to a specific section).
				do_settings_sections( 'ocwp_page' );

				// Output save settings button.
				submit_button( __( 'Save Settings', 'ocwp' ) );

			?>
			</form>
		</div>
		<?php

	}

	public static function settings_init() {

		// Register a new setting for "wporg" page.
		register_setting( 'ocwp_group', 'ocwp_meetup' );

		// Register a new section in the "wporg" page.
		add_settings_section(
			'ocwp_meetup_section',
			__( 'Meetup Information', 'ocwp' ),
			[ __CLASS__, 'section_description' ],
			'ocwp_page'
		);

		// Register a new field in the "ocwp_meetup" section, inside the "ocwp_page" page.
		add_settings_field(
			'name',
			__( 'Meetup Name', 'ocwp' ),
			[ __CLASS__, 'meetup_name_field' ],
			'ocwp_page',
			'ocwp_meetup_section',
			[
				'label_for'        => 'name',
				'class'            => 'ocwp_meetup_name',
				'owcp_custom_data' => 'custom',
			]
		);

		// Register a new field in the "ocwp_meetup" section, inside the "ocwp_page" page.
		add_settings_field(
			'url',
			__( 'Meetup URL', 'ocwp' ),
			[ __CLASS__, 'meetup_url_field' ],
			'ocwp_page',
			'ocwp_meetup_section',
			[
				'label_for' => 'url',
				'class'     => 'ocwp_meetup_url',
			]
		);

	}

	/**
	 * This method outputs the description for the section.
	 *
	 * @return void
	 */
	public static function section_description() {
		echo '<p>In this section we will store information about our meetup.</p>';
	}

	/**
	 * This method outputs the field for the setting.
	 *
	 * @return void
	 */
	public static function meetup_name_field( $args = [] ) {

		// Get the value of the setting we've registered with register_setting().
		$options = get_option( 'ocwp_meetup' );

		// Output the field.
		?>
		<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
			data-custom="<?php echo esc_attr( $args['owcp_custom_data'] ); ?>"
			class="<?php echo esc_attr( $args['class'] ); ?>"
			name="ocwp_meetup[<?php echo esc_attr( $args['label_for'] ); ?>]"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? esc_attr( $options[ $args['label_for'] ] ) : ''; ?>"
		>
		<p class="description">
			<?php esc_html_e( 'Enter the name of your meetup.', 'ocwp' ); ?>
		</p>
		<?php

	}

	/**
	 * This method outputs the field for the setting.
	 *
	 * @return void
	 */
	public static function meetup_url_field( $args = [] ) {

		// Get the value of the setting we've registered with register_setting().
		$options = get_option( 'ocwp_meetup' );

		// Output the field.
		?>
		<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
			class="<?php echo esc_attr( $args['class'] ); ?>"
			name="ocwp_meetup[<?php echo esc_attr( $args['label_for'] ); ?>]"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? esc_attr( $options[ $args['label_for'] ] ) : ''; ?>"
		>
		<p class="description">
			<?php esc_html_e( 'Enter the url of your meetup.', 'ocwp' ); ?>
		</p>
		<?php

	}

	public static function ajax_save_options() {

		if (
			isset( $_POST['ocwp_nonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ocwp_nonce'] ) ), 'ocwp_ajax_save_option' ) &&
			current_user_can( 'manage_options' )
		) {

			$is_sent = wp_mail(
				get_bloginfo( 'admin_email' ),
				__( 'A setting was updated', 'ocwp' ),
				sprintf(
					"%s,\n\nName: %s\n\nURL: %s",
					__( 'This is what was submitted.', 'ocwp' ),
					sanitize_text_field( wp_unslash( $_POST['meetup_name'] ) ),
					sanitize_text_field( wp_unslash( $_POST['meetup_url'] ) )
				)
			);

			update_option(
				'ocwp_meetup',
				[
					'name' => sanitize_text_field( wp_unslash( $_POST['meetup_name'] ) ),
					'url'  => sanitize_text_field( wp_unslash( $_POST['meetup_url'] ) ),
				]
			);

			global $wpdb;
			$wpdb->query(
				$wpdb->prepare(
					"
					INSERT INTO {$wpdb->prefix}options SET
					option_name = 'ocwp_meetup_name',
					option_value = %s,
					autoload = 'no'
					",
					sanitize_text_field( wp_unslash( $_POST['meetup_name'] ) )
				)
			);
			$wpdb->query(
				$wpdb->prepare(
					"
					INSERT INTO {$wpdb->prefix}options SET
					option_name = 'ocwp_meetup_url',
					option_value = %s,
					autoload = 'no'
					",
					sanitize_text_field( wp_unslash( $_POST['meetup_url'] ) )
				)
			);

			if ( $is_sent ) {
				wp_send_json_success( [ 'message' => __( 'The message was sent' ) ] );
			} else {
				wp_send_json_success( [ 'message' => __( 'The message was NOT sent' ) ] );
			}

		}

	}

}
Admin_Pages::hooks();
