<?php

namespace MeuMouse\Woo_Custom_Installments;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Init class plugin
 * 
 * @since 1.0.0
 * @version 5.2.0
 * @package MeuMouse.com
 */
class Init {

  /**
   * Consctruct function
   * 
   * @since 1.0.0
   * @version 4.5.0
   * @return void
   */
  public function __construct() {
    add_action( 'admin_init', array( $this, 'set_default_options' ) );
  }


  /**
   * Gets the items from the array and inserts them into the option if it is empty,
   * or adds new items with default value to the option
   * 
   * @since 2.0.0
   * @version 4.5.0
   * @return void
   */
  public function set_default_options() {
    $get_options = $this->set_default_data_options();
    $default_options = get_option('woo-custom-installments-setting', array());

    if ( empty( $default_options ) ) {
        $options = $get_options;
        update_option('woo-custom-installments-setting', $options);
        delete_transient('woo_custom_installments_settings_cache');
    } else {
        foreach ( $get_options as $key => $value ) {
            if ( ! isset( $default_options[$key] ) ) {
                $default_options[$key] = $value;
            }
        }

        update_option('woo-custom-installments-setting', $default_options);
        delete_transient('woo_custom_installments_settings_cache');
    }
  }


  /**
   * Set default options
   * 
   * @since 2.0.0
   * @version 5.2.0
   * @return array
   */
  public function set_default_data_options() {
    $options = array(
      'enable_installments_all_products' => 'yes',
      'remove_price_range' => 'no',
      'custom_text_after_price' => 'no',
      'set_fee_per_installment' => 'no',
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
      'product_price_order' => '1',
      'best_installments_order' => '2',
      'discount_pix_order' => '3',
      'economy_pix_order' => '4',
      'slip_bank_order' => '5',
      'text_discount_per_quantity_message' => 'Compre %d UN e ganhe %s de desconto',
      'enable_post_meta_feed_xml_price' => 'no',
      'economy_pix_bg' => '#22c55e',
      'font_size_economy_pix' => '1',
      'font_size_economy_pix_unit' => 'rem',
      'margin_top_economy_pix' => '1',
      'margin_top_economy_pix_unit' => 'rem',
      'margin_bottom_economy_pix' => '1',
      'margin_bottom_economy_pix_unit' => 'rem',
      'border_radius_economy_pix' => '0.3',
      'border_radius_economy_pix_unit' => 'rem',
      'economy_pix_icon_class' => 'fa-solid fa-circle-info',
      'set_custom_hook_payment_form' => '',
      'price_range_method' => 'dynamic',
      'enable_elementor_widgets' => 'yes',
      'enable_price_grid_in_widgets' => 'yes',
      'discount_per_qtd_message_method' => 'hook',
      'discount_value_custom_product_price' => '10',
    );

    return apply_filters( 'woo_custom_installments_set_default_options', $options );
  }


  /**
   * Checks if the option exists and returns the indicated array item
   * 
   * @since 2.0.0
   * @version 4.5.0
   * @param string $key | Array key
   * @return mixed | string or false
   */
  public static function get_setting( $key ) {
    $options = get_option('woo-custom-installments-setting', array());

    // check if array key exists and return key
    if ( isset( $options[$key] ) ) {
        return $options[$key];
    }

    return false;
  }
}

new Init();