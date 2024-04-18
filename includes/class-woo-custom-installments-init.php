<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Init class plugin
 * 
 * @since 1.0.0
 * @version 4.3.0
 * @package MeuMouse.com
 */
class Woo_Custom_Installments_Init {

  public $responseObj;
  public $licenseMessage;
  public $show_message = false;
  public $active_license = false;
  public $deactive_license = false;
  public $clear_cache = false;
  public $site_not_allowed = false;
  public $product_not_allowed = false;
  

  /**
   * Consctruct function
   * 
   * @since 1.0.0
   * @version 4.3.0
   * @return void
   */
  public function __construct() {
    add_action( 'admin_init', array( $this, 'woo_custom_installments_set_default_options' ) );
    add_action( 'admin_init', array( $this, 'woo_custom_installments_connect_api' ) );
    add_action( 'admin_init', array( $this, 'alternative_activation_process' ) );
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
            if ( ! isset( $options[$key] ) ) {
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
   * @version 4.3.0
   * @return array
   */
  public function set_default_data_options() {
    return array(
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
      'text_before_discount_ticket' => 'À vista',
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
      'enable_post_meta_feed_xml_price' => 'no',
    );
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
   * Get option interest of calc installments
   * 
   * @since 2.3.5
   * @version 3.8.0
   * @return string
  */
  public static function get_fee( $product = false, $installments = 1 ) {
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
    $discount = self::get_setting('discount_main_price');

    return apply_filters( 'woo_custom_installments_get_main_price_discount', $discount, $product );
  }


  /**
   * Load API settings
   * 
   * @since 2.0.0
   * @version 4.0.0
   * @return void
   */
  public function woo_custom_installments_connect_api() {
    if ( current_user_can('manage_woocommerce') ) {
      $this->responseObj = new stdClass();
      $message = '';
      $license_key = get_option('woo_custom_installments_license_key', '');
  
      // active license action
      if ( isset( $_POST['woo_custom_installments_active_license'] ) ) {
        // clear response cache first
        delete_transient('woo_custom_installments_api_request_cache');
        delete_transient('woo_custom_installments_api_response_cache');

        $license_key = ! empty( $_POST['woo_custom_installments_license_key'] ) ? $_POST['woo_custom_installments_license_key'] : '';
        update_option( 'woo_custom_installments_license_key', $license_key ) || add_option('woo_custom_installments_license_key', $license_key );
        update_option( 'woo_custom_installments_temp_license_key', $license_key ) || add_option('woo_custom_installments_temp_license_key', $license_key );
      }

      if ( ! self::license_valid() ) {
        update_option( 'woo_custom_installments_license_status', 'invalid' );
      }

      // Check on the server if the license is valid and update responses and options
      if ( Woo_Custom_Installments_Api::check_purchase_key( $license_key, $this->licenseMessage, $this->responseObj, WOO_CUSTOM_INSTALLMENTS_FILE ) ) {
          if ( $this->responseObj && $this->responseObj->is_valid ) {
            update_option( 'woo_custom_installments_license_status', 'valid' );
            delete_option('woo_custom_installments_temp_license_key');
            delete_option('woo_custom_installments_alternative_license');

            $this->active_license = true;
          } else {
            update_option( 'woo_custom_installments_license_status', 'invalid' );
          }
      } else {
          if ( ! empty( $license_key ) && ! empty( $this->licenseMessage ) ) {
              $this->showMessage = true;
          }
      }

      // deactive license action
      if ( isset( $_POST['woo_custom_installments_deactive_license'] ) ) {
        if ( Woo_Custom_Installments_Api::RemoveLicenseKey( WOO_CUSTOM_INSTALLMENTS_FILE, $message ) ) {
          update_option( 'woo_custom_installments_license_status', 'invalid' );
          delete_option( 'woo_custom_installments_license_key' );
          delete_transient('woo_custom_installments_api_request_cache');
          delete_transient('woo_custom_installments_api_response_cache');
          delete_option('woo_custom_installments_license_response_object');
          delete_option('woo_custom_installments_alternative_license_decrypted');
          delete_option('woo_custom_installments_alternative_license_activation');
          delete_option('woo_custom_installments_temp_license_key');
          delete_option('woo_custom_installments_alternative_license');

          $this->deactive_license = true;
        }
      }

      // clear activation cache
      if ( isset( $_POST['woo_custom_installments_clear_activation_cache'] ) ) {
        delete_transient('woo_custom_installments_api_request_cache');
        delete_transient('woo_custom_installments_api_response_cache');

        $this->clear_cache = true;
      }
    }
  }


  /**
   * Generate alternative activation object from decrypted license
   * 
   * @since 4.3.0
   * @return void
   */
  public function alternative_activation_process() {
    $decrypted_license_data = get_option('woo_custom_installments_alternative_license_decrypted');
    $license_data_array = json_decode( stripslashes( $decrypted_license_data ) );
    $this_domain = Woo_Custom_Installments_Api::get_domain();
    $allowed_products = array( '1', '7', );

    if ( $license_data_array === null ) {
      return;
    }

    if ( $this_domain !== $license_data_array->site_domain ) {
      $this->site_not_allowed = true;

      return;
    }

    if ( ! in_array( $license_data_array->selected_product, $allowed_products ) ) {
      $this->product_not_allowed = true;

      return;
    }

    $license_object = $license_data_array->license_object;

    if ( $this_domain === $license_data_array->site_domain ) {
      $obj = new stdClass();
      $obj->license_key = $license_data_array->license_code;
      $obj->email = $license_data_array->user_email;
      $obj->domain = $this_domain;
      $obj->app_version = WOO_CUSTOM_INSTALLMENTS_VERSION;
      $obj->product_id = $license_data_array->selected_product;
      $obj->product_base = $license_data_array->product_base;
      $obj->is_valid = $license_object->is_valid;
      $obj->license_title = $license_object->license_title;
      $obj->expire_date = $license_object->expire_date;

      update_option( 'woo_custom_installments_alternative_license', 'active' );
      update_option( 'woo_custom_installments_license_response_object', $obj );
      update_option( 'woo_custom_installments_license_key', $obj->license_key );
      delete_option('woo_custom_installments_alternative_license_decrypted');
    }
  }


  /**
   * Check if license if valid
   * 
   * @since 3.8.5
   * @version 4.0.0
   * @return bool
   */
  public static function license_valid() {
    $object_query = get_option('woo_custom_installments_license_response_object');

    // clear api request and response cache if object is empty
    if ( empty( $object_query ) ) {
      delete_transient('woo_custom_installments_api_request_cache');
      delete_transient('woo_custom_installments_api_response_cache');
    }

    if ( ! empty( $object_query ) && isset( $object_query->is_valid )  ) {
      update_option( 'woo_custom_installments_license_status', 'valid' );

      return true;
    } else {
        update_option( 'woo_custom_installments_license_key', '' );
        update_option( 'woo_custom_installments_license_status', 'invalid' );

        return false;
    }
  }


  /**
   * Get license title
   * 
   * @since 4.0.0
   * @return string
   */
  public static function license_title() {
    $object_query = get_option('woo_custom_installments_license_response_object');

    if ( ! empty( $object_query ) && isset( $object_query->license_title ) ) {
      return $object_query->license_title;
    } else {
      return esc_html__(  'Não disponível', 'woo-custom-installments' );
    }
  }


  /**
   * Get license expire date
   * 
   * @since 4.0.0
   * @return string
   */
  public static function license_expire() {
    $object_query = get_option('woo_custom_installments_license_response_object');

    if ( ! empty( $object_query ) && isset( $object_query->expire_date ) ) {
      if ( $object_query->expire_date === 'No expiry' ) {
        return esc_html__( 'Nunca expira', 'woo-custom-installments' );
      } else {
        if ( strtotime( $object_query->expire_date ) < time() ) {
          update_option( 'woo_custom_installments_license_status', 'invalid' );
          delete_option('woo_custom_installments_license_response_object');

          return esc_html__( 'Licença expirada', 'woo-custom-installments' );
        }

        // get wordpress date format setting
        $date_format = get_option('date_format');

        return date( $date_format, strtotime( $object_query->expire_date ) );
      }
    }
  }
}

new Woo_Custom_Installments_Init();