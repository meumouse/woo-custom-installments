<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main class for call handlers
 * 
 * @package MeuMouse.com
 * @version 1.0.0
 */
class Woo_Custom_Installments_Init {

	public $woo_custom_installments_settings = array();
  public $plugin_file = __FILE__;
  public $responseObj;
  public $licenseMessage;
  public $showMessage = false;
  public $activateLicense = false;
  public $deactivateLicense = false;
  
  public function __construct() {
    add_action( 'plugins_loaded', array( $this, 'load_api_settings' ), 999 );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_update_table_installments' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'disable_update_checkout' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'disable_update_installments' ) );

    // load plugin setting
    if ( empty( $this->woo_custom_installments_settings ) ) {
      $this->woo_custom_installments_settings = get_option( 'woo-custom-installments-setting' );
    }
  }


  /**
   * Load API settings
   * 
   * @since 2.0.0
   */
  public function load_api_settings() {
    $this->responseObj = new stdClass();
    $this->responseObj->is_valid = false;

    $licenseKey = get_option('woo_custom_installments_license_key', '');

    // Save settings on active license
    if ( current_user_can('manage_woocommerce') && isset( $_POST['woo_custom_installments_active_license'] ) ) {
      update_option( 'woo_custom_installments_license_key', $_POST );
      $licenseKey = !empty( $_POST['woo_custom_installments_license_key'] ) ? $_POST['woo_custom_installments_license_key'] : '';
      update_option( 'woo_custom_installments_license_key', $licenseKey ) || add_option('woo_custom_installments_license_key', $licenseKey );
      update_option( '_site_transient_update_plugins', '' );

      if ( $this->responseObj->is_valid ) {
        $this->activateLicense = true;
      }
      
      if ( get_option( 'woo_custom_installments_license_status' ) == 'invalid' ) {
        update_option( 'woo_custom_installments_license_key', '' );
        update_option( '_site_transient_update_plugins', '' );
      }
    }

    // Save settings on deactive license, or remove license status if it is invalid
    if ( current_user_can('manage_woocommerce') && ( isset( $_POST['woo_custom_installments_deactive_license'] ) )) {
      if ( Woo_Custom_Installments_Api::RemoveLicenseKey( __FILE__, $message ) ) {
        update_option( 'woo_custom_installments_license_status', 'invalid' );
        delete_option( 'woo_custom_installments_license_key' );
        update_option( '_site_transient_update_plugins', '' );

        $this->deactivateLicense = true;
      }
    }

    // Check on the server if the license is valid and update responses and options
    if ( Woo_Custom_Installments_Api::CheckWPPlugin( $licenseKey, $this->licenseMessage, $this->responseObj, __FILE__ ) ) {
      update_option( 'woo_custom_installments_license_status', 'valid' );
    } else {
      if ( !empty( $licenseKey ) && !empty( $this->licenseMessage ) ) {
        update_option( 'woo_custom_installments_license_status', 'invalid' );
        $this->showMessage = true;
      }
    }
  }


