<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

use TierPricingTable\PriceManager;
use TierPricingTable\Services\ProductPageService;
use TierPricingTable\Core\ServiceContainer;
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
     * @version 5.4.0
	 * @return void
	 */
	public function __construct() {
        if ( class_exists('TierPricingTable\PriceManager') ) {
            // set price for installments list
            add_filter( 'Woo_Custom_Installments/Price/Set_Values_Price', array( $this, 'set_tier_price' ), 10, 2 );

            // set params to installments table
            add_filter( 'Woo_Custom_Installments/Assets/Frontend_Params', array( $this, 'check_tiered_plugin' ), 10, 1 );

            // Remove filters to avoid conflicts
            add_action( 'init', array( $this, 'remove_actions' ), 20 );
        }
	}


	/**
     * Set product price based on tiered pricing rules
     * 
     * @since 4.5.2
     * @version 5.4.0
     * @param float $price | Product price
     * @param WC_Product $product | Object product
     * @return float
     */
    public function set_tier_price( $price, $product ) {
        if ( ! self::check_plugin() ) {
            return $price;
        }

        $product_id = $product->get_id();
        $pricing_rule = PriceManager::getPricingRule( $product_id );

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


    /**
     * Remove actions and filters to avoid conflicts
     * 
     * @since 5.4.0
     * @return void
     */
    public function remove_actions() {
        $container = ServiceContainer::getInstance();
        $service = $container->get( ProductPageService::class );

        // Check if service is available
        if ( ! $service ) {
            return;
        }

        remove_action( 'woocommerce_get_price_html', array( $service, 'wrapPrice' ), 101 );
        remove_filter( 'woocommerce_get_price_html', array( $service, 'renderTooltip' ), 999 );
    }
}