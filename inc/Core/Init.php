<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Init class plugin
 * 
 * @since 1.0.0
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Init {

    /**
     * Construct function
     * 
     * @since 1.0.0
     * @version 5.4.0
     * @return void
     */
    public function __construct() {
        load_plugin_textdomain( 'woo-custom-installments', false, dirname( WOO_CUSTOM_INSTALLMENTS_BASENAME ) . '/languages/' );

        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
    
        // check if WooCommerce is active
        if ( is_plugin_active('woocommerce/woocommerce.php') && version_compare( WC_VERSION, '6.0', '>' ) ) {
            self::instance_classes();
            
            add_filter( 'plugin_action_links_' . WOO_CUSTOM_INSTALLMENTS_BASENAME, array( $this, 'add_action_plugin_links' ), 10, 4 );
            add_filter( 'plugin_row_meta', array( $this, 'add_row_meta_links' ), 10, 4 );
            
            $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

            // remove Pro badge if plugin is licensed
            if ( get_option('woo_custom_installments_license_status') !== 'valid' && false !== strpos( $url, 'wp-admin/plugins.php' ) ) {
                add_filter( 'plugin_action_links_' . WOO_CUSTOM_INSTALLMENTS_BASENAME, array( $this, 'display_be_pro_badge' ), 10, 4 );
                add_action( 'admin_head', array( $this, 'be_pro_badge_styles' ) );
            }
        } else {
            add_action( 'admin_notices', array( $this, 'woo_version_notice' ) );
            deactivate_plugins('woo-custom-installments/woo-custom-installments.php');
            add_action( 'admin_notices', array( $this, 'require_woocommerce_notice' ) );
        }
    }


    /**
     * WooCommerce version notice
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @return void
     */
    public function woo_version_notice() {
        $class = 'notice notice-error is-dismissible';
        $message = __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer a versão do WooCommerce 6.0 ou maior. Faça a atualização do plugin WooCommerce.', 'woo-custom-installments' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
    }


    /**
     * Notice if WooCommerce is deactivate
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @return void
     */
    public function require_woocommerce_notice() {
        if ( current_user_can('install_plugins') ) {
            $class = 'notice notice-error is-dismissible';
            $message = __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer que <strong>WooCommerce</strong> esteja instalado e ativado.', 'woo-custom-installments' );

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
        }
    }


    /**
     * Plugin action links
     * 
     * @since 1.0.0
     * @version 5.4.0
     * @return array
     */
    public function add_action_plugin_links( $action_links ) {
        $plugins_links = array(
            '<a href="' . admin_url('admin.php?page=woo-custom-installments') . '">'. __( 'Configurar', 'woo-custom-installments' ) .'</a>',
        );

        return array_merge( $plugins_links, $action_links );
    }


    /**
     * Add meta links on plugin
     * 
     * @since 4.0.0
     * @version 5.4.0
     * @param string $plugin_meta | An array of the plugin’s metadata, including the version, author, author URI, and plugin URI
     * @param string $plugin_file | Path to the plugin file relative to the plugins directory
     * @param array $plugin_data | An array of plugin data
     * @param string $status | Status filter currently applied to the plugin list
     * @return string
     */
    public function add_row_meta_links( $plugin_meta, $plugin_file, $plugin_data, $status ) {
        if ( strpos( $plugin_file, WOO_CUSTOM_INSTALLMENTS_BASENAME ) !== false ) {
            $new_links = array(
                'docs' => '<a href="'. WOO_CUSTOM_INSTALLMENTS_DOCS_LINK .'" target="_blank">'. __( 'Documentação', 'woo-custom-installments' ) .'</a>',
            );
            
            $plugin_meta = array_merge( $plugin_meta, $new_links );
        }
    
        return $plugin_meta;
    }


    /**
     * Plugin action links Pro version
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @return array
     */
    public static function display_be_pro_badge( $action_links ) {
        $plugins_links = array(
            '<a id="get-pro-woo-custom-installments" target="_blank" href="https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugins-list&utm_campaign=wci">' . __( 'Seja PRO', 'woo-custom-installments' ) . '</a>'
        );
    
        return array_merge( $plugins_links, $action_links );
    }


    /**
     * Display badge in CSS for get pro in plugins page
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @access public
     */
    public function be_pro_badge_styles() {
        ob_start(); ?>

        #get-pro-woo-custom-installments {
            display: inline-block;
            padding: 0.35em 0.6em;
            font-size: 0.8125em;
            font-weight: 600;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            background-color: #008aff;
            transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
        }
        
        #get-pro-woo-custom-installments:hover {
            background-color: #0078ed;
        }

        <?php $css = ob_get_clean();
        $css = wp_strip_all_tags( $css );

        printf( __('<style>%s</style>'), $css );
    }

    
    /**
     * Instance classes after load Composer
     * 
     * @since 5.4.0
     * @return void
     */
    public static function instance_classes() {
        /**
         * Filter to add new classes
         * 
         * @since 5.4.0
         * @param array $classes | Array with classes to instance
         */
        $classes = apply_filters( 'Woo_Custom_Installments/Init/Instance_Classes', array(
            '\MeuMouse\Woo_Custom_Installments\Core\Compatibility',
            '\MeuMouse\Woo_Custom_Installments\API\License',
            '\MeuMouse\Woo_Custom_Installments\Admin\Admin_Options',
            '\MeuMouse\Woo_Custom_Installments\Core\Assets',
            '\MeuMouse\Woo_Custom_Installments\Core\Ajax',
            '\MeuMouse\Woo_Custom_Installments\Core\Frontend',
            '\MeuMouse\Woo_Custom_Installments\Cron\Routines',
            '\MeuMouse\Woo_Custom_Installments\Integrations\Elementor',
        	'\MeuMouse\Woo_Custom_Installments\Core\Updater',
        ));

        // iterate for each class and instance it
        foreach ( $classes as $class ) {
            if ( class_exists( $class ) ) {
                new $class();
            }
        }
    }
}