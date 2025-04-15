<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

use \TierPricingTable\PriceManager;
use \WC_Product;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with Tiered Pricing Table plugin
 *
 * @since 4.5.2
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Tiered_Pricing_Table {

	/**
	 * Construct function
	 * 
	 * @since 4.5.2
     * @version 5.1.0
	 * @return void
	 */
	public function __construct() {
		add_filter( 'woo_custom_installments_set_values_price', array( $this, 'adjust_price_based_on_tiered_pricing' ), 10, 2 );
        add_filter( 'woo_custom_installments_dynamic_table_params', array( $this, 'check_tiered_plugin' ), 10, 1 );
	}


	/**
     * Adjust product price based on tiered pricing rules
     * 
     * @since 4.5.2
     * @version 5.1.0
     * @param float $price | Product price
     * @param WC_Product $product | Object product
     * @return float
     */
    public function adjust_price_based_on_tiered_pricing( $price, $product ) {
        if ( ! self::check_plugin() ) {
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


    /**
     * Check if Tiered Price Table is active plugin
     * 
     * @since 5.1.0
     * @return bool
     */
    public static function check_plugin() {
        if ( class_exists('TierPricingTable\PriceManager') ) {
            return true;
        }

        return false;
    }


    /**
     * Add param to update table script
     * 
     * @since 5.1.0
     * @param array $params | Current params
     * @return array
     */
    public function check_tiered_plugin( $params ) {
        $new_params = array(
            'check_tiered_plugin' => self::check_plugin(),
        );

        return array_merge( $params, $new_params );
    }
}