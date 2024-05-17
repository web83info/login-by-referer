<?php
/**
 * LoginByReferer_Core
 *
 * @package Login_By_Referer
 */

namespace LoginByReferer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core class
 */
class LoginByReferer_Core {

	// Singleton trait.
	use Singleton;

	/**
	 * Plugin constant.
	 */
	const PLUGIN_VERSION = '1.0.4';
	const PLUGIN_PREFIX  = 'login-by-referer';
	const PLUGIN_GITHUB  = 'https://github.com/web83info/login-by-referer';

	const OPTION_DEFAULT_REFERER_ALLOWED = 'https://example.com/';
	const OPTION_DEFAULT_USER_ID         = '';

	const OPTION_DEFAULT_OTHER_INIT      = 0;
	const OPTION_DEFAULT_OTHER_UNINSTALL = 0;

	/**
	 * Default values for table 'options'.
	 * Saved under the key 'login-by-referer_*'.
	 *
	 * @var array
	 */
	private $settings = array(
		'referer_allowed' => self::OPTION_DEFAULT_REFERER_ALLOWED,
		'user_id'         => self::OPTION_DEFAULT_USER_ID,
		'other_init'      => self::OPTION_DEFAULT_OTHER_INIT,
		'other_uninstall' => self::OPTION_DEFAULT_OTHER_UNINSTALL,
	);

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	private function __construct() {

		// Initialize if option 'login-by-referer_other_init' is checked.
		if ( '1' === get_option( self::PLUGIN_PREFIX . '_other_init' ) ) {
			foreach ( $this->settings as $option_key => $option_value ) {
				delete_option( self::PLUGIN_PREFIX . '_' . $option_key );
			}
		}

		// Load default value.
		foreach ( $this->settings as $option_key => $option_value ) {
			if ( false === get_option( self::PLUGIN_PREFIX . '_' . $option_key ) ) {
				update_option( self::PLUGIN_PREFIX . '_' . $option_key, $option_value );
				$this->settings[ $option_key ] = $option_value;
			}
		}

		// Load textdomain.
		add_action( 'admin_menu', array( $this, 'load_textdomain' ) );

		// Load CSS and JS.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_css_js' ) );

		// Auto login.
		add_action( 'init', array( $this, 'auto_login' ) );

		// Auto login user can't go to dashboard.
		add_action( 'auth_redirect', array( $this, 'auto_login_user_no_dashboard' ) );

		// Add logout button on admin bar.
		add_action( 'admin_bar_menu', array( $this, 'add_logout_in_admin_bar' ), 9999 );
	}

	/**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( self::PLUGIN_PREFIX );
	}

	public function load_css_js() {
		// CSS.
		wp_enqueue_style(
			self::PLUGIN_PREFIX . '-css',
			plugins_url( 'assets/login-by-referer.min.css', dirname( __FILE__ ) ),
			array(),
			self::PLUGIN_VERSION
		);
	}

	/**
	 * Auto login
	 *
	 * @return void
	 */
	public function auto_login() {
		if ( is_user_logged_in() ) {
			return;
		}
		$referer_raw = str_replace(
			array( "\r\n", "\r", "\n" ),
			"\n",
			get_option( self::PLUGIN_PREFIX . '_referer_allowed' )
		);
		$referers    = explode( "\n", $referer_raw );
		$user        = get_user_by( 'id', get_option( self::PLUGIN_PREFIX . '_user_id' ) );
		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$http_referer = esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) );
		} else {
			$http_referer = '';
		}
		if ( $http_referer && $user ) {
			foreach ( $referers as $referer ) {
				if ( $referer && strpos( $http_referer, $referer ) !== false ) {
					wp_set_current_user( $user->id, $user->user_login );
					wp_set_auth_cookie( $user->id );
					break;
				}
			}
		}
	}

	/**
	 * Auto login user can't go to dashboard.
	 *
	 * @param int $user_id User ID.
	 * @return void
	 */
	public function auto_login_user_no_dashboard( $user_id ) {
		if ( $this->is_autologin_user() ) {
			wp_safe_redirect( get_home_url() );
			exit();
		}
	}

	/**
	 * Am I a auto logined user?
	 *
	 * @return bool
	 */
	public function is_autologin_user() {
		return get_option( self::PLUGIN_PREFIX . '_user_id' ) === (string) wp_get_current_user()->id;
	}

	/**
	 * Add logout button on admin bar.
	 *
	 * @return void
	 */
	public function add_logout_in_admin_bar() {
		global $wp_admin_bar;
		if ( ! is_object( $wp_admin_bar ) ) {
			return;
		}

		if ( $this->is_autologin_user() ) {

			// Delete all nodes.
			$nodes = $wp_admin_bar->get_nodes();
			foreach ( $nodes as $node ) {
				// 'top-secondary' is located on right top.
				if ( ! $node->parent || 'top-secondary' === $node->parent ) {
					$wp_admin_bar->remove_menu( $node->id );
				}
			}

			$wp_admin_bar->add_menu(
				array(
					'id'    => 'loginbyreferer-logout',
					'title' => __( 'Logout', 'login-by-referer' ),
					'href'  => wp_logout_url(),
					'meta'  => array(
						'class' => 'ab-top-secondary',
					),
				)
			);
		}
	}
}
