<?php
/**
 * Plugin Name: Get User Custom Field Values
 * Version:     3.2
 * Plugin URI:  https://coffee2code.com/wp-plugins/get-user-custom-field-values/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * Text Domain: get-user-custom-field-values
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Use widgets, shortcodes, and/or template tags to easily retrieve and display custom field values for users.
 *
 * Compatible with WordPress 4.6+ through 5.4+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/get-user-custom-field-values/
 *
 * @package Get_User_Custom_Field_Values
 * @author  Scott Reilly
 * @version 3.2
 */

/*
 * TODO:
 * - Create hooks to allow disabling shortcode, shortcode builder, and widget support
 * - Support getting random custom field values
 * - Support specifying a limit on the number of custom field values returned
 * - Facilitate conditional output, maybe via c2c_get_user_custom_if() where text is only output if post
 *   has the custom field AND it equals a specified value (or one of an array of possible values)
 *   echo c2c_get_user_custom_if( 'size', array( 'XL', 'XXL' ), 'Sorry, this size is out of stock.' );
 * - Introduce a 'format' shortcode attribute and template tag argument.  Defines the output format for each
 *   matching custom field, i.e. c2c_get_user_custom(..., $format = 'Size %key% has %value%' in stock.')
 * - Support specifying $field as array or comma-separated list of custom fields.
 * - Create args array alternative template tag: c2c_user_custom_field( $field, $args = array() ) so features
 *   can be added and multiple arguments don't have to be explicitly provided.  Perhaps transition c2c_get_user_custom()
 *   in plugin's v3.0 and detect args.
 *   function c2c_get_user_custom( $field, $args = array() ) {
 *     if ( ! empty( $args ) && ! is_array( $args ) ) // Old style usage
 *       return c2c_old_get_user_custom( $field, ... ); // Or: $args = c2c_get_user_custom_args_into_array( ... );
 *     // Do new handling here.
 *   }
 * - Support name filters to run against found custom fields
 *   c2c_get_user_custom( 'favorite_site', array( 'filters' => array( 'strtoupper', 'make_clickable' ) ) )
 * - Since it's shifting to args array, might as well support 'echo'
 * - Allow $field value to actually be an array of different field names to use.
 *   See: https://wordpress.org/support/topic/multiple-field-output-in-widget
 */

/*
	Copyright (c) 2006-2020 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'get-user-custom.widget.php' );
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'get-user-custom.shortcode.php' );

if ( ! function_exists( 'c2c_get_current_user_custom' ) ):
/**
 * Access custom fields for the currently logged in user.
 *
 * If the current visitor is NOT logged in, then the $none value is returned.
 *
 * @param string  $field       The name/key value of the custom field.
 * @param string  $before      Optional. Text to appear before all the custom field value(s) if a value exists. Default ''.
 * @param string  $after       Optional. Text to appear after all the custom field value if a value(s) exists. Default ''.
 * @param string  $none        Optional. The text to display in place of the field value should no field value exists; if
 *                             defined as '' and no field value exists, then nothing (including no $before and $after) gets
 *                             displayed.  Default ''.
 * @param string  $between     Optional. The text to display between multiple occurrences of the custom field; if defined as '',
 *                             then only the first instance will be used.  Default ''.
 * @param string  $before_last Optional. The text to display between the next-to-last and last items listed when multiple
 *                             occurrences of the custom field exist; $between MUST be set to something other than '' for this
 *                             to take effect.  Default ''.
 * @return string The value for the specified custom field.
 */
function c2c_get_current_user_custom( $field, $before='', $after='', $none='', $between='', $before_last='' ) {
	$user = wp_get_current_user();

	return c2c_get_user_custom( ( isset( $user->ID ) ? (int) $user->ID : 0 ), $field, $before, $after, $none, $between, $before_last );
}
add_filter( 'c2c_get_current_user_custom', 'c2c_get_current_user_custom', 10, 6 );
endif;


if ( ! function_exists( 'c2c_get_author_custom' ) ) :
/**
 * Access custom fields for the current author (when on the permalink page for a post, page, or in a loop),
 *
 * @param string  $field       The name/key value of the custom field.
 * @param string  $before      Optional. Text to appear before all the custom field value(s) if a value exists. Default ''.
 * @param string  $after       Optional. Text to appear after all the custom field value if a value(s) exists. Default ''.
 * @param string  $none        Optional. The text to display in place of the field value should no field value exists; if
 *                             defined as '' and no field value exists, then nothing (including no $before and $after) gets
 *                             displayed.  Default ''.
 * @param string  $between     Optional. The text to display between multiple occurrences of the custom field; if defined as '',
 *                             then only the first instance will be used.  Default ''.
 * @param string  $before_last Optional. The text to display between the next-to-last and last items listed when multiple
 *                             occurrences of the custom field exist; $between MUST be set to something other than '' for this
 *                             to take effect.  Default ''.
 * @return string The value for the specified custom field.
 */
