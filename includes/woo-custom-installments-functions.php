<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit; }


/**
 * Integration with plugin wc-dynamic-pricing-and-discounts
 * 
 * @return void
 * @since 1.0.0
 */
add_filter( 'woo_custom_installments_set_values_price', 'woo_custom_installments_wc_dynamic_pricing_and_discounts_integration', 10, 2 );
function woo_custom_installments_wc_dynamic_pricing_and_discounts_integration( $price, $product ) {

  if ( ! class_exists( 'RP_WCDPD', false ) ) {
    return $price;
  }

  if ( 'variable' === $product->get_type() ) {
    $prices = array();
    foreach ( $product->get_available_variations() as $var ) {
      // Load variation
      $variation = wc_get_product( $var['variation_id'] );
      $prices[]  = $variation->get_price();
    }

    return $prices ? min( $prices ) : $price;
  }

  return $price;
}
