<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Integration with plugin wc-dynamic-pricing-and-discounts
 * 
 * @since 1.0.0
 * @version 4.3.5
 * @param string $price | Product price
 * @param object $product | Product object
 * @return string
 */
function woo_custom_installments_compatibility_wcdpd( $price, $product ) {
  if ( ! class_exists( 'RP_WCDPD', false ) ) {
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

add_filter( 'woo_custom_installments_set_values_price', 'woo_custom_installments_compatibility_wcdpd', 10, 2 );