<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\Admin\Default_Options;
use MeuMouse\Woo_Custom_Installments\API\License;
use MeuMouse\Woo_Custom_Installments\Core\Logger;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for handle AJAX callbacks
 * 
 * @since 4.5.0
 * @version 5.5.4
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
     * @version 5.5.0
	 * @return void
	 */
	public function __construct() {
        // set logger source
        Logger::set_logger_source( 'woo-custom-installments-license', false );

        // save admin options
		add_action( 'wp_ajax_wci_save_options', array( $this, 'save_options_callback' ) );

        // activate license process
        add_action( 'wp_ajax_wci_alternative_activation_license', array( $this, 'alternative_activation_callback' ) );

        // alternative license process
        add_action( 'wp_ajax_wci_active_license', array( $this, 'active_license_callback' ) );

        // deactive license process
        add_action( 'wp_ajax_wci_deactive_license_action', array( $this, 'deactive_license_callback' ) );

        // reset plugin to default
        add_action( 'wp_ajax_reset_plugin_action', array( $this, 'reset_plugin_callback' ) );

        // sync license action
        add_action( 'wp_ajax_wci_sync_license_action', array( $this, 'sync_license_callback' ) );
	}


    /**
     * Save options in AJAX
     * 
     * @since 3.0.0
     * @version 5.4.9
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
     * @version 5.4.3
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

        // Decrypt the file content
        $decrypted_data = License::decrypt_alternative_license( $file_content, $decrypt_keys );

        if ( $decrypted_data === null ) {
            wp_send_json([
                'status' => 'error',
                'toast_header' => __( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                'toast_body' => __( 'Não foi possível descriptografar o arquivo de licença.', 'woo-custom-installments' ),
            ]);
        }

        $license_data_array = json_decode( stripslashes( $decrypted_data ) );
        $this_domain = License::get_domain();

        if ( ! $license_data_array ) {
            wp_send_json([
                'status' => 'error',
                'toast_header' => __( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                'toast_body' => __( 'O arquivo de licença não contém dados válidos.', 'woo-custom-installments' ),
            ]);
        }

        if ( $this_domain !== $license_data_array->site_domain ) {
            wp_send_json([
                'status' => 'error',
                'toast_header' => __( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                'toast_body' => __( 'O domínio de ativação não é permitido.', 'woo-custom-installments' ),
            ]);
        }

        if ( ! in_array( $license_data_array->selected_product, array( '1', '7' ), true ) ) {
            wp_send_json([
                'status' => 'error',
                'toast_header' => __( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                'toast_body' => __( 'A licença informada não é permitida para este produto', 'woo-custom-installments' ),
            ]);
        }

        delete_transient( 'woo_custom_installments_api_request_cache' );
        delete_transient( 'woo_custom_installments_api_response_cache' );
        delete_transient( 'woo_custom_installments_license_status_cached' );

        $license_object = $license_data_array->license_object;

        // build object
        $obj = (object) array(
            'license_key' => $license_data_array->license_code,
            'email' => $license_data_array->user_email,
            'domain' => $this_domain,
            'app_version' => WOO_CUSTOM_INSTALLMENTS_VERSION,
            'product_id' => $license_data_array->selected_product,
            'product_base' => $license_data_array->product_base,
            'is_valid' => $license_object->is_valid,
            'license_title'=> $license_object->license_title,
            'expire_date' => $license_object->expire_date,
        );

        update_option( 'woo_custom_installments_alternative_license', 'active' );
        update_option( 'woo_custom_installments_license_response_object', $obj );
        update_option( 'woo_custom_installments_license_key', $obj->license_key );
        update_option( 'woo_custom_installments_license_status', 'valid' );

        // send response
        wp_send_json([
            'status' => 'success',
            'toast_header' => __( 'Licença ativa', 'woo-custom-installments' ),
            'toast_body' => __( 'A licença foi ativada com sucesso!', 'woo-custom-installments' ),
            'dropfile_message' => __( 'Licença processada com sucesso!', 'woo-custom-installments' ),
        ]);
    }


    /**
     * Deactive license on AJAX callback
     * 
     * @since 4.5.0
     * @version 5.5.4
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
                delete_option('woo_custom_installments_alternative_license');
                delete_option('woo_custom_installments_temp_license_key');
                delete_option('woo_custom_installments_license_info');
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
     * Reset plugin options to default on AJAX callback
     * 
     * @since 4.5.0
     * @version 5.5.4
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
                delete_option('woo_custom_installments_license_info');

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
     * Sync license on AJAX callback
     * 
     * @since 5.5.4
     * @return void
     */
    public function sync_license_callback() {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'wci_sync_license_action' ) {
            $api_url = 'https://api.meumouse.com/wp-json/license/license/view';
            
            // send request
            $response = wp_remote_post( $api_url, array(
                'body' => array(
                    'api_key' => '315D36C6-0C80F95B-3CAC4C7C-6BE7D8E0',
                    'license_code' => get_option('woo_custom_installments_license_key'),
                ),
                'timeout' => 30,
            ));

            if ( is_wp_error( $response ) ) {
                Logger::register_log( '[WOO CUSTOM INSTALLMENTS] Error on sync licence: ' . print_r( $response, true ), 'ERROR' );
            }

            $response_body = wp_remote_retrieve_body( $response );
            $response_code = wp_remote_retrieve_response_code( $response );
            $details = json_decode( $response_body );

            if ( $response_code === 200 ) {
                if ( $details ) {
                    update_option( 'woo_custom_installments_license_info', $details );

                    $data = $details->data;

                    $obj = new \stdClass();
                    $obj->is_valid = ( $data->status === 'A' );
                    $obj->expire_date  = isset( $data->expiry_time ) ? $data->expiry_time : '';
                    $obj->license_title= isset( $data->license_title ) ? $data->license_title : '';
                    $obj->license_key = isset( $data->purchase_key ) ? $data->purchase_key : '';

                    update_option( 'woo_custom_installments_license_response_object', $obj );
                    update_option( 'woo_custom_installments_license_status', $obj->is_valid ? 'valid' : 'invalid' );

                    if ( ! empty( $obj->expire_date ) && $obj->expire_date !== 'No expiry' ) {
                        License::schedule_license_expiration_check( strtotime( $obj->expire_date ) );
                    }
                }

                $date_format = get_option('date_format');
                $status_html = '<span class="badge bg-translucent-danger rounded-pill">' . esc_html__( 'Inválida', 'woo-custom-installments' ) . '</span>';
                $features_html = '<span class="badge bg-translucent-warning rounded-pill">' . esc_html__( 'Básicos', 'woo-custom-installments' ) . '</span>';
                $type_text = '';
                $expire_text = '';

                if ( $obj->is_valid ) {
                    $status_html = '<span class="badge bg-translucent-success rounded-pill">' . esc_html__( 'Válida', 'woo-custom-installments' ) . '</span>';
                    $features_html = '<span class="badge bg-translucent-primary rounded-pill">' . esc_html__( 'Pro', 'woo-custom-installments' ) . '</span>';

                    $expire_format = ( $obj->expire_date === 'No expiry' ) ? esc_html__( 'Nunca expira', 'woo-custom-installments' ) : date( $date_format, strtotime( $obj->expire_date ) );
                    $type_text = ( strpos( $obj->license_key, 'CM-' ) === 0 ) ? sprintf( esc_html__( 'Assinatura: Clube M - %s', 'woo-custom-installments' ), $data->license_title ) : sprintf( esc_html__( 'Tipo da licença: %s', 'woo-custom-installments' ), $data->license_title );
                    $expire_text = sprintf( esc_html__( 'Licença expira em: %s', 'woo-custom-installments' ), $expire_format );
                }

                $response = array(
                    'status' => 'success',
                    'toast_header_title' => esc_html__( 'Informações atualizadas', 'woo-custom-installments' ),
                    'toast_body_title' => esc_html__( 'A licença foi sincronizada com sucesso!', 'woo-custom-installments' ),
                    'license' => array(
                        'status_html' => $status_html,
                        'features_html' => $features_html,
                        'type_html' => $type_text,
                        'expire_html' => $expire_text,
                    ),
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'toast_header_title' => esc_html__( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ),
                    'toast_body_title' => esc_html__( 'Não foi possível sincronizar as informações da licença.', 'woo-custom-installments' ),
                );
            }

            wp_send_json( $response );
        }
    }
}