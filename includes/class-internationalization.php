<?php
/**
 * Reusable i18n class.
 *
 * @package ocwp
 */

namespace ocwp;

/**
 * The Internationalization class sets up this WordPress plugin to be translated.
 */
class Internationalization {

	/**
	 * This static method can be called to hook into WordPress to initialize translations.
	 *
	 * @return void
	 */
	public static function hooks() {

		add_action( 'plugins_loaded', [ __CLASS__, 'init' ] );

	}

	/**
	 * Once all plugins have been loaded, translate any strings we have in our plugin.
	 *
	 * @return void
	 */
	public static function init() {

		load_plugin_textdomain( 'ocwp', false, OCWP_LANG );

	}

}

// Start the i18n process.
Internationalization::hooks();
