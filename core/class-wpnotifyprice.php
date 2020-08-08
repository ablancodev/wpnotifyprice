<?php 
class WPNotifyPrice {
 
    public static function save_lead( $email, $product_id ) {
        global $wpdb;
        
        $rows_affected = $wpdb->replace( $wpdb->base_prefix . 'wpnotifyprice_lead', array( 'email' => $email, 'product_id' => $product_id ) );
        return $rows_affected;
    }
    
    public static function send_notifications( $product_id ) {
        global $wpdb;
        
        $leads = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->base_prefix . "wpnotifyprice_lead WHERE product_id = %d", intval( $product_id ) ) );
        if ( $leads && ( sizeof( $leads ) > 0 ) ) {
            $header = array('Content-Type: text/html; charset=UTF-8');
            foreach ( $leads as $lead ) {
                $header[] = 'BCC: ' . $lead->email;
            }
            
            $subject = get_option('wc_settings_tab_wpnotifyprice_subject', __('The price has dropped !!', 'wpnotifyprice'));
            $message = get_option('wc_settings_tab_wpnotifyprice_message', __('We have good news!<br> The price of [product_link] has dropped. Get it !!', 'wpnotifyprice'));
            
            // Mandamos el email
            @wp_mail( '', $subject, $message, $header );
        }
    }
}
?>