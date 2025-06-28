<?php
/**
 * This file contains the Banner Settings model class.
 *
 * @package ocwp
 */

namespace ocwp;

/**
 * This class handles the routing for the dashboard experience.
 */
class Customizer_Model {

	public static function sanitize_color_theme( $value = '' ) {

		$default = 'blue';
		$allowed_colors = [ 'light', 'blue', 'green', 'red', 'black', 'navy_blue', 'custom' ];
		if ( ! in_array( trim( $value ), $allowed_colors, true ) ) {

			return $default;

		}

		return $value;

	}

	public static function sanitize_font_family( $value = '' ) {

		$default = 'Helvetica';
		if ( ! in_array( trim( $value ), [ 'Arial', 'Helvetica', 'Times New Roman', 'Times', 'Courier New', 'Courier', 'Verdana', 'Georgia', 'Palatino', 'Garamond', 'Bookman', 'Comic Sans MS', 'Trebuchet MS', 'Arial Black', 'Impact' ], true ) ) {

			return $default;

		}

		return $value;

	}

	public static function sanitize_font_size( $value = '' ) {

		$default = '10px';
		if ( ! in_array( trim( $value ), [ '8px', '9px', '10px', '11px', '12px', '14px', '18px' ], true ) ) {

			return $default;

		}

		return $value;

	}

	public static function sanitize_display_style( $value = '' ) {

		$default = 'banner';
		if ( ! in_array( trim( $value ), [ 'banner', 'modal', 'tooltip' ], true ) ) {

			return $default;

		}

		return $value;

	}

	public static function sanitize_position( $value = '' ) {

		$default = 'bottom';
		if ( ! in_array( trim( $value ), [ 'bottom', 'bottom_left', 'bottom_right', 'top', 'top_left', 'top_right', ], true ) ) {

			return $default;

		}

		return $value;

	}

	public static function sanitize_yes_no( $value = '' ) {

		$default = 'no';
		if ( ! in_array( trim( $value ), [ 'yes', 'no' ], true ) ) {

			return $default;

		}

		return $value;

	}

	public static function sanitize_color( $value = '' ) {

		return sanitize_hex_color( $value );

	}

	public static function sanitize_language_detection( $value = '' ) {

		$values = explode( ',', $value );
		if ( is_array( $values ) && ! empty( $values ) ) {

			foreach ( $values as &$v ) {

				if ( ! in_array( $v, [ 'de', 'fr', 'it', 'es' ], true ) ) {

					unset( $v );

				}

			}

		} else {

			$values = [ $value ];
		}

		return $values;

	}

}
