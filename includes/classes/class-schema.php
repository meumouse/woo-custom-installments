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
 * @version 5.2.0
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
      add_filter( 'rank_math/json_ld', array( $this, 'rank_math_json_ld' ), 99, 2 );
    }
  }


  /**
   * Add product price with discount on Schema.org
   * 
   * @since 2.0.0
   * @version 5.2.0
   * @param array $markup | Array of params
   * @param \WC_Product $product | Product object
   */
  public function schema_data_product( $markup, $product ) {
    $price = $product->get_price();
    $discounted_price = $this->apply_discount( $price );

    foreach ( ['lowPrice', 'highPrice', 'price'] as $price_key ) {
        if ( isset( $markup[$price_key] ) ) {
            $markup[$price_key] = $discounted_price;
        }
    }

    return $markup;
  }


  /**
   * Modify Rank Math JSON-LD data
   * 
   * @since 5.2.0
   * @param array $data | JSON-LD data
   * @param object $jsonld | JSON-LD object
   */
  public function rank_math_json_ld( $data, $jsonld ) {
    $discount = $this->get_discount();

    // Check if there is a discount
    if ( 0 >= $discount ) {
      return $data;
    }

    if ( isset( $data['richSnippet']['offers']['price'] ) && $data['richSnippet']['@type'] === 'Product' ) {
      $data['richSnippet']['offers']['price'] = $this->apply_discount( $data['richSnippet']['offers']['price'] );
    }

    return $data;
  }


  /**
   * Get discount percentage
   * 
   * @since 5.2.0
   * @return float
   */
  private function get_discount() {
    // Get discount from settings or product meta
    $discount = Init::get_setting('discount_main_price');
    
    return floatval( $discount );
  }


  /**
   * Apply discount to a given price
   * 
   * @since 5.2.0
   * @param float $price | Original price
   * @return float
   */
  private function apply_discount( $price ) {
    $discount = $this->get_discount();

    return wc_format_decimal( $price - ( $price * ( $discount / 100 ) ), wc_get_price_decimals() );
  }

}

if ( Init::get_setting('display_discount_price_schema') === 'yes' ) {
  new Schema();
}