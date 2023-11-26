<?php
/**
 * Plugin Name:         Login by Referer
 * Plugin URI:
 * Description:         This plugin allows a user to login by specific referer.
 * Version:             1.0.0
 * Requires at least:   6.0
 * Tested up to:        6.4.1
 * Requires PHP:        7.4
 * Author:              web83info <me@web83.info>
 * Author URI:
 * Requires License:    no
 * License:             GPLv2+
 *
 * @package Login_By_Referer
 * @author  web83info
 * @link
 * @license
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
