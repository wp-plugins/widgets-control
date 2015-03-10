<?php
/**
 * conditions.php
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

// Condition Engine

/**
 * Find out if the current page is $page. If $page is empty we consider that we ask for the current page which will always be true.
 * Note that is_page() is of no use for us as it will only consider the condition to be met if
 * $wp_query->is_page ...
 *
 * @param mixed $page Page ID, title, slug, or array of those
 * @return true if $page is empty or $page is the ID, title, slug, ... of the current page
 */
function _widgets_plugin_is_poge( $page = '' ) {
	global $post;
	$result = false;
	if ( empty( $page ) ) {
		$result = true;
	} else if ( !empty( $post ) ) {
		if (
		( $post->ID == $page ) ||
		( $post->post_title == $page )
		) {
			$result = true;
		} else {
			$permalink = get_permalink( $post->ID );
			$prefix = home_url();
			$slug = substr( $permalink, strlen( $prefix ) );
			$slug = ltrim( rtrim( $slug, '/'), '/' );
			$page = ltrim( rtrim( $page, '/'), '/' );
			if ( $slug == $page ) {
				$result = true;
			}
		}
	}
	return $result;
}

/**
 * Determines TRUE if the current page matches. Used to show or not sidebars and widgets on a page.
 * @param string $condition evaluation condition
 * @param string $pages pages expression
 */
function _widgets_plugin_evaluate_display_condition( $condition, $pages ) {
	global $post;
	$show = FALSE;
	if ( empty( $condition ) || !$condition || ( $condition == WIDGETS_PLUGIN_SHOW_ON_ALL_PAGES ) ) {
		$show = TRUE;
	}
	if ( ! $show ) {
		$pages = explode( "\n", $pages ); // must use "
		if ( is_array( $pages ) ) {
			$match = FALSE;
			$i = 0;
			while ( ! $match && ( $i < count( $pages ) ) ) {
				$page = trim( $pages[$i] );
				// token?
				if ( ( strpos( $page, '[' ) == 0 ) && ( strrpos( $page, ']' ) == strlen( $page ) - 1 ) ) {
					// strip tokenizers
					$page = trim ( substr( $page, 1, strlen( $page ) - 2 ) );
					// decompose
					$page_params = explode( ':', $page );
					//$match = FALSE;
					$token = isset( $page_params[0] ) ? trim( $page_params[0] ) : null;
					$value = isset( $page_params[1] ) ? trim( $page_params[1] ) : null;
					$value2 = isset( $page_params[2] ) ? trim( $page_params[2] ) : null;
					switch ( $token ) {
						case 'home' :
							$match = is_home();
							break;
						case 'front' :
							$match = is_front_page();
							break;
						case 'single' :
							$match = is_single();
							break;
						case 'page' :
							$match = is_page();
							break;
						case 'category' :
							if ( ! empty( $value ) ) {
								$match = is_category( $value );
							} else {
								$match = is_category();
							}
							break;
						case 'has_term' :
							if ( ! empty( $value ) ) {
								$match = has_term( $value, $value2, $post );
							}
							break;
						case 'tag' :
							if ( ! empty ( $value ) ) {
								$match = is_tag( $value );
							} else {
								$match = is_tag();
							}
							break;
						case 'tax' :
							if ( ! empty( $value ) ) {
								$match = is_tax( $value );
							} else {
								$match = is_tax();
							}
							break;
						case 'author' :
							if ( ! empty( $value) ) {
								$match = is_author( $value );
							} else {
								$match = is_author();
							}
							break;
						case 'archive' :
							$match = is_archive();
							break;
						case 'search' :
							$match = is_search();
							break;
						case '404' :
							$match = is_404();
							break;
					}
					switch ( $condition ) {
						case WIDGETS_PLUGIN_SHOW_ON_THESE_PAGES :
							$show = $match;
							break;
						case WIDGETS_PLUGIN_SHOW_NOT_ON_THESE_PAGES :
							$show = ! $match;
							break;
					}
				} else {
					switch ( $condition ) {
						case WIDGETS_PLUGIN_SHOW_ON_THESE_PAGES :
							$page = trim( $page );
							$match = _widgets_plugin_is_poge( $page );
							$show = $match;
							break;
						case WIDGETS_PLUGIN_SHOW_NOT_ON_THESE_PAGES :
							$page = trim( $page );
							$match = _widgets_plugin_is_poge( $page );
							$show = ! $match;
							break;
					}
				}
				$i++;
			} // loop
		}
	}
	return $show;
}
