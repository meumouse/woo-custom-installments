<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Disable PHP errors and warnings if any, and WP_DEBUG_DISPLAY is true in wp-config.php
 */
error_reporting( E_ERROR | E_WARNING | E_PARSE );
    
/**
 * Class handler API calls
 * 
 * @since 2.0.0
 */
if ( ! class_exists( 'Woo_Custom_Installments_Api' ) ) {
	class Woo_Custom_Installments_Api extends Woo_Custom_Installments_Init {
    	public $key = "2951578DE46F56D7";
    	private $product_id = "1";
    	private $product_base = "woo-custom-installments";
        private $server_host = 'https://meumouse.com/wp-json/license/';
    	private $isEncryptUpdate = true;
    	private $pluginFile;
        private static $selfobj = null;
        private $version = "";
        private $isTheme = false;
        private $emailAddress = "";
        private static $_onDeleteLicense = array();

		public function __construct( $plugin_base_file = '') {
            parent::__construct();

			$this->pluginFile = $plugin_base_file;
            $dir = dirname( $plugin_base_file );
            $dir = str_replace('\\','/', $dir );

            if ( strpos( $dir,'wp-content/themes') !== FALSE) {
                $this->isTheme = true;
            }
		}

		public function setEmailAddress( $emailAddress ) {
            $this->emailAddress = $emailAddress;
        }

		function initActionHandler(){
			$handler = hash("crc32b", $this->product_id . $this->key . $this->getDomain() ) . "_handle";
			if ( isset( $_GET['action'] ) && $_GET['action'] == $handler){
				$this->handleServerRequest();
				exit;
			}
		}

		function handleServerRequest(){
			$type = isset( $_GET['type'] ) ? strtolower( $_GET['type'] ) : "";

			switch( $type ) {
				case "rl": //remove license
					$this->removeOldWPResponse();
					$obj = new stdClass();
					$obj->product = $this->product_id;
					$obj->status = true;
					echo $this->encryptObj( $obj );
					
					return;
				case "rc": //remove license
					$key = $this->getKeyName();
					delete_option( $key );
					$obj = new stdClass();
					$obj->product = $this->product_id;
					$obj->status  = true;
					echo $this->encryptObj( $obj );

					return;
				case "dl": //delete plugins
					$obj = new stdClass();
					$obj->product = $this->product_id;
					$obj->status = false;
					$this->removeOldWPResponse();

					require_once( ABSPATH . 'wp-admin/includes/file.php' );

					if ( $this->isTheme ) {
						$res = delete_theme( $this->pluginFile );

						if ( ! is_wp_error( $res ) ) {
							$obj->status = true;
						}

						echo $this->encryptObj( $obj );
					} else {
					    deactivate_plugins( [ plugin_basename( $this->pluginFile ) ] );
						$res = delete_plugins( [ plugin_basename( $this->pluginFile ) ] );

						if ( ! is_wp_error( $res ) ) {
							$obj->status = true;
						}

						echo $this->encryptObj( $obj );
					}
					
					return;
				default:
					return;
			}
		}


		/**
         * @param callable $func
         */
        static function addOnDelete( $func){
            self::$_onDeleteLicense[] = $func;
        }


		/**
		 * @param $plugin_base_file
		 *
		 * @return self|null
		 */
		static function &getInstance( $plugin_base_file = null ) {
			if ( empty( self::$selfobj ) ) {
				if ( !empty( $plugin_base_file ) ) {
					self::$selfobj = new self( $plugin_base_file );
				}
			}

			return self::$selfobj;
		}

        static function getRenewLink( $responseObj, $type = "s" ) {
			if ( empty( $responseObj->renew_link ) ) {
                return "";
            }

            $isShowButton = false;

            if ( $type == "s" ) {
                $support_str = strtolower( trim( $responseObj->support_end ) );

                if ( strtolower( trim( $responseObj->support_end ) ) == "no support" ) {
                    $isShowButton = true;
                } elseif ( !in_array( $support_str, ["unlimited"] ) ) {
                    if ( strtotime( 'ADD 30 DAYS', strtotime( $responseObj->support_end ) ) < time() ) {
                        $isShowButton = true;
                    }
                }
                
                if ( $isShowButton ) {
                    return $responseObj->renew_link . ( strpos( $responseObj->renew_link, "?" ) === FALSE ? '?type=s&lic=' . rawurlencode( $responseObj->license_key ) : '&type=s&lic='. rawurlencode( $responseObj->license_key ) );
                }

                return '';
            } else {
                $isShowButton = false;
                $expire_str = strtolower( trim( $responseObj->expire_date ) );

                if ( !in_array( $expire_str, ["unlimited", "no expiry"] ) ) {
                    if ( strtotime( 'ADD 30 DAYS', strtotime( $responseObj->expire_date ) ) < time() ) {
                        $isShowButton = true;
                    }
                }

                if ( $isShowButton ) {
                    return $responseObj->renew_link . ( strpos( $responseObj->renew_link, "?" ) === FALSE ? '?type=l&lic=' . rawurlencode( $responseObj->license_key ) : '&type=l&lic=' . rawurlencode( $responseObj->license_key ) );
                }

                return '';
            }
		}

		private function encrypt( $plainText, $password = '') {
			if ( empty( $password ) ) {
				$password = $this->key;
			}

			$plainText = rand( 10, 99 ) . $plainText . rand( 10, 99 );
			$method = 'aes-256-cbc';
			$key = substr( hash( 'sha256', $password, true ), 0, 32 );
			$iv = substr( strtoupper( md5( $password ) ), 0, 16 );

			return base64_encode( openssl_encrypt( $plainText, $method, $key, OPENSSL_RAW_DATA, $iv ) );
		}

		private function decrypt( $encrypted, $password = '' ) {
			if ( empty( $password ) ) {
				$password = $this->key;
			}

			$method = 'aes-256-cbc';
			$key = substr( hash( 'sha256', $password, true ), 0, 32 );
			$iv = substr( strtoupper( md5( $password ) ), 0, 16 );
			$plaintext = openssl_decrypt( base64_decode( $encrypted ), $method, $key, OPENSSL_RAW_DATA, $iv );

			return substr( $plaintext, 2, -2 );
		}

		function encryptObj( $obj ) {
			$text = serialize( $obj );

			return $this->encrypt( $text );
		}

		private function decryptObj( $ciphertext ) {
			$text = $this->decrypt( $ciphertext );

			return unserialize( $text );
		}


        /**
         * Get domain of activation
         * 
         * @since 1.0.0
         * @return string
         */
		private function getDomain() {
		    if ( function_exists( "site_url" ) ) {
                return site_url();
            }

			if ( defined( "WPINC" ) && function_exists( "get_bloginfo" ) ) {
				return get_bloginfo( 'url' );
			} else {
				$base_url = ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == "on" ) ? "https" : "http" );
				$base_url .= "://" . $_SERVER['HTTP_HOST'];
				$base_url .= str_replace( basename( $_SERVER['SCRIPT_NAME'] ), "", $_SERVER['SCRIPT_NAME'] );

				return $base_url;
			}
		}

		private function getEmail() {
            return $this->emailAddress;
        }

		private function processs_response( $response ) {
			$resbk = "";

			if ( !empty( $response ) ) {
				if ( !empty( $this->key ) ) {
					$resbk = $response;
					$response = $this->decrypt( $response );
				}

				$response = json_decode( $response );

				if ( is_object( $response ) ) {
					return $response;
				} else {
					$response = new stdClass();
					$response->status = false;
					$response->msg = __( 'Erro de resposta, entre em contato com o suporte ou atualize o plugin.', 'woo-custom-installments' );
					
                    if ( !empty( $bkjson ) ) {
                        $bkjson = @json_decode( $resbk );

                        if ( !empty( $bkjson->msg ) ) {
                            $response->msg = $bkjson->msg;
                        }
					}

					$response->data = NULL;

					return $response;
				}
			}

			$response = new stdClass();
			$response->msg = __( 'Resposta desconhecida', 'woo-custom-installments' );
			$response->status = false;
			$response->data = NULL;

			return $response;
		}


        /**
         * Request on API server
         * 
         * @since 1.0.0
         * @access private
         * @return string | $response
         */
		private function _request( $relative_url, $data, &$error = '' ) {
            $response = new stdClass();
            $response->status = false;
            $response->msg = __( 'Resposta vazia.', 'woo-custom-installments' );
            $response->is_request_error = false;
            $finalData = json_encode( $data );

            // set transient name with get send data
            $transient_name = 'woo-custom-installments-' . md5( $relative_url . $finalData );
            // Try get a transient
            $cached_response = get_transient( $transient_name );

            if ( $cached_response !== false ) {
                // if exists transient, return cached data
                return $this->processs_response( $cached_response );
            }

            if ( !empty( $this->key ) ) {
                $finalData = $this->encrypt( $finalData );
            }

            $cache_key = 'woo_custom_installments_url_server_cache';

            // try get data in the cache
            $cached_data = wp_cache_get( $cache_key );

            // if data is empty in the cache, storage data
            if ( false === $cached_data ) {
                $url = rtrim( $this->server_host, '/' ) . "/" . ltrim( $relative_url, '/' );
                // set time cache for 30 days
                $cache_time = 7 * DAY_IN_SECONDS;
                // set data in the cache
                wp_cache_set( $cache_key, $url, '', $cache_time );
            }

            if ( get_option( 'woo_custom_installments_license_status' ) != 'valid' ) {
                $url = rtrim( $this->server_host, '/' ) . "/" . ltrim( $relative_url, '/' );
            }

            if ( function_exists('wp_remote_post') ) {
                $request_params = [
                    'method' => 'POST',
                    'sslverify' => true,
                    'timeout' => 60,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => [],
                    'body' => $finalData,
                    'cookies' => []
                ];
                
                $serverResponse = wp_remote_post( $url, $request_params );
                $http_code = wp_remote_retrieve_response_code( $serverResponse );

                if ( is_wp_error( $serverResponse ) ) {
                    $request_params['sslverify'] = false;
                    $serverResponse = wp_remote_post( $url, $request_params );

                     if ( is_wp_error( $serverResponse ) ) {
                        $response->msg = $serverResponse->get_error_message();
                        $response->status = false;
                        $response->data = NULL;
                        $response->is_request_error = true;

                        return $response;
                    } else {
                        // if data response success, storage transient for 1 day
                        if ( ! empty( $serverResponse['body'] ) && ( is_array( $serverResponse ) && 200 === (int) wp_remote_retrieve_response_code( $serverResponse ) ) && $serverResponse['body'] != "GET404" ) {
                            $cached_response = $serverResponse['body'];
                            set_transient( $transient_name, $cached_response, 7 * DAY_IN_SECONDS );

                            return $this->processs_response( $cached_response );
                        }
                    }
                } else {
                    if ( !empty( $serverResponse['body']) && ( is_array( $serverResponse ) && 200 === (int) wp_remote_retrieve_response_code( $serverResponse )) && $serverResponse['body'] != "GET404" ) {
                        return $this->processs_response( $serverResponse['body'] );
                    }
                }

            }

            if ( !extension_loaded('curl') ) {
                $response->msg = __( 'A extensão cURL está faltando.', 'woo-custom-installments' );
                $response->status = false;
                $response->data = NULL;
                $response->is_request_error = true;

                return $response;
            }
            
            //curl when fall back
            $curlParams = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $finalData,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: text/plain",
                    "cache-control: no-cache"
                )
            );
            
            $curl = curl_init();
            curl_setopt_array( $curl, $curlParams);
            $serverResponse = curl_exec( $curl );
            $curlErrorNo = curl_errno($curl);
            $error = curl_error( $curl );
            curl_close( $curl );

            if ( !$curlErrorNo ) {
                if ( ! empty( $serverResponse ) ) {
                    return $this->processs_response( $serverResponse );
                }
            } else {
                $curl = curl_init();
                $curlParams[CURLOPT_SSL_VERIFYPEER] = false;
                $curlParams[CURLOPT_SSL_VERIFYHOST] = false;
                curl_setopt_array( $curl, $curlParams);
                $serverResponse = curl_exec( $curl );
                $curlErrorNo=curl_errno($curl);
                $error = curl_error( $curl );
                curl_close( $curl );

                if ( !$curlErrorNo ) {
                    if ( ! empty( $serverResponse ) ) {
                        return $this->processs_response( $serverResponse );
                    }
                } else {
                    $response->msg = $error;
                    $response->status = false;
                    $response->data = NULL;
                    $response->is_request_error = true;

                    return $response;
                }
            }

            $response->msg = __( 'Resposta desconhecida', 'woo-custom-installments' );
            $response->status = false;
            $response->data = NULL;
            $response->is_request_error = true;

            return $response;
        }

		private function getParam( $purchase_key, $app_version, $admin_email = '' ) {
			$req = new stdClass();
			$req->license_key = $purchase_key;
			$req->email = ! empty( $admin_email ) ? $admin_email : $this->getEmail();
			$req->domain = $this->getDomain();
			$req->app_version = $app_version;
			$req->product_id = $this->product_id;
			$req->product_base = $this->product_base;

			return $req;
		}

		private function getKeyName() {
            return hash( 'crc32b', $this->getDomain() . $this->pluginFile . $this->product_id . $this->product_base . $this->key . "LIC" );
        }

        private function SaveWPResponse( $response ) {
            $key = $this->getKeyName();
            $data = $this->encrypt( serialize( $response ), $this->getDomain() );
            update_option( $key, $data ) || add_option( $key, $data );
        }

        private function getOldWPResponse() {
            $key = $this->getKeyName();
            $response = get_option( $key, NULL );

            if ( empty( $response ) ) {
                return NULL;
            }

            return unserialize( $this->decrypt( $response, $this->getDomain() ) );
        }

        private function removeOldWPResponse() {
            $key = $this->getKeyName();
            $isDeleted = delete_option( $key );

            foreach ( self::$_onDeleteLicense as $func ) {
                if ( is_callable( $func ) ) {
                    call_user_func( $func );
                }
            }

            return $isDeleted;
        }

		public static function RemoveLicenseKey( $plugin_base_file, &$message = "" ) {
			$obj = self::getInstance( $plugin_base_file );
			return $obj->_removeWPPluginLicense( $message );
		}

		public static function CheckWPPlugin( $purchase_key, &$error = "", &$responseObj = null, $plugin_base_file = "" ) {
			$obj = self::getInstance( $plugin_base_file );

			return $obj->_CheckWPPlugin( $purchase_key, $error, $responseObj );
		}

		final function _removeWPPluginLicense( &$message = '' ) {
			$oldRespons = $this->getOldWPResponse();

			if ( !empty( $oldRespons->is_valid ) ) {
				if ( ! empty( $oldRespons->license_key ) ) {
					$param = $this->getParam( $oldRespons->license_key, $this->version );
					$response = $this->_request( 'product/deactive/' . $this->product_id, $param, $message );

					if ( empty( $response->code ) ) {
						if ( ! empty( $response->status ) ) {
							$message = $response->msg;
							$this->removeOldWPResponse();

							return true;
						} else {
							$message = $response->msg;
						}
					} else {
						$message=$response->message;
					}
				}
			} else {
                $this->removeOldWPResponse();

				return true;
			}

			return false;
		}

		public static function GetRegisterInfo() {
			if ( !empty( self::$selfobj ) ) {
				return self::$selfobj->getOldWPResponse();
			}

			return null;
		}

		final function _CheckWPPlugin( $purchase_key, &$error = "", &$responseObj = null ) {
            if ( empty( $purchase_key ) ) {
                $this->removeOldWPResponse();
                $error = "";

                return false;
            }

            $oldRespons = $this->getOldWPResponse();
            $isForce = false;

            if ( !empty( $oldRespons ) ) {
                if ( ! empty( $oldRespons->expire_date ) && strtolower( $oldRespons->expire_date ) != "no expiry" && strtotime( $oldRespons->expire_date ) < time() ) {
                    $isForce = true;
                }

                if ( ! $isForce && ! empty( $oldRespons->is_valid ) && $oldRespons->next_request > time() && ( ! empty( $oldRespons->license_key ) && $purchase_key == $oldRespons->license_key ) ) {
                    $responseObj = clone $oldRespons;
                    unset( $responseObj->next_request );

                    return true;
                }
            }

            $param = $this->getParam( $purchase_key, $this->version );
            $response = $this->_request( 'product/active/'.$this->product_id, $param, $error );

            if ( empty( $response->is_request_error ) ) {
                if ( empty( $response->code ) ) {
                    if ( ! empty( $response->status ) ) {
                        if ( ! empty( $response->data ) ) {
                            $serialObj = $this->decrypt( $response->data, $param->domain );
                            $licenseObj = unserialize( $serialObj );

                            if ( $licenseObj->is_valid ) {
                                $responseObj = new stdClass();
                                $responseObj->is_valid = $licenseObj->is_valid;
                                
                                if ( $licenseObj->request_duration > 0 ) {
                                    $responseObj->next_request = strtotime( "+ {$licenseObj->request_duration} hour" );
                                } else {
                                    $responseObj->next_request = time();
                                }

                                $responseObj->expire_date = $licenseObj->expire_date;
                                $responseObj->support_end = $licenseObj->support_end;
                                $responseObj->license_title = $licenseObj->license_title;
                                $responseObj->license_key = $purchase_key;
                                $responseObj->msg = $response->msg;
                                $responseObj->renew_link = !empty($licenseObj->renew_link) ? $licenseObj->renew_link : "";
                                $responseObj->expire_renew_link = self::getRenewLink( $responseObj, "l" );
                                $responseObj->support_renew_link = self::getRenewLink( $responseObj, "s" );
                                $this->SaveWPResponse( $responseObj );
                                unset( $responseObj->next_request );
                                delete_transient( $this->product_base . "_up" );

                                return true;
                            } else {
                                if ( $this->__checkoldtied( $oldRespons, $responseObj, $response ) ) {
                                    return true;
                                } else {
                                    $this->removeOldWPResponse();
                                    $error = ! empty( $response->msg ) ? $response->msg : "";
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
                if ( $this->__checkoldtied( $oldRespons, $responseObj, $response ) ) {
                    return true;
                } else {
                    $this->removeOldWPResponse();
                    $error = ! empty( $response->msg ) ? $response->msg : "";
                }
            }

            return $this->__checkoldtied( $oldRespons, $responseObj );
        }

        private function __checkoldtied( &$oldRespons, &$responseObj ){
            if ( !empty( $oldRespons ) && ( empty( $oldRespons->tried ) || $oldRespons->tried <= 2) ) {
                $oldRespons->next_request = strtotime("+ 1 hour");
                $oldRespons->tried=empty($oldRespons->tried)?1:($oldRespons->tried+1);
                $responseObj = clone $oldRespons;
                unset( $responseObj->next_request );

                if ( isset($responseObj->tried) ) {
                    unset( $responseObj->tried );
                }

                $this->SaveWPResponse( $oldRespons );

                return true;
            }

            return false;
        }
	}
}