  /**
	 * Plugin default settings
   * 
   * @since 2.0.0
	 * @return array
	 * @access public
	 */
	public $default_settings = array(
    'enable_installments_all_products' => 'yes',
    'remove_price_range' => 'yes',
    'custom_text_after_price' => 'no',
    'set_fee_per_installment' => 'no',
    'set_fee_first_installment' => 'no',
    'disable_update_checkout' => 'no',
    'disable_update_installments' => 'no',
    'enable_all_discount_options' => 'yes',
    'display_installments_cart' => 'yes',
    'include_shipping_value_in_discounts' => 'yes',
    'display_tag_discount_price_checkout' => 'yes',
    'display_discount_price_schema' => 'yes',
    'enable_functions_discount_per_quantity' => 'no',
    'set_discount_per_quantity_global' => 'no',
    'enable_functions_discount_per_quantity_single_product' => 'no',
    'enable_discount_per_unit_discount_per_quantity' => 'no',
    'message_discount_per_quantity' => 'no',
    'display_tag_interest_checkout' => 'no',
    'enable_all_interest_options' => 'no',
    'enable_pix_method_payment_form' => 'no',
    'enable_instant_approval_badge' => 'no',
    'enable_ticket_method_payment_form' => 'no',
    'enable_ticket_discount_main_price' => 'no',
    'enable_credit_card_method_payment_form' => 'no',
    'enable_debit_card_method_payment_form' => 'no',
    'enable_mastercard_flag' => 'no',
    'enable_american_express_flag' => 'no',
    'enable_paypal_flag' => 'no',
    'enable_stripe_flag' => 'no',
    'enable_mercado_pago_flag' => 'no',
    'enable_pagseguro_flag' => 'no',
    'enable_visa_flag' => 'no',
    'enable_elo_flag' => 'no',
    'enable_hipercard_flag' => 'no',
    'enable_diners_club_flag' => 'no',
    'enable_discover_flag' => 'no',
    'enable_pagarme_flag' => 'no',
    'enable_cielo_flag' => 'no',
    'center_group_elements_loop' => 'yes',
    'fee_installments_global' => '2.0',
    'max_qtd_installments' => '12',
    'max_qtd_installments_without_fee' => '3',
    'min_value_installments' => '20',
    'display_discount_price_hook' => 'display_loop_and_single_product',
    'get_type_best_installments' => 'best_installment_without_fee',
    'hook_display_best_installments' => 'display_loop_and_single_product',
    'hook_display_best_installments_after_before_discount' => 'before_discount',
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
    'discount_main_price' => '10',
    'discount_main_price_color' => '#22c55e',
    'font_size_discount_price' => '1',
    'unit_font_size_discount_price' => 'rem',
    'margin_top_discount_price' => '1',
    'unit_margin_top_discount_price' => 'rem',
    'margin_bottom_discount_price' => '2',
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
    'margin_top_best_installments' => '0.5',
    'unit_margin_top_best_installments' => 'rem',
    'margin_bottom_best_installments' => '0',
    'unit_margin_bottom_best_installments' => 'rem',
    'icon_best_installments' => 'fa-regular fa-credit-card',
    'set_quantity_enable_discount' => '1',
    'discount_per_quantity_method' => 'percentage',
    'value_for_discount_per_quantity' => '0',
    'custom_text_after_price_front' => 'no Pix',
    'ticket_discount_icon' => 'fa-solid fa-barcode',
    'hook_order_discount_ticket' => 'after_main_discount',
    'discount_method_ticket' => 'percentage',
    'discount_ticket' => '0',
    'text_before_discount_ticket' => 'A vista',
    'text_after_discount_ticket' => 'no Boleto bancário',
    'discount_ticket_color_badge' => '#ffba08',
    'font_size_discount_ticket' => '1',
    'unit_font_size_discount_ticket' => 'rem',
    'margin_top_discount_ticket' => '1',
    'unit_margin_top_discount_ticket' => 'rem',
    'margin_bottom_discount_ticket' => '2',
    'unit_margin_bottom_discount_ticket' => 'rem',
    'border_radius_discount_ticket' => '0.3',
    'unit_border_radius_discount_ticket' => 'rem',
  );


  /**
	 * Function for get plugin general settings
	 * 
	 * @return string 
	 * @since 2.0.0
	 * @access public
	 */
	public function getSetting( $key ) {
    if ( ! empty( $this->woo_custom_installments_settings) && isset( $this->woo_custom_installments_settings[ $key ] ) ) {
      return $this->woo_custom_installments_settings[ $key ];
    }

    if ( isset( $this->default_settings[ $key ] ) ) {
      return $this->default_settings[ $key ];
    }

    return false;
  }


