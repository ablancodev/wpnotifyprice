<?php
class ClassWPNotifyPrice_Shortcodes {
    
    public static function init() {
        add_shortcode( 'woonotifyprice_button', array( __CLASS__, 'woonotifyprice_button' ) );
    }
    
    public static function woonotifyprice_button( $attr ) {   
        return WPNotifyPrice_Template::modal();
    }  
}
ClassWPNotifyPrice_Shortcodes::init();
?>