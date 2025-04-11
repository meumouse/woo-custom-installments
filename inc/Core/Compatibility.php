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
class Compatibility {

    /**
     * Consctruct function
     * 
     * @since 1.0.0
     * @version 5.4.0
     * @return void
     */
    public function __construct() {
        add_action( 'before_woocommerce_init', array( $this, 'setup_hpos_compatibility' ) );
    }


    /**
     * Setup WooCommerce High-Performance Order Storage (HPOS) compatibility
     * 
     * @since 3.2.0
     * @version 5.4.0
     * @return void
     */
    public function setup_hpos_compatibility() {
        if ( defined('WC_VERSION') && version_compare( WC_VERSION, '7.1', '>' ) ) {
            if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WOO_CUSTOM_INSTALLMENTS_FILE, true );
            }
        }
    }
}