<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/**
 * Main class for call handlers
 * 
 * @package MeuMouse.com
 * @version 1.0.0
 */
class Woo_Custom_Installments_Init {

	public $woo_custom_installments_settings = array();
  public $options = array();
  public $customFeeInstallments = array();
  public $plugin_file = __FILE__;
  public $responseObj;
  public $licenseMessage;
  public $showMessage = false;
  public $slug = 'woo-custom-installments';

  
  public function __construct() {
    global $wpdb, $options, $responseObj;

    add_action( 'init', array( $this, 'saveOptionsApi' ), 999 );
    add_action( 'init', array( $this, 'initApi' ), 999 );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'disable_update_checkout' ) );

    // load plugin setting
    if( empty( $this->woo_custom_installments_settings ) ) {
      $this->woo_custom_installments_settings = get_option( 'woo-custom-installments-setting' );
    }

  }


  /**
   * Load API settings
   * 
   * @since 2.0.0
   */
  public function initApi() {
    $this->responseObj = new stdClass();
    $this->responseObj->is_valid = false;

    $licenseKey = get_option( 'woo_custom_installments_license_key', '');
    $licenseEmail = get_option( 'woo_custom_installments_license_email', '');
    
    // Save settings on active license
    if( current_user_can( 'manage_woocommerce' ) && isset( $_POST[ 'active_license' ] ) ) {
      update_option( 'woo_custom_installments_license_key', $_POST );
      $licenseKey = !empty( $_POST[ 'license_key' ] ) ? $_POST[ 'license_key' ] : "";
      $licenseEmail = !empty( $_POST[ 'license_email' ] ) ? $_POST[ 'license_email' ] : "";
      update_option( 'woo_custom_installments_license_key', $licenseKey ) || add_option( 'woo_custom_installments_license_key', $licenseKey );
      update_option( 'woo_custom_installments_license_email', $licenseEmail) || add_option( 'woo_custom_installments_license_email', $licenseEmail );
      update_option( '_site_transient_update_plugins', '' );
      $activateLicense = true;
    }

    // Save settings on deactive license
    if( current_user_can( 'manage_woocommerce' ) && isset( $_POST[ 'deactive_license' ] ) ) {
      if( Woo_Custom_Installments_Api::RemoveLicenseKey( __FILE__, $message ) ) {
        update_option( 'woo_custom_installments_license_key', '' ) || add_option( 'woo_custom_installments_license_key', '' );
        update_option( '_site_transient_update_plugins', '');
        delete_option( 'license_status' );
        $deactivateLicense = true;
      }
    }

    if( Woo_Custom_Installments_Api::CheckWPPlugin( $licenseKey, $licenseEmail, $this->licenseMessage, $this->responseObj, __FILE__ ) ) {
      add_option( 'license_status', 'valid' );
      $this->responseObj->is_valid = true;
      return;
    } else {
      if( !empty( $licenseKey ) && !empty( $this->licenseMessage ) ) {
          add_option( 'license_status', 'invalid' );
          $this->responseObj->is_valid = false;
          $this->showMessage = true;
      }
    }
    
  }

  public function saveOptionsApi() {
    // Save settings on active license
    if( current_user_can( 'manage_woocommerce' ) && isset( $_POST[ 'active_license' ] ) ) {
      update_option( 'woo_custom_installments_license_key', $_POST );
      $licenseKey = ! empty( $_POST[ 'license_key' ] ) ? $_POST[ 'license_key' ] : "";
      $licenseEmail = ! empty( $_POST[ 'license_email' ] ) ? $_POST[ 'license_email' ] : "";
      update_option( 'woo_custom_installments_license_key', $licenseKey ) || add_option( 'woo_custom_installments_license_key', $licenseKey );
      update_option( 'woo_custom_installments_license_email', $licenseEmail) || add_option( 'woo_custom_installments_license_email', $licenseEmail );
      update_option( '_site_transient_update_plugins', '' );
      $activateLicense = true;
    }

    // Save settings on deactive license
    if( current_user_can( 'manage_woocommerce' ) && isset( $_POST[ 'deactive_license' ] ) ) {
      if( Woo_Custom_Installments_Api::RemoveLicenseKey( __FILE__, $message ) ) {
        update_option( 'woo_custom_installments_license_key', '' ) || add_option( 'woo_custom_installments_license_key', '' );
        update_option( '_site_transient_update_plugins', '');
        $deactivateLicense = true;
      }
    }
  }


  /**
	 * Plugin default settings
   * 
	 * @return array
	 * @since 2.0.0
	 * @access public
	 */
	public $default_settings = array(
    'fee_installments_global' => '2.0',
    'max_qtd_installments' => '12',
    'max_qtd_installments_without_fee' => '3',
    'min_value_installments' => '20',
    'display_discount_price_hook' => 'display_loop_and_single_product',
    'get_type_best_installments' => 'best_installment_without_fee',
    'hook_display_best_installments' => 'display_loop_and_single_product',
    'hook_display_best_installments_after_before_discount' => 'after_discount',
    'display_installment_type' => 'popup',
    'hook_payment_form_single_product' => 'before_cart',
    'text_before_price' => 'A vista',
    'text_after_price' => 'no Pix',
    'text_initial_variables' => 'A partir de',
    'text_button_installments' => 'Detalhes do parcelamento',
    'text_pix_container' => 'Transferências:',
    'text_ticket_container' => 'Boleto bancário:',
    'text_instructions_ticket_container' => 'Ao finalizar sua compra você receberá os detalhes para realizar o pagamento.',
    'text_credit_card_container' => 'Cartões de crédito:',
    'text_debit_card_container' => 'Cartões de débito:',
    'text_table_installments' => 'Parcelas:',
    'text_with_fee_installments' => 'com juros',
    'text_without_fee_installments' => 'sem juros',
    'text_container_payment_forms' => 'Formas de pagamento',
    'text_display_installments_payment_forms' => '{{ parcelas }}x de {{ valor }} {{ juros }}',
    'text_display_installments_loop' => 'Em até {{ parcelas }}x de {{ valor }} {{ juros }}',
    'text_display_installments_single_product' => 'Em até {{ parcelas }}x de {{ valor }} {{ juros }}',
    'product_price_discount_method' => 'percentage',
    'gateway_discount_method' => 'percentage',
    'icon_main_price' => 'fa-brands fa-pix',
    'discount_main_price' => '0',
    'discount_main_price_color' => '#22c55e',
    'font_size_discount_price' => '1',
    'unit_font_size_discount_price' => 'rem',
    'margin_top_discount_price' => '0.75',
    'unit_margin_top_discount_price' => 'rem',
    'margin_bottom_discount_price' => '1',
    'unit_margin_bottom_discount_price' => 'rem',
    'border_radius_discount_main_price' => '0.3',
    'unit_border_radius_discount_main_price' => 'rem',
    'button_popup_color' => '#008aff',
    'button_popup_size' => 'normal',
    'margin_top_popup_installments' => '1',
    'unit_margin_top_popup_installments' => 'rem',
    'margin_bottom_popup_installments' => '3',
    'unit_margin_bottom_popup_installments' => 'rem',
    'border_radius_popup_installments' => '0.25',
    'unit_border_radius_popup_installments' => 'rem',
    'best_installments_color' => '#343A40',
    'font_size_best_installments' => '1',
    'unit_font_size_best_installments' => 'rem',
    'margin_top_best_installments' => '1',
    'unit_margin_top_best_installments' => 'rem',
    'margin_bottom_best_installments' => '2',
    'unit_margin_bottom_best_installments' => 'rem',
    'icon_best_installments' => 'fa-regular fa-credit-card',
  );


  /**
	 * Function for get plugin general settings
	 * 
	 * @return string 
	 * @since 2.0.0
	 * @access public
	 */
	public function getSetting( $key ) {
    if( ! empty( $this->woo_custom_installments_settings) && isset( $this->woo_custom_installments_settings[ $key ] ) ) {
      return $this->woo_custom_installments_settings[ $key ];
    }

    if( isset( $this->default_settings[ $key ] ) ) {
      return $this->default_settings[ $key ];
    }

    return false;
  }


  /**
   * Enqueue scripts
   *
   * @return void
   * @since 1.0.0
   */
  public function enqueue_scripts() {
    wp_enqueue_script( 'font-awesome-lib', 'https://kit.fontawesome.com/f6bf37e2e4.js' );
    wp_enqueue_script( 'accounting-lib', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/js/accounting.min.js' );
    wp_enqueue_script( 'woo-custom-installments-front-scripts', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/js/front.js' );
    wp_enqueue_style( 'woo-custom-installments-front-styles', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/css/front.css' );

    $interest = $this->get_fee();
    $installments_fee = array();

    foreach ( range( 1, $this->getSetting( 'max_qtd_installments' ) ) as $i ) {
      $installments_fee[ $i ] = $this->get_fee( false, $i );
    }

    wp_localize_script( 'woo-custom-installments-front-scripts', 'Woo_Custom_Installments_Params', apply_filters( 'woo_custom_installments_dynamic_table_params', array(
      'currency_format_num_decimals'  => wc_get_price_decimals(),
      'currency_format_symbol'        => get_woocommerce_currency_symbol(),
      'currency_format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
      'currency_format_thousand_sep'  => esc_attr( wc_get_price_thousand_separator() ),
      'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
      'rounding_precision'            => wc_get_rounding_precision(),
      'max_installments'              => $this->getSetting( 'max_qtd_installments' ),
      'max_installments_no_fee'       => $this->getSetting( 'max_qtd_installments_without_fee' ),
      'min_installment'               => $this->getSetting( 'min_value_installments' ),
      'fees'                          => $installments_fee,
      'fee'                           => $interest,
      'without_fee_label'             => $this->getSetting( 'text_without_fee_installments' ),
      'with_fee_label'                => $this->getSetting( 'text_with_fee_installments' ),
    ) ) );
  }


  /**
   * Disable update checkout
   * 
   * @return bool
   * @since 2.3.5
   */
  public function disable_update_checkout() {
    $options = get_option( 'woo-custom-installments-setting' );
    
    if ( is_checkout()  && isset( $options['disable_update_checkout'] ) == 'yes' ) {
      wp_dequeue_script('woo-custom-installments-front-scripts');
    }
  }


  /**
   * Get option interest of calc installments
   * 
   * @return string
   * @since 2.3.5
  */
  public function get_fee( $product = false, $installments = 1 ) {
    $options = get_option( 'woo-custom-installments-setting' );
    $customFeeInstallments = array();
    $customFeeInstallments = get_option('woo_custom_installments_custom_fee_installments');
    $customFeeInstallments = maybe_unserialize( $customFeeInstallments );

    if ( isset( $options['set_fee_per_installment'] ) == 'yes' ) {
      $fee = isset( $customFeeInstallments[$installments]['amount'] ) ? floatval( $customFeeInstallments[$installments]['amount'] ) : 0;
    } else {
      $fee = $this->getSetting( 'fee_installments_global' );
    }
    
    return apply_filters( 'woo_custom_installments_fee', $fee, $product, $installments );
  }


  /**
   * Get discount in main price
   * 
   * @return string
   * @since 2.3.5
  */
  public function get_main_price_discount( $product = false ) {
    // default discount
    $discount = $this->getSetting( 'discount_main_price' );
    return apply_filters( 'woo_custom_installments_get_main_price_discount', $discount, $product );
  }
  
}

new Woo_Custom_Installments_Init();