<?php
/**
 * WPNotifyPrice.php
 *
 * Copyright (c) 2011,2017 Antonio Blanco http://www.ablancodev.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author Antonio Blanco
 * @package wpnotifyprice
 * @since wpnotifyprice 1.0.0
 *
 * Plugin Name: WPNotifyPrice
 * Plugin URI: http://www.eggemplo.com
 * Description: 
 * Version: 1.0.0
 * Author: eggemplo
 * Author URI: http://www.ablancodev.com
 * Text Domain: wpnotifyprice
 * Domain Path: /languages
 * License: GPLv3
 */
if (! defined ( 'WPNOTIFYPRICE_CORE_DIR' )) {
    define ( 'WPNOTIFYPRICE_CORE_DIR', WP_PLUGIN_DIR . '/wpnotifyprice' );
}
define ( 'WPNOTIFYPRICE_FILE', __FILE__ );

define ( 'WPNOTIFYPRICE_PLUGIN_URL', plugin_dir_url ( WPNOTIFYPRICE_FILE ) );

class WPNotifyPrice_Plugin {
    
    public static $notices = array ();
    
    
    public static function init() {
        add_action ( 'init', array (
            __CLASS__,
            'wp_init'
        ) );
        add_action ( 'admin_notices', array (
            __CLASS__,
            'admin_notices'
        ) );
                
        register_activation_hook( WPNOTIFYPRICE_FILE, array( __CLASS__, 'activate' ) );
        
        // Ajax
        add_action( 'wp_ajax_custom_action', array( __CLASS__, 'custom_action' ) );
        add_action( 'wp_ajax_nopriv_custom_action', array( __CLASS__, 'custom_action' ) );
        
    }
    public static function wp_init() {
        load_plugin_textdomain ( 'wpnotifyprice', null, 'wpnotifyprice/languages' );
        
        // Core
        require_once 'core/class-wpnotifyprice.php';
        require_once 'core/class-wpnotifyprice-shortcodes.php';
        require_once 'core/class-wpnotifyprice-woocommerce.php';
        
        // Template
        require_once 'template/class-wpnotifyprice-template.php';
        
        // styles & javascript
        add_action ( 'wp_enqueue_scripts', array (
            __CLASS__,
            'wp_enqueue_scripts'
        ) );
    }
    
    public static function wp_enqueue_scripts($page) {
        // Incluir Bootstrap JS y dependencia popper
        wp_enqueue_script( 'popper_js',
            'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js',
            array(),
            '1.14.3',
            true);
        wp_enqueue_script( 'bootstrap_js',
            'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js',
            array('jquery','popper_js'),
            '4.1.3',
            true);
        
        wp_enqueue_script( 'wpnotifyprice_js',
            WPNOTIFYPRICE_PLUGIN_URL . '/js/wpnotifyprice-js.js',
            array('jquery'),
            microtime(),
            true);
        
        // Incluir Bootstrap CSS
        wp_enqueue_style( 'bootstrap_css',
            'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css',
            array(),
            '4.1.3'
            );

        // css
        //wp_register_style ( 'wpnotifyprice-style', WPNOTIFYPRICE_PLUGIN_URL . '/css/wpnotifyprice-style.css', array (), '1.0.0' );
        //wp_enqueue_style ( 'wpnotifyprice-style' );
        
        
    }
    
    public static function admin_notices() {
        if (! empty ( self::$notices )) {
            foreach ( self::$notices as $notice ) {
                echo $notice;
            }
        }
    }   
    
	/**
	 * Plugin activation work.
	 *
	 */
	public static function activate() {
	    self::setupDatabase();
	}
	
	private static function setupDatabase() {
	    global $wpdb;
	    
	    $charset_collate = '';
	    if ( ! empty( $wpdb->charset ) ) {
	        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	    }
	    if ( ! empty( $wpdb->collate ) ) {
	        $charset_collate .= " COLLATE $wpdb->collate";
	    }
	    
	    $queries = array();
	    
	    // Leads
	    $lead_table = $wpdb->base_prefix . 'wpnotifyprice_lead';
	    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $lead_table . "'" ) != $lead_table ) {
	        $queries[] = "CREATE TABLE " . $lead_table . "(
				email           VARCHAR(256) NOT NULL,
				product_id      BIGINT(20) UNSIGNED NOT NULL,
				notified        DATETIME DEFAULT NULL,
				PRIMARY KEY     (email, product_id),
				INDEX           wpnotifyprice_productid (product_id)
			) $charset_collate;";
	    }
	    
	    if ( !empty( $queries ) ) {
	        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	        dbDelta( $queries );
	    }
	}
	
	public static function custom_action() {
	    if (
	       ! isset( $_POST['wpnotifyprice_nonce_field'] )
	       || ! wp_verify_nonce( $_POST['wpnotifyprice_nonce_field'], 'custom_action_nonce')
	    ) {
	       exit('The form is not valid');
	    } else {
	        // Recogemos datos y guardamos
	        $email = sanitize_email( $_POST['email'] );
	        $product_id = intval( $_POST['product_id'] );
	        WPNotifyPrice::save_lead( $email, $product_id );
	        
	        
	        echo "Le avisaremos cuando baje el precio, muchas gracias por el interés.";
	        wp_die();
	    }
	}
}
WPNotifyPrice_Plugin::init();