<?php
class ClassWPNotifyPrice_Shortcodes {
    
    public static function init() {
        add_shortcode( 'wpnotifyprice', array( __CLASS__, 'wpnotifyprice' ) );
    }
    
    public static function wpnotifyprice( $attr ) {   
        return WPNotifyPrice_Template::modal();
    }  
}
ClassWPNotifyPrice_Shortcodes::init();
?>