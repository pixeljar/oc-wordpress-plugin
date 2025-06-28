<?php
/**
 * This file contains the Customizer controller class.
 *
 * @package ocwp
 */

namespace ocwp;

/**
 * This class handles the routing for the dashboard experience.
 */
class Customizer {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public static function hooks() {

		// Main ocwp Panel.
		add_action( 'customize_register', [ __CLASS__, 'customize_panels' ], 10 );

		// General Settings Section.
		add_action( 'customize_register', [ __CLASS__, 'customize_sections' ], 20 );

		// General Settings Controls.
		add_action( 'customize_register', [ __CLASS__, 'general_controls' ], 30 );

		// Banner Text Controls.
		add_action( 'customize_register', [ __CLASS__, 'banner_text_controls' ], 50 );

		// Non-EU Controls.
		add_action( 'customize_register', [ __CLASS__, 'non_eu_controls' ], 60 );

		// Multi-language Controls.
		add_action( 'customize_register', [ __CLASS__, 'multi_language_controls' ], 60 );

		add_action( 'wp_head', [ __CLASS__, 'preview_css' ], 100 );

		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_global_styles' ] );
		add_action( 'wp_footer', [ __CLASS__, 'customize_item' ] );

	}

	public static function customize_panels( $wp_customize ) {

		require_once OCWP_CONTROLS . 'class-text-radio-button-control.php';
		require_once OCWP_CONTROLS . 'class-checkbox-multiple-control.php';
		require_once OCWP_INC . 'class-customizer-model.php';

		$wp_customize->add_panel(
			'ocwp_consent_banner',
			array(
				'title'       => esc_html__( 'OCWP - Customizer', 'ocwp' ),
				'description' => esc_html__( 'This is where you edit all of the visual settings of the plugin', 'ocwp' ),
			)
		);

	}

	public static function customize_sections( $wp_customize ) {

		// General Settings.
		$wp_customize->add_section(
			'ocwp_general',
			[
				'panel'       => 'ocwp_consent_banner',
				'title'       => esc_html__( 'General', 'ocwp' ),
				'description' => esc_html__( 'This is where you edit all of the visual settings of the plugin', 'ocwp' ),
			]
		);

		// Banner Text.
		$wp_customize->add_section(
			'ocwp_banner_text',
			[
				'panel' => 'ocwp_consent_banner',
				'title' => esc_html__( 'Banner Text', 'ocwp' ),
			]
		);

		// Non-EU.
		$wp_customize->add_section(
			'ocwp_non_eu',
			[
				'panel' => 'ocwp_consent_banner',
				'title' => esc_html__( 'Non-EU', 'ocwp' ),
			]
		);

		// Multi-language.
		$wp_customize->add_section(
			'ocwp_multi_language',
			[
				'panel' => 'ocwp_consent_banner',
				'title' => esc_html__( 'Multi-language', 'ocwp' ),
			]
		);

	}

