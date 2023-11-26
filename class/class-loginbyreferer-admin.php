<?php
/**
 * LoginByReferer_Admin
 *
 * @package Login_By_Referer
 */

namespace LoginByReferer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin class
 */
class LoginByReferer_Admin {

	// Singleton trait.
	use Singleton;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	private function __construct() {
		// Plugin setting page.
		add_action( 'admin_menu', array( $this, 'register_option_page' ) );
		add_action( 'admin_init', array( $this, 'register_section_field' ) );

		// Register settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Option page.
	 *
	 * @return void
	 */
	public function register_option_page() {
		add_options_page(
			'Login by Referer',
			'Login by Referer',
			'manage_options',
			LoginByReferer_Core::PLUGIN_PREFIX,
			array( $this, 'register_option_page_html' ),
		);
	}

	/**
	 * Option page HTML.
	 *
	 * @return void
	 */
	public function register_option_page_html() {
		echo '<div class="wrap">';
		echo '<h1>Login by Referer Setting</h1>';
		echo '<form method="post" action="options.php">';
		settings_fields( LoginByReferer_Core::PLUGIN_PREFIX . '-field1' );
		do_settings_sections( LoginByReferer_Core::PLUGIN_PREFIX );
		submit_button();
		echo '</form>';
		echo '</div>';
	}

	/**
	 * Add section and field.
	 *
	 * @return void
	 */
	public function register_section_field() {
		// Add section 1.
		// ID, Title, Callback function, Setting page slug.
		add_settings_section(
			LoginByReferer_Core::PLUGIN_PREFIX . '-section1',
			__( 'Referer settings', 'login-by-referer' ),
			array( $this, 'register_section1_html' ),
			LoginByReferer_Core::PLUGIN_PREFIX
		);
		// Add field 1-1 (Allowed Referer).
		// ID, Label, Callback function, Setting page slug, Section ID.
		add_settings_field(
			LoginByReferer_Core::PLUGIN_PREFIX . '_referer_allowed',
			__( 'Allowed referer', 'login-by-referer' ),
			array( $this, 'register_field_referer_allowed_html' ),
			LoginByReferer_Core::PLUGIN_PREFIX,
			LoginByReferer_Core::PLUGIN_PREFIX . '-section1',
			array(
				'label_for' => LoginByReferer_Core::PLUGIN_PREFIX . '_referer_allowed',
			),
		);

		// Add section 2.
		add_settings_section(
			LoginByReferer_Core::PLUGIN_PREFIX . '-section2',
			__( 'User settings', 'login-by-referer' ),
			array( $this, 'register_section2_html' ),
			LoginByReferer_Core::PLUGIN_PREFIX
		);
		// Add field 2-1 (User to login).
		add_settings_field(
			LoginByReferer_Core::PLUGIN_PREFIX . '_user_id',
			__( 'User to login', 'login-by-referer' ),
			array( $this, 'register_field_user_id_html' ),
			LoginByReferer_Core::PLUGIN_PREFIX,
			LoginByReferer_Core::PLUGIN_PREFIX . '-section2',
			array(
				'label_for' => LoginByReferer_Core::PLUGIN_PREFIX . '_user_id',
			),
		);
	}

	/**
	 * Section 1 HTML.
	 *
	 * @return void
	 */
	public function register_section1_html() {
		echo esc_html__( 'Setting about referer', 'login-by-referer' );
	}

	/**
	 * Field 1-1 HTML.
	 *
	 * @return void
	 */
	public function register_field_referer_allowed_html() {
		printf(
			'<textarea rows="8" cols="30" name="%s_referer_allowed" id="%s_referer_allowed">%s</textarea>',
			esc_attr( LoginByReferer_Core::PLUGIN_PREFIX ),
			esc_attr( LoginByReferer_Core::PLUGIN_PREFIX ),
			esc_html( get_option( LoginByReferer_Core::PLUGIN_PREFIX . '_referer_allowed' ) )
		);

	}

	/**
	 * Section 2 HTML.
	 *
	 * @return void
	 */
	public function register_section2_html() {
		echo esc_html__( 'Setting about user', 'login-by-referer' );
	}

	/**
	 * Field 2-1 HTML.
	 *
	 * @return void
	 */
	public function register_field_user_id_html() {
		$users = get_users();
		echo esc_html__( 'Login as the following user.', 'login-by-referer' );
		echo '<ul>';
		foreach ( $users as $user ) {
			// Do not allow register yourself.
			if ( wp_get_current_user()->id === $user->id ) {
				continue;
			}
			echo '<li>';
			printf(
				'<input type="radio" name="%s_user_id" id="%s_user_id_%s" value="%s" %s />',
				esc_attr( LoginByReferer_Core::PLUGIN_PREFIX ),
				esc_attr( LoginByReferer_Core::PLUGIN_PREFIX ),
				esc_attr( $user->id ),
				esc_attr( $user->id ),
				checked( get_option( LoginByReferer_Core::PLUGIN_PREFIX . '_user_id', true ), $user->id, false ),
			);
			printf(
				'<label for="%s_user_id_%s">%s (%s)</label>',
				esc_attr( LoginByReferer_Core::PLUGIN_PREFIX ),
				esc_attr( $user->id ),
				esc_attr( $user->display_name ),
				esc_attr( $user->user_login )
			);
			echo '</li>';
		}
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			LoginByReferer_Core::PLUGIN_PREFIX . '-field1',
			LoginByReferer_Core::PLUGIN_PREFIX . '_referer_allowed',
			'esc_html',
		);
		register_setting(
			LoginByReferer_Core::PLUGIN_PREFIX . '-field1',
			LoginByReferer_Core::PLUGIN_PREFIX . '_user_id',
			'esc_attr',
		);
	}
}
