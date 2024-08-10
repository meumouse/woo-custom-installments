<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\License;
use MeuMouse\Woo_Custom_Installments\Frontend;
use MeuMouse\Woo_Custom_Installments\CalCulate_Values;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for handle AJAX callbacks
 * 
 * @since 4.5.0
 * @package MeuMouse.com
 */
class Ajax {

	/**
	 * Construct function
	 * 
	 * @since 4.5.0
	 * @return void
	 */
	public function __construct() {
        // save admin options
		add_action( 'wp_ajax_wci_save_options', array( $this, 'ajax_save_options_callback' ) );

        // alternative license process
        add_action( 'wp_ajax_wci_alternative_activation_license', array( $this, 'alternative_activation_callback' ) );

        // deactive license process
        add_action( 'wp_ajax_deactive_license_action', array( $this, 'deactive_license_callback' ) );

        // clear activation cache
        add_action( 'wp_ajax_clear_activation_cache_action', array( $this, 'clear_activation_cache_callback' ) );

        // reset plugin to default
        add_action( 'wp_ajax_reset_plugin_action', array( $this, 'reset_plugin_callback' ) );

        add_action( 'wp_ajax_get_updated_variation_prices_action', array( $this, 'get_update_variation_prices_callback' ) );
        add_action( 'wp_ajax_nopriv_get_updated_variation_prices_action', array( $this, 'get_update_variation_prices_callback' ) );
	}


