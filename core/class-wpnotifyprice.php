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
            // Mandamos el email
            @wp_mail( '', 'Bajada de precio', 'Tenemos buenas noticias.<br><a href="' . get_the_permalink( $product_id ) . '">Tu  producto favorito</a> ha bajado de precio.', $header );
        }
    }
}
?>