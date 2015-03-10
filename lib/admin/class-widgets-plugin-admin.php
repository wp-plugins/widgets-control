<?php
/**
 * class-widgets-plugin-admin.php
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

/**
 * Administrative back end extensions to widgets.
 */
class Widgets_Plugin_Admin {

	/**
	 * Action setup.
	 */
	public static function init() {
		add_action( 'sidebar_admin_setup', array( __CLASS__, 'add_controls' ) );
	}

	/**
	 * Called upon entering the widget admin section.
	 * Called when saving a widget's options.
	 */
	public static function add_controls() {
		global $wp_registered_widgets, $wp_registered_widget_controls;

		$settings = widgets_plugin_get_widget_settings();
		$widget_settings = isset( $settings['widgets'] ) ? $settings['widgets'] : array();
		// update the stored settings
		if ( isset( $_POST['widget-id'] ) ) {
			$widget_id = $_POST['widget-id'];
			if ( !empty( $widget_id ) ) {
				// where to show
				$condition = isset( $_POST[$widget_id . '-widgets-plugin-display-condition'] ) ? $_POST[$widget_id . '-widgets-plugin-display-condition'] : null;
				if (
					! isset ( $condition ) ||
					! ( ( $condition == WIDGETS_PLUGIN_SHOW_ON_ALL_PAGES ) ||
					( $condition == WIDGETS_PLUGIN_SHOW_ON_THESE_PAGES ) ||
					( $condition == WIDGETS_PLUGIN_SHOW_NOT_ON_THESE_PAGES ) )
				) {
					unset( $settings['widgets'][$widget_id]['display']['condition'] );
				} else {
					$settings['widgets'][$widget_id]['display']['condition'] = $condition;
				}
				$pages = isset( $_POST[$widget_id . '-widgets-plugin-display-pages'] ) ? $_POST[$widget_id . '-widgets-plugin-display-pages'] : '';
				$pages = wp_filter_nohtml_kses( trim( $pages ) );
				if ( ! isset( $pages ) || empty ( $pages ) ) {
					unset( $settings['widgets'][$widget_id]['display']['pages'] );
				} else {
					$settings['widgets'][$widget_id]['display']['pages'] = $pages;
				}
				// store the settings
				widgets_plugin_set_widget_settings( $settings );
			} // !empty( $widget_id )
		}

		foreach ( $wp_registered_widgets as $id => $widget ) {
			$alter_callback = false;
			if ( isset( $wp_registered_widget_controls[$id]['params'][0] ) && is_array( $wp_registered_widget_controls[$id]['params'][0] ) ) {
				$wp_registered_widget_controls[$id]['params'][0]['widgets-plugin-id'] = $id;
				$alter_callback = true;
			} else if ( empty( $wp_registered_widget_controls[$id]['params'] ) ) {

				if ( !isset( $wp_registered_widget_controls[$id]['params'] ) || ( $wp_registered_widget_controls[$id]['params'] === null ) ) {
					// params will be null when a widget does not provide any controls (e.g. no title field, no checkboxes, ... nothing)
					$wp_registered_widget_controls[$id]['params'] = array();
				}
				if ( is_array( $wp_registered_widget_controls[$id]['params'] ) ) {
					$wp_registered_widget_controls[$id]['params'][0]['widgets-plugin-id'] = $id;
					$alter_callback = true;
				}
			}

			if ( $alter_callback ) {
				// replace the callback with our own
				$wp_registered_widget_controls[$id]['original_callback'] = isset( $wp_registered_widget_controls[$id]['callback'] ) ? $wp_registered_widget_controls[$id]['callback'] : null;
				$wp_registered_widget_controls[$id]['callback'] = array( __CLASS__, 'alter_controls' );
			} else {
				if ( isset( $settings['widgets'][$id] ) ) {
					unset( $settings['widgets'][$id] );
					widgets_plugin_set_widget_settings( $settings );
				}
			}
		}
	}

