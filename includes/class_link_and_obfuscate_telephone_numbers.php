<?php
/**
 * Class file for the Link and obfuscate telephone numbers Wordpress plugin
 * LAOTN = Link and Obfuscate Telephone Numbers
 *
 * @author  John Evans<john@grandadevans.com>
 * @since   0.2
 * @version 0.2
 * @package Link and Obfuscate Telephone Numbers Wordpress Plugin
 *
 */
class LAOTN
{

    /**
     * The main method which is responsible for either performing the required 
     * actions or calling other methods which do so.
     *
     * @param array $args The array of information passed to the plugin by 
     *              Wordpress
     * @return      Returns the finished obfuscated article whether it be a link 
     *              not
     * @since       0.2
     * @static
     *
     */
    public static function action( $args )
    {
        //Get values from shortcode and put them in local variables
        extract
        (
            shortcode_atts
            (
                array
                (
                   'tel'                   => "",
                   'link'                  => true,
                   'debug'                 => false,
                   'link_text'             => "",
                   'use_htmlentities'      => true,
                   'use_noscript_fallback' => true,
                   'noscript_message'      => __("Please enable JavaScript to see this field.", "tel-obfuscate-shortcode")
                ),
                $args
            )
        );
        
        // Return with an error if the telephone number is not set or is not a 
        // string
        if( ! (string) $tel || strlen( $tel ) == 0 ) {
            return __("You have not entered a telephone number for this shortcode.", "tel-obfuscate-shortcode");
        } else {        

            //Init return variable
            $ret = (string) $tel;
            
            //Encode as htmlentities
            if( $use_htmlentities === "1" )
            {
                $ret = static::html_entities_all( $ret );
            }

            // Set link text
            if ( ! (string) $link_text || strlen( $link_text ) == 0 ) {
                $link_text = $ret;
            }
            
            // Create a new instance of mdetect
            require_once( LAOTNPATH . 'includes/mdetect.php');
            $mdetect = new uagent_info;

            //Wrap in tel: link
            if ( $link == true && ( $mdetect->DetectMobileQuick() == true ) )
            {
                $ret = '<a href="tel:' . $ret . '">' . $link_text . ' </a>';
            }
            
            //Convert to JS snippet
            $ret = static::safe_text( $ret );
                
            //Add noscript fallback
            if( $use_noscript_fallback == true )
            {
                $ret .= '<noscript>' . $noscript_message . '</noscript>';
            }
            
            if( defined( LAOTN_DEBUG ) || $debug == true )
            {
                $ret .= '
                    <div class="tlao_debug">
                        --- LAOTN debug info: --- <br />
                        Raw tel string: ' . $tel . ' <br/>
                        Linkable: ' . $linkable . ' <br/>
                        Link title: ' . $link_title . ' <br/>
                        noscript fallback: ' . $use_noscript_fallback . '<br/>
                        noscript message: ' . $noscript_message . '<br/>
                        --- End of LAOTN debug info ---
                    </div>
                ';      
            }

        }
        return $ret;
    }



    /**
     * Encodes every character in $text into its numeric html representation.
     * http://stackoverflow.com/questions/3005116/how-to-convert-all-characters-to-their-html-entity-equivalent-using-php/3005240
     *
     * @param string    String which should be encoded
     * @return          Returns the fully HTML encoded string
     * @since           0.2
     * @static
     */
    public static function html_entities_all($text)
    {
        $text = mb_convert_encoding( $text , 'UTF-32', 'UTF-8' );
        $t = unpack( "N*", $text );
        $t = array_map( array( 'LAOTN', 'html_entities_closure_wrap' ), $t );
        
        return implode("", $t);
    }



    /**
     * This method has been added purely for servers that have a PHP version of 
     * less that 5.3
     *
     * @param string    String to be wrapped
     * @return          Returns the completed string
     * @since           0.2
     * @static
     */
    public static function html_entities_closure_wrap($n)
    {
        return "&#$n;";
    }


    /**
     * The actual obfuscator function
     * http://khromov.wordpress.com/2011/10/04/php-function-for-scrambling-e-mail-addressesphone-numbers-using-javascript/
     *
     * @param string    Characters to Obfuscate
     * @return          Returns the obfuscated text
     * @since           0.2
     * @static
     **/
    public static function safe_text($text)
    {
        //Check if text is UTF-8 and decode if it is
        if( mb_detect_encoding( $text, 'UTF-8', true ) )
        {
            $text = utf8_decode( $text );
        }
        
        //Create the obfuscation array
        $chars = str_split( $text );

        $enc[] = rand( 0, 255 );

        foreach( $chars as $char ) {
            $enc[] = ord( $char ) - $enc[ sizeof( $enc ) - 1 ];
        }
        
        $finished_array = join( ',', $enc ); 
        
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
