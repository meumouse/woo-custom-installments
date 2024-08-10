<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Include Schema.org format for products on search engine
 * 
 * @since 2.0.0
 * @version 4.5.0
 * @package MeuMouse.com
 */
class Schema {

  /**
   * Construct function
   * 
   * @since 2.0.0
   * @version 4.5.0
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
   * @param array $markup | Array of params
   * @param int $product | Product ID
   */
  public function schema_data_product( $markup, $product ) {
    $discount = Init::get_setting('discount_main_price');

    // Check if there is discount
    if ( 0 >= $discount ) {
      return $markup;
    }

    if ( isset( $markup['lowPrice'] ) ) {
      $markup['lowPrice'] = wc_format_decimal( $markup['lowPrice'] - ( $markup['lowPrice'] * ( $discount / 100 ) ), wc_get_price_decimals() );
    }

    if ( isset( $markup['highPrice'] ) ) {
      $markup['highPrice'] = wc_format_decimal( $markup['highPrice'] - ( $markup['highPrice'] * ( $discount / 100 ) ), wc_get_price_decimals() );
    }

    if ( isset( $markup['price'] ) ) {
      $markup['price'] = wc_format_decimal( $markup['price'] - ( $markup['price'] * ( $discount / 100 ) ), wc_get_price_decimals() );
    }

    return $markup;
  }

}

if ( Init::get_setting('display_discount_price_schema') === 'yes' ) {
  new Schema();
}