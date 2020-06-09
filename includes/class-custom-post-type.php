<?php
/**
 * This file shows how to work with custom post types.
 *
 * @package ocwp
 */

namespace ocwp;

/**
 * A Members Custom Post Type.
 */
class Custom_Post_Type {

	/**
	 * This static method can be called to hook into WordPress to register post types.
	 *
	 * @return void
	 */
	public static function hooks() {

		// Register the post type.
		add_action( 'init', [ __CLASS__, 'register_post_type' ] );

		// Adds the blog link to the content area.
		add_filter( 'the_content', [ __CLASS__, 'blog_link' ] );

		// Redirect to the user's blog.
		add_action( 'template_redirect', [ __CLASS__, 'blog_redirect' ] );

		// Add Meta Boxes.
		add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_boxes' ] );

		// Save the post meta.
		add_action( 'save_post_ocwp-member', [ __CLASS__, 'save_post' ] );

	}

	/**
	 * Flush the permalinks when you activate this plugin to add the post type to the rewrite rules.
	 *
	 * @return void
	 */
	public static function activation() {

		// Register the post type during activation.
		self::register_post_type();

		// ATTENTION: This is *only* done during plugin activation hook in this example!
		// You should *NEVER EVER* do this on every page load!!
		flush_rewrite_rules();

	}

	/**
	 * Flush the permalinks when you deactivate this plugin to remove the post type fromthe rewrite rules.
	 *
	 * @return void
	 */
	public static function deactivation() {

		// ATTENTION: This is *only* done during plugin deactivation hook in this example!
		// You should *NEVER EVER* do this on every page load!!
		flush_rewrite_rules();

	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {

		// Create Labels for the Post Type.
		$labels = [
			'name'                  => _x( 'Members', 'Post Type General Name', 'ocwp' ),
			'singular_name'         => _x( 'Member', 'Post Type Singular Name', 'ocwp' ),
			'menu_name'             => __( 'Members', 'ocwp' ),
			'name_admin_bar'        => __( 'Member', 'ocwp' ),
			'archives'              => __( 'Member Archives', 'ocwp' ),
			'attributes'            => __( 'Member Attributes', 'ocwp' ),
			'parent_item_colon'     => __( 'Parent Member: ', 'ocwp' ),
			'all_items'             => __( 'All Members', 'ocwp' ),
			'add_new_item'          => __( 'Add New Member', 'ocwp' ),
			'add_new'               => __( 'Add New', 'ocwp' ),
			'new_item'              => __( 'New Member', 'ocwp' ),
			'edit_item'             => __( 'Edit Member', 'ocwp' ),
			'update_item'           => __( 'Update Member', 'ocwp' ),
			'view_item'             => __( 'View Member', 'ocwp' ),
			'view_items'            => __( 'View Members', 'ocwp' ),
			'search_items'          => __( 'Search Member', 'ocwp' ),
			'not_found'             => __( 'Not found', 'ocwp' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'ocwp' ),
			'featured_image'        => __( 'Profile Photo', 'ocwp' ),
			'set_featured_image'    => __( 'Set profile photo', 'ocwp' ),
			'remove_featured_image' => __( 'Remove profile photo', 'ocwp' ),
			'use_featured_image'    => __( 'Use as profile photo', 'ocwp' ),
			'insert_into_item'      => __( 'Insert into Member', 'ocwp' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Member', 'ocwp' ),
			'items_list'            => __( 'Members list', 'ocwp' ),
			'items_list_navigation' => __( 'Members list navigation', 'ocwp' ),
			'filter_items_list'     => __( 'Filter Members list', 'ocwp' ),
		];

		// Modify the permalinks.
		$rewrite = [
			'slug'       => 'member',
			'with_front' => false,
			'pages'      => true,
			'feeds'      => true,
			'ep_mask'    => 33554432, // 2^25
		];

		// The Post Type Arguments.
		$member_args = apply_filters(
			'ocwp_members_args',
			[
				'label'               => __( 'Member', 'ocwp' ),
				'description'         => __( 'A list of Members in our OCWP Community', 'ocwp' ),
				'labels'              => $labels,
				'supports'            => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 5,
				'menu_icon'           => 'dashicons-groups',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'rewrite'             => $rewrite,
				'capability_type'     => 'page',
				'show_in_rest'        => true,
			]
		);

		\register_post_type( 'ocwp-member', $member_args );

		// Added only to our post type.
		add_rewrite_endpoint( 'go', 33554432 );

	}

	/**
	 * Redirect to the member's blog.
	 */
	public static function blog_redirect() {

		// If this is not a special request to our endpoint.
		if ( ! is_singular( 'ocwp-member' ) || ! get_query_var( 'go' ) || 'blog' !== get_query_var( 'go' ) ) {
			return;
		}

		global $wpdb;

		// Timestamp for midnight today.
		$meta_key = '_clicks-' . mktime( 0, 0, 0, gmdate( 'n' ), gmdate( 'j' ), gmdate( 'Y' ) );

		// Track the click.
		if ( get_post_meta( get_queried_object_id(), $meta_key, true ) ) {

			$wpdb->query(
				$wpdb->prepare(
					"
					UPDATE		{$wpdb->postmeta}
					SET			meta_value = meta_value + 1
					WHERE		meta_key = %s
					AND			post_id = %s
					",
					$meta_key,
					get_queried_object_id()
				)
			);

		} else {

			update_post_meta( get_queried_object_id(), $meta_key, 1 );

		}

		// Redirect using a 301 (permanent) redirect.
		wp_redirect(
			get_post_meta(
				get_queried_object_id(),
				'blog_link',
				true
			),
			301
		);
		exit;
	}

	/**
	 * Adds the member's blog link to their listing.
	 *
	 * @param string $content The content.
	 * @return string
	 */
	public static function blog_link( $content = '' ) {

		$blog_link = get_post_meta( get_queried_object_id(), 'blog_link', true );
		if ( is_singular( 'ocwp-member' ) && '' !== $blog_link ) {

			$content .= sprintf(
				'<p>Visit my <a href="%1$s">blog</a>.</p>',
				esc_attr(
					esc_url(
						sprintf(
							'%sgo/blog',
							trailingslashit(
								get_permalink( get_queried_object_id() )
							)
						)
					)
				)
			);

		}

		return $content;
	}

	/**
	 * Adds a metabox to the OCWP Member post type.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {

		add_meta_box(
			'ocwp-member-metabox',
			__( 'Member Details', 'owcp' ),
			[ __CLASS__, 'member_details_metabox' ],
			'ocwp-member',
			'normal',
			'default'
		);

	}

	/**
	 * Outputs the content of the metabox.
	 *
	 * @param WP_Post $post The post object.
	 * @return void
	 */
	public static function member_details_metabox( $post ) {

		wp_nonce_field(
			'ocwp-member-save_postmeta',
			'ocwp-member_nonce'
		);

		printf(
			'<label>
				<span>%s</span>
				<input type="text" name="blog_link" value="%s"/>
			</label>',
			esc_html__( 'Blog Link', 'ocwp' ),
			esc_attr(
				esc_url(
					get_post_meta( $post->ID, 'blog_link', true )
				)
			)
		);

	}

	/**
	 * Saves the meta to the post.
	 *
	 * @param integer $post_id The post ID.
	 * @return void
	 */
	public static function save_post( $post_id ) {

		// Autosaves kill post meta.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// User has permission.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// verify intent.
		$nonce_field = 'ocwp-member_nonce';
		if (
			! isset( $_POST[ $nonce_field ] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $nonce_field ] ) ), 'ocwp-member-save_postmeta' )
		) {
			return $post_id;
		}

		if (
			isset( $_POST['blog_link'] )
		) {

			$blog_link = esc_url_raw( wp_unslash( $_POST['blog_link'] ) );
			update_post_meta( $post_id, 'blog_link', $blog_link );

		}

	}

}
Custom_Post_Type::hooks();