    /**
     * Save options in AJAX
     * 
     * @since 3.0.0
     * @version 4.5.0
     * @return void
     */
    public function ajax_save_options_callback() {
        if ( isset( $_POST['form_data'] ) ) {
            // Convert serialized data into an array
            parse_str( $_POST['form_data'], $form_data );

            $options = get_option('woo-custom-installments-setting');

            /**
             * Add custom option to AJAX form data
             * 
             * @since 4.5.0
             */
            do_action('woo_custom_installments_ajax_form_data');

            $options['enable_installments_all_products'] = isset( $form_data['enable_installments_all_products'] ) ? 'yes' : 'no';
            $options['remove_price_range'] = isset( $form_data['remove_price_range'] ) && License::is_valid() ? 'yes' : 'no';
            $options['custom_text_after_price'] = isset( $form_data['custom_text_after_price'] ) ? 'yes' : 'no';
            $options['set_fee_per_installment'] = isset( $form_data['set_fee_per_installment'] ) && License::is_valid() ? 'yes' : 'no';
            $options['enable_all_discount_options'] = isset( $form_data['enable_all_discount_options'] ) ? 'yes' : 'no';
            $options['display_installments_cart'] = isset( $form_data['display_installments_cart'] ) ? 'yes' : 'no';
            $options['include_shipping_value_in_discounts'] = isset( $form_data['include_shipping_value_in_discounts'] ) ? 'yes' : 'no';
            $options['display_tag_discount_price_checkout'] = isset( $form_data['display_tag_discount_price_checkout'] ) ? 'yes' : 'no';
            $options['display_discount_price_schema'] = isset( $form_data['display_discount_price_schema'] ) && License::is_valid() ? 'yes' : 'no';
            $options['enable_functions_discount_per_quantity'] = isset( $form_data['enable_functions_discount_per_quantity'] ) && License::is_valid() ? 'yes' : 'no';
            $options['enable_discount_per_unit_discount_per_quantity'] = isset( $form_data['enable_discount_per_unit_discount_per_quantity'] ) ? 'yes' : 'no';
            $options['message_discount_per_quantity'] = isset( $form_data['message_discount_per_quantity'] ) ? 'yes' : 'no';
            $options['enable_all_interest_options'] = isset( $form_data['enable_all_interest_options'] ) ? 'yes' : 'no';
            $options['display_tag_interest_checkout'] = isset( $form_data['display_tag_interest_checkout'] ) ? 'yes' : 'no';
            $options['enable_pix_method_payment_form'] = isset( $form_data['enable_pix_method_payment_form'] ) ? 'yes' : 'no';
            $options['enable_instant_approval_badge'] = isset( $form_data['enable_instant_approval_badge'] ) ? 'yes' : 'no';
            $options['enable_ticket_method_payment_form'] = isset( $form_data['enable_ticket_method_payment_form'] ) ? 'yes' : 'no';
            $options['enable_ticket_discount_main_price'] = isset( $form_data['enable_ticket_discount_main_price'] ) ? 'yes' : 'no';
            $options['enable_credit_card_method_payment_form'] = isset( $form_data['enable_credit_card_method_payment_form'] ) ? 'yes' : 'no';
            $options['enable_debit_card_method_payment_form'] = isset( $form_data['enable_debit_card_method_payment_form'] ) ? 'yes' : 'no';
            $options['enable_mastercard_flag_credit'] = isset( $form_data['enable_mastercard_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_visa_flag_credit'] = isset( $form_data['enable_visa_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_elo_flag_credit'] = isset( $form_data['enable_elo_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_hipercard_flag_credit'] = isset( $form_data['enable_hipercard_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_diners_club_flag_credit'] = isset( $form_data['enable_diners_club_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_discover_flag_credit'] = isset( $form_data['enable_discover_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_american_express_flag_credit'] = isset( $form_data['enable_american_express_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_paypal_flag_credit'] = isset( $form_data['enable_paypal_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_stripe_flag_credit'] = isset( $form_data['enable_stripe_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_mercado_pago_flag_credit'] = isset( $form_data['enable_mercado_pago_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_pagseguro_flag_credit'] = isset( $form_data['enable_pagseguro_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_pagarme_flag_credit'] = isset( $form_data['enable_pagarme_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_cielo_flag_credit'] = isset( $form_data['enable_cielo_flag_credit'] ) ? 'yes' : 'no';
            $options['enable_mastercard_flag_debit'] = isset( $form_data['enable_mastercard_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_visa_flag_debit'] = isset( $form_data['enable_visa_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_elo_flag_debit'] = isset( $form_data['enable_elo_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_hipercard_flag_debit'] = isset( $form_data['enable_hipercard_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_diners_club_flag_debit'] = isset( $form_data['enable_diners_club_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_discover_flag_debit'] = isset( $form_data['enable_discover_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_american_express_flag_debit'] = isset( $form_data['enable_american_express_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_paypal_flag_debit'] = isset( $form_data['enable_paypal_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_stripe_flag_debit'] = isset( $form_data['enable_stripe_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_mercado_pago_flag_debit'] = isset( $form_data['enable_mercado_pago_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_pagseguro_flag_debit'] = isset( $form_data['enable_pagseguro_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_pagarme_flag_debit'] = isset( $form_data['enable_pagarme_flag_debit'] ) ? 'yes' : 'no';
            $options['enable_cielo_flag_debit'] = isset( $form_data['enable_cielo_flag_debit'] ) ? 'yes' : 'no';
            $options['center_group_elements_loop'] = isset( $form_data['center_group_elements_loop'] ) ? 'yes' : 'no';
            $options['enable_economy_pix_badge'] = isset( $form_data['enable_economy_pix_badge'] ) && License::is_valid() ? 'yes' : 'no';
            $options['enable_post_meta_feed_xml_price'] = isset( $form_data['enable_post_meta_feed_xml_price'] ) && License::is_valid() ? 'yes' : 'no';

            if ( isset( $form_data['woo_custom_installments_discounts'] ) && ! empty( $form_data['woo_custom_installments_discounts'] ) && License::is_valid() ) {
                update_option( 'woo_custom_installments_discounts_setting', maybe_serialize( $form_data['woo_custom_installments_discounts'] ) );
            }

            if ( isset( $form_data['woo_custom_installments_interests'] ) && ! empty( $form_data['woo_custom_installments_interests'] ) && License::is_valid() ) {
                update_option( 'woo_custom_installments_interests_setting', maybe_serialize( $form_data['woo_custom_installments_interests'] ) );
            }

            if ( isset( $form_data['custom_fee_installments'] ) && is_array( $form_data['custom_fee_installments'] ) && License::is_valid() ) {
                update_option( 'woo_custom_installments_custom_fee_installments', maybe_serialize( $form_data['custom_fee_installments'] ) );
            }

            // Merge the form data with the default options
            $updated_options = wp_parse_args( $form_data, $options );

            // Save the updated options
            $saved_options = update_option( 'woo-custom-installments-setting', $updated_options );

            if ( $saved_options ) {
                // Clear the transient cache whenever options are updated
                delete_transient('woo_custom_installments_settings_cache');

                $response = array(
                    'status' => 'success',
                    'toast_header_title' => esc_html__( 'Salvo com sucesso', 'woo-custom-installments' ),
                    'toast_body_title' => esc_html__( 'As configurações foram atualizadas!', 'woo-custom-installments' ),
                    'options' => $updated_options,
                    'custom_fee_installments' => isset( $custom_fee_installments ) ? $custom_fee_installments : '',
                );

                wp_send_json( $response ); // Send JSON response
            }
        }
    }


    /**
     * Handle alternative activation license file .key
     * 
     * @since 4.3.0
     * @version 4.5.0
     * @return void
     */
    public function alternative_activation_callback() {
        if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'wci_alternative_activation_license' ) {
            $response = array(
                'status' => 'error',
                'toast_header' => __( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                'toast_body' => __( 'Erro ao carregar o arquivo. A ação não foi acionada corretamente.', 'woo-custom-installments' ),
            );

            wp_send_json( $response );
        }

        // Check if the file was uploaded
        if ( empty( $_FILES['file'] ) ) {
            $response = array(
                'status' => 'error',
                'toast_header' => __( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                'toast_body' => __( 'Erro ao carregar o arquivo. O arquivo não foi enviado.', 'woo-custom-installments' ),
            );

            wp_send_json( $response );
        }

        $file = $_FILES['file'];

        // Check if it is a .key file
        if ( pathinfo( $file['name'], PATHINFO_EXTENSION ) !== 'key' ) {
            $response = array(
                'status' => 'invalid_file',
                'toast_header' => __( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                'toast_body' => __( 'Arquivo inválido. O arquivo deve ser extensão .key', 'woo-custom-installments' ),
            );
            
            wp_send_json( $response );
        }

        $file_content = file_get_contents( $file['tmp_name'] );

        $decrypt_keys = array(
            '2951578DE46F56D7', // original product key
            'B729F2659393EE27', // Clube M
        );

        $decrypted_data = License::decrypt_alternative_license( $file_content, $decrypt_keys );

        if ( $decrypted_data !== null ) {
            update_option( 'woo_custom_installments_alternative_license_decrypted', $decrypted_data );
            
            $response = array(
                'status' => 'success',
                'dropfile_message' => __( 'Arquivo enviado com sucesso.', 'woo-custom-installments' ),
                'toast_header' => __( 'Licença enviada e decriptografada com sucesso.', 'woo-custom-installments' ),
                'toast_body' => __( 'Licença enviada e decriptografada com sucesso.', 'woo-custom-installments' ),
            );
        } else {
            $response = array(
                'status' => 'error',
                'toast_header' => __( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                'toast_body' => __( 'Não foi possível descriptografar o arquivo de licença.', 'woo-custom-installments' ),
            );
        }

        wp_send_json( $response );
    }


    /**
     * Deactive license on AJAX callback
     * 
     * @since 4.5.0
     * @return void
     */
    public function deactive_license_callback() {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'deactive_license_action' ) {
            $message = '';
            $deactivation = License::deactive_license( WOO_CUSTOM_INSTALLMENTS_FILE, $message );

            if ( $deactivation ) {
                update_option( 'woo_custom_installments_license_status', 'invalid' );
                delete_option('woo_custom_installments_license_key');
                delete_option('woo_custom_installments_license_response_object');
                delete_option('woo_custom_installments_alternative_license_decrypted');
                delete_option('woo_custom_installments_alternative_license');
                delete_option('woo_custom_installments_temp_license_key');
                delete_option('woo_custom_installments_alternative_license_activation');
                delete_transient('woo_custom_installments_api_request_cache');
                delete_transient('woo_custom_installments_api_response_cache');
                delete_transient('woo_custom_installments_license_status_cached');

                $response = array(
                    'status' => 'success',
                    'toast_header_title' => esc_html__( 'A licença foi desativada', 'woo-custom-installments' ),
                    'toast_body_title' => esc_html__( 'Todos os recursos da versão Pro agora estão desativados!', 'woo-custom-installments' ),
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'toast_header_title' => esc_html__( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                    'toast_body_title' => esc_html__( 'Ocorreu um erro ao desativar sua licença.', 'woo-custom-installments' ),
                );
            }

            wp_send_json( $response );
        }
    }


    /**
     * Clear activation cache on AJAX callback
     * 
     * @since 4.5.0
     * @return void
     */
    public function clear_activation_cache_callback() {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'clear_activation_cache_action' ) {
            delete_transient('woo_custom_installments_api_request_cache');
            delete_transient('woo_custom_installments_api_response_cache');
            delete_transient('woo_custom_installments_license_status_cached');
            delete_option('woo_custom_installments_alternative_license');
            delete_option('woo_custom_installments_alternative_license_activation');

            $response = array(
                'status' => 'success',
                'toast_header_title' => esc_html__( 'Cache de ativação limpo', 'woo-custom-installments' ),
                'toast_body_title' => esc_html__( 'O cache de ativação foi limpo com sucesso!', 'woo-custom-installments' ),
            );

            wp_send_json( $response );
        }
    }


    /**
     * Reset plugin options to default on AJAX callback
     * 
     * @since 4.5.0
     * @return void
     */
    public function reset_plugin_callback() {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'reset_plugin_action' ) {
            $delete_option = delete_option('woo-custom-installments-setting');

            if ( $delete_option ) {
                delete_option('woo_custom_installments_discounts_setting');
                delete_option('woo_custom_installments_interests_setting');
                delete_option('woo_custom_installments_custom_fee_installments');
                delete_option('woo_custom_installments_alternative_license');
                delete_option('woo_custom_installments_alternative_license_activation');

                $response = array(
                    'status' => 'success',
                    'toast_header_title' => esc_html__( 'As opções foram redefinidas', 'woo-custom-installments' ),
                    'toast_body_title' => esc_html__( 'As opções foram redefinidas com sucesso!', 'woo-custom-installments' ),
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'toast_header_title' => esc_html__( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                    'toast_body_title' => esc_html__( 'Ocorreu um erro ao redefinir as configurações.', 'woo-custom-installments' ),
                );
            }

            wp_send_json( $response );
        }
    }


    /**
     * Get updated prices for selected variation on callback AJAX
     * 
     * @since 4.5.0
     * @return void
     */
    public function get_update_variation_prices_callback() {
        if ( isset( $_POST['variation_id'] ) ) {
            $variation_id = intval( $_POST['variation_id'] );
            $variation = wc_get_product( $variation_id );

            $response = apply_filters( 'woo_custom_installments_update_variation_prices', array(
                'pix_price' => array(
                    'element' => '.pix-method-name',
                    'price'  => wc_price( Calculate_Values::get_discounted_price( $variation, 'main' ) ),
                ),
                'economy_pix' => array(
                    'element' => '.discount-before-economy-pix',
                    'price'  => wc_price( Frontend::calculate_pix_economy( $variation ) ),
                ),
                'ticket_price' => array(
                    'element' => '.ticket-method-name',
                    'price'  => wc_price( Calculate_Values::get_discounted_price( $variation, 'ticket' ) ),
                ),
            ));
    
            if ( $variation ) {
                wp_send_json_success( $response );
            }
        }
    
        wp_send_json_error( 'Invalid product variation ID' );
    }
}

new Ajax();