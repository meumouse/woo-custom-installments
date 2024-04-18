<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Change colors on front-end
 *
 * @since 2.1.0
 * @version 3.8.0
 * @package MeuMouse.com
 */
class Woo_Custom_Installments_Custom_Design extends Woo_Custom_Installments_Init {

  public function __construct() {
    parent::__construct();

    add_action( 'wp_head', array( $this, 'woo_custom_installments_custom_design' ) );
  }

  /**
   * Custom option design
   * 
   * @since 2.0.0
   * @version 3.8.0
   * @return string
   */
  public function woo_custom_installments_custom_design() {
    $main_price_color = self::get_setting('discount_main_price_color');
    $main_price_bg = $this->hex2rgba( $main_price_color );
    $font_size_main_price = self::get_setting( 'font_size_discount_price' );
    $font_size_unit_main_price = self::get_setting( 'unit_font_size_discount_price' );
    $margin_top_main_price = self::get_setting( 'margin_top_discount_price' );
    $unit_margin_top_main_price = self::get_setting( 'unit_margin_top_discount_price' );
    $margin_bottom_main_price = self::get_setting( 'margin_bottom_discount_price' );
    $unit_margin_bottom_main_price = self::get_setting( 'unit_margin_bottom_discount_price' );
    $button_popup_color = self::get_setting( 'button_popup_color' );
    $button_popup_size = self::get_setting( 'button_popup_size' );
    $margin_top_popup = self::get_setting( 'margin_top_popup_installments' );
    $unit_margin_top_popup = self::get_setting( 'unit_margin_top_popup_installments' );
    $margin_bottom_popup = self::get_setting( 'margin_bottom_popup_installments' );
    $unit_margin_bottom_popup = self::get_setting( 'unit_margin_bottom_popup_installments' );
    $best_installments_color = self::get_setting( 'best_installments_color' );
    $font_size_best_installments = self::get_setting( 'font_size_best_installments' );
    $unit_font_size_best_installments = self::get_setting( 'unit_font_size_best_installments' );
    $margin_top_best_installments = self::get_setting( 'margin_top_best_installments' );
    $unit_margin_top_best_installments = self::get_setting( 'unit_margin_top_best_installments' );
    $margin_bottom_best_installments = self::get_setting( 'margin_bottom_best_installments' );
    $unit_margin_bottom_best_installments = self::get_setting( 'unit_margin_bottom_best_installments' );
    $border_radius_popup_installments = self::get_setting( 'border_radius_popup_installments' );
    $unit_border_radius_popup_installments = self::get_setting( 'unit_border_radius_popup_installments' );
    $border_radius_discount_main_price = self::get_setting( 'border_radius_discount_main_price' );
    $unit_border_radius_discount_main_price = self::get_setting( 'unit_border_radius_discount_main_price' );
    $center_group_elements_loop = Woo_Custom_Installments_Init::get_setting('center_group_elements_loop') === 'yes';
    $discount_ticket_color = self::get_setting( 'discount_ticket_color_badge' );
    $discount_ticket_color_bg = $this->hex2rgba( $discount_ticket_color );
    $font_size_discount_ticket = self::get_setting( 'font_size_discount_ticket' );
    $font_size_unit_discount_ticket = self::get_setting( 'unit_font_size_discount_ticket' );
    $margin_top_discount_ticket = self::get_setting( 'margin_top_discount_ticket' );
    $margin_top_unit_discount_ticket = self::get_setting( 'unit_margin_top_discount_ticket' );
    $margin_bottom_discount_ticket = self::get_setting( 'margin_bottom_discount_ticket' );
    $margin_bottom_unit_discount_ticket = self::get_setting( 'unit_margin_bottom_discount_ticket' );
    $border_radius_discount_ticket = self::get_setting( 'border_radius_discount_ticket' );
    $border_radius_unit_discount_ticket = self::get_setting( 'unit_border_radius_discount_ticket' );

    // main price styles
    $css = '.woo-custom-installments-offer {';
      $css .= 'color:'. $main_price_color .' !important;';
      $css .= 'background-color:'. $main_price_bg .' !important;';
      $css .= 'font-size:'. $font_size_main_price . $font_size_unit_main_price .' !important;';
      $css .= 'margin-top:'. $margin_top_main_price . $unit_margin_top_main_price .' !important;';
      $css .= 'margin-bottom:'. $margin_bottom_main_price . $unit_margin_bottom_main_price .' !important;';
      $css .= 'border-radius:'. $border_radius_discount_main_price . $unit_border_radius_discount_main_price .' !important;';
    $css .= '}';

    $css .= '.woo-custom-installments-offer .amount {';
      $css .= 'font-size:'. $font_size_main_price . $font_size_unit_main_price .' !important;';
      $css .= 'color:'. $main_price_color .' !important;';
    $css .= '}';

    $css .= '.instant-approval-badge, .badge-discount-checkout {';
      $css .= 'color:'. $main_price_color .' !important;';
      $css .= 'background-color:'. $main_price_bg .' !important;';
    $css .= '}';

    $css .= '.woo-custom-installments-economy-pix-badge {';
      $css .= 'background-color:'. $main_price_color .';';
    $css .= '}';

    // color and border button popup
    $css .= '#open-popup {';
      $css .= 'color:'. $button_popup_color .';';
      $css .= 'border-color:'. $button_popup_color .' !important;';
      $css .= 'border-radius:'. $border_radius_popup_installments . $unit_border_radius_popup_installments .' !important;';
    $css .= '}';

    $css .= '#open-popup:hover {';
      $css .= 'background-color:'. $button_popup_color .' !important;';
    $css .= '}';

    // button popup size
    if ( $button_popup_size == 'small' ) {
      $css .= '#open-popup {';
        $css .= 'padding: 0.475rem 1.25rem;';
        $css .= 'font-size: 0.75rem;';
      $css .= '}';
    } elseif ( $button_popup_size == 'normal' ) {
      $css .= '#open-popup {';
        $css .= 'padding: 0.625rem 1.75rem;';
        $css .= 'font-size: 0.875rem;';
      $css .= '}';
    } elseif ( $button_popup_size == 'large' ) {
      $css .= '#open-popup {';
        $css .= 'padding: 0.785rem 2rem;';
        $css .= 'font-size: 1rem;';
      $css .= '}';
    } else {
      $css .= '#open-popup {';
        $css .= 'display: inline-block;';
        $css .= 'position: relative;';
        $css .= 'font-weight: 500;';
        $css .= 'text-decoration: none;';
        $css .= 'border: 0;';
        $css .= 'padding: 0;';
        $css .= 'color:'. $button_popup_color .';';
      $css .= '}';
      $css .= '#open-popup::after {';
        $css .= 'content: "";';
        $css .= 'position: absolute;';
        $css .= 'bottom: 0;';
        $css .= 'right: 0;';
        $css .= 'width: 100%;';
        $css .= 'height: 0.0625rem;';
        $css .= 'background-color:'. $button_popup_color .';';
      $css .= '}';
      $css .= '#open-popup:hover {';
        $css .= 'text-decoration: none;';
        $css .= 'color:'. $button_popup_color .';';
        $css .= 'background-color: transparent !important;';
      $css .= '}';
      $css .= '#open-popup:hover::after {';
        $css .= '-webkit-animation: linkUnderline .6s ease-in-out;';
        $css .= 'animation: linkUnderline .6s ease-in-out;';
      $css .= '}';
      $css .= '@-webkit-keyframes linkUnderline {
        0% {
            width: 100%; }
        50% {
            width: 0; }
        100% {
            left: 0;
            width: 100%; }
        }
        
        @keyframes linkUnderline {
        0% {
            width: 100%; }
        50% {
            width: 0; }
        100% {
            left: 0;
            width: 100%; } 
        }';
    }

