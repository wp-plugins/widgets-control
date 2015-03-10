<?php
/**
 * constants.php
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

define( 'WIDGETS_PLUGIN_THEME_SETTINGS', 'settings' );
define( 'WIDGETS_PLUGIN_WIDGET_SETTINGS', 'widgets' );
define( 'WIDGETS_PLUGIN_AUTO_CLEAR_CACHE', 'clear-cache' );
define( 'WIDGETS_PLUGIN_AUTO_CLEAR_CACHE_DEFAULT', 'no' );

define( 'WIDGETS_PLUGIN_TOKEN_HELPTEXT',
	__( 'Put each item on a line by itself.', WIDGETS_PLUGIN_DOMAIN ) . ' ' .
	__( 'To include or exclude pages, use page ids, titles or slugs.', WIDGETS_PLUGIN_DOMAIN ) . ' ' .
	__( 'These tokens can be used:', WIDGETS_PLUGIN_DOMAIN ) .
	/* translators: this needs no translation */
	' [home] [front] [single] [page] [category] [category:xyz] [has_term:term:taxonomy] [tag] [tag:xyz] [tax] [tax:xyz] [author] [author:xyz] [archive] [search] [404]'
);

define( 'WIDGETS_PLUGIN_SHOW_ON_ALL_PAGES', '' );
define( 'WIDGETS_PLUGIN_SHOW_ON_THESE_PAGES', '1' );
define( 'WIDGETS_PLUGIN_SHOW_NOT_ON_THESE_PAGES', '2' );

define( 'WIDGETS_PLUGIN_THEME_SETTINGS_NONCE', 'widgets-plugin-theme-settings-nonce' );
