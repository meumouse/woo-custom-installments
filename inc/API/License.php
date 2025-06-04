<?php

namespace MeuMouse\Woo_Custom_Installments\API;

use MeuMouse\Woo_Custom_Installments\Core\Logger;

// Exit if accessed directly.
defined('ABSPATH') || exit;
    
/**
 * Connect to license authentication server
 * 
 * @since 2.0.0
 * @version 5.4.0
 * @package MeuMouse.com
 */
class License {

    private $product_id;
    private $product_base;
    private $product_key;

    private $wci_product_id = '1';
    private $wci_product_base = 'woo-custom-installments';
    public $wci_product_key = '2951578DE46F56D7';

    private $clube_m_produt_id = '7';
    private $clube_m_product_base = 'clube-m';
    private $clube_m_product_key = 'B729F2659393EE27';

    public static $server_host = 'https://api.meumouse.com/wp-json/license/';
    private $plugin_file;
    private $version = WOO_CUSTOM_INSTALLMENTS_VERSION;
    private $is_theme = false;
    private $email_address = WOO_CUSTOM_INSTALLMENTS_ADMIN_EMAIL;
    private static $_onDeleteLicense = array();
    private static $self_obj;

    /**
     * Construct function
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @param string $plugin_base_file
     * @return void
     */
    public function __construct( $plugin_base_file = '' ) {
        $license_key = get_option('woo_custom_installments_license_key');

        // check if license is for Clube M, else license is product base
        if ( strpos( $license_key, 'CM-' ) === 0 ) {
            $this->product_base = $this->clube_m_product_base;
            $this->product_id = $this->clube_m_produt_id;
            $this->product_key = $this->clube_m_product_key;
        } else {
            $this->product_base = $this->wci_product_base;
            $this->product_id = $this->wci_product_id;
            $this->product_key = $this->wci_product_key;
        }

        $this->plugin_file = $plugin_base_file;
        $dir = dirname( $plugin_base_file );
        $dir = str_replace('\\','/', $dir );

        if ( strpos( $dir,'wp-content/themes' ) !== FALSE ) {
            $this->is_theme = true;
        }

        // deactive license on expire time
        add_action( 'Woo_Custom_Installments/License/Check_Expires_Time', array( __CLASS__, 'check_license_expires_time' ) );

        // register schedule event first time
        if ( ! get_option('woo_custom_installments_schedule_expiration_check_runned') ) {
            add_action( 'admin_init', array( __CLASS__, 'schedule_license_expiration_check' ) );
        }

        // set logger source
        Logger::set_logger_source( 'woo-custom-installments-license', false );

        if ( get_option('woo_custom_installments_license_expired') ) {
            add_action( 'admin_notices', array( __CLASS__, 'license_expired_notice' ) );
        }

        // display require license modal
        add_action( 'Woo_Custom_Installments/Admin/Header', array( $this, 'display_modal_license_require' ) );
    }


    /**
     * Get plugin instance
     * 
     * @since 2.0.0
     * @param self $plugin_base_file | Plugin file
     * @return self|null
     */
    static function &get_instance( $plugin_base_file = null ) {
        if ( empty( self::$self_obj ) ) {
            if ( ! empty( $plugin_base_file ) ) {
                self::$self_obj = new self( $plugin_base_file );
            }
        }

        return self::$self_obj;
    }


    /**
     * Get renew license link
     * 
     * @since 2.0.0
     * @param object $response_object | Response object
     * @param string $type | Renew type
     * @return string
     */
    private static function get_renew_link( $response_object, $type = 's' ) {
        if ( empty( $response_object->renew_link ) ) {
            return '';
        }

        $show_button = false;

        if ( $type == 's' ) {
            $support_str = strtolower( trim( $response_object->support_end ) );

            if ( strtolower( trim( $response_object->support_end ) ) == 'no support' ) {
                $show_button = true;
            } elseif ( ! in_array( $support_str, ["unlimited"] ) ) {
                if ( strtotime( 'ADD 30 DAYS', strtotime( $response_object->support_end ) ) < time() ) {
                    $show_button = true;
                }
            }
            
            if ( $show_button ) {
                return $response_object->renew_link . ( strpos( $response_object->renew_link, '?' ) === FALSE ? '?type=s&lic=' . rawurlencode( $response_object->license_key ) : '&type=s&lic='. rawurlencode( $response_object->license_key ) );
            }

            return '';
        } else {
            $show_button = false;
            $expire_str = strtolower( trim( $response_object->expire_date ) );

            if ( ! in_array( $expire_str, array( 'unlimited', 'no expiry' ) ) ) {
                if ( strtotime( 'ADD 30 DAYS', strtotime( $response_object->expire_date ) ) < time() ) {
                    $show_button = true;
                }
            }

            if ( $show_button ) {
                return $response_object->renew_link . ( strpos( $response_object->renew_link, '?' ) === FALSE ? '?type=l&lic=' . rawurlencode( $response_object->license_key ) : '&type=l&lic=' . rawurlencode( $response_object->license_key ) );
            }

            return '';
        }
    }


