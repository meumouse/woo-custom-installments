<?php

namespace MeuMouse\Woo_Custom_Installments\Compat;

use \TierPricingTable\PriceManager;
use \WC_Product;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with Tiered Pricing Table plugin
 *
 * @since 4.5.2
 * @package MeuMouse.com
 */
class TierPricingTable {

	/**
	 * Construct function
	 * 
	 * @since 4.5.2
	 * @return void
	 */
	public function __construct() {
		add_filter( 'woo_custom_installments_set_values_price', array( $this, 'adjust_price_based_on_tiered_pricing' ), 10, 2 );
	}


	/**
     * Adjust product price based on tiered pricing rules
     * 
     * @since 4.5.2
     * @param float $price | Product price
     * @param WC_Product $product | Object product
     * @return float
     */
    public function adjust_price_based_on_tiered_pricing( $price, $product ) {
        if ( ! class_exists('TierPricingTable\PriceManager') ) {
            return $price;
        }

        $pricing_rule = PriceManager::getPricingRule( $product->get_id() );

        if ( $pricing_rule ) {
            // Get the tier price for the minimum quantity set in the pricing rule
            $min_quantity = $pricing_rule->getMinimum();
            $tier_price = $pricing_rule->getTierPrice( $min_quantity );

            // If a valid tier price is found, return it
            if ( $tier_price ) {
                return $tier_price;
            }
        }

        return $price;
    }
}

new TierPricingTable();