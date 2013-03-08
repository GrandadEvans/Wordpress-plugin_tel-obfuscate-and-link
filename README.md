This plugin will convert telephone numbers to callable links if the user is on 
a smartphone. If the user is not on a smartphone the telephone number is 
displayed in plain text.

The text/link telephone number is then obfuscated to stop spambots intercepting
your number.

## Attributes ##
The following options are avaiable 
* Add custom link text such as "Call Me!"
* Turn the link feature off so that just plain telephone numbers are displayed*
* Turn the HTML_entities option on or off
* This is the long description.  No limit, and you can use Markdown (as well as in the following sections).
* Turn noscript on or off
* Add a custom noscript message

## Installation ##
### Download ###

Download the plugin and unzip it into your wp-admin/plugins directory.
Search the plugins from your admin area.

Click on the plugins option in the left hand menu of your admin screen and click “Add New”. Then do a search for “Tel: link & obfuscate” (without the quotes) and you should see this plugin.
How to use
To use the plugin all you have to do is add the shortcode
[tel-link-and-obf tel=”1234567890″]
obviously replacing 1234567890 with the phone number you wish to link to and/or obfuscate.
Shortcode Attributes

There are a few allowed attributes with the plugin. They are listed below and all but the tel attribute are optional as we can see here where all the options are set.
[tel-link-and-obf
    tel="123456790"
    link="1" link_text="Call Me!"
    html_entities="1"
    use_noscript_fallback="1"
    noscript_message="You don't have javascript enabled. It is needed to display this information"]
Attributes Explained

#### link ####
link=”0″ This will simply obfuscate the telephone number
link=”1″ This is the default and will add a tel: link (if on mobile/cell phone) and then obfuscate it.

#### link_text ####
link_text=”Your text here” eg link_text=”Call Me!” will display “Call Me!” instead of the telephone number

#### html_entities ####
html_entities=”1″ This is the default and will replace characters with their HTML code equivalent
html_entities=”0″ This will not convert the string into their HTML equivalent

#### use_noscript_fallback ####
use_noscript_fallback=”1″ This is the default and will display a message if the user does not have Javascript enabled
use_noscript_fallback=”0″ This will not show the user an error message if he/she doesn't have Javascript enabled.

#### noscript_message ####
noscript_message=”You have not entered a telephone number for this shortcode.”, default
noscript_message=”Enter your own message”

## Bugs/Contributions ##

If you find a bug then please do not hesitate to contact me at  john@grandadevans.com
If you fancy contributing to the code you can find it on Github at https://github.com/evanswebdesign/Wordpress-plugin_tel-obfuscate-and-link
