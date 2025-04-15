<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\API\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Include Schema.org format for products on search engine
 * 
 * @since 2.0.0
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Schema {

	/**
	 * Construct function
	 * 
	 * @since 2.0.0
	 * @version 5.2.0
	 * @package MeuMouse.com
	 */
	public function __construct() {
		if ( License::is_valid() ) {
			add_filter( 'woocommerce_structured_data_product_offer', array( $this, 'schema_data_product' ), 20, 2 );
		}
	}


	/**
	 * Add product price with discount on Schema.org
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @param array $markup | Array of params
	 * @param \WC_Product $product | Product object
	 */
	public function schema_data_product( $markup, $product ) {
		$price = self::get_price();
		$discounted_price = self::apply_discount( $price );
		$prices = array( 'lowPrice', 'highPrice', 'price' );

		foreach ( $prices as $price_key ) {
			if ( isset( $markup[$price_key] ) ) {
				$markup[$price_key] = $discounted_price;
			}
		}

		return $markup;
	}


	/**
	 * Get discount percentage
	 * 
	 * @since 5.2.0
	 * @version 5.4.0
	 * @return float
	 */
	public static function get_discount() {
		// Get discount from settings or product meta
		$discount = Admin_Options::get_setting('discount_main_price');
		
		return floatval( $discount );
	}


	/**
	 * Apply discount to a given price
	 * 
	 * @since 5.2.0
	 * @version 5.4.0
	 * @param float $price | Original price
	 * @return float
	 */
	public static function apply_discount( $price ) {
		$discount = $this->get_discount();

		return wc_format_decimal( $price - ( $price * ( $discount / 100 ) ), wc_get_price_decimals() );
	}
}

if ( Admin_Options::get_setting('display_discount_price_schema') === 'yes' ) {
  	new Schema();
}