    $css .= '#open-popup, #accordion-installments {';
      $css .= 'margin-top:'. $margin_top_popup . $unit_margin_top_popup .' !important;';
      $css .= 'margin-bottom:'. $margin_bottom_popup . $unit_margin_bottom_popup .' !important;';
    $css .= '}';

    // card info styles
    $css .= '.woo-custom-installments-card-container {';
      $css .= 'color:'. $best_installments_color .' !important;';
      $css .= 'font-size:'. $font_size_best_installments . $unit_font_size_best_installments .' !important;';
      $css .= 'margin-top:'. $margin_top_best_installments . $unit_margin_top_best_installments .' !important;';
      $css .= 'margin-bottom:'. $margin_bottom_best_installments . $unit_margin_bottom_best_installments .' !important;';
    $css .= '}';

    $css .= '.woo-custom-installments-card-container .amount {';
      $css .= 'font-size:'. $font_size_best_installments . $unit_font_size_best_installments .' !important;';
      $css .= 'color:'. $best_installments_color .' !important;';
    $css .= '}';

    $css .= 'th.final-text .amount, th.final-price .amount {';
      $css .= 'font-size:'. $font_size_best_installments . $unit_font_size_best_installments .' !important;';
      $css .= 'color:'. $best_installments_color .' !important;';
    $css .= '}';