    /**
     * Encrypt response
     * 
     * @since 2.0.0
     * @param string $plaintext | Object response to encrypt
     * @param string $password | Product key
     * @return string
     */
    private function encrypt( $plaintext, $password = '' ) {
        if ( empty( $password ) ) {
            $password = $this->product_key;
        }

        $plaintext = wp_rand( 10, 99 ) . $plaintext . wp_rand( 10, 99 );
        $method = 'aes-256-cbc';
        $key = substr( hash( 'sha256', $password, true ), 0, 32 );
        $iv = substr( strtoupper( md5( $password ) ), 0, 16 );

        return base64_encode( openssl_encrypt( $plaintext, $method, $key, OPENSSL_RAW_DATA, $iv ) );
    }
    

    /**
     * Decrypt response
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @param string $encrypted | Encrypted response
     * @param string $password | Product key
     * @return string
     */
    private function decrypt( $encrypted, $password = '' ) {
        if ( empty( $password ) ) {
            $password = $this->product_key;
        }

        if ( WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ) {
            Logger::register_log( 'License API response encrypted: ' . print_r( $encrypted, true ) );
        }

        if ( is_string( $encrypted ) ) {
            $method = 'aes-256-cbc';
            $key = substr( hash( 'sha256', $password, true ), 0, 32 );
            $iv = substr( strtoupper( md5( $password ) ), 0, 16 );
    
            $plaintext = openssl_decrypt( base64_decode( $encrypted ), $method, $key, OPENSSL_RAW_DATA, $iv );
    
            if ( $plaintext === false ) {
                if ( WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ) {
                    Logger::register_log( 'License API - fail on decrypt: ' . print_r( $plaintext, true ), 'ERROR' );
                }

                return '';
            }
    
            return substr( $plaintext, 2, -2 );
        } else {
            if ( WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ) {
                Logger::register_log( 'License API - Entry for decrypt is not string : ' . print_r( $encrypted, true ), 'ERROR' );
            }
           
            return '';
        }
    }


    /**
     * Get site domain
     * 
     * @since 2.0.0
     * @return string
     */
    public static function get_domain() {
        if ( function_exists('site_url') ) {
            return site_url();
        }

        if ( defined('WPINC') && function_exists('get_bloginfo') ) {
            return get_bloginfo('url');
        } else {
            $base_url = ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == "on" ) ? "https" : "http" );
            $base_url .= "://" . $_SERVER['HTTP_HOST'];
            $base_url .= str_replace( basename( $_SERVER['SCRIPT_NAME'] ), "", $_SERVER['SCRIPT_NAME'] );