	/**
	 * Called when a widget's controls should be displayed.
	 * Takes care of calling the original callback of the widget
	 * and adding theme-specific controls.
	 * @see wp_register_widget_control() in wp_includes/widget.php
	 */
	public static function alter_controls() {
		global $wp_registered_widget_controls;
		$params = func_get_args();
		$settings = widgets_plugin_get_widget_settings();
		$widget_settings = isset( $settings['widgets'] ) ? $settings['widgets'] : array() ;
		$id = ( is_array ( $params[0] ) ) ? $params[0]['widgets-plugin-id'] : array_pop( $params );
		$widget_id = $id;
		if ( is_array( $params[0] ) && isset( $params[0]['number'] ) ) {
			$number = $params[0]['number'];
		}
		if ( $number == -1 ) {
			// @see wp-admin/includes/widgets.php quoting:
			// number == -1 implies a template where id numbers are replaced by a generic '__i__'
			$number = '__i__';
			$value = "";
		}
		if ( isset( $number ) ) {
			$widget_id = $wp_registered_widget_controls[$id]['id_base'] . '-' . $number;
		}
		// call the original callback before adding our stuff
		$callback = isset( $wp_registered_widget_controls[$id]['original_callback'] ) ? $wp_registered_widget_controls[$id]['original_callback'] : null;
		if ( is_callable( $callback ) ) {
			call_user_func_array( $callback, $params );
		}
		// now add our stuff
		echo '<p><b>' . __( 'Visibility', WIDGETS_PLUGIN_DOMAIN ) . '</b></p>';
		// where to show
		echo '<p>';
		$selected = isset( $settings['widgets'][$widget_id]['display']['condition'] ) ? $settings['widgets'][$widget_id]['display']['condition'] : null;
		$widget_condition_option_id = $widget_id . '-widgets-plugin-display-condition';
		if ( ( $selected == NULL ) || empty( $selected ) ) {
			$selected = WIDGETS_PLUGIN_SHOW_ON_ALL_PAGES;
		}
		echo '<input type="radio" name="' . $widget_condition_option_id . '" value="' . WIDGETS_PLUGIN_SHOW_ON_ALL_PAGES . '" ' . ( $selected == WIDGETS_PLUGIN_SHOW_ON_ALL_PAGES ? 'checked="checked"' : '') . '/>' . __( 'Show on all pages', WIDGETS_PLUGIN_DOMAIN ) . '<br/>';
		echo '<input type="radio" name="' . $widget_condition_option_id . '" value="' . WIDGETS_PLUGIN_SHOW_ON_THESE_PAGES . '" '  . ( $selected == WIDGETS_PLUGIN_SHOW_ON_THESE_PAGES ? 'checked="checked"' : '') . '/>' . __( 'Show only on these pages', WIDGETS_PLUGIN_DOMAIN ) . '<br/>';
		echo '<input type="radio" name="' . $widget_condition_option_id . '" value="' . WIDGETS_PLUGIN_SHOW_NOT_ON_THESE_PAGES . '" ' . ( $selected == WIDGETS_PLUGIN_SHOW_NOT_ON_THESE_PAGES ? 'checked="checked"' : '') . '/>' . __( 'Show on all except these pages', WIDGETS_PLUGIN_DOMAIN ) . '<br/>';
		$pages = isset( $settings['widgets'][$widget_id]['display']['pages'] ) ? $settings['widgets'][$widget_id]['display']['pages'] : '';
		$widget_pages_option_id = $widget_id . '-widgets-plugin-display-pages';
		echo '<textarea cols="20" rows="3" name="' . $widget_pages_option_id . '">';
		echo $pages;
		echo '</textarea>';
		echo '<br/>';
		echo '<span class="description">';
		echo WIDGETS_PLUGIN_TOKEN_HELPTEXT;
		echo '</span>';
		echo '</p>';
	}
}
Widgets_Plugin_Admin::init();
