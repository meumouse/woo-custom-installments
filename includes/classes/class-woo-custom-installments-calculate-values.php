<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class Woo_Custom_Installments_Calculate_Values {

  /**
   * Calculate total value of single installment with interest
   *
   * @since 1.0.0
   * @param float $value (Base value for calc)
   * @param float $fee (Fee of interest)
   * @param int $installments (Total of installments)
   * @return float (Value of installment)
   */
  public static function calculate_installment_with_fee( $value, $fee, $installments ) {
    $percentage = wc_format_decimal( $fee ) / 100.00;
    $installment_price = $value * $percentage * ( ( 1 + $percentage ) ** $installments ) / ( ( ( 1 + $percentage ) ** $installments ) - 1 );

    return apply_filters( 'woo_custom_installments_with_fee', $installment_price, $value, $fee, $installments );
  }

  /**
   * Calculate total value of single installment without interest
   *
   * @since 1.0.0
   * @param float $value (Base value for calc)
   * @param int $installments (Total of installments)
   * @return float (Value of installment)
   */
  public static function calculate_installment_no_fee( $value, $installments ) {
    $installment_price = $value / $installments;
    return apply_filters( 'woo_custom_installments_no_fee', $installment_price, $value, $installments );
  }

  /**
   * Calcular o valor final de um parcelamento
   *
   * @since 1.0.0
   * @param float $value (Value of installment)
   * @param int $installments_total (Number total of installments)
   * @return float (Total value to be paid in installments without interest)
   */
  public static function calculate_final_price( $value, $installments_total ) {
    return apply_filters( 'woo_custom_installments_final_price', round( $value, 2 ) * $installments_total, $value, $installments_total );
  }

  /**
   * Calculate product value after apply one discount
   *
   * @since 1.0.0
   * @param float $value (Original value)
   * @param int|float $discount (Discount value)
   * @return float (Final value with discount)
   */
  public static function calculate_discounted_price( $value, $discount, $product = false ) {
    // it's not an empty value
    if ( $discount ) {
      $price = $value * ( ( 100 - $discount ) / 100 );
    } else {
      $price = $value;
    }

    return apply_filters( 'woo_custom_installments_discounted_price', $price, $value, $discount, $product );
  }

}