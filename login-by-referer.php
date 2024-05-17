<?php
/**
 * Login by Referer
 *
 * @package             Login_By_Referer
 * @author              web83info
 * @copyright           2023 web83info
 * @license             GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:         Login by Referer
 * Plugin URI:
 * Description:         This plugin allows a user to login by specific referer.
 * Version:             1.0.4
 * Requires at least:   6.0
 * Tested up to:        6.5.3
 * Requires PHP:        7.4
 * Author:              web83info <me@web83.info>
 * Author URI:
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace LoginByReferer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'class/trait-singleton.php';
require_once 'class/class-loginbyreferer-core.php';
require_once 'class/class-loginbyreferer-admin.php';

$show_login_by_referer_core  = LoginByReferer_Core::get_instance();
$show_login_by_referer_admin = LoginByReferer_Admin::get_instance();