function c2c_get_author_custom( $field, $before='', $after='', $none='', $between='', $before_last='' ) {
	global $authordata;

	if ( $authordata && ( is_single() || is_page() || in_the_loop() ) ) {
		return c2c_get_user_custom( ( isset( $authordata->ID ) ? (int) $authordata->ID : 0 ), $field, $before, $after, $none, $between, $before_last );
	}
}
add_filter( 'c2c_get_author_custom', 'c2c_get_author_custom', 10, 6 );
endif;


if ( ! function_exists( 'c2c_get_user_custom' ) ) :
/**
 * Access custom fields for any user specified by the $user_id value.
 *
 * @param int     $user_id     The user ID.
 * @param string  $field       The name/key value of the custom field.
 * @param string  $before      Optional. Text to appear before all the custom field value(s) if a value exists. Default ''.
 * @param string  $after       Optional. Text to appear after all the custom field value if a value(s) exists. Default ''.
 * @param string  $none        Optional. The text to display in place of the field value should no field value exists; if
 *                             defined as '' and no field value exists, then nothing (including no $before and $after) gets
 *                             displayed.  Default ''.
 * @param string  $between     Optional. The text to display between multiple occurrences of the custom field; if defined as '',
 *                             then only the first instance will be used.  Default ''.
 * @param string  $before_last Optional. The text to display between the next-to-last and last items listed when multiple
 *                             occurrences of the custom field exist; $between MUST be set to something other than '' for this
 *                             to take effect.  Default ''.
 * @return string The value for the specified custom field.
 */
function c2c_get_user_custom( $user_id, $field, $before='', $after='', $none='', $between='', $before_last='' ) {
	global $wpdb;

	if ( ! $field ) {
		return;
	}

	$values      = array();
	$meta_values = get_user_meta( $user_id, $field );

	// If no value was found, consider checking the user object itself.
	$user_fields = array( 'display_name', 'user_email', 'user_login', 'user_nicename', 'user_registered', 'user_url' );
	if ( ! $meta_values && in_array( $field, $user_fields ) && apply_filters( 'c2c_get_user_custom-user_field_proxy', true, $field ) ) {
		if ( $user = get_userdata( $user_id ) ) {
			$meta_values = array( $user->$field );
		}
	}

	if ( ! $between ) {
		$meta_values = array_slice( $meta_values, 0, 1 );
	}

	if ( $meta_values ) {
		$sanitized_field = sanitize_key( $field );

		foreach ( $meta_values as $metas ) {
			$metas = maybe_unserialize( $metas );

			foreach ( (array) $metas as $meta ) {
				$meta     = apply_filters( "the_user_meta_{$sanitized_field}", $meta );
				$values[] = apply_filters( 'the_user_meta', $meta );
			}
		}
	}

	if ( ! $values ) {
		$value = '';
	} else {
		$values = array_map( 'trim', $values );
		if ( ! $before_last ) {
			$value = implode( $between, $values );
		} else {
			switch ( $size = sizeof( $values ) ) {
				case 1:
					$value = $values[0];
					break;
				case 2:
					$value = $values[0] . $before_last . $values[1];
					break;
				default:
					$value = implode( $between, array_slice( $values, 0, $size-1 ) ) . $before_last . $values[ $size-1 ];
			}
		}
	}

	if ( ! $value ) {
		if ( ! $none ) {
			return;
		}
		$value = $none;
	}

	return $before . $value . $after;
}
add_filter( 'c2c_get_user_custom', 'c2c_get_user_custom', 10, 7 );
endif;

// Some filters you may wish to perform: (these are filters typically done to 'the_content' (post content))
//add_filter('the_user_meta', 'convert_chars');
//add_filter('the_user_meta', 'wptexturize');

// Other optional filters (you would need to obtain and activate these plugins before trying to use these)
//add_filter('the_user_meta', 'c2c_hyperlink_urls', 9);
//add_filter('the_user_meta', 'text_replace', 2);
//add_filter('the_user_meta', 'textile', 6);
