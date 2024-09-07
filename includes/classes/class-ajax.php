<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\License;
use MeuMouse\Woo_Custom_Installments\Frontend;
use MeuMouse\Woo_Custom_Installments\CalCulate_Values;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for handle AJAX callbacks
 * 
 * @since 4.5.0
 * @version 5.0.0
 * @package MeuMouse.com
 */
class Ajax {

	/**
	 * Construct function
	 * 
	 * @since 4.5.0
     * @version 5.0.0
	 * @return void
	 */
	public function __construct() {
        // save admin options
		add_action( 'wp_ajax_wci_save_options', array( $this, 'ajax_save_options_callback' ) );

        // alternative license process
        add_action( 'wp_ajax_wci_alternative_activation_license', array( $this, 'alternative_activation_callback' ) );

        // deactive license process
        add_action( 'wp_ajax_wci_deactive_license_action', array( $this, 'deactive_license_callback' ) );

        // clear activation cache
        add_action( 'wp_ajax_clear_activation_cache_action', array( $this, 'clear_activation_cache_callback' ) );

        // reset plugin to default
        add_action( 'wp_ajax_reset_plugin_action', array( $this, 'reset_plugin_callback' ) );

        add_action( 'wp_ajax_get_updated_variation_prices_action', array( $this, 'get_update_variation_prices_callback' ) );
        add_action( 'wp_ajax_nopriv_get_updated_variation_prices_action', array( $this, 'get_update_variation_prices_callback' ) );

        if ( Init::get_setting('remove_price_range') === 'yes' && Init::get_setting('price_range_method') === 'ajax' && License::is_valid() ) {
            add_action( 'wp_ajax_get_updated_price_html', array( $this, 'get_updated_price_html_callback' ) );
            add_action( 'wp_ajax_nopriv_get_updated_price_html', array( $this, 'get_updated_price_html_callback' ) );
        }
	}


    /**
     * Save options in AJAX
     * 
     * @since 3.0.0
     * @version 5.0.0
     * @return void
     */
    public function ajax_save_options_callback() {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'wci_save_options' ) {
            // Convert serialized data into an array
            parse_str( $_POST['form_data'], $form_data );

            $fields_without_license = array(
                'enable_installments_all_products',
                'custom_text_after_price',
                'enable_all_discount_options',
                'display_installments_cart',
                'include_shipping_value_in_discounts',
                'display_tag_discount_price_checkout',
                'enable_discount_per_unit_discount_per_quantity',
                'message_discount_per_quantity',
                'enable_all_interest_options',
                'display_tag_interest_checkout',
                'enable_pix_method_payment_form',
                'enable_instant_approval_badge',
                'enable_ticket_method_payment_form',
                'enable_ticket_discount_main_price',
                'enable_credit_card_method_payment_form',
                'enable_debit_card_method_payment_form',
                'enable_mastercard_flag_credit',
                'enable_visa_flag_credit',
                'enable_elo_flag_credit',
                'enable_hipercard_flag_credit',
                'enable_diners_club_flag_credit',
                'enable_discover_flag_credit',
                'enable_american_express_flag_credit',
                'enable_paypal_flag_credit',
                'enable_stripe_flag_credit',
                'enable_mercado_pago_flag_credit',
                'enable_pagseguro_flag_credit',
                'enable_pagarme_flag_credit',
                'enable_cielo_flag_credit',
                'enable_mastercard_flag_debit',
                'enable_visa_flag_debit',
                'enable_elo_flag_debit',
                'enable_hipercard_flag_debit',
                'enable_diners_club_flag_debit',
                'enable_discover_flag_debit',
                'enable_american_express_flag_debit',
                'enable_paypal_flag_debit',
                'enable_stripe_flag_debit',
                'enable_mercado_pago_flag_debit',
                'enable_pagseguro_flag_debit',
                'enable_pagarme_flag_debit',
                'enable_cielo_flag_debit',
                'center_group_elements_loop'
            );

            $fields_with_license = array(
                'remove_price_range',
                'set_fee_per_installment',
                'display_discount_price_schema',
                'enable_functions_discount_per_quantity',
                'enable_economy_pix_badge',
                'enable_post_meta_feed_xml_price',
            );

            foreach ( $fields_without_license as $field ) {
                $options[$field] = isset( $form_data[$field] ) ? 'yes' : 'no';
            }

            foreach ( $fields_with_license as $field ) {
                $options[$field] = ( isset( $form_data[$field] ) && License::is_valid() ) ? 'yes' : 'no';
            }

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
     * @version 5.0.0
     * @return void
     */
    public function deactive_license_callback() {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'wci_deactive_license_action' ) {
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


    /**
     * Get HTML price on update variation in AJAX callback
     * 
     * @since 4.5.1
     * @return void
     */
    public function get_updated_price_html_callback() {
        if ( isset( $_POST['product_id'] ) ) {
            $product_id = intval( $_POST['product_id'] );
            $product = wc_get_product( $product_id );
    
            if ( $product ) {
                $price_html = apply_filters( 'woocommerce_get_price_html', $product->get_price_html(), $product );
    
                // get price HTML
                wp_send_json_success( array(
                    'price_html' => $price_html,
                ));
            }
        }
    
        wp_send_json_error( array(
            'message' => 'Invalid product',
        ));
    }
}

new Ajax();