            return $base_url;
        }
    }


    /**
     * Processes the API response
     *
     * @since 2.0.0
     * @version 5.4.0
     * @param string $response | Raw API response
     * @return stdClass|mixed Object decoded from the JSON response or error object, if applicable.
     */
    private function process_response( $response ) {
        if ( get_option('woo_custom_installments_alternative_license') === 'active' ) {
            return;
        }

        if ( ! empty( $response ) ) {
            $resbk = $response;
            $decrypted_response = $response;

            if ( WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ) {
                Logger::register_log( 'License API - Process response : ' . print_r( $response, true ) );
            }

            if ( ! empty( $this->product_key ) ) {
                // Try to decrypt
                $decrypted_response = $this->decrypt( $response );

                if ( WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ) {
                    Logger::register_log( 'License API - Decrypted response : ' . print_r( $decrypted_response, true ) );
                }

                if ( empty( $decrypted_response ) ) {
                    update_option( 'woo_custom_installments_alternative_license_activation', 'yes' );

                    // Handle decryption failure
                    $decryption_error = new \stdClass();
                    $decryption_error->status = false;
                    $decryption_error->msg = __( 'Ocorreu um erro na conexão com o servidor de verificação de licenças. Verifique o erro nos logs do WooCommerce.', 'woo-custom-installments' );
                    $decryption_error->data = NULL;

                    return $decryption_error;
                }
            }

            // Ensure decrypted_response is a string before decoding the JSON
            if ( is_object( $decrypted_response ) ) {
                $decrypted_response = json_encode( $decrypted_response );
            }

            // Try decoding the JSON
            $decoded_response = json_decode( $decrypted_response );

            if ( WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ) {
                Logger::register_log( 'License API - Response decoded : ' . print_r( $decoded_response, true ) );
            }

            if ( json_last_error() !== JSON_ERROR_NONE ) {
                // Handle JSON decoding error
                $json_error = new \stdClass();
                $json_error->status = false;
                $json_error->msg = sprintf( __( 'Erro JSON: %s', 'woo-custom-installments' ), json_last_error_msg() );
                $json_error->data = $resbk;

                return $json_error;
            }

            return $decoded_response;
        }

        // Treat unknown response
        $unknown_response = new \stdClass();
        $unknown_response->msg = __( 'Resposta desconhecida', 'woo-custom-installments' );
        $unknown_response->status = false;
        $unknown_response->data = NULL;

        return $unknown_response;
    }


    /**
     * Request on API server
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @param string $relative_url | API URL to concat
     * @param object $data | Object data to encode and add to body request
     * @param string $error | Error message
     * @return string
     */
    private function _request( $relative_url, $data, &$error = '' ) {
        $transient_name = 'woo_custom_installments_api_request_cache';
        $cached_response = get_transient( $transient_name );

        if ( false === $cached_response ) {
            $response = new \stdClass();
            $response->status = false;
            $response->msg = __( 'Resposta vazia.', 'woo-custom-installments' );
            $response->is_request_error = false;
            $final_data = wp_json_encode( $data );
            $url = rtrim( self::$server_host, '/' ) . "/" . ltrim( $relative_url, '/' );
    
            if ( ! empty( $this->product_key ) ) {
                $final_data = $this->encrypt( $final_data );
            }
    
            if ( function_exists('wp_remote_post') ) {
                $request_params = array(
                    'method' => 'POST',
                    'sslverify' => true,
                    'timeout' => 60,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(),
                    'body' => $final_data,
                    'cookies' => array(),
                );
    
                $server_response = wp_remote_post( $url, $request_params );

                if ( WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ) {
                    Logger::register_log( 'License API - Request response : ' . print_r( $server_response, true ) );
                }

                if ( is_wp_error( $server_response ) ) {
                    $request_params['sslverify'] = false;
                    $server_response = wp_remote_post( $url, $request_params );
    
                    if ( is_wp_error( $server_response ) ) {
                        $curl_error_message = $server_response->get_error_message();
    
                        // Check if it is a cURL 35 error
                        if ( strpos( $curl_error_message, 'cURL error 35' ) !== false ) {
                            $error = __( 'Erro cURL 35: Problema de comunicação SSL/TLS.', 'woo-custom-installments' );
                        } else {
                            $response->msg = $curl_error_message;
                            $response->status = false;
                            $response->data = NULL;
                            $response->is_request_error = true;
                        }
                    } else {
                        // If data response is successful, cache for 7 days
                        if ( ! empty( $server_response['body'] ) && ( is_array( $server_response ) && 200 === (int) wp_remote_retrieve_response_code( $server_response ) ) && $server_response['body'] != "GET404" ) {
                            $cached_response = $server_response['body'];
                            set_transient( $transient_name, $cached_response, 7 * DAY_IN_SECONDS );
                        }
                    }
                } else {
                    if ( ! empty( $server_response['body'] ) && ( is_array( $server_response ) && 200 === (int) wp_remote_retrieve_response_code( $server_response ) ) && $server_response['body'] != "GET404" ) {
                        $cached_response = $server_response['body'];
                    }
                }
            } elseif ( ! extension_loaded( 'curl' ) ) {
                $response->msg = __( 'A extensão cURL está faltando.', 'woo-custom-installments' );
                $response->status = false;
                $response->data = NULL;
                $response->is_request_error = true;
            } else {
                // Curl when in last resort
                $curlParams = array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 120,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $final_data,
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: text/plain",
                        "cache-control: no-cache"
                    )
                );
    
                $curl = curl_init();
                curl_setopt_array( $curl, $curlParams );
                $server_response = curl_exec( $curl );
                $curlErrorNo = curl_errno( $curl );
                $error = curl_error( $curl );
                curl_close( $curl );
    
                if ( ! curl_exec( $curl ) ) {
                    $error_message = curl_error( $curl );
    
                    // Check if it is a cURL 35 error
                    if ( strpos( $error_message, 'cURL error 35' ) !== false ) {
                        $error = __( 'Erro cURL 35: Problema de comunicação SSL/TLS.', 'woo-custom-installments' );
                    } else {
                        $response->msg = sprintf( __( 'Erro cURL: %s', 'woo-custom-installments' ), $error_message );
                    }
                }
    
                if ( ! $curlErrorNo ) {
                    if ( ! empty( $server_response ) ) {
                        $cached_response = $server_response;
                    }
                } else {
                    $curl = curl_init();
                    $curlParams[CURLOPT_SSL_VERIFYPEER] = false;
                    $curlParams[CURLOPT_SSL_VERIFYHOST] = false;
                    curl_setopt_array( $curl, $curlParams );
                    $server_response = curl_exec( $curl );
                    $curlErrorNo = curl_errno( $curl );
                    $error = curl_error( $curl );
                    curl_close( $curl );
    
                    if ( ! $curlErrorNo ) {
                        if ( ! empty( $server_response ) ) {
                            $cached_response = $server_response;
                        }
                    } else {
                        $response->msg = $error;
                        $response->status = false;
                        $response->data = NULL;
                        $response->is_request_error = true;
                    }
                }
            }
    
            // If there is a response, set it in cache
            if ( ! empty( $cached_response ) ) {
                set_transient( $transient_name, $cached_response, 7 * DAY_IN_SECONDS );
            }
    
            return $this->process_response( $cached_response ? $cached_response : $response ); // Fixed from process_response to processes_response
        }
    
        return $this->process_response( $cached_response );
    }

    
    /**
     * Build object to send response API
     * 
     * @since 2.0.0
     * @param string $purchase_key | License key
     * @return object
     */
    private function get_response_param( $purchase_key ) {
        $req = new \stdClass();
        $req->license_key = $purchase_key;
        $req->email = $this->email_address;
        $req->domain = self::get_domain();
        $req->app_version = $this->version;
        $req->product_id = $this->product_id;
        $req->product_base = $this->product_base;

        return $req;
    }


    /**
     * Generate hash key
     * 
     * @since 2.0.0
     * @return string
     */
    private function get_key_name() {
        return hash( 'crc32b', self::get_domain() . $this->plugin_file . $this->product_id . $this->product_base . $this->product_key . "LIC" );
    }


    /**
     * Set response base option
     * 
     * @since 2.0.0
     * @param object $response | Response object
     * @return void
     */
    private function set_response_base( $response ) {
        $key = $this->get_key_name();
        $data = $this->encrypt( maybe_serialize( $response ), self::get_domain() );
        update_option( $key, $data ) || add_option( $key, $data );
    }


    /**
     * Get response base option
     * 
     * @since 2.0.0
     * @return string
     */
    public function get_response_base() {
        $key = $this->get_key_name();
        $response = get_option( $key, NULL );

        if ( empty( $response ) ) {
            return NULL;
        }

        return maybe_unserialize( $this->decrypt( $response, self::get_domain() ) );
    }


    /**
     * Remove response base option
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @return string
     */
    public function remove_response_base() {
        $key = $this->get_key_name();
        $is_deleted = delete_option( $key );

        update_option( 'woo_custom_installments_license_status', 'invalid' );
        delete_option('woo_custom_installments_license_key');
        delete_option('woo_custom_installments_license_response_object');
        delete_option('woo_custom_installments_alternative_license');
        delete_option('woo_custom_installments_temp_license_key');
        delete_option('woo_custom_installments_alternative_license_activation');
        delete_transient('woo_custom_installments_api_request_cache');
        delete_transient('woo_custom_installments_api_response_cache');
        delete_transient('woo_custom_installments_license_status_cached');

        foreach ( self::$_onDeleteLicense as $func ) {
            if ( is_callable( $func ) ) {
                call_user_func( $func );
            }
        }

        return $is_deleted;
    }


    /**
     * Deactive license action
     * 
     * @since 2.0.0
     * @param string $plugin_base_file | Plugin base file
     * @param string $message | Error message
     * @return object
     */
    public static function deactive_license( $plugin_base_file, &$message = "" ) {
        $obj = self::get_instance( $plugin_base_file );

        return $obj->deactive_license_process( $message );
    }


    /**
     * Check purchase key
     * 
     * @since 2.0.0
     * @param string $purchase_key | License key
     * @param string $error | Error message
     * @param object $response | Response object
     * @param string $plugin_base_file | Plugin base file
     * @return object
     */
    public static function check_license( $purchase_key, &$error = '', &$response = null, $plugin_base_file = '' ) {
        $obj = self::get_instance( $plugin_base_file );

        return $obj->check_license_object( $purchase_key, $error, $response );
    }


    /**
     * Deactive license process
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @param string $message | Error message
     * @return bool
     */
    final function deactive_license_process( &$message = '' ) {
        $old_response = $this->get_response_base();

        if ( ! empty( $old_response->is_valid ) ) {
            if ( ! empty( $old_response->license_key ) ) {
                $param = $this->get_response_param( $old_response->license_key );
                $response = $this->_request( 'product/deactive/' . $this->product_id, $param, $message );
                update_option('woo_custom_installments_license_response_object', $response);

                if ( WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ) {
                    Logger::register_log( 'License API - Deactive response object : ' . print_r( $response, true ) );
                }

                if ( empty( $response->code ) ) {
                    update_option( 'woo_custom_installments_license_status', 'invalid' );
                    delete_option('woo_custom_installments_license_key');
                    delete_option('woo_custom_installments_license_response_object');
                    delete_option('woo_custom_installments_alternative_license');
                    delete_option('woo_custom_installments_temp_license_key');
                    delete_option('woo_custom_installments_license_expired');
                    delete_option('woo_custom_installments_alternative_license_activation');
                    delete_transient('woo_custom_installments_api_request_cache');
                    delete_transient('woo_custom_installments_api_response_cache');
                    delete_transient('woo_custom_installments_license_status_cached');

                    if ( ! empty( $response->status ) ) {
                        $message = $response->msg;
                        $this->remove_response_base();

                        return true;
                    } else {
                        $message = $response->msg;

                        return true;
                    }
                } else {
                    $message = $response->message;
                }
            }
        } else {
            $this->remove_response_base();

            return true;
        }

        return false;
    }


    /**
     * Check if license is active and valid
     * 
     * @since 2.0.0
     * @param string $purchase_key | License key
     * @param string $error | Error message
     * @param object $response_object | Response object
     * @return mixed object or bool
     */
    final function check_license_object( $purchase_key, &$error = '', &$response_object = null ) {
        if ( get_option('woo_custom_installments_alternative_license') === 'active' ) {
            return;
        }

        if ( empty( $purchase_key ) ) {
            $this->remove_response_base();
            $error = "";
    
            return false;
        }
    
        $transient_name = 'woo_custom_installments_api_response_cache';
        $cached_response = get_transient( $transient_name );
    
        if ( false !== $cached_response ) {
            $response_object = maybe_unserialize( $cached_response );
            unset( $response_object->next_request );
    
            return true;
        }
    
        $old_response = $this->get_response_base();
        $isForce = false;
    
        if ( ! empty( $old_response ) ) {
            if ( ! empty( $old_response->expire_date ) && strtolower( $old_response->expire_date ) != "no expiry" && strtotime( $old_response->expire_date ) < time() ) {
                $isForce = true;
            }
    
            if ( ! $isForce && ! empty( $old_response->is_valid ) && $old_response->next_request > time() && ( ! empty( $old_response->license_key ) && $purchase_key == $old_response->license_key ) ) {
                $response_object = clone $old_response;
                unset( $response_object->next_request );
    
                return true;
            }
        }
    
        $param = $this->get_response_param( $purchase_key );
        $response = $this->_request( 'product/active/' . $this->product_id, $param, $error );

        if ( empty( $response->is_request_error ) ) {
            if ( empty( $response->code ) ) {
                if ( ! empty( $response->status ) ) {
                    if ( ! empty( $response->data ) ) {
                        $serialObj = $this->decrypt( $response->data, $param->domain );
                        $licenseObj = maybe_unserialize( $serialObj );
                        update_option( 'woo_custom_installments_license_response_object', $licenseObj );

                        // schedule event for check expiration license time
                        self::schedule_license_expiration_check();
    
                        if ( $licenseObj->is_valid ) {
                            $response_object = new \stdClass();
                            $response_object->is_valid = $licenseObj->is_valid;
    
                            if ( $licenseObj->request_duration > 0 ) {
                                $response_object->next_request = strtotime( "+ {$licenseObj->request_duration} hour" );
                            } else {
                                $response_object->next_request = time();
                            }
    
                            $response_object->expire_date = $licenseObj->expire_date;
                            $response_object->support_end = $licenseObj->support_end;
                            $response_object->license_title = $licenseObj->license_title;
                            $response_object->license_key = $purchase_key;
                            $response_object->msg = $response->msg;
                            $response_object->renew_link = ! empty( $licenseObj->renew_link ) ? $licenseObj->renew_link : '';
                            $response_object->expire_renew_link = self::get_renew_link( $response_object, "l" );
                            $response_object->support_renew_link = self::get_renew_link( $response_object, "s" );
                            $this->set_response_base( $response_object );
    
                            // Cache the response for 1 day
                            set_transient( $transient_name, maybe_serialize( $response_object ), DAY_IN_SECONDS );
    
                            unset( $response_object->next_request );
                            delete_transient( $this->product_base . "_up" );
    
                            return true;
                        } else {
                            if ( $this->check_old_response( $old_response, $response_object, $response ) ) {
                                return true;
                            } else {
                                $this->remove_response_base();
                                $error = ! empty( $response->msg ) ? $response->msg : '';
                            }
                        }
                    } else {
                        $error = __( 'Dados inválidos.', 'woo-custom-installments' );
                    }
                } else {
                    $error = $response->msg;
                }
            } else {
                $error = $response->message;
            }
        } else {
            if ( $this->check_old_response( $old_response, $response_object, $response ) ) {
                return true;
            } else {
                $this->remove_response_base();
                $error = ! empty( $response->msg ) ? $response->msg : '';
            }
        }
    
        return $this->check_old_response( $old_response, $response_object );
    }


    /**
     * Check if old response is active
     * 
     * @since 2.0.0
     * @param object $old_response | 
     * @param object $response_object | 
     * @return bool
     */
    private function check_old_response( &$old_response, &$response_object ) {
        if ( ! empty( $old_response ) && ( empty( $old_response->tried ) || $old_response->tried <= 2 ) ) {
            $old_response->next_request = strtotime('+ 1 hour');
            $old_response->tried = empty( $old_response->tried ) ? 1 : ( $old_response->tried + 1 );
            $response_object = clone $old_response;
            unset( $response_object->next_request );

            if ( isset( $response_object->tried ) ) {
                unset( $response_object->tried );
            }

            $this->set_response_base( $old_response );

            return true;
        }

        return false;
    }


    /**
     * Get license expires time
     * 
     * @since 5.4.0
     * @param string $license_key | License key
     * @return array
     */
    public static function get_expires_time( $license_key ) {
        $api_url = self::$server_host . 'license/view';

        $response = wp_remote_post( $api_url, array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body' => array(
                'api_key' => '41391199-FE02BDAA-3E8E3920-CDACDE2F',
                'license_code' => $license_key
            ),
            'timeout' => 30,
        ));

        if ( is_wp_error( $response ) ) {
            Logger::register_log( 'Error getting license expiration time: ' . $response->get_error_message(), 'ERROR' );

            return false;
        }

        $response_body = wp_remote_retrieve_body( $response );
        $decoded_response = json_decode( $response_body, true );

        // check if response is valid
        if ( ! is_array( $decoded_response ) || empty( $decoded_response['data']['expiry_time'] ) ) {
            Logger::register_log( 'Invalid response from license API: ' . print_r( $decoded_response, true ), 'ERROR' );
            return false;
        }

        return $decoded_response['data']['expiry_time'];
    }


    /**
     * Check if license is valid
     * 
     * @since 2.0.0
     * @return bool
     */
    public static function is_valid() {
        $cached_result = get_transient('woo_custom_installments_license_status_cached');

        // If the result is cached, return it
        if ( $cached_result !== false ) {
            return $cached_result;
        }

        $object_query = get_option('woo_custom_installments_license_response_object');

        if ( is_object( $object_query ) && ! empty( $object_query ) && isset( $object_query->is_valid )  ) {
            // set response cache for 24h
            set_transient('woo_custom_installments_license_status_cached', true, 86400);

            return true;
        } else {
            set_transient( 'woo_custom_installments_license_status_cached', false, 86400 );
            update_option( 'woo_custom_installments_license_status', 'invalid' );

            return false;
        }
    }


    /**
     * Get license title
     * 
     * @since 2.0.0
     * @return string
     */
    public static function license_title() {
        $object_query = get_option('woo_custom_installments_license_response_object');
    
        if ( is_object( $object_query ) && ! empty( $object_query ) && isset( $object_query->license_title ) ) {
          return $object_query->license_title;
        } else {
          return esc_html__( 'Não disponível', 'woo-custom-installments' );
        }
    }


    /**
     * Get license expire date
     * 
     * @since 2.0.0
     * @return string
     */
    public static function license_expire() {
        $object_query = get_option('woo_custom_installments_license_response_object');

        if ( is_object( $object_query ) && ! empty( $object_query ) && isset( $object_query->expire_date ) ) {
            if ( $object_query->expire_date === 'No expiry' ) {
                return esc_html__( 'Nunca expira', 'woo-custom-installments' );
            } else {
                if ( strtotime( $object_query->expire_date ) < time() ) {
                    $object_query->is_valid = false;

                    update_option( 'woo_custom_installments_license_response_object', $object_query );
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


    /**
     * Check if license is expired
     * 
     * @since 2.0.0
     * @return bool
     */
    public static function expired_license() {
        $object_query = get_option('woo_custom_installments_license_response_object');

        if ( is_object( $object_query ) && ! empty( $object_query ) && isset( $object_query->expire_date ) ) {
            if ( $object_query->expire_date === 'No expiry' ) {
                return false;
            } else {
                if ( strtotime( $object_query->expire_date ) < time() ) {
                    $object_query->is_valid = false;

                    update_option( 'woo_custom_installments_license_response_object', $object_query );
                    delete_option('woo_custom_installments_alternative_license');

                    return false;
                }
            }
        }
    }


    /**
     * Try to decrypt license with multiple keys
     * 
     * @since 2.0.0
     * @param string $encrypted_data | Encrypted data
     * @param array $possible_keys | Array list with decryp keys
     * @return mixed Decrypted string or null
     */
    public static function decrypt_alternative_license( $encrypted_data, $possible_keys ) {
        foreach ( $possible_keys as $key ) {
            $decrypted_data = openssl_decrypt( $encrypted_data, 'AES-256-CBC', $key, 0, substr( $key, 0, 16 ) );

            // Checks whether decryption was successful
            if ( $decrypted_data !== false ) {
                return $decrypted_data;
            }
        }
        
        return null;
    }


    /**
     * Check expiration license on schedule event
     * 
     * @since 5.4.0
     * @return void
     */
    public static function schedule_license_expiration_check( $expiration_timestamp = 0 ) {
        // Cancel any previous bookings to avoid duplication
        wp_clear_scheduled_hook('Woo_Custom_Installments/License/Check_Expires_Time');

        if ( $expiration_timestamp > 0 ) {
            if ( $expiration_timestamp > time() ) {
                // Add 24h to timestamp
                $expiration_timestamp += DAY_IN_SECONDS;

                // Schedule event to expire at exactly the right time
                wp_schedule_single_event( $expiration_timestamp, 'Woo_Custom_Installments/License/Check_Expires_Time' );
            }
        } else {
            $object_query = get_option('woo_custom_installments_license_response_object');
    
            if ( is_object( $object_query ) && ! empty( $object_query->expire_date ) ) {
                $expiration_timestamp = strtotime( $object_query->expire_date );
        
                if ( $expiration_timestamp > time() ) {
                    // Add 24h to timestamp
                    $expiration_timestamp += DAY_IN_SECONDS;
    
                    // Schedule event to expire at exactly the right time
                    wp_schedule_single_event( $expiration_timestamp, 'Woo_Custom_Installments/License/Check_Expires_Time' );
                }
            }
        }

        // register runned event
        update_option( 'woo_custom_installments_schedule_expiration_check_runned', true );
    }


    /**
     * Deactivate license on scheduled event
     * 
     * @since 5.4.0
     * @return void
     */
    public static function check_license_expires_time() {
        $license_key = get_option('woo_custom_installments_license_key');
        $api_expiry_time = self::get_expires_time( $license_key );

        if ( $api_expiry_time ) {
            $expiration_timestamp = strtotime( $api_expiry_time );

            // license expired
            if ( $expiration_timestamp < time() ) {
                update_option( 'woo_custom_installments_license_expired', true );
                $message = '';

                self::deactive_license( WOO_CUSTOM_INSTALLMENTS_FILE, $message );
            } else {
                self::schedule_license_expiration_check( $expiration_timestamp );
            }
        }
    }


    /**
     * Display modal for require license when is not Pro
     * 
     * @since 4.5.0
     * @return void
     */
    public function display_modal_license_require() {
        if ( ! self::is_valid() ) : ?>
            <div id="popup-pro-notice" class="popup-container">
                <div class="popup-content popup-sm">
                    <div class="popup-body">
                        <div class="d-flex flex-column align-items-center p-4">
                            <div class="btn-icon rounded-circle p-2 mb-3 bg-translucent-primary">
                                <svg class="icon-pro icon-primary" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                            </div>
                            <h5 class="text-center mb-2 mt-3"><?php echo esc_html__('Este recurso está disponível na versão Pro', 'woo-custom-installments'); ?></h5>
                            <span class="title-hightlight mt-2 mb-3"></span>
                            <span class="text-muted fs-lg p-3"><?php echo esc_html__( 'Uma licença permite que você desbloqueie todos os recursos Pro que o plugin tem a oferecer.', 'woo-custom-installments' ) ?></span>
                        </div>
                        
                        <div class="my-4 p-3">
                            <button id="active_license_form" class="btn btn-lg btn-outline-secondary me-3"><?php echo esc_html__('Já tenho uma licença', 'woo-custom-installments'); ?></button>
                            <a class="btn btn-lg btn-primary d-inline-flex" href="https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugins-list&utm_campaign=wci" target="_blank">
                                <span><?php echo esc_html__( 'Comprar uma licença', 'woo-custom-installments' ) ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;
    }


    /**
     * Display be Pro license message for Elementor widgets
     * 
     * @since 5.0.0
     * @version 5.4.0
     * @return void
     */
    public static function render_widget_license_message() {
        if ( ! License::is_valid() ) : ?>
            <div class="wci-license-message-box">
                <div class="message-box-header">
                    <svg class="woo-custom-installments-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 732.07 983.38"><path d="M569.06,765.19c115.21,1.52,248.61-42.44,248.61-156.14s-141-162.2-141-162.2V381.66C851,463.52,955.62,601.47,884.37,750S595.83,927.49,595.83,927.49v104H414.44V927.39C258.3,916.78,174.93,801.57,174.93,801.57L287.1,671.2C399.28,751.55,460.92,763.77,569.06,765.19Z" transform="translate(-174.93 -48.16)" style="fill:#040f0f"/><circle cx="299.39" cy="314.55" r="31.83" style="fill:#2ba84a"/><path d="M572.1,546.9l-32.6-77.1c-181.15-27.07-216-163.93-216-163.93C475.08,240.68,593.32,321,593.32,321l72.76-34.87,63.67,25.77,60.64-81.86c-63.67-68.21-194-84.89-194-84.89v-97H412.92V149.73c-203.13,0-283.47,222.84-189.49,341.08S406.86,598.44,546.32,619.66s63.67,94,63.67,94,148.56-7.58,159.17-109.15S660,464,660,464l-23.5,13.94,47,106.12Z" transform="translate(-174.93 -48.16)" style="fill:#2ba84a"/></svg>
                </div>

                <h5><?php echo esc_html__('Este recurso está disponível na versão Pro', 'woo-custom-installments'); ?></h5>
                <span class="message-cta"><?php echo esc_html__( 'Uma licença permite que você desbloqueie todos os recursos Pro que o Parcelas Customizadas tem a oferecer.', 'woo-custom-installments' ) ?></span>
            </div>
        <?php endif;
    }


    /**
	 * Display admin notice when license is expired
	 * 
	 * @since 4.5.1
     * @version 5.4.0
	 * @return void
	 */
    public static function license_expired_notice() {
        if ( self::expired_license() ) {
			$class = 'notice notice-error is-dismissible';
			$message = __( 'Sua licença do <strong>Parcelas Customizadas para WooCommerce</strong> expirou, realize a renovação para continuar aproveitando os recursos Pro.', 'woo-custom-installments' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}
    }
}