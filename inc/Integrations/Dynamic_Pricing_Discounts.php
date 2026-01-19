<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

use \WC_Product;

// Exit if accessed directly.
defined('ABSPATH') || exit;

// check if the plugin Dynamic Pricing and Discounts is active
if ( class_exists('RP_WCDPD') ) {
    /**
     * Compatibility with Dynamic Pricing and Discounts plugin
     *
     * @since 4.5.2
     * @version 5.5.1
     * @package MeuMouse\Woo_Custom_Installments\Integrations
     * @author MeuMouse.com
     */
    class Dynamic_Pricing_Discounts {

        /**
         * Construct function
         * 
         * @since 4.5.2
         * @version 5.5.1
         * @return void
         */
        public function __construct() {
            add_filter( 'Woo_Custom_Installments/Price/Set_Values_Price', array( $this, 'add_compatibility_wcdpd' ), 10, 2 );
        }


        /**
         * Integration with plugin wc-dynamic-pricing-and-discounts
         * 
         * @since 1.0.0
         * @version 5.4.0
         * @param float $price | Product price
         * @param WC_Product $product | Object product
         * @return float
         */
        public function add_compatibility_wcdpd( $price, $product ) {
            if ( ! class_exists('RP_WCDPD') ) {
                return $price;
            }
        
            if ( $product->get_type() === 'variable' ) {
                $prices = array();
            
                foreach ( $product->get_available_variations() as $var ) {
                    // Load variation
                    $variation = wc_get_product( $var['variation_id'] );
                    $prices[] = $variation->get_price();
                }
            
                return $prices ? min( $prices ) : $price;
            }
        
            return $price;
        }
    }
}