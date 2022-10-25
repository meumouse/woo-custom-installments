<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/**
 * Class that handles strings on the front-end
 * 
 * @package MeuMouse.com
 * @version 1.0.0
 */
class Woo_Custom_Installments_Init {

  public static $display_text_formatted;
  public static $display_text_formatted_loop;
  public static $display_text_formatted_main_price;
  public static $change_schema;
  public static $always_show_ticket;


  public function __construct() {
    self::set_variables();

    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ), 999 );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ), 999 );
  }


  public static function set_variables() {
    self::set_global_variables();
  }

  /**
   * Enqueue scripts
   *
   * @return void
   */
  public function enqueue_script() {
    wp_enqueue_script( 'bootstrap', WOO_CUSTOM_INSTALLMENTS_URL . '/assets/js/bootstrap.min.js', array( 'jquery', 'accounting' ), '5.2.2', true );

    wp_enqueue_script( 'woo-custom-installments', WOO_CUSTOM_INSTALLMENTS_URL . '/assets/js/woo-custom-installments.js', array( 'jquery', 'accounting' ), '1.0.0', true );

    $default_fee      = Woo_Custom_Installments_Init::get_fee();
    $installments_fee = array();

    foreach ( range( 1, Woo_Custom_Installments_Init::get_installments_limit() ) as $i ) {
      $installments_fee[ $i ] = Woo_Custom_Installments_Init::get_fee( false, $i );
    }

    wp_localize_script( 'woo-custom-installments', 'Woo_Custom_Installments_Params', apply_filters( 'woo_custom_installments_dynamic_table_params', array(
      'currency_format_num_decimals'  => wc_get_price_decimals(),
      'currency_format_symbol'        => get_woocommerce_currency_symbol(),
      'currency_format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
      'currency_format_thousand_sep'  => esc_attr( wc_get_price_thousand_separator() ),
      'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
      'rounding_precision'            => wc_get_rounding_precision(),
      'max_installments'              => Woo_Custom_Installments_Init::get_installments_limit(),
      'max_installments_no_fee'       => Woo_Custom_Installments_Init::get_max_installment_no_fee(),
      'min_installment'               => Woo_Custom_Installments_Init::get_min_installment(),
      'fees'                          => $installments_fee,
      'fee'                           => $default_fee,
      'without_fee_label'             => __( 'sem juros', 'woo-custom-installments' ),
      'with_fee_label'                => __( 'com juros', 'woo-custom-installments' ),
    ) ) );
  }

  /**
   * Enqueue the CSS
   *
   * @return void
   */
  public function enqueue_css() {

    if ( apply_filters( 'woo_custom_installments_load_css', true ) ) {
      $style = get_option( 'woo_custom_installments_style', 'default' );

      if ( ! in_array( $style, array_keys( woo_custom_installments_get_available_styles() ) ) ) {
        $style = 'default';
      }

      if ( 'none' === $style ) {
        return;
      }

      $files = array(
        'default'     => 'default.css',
      );

      wp_enqueue_style( 'woo-custom-installments-css', WOO_CUSTOM_INSTALLMENTS_URL . '/assets/css/' . $files[ $style ], 999 );
    }
  }

  /**
   * Define global variables
   * 
   * @return self
   */
  public static function set_global_variables() {
    self::$display_text_formatted      = get_option( 'woo_custom_installments_text_formatted', '{{ parcelas }}x de {{ valor }} {{ juros }}' );
    self::$display_text_formatted_loop = get_option( 'woo_custom_installments_text_formatted_loop', 'Em até {{ parcelas }}x de {{ valor }}' );
    self::$display_text_formatted_main_price = get_option( 'woo_custom_installments_text_formatted_main_price', 'Em até {{ parcelas }}x de {{ valor }} {{ juros }}' );
    self::$change_schema                 = get_option( 'woo_custom_installments_change_schema', 'yes' );
    self::$always_show_ticket            = get_option( 'woo_custom_installments_always_show_ticket', 'no' );
  }

  /**
   * Get discount in main price
   * 
   * @return string
  */
  public static function get_main_price_discount( $product = false ) {
    // default discount
    $discount = get_option( 'woo_custom_installments_main_price', 0 );
    return apply_filters( 'woo_custom_installments_get_main_price_discount', $discount, $product );
  }

  /**
   * Get text after price
   * 
   * @return string
  */
  public static function get_text_after_price( $product = false ) {
    // default value
    $text = get_option( 'woo_custom_installments_text_after_price', __( 'no Pix', 'woo-custom-installments' ) );
    return apply_filters( 'woo_custom_installments_text_after_price', $text, $product );
  }

  /**
   * Get option visibility full table
   * 
   * @return bool
  */
  public static function get_table_visibility( $product = false ) {
    $table_visibility = get_option( 'woo_custom_installments_display_full_table', 'hide' );
    return apply_filters( 'woo_custom_installments_table_visibility', $table_visibility, $product );
  }

  /**
   * Get formatted text
   * 
   * @return string
  */
  public static function get_table_formatted_text( $product = false ) {
    $text = get_option( 'woo_custom_installments_table_formatted_text', '{{ parcelas }}x de {{ valor }} {{ juros }}' );
    return apply_filters( 'woo_custom_installments_table_formatted_text', $text, $product );
  }

  /**
   * Get option display shop page
   * 
   * @return bool
  */
  public static function get_shop_page_view( $product = false ) {
    $shop_page = get_option( 'woo_custom_installments_display_shop_page', 'hide' );
    return apply_filters( 'woo_custom_installments_shop_page', $shop_page, $product );
  }

  /**
   * Get option display single product page
   * 
   * @return bool
  */
  public static function get_single_page_view( $product = false ) {
    $single_page = get_option( 'woo_custom_installments_display_single_product', 'hide' );
    return apply_filters( 'woo_custom_installments_single_page', $single_page, $product );
  }

  /**
   * Visibility price with discount
   * 
   * @return bool
  */
  public static function get_ticket_visibility( $product = false ) {
    $visibility = get_option( 'woo_custom_installments_ticket_visibility', 'both' );
    return apply_filters( 'woo_custom_installments_ticket_visibilty', $visibility, $product );
  }

  /**
   * Get option installment min value
   * 
   * @return string
  */
  public static function get_min_installment( $product = false ) {
    $min = get_option( 'woo_custom_installments_min_value_installment', 0 );
    return apply_filters( 'woo_custom_installments_min_installment', $min, $product );
  }

  /**
   * Get option max installment without interest
   * 
   * @return string
  */
  public static function get_max_installment_no_fee( $product = false ) {
    $default = get_option( 'woo_custom_installments_max_installments_without_interest', 0 );
    return apply_filters( 'woo_custom_installments_max_installments_no_fee', $default, $product );
  }

  /**
   * Get option limit of installments
   * 
   * @return string
  */
  public static function get_installments_limit( $product = false ) {
    $limit = get_option( 'woo_custom_installments_max_installments', 0 );
    return apply_filters( 'woo_custom_installments_installments_limit', $limit, $product );
  }

  /**
   * Get option interest of calc installments
   * 
   * @return string
  */
  public static function get_fee( $product = false, $installments = 1 ) {
    $fee = get_option( 'woo_custom_installments_fee_interest', 0 );
    return apply_filters( 'woo_custom_installments_fee', $fee, $product, $installments );
  }

  /**
   * Get option display in page cart
   * 
   * @return bool
  */
  public static function show_in_cart() {
    return get_option( 'woo_custom_installments_show_in_cart', 'yes' );
  }
  
}