<?php
/**
 * This file handles the various embed methods for the settings API.
 *
 * @package ocwp
 */

/**
 * In this class you'll find examples of shortcodes and template tags.
 */
class Setting_Embeds {

	/**
	 * This method hooks into WordPress to register the shortcode and widget embeds.
	 *
	 * @return void
	 */
	public static function hooks() {

		add_action( 'init', [ __CLASS__, 'register_shortcodes' ] );

	}

	/**
	 * Registers all shortcodes related to our settings.
	 *
	 * @return void
	 */
	public static function register_shortcodes() {

		add_shortcode(
			'ocwp_meetup_name',
			[ __CLASS__, 'meetup_name_shortcode' ]
		);
		add_shortcode( 'ocwp_meetup_url', [ __CLASS__, 'meetup_url_shortcode' ] );
		add_shortcode( 'ocwp_meetup_open_form', [ __CLASS__, 'meetup_open_form_shortcode' ] );
		add_shortcode( 'ocwp_meetup_close_form', [ __CLASS__, 'meetup_close_form_shortcode' ] );

	}

	/**
	 * This method will route any shortcodes used through our template tag to output our Meetup Name.
	 *
	 * @param array  $atts This associative array will contain all of the attributes passed into our shortcode.
	 * @param string $content If our shortcode is wrapped around some text, it will be contained here.
	 * @return string
	 */
	public static function meetup_name_shortcode( $atts = [], $content = '' ) {

		// Set up some defaults in case they're missing from the shortcode.
		$atts = shortcode_atts(
			[
				'display' => 'value',
			],
			$atts,
			'ocwp_meetup_name'
		);

		$output = self::meetup_name_template_tag( $atts['display'], $content, true );

		return $output;

	}

	/**
	 * We route all output of the Meetup Name through this template tag so we can just make changes once.
	 *
	 * @param string  $display Display the Meetup Name or output a form field.
	 * @param string  $content The content.
	 * @param boolean $return Return the content or print to the browser.
	 * @return mixed
	 */
	public static function meetup_name_template_tag( $display = 'value', $content = '', $return = false ) {

		// Get the option using the Options API.
		$ocwp_meetup = get_option(
			'ocwp_meetup',
			[
				'name' => '',
			]
		);

		if ( 'value' === $display ) {

			if ( ! empty( $ocwp_meetup['name'] ) ) {

				$output = esc_html( $ocwp_meetup['name'] );

			} else {

				$output = esc_html__( '- not set -', 'ocwp' );

			}

		} elseif ( 'field' === $display ) {

			$output = sprintf(
				'<input type="text" name="ocwp_meetup[name]" id="ocwp_meetup_name" value="%1$s" placeholder="%2$s">',
				esc_attr( $ocwp_meetup['name'] ),
				esc_attr__( 'Please enter the name of your meetup.', 'ocwp' )
			);

		}

		if ( true === $return ) {

			return $output;

		} else {

			echo wp_kses_post( $output );

		}

	}

	/**
	 * This method will route any shortcodes used through our template tag to output our Meetup Url.
	 *
	 * @param array  $atts This associative array will contain all of the attributes passed into our shortcode.
	 * @param string $content If our shortcode is wrapped around some text, it will be contained here.
	 * @return string
	 */
	public static function meetup_url_shortcode( $atts = [], $content = '' ) {

		// Set up some defaults in case they're missing from the shortcode.
		$atts = shortcode_atts(
			[
				'display' => 'value',
				'link'    => false,
			],
			$atts,
			'ocwp_meetup_url'
		);

		$output = self::meetup_url_template_tag(
			$atts['display'],
			filter_var( $atts['link'], FILTER_VALIDATE_BOOLEAN ),
			$content,
			true
		);

		return $output;

	}

	/**
	 * We route all output of the Meetup Url through this template tag so we can just make changes once.
	 *
	 * @param string  $display Display the Meetup Url or output a form field.
	 * @param boolean $link If true, we will create a link to click.
	 * @param string  $content The content.
	 * @param boolean $return Return the content or print to the browser.
	 * @return mixed
	 */
	public static function meetup_url_template_tag( $display = 'value', $link = false, $content = '', $return = false ) {

		// Get the option using the Options API.
		$ocwp_meetup = get_option(
			'ocwp_meetup',
			[
				'url' => '',
			]
		);

		if ( 'value' === $display ) {

			if ( ! empty( $ocwp_meetup['url'] ) ) {

				if ( true === $link ) {

					$output = sprintf(
						'<a href="%1$s" target="_blank">%2$s</a>',
						esc_attr( esc_url( $ocwp_meetup['url'] ) ),
						esc_html( esc_url( $ocwp_meetup['url'] ) )
					);

				} else {

					$output = esc_html( esc_url( $ocwp_meetup['url'] ) );

				}

			} else {

				$output = esc_html__( '- url not set -', 'ocwp' );

			}

		} elseif ( 'field' === $display ) {

			$output = sprintf(
				'<input type="text" name="ocwp_meetup[url]" id="ocwp_meetup_url" value="%1$s" placeholder="%2$s">',
				esc_attr( $ocwp_meetup['url'] ),
				esc_attr__( 'Please enter the url of your meetup.', 'ocwp' )
			);

		}

		if ( true === $return ) {

			return $output;

		} else {

			echo wp_kses_post( $output );

		}

	}

	/**
	 * This method will output the opening of a form tag.
	 *
	 * @param array  $atts This associative array will contain all of the attributes passed into our shortcode.
	 * @param string $content If our shortcode is wrapped around some text, it will be contained here.
	 * @return string
	 */
	public static function meetup_open_form_shortcode( $atts = [], $content = '' ) {

		$form  = '<form method="post">';
		$form .= wp_nonce_field( 'ocwp_update_option_frontend', '_wpnonce', true, false );

		return $form;

	}

	/**
	 * This method will output the closing of a form tag.
	 *
	 * @param array  $atts This associative array will contain all of the attributes passed into our shortcode.
	 * @param string $content If our shortcode is wrapped around some text, it will be contained here.
	 * @return string
	 */
	public static function meetup_close_form_shortcode( $atts = [], $content = '' ) {

		$form  = sprintf(
			'<input type="submit" name="%s">',
			esc_attr__( 'Save Changes', 'ocwp' )
		);
		$form .= '</form>';

		return $form;

	}



}
Setting_Embeds::hooks();
