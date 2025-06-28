<?php

namespace ocwp;

/**
 * Multiple checkbox customize control class.
 *
 * @since  1.0.0
 * @access public
 */


if ( class_exists( '\WP_Customize_Control' ) ) {

	class Checkbox_Multiple_Control extends \WP_Customize_Control {

		/**
		 * The type of customize control being rendered.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    string
		 */
		public $type = 'checkbox-multiple';

		/**
		 * Enqueue scripts/styles.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue() {

			wp_enqueue_script( 'ocwp-customize-controls', OCWP_URL . 'assets/js/customize-controls.js', [ 'jquery' ], OCWP_VERSION, true );
			wp_localize_script(
				'ocwp-customize-controls',
				'OCWP_CUSTOMIZER',
				[
					'key' => 'value',
				]
			);

		}

		/**
		 * Displays the control content.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function render_content() {

			if ( empty( $this->choices ) ) {
				return;
			}

			if ( ! empty( $this->label ) ) {
				printf( '<span class="customize-control-title">%s</span>', esc_html( $this->label ) );
			}

			if ( ! empty( $this->description ) ) {
				printf( '<span class="description customize-control-description">%s</span>', esc_html( $this->description ) );
			}

			$multi_values = ! is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value();

			?>
			<ul>
				<?php foreach ( $this->choices as $value => $label ) : ?>

					<li>
						<label>
							<input type="checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $multi_values ) ); ?> />
							<?php echo esc_html( $label ); ?>
						</label>
					</li>

				<?php endforeach; ?>
			</ul>

			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $multi_values ) ); ?>" />
			<?php

		}

	}

}