    /**
     * Center woo-custom-installments-group elements
     * 
     * @since 2.2.0
     * @version 3.6.0
     */
    if ( $center_group_elements_loop ) {
      $css .= '.archive .woo-custom-installments-group,
      .loop .woo-custom-installments-group,
      li.product .woo-custom-installments-group,
      li.wc-block-grid__product .woo-custom-installments-group,
      .product-grid-item .woo-custom-installments-group,
      .e-loop-item.product .woo-custom-installments-group,
      .swiper-slide .type-product .woo-custom-installments-group,
      .shopengine-single-product-item .woo-custom-installments-group,
      .products-list.grid .item-product .woo-custom-installments-group,
      .product-item.grid .woo-custom-installments-group,
      .card-product .woo-custom-installments-group {';
        $css .= 'justify-items: center;';
        $css .= 'align-items: center;';
        $css .= 'justify-content: center;';
      $css .= '}';
    }

    // discount ticket badge styles
    $css .= '.woo-custom-installments-ticket-discount {';
      $css .= 'color:'. $discount_ticket_color .' !important;';
      $css .= 'background-color:'. $discount_ticket_color_bg .' !important;';
      $css .= 'font-size:'. $font_size_discount_ticket . $font_size_unit_discount_ticket .' !important;';
      $css .= 'margin-top:'. $margin_top_discount_ticket . $margin_top_unit_discount_ticket .' !important;';
      $css .= 'margin-bottom:'. $margin_bottom_discount_ticket . $margin_bottom_unit_discount_ticket .' !important;';
      $css .= 'border-radius:'. $border_radius_discount_ticket . $border_radius_unit_discount_ticket .' !important;';
    $css .= '}';

    $css .= '.woo-custom-installments-ticket-discount .amount {';
      $css .= 'color:'. $discount_ticket_color .' !important;';
    $css .= '}';

    // best installments order
    $css .= '.woo-custom-installments-card-container {';
      $css .= 'order: '. Woo_Custom_Installments_Init::get_setting('best_installments_order') .' !important;';
    $css .= '}';

    // discount pix order
    $css .= '.woo-custom-installments-offer {';
      $css .= 'order: '. Woo_Custom_Installments_Init::get_setting('discount_pix_order') .' !important;';
    $css .= '}';

    // economy pix order
    $css .= '.woo-custom-installments-economy-pix-badge {';
      $css .= 'order: '. Woo_Custom_Installments_Init::get_setting('economy_pix_order') .' !important;';
    $css .= '}';

    // slip bank order
    $css .= '.woo-custom-installments-ticket-discount {';
      $css .= 'order: '. Woo_Custom_Installments_Init::get_setting('slip_bank_order') .' !important;';
    $css .= '}';

    // hide table variations if range price is activated
    if ( Woo_Custom_Installments_Init::get_setting('remove_price_range') === 'yes' ) {
      $css .= '.woocommerce-variation-price {';
        $css .= 'display: none !important;';
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

    if ( strlen( $hex ) === 3 ) {
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