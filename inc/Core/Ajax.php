<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\Admin\Default_Options;
use MeuMouse\Woo_Custom_Installments\API\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for handle AJAX callbacks
 * 
 * @since 4.5.0
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Ajax {

    /**
     * Save the license response object
     * 
     * @since 5.4.0
     * @return object
     */
    public $response_obj;

    /**
     * Save the license message
     * 
     * @since 5.4.0
     * @return string
     */
    public $license_message;

    /**
     * Plugin file constant
     * 
     * @since 5.4.0
     * @return string
     */
    public $plugin_file = WOO_CUSTOM_INSTALLMENTS_FILE;

	/**
	 * Construct function
	 * 
	 * @since 4.5.0
     * @version 5.4.0
	 * @return void
	 */
	public function __construct() {
        // save admin options
		add_action( 'wp_ajax_wci_save_options', array( $this, 'save_options_callback' ) );

        // activate license process
        add_action( 'wp_ajax_wci_alternative_activation_license', array( $this, 'alternative_activation_callback' ) );

        // alternative license process
        add_action( 'wp_ajax_wci_active_license', array( $this, 'active_license_callback' ) );

        // deactive license process
        add_action( 'wp_ajax_wci_deactive_license_action', array( $this, 'deactive_license_callback' ) );

        // clear activation cache
        add_action( 'wp_ajax_clear_activation_cache_action', array( $this, 'clear_activation_cache_callback' ) );

        // reset plugin to default
        add_action( 'wp_ajax_reset_plugin_action', array( $this, 'reset_plugin_callback' ) );
	}


    /**
     * Save options in AJAX
     * 
     * @since 3.0.0
     * @version 5.4.3
     * @return void
     */
    public function save_options_callback() {
        // check security nonce
        check_ajax_referer( 'wci_save_options_nonce', 'security' );
        
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'wci_save_options' ) {
            // convert serialized form data on a array
            parse_str( $_POST['form_data'], $form_data );

            // get current options
            $options = get_option( 'woo-custom-installments-setting', array() );

            $switchs_without_license = array(
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
                'center_group_elements_loop',
                'enable_elementor_widgets',
                'enable_price_grid_in_widgets',
                'add_discount_custom_product_price',
                'enable_force_styles',
            );

            $switchs_with_license = array(
                'remove_price_range',
                'set_fee_per_installment',
                'display_discount_price_schema',
                'enable_functions_discount_per_quantity',
                'enable_economy_pix_badge',
                'enable_post_meta_feed_xml_price',
                'enable_sale_badge',
                'update_price_with_quantity',
            );
    
            // update switch options without license
            foreach ( $switchs_without_license as $field ) {
                $options[$field] = isset( $form_data[$field] ) ? 'yes' : 'no';
            }
    
            // update switch options with license
            foreach ( $switchs_with_license as $field ) {
                $options[$field] = ( isset( $form_data[$field] ) && License::is_valid() ) ? 'yes' : 'no';
            }

            $fields_with_license = array(
                'text_display_installments_payment_forms',
                'text_display_installments_loop',
                'text_display_installments_single_product',
            );

            // get default options
            $default_options = Default_Options::set_default_data_options();

            // update switch options with license
            foreach ( $fields_with_license as $field ) {
                $options[$field] = ( isset( $form_data[$field] ) && License::is_valid() ) ? $form_data[$field] : $default_options[$field];
            }
    
            // Update discount payments methods settings
            if ( isset( $form_data['woo_custom_installments_discounts'] ) && License::is_valid() ) {
                update_option( 'woo_custom_installments_discounts_setting', maybe_serialize( $form_data['woo_custom_installments_discounts'] ) );
            }
    
            // Update interests settings
            if ( isset( $form_data['woo_custom_installments_interests'] ) && License::is_valid() ) {
                update_option( 'woo_custom_installments_interests_setting', maybe_serialize( $form_data['woo_custom_installments_interests'] ) );
            }
    
            // Update fee per installments
            if ( isset( $form_data['custom_fee_installments'] ) && is_array( $form_data['custom_fee_installments'] ) && License::is_valid() ) {
                update_option( 'woo_custom_installments_custom_fee_installments', maybe_serialize( $form_data['custom_fee_installments'] ) );
            }

            // merge current data with form data
            $updated_options = Helpers::merge_options( $options, $form_data );

            // Save the updated options
            $saved_options = update_option( 'woo-custom-installments-setting', $updated_options );

            if ( $saved_options ) {
                $response = array(
                    'status' => 'success',
                    'toast_header_title' => esc_html__( 'Salvo com sucesso', 'woo-custom-installments' ),
                    'toast_body_title' => esc_html__( 'As configurações foram atualizadas!', 'woo-custom-installments' ),
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'toast_header_title' => esc_html__( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                    'toast_body_title' => esc_html__( 'Não foi possível salvar as configurações', 'woo-custom-installments' ),
                );
            }

            // debug mode
            if ( WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ) {
                $response['debug'] = array(
                    'options' => get_option('woo-custom-installments-setting'),
                );
            }

            // send JSON response to frontend
            wp_send_json( $response );
        }
    }


    /**
     * Active license process on AJAX callback
     * 
     * @since 5.4.0
     * @return void
     */
    public function active_license_callback() {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'wci_active_license' ) {
            $this->response_obj = new \stdClass();
            $message = '';
            $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        
            // clear response cache first
            delete_transient('woo_custom_installments_api_request_cache');
            delete_transient('woo_custom_installments_api_response_cache');
            delete_transient('woo_custom_installments_license_status_cached');

            update_option( 'woo_custom_installments_license_key', $license_key ) || add_option('woo_custom_installments_license_key', $license_key );
            update_option( 'woo_custom_installments_temp_license_key', $license_key ) || add_option('woo_custom_installments_temp_license_key', $license_key );
    
            // Check on the server if the license is valid and update responses and options
            if ( License::check_license( $license_key, $this->license_message, $this->response_obj, $this->plugin_file ) ) {
                if ( $this->response_obj && $this->response_obj->is_valid ) {
                    update_option( 'woo_custom_installments_license_status', 'valid' );
                    delete_option('woo_custom_installments_temp_license_key');
                    delete_option('woo_custom_installments_license_expired');
                    delete_option('woo_custom_installments_alternative_license_activation');
                } else {
                    update_option( 'woo_custom_installments_license_status', 'invalid' );
                }
        
                if ( License::is_valid() ) {
                    $response = array(
                        'status' => 'success',
                        'toast_header_title' => __( 'Licença ativada com sucesso.', 'woo-custom-installments' ),
                        'toast_body_title' => __( 'Agora todos os recursos estão ativos!', 'woo-custom-installments' ),
                    );
                }
            } else {
                if ( ! empty( $license_key ) && ! empty( $this->license_message ) ) {
                    $response = array(
                        'status' => 'error',
                        'toast_header_title' => __( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                        'toast_body_title' => $this->license_message,
                    );
                }
            }

            // send response for frontend
            wp_send_json( $response );
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
            $deactivation = License::deactive_license( $this->plugin_file, $message );

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
}