<?php
/*
Plugin Name: Tel: link & obfuscate
Plugin URI: http://grandadevans.com/resources/wordpress-plugin-to-link-and-obfuscate-telephone-numbers/
Description: Link to telephone numbers if the user is on a smartphone or leave plain text if not and then obfuscate the text/link
Version: 0.1
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

/* This plugin is based upon and a re-write of the plugin at http://wordpress.org/extend/plugins/email-obfuscate-shortcode/ */

// Include the mdetect file
define( 'MYPLUGINNAME_PATH', plugin_dir_path(__FILE__) );
require MYPLUGINNAME_PATH . 'mdetect.php';

/**
 * Debug flag
 **/
define('TELOBF_DEBUG', false);

/**
 * Register activation hook
 **/
register_activation_hook( __FILE__, array( 'TELOBF', 'activate' ) );

/**
 * Register the shortcode
 **/
add_shortcode('tel-link-and-obf', array('TELOBF', 'obfuscate_shortcode'));

/**
 * Function for calling TELOBF from other plugins.
 * 
 * This function is callable in any of your plugins.
 * 
 * Example of array to pass to args:
 * 
 * Simple:
 * array
 *  (
 *      'tel' => '123456790'
 *  )
 * 
 * All args:
 * array
 *  (
 *      'tel' => '1234567890',
 *      'link' => 1,
 *      'link_text' => 'Click Me',
 *      'use_htmlentities' => 1,
 *      'use_noscript_fallback' => 1,
 *      'noscript_message' => 'Please enable JavaScript to see this field'
 *   )
 * 
 **/
function telobf_tel_obfuscate($args)
{
    return TELOBF::obfuscate_shortcode($args);
}

/**
 * Main class
 **/
class TELOBF
{
    /**
     * Plugin activation function
     **/
    static function activate()
    {
        //Check for required functions
        if(!function_exists('mb_convert_encoding') || !function_exists('mb_detect_encoding')) {
            die(__('You need to install the PHP Multibyte String (mbstring) extension to use this plugin.'));
        }
    }
    
    /**
     * Obfuscation routine
     **/
    static function obfuscate_shortcode($args)
    {
        //Get values from shortcode and put them in local variables
        extract
        (
            shortcode_atts
            (
                array
                (
        	       'tel' => false,
                   'link' => true,
                   'link_text' => "",
                   'use_htmlentities' => true,
                   'use_noscript_fallback' => true,
                   'noscript_message' => __("Please enable JavaScript to see this field.", "tel-obfuscate-shortcode")
                ),
                $args
            )
        );
        
        if(!isset($tel)) {
            return __("You have not entered a telephone number for this shortcode.", "tel-obfuscate-shortcode");
        } else {        
            //Init return variable
            $ret = $tel;
            
            //Encode as htmlentities
            if(isset($use_htmlentities)) {
                $ret = TELOBF::html_entities_all($ret);
            }

            // Set link text
            if ($link_text === "") {
                $link_text = $ret;
            }
            
            $mdetect = new uagent_info;
            //Wrap in tel: link
            if ($link == true && ($mdetect->DetectMobileQuick() == true)) {
                $ret = '<a href="tel:' . $ret . '">' . $link_text . ' </a>';
            }
            
            //Convert to JS snippet
            $ret = TELOBF::safe_text($ret);
                
            //Add noscript fallback
            if($use_noscript_fallback) {
                $ret .= '<noscript>' . $noscript_message . '</noscript>';
            }
            
            if(TELOBF_DEBUG)
            {
                $ret .= '
                            <div class="telobf_debug">
                                --- TELOBF debug info: --- <br />
                                Raw tel string: ' . $tel . ' <br/>
                                Linkable: ' . $linkable . ' <br/>
                                Link title: ' . $link_title . ' <br/>
                                noscript fallback: ' . $use_noscript_fallback . '<br/>
                                noscript message: ' . $noscript_message . '<br/>
                                --- End of TELOBF debug info ---
                            </div>
                        ';      
            }
            return $ret;
        }
    }
    
    /**
     * Encodes every character in $text into its numeric html representation.
     * http://stackoverflow.com/questions/3005116/how-to-convert-all-characters-to-their-html-entity-equivalent-using-php/3005240
     */
    static function html_entities_all($text)
    {
        $text = mb_convert_encoding($text , 'UTF-32', 'UTF-8');
        $t = unpack("N*", $text);
        $t = array_map(array('TELOBF', 'html_entities_closure_wrap'), $t);
        
        return implode("", $t);
    }
    
    //For PHP <5.3 support.
    static function html_entities_closure_wrap($n)
    {
    	return "&#$n;";
    }
    
    /**
     * safe_text() obfuscator function
     * http://khromov.wordpress.com/2011/10/04/php-function-for-scrambling-e-mail-addressesphone-numbers-using-javascript/
     **/
    static function safe_text($text)
    {
        //Check if text is UTF-8 and decode if it is
        if(mb_detect_encoding($text, 'UTF-8', true)) {
            $text = utf8_decode($text);
        }
        
        //Create the obfuscation array
        $chars = str_split($text);
    
        $enc[] = rand(0,255);
    
        foreach($chars as $char) {
            $enc[] = ord($char)-$enc[sizeof($enc)-1];
        }
        
        $finished_array = join(',',$enc); 
        
        $ret = '<script type="text/javascript">
                    var t=[' . $finished_array . '];
                    for (var i=1; i<t.length; i++)
                    {
                        document.write(String.fromCharCode(t[i]+t[i-1])); 
                    }
                </script>';
                
        return $ret;   
    }
}