	public static function general_controls( $wp_customize ) {

		// BEGIN Display Banner.
		$wp_customize->add_setting(
			'ocwp_banner_text',
			[
				'type'              => 'option',
				'default'           => 'Lorem Ipsum...',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field',
			]
		);
		$wp_customize->add_control(
			'ocwp_banner_text',
			[
				'label'   => esc_html__( 'Banner Text', 'ocwp' ),
				'section' => 'ocwp_general',
				'type'    => 'textarea',
			]
		);
		// END Display Banner.

		// BEGIN Display Banner.
		$wp_customize->add_setting(
			'ocwp_display_banner',
			[
				'type'              => 'option',
				'default'           => 'no',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_yes_no' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_display_banner',
			[
				'label'   => esc_html__( 'Do you want to display the consent banner on your website?', 'ocwp' ),
				'section' => 'ocwp_general',
				'type'    => 'radio',
				'choices'     => [
					'yes' => __( 'Yes', 'ocwp' ),
					'no'  => __( 'No', 'ocwp' ),
				],
			]
		);
		// END Display Banner.

		// BEGIN Color Theme.
		$wp_customize->add_setting(
			'ocwp_color_theme',
			[
				'type'              => 'option',
				'default'           => 'blue',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_color_theme' ],
			]
		);
		$color_choices = [
			'light'     => __( 'Light', 'ocwp' ),
			'blue'      => __( 'Blue', 'ocwp' ),
			'green'     => __( 'Green', 'ocwp' ),
			'red'       => __( 'Red', 'ocwp' ),
			'black'     => __( 'Black', 'ocwp' ),
			'navy_blue' => __( 'Navy Blue', 'ocwp' ),
			'custom'    => __( 'Custom', 'ocwp' ),
		];
		$wp_customize->add_control(
			'ocwp_color_theme',
			[
				'label'       => esc_html__( 'Color Theme', 'ocwp' ),
				'description' => esc_html__( 'Choose your color theme for the consent banner.', 'ocwp' ),
				'section'     => 'ocwp_general',
				'type'        => 'select',
				'choices'     => $color_choices,
			]
		);
		// END Color Theme.

		// BEGIN Font Family.
		$wp_customize->add_setting(
			'ocwp_font_family',
			[
				'type'              => 'option',
				'default'           => 'Helvetica',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_font_family' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_font_family',
			[
				'label'           => esc_html__( 'Font Family', 'ocwp' ),
				'description'     => esc_html__( 'Choose the font to be used in your consent banner.', 'ocwp' ),
				'section'         => 'ocwp_general',
				'active_callback' => [ __CLASS__, 'is_custom' ],
				'type'            => 'select',
				'choices'         => [
					'Arial'           => 'Arial',
					'Helvetica'       => 'Helvetica',
					'Times New Roman' => 'Times New Roman',
					'Times'           => 'Times',
					'Courier New'     => 'Courier New',
					'Courier'         => 'Courier',
					'Verdana'         => 'Verdana',
					'Georgia'         => 'Georgia',
					'Palatino'        => 'Palatino',
					'Garamond'        => 'Garamond',
					'Bookman'         => 'Bookman',
					'Comic Sans MS'   => 'Comic Sans MS',
					'Trebuchet MS'    => 'Trebuchet MS',
					'Arial Black'     => 'Arial Black',
					'Impact'          => 'Impact',
				],
			]
		);
		// END Font Family.

		// BEGIN Font Size.
		$wp_customize->add_setting(
			'ocwp_font_size',
			[
				'type'              => 'option',
				'default'           => '10px',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_font_size' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_font_size',
			[
				'label'           => esc_html__( 'Font Size', 'ocwp' ),
				'description'     => esc_html__( 'Choose the font size to be used in your consent banner.', 'ocwp' ),
				'section'         => 'ocwp_general',
				'active_callback' => [ __CLASS__, 'is_custom' ],
				'type'            => 'select',
				'choices'         => [
					'8px'  => __( '8px', 'ocwp' ),
					'9px'  => __( '9px', 'ocwp' ),
					'10px' => __( '10px', 'ocwp' ),
					'11px' => __( '11px', 'ocwp' ),
					'12px' => __( '12px', 'ocwp' ),
					'14px' => __( '14px', 'ocwp' ),
					'18px' => __( '18px', 'ocwp' ),
				],
			]
		);
		// END Font Size.

		// BEGIN Font Color.
		$wp_customize->add_setting(
			'ocwp_font_color',
			[
				'type'              => 'option',
				'default'           => '#ffffff',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_color' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_font_color',
			[
				'label'           => esc_html__( 'Font Color', 'ocwp' ),
				'description'     => esc_html__( 'Choose the color of the font in your consent banner.', 'ocwp' ),
				'section'         => 'ocwp_general',
				'active_callback' => [ __CLASS__, 'is_custom' ],
				'type'            => 'color',
			]
		);
		// END Font Color.

		// BEGIN Button Background Color.
		$wp_customize->add_setting(
			'ocwp_button_bg_color',
			[
				'type'              => 'option',
				'default'           => '#ffff00',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_color' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_button_bg_color',
			[
				'label'           => esc_html__( 'Button Background Color', 'ocwp' ),
				'description'     => esc_html__( 'Choose the background color for the buttons in your consent banner.', 'ocwp' ),
				'section'         => 'ocwp_general',
				'active_callback' => [ __CLASS__, 'is_custom' ],
				'type'            => 'color',
			]
		);
		// END Button Background Color.

		// BEGIN Button Text Color.
		$wp_customize->add_setting(
			'ocwp_button_text_color',
			[
				'type'              => 'option',
				'default'           => '#333333',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_color' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_button_text_color',
			[
				'label'           => esc_html__( 'Button Text Color', 'ocwp' ),
				'description'     => esc_html__( 'Choose the text color for the buttons in your consent banner.', 'ocwp' ),
				'section'         => 'ocwp_general',
				'active_callback' => [ __CLASS__, 'is_custom' ],
				'type'            => 'color',
			]
		);
		// END Button Text Color.

		// BEGIN Banner Background Color.
		$wp_customize->add_setting(
			'ocwp_banner_bg_color',
			[
				'type'              => 'option',
				'default'           => '#209ce9',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_color' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_banner_bg_color',
			[
				'label'           => esc_html__( 'Banner Background Color', 'ocwp' ),
				'description'     => esc_html__( 'Choose the background color for the background of your consent banner.', 'ocwp' ),
				'section'         => 'ocwp_general',
				'active_callback' => [ __CLASS__, 'is_custom' ],
				'type'            => 'color',
			]
		);
		// END Banner Background Color.

		// BEGIN Display Style.
		$wp_customize->add_setting(
			'ocwp_display_style',
			[
				'type'              => 'option',
				'default'           => 'banner',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_display_style' ],
			]
		);
		$wp_customize->add_control(
			new Text_Radio_Button_Control(
				$wp_customize,
				'ocwp_display_style',
				[
					'label'       => esc_html__( 'Display Style', 'ocwp' ),
					'description' => esc_html__( 'Choose how you would like to display the consent banner.', 'ocwp' ),
					'section'     => 'ocwp_general',
					'choices'     => [
						'banner'  => __( 'Banner', 'ocwp' ),
						'modal'   => __( 'Modal', 'ocwp' ),
						'tooltip' => __( 'Tooltip', 'ocwp' ),
					],
				]
			)
		);
		// END Display Style.

		// BEGIN Position.
		$wp_customize->add_setting(
			'ocwp_banner_position',
			[
				'type'              => 'option',
				'default'           => 'bottom',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_position' ],
			]
		);
		$wp_customize->add_control(
			new Text_Radio_Button_Control(
				$wp_customize,
				'ocwp_banner_position',
				[
					'label'           => esc_html__( 'Position', 'ocwp' ),
					'description'     => esc_html__( 'Choose where you want the consent banner to be placed.', 'ocwp' ),
					'section'         => 'ocwp_general',
					'active_callback' => [ __CLASS__, 'is_banner' ],
					'choices'         => [
						'bottom' => __( 'Bottom', 'ocwp' ),
						'top'    => __( 'Top', 'ocwp' ),
					],
				]
			)
		);
		$wp_customize->add_setting(
			'ocwp_tooltip_position',
			[
				'type'              => 'option',
				'default'           => 'bottom',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_position' ],
			]
		);
		$wp_customize->add_control(
			new Text_Radio_Button_Control(
				$wp_customize,
				'ocwp_tooltip_position',
				[
					'label'           => esc_html__( 'Position', 'ocwp' ),
					'description'     => esc_html__( 'Choose where you want the tooltip to be placed.', 'ocwp' ),
					'section'         => 'ocwp_general',
					'active_callback' => [ __CLASS__, 'is_tooltip' ],
					'choices'         => [
						'bottom_left'  => __( 'Bottom Left', 'ocwp' ),
						'bottom_right' => __( 'Bottom Right', 'ocwp' ),
						'top_left'     => __( 'Top Left', 'ocwp' ),
						'top_right'    => __( 'Top Right', 'ocwp' ),
					],
				]
			)
		);
		// END Position.

	}

	public static function banner_text_controls( $wp_customize ) {

		// BEGIN Content Personalization.
		$wp_customize->add_setting(
			'ocwp_content_personalization',
			[
				'type'              => 'option',
				'default'           => 'no',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_yes_no' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_content_personalization',
			[
				'label'   => esc_html__( 'Do you currently use or plan to use content personalization on your website?', 'ocwp' ),
				'section' => 'ocwp_banner_text',
				'type'    => 'radio',
				'choices'     => [
					'yes' => __( 'Yes', 'ocwp' ),
					'no'  => __( 'No', 'ocwp' ),
				],
			]
		);
		// END Content Personalization.

		// BEGIN Targeted Advertising.
		$wp_customize->add_setting(
			'ocwp_targeted_advertising',
			[
				'type'              => 'option',
				'default'           => 'no',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_yes_no' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_targeted_advertising',
			[
				'label'   => esc_html__( 'Do you currently run or anticipate running targeted advertising, such as via Google Adsense, on your website?', 'ocwp' ),
				'section' => 'ocwp_banner_text',
				'type'    => 'radio',
				'choices'     => [
					'yes' => __( 'Yes', 'ocwp' ),
					'no'  => __( 'No', 'ocwp' ),
				],
			]
		);
		// END Targeted Advertising.

		// BEGIN Shared User Data.
		$wp_customize->add_setting(
			'ocwp_shared_user_data',
			[
				'type'              => 'option',
				'default'           => 'no',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_yes_no' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_shared_user_data',
			[
				'label'   => esc_html__( 'Do you currently share or plan to share user data with third-party social media platforms?', 'ocwp' ),
				'section' => 'ocwp_banner_text',
				'type'    => 'radio',
				'choices'     => [
					'yes' => __( 'Yes', 'ocwp' ),
					'no'  => __( 'No', 'ocwp' ),
				],
			]
		);
		// END Shared User Data.

	}

	public static function non_eu_controls( $wp_customize ) {

		// BEGIN Scrolling Visitor Consent.
		$wp_customize->add_setting(
			'ocwp_scrolling_visitor_consent',
			[
				'type'              => 'option',
				'default'           => 'no',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_yes_no' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_scrolling_visitor_consent',
			[
				'label'   => esc_html__( 'Visitor consent is received upon page scroll (recommended)', 'ocwp' ),
				'section' => 'ocwp_non_eu',
				'type'    => 'radio',
				'choices'     => [
					'yes' => __( 'Yes', 'ocwp' ),
					'no'  => __( 'No', 'ocwp' ),
				],
			]
		);
		// END Scrolling Visitor Consent.

		// BEGIN Display For Non-EU.
		$wp_customize->add_setting(
			'ocwp_display_for_non_eu',
			[
				'type'              => 'option',
				'default'           => 'no',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_yes_no' ],
			]
		);
		$wp_customize->add_control(
			'ocwp_display_for_non_eu',
			[
				'label'   => esc_html__( 'Display consent banner for non-EU users', 'ocwp' ),
				'section' => 'ocwp_non_eu',
				'type'    => 'radio',
				'choices'     => [
					'yes' => __( 'Yes', 'ocwp' ),
					'no'  => __( 'No', 'ocwp' ),
				],
			]
		);
		// END Display For Non-EU.

	}

	public static function multi_language_controls( $wp_customize ) {

		// BEGIN Language Detection
		$wp_customize->add_setting(
			'ocwp_language_detection',
			[
				'type'              => 'option',
				'default'           => 'no',
				'transport'         => 'refresh',
				'sanitize_callback' => [ new Customizer_Model(), 'sanitize_language_detection' ],
			]
		);
		$wp_customize->add_control(
			new Checkbox_Multiple_Control(
				$wp_customize,
				'ocwp_language_detection',
				[
					'label'   => esc_html__( 'Select additional languages that should be auto-detected based on visitor IP address. Note: English is always on.', 'ocwp' ),
					'section' => 'ocwp_multi_language',
					'type'    => 'checkbox-multiple',
					'choices' => [
						'de' => __( 'German', 'ocwp' ),
						'fr' => __( 'French', 'ocwp' ),
						'it' => __( 'Italian', 'ocwp' ),
						'es' => __( 'Spanish', 'ocwp' ),
					],
				]
			)
		);
		// END Language Detection

	}

	public static function preview_css() {

		if ( ! is_customize_preview() ) {
			return;
		}

		global $wp_customize;

		if ( 'custom' === $wp_customize->get_setting( 'ocwp_color_theme' )->value() ) {

			$font_family       = $wp_customize->get_setting( 'ocwp_font_family' )->value();
			$font_size         = $wp_customize->get_setting( 'ocwp_font_size' )->value();
			$font_color        = $wp_customize->get_setting( 'ocwp_font_color' )->value();
			$button_bg_color   = $wp_customize->get_setting( 'ocwp_button_bg_color' )->value();
			$button_text_color = $wp_customize->get_setting( 'ocwp_button_text_color' )->value();
			$banner_bg_color   = $wp_customize->get_setting( 'ocwp_banner_bg_color' )->value();

		} else {

			$color             = $wp_customize->get_setting( 'ocwp_color_theme' )->value();
			$font_family       = 'Arial';
			$font_size         = '18px';
			$font_color        = 'white';
			$button_bg_color   = 'blue';
			$button_text_color = 'white';
			$banner_bg_color   = 'blue';

		}

		$display_style = $wp_customize->get_setting( 'ocwp_display_style' )->value();

		echo '<style id="ocwp-customizer-live-preview">';
		echo '/* OCWP BANNER PREVIEW */';
		if ( 'banner' === $display_style ) {

			echo '.ocwp-banner.banner {';
				echo 'height: 100px;';
				echo 'overflow: scroll;';
				echo 'width: 100%;';

			switch ( $wp_customize->get_setting( 'ocwp_banner_position' )->value() ) {

				case 'top':
					echo 'bottom: auto !important;';
					echo 'top: 0 !important;';
					break;

				case 'bottom':
					echo 'bottom: 0 !important;';
					echo 'top: auto !important;';
					break;

			}

			echo '}';

		}
		echo '.ocwp-banner.banner[style] {';
			printf( 'background: %s !important;', $banner_bg_color );
			printf( 'color: %s !important;', $font_color );
			printf( 'font-family: \'%s\' !important;', $font_family );
			printf( 'font-size: %s !important;', $font_size );
		echo '}';
		echo '.ocwp-banner.banner a[style] {';
			printf( 'color: %s !important;', $font_color );
			printf( 'font-family: \'%s\' !important;', $font_family );
			printf( 'font-size: %s !important;', $font_size );
		echo '}';
		echo '.ocwp-banner.banner button[style] {';
			printf( 'background: %s !important;', $button_bg_color );
			printf( 'color: %s !important;', $button_text_color );
			printf( 'font-family: \'%s\' !important;', $font_family );
			printf( 'font-size: %s !important;', $font_size );
		echo '}';

		echo '/* OCWP MODAL PREVIEW */';
		echo '.ocwp-banner.modal[style] {';
			echo 'height: 400px;';
			echo 'overflow: scroll;';
			echo 'width: 400px;';

			printf( 'background: %s !important;', $banner_bg_color );
			printf( 'color: %s !important;', $font_color );
			printf( 'font-family: \'%s\' !important;', $font_family );
			printf( 'font-size: %s !important;', $font_size );
		echo '}';
		echo '.ocwp-banner.modal a[style] {';
			printf( 'color: %s !important;', $font_color );
			printf( 'font-family: \'%s\' !important;', $font_family );
			printf( 'font-size: %s !important;', $font_size );
		echo '}';
		echo '.ocwp-banner.modal button[style] {';
			printf( 'background: %s !important;', $button_bg_color );
			printf( 'color: %s !important;', $button_text_color );
			printf( 'font-family: \'%s\' !important;', $font_family );
			printf( 'font-size: %s !important;', $font_size );
		echo '}';

		echo '/* OCWP TOOLTIP PREVIEW */';
		if ( 'tooltip' === $display_style ) {

			echo '.ocwp-banner.tooltip {';
				echo 'height: 200px;';
				echo 'overflow: scroll;';
				echo 'width: 200px;';

			switch ( $wp_customize->get_setting( 'ocwp_tooltip_position' )->value() ) {

				case 'bottom_left':
					echo 'bottom:24px;';
					echo 'left:24px;';
					echo 'right:auto;';
					echo 'top:auto;';
					break;

				case 'bottom_right':
					echo 'bottom:24px;';
					echo 'left:auto;';
					echo 'right:24px;';
					echo 'top:auto;';
					break;

				case 'top_left':
					echo 'bottom:auto;';
					echo 'left:24px;';
					echo 'right:auto;';
					echo 'top:24px;';
					break;

				case 'top_right':
					echo 'bottom:auto;';
					echo 'left:auto;';
					echo 'right:24px;';
					echo 'top:24px;';
					break;

			}

			echo '}';

		}
		echo '.ocwp-banner.tooltip[style] {';
			printf( 'background: %s !important;', $banner_bg_color );
			printf( 'color: %s !important;', $font_color );
			printf( 'font-family: \'%s\' !important;', $font_family );
			printf( 'font-size: %s !important;', $font_size );
		echo '}';
		echo '.ocwp-banner.tooltip a[style] {';
			printf( 'color: %s !important;', $font_color );
			printf( 'font-family: \'%s\' !important;', $font_family );
			printf( 'font-size: %s !important;', $font_size );
		echo '}';
		echo '.ocwp-banner.tooltip button[style] {';
			printf( 'background: %s !important;', $button_bg_color );
			printf( 'color: %s !important;', $button_text_color );
			printf( 'font-family: \'%s\' !important;', $font_family );
			printf( 'font-size: %s !important;', $font_size );
		echo '}';
		echo '</style>';
	}

	public static function is_free_and_custom( $control ) {

		if ( 'custom' === $control->manager->get_setting( 'ocwp_color_theme' )->value() ) {

			return true;

		}

		return false;

	}

	public static function is_custom( $control ) {

		if ( 'custom' === $control->manager->get_setting( 'ocwp_color_theme' )->value() ) {

			return true;

		}

		return false;

	}

	public static function is_banner( $control ) {

		if ( 'banner' === $control->manager->get_setting( 'ocwp_display_style' )->value() ) {

			return true;

		}

		return false;

	}

	public static function is_tooltip( $control ) {

		if ( 'tooltip' === $control->manager->get_setting( 'ocwp_display_style' )->value() ) {

			return true;

		}

		return false;

	}

	public static function enqueue_global_styles() {

		if ( 'yes' !== get_option( 'ocwp_display_banner', 'no' ) && ! is_admin() && ! is_customize_preview() ) {
			return;
		}

		if ( is_customize_preview() ) {

			global $wp_customize;

			if ( 'no' === $wp_customize->get_setting( 'ocwp_display_banner' )->value() ) {
				return;
			}

		}

		wp_enqueue_style(
			'ocwp-frontend',
			OCWP_URL . 'assets/css/frontend.css',
			[],
			OCWP_VERSION
		);

	}

	public static function customize_item() {

		if ( 'yes' !== get_option( 'ocwp_display_banner', 'no' ) && ! is_admin() && ! is_customize_preview() ) {
			return;
		}

		$class = get_option( 'ocwp_display_style', 'banner' );
		$content = get_option( 'ocwp_banner_text', 'Lorem Ipsum...' );

		if ( is_customize_preview() ) {

			global $wp_customize;

			if ( 'no' === $wp_customize->get_setting( 'ocwp_display_banner' )->value() ) {
				return;
			}

			$class = $wp_customize->get_setting( 'ocwp_display_style' )->value();
			$content = $wp_customize->get_setting( 'ocwp_banner_text' )->value();

		}

		echo sprintf(
			'<div class="ocwp-banner %s">%s</div>',
			esc_attr( $class ),
			esc_html( $content )
		);
	}

}
Customizer::hooks();
