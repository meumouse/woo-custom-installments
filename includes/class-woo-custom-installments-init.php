<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Init class plugin
 * 
 * @since 1.0.0
 * @version 3.8.0
 * @package MeuMouse.com
 */
class Woo_Custom_Installments_Init {

  public $responseObj;
  public $licenseMessage;
  public $showMessage = false;
  public $activateLicense = false;
  public $deactivateLicense = false;
  
  public function __construct() {
    add_action( 'plugins_loaded', array( $this, 'woo_custom_installments_set_default_options' ), 998 );
    add_action( 'plugins_loaded', array( $this, 'woo_custom_installments_connect_api' ), 999 );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_update_table_installments' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_update_checkout' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'disable_update_installments' ) );
  }


  /**
   * Gets the items from the array and inserts them into the option if it is empty,
   * or adds new items with default value to the option
   * 
   * @since 2.0.0
   * @version 3.8.0
   * @return void
   */
  public function woo_custom_installments_set_default_options() {
    $get_options = $this->set_default_data_options();
    $default_options = get_option('woo-custom-installments-setting', array());

    if ( empty( $default_options ) ) {
        $options = $get_options;
        update_option('woo-custom-installments-setting', $options);
    } else {
        $options = $default_options;

        foreach ( $get_options as $key => $value ) {
            if ( !isset( $options[$key] ) ) {
                $options[$key] = $value;
            }
        }

        update_option('woo-custom-installments-setting', $options);
    }
  }


  /**
   * Set default options
   * 
   * @since 2.0.0
   * @version 3.8.0
   * @return array
   */
  public function set_default_data_options() {
    $options = array(
      'enable_installments_all_products' => 'yes',
      'remove_price_range' => 'no',
      'custom_text_after_price' => 'no',
      'set_fee_per_installment' => 'no',
      'disable_update_installments' => 'no',
      'enable_all_discount_options' => 'yes',
      'display_installments_cart' => 'yes',
      'include_shipping_value_in_discounts' => 'yes',
      'display_tag_discount_price_checkout' => 'yes',
      'display_discount_price_schema' => 'yes',
      'enable_functions_discount_per_quantity' => 'no',
      'enable_discount_per_quantity_method' => 'global',
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
      'enable_mastercard_flag_credit' => 'no',
      'enable_american_express_flag_credit' => 'no',
      'enable_paypal_flag_credit' => 'no',
      'enable_stripe_flag_credit' => 'no',
      'enable_mercado_pago_flag_credit' => 'no',
      'enable_pagseguro_flag_credit' => 'no',
      'enable_visa_flag_credit' => 'no',
      'enable_elo_flag_credit' => 'no',
      'enable_hipercard_flag_credit' => 'no',
      'enable_diners_club_flag_credit' => 'no',
      'enable_discover_flag_credit' => 'no',
      'enable_pagarme_flag_credit' => 'no',
      'enable_cielo_flag_credit' => 'no',
      'enable_mastercard_flag_debit' => 'no',
      'enable_american_express_flag_debit' => 'no',
      'enable_paypal_flag_debit' => 'no',
      'enable_stripe_flag_debit' => 'no',
      'enable_mercado_pago_flag_debit' => 'no',
      'enable_pagseguro_flag_debit' => 'no',
      'enable_visa_flag_debit' => 'no',
      'enable_elo_flag_debit' => 'no',
      'enable_hipercard_flag_debit' => 'no',
      'enable_diners_club_flag_debit' => 'no',
      'enable_discover_flag_debit' => 'no',
      'enable_pagarme_flag_debit' => 'no',
      'enable_cielo_flag_debit' => 'no',
      'center_group_elements_loop' => 'no',
      'fee_installments_global' => '2.0',
      'max_qtd_installments' => '12',
      'max_qtd_installments_without_fee' => '3',
      'min_value_installments' => '20',
      'display_discount_price_hook' => 'display_loop_and_single_product',
      'get_type_best_installments' => 'best_installment_without_fee',
      'hook_display_best_installments' => 'display_loop_and_single_product',
      'display_installment_type' => 'popup',
      'hook_payment_form_single_product' => 'before_cart',
      'text_before_price' => 'À vista',
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
      'enable_economy_pix_badge' => 'yes',
      'text_economy_pix_badge' => 'Economize %s no Pix',
      'display_economy_pix_hook' => 'only_single_product',
      'display_discount_ticket_hook' => 'global',
      'best_installments_order' => '1',
      'discount_pix_order' => '2',
      'economy_pix_order' => '3',
      'slip_bank_order' => '4',
      'text_discount_per_quantity_message' => 'Compre %d UN e ganhe %s de desconto',
    );

    return $options;
  }


  /**
   * Checks if the option exists and returns the indicated array item
   * 
   * @since 2.0.0
   * @version 3.8.0
   * @param $key | Array key
   * @return mixed | string or false
   */
  public static function get_setting( $key ) {
    $default_options = get_option('woo-custom-installments-setting', array());

    // check if array key exists and return key
    if ( isset( $default_options[$key] ) ) {
        return $default_options[$key];
    }

    return false;
  }


  /**
   * Enqueue scripts and styles
   *
   * @since 1.0.0
   * @version 3.8.0
   * @return void
   */
  public function enqueue_scripts() {
    if ( is_singular() ) {
        global $post;

        $params = array(
            'enable_discount_per_unit' => get_post_meta( $post->ID, 'enable_discount_per_unit', true ),
            'discount_per_unit_method' => get_post_meta( $post->ID, 'discount_per_unit_method', true ),
            'unit_discount_amount' => get_post_meta( $post->ID, 'unit_discount_amount', true ),
            'currency_symbol' => get_woocommerce_currency_symbol(),
        );

        wp_localize_script( 'woo-custom-installments-front-scripts', 'wci_front_params', $params );
    }

    wp_enqueue_script( 'woo-custom-installments-front-scripts', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/woo-custom-installments-front-scripts.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
    wp_enqueue_style( 'woo-custom-installments-front-styles', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/css/woo-custom-installments-front-styles.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
    wp_enqueue_script( 'font-awesome-lib', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/font-awesome.min.js', array(), '6.4.0' );

    if ( self::get_setting('display_installment_type') == 'popup' ) {
      wp_enqueue_script( 'woo-custom-installments-front-modal', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/modal.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
    } elseif ( self::get_setting('display_installment_type') == 'accordion' ) {
      wp_enqueue_script( 'woo-custom-installments-front-accordion', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/accordion.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
    }
  }


  /**
   * Update table installments
   * 
   * @since 2.9.0
   * @version 3.8.0
   * @return void
   */
  public function enqueue_update_table_installments() {
    // check if is product page
    if ( is_product() ) {
        $product_id = get_the_ID();
        $product = wc_get_product( $product_id );

        // check if product is variable
        if ( $product && $product->is_type('variable') && self::get_setting( 'display_installment_type' ) != 'hide' ) {
            wp_enqueue_script( 'woo-custom-installments-update-table-installments', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/woo-custom-installments-update-table-installments.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
            wp_enqueue_script( 'accounting-lib', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/accounting.min.js', array(), '0.4.2' );

            $interest = $this->get_fee();
            $installments_fee = array();

            foreach ( range( 1, self::get_setting( 'max_qtd_installments' ) ) as $i ) {
                $installments_fee[ $i ] = $this->get_fee( false, $i );
            }

            wp_localize_script( 'woo-custom-installments-update-table-installments', 'Woo_Custom_Installments_Params', apply_filters( 'woo_custom_installments_dynamic_table_params', array(
                'currency_format_num_decimals' => wc_get_price_decimals(),
                'currency_format_symbol' => get_woocommerce_currency_symbol(),
                'currency_format_decimal_sep' => esc_attr( wc_get_price_decimal_separator() ),
                'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
                'currency_format' => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
                'rounding_precision' => wc_get_rounding_precision(),
                'max_installments' => self::get_setting( 'max_qtd_installments' ),
                'max_installments_no_fee' => self::get_setting( 'max_qtd_installments_without_fee' ),
                'min_installment' => self::get_setting( 'min_value_installments' ),
                'fees' => $installments_fee,
                'fee' => $interest,
                'without_fee_label' => self::get_setting( 'text_without_fee_installments' ),
                'with_fee_label' => self::get_setting( 'text_with_fee_installments' ),
            ) ) );
        }
    }
  }


  /**
   * Enqueue update checkout script
   * 
   * @since 3.6.0
   * @version 3.8.0
   * @return void
   */
  public function enqueue_update_checkout() {
    if ( is_checkout() && self::get_setting('enable_all_discount_options') === 'yes' ) {
      wp_enqueue_script( 'woo-custom-installments-update-table-installments', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/update-checkout.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
    }
  }


  /**
   * Disable update table installments
   * 
   * @since 2.9.0
   * @version 3.8.0
   * @return void
   */
  public function disable_update_installments() {
    if ( self::get_setting('disable_update_installments') === 'yes' ) {
      wp_dequeue_script('woo-custom-installments-update-table-installments');
    }
  }
  

  /**
   * Get option interest of calc installments
   * 
   * @since 2.3.5
   * @version 3.8.0
   * @return string
  */
  public function get_fee( $product = false, $installments = 1 ) {
    $customFeeInstallments = array();
    $customFeeInstallments = get_option('woo_custom_installments_custom_fee_installments');
    $customFeeInstallments = maybe_unserialize( $customFeeInstallments );

    if ( self::get_setting('set_fee_per_installment') === 'yes' ) {
      $fee = isset( $customFeeInstallments[$installments]['amount'] ) ? floatval( $customFeeInstallments[$installments]['amount'] ) : 0;
    } else {
      $fee = self::get_setting( 'fee_installments_global' );
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
    $discount = self::get_setting( 'discount_main_price' );

    return apply_filters( 'woo_custom_installments_get_main_price_discount', $discount, $product );
  }


  /**
   * Load API settings
   * 
   * @since 2.0.0
   * @version 3.8.0
   */
  public function woo_custom_installments_connect_api() {
    if ( current_user_can('manage_woocommerce') ) {
      $this->responseObj = new stdClass();
      $this->responseObj->is_valid = false;
  
      $licenseKey = get_option('woo_custom_installments_license_key', '');
  
      // Save settings on active license
      if ( isset( $_POST['woo_custom_installments_active_license'] ) ) {
        delete_transient('woo_custom_installments_api_request_cache');
        delete_transient('woo_custom_installments_api_response_cache');
        update_option( 'woo_custom_installments_license_key', $_POST );
        $licenseKey = !empty( $_POST['woo_custom_installments_license_key'] ) ? $_POST['woo_custom_installments_license_key'] : '';
        update_option( 'woo_custom_installments_license_key', $licenseKey ) || add_option('woo_custom_installments_license_key', $licenseKey );
        update_option( '_site_transient_update_plugins', '' );
      }
  
      if ( get_option( 'woo_custom_installments_license_status' ) !== 'valid' ) {
        update_option( 'woo_custom_installments_license_key', '' );
      }
  
      // Save settings on deactive license, or remove license status if it is invalid
      if ( isset( $_POST['woo_custom_installments_deactive_license'] ) ) {
        if ( Woo_Custom_Installments_Api::RemoveLicenseKey( __FILE__, $message ) ) {
          update_option( 'woo_custom_installments_license_status', 'invalid' );
          update_option( 'woo_custom_installments_license_key', '' );
          update_option( '_site_transient_update_plugins', '' );
  
          $this->deactivateLicense = true;
        }
      }
  
      // Check on the server if the license is valid and update responses and options
      if ( Woo_Custom_Installments_Api::CheckWPPlugin( $licenseKey, $this->licenseMessage, $this->responseObj, __FILE__ ) ) {
        update_option( 'woo_custom_installments_license_status', 'valid' );

        if ( isset( $_POST['woo_custom_installments_active_license'] ) && $this->responseObj && $this->responseObj->is_valid ) {
          $this->activateLicense = true;
        }
      } else {
        if ( !empty( $licenseKey ) && !empty( $this->licenseMessage ) ) {
          update_option( 'woo_custom_installments_license_status', 'invalid' );
  
          $this->showMessage = true;
        }
      }

      // clear activation cache
      if ( isset( $_POST['woo_custom_installments_clear_activation_cache'] ) ) {
        delete_transient('woo_custom_installments_api_request_cache');
        delete_transient('woo_custom_installments_api_response_cache');
      }
    }
  }
  
}

new Woo_Custom_Installments_Init();