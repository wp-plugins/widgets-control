<?php
/**
 * class-widgets-plugin-admin-settings.php
 *
 * Copyright (c) www.itthinx.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package widgets
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class Widgets_Plugin_Admin_Settings {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_filter( 'plugin_action_links_'. plugin_basename( WIDGETS_PLUGIN_FILE ), array( __CLASS__, 'admin_settings_link' ) );
	}

	public static function admin_settings_link( $links ) {
		if ( current_user_can( 'manage_options' ) ) {
			$links = array( '<a href="' . get_admin_url( null, 'plugins.php?page=widgets-plugin' ) . '">' . __( 'Settings', WIDGETS_PLUGIN_DOMAIN ) . '</a>' ) + $links;
		}
		return $links;
	}

	public static function admin_menu() {
		add_plugins_page(
			__( 'Widgets Control', WIDGETS_PLUGIN_DOMAIN ),
			__( 'Widgets Control', WIDGETS_PLUGIN_DOMAIN ),
			'manage_options',
			'widgets-plugin',
			array( __CLASS__, 'settings' )
		);
	}

	public static function settings() {
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Access denied.', WIDGETS_PLUGIN_DOMAIN ) );
		}

		if ( isset( $_POST['action'] ) && ( $_POST['action'] == 'set' ) && wp_verify_nonce( $_POST['widgets-plugin-settings'], 'admin' ) ) {
			$auto_clear_cache = empty( $_POST['auto_clear_cache'] ) ? 'no' : 'yes';
			Widgets_Plugin_Options::update_option(
				WIDGETS_PLUGIN_AUTO_CLEAR_CACHE,
				$auto_clear_cache
			);
		}

		$auto_clear_cache = Widgets_Plugin_Options::get_option(
			WIDGETS_PLUGIN_AUTO_CLEAR_CACHE,
			WIDGETS_PLUGIN_AUTO_CLEAR_CACHE_DEFAULT
		);

		echo '<h2>' .
			__( 'Widgets Control Settings', WIDGETS_PLUGIN_DOMAIN ) .
			'</h2>';

		echo '<div style="margin-right:1em;">';
		echo '<form name="settings" method="post" action="">';
		echo '<div class="widgets-plugin">';
		echo '<p>';
		echo '<label>';
		echo sprintf( '<input type="checkbox" name="auto_clear_cache" %s />', $auto_clear_cache == 'yes' ? ' checked="checked" ' : '' );
		echo ' ';
		echo __( 'Automatic Cache Clearing', WIDGETS_PLUGIN_DOMAIN );
		echo '</label>';
		echo '</p>';
		echo '<p>';
		echo __( 'Automatically clear the cache when widgets are updated?', WIDGETS_PLUGIN_DOMAIN );
		echo '</p>';
		echo '<p class="description">';
		echo __( 'This works with W3 Total Cache, WP Super Cache and WP Engine.', WIDGETS_PLUGIN_DOMAIN );
		echo ' ';
		echo __( 'This option is only provided as a convenience while you set up your site. It is recommended while you set up your widgets or when you work on changes. Note that you can also clear the cache manually even with this option disabled and we recommend to disable it once you have finished setting up your widgets.', WIDGETS_PLUGIN_DOMAIN );
		echo ' ';
		echo __( 'It is recommended while you set up your widgets or when you work on changes. Note that you can also clear the cache manually even with this option disabled and we recommend to disable it once you have finished setting up your widgets.', WIDGETS_PLUGIN_DOMAIN );
		echo ' ';
		echo __( 'Note that you can also clear the cache manually even with this option disabled and we recommend to disable it once you have finished setting up your widgets.', WIDGETS_PLUGIN_DOMAIN );
		echo '</p>';
		wp_nonce_field( 'admin', 'widgets-plugin-settings', true, true );
		echo '<div class="buttons">';
		echo sprintf( '<input class="save button button-primary" type="submit" name="submit" value="%s" />', esc_attr( __( 'Save', WIDGETS_PLUGIN_DOMAIN ) ) );
		echo '<input type="hidden" name="action" value="set" />';
		echo '</div>';
		echo '</form>';
		echo '</div>';
	}
}
add_action( 'init', array( 'Widgets_Plugin_Admin_Settings', 'init' ) );
