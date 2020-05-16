<?php
class ClassWPNotifyPrice_Shortcodes {
    
    public static function init() {
        add_shortcode( 'ads', array( __CLASS__, 'my_ads_shortcode' ) );
    }
    
    public static function my_ads_shortcode( $attr ) {
                
        ob_start();
        get_template_part( 'ads' );
        return ob_get_clean();
    }  
}
ClassWPNotifyPrice_Shortcodes::init();
?>