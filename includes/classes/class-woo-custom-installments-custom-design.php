<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }


/**
 * Integration functions
 *
 * @package  MeuMouse.com
 * @since  2.1.0
 */

class Woo_Custom_Installments_Custom_Design extends Woo_Custom_Installments_Init {


  public function __construct() {
    parent::__construct();

    add_action( 'wp_head', array( $this, 'woo_custom_installments_custom_design' ) );
  }

  /**
   * Custom option design
   * 
   * @return string
   * @since 2.0.0
   */
  public function woo_custom_installments_custom_design() {
    $main_price_color = $this->getSetting('discount_main_price_color');
    $main_price_bg = $this->hex2rgba( $main_price_color );
    $font_size_main_price = $this->getSetting( 'font_size_discount_price' );
    $font_size_unit_main_price = $this->getSetting( 'unit_font_size_discount_price' );
    $margin_top_main_price = $this->getSetting( 'margin_top_discount_price' );
    $unit_margin_top_main_price = $this->getSetting( 'unit_margin_top_discount_price' );
    $margin_bottom_main_price = $this->getSetting( 'margin_bottom_discount_price' );
    $unit_margin_bottom_main_price = $this->getSetting( 'unit_margin_bottom_discount_price' );
    $button_popup_color = $this->getSetting( 'button_popup_color' );
    $button_popup_size = $this->getSetting( 'button_popup_size' );
    $margin_top_popup = $this->getSetting( 'margin_top_popup_installments' );
    $unit_margin_top_popup = $this->getSetting( 'unit_margin_top_popup_installments' );
    $margin_bottom_popup = $this->getSetting( 'margin_bottom_popup_installments' );
    $unit_margin_bottom_popup = $this->getSetting( 'unit_margin_bottom_popup_installments' );
    $best_installments_color = $this->getSetting( 'best_installments_color' );
    $font_size_best_installments = $this->getSetting( 'font_size_best_installments' );
    $unit_font_size_best_installments = $this->getSetting( 'unit_font_size_best_installments' );
    $margin_top_best_installments = $this->getSetting( 'margin_top_best_installments' );
    $unit_margin_top_best_installments = $this->getSetting( 'unit_margin_top_best_installments' );
    $margin_bottom_best_installments = $this->getSetting( 'margin_bottom_best_installments' );
    $unit_margin_bottom_best_installments = $this->getSetting( 'unit_margin_bottom_best_installments' );
    $get_position_best_installments = $this->getSetting( 'hook_display_best_installments_after_before_discount' );
    $border_radius_popup_installments = $this->getSetting( 'border_radius_popup_installments' );
    $unit_border_radius_popup_installments = $this->getSetting( 'unit_border_radius_popup_installments' );
    $border_radius_discount_main_price = $this->getSetting( 'border_radius_discount_main_price' );
    $unit_border_radius_discount_main_price = $this->getSetting( 'unit_border_radius_discount_main_price' );
    $options = get_option( 'woo-custom-installments-setting' );
    $center_group_elements_loop = isset( $options['center_group_elements_loop'] ) == 'yes';
    $center_group_elements_single_product = isset( $options['center_group_elements_single_product'] ) == 'yes';
  
    // main price styles
    $css = '.woo-custom-installments-offer {';
      $css .= 'color:'. $main_price_color .';';
      $css .= 'background-color:'. $main_price_bg .';';
      $css .= 'font-size:'. $font_size_main_price . $font_size_unit_main_price .';';
      $css .= 'margin-top:'. $margin_top_main_price . $unit_margin_top_main_price .';';
      $css .= 'margin-bottom:'. $margin_bottom_main_price . $unit_margin_bottom_main_price .';';
      $css .= 'border-radius:'. $border_radius_discount_main_price . $unit_border_radius_discount_main_price .';';
    $css .= '}';

    $css .= '.woo-custom-installments-offer .amount {';
      $css .= 'color:'. $main_price_color .';';
    $css .= '}';

    $css .= '.instant-approval-badge, .badge-discount-checkout {';
      $css .= 'color:'. $main_price_color .';';
      $css .= 'background-color:'. $main_price_bg .';';
    $css .= '}';

    // color and border button popup
    $css .= '#open-popup {';
      $css .= 'color:'. $button_popup_color .';';
      $css .= 'border-color:'. $button_popup_color .';';
      $css .= 'border-radius:'. $border_radius_popup_installments . $unit_border_radius_popup_installments .';';
    $css .= '}';

    $css .= '#open-popup:hover {';
      $css .= 'background-color:'. $button_popup_color .';';
    $css .= '}';

    // button popup size
    if( $button_popup_size == 'small' ) {
      $css .= '#open-popup {';
        $css .= 'padding: 0.475rem 1.25rem;';
        $css .= 'font-size: 0.75rem;';
      $css .= '}';
    } elseif( $button_popup_size == 'normal' ) {
      $css .= '#open-popup {';
        $css .= 'padding: 0.625rem 1.75rem;';
        $css .= 'font-size: 0.875rem;';
      $css .= '}';
    } else {
      $css .= '#open-popup {';
        $css .= 'padding: 0.785rem 2rem;';
        $css .= 'font-size: 1rem;';
      $css .= '}';
    }

    $css .= '#open-popup, #accordion-installments {';
      $css .= 'margin-top:'. $margin_top_popup . $unit_margin_top_popup .';';
      $css .= 'margin-bottom:'. $margin_bottom_popup . $unit_margin_bottom_popup .';';
    $css .= '}';

    // card info styles
    $css .= '.woo-custom-installments-card-container {';
      $css .= 'color:'. $best_installments_color .';';
      $css .= 'font-size:'. $font_size_best_installments . $unit_font_size_best_installments .';';
      $css .= 'margin-top:'. $margin_top_best_installments . $unit_margin_top_best_installments .';';
      $css .= 'margin-bottom:'. $margin_bottom_best_installments . $unit_margin_bottom_best_installments .';';
    $css .= '}';

    $css .= '.woo-custom-installments-card-container .amount {';
      $css .= 'color:'. $best_installments_color .';';
    $css .= '}';

    $css .= 'th.final-text .amount, th.final-price .amount {';
      $css .= 'color:'. $best_installments_color .';';
    $css .= '}';

    /**
     * Change order position discount and card info
     * 
     * @since 2.1.0
     */
    if( $get_position_best_installments == 'after_discount' ) {
      $css .= '.woo-custom-installments-offer {';
        $css .= 'order: 1;';
      $css .= '}';
      $css .= '.woo-custom-installments-card-container {';
        $css .= 'order: 2;';
      $css .= '}';
    } else {
      $css .= '.woo-custom-installments-card-container {';
        $css .= 'order: 1;';
      $css .= '}';
      $css .= '.woo-custom-installments-offer {';
        $css .= 'order: 2;';
      $css .= '}';
    }

    /**
     * Center woo-custom-installments-group elements
     * 
     * @since 2.2.0
     */
    if( $center_group_elements_single_product ) {
      $css .= '.woo-custom-installments-group.single-product {';
        $css .= 'justify-items: center;';
      $css .= '}';
    }

    if( $center_group_elements_loop ) {
      $css .= '.price .woo-custom-installments-group:not(.single-product) {';
        $css .= 'justify-items: center;';
      $css .= '}';
    }

    ?>
    <style type="text/css">
      <?php echo $css; ?>
    </style> <?php
  }


  /**
   * Generate background color for discount main price
   * 
   * @return string
   * @since 2.0.0
   */
  public function hex2rgba( $color, $opacity = 0.1 ) {
    $hex = str_replace('#', '', $color);

    if( strlen( $hex ) === 3 ) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2);
    }

    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );
    $a = $opacity;

    return "rgba($r, $g, $b, $a)";
  }

}

new Woo_Custom_Installments_Custom_Design();