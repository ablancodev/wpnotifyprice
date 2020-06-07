<?php
class ClassWPNotifyPrice_Woocommerce {
    
    public static function init() {
        // Mostramos el formulario y botón
        add_action( 'woocommerce_before_single_product_summary',  array( __CLASS__, 'woocommerce_before_single_product_summary' ),  10 );
        
        // Datectamos el cambio de precio
        add_action( 'woocommerce_before_product_object_save', array( __CLASS__, 'woocommerce_product_object_updated_props' ), 10, 2 );
        
    }
    
    function woocommerce_product_object_updated_props( $product, $data ) {

        $changes = $product->get_changes();
        
        $product_price = $product->get_price();
        $new_price = $product_price;
        if ( $product->is_on_sale() ) {
            if ( isset( $changes['sale_price'] ) ) {
                $new_price = $changes['sale_price'];
            }
        } else {
            if ( isset( $changes['regular_price'] ) ) {
                $new_price = $changes['regular_price'];
            }
        }
        
        if ( $new_price < $product_price ) { // Si ha bajado el precio ...
            WPNotifyPrice::send_notifications( $product->get_id() );    
        }
        
    }
    
    public static function woocommerce_before_single_product_summary() {
        echo WPNotifyPrice_Template::modal();
    }  
}
ClassWPNotifyPrice_Woocommerce::init();
?>

