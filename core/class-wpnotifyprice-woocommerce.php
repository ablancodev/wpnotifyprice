<?php
class ClassWPNotifyPrice_Woocommerce {
    
    public static function init() {
        // Mostramos el formulario y botón
        add_action( 'woocommerce_before_single_product_summary',  array( __CLASS__, 'woocommerce_before_single_product_summary' ),  10 );
        
        // Datectamos el cambio de precio
        add_action( 'woocommerce_before_product_object_save', array( __CLASS__, 'woocommerce_product_object_updated_props' ), 10, 2 );
        
    }
    
    function woocommerce_product_object_updated_props( $product, $data ) {

        /*
        error_log( print_r( $product->get_price(), true ) );
        error_log( print_r( $product->is_on_sale(), true ) );
        error_log( print_r( $product->get_changes(), true ) );
        */
        
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
        
        //error_log( print_r( $data, true ) );
        
        /*
        $_pf = new WC_Product_Factory();
        $product = $_pf->get_product($post_before->ID);
        error_log( print_r( $product->get_price(), true ) );
        
        
        $_pf2 = new WC_Product_Factory();
        $product2 = $_pf2->get_product($post_after->ID);
        error_log( print_r( $product2->get_price(), true ) );
        */
        
        /*
        $changed_props = $product->get_changes();
        error_log( print_r( $changed_props, true ) );
        error_log( print_r( $product->get_price(), true ) );
        
        $new_price = in_array( 'price', $changed_props, true ) ? $changed_props['price'] : $product->get_price();
        if ( $product->get_price() > $new_price )
        {
            error_log( "Ha bajado el precio");
            //wp_update_post( array( 'ID' => $product->get_id(), 'post_status' => 'pending' ) );
        }
        */
    }
    
    public static function woocommerce_before_single_product_summary() {
        ?>
        <!-- Modal Starts -->
        <div class="modal fade" id="bootstrapModal" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                    	<h4 class="modal-title">Avísame si baja el precio</h4>
                    </div>
                    <!-- Modal Body -->
                    <div class="modal-body">
                    	<form class="wordpress-ajax-form" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
                    	<input type="text" placeholder="Su email" name="email" />
                    	<input type="hidden" name="product_id" value="<?php echo get_the_ID();?>"/>
                    	<input type="submit" value="Enviar" />
                    	<input type="hidden" name="action" value="custom_action">
						<?php wp_nonce_field( 'custom_action_nonce', 'wpnotifyprice_nonce_field' ); ?>
                    	</form>
                    </div>
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                    	<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close');?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Ends -->
        
        <!-- Modal Trigger -->
    	<button type="button" class="btn btn-info" data-toggle="modal" data-target="#bootstrapModal">Avísame si baja de precio</button>
        <?php
    }  
}
ClassWPNotifyPrice_Woocommerce::init();
?>

