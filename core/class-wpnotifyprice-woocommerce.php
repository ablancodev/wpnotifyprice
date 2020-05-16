<?php
class ClassWPNotifyPrice_Woocommerce {
    
    public static function init() {
        add_action( 'woocommerce_before_single_product_summary',  array( __CLASS__, 'woocommerce_before_single_product_summary' ),  10 );
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
                    	<input type="text" placeholder="Su email" name="wpnotifyprice_email" />
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

