<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

class Woo_Custom_Installments_Schema {

  /**
   * Construct function
   * 
   * @since 2.0.0
   * @version 4.2.0
   * @package MeuMouse.com
   */
  public function __construct() {
    if ( Woo_Custom_Installments_Init::license_valid() ) {
      add_filter( 'woocommerce_structured_data_product_offer', array( $this, 'woo_custom_installments_schema_data_product' ), 20, 2 );
    }
  }


  /**
   * Add product price with discount on Schema.org
   * 
   * @since 2.0.0
   * @param array $markup | Array of params
   * @param int $product | Product ID
   */
  public function woo_custom_installments_schema_data_product( $markup, $product ) {
    $discount = Woo_Custom_Installments_Init::get_setting('discount_main_price');

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

new Woo_Custom_Installments_Schema();