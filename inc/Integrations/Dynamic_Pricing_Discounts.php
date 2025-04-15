<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

use \WC_Product;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with Dynamic Pricing and Discounts plugin
 *
 * @since 4.5.2
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Dynamic_Pricing_Discounts {

	/**
	 * Construct function
	 * 
	 * @since 4.5.2
	 * @return void
	 */
	public function __construct() {
		add_filter( 'woo_custom_installments_set_values_price', array( $this, 'add_compatibility_wcdpd' ), 10, 2 );
	}


    /**
     * Integration with plugin wc-dynamic-pricing-and-discounts
     * 
     * @since 1.0.0
     * @version 4.5.2
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