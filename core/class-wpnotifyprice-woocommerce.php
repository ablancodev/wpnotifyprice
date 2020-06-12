<?php
class ClassWPNotifyPrice_Woocommerce {
    
    public static function init() {
        // Mostramos el formulario y botón en la ifcha de producto si tenemos la opción activada
        if ( get_option('wc_settings_tab_wpnotifyprice_display', 'yes') == 'yes' ) {
            add_action( 'woocommerce_before_single_product_summary',  array( __CLASS__, 'woocommerce_before_single_product_summary' ),  10 );
        }
        
        // Datectamos el cambio de precio, y si procede, mandamos emails.
        add_action( 'woocommerce_before_product_object_save', array( __CLASS__, 'woocommerce_product_object_updated_props' ), 10, 2 );
        
        // Creamos una sección de configuración (tab en Woocommerce)
        add_filter( 'woocommerce_settings_tabs_array', array( __CLASS__, 'woocommerce_settings_tabs_array'), 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_wpnotifyprice', array( __CLASS__, 'woocommerce_settings_tabs_settings_tab_wpnotifyprice' ) );
        add_action( 'woocommerce_update_options_settings_tab_wpnotifyprice', array( __CLASS__, 'woocommerce_update_options_settings_tab_wpnotifyprice' ) );
    }
    
    
    public function woocommerce_product_object_updated_props( $product, $data ) {

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
    
    /* Settings */
    public static function woocommerce_settings_tabs_array( $settings_tabs ) {
        $settings_tabs['settings_tab_wpnotifyprice'] = __( 'WP Notify Price', 'wpnotifyprice' );
        return $settings_tabs;
    }
    
    public static function woocommerce_settings_tabs_settings_tab_wpnotifyprice() {
        woocommerce_admin_fields( self::getSettings() );
    }
    public static function woocommerce_update_options_settings_tab_wpnotifyprice() {
        woocommerce_update_options( self::getSettings() );
    }
    
    private static function getSettings() {
        $settings = array(
            'section_title' => array(
                'name'     => __( 'Woocommerce Notify Price', 'wpnotifyprice' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_wpnotifyprice_section_title'
            ),
            'display' => array(
                'name'    => __( 'Display on product page', 'wpnotifyprice' ),
                'type'    => 'checkbox',
                'desc'    => __( 'Display the button on the product page.', 'woocommerce-settings-tab-demo' ),
                'id'      => 'wc_settings_tab_wpnotifyprice_display',
                'std'     => 'yes', // WooCommerce < 2.0
                'default' => 'yes' // WooCommerce >= 2.0
            ),
            'shortcode' => array(
                'name'    => __( 'Shortcode', 'wpnotifyprice' ),
                'type'    => 'title',
                'desc'    => __( 'You can use the shortcode: [wpnotifyprice] where you can display que button.', 'woocommerce-settings-tab-demo' ),
                'id'      => 'wc_settings_tab_wpnotifyprice_shortcode_text'
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_tab_wpnotifyprice_section_end'
            )
        );
        return apply_filters( 'wc_settings_tab_wpnotifyprice_settings', $settings );
    }
}
ClassWPNotifyPrice_Woocommerce::init();
?>

