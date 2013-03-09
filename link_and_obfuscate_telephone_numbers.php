<?php
/*
Plugin Name: Link/Obfuscate Telephone Numbers
Plugin URI: http://grandadevans.com/resources/wordpress-plugin-to-link-and-obfuscate-telephone-numbers/
Description: Link to telephone numbers if the user is on a smartphone or leave plain text if not and then obfuscate the text/link
Version: 0.2
Author: Grandadevans
Author URI: http://grandadevans.com
License: GPL2

Copyright 2013  John Evans  (email : john@grandadevans.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Define the paths
 *
 * The LAOTNPATH is the path to the plugin
 * The LAOTNURL  is the url to the plugin
 */
define( 'LAOTNPATH', plugin_dir_path(__FILE__) );
define( 'LAOTNURL' , plugin_dir_url( __FILE__) );

/**
 * Debug flag - If this is set the debugged output will be sent
 * @since   0.2
 **/
define('LAOTN_DEBUG', false);

/**
 * Include the plugin class
 */
require_once(LAOTNPATH . 'includes/class_link_and_obfuscate_telephone_numbers.php');

/**
 * Register the hooks, these are not used at the minute but they have been 
 * included for ease of upgradability should they be required in the future.
 */
register_activation_hook(__FILE__, LAOTN_activation);
register_deactivation_hook(__FILE__, LAOTN_deactivation);

/**
 * Register the shortcode
 **/
add_shortcode( 'link_obfuscate_telephone', array( 'LAOTN', 'action' ) );

/**
 * The actual hook functions - Simple calls an empty uninstall function for the 
 * time being
 *
 * @since   0.2
 */
function LAOTN_activation()
{
    //Check for required functions
    if( ! function_exists( 'mb_convert_encoding' ) || ! function_exists( 'mb_detect_encoding' ) )
    {
        die(__('You need to install the PHP Multibyte String (mbstring) extension to use this plugin.'));
    }

    // Call the uninstall hook
    LAOTN_uninstall_hook(__FILE_, LAOTN_uninstall);
}


/**
 * The de-activation plugin
 * 
 *@since 0.2
 */
function LAOTN_deactivation()
{
    // Nothing goin on here
}


/**
 * The uninstall hook function
 * This is just placed here for upgradability
 *
 * @since 0.2
 */
function LAOTN_uninstall_hook()
{
    // Placed here in case it is needed in the future

}



