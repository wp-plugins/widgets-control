<?php
/**
 * Plugin Name: Widgets Control
 * Plugin URI: http://www.itthinx.com/
 * Description: A Widget toolbox that adds visibility management and helps to control where widgets are shown efficiently.
 * Version: 1.0.0
 * Author: itthinx
 * Author URI: http://www.itthinx.com
 * Donate-Link: http://www.itthinx.com/plugins/widgets-control/
 * License: GPLv3
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

function widgets_plugin_set() {
	define( 'WIDGETS_PLUGIN_VERSION',  '1.0.0' );
	define( 'WIDGETS_PLUGIN_NAME',     'widgets-control' );
	define( 'WIDGETS_PLUGIN_DOMAIN',   'widgets-control' );
	define( 'WIDGETS_PLUGIN_FILE',     __FILE__ );
	define( 'WIDGETS_PLUGIN_BASENAME', plugin_basename( WIDGETS_PLUGIN_FILE ) );
	define( 'WIDGETS_PLUGIN_DIR',      WP_PLUGIN_DIR . '/widgets-control' );
	define( 'WIDGETS_PLUGIN_LIB',      WIDGETS_PLUGIN_DIR . '/lib' );
	define( 'WIDGETS_PLUGIN_URL',      WP_PLUGIN_URL . '/widgets-control' );
}

/**
 * Widget Plugin main class.
 */
class Widgets_Plugin {

	/**
	 * Plugin setup.
	 */
	public static function init() {
		if ( !defined( 'WIDGETS_PLUGIN_VERSION' ) ) {
			widgets_plugin_set();
			add_action( 'init', array( __CLASS__, 'wp_init' ) );
			require_once WIDGETS_PLUGIN_LIB . '/includes/constants.php';
			require_once WIDGETS_PLUGIN_LIB . '/conditions.php';
			require_once WIDGETS_PLUGIN_LIB . '/class-widgets-plugin-options.php';
			require_once WIDGETS_PLUGIN_LIB . '/widgets.php';
			if ( is_admin() ) {
				require_once WIDGETS_PLUGIN_LIB . '/admin/class-widgets-plugin-admin.php';
				require_once WIDGETS_PLUGIN_LIB . '/admin/class-widgets-plugin-admin-settings.php';
			}
			require_once WIDGETS_PLUGIN_LIB . '/class-widgets-plugin-cache.php';
		}
	}

	/**
	 * Hooked on the init action, loads translations.
	 */
	public static function wp_init() {
		load_plugin_textdomain( WIDGETS_PLUGIN_DOMAIN, null, 'widgets-control/languages' );
	}
}
Widgets_Plugin::init();
