<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit; }

/**
 * Get list styles
 * 
 * @since 1.0.0
 * @return array
 */
function woo_custom_installments_get_available_styles() {
  return array(
    'default'     => __( 'Padrão', 'woo-custom-installments' ),
    'none'        => __( 'Não carregar CSS', 'woo-custom-installments' ),
  );
}

/**
 * If is single installment, hide table
 * 
 * @return string
 */
add_filter( 'woo_custom_installments_formatted_text', 'custom_formatted_installments', 10, 5 );
function custom_formatted_installments( $final_text, $parcela, $find, $replace, $parcelamento ) {
  if ( 1 === count( $parcelamento ) ) {
    return;
  }

  return $final_text;
}

/**
 * Integration with plugin wc-dynamic-pricing-and-discounts
 * 
 * @return void
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