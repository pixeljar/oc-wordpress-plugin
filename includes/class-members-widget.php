<?php
/**
 * This file shows how to work with custom post types.
 *
 * @package ocwp
 */

namespace ocwp;

use WP_Widget;

/**
 * Undocumented class
 */
class Members_Widget extends WP_Widget {

	/**
	 * Sets up the widget.
	 */
	public function __construct() {

		// Set up the widget options.
		$widget_options = array(
			'classname'                   => 'ocwp-members-widget',
			'description'                 => __( 'Displays a random list of OCWP Members.', 'ocwp' ),
			'customize_selective_refresh' => true,
		);

		// Create the widget.
		parent::__construct(
			'ocwp-members-widget',
			__( 'OCWP Members Widget', 'ocwp' ),
			$widget_options
		);
	}

	/**
	 * Widget output on the front-end.
	 *
	 * @param array $sidebar The sidebar settings.
	 * @param array $instance The widget settings.
	 * @return void
	 */
	public function widget( $sidebar, $instance ) {


		// Open the sidebar widget wrapper.
		echo $sidebar['before_widget'];

		// Output the widget title if set.
		if ( ! empty( $instance['title'] ) ) {

			// Open the sidebar widget title wrapper.
			echo $sidebar['before_title'];

			// Apply filters and output widget title.
			echo esc_html(
				apply_filters(
					'widget_title',
					$instance['title'],
					$instance,
					$this->id_base
				)
			);

			// Close the sidebar widget title wrapper.
			echo $sidebar['after_title'];
		}

		$defaults = [
			'num_members'    => 5,
			'show_blog_link' => '1',
			'order'          => 'desc',
			'orderby'        => 'rand',
		];

		$instance = wp_parse_args(
			$instance,
			$defaults
		);

		$member_args = [
			'post_type'      => 'ocwp-member',
			'posts_per_page' => $instance['num_members'],
			'order'          => strtoupper( $instance['order'] ),
			'orderby'        => $instance['orderby'],
		];

		$members = get_posts( $member_args );
		if ( count( $members ) > 0 ) {

			// Output members.
			echo wp_kses_post( '<ul>' );

			foreach ( $members as $member ) {

				printf(
					'<h3>%s%s</h3>',
					get_avatar( get_post_meta( $member->ID, 'email', true ), 50, 'retro', '', [ 'class' => 'alignleft' ] ),
					esc_html( get_the_title( $member->ID ) )
				);

				if ( 1 === $instance['show_blog_link'] ) {

					printf(
						'<a href="%s" target="_blank">%s</a>',
						esc_attr( get_post_meta( $member->ID, 'blog_link', true ) ),
						esc_html__( 'View Blog', 'ocwp' )
					);

				}

			}

			echo wp_kses_post( '</ul>' );

		} else {

			printf(
				'<p>%s</p>',
				esc_html__( 'No members found.', 'ocwp' )
			);

		}

		// Close the sidebar widget wrapper.
		echo $sidebar['after_widget'];
	}

	/**
	 * Outputs the optiosn in the admin.
	 *
	 * @param array $instance The values for the widget.
	 * @return void
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance,
			[
				'title'          => __( 'Our Members', 'ocwp' ),
				'num_members'    => '5',
				'show_blog_link' => '0',
				'orderby'        => 'rand', // date, title, rand.
				'order'          => 'desc', // asc, desc.
			]
		); ?>

		<p>
			<label>
				<?php esc_html_e( 'Title:', 'ocwp' ); ?>
				<input
					type="text"
					class="widefat"
					name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
					value="<?php echo esc_attr( $instance['title'] ); ?>"
				/>
			</label>
		</p>

		<p>
			<label>
				<?php esc_html_e( 'Num Members:', 'ocwp' ); ?>
				<input
					type="text"
					class="widefat"
					name="<?php echo esc_attr( $this->get_field_name( 'num_members' ) ); ?>"
					value="<?php echo esc_attr( $instance['num_members'] ); ?>"
				/>
			</label>
		</p>

		<p>
			<label>
				<input
					type="checkbox"
					name="<?php echo esc_attr( $this->get_field_name( 'show_blog_link' ) ); ?>"
					value="1"
					<?php checked( "1", $instance['show_blog_link'], true ); ?>
				/>
				<?php esc_html_e( 'Show Blog Link?', 'ocwp' ); ?>
			</label>
		</p>

		<p>
			<label>
				<?php esc_html_e( 'Order By:', 'ocwp' ); ?>
				<select
					name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>"
					class="widefat"
				>
					<option value="date" <?php selected( "date", $instance['orderby'], true ); ?>><?php esc_html_e( 'Date', 'ocwp' ); ?></option>
					<option value="title" <?php selected( "title", $instance['orderby'], true ); ?>><?php esc_html_e( 'Title', 'ocwp' ); ?></option>
					<option value="rand" <?php selected( "rand", $instance['orderby'], true ); ?>><?php esc_html_e( 'Random', 'ocwp' ); ?></option>
				</select>
			</label>
		</p>

		<p>
			<label>
				<?php esc_html_e( 'Order:', 'ocwp' ); ?>
				<select
					name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>"
					class="widefat"
				>
					<option value="asc" <?php selected( "asc", $instance['order'], true ); ?>><?php esc_html_e( 'Ascending', 'ocwp' ); ?></option>
					<option value="desc" <?php selected( "desc", $instance['order'], true ); ?>><?php esc_html_e( 'Descending', 'ocwp' ); ?></option>
				</select>
			</label>
		</p>
	<?php }

	/**
	 * Update the values of the widget.
	 *
	 * @param array $new_instance The new values being submitted.
	 * @param array $old_instance The old values for comparison.
	 * @return void
	 */
	public function update( $new_instance, $old_instance ) {

		// Create empty array to store sanitized data.
		$instance = [];

		// Sanitize data from widget form.
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['num_members'] = intval( $new_instance['num_members'] );

		if ( ! isset( $new_instance['show_blog_link'] ) ) {
			$instance['show_blog_link'] = 0;
		} else {
			$instance['show_blog_link'] = 1;
		}

		if ( in_array( $new_instance['orderby'], [ 'date', 'title', 'rand' ], true ) ) {
			$instance['orderby'] = $new_instance['orderby'];
		}

		if ( in_array( $new_instance['order'], [ 'asc', 'desc' ], true ) ) {
			$instance['order'] = $new_instance['order'];
		}

		// Return sanitized data.
		return $instance;
	}

}

add_action(
	'widgets_init',
	function() {
		register_widget( '\ocwp\Members_Widget' );
	}
);
