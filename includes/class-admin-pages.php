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
				'wporg_messages',
				'wporg_message',
				__( 'Settings Saved', 'wporg' ),
				'updated'
			);

		}

		// Show error/update messages.
		settings_errors( 'wporg_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form action="options.php" method="post">
			<?php

				// Output security fields for the registered setting "wporg".
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
				'label_for'         => 'name',
				'class'             => 'ocwp_meetup_name',
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
				'label_for'         => 'url',
				'class'             => 'ocwp_meetup_url',
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

}
Admin_Pages::hooks();
