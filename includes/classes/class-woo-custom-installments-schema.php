<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class Woo_Custom_Installments_Schema extends Woo_Custom_Installments_Init {

  public function __construct() {
    parent::__construct();
    $licenseValid = get_option( 'license_status' ) == 'valid';

    if( $licenseValid ) {
      add_filter( 'woocommerce_structured_data_product_offer', array( $this, 'woo_custom_installments_schema_data_product' ), 20, 2 );
    } else {
      remove_filter( 'woocommerce_structured_data_product_offer', array( $this, 'woo_custom_installments_schema_data_product' ), 20, 2 );
    }
  }

  public function woo_custom_installments_schema_data_product( $markup, $product ) {
    $discount = $this->get_main_price_discount( $product );

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