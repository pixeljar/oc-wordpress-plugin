<?php
/**
 * Handles interaction with the WP_User Object.
 *
 * @package @ocwp
 */

namespace ocwp;

/**
 * The OCWP User Class.
 *
 * @see https://developer.wordpress.org/plugins/users/
 */
class Users {

	/**
	 * Hooks into WordPress
	 *
	 * @return void
	 */
	public static function hooks() {

		// Create random users on every page load, but only if you're an admin.
		add_action( 'admin_init', [ __CLASS__, 'create_random_user' ] );

		// Shortcode to edit nicknames.
		add_shortcode( 'ocwp_edit_nickname_form', [ __CLASS__, 'edit_nickname_form' ] );

		// Handle nickname updates.
		add_action( 'init', [ __CLASS__, 'maybe_update_nickname' ] );

	}

	/**
	 * Creates a new role for OCWP Members with custom capabilities when the plugin is activated.
	 *
	 * @see https://developer.wordpress.org/plugins/users/roles-and-capabilities/
	 *
	 * @return void
	 */
	public static function activation() {

		add_role(
			'ocwp_member',
			__( 'OCWP Member', 'ocwp' ),
			[
				'ocwp-member' => true,
				'read'        => true,
				'edit'        => true,
			]
		);

	}

	/**
	 * Delets the custom OCWP Members role on deactivation.
	 *
	 * @see https://developer.wordpress.org/plugins/users/roles-and-capabilities/
	 *
	 * @return void
	 */
	public static function deactivation() {

		remove_role( 'ocwp_member' );

	}

	/**
	 * Create a random user.
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_insert_user/
	 *
	 * @return mixed
	 */
	public static function create_random_user() {

		if ( ! current_user_can( 'create_users' ) ) {

			return false;

		}

		$user_name  = time();
		$user_email = 'user+' . $user_name . '@example.com';

		$admin_color_schemes = [
			'fresh',
			'light',
			'modern',
			'blue',
			'coffee',
			'ectoplasm',
			'midnight',
			'ocean',
			'sunrise',
		];

		// Check if the username is taken.
		$user_id = username_exists( $user_name );

		// Check that the email address does not belong to a registered user.
		if ( ! $user_id && false === email_exists( $user_email ) ) {

			// Create a random password.
			$random_password = wp_generate_password( 32 );

			// Create the user.
			$user_id = wp_insert_user(
				[
					'user_login'           => $user_name,
					'user_pass'            => $random_password,
					'user_email'           => $user_email,
					'role'                 => 'ocwp_member',
					'admin_color'          => $admin_color_schemes[ array_rand( $admin_color_schemes, 1 ) ],
					'show_admin_bar_front' => false,
				]
			);

		}

	}

	/**
	 * Shortcode to embed a form for the user to edit their nickname.
	 *
	 * @param array  $atts Any shortcode attributes.
	 * @param string $content Content in between open/close tags.
	 * @return string
	 */
	public static function edit_nickname_form( $atts = [], $content = '' ) {

		ob_start();
		?>
		<form method="post">

			<!-- SECURITY -->
			<?php wp_nonce_field( 'ocwp_update_nickname' ); ?>

			<!-- FORM FIELD -->
			<label for="ocwp_nickname"><?php esc_html_e( 'Nickname', 'ocwp' ); ?></label>
			<input type="text" name="ocwp_nickname" id="ocwp_nickname" value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'nickname', true ) ); ?>">

			<!-- SUBMIT BUTTON -->
			<input type="submit" name="ocwp_nickname_submit" value="<?php esc_attr_e( 'Save Changes', 'ocwp' ); ?>">

		</form>
		<?php
		$output = ob_get_clean();

		return $output;

	}

	/**
	 * If we detect a nickname change, go ahead and process the submission.
	 *
	 * @return void
	 */
	public static function maybe_update_nickname() {

		if (
			isset( $_POST['_wpnonce'] ) &&
			isset( $_POST['ocwp_nickname'] ) &&
			wp_verify_nonce(
				sanitize_text_field(
					wp_unslash( $_POST['_wpnonce'] )
				),
				'ocwp_update_nickname'
			)
		) {

			global $current_user;

			$updated_nickname = sanitize_text_field(
				wp_unslash(
					$_POST['ocwp_nickname']
				)
			);

			// Update the user's nickname.
			update_user_meta( get_current_user_id(), 'nickname', $updated_nickname );

			// Update the user's display name.
			wp_update_user(
				[
					'ID'           => get_current_user_id(),
					'display_name' => $updated_nickname,
				]
			);

			// Update the global current user object to see your changes on the same request.
			$current_user->display_name = $updated_nickname;

		}

	}

}

Users::hooks();
