<?php 
class WPNotifyPrice {
 
    public static function save_lead( $email, $product_id ) {
        global $wpdb;
        
        $rows_affected = $wpdb->replace( $wpdb->base_prefix . 'wpnotifyprice_lead', array( 'email' => $email, 'product_id' => $product_id ) );
        return $rows_affected;
    }
    
    public static function send_notifications( $product_id ) {
        error_log( "Enviamos notificaciones del producto: " . $product_id );
    }
}
?>