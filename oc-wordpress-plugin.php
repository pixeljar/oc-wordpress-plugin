<?php
/**
 * Plugin Name: OC WordPress Plugin
 * Description: This plugin shows many examples of core WordPress functionality that could be built into a plugin.
 * Plugin URI: https://www.pixeljar.com
 * Author: Pixel Jar
 * Author URI: http://www.pixeljar.com
 * Version: 1.0
 * License: GPL2
 * Text Domain: ocwp
 * Domain Path: /lang
 *
 * Copyright (C) Dec 10, 2019  Pixel Jar  info@pixeljar.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package WordPress
 */

// Only proceed if this file is being loaded through WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'This file must not be called directly.' );
}

// Check for minimum PHP Version.
if ( ! function_exists( 'is_php_version_compatible' ) || ! is_php_version_compatible( '5.3.0' ) ) {

	add_action(
		'admin_notices',
		function() {

			$class   = 'notice notice-error';
			$message = __( 'Your site does not meet the minimum PHP version to run this plugin.', 'ocwp' );
			echo sprintf(
				'<div class="%s"><p>%s</p></div>',
				esc_attr( $class ),
				esc_html( $message )
			);

		}
	);
	return false;

}

// Check for minimum WordPress Version.
if (  ! function_exists( 'is_wp_version_compatible' ) ||  ! is_wp_version_compatible( '5.2.0' ) ) {

	add_action(
		'admin_notices',
		function() {

			$class   = 'notice notice-error';
			$message = __( 'Your site does not meet the minimum WordPress version to run this plugin.', 'ocwp' );
			echo sprintf(
				'<div class="%s"><p>%s</p></div>',
				esc_attr( $class ),
				esc_html( $message )
			);

		}
	);
	return false;

}

define( 'OCWP_MAIN_FILE', __FILE__ );
define( 'OCWP_URL', plugin_dir_url( __FILE__ ) );
define( 'OCWP_PATH', plugin_dir_path( __FILE__ ) );
define( 'OCWP_LANG', OCWP_PATH . 'lang/' );
define( 'OCWP_INC', OCWP_PATH . 'includes/' );

// Internationalization.
require_once OCWP_INC . 'class-internationalization.php';

// Admin Pages.
require_once OCWP_INC . 'class-admin-pages.php';

// Embeds.
require_once OCWP_INC . 'class-frontend-options.php';
require_once OCWP_INC . 'class-setting-embeds.php';

// Custom Post Type.
require_once OCWP_INC . 'class-custom-post-type.php';

// WordPress Cron.
require_once OCWP_INC . 'class-cron.php';

// Post to Webservice.
require_once OCWP_INC . 'class-post-to-webservice.php';

// Register an activation hook to set up post type permalinks.
register_activation_hook( __FILE__, '\ocwp\Custom_Post_Type::activation' );
register_deactivation_hook( __FILE__, '\ocwp\Custom_Post_Type::deactivation' );