  /**
   * Enqueue scripts and styles
   *
   * @return void
   * @since 1.0.0
   */
  public function enqueue_scripts() {
    global $post;

    wp_enqueue_script( 'woo-custom-installments-front-scripts', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/js/woo-custom-installments-front-scripts.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
    
    $params = array(
      'enable_discount_per_unit' => get_post_meta( $post->ID, 'enable_discount_per_unit', true ),
      'discount_per_unit_method' => get_post_meta( $post->ID, 'discount_per_unit_method', true ),
      'unit_discount_amount' => get_post_meta( $post->ID, 'unit_discount_amount', true ),
      'currency_symbol' => get_woocommerce_currency_symbol(),
    );

    wp_localize_script( 'woo-custom-installments-front-scripts', 'wci_front_params', $params );

    wp_enqueue_style( 'woo-custom-installments-front-styles', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/css/woo-custom-installments-front-styles.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
    wp_enqueue_script( 'font-awesome-lib', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/js/font-awesome.min.js', array(), '6.4.0' );
  }


  /**
   * Update table installments
   * 
   * @since 2.9.0
   * @return void
   * @package MeuMouse.com
   */
  public function enqueue_update_table_installments() {
    // check if is product page
    if ( is_product() ) {
        $product_id = get_the_ID();
        $product = wc_get_product( $product_id );

        // check if product is variable
        if ( $product && $product->is_type('variable') && $this->getSetting( 'display_installment_type' ) != 'hide' ) {
            wp_enqueue_script( 'woo-custom-installments-update-table-installments', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/js/woo-custom-installments-update-table-installments.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
            wp_enqueue_script( 'accounting-lib', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/js/accounting.min.js', array(), '0.4.2' );

            $interest = $this->get_fee();
            $installments_fee = array();

            foreach ( range( 1, $this->getSetting( 'max_qtd_installments' ) ) as $i ) {
                $installments_fee[ $i ] = $this->get_fee( false, $i );
            }

            wp_localize_script( 'woo-custom-installments-update-table-installments', 'Woo_Custom_Installments_Params', apply_filters( 'woo_custom_installments_dynamic_table_params', array(
                'currency_format_num_decimals' => wc_get_price_decimals(),
                'currency_format_symbol' => get_woocommerce_currency_symbol(),
                'currency_format_decimal_sep' => esc_attr( wc_get_price_decimal_separator() ),
                'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
                'currency_format' => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
                'rounding_precision' => wc_get_rounding_precision(),
                'max_installments' => $this->getSetting( 'max_qtd_installments' ),
                'max_installments_no_fee' => $this->getSetting( 'max_qtd_installments_without_fee' ),
                'min_installment' => $this->getSetting( 'min_value_installments' ),
                'fees' => $installments_fee,
                'fee' => $interest,
                'without_fee_label' => $this->getSetting( 'text_without_fee_installments' ),
                'with_fee_label' => $this->getSetting( 'text_with_fee_installments' ),
            ) ) );
        }
    }
  }


  /**
   * Disable update checkout
   * 
   * @since 2.3.5
   * @return bool
   */
  public function disable_update_checkout() {
    $options = get_option( 'woo-custom-installments-setting' );
    
    if ( is_checkout()  && isset( $options['disable_update_checkout'] ) && $options['disable_update_checkout'] == 'yes' ) {
      wp_dequeue_script('woo-custom-installments-front-scripts');
    }
  }


  /**
   * Disable update table installments
   * 
   * @since 2.9.0
   * @return bool
   */
  public function disable_update_installments() {
    $options = get_option( 'woo-custom-installments-setting' );
    
    if ( isset( $options['disable_update_installments'] ) && $options['disable_update_installments'] == 'yes' ) {
      wp_dequeue_script('woo-custom-installments-update');
    }
  }
  

  /**
   * Get option interest of calc installments
   * 
   * @since 2.3.5
   * @return string
  */
  public function get_fee( $product = false, $installments = 1 ) {
    $options = get_option( 'woo-custom-installments-setting' );
    $customFeeInstallments = array();
    $customFeeInstallments = get_option('woo_custom_installments_custom_fee_installments');
    $customFeeInstallments = maybe_unserialize( $customFeeInstallments );

    if ( isset( $options['set_fee_per_installment'] ) && $options['set_fee_per_installment'] == 'yes' ) {
      $fee = isset( $customFeeInstallments[$installments]['amount'] ) ? floatval( $customFeeInstallments[$installments]['amount'] ) : 0;
    } else {
      $fee = $this->getSetting( 'fee_installments_global' );
    }
    
    return apply_filters( 'woo_custom_installments_fee', $fee, $product, $installments );
  }


  /**
   * Get discount in main price
   * 
   * @since 2.3.5
   * @return string
  */
  public function get_main_price_discount( $product = false ) {
    // default discount
    $discount = $this->getSetting( 'discount_main_price' );
    return apply_filters( 'woo_custom_installments_get_main_price_discount', $discount, $product );
  }
  
}

new Woo_Custom_Installments_Init();