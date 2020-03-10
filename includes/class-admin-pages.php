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
		?>

		<div class="wrap">
			<!-- ALL PAGE CONTENT GOES HERE -->
		</div>

		<?php
	}

}
Admin_Pages::hooks();
