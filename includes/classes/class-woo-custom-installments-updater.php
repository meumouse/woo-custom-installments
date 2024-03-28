<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class to make requests to a remote server to get plugin versions and updates
 * 
 * @since 3.0.0
 * @version 3.6.0
 * @package MeuMouse.com
 */
if ( ! class_exists( 'Woo_Custom_Installments_Updater' ) ) {
    class Woo_Custom_Installments_Updater {

        public $plugin_slug;
        public $version;
        public $cache_key;
        public $cache_data_base_key;
        public $cache_allowed;
        public $time_cache;
        private static $instance;

        /**
         * Instance class
         * 
         * @since 3.0.0
         * @return self Woo_Custom_Installments_Updater
         */
        public static function run() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            
            return self::$instance;
        }

        public function __construct() {
            if ( defined( 'WOO_CUSTOM_INSTALLMENTS_DEV_MODE' ) ) {
                add_filter( 'https_ssl_verify', '__return_false' );
                add_filter( 'https_local_ssl_verify', '__return_false' );
                add_filter( 'http_request_host_is_external', '__return_true' );
            }

            $this->plugin_slug = WOO_CUSTOM_INSTALLMENTS_SLUG;
            $this->version = WOO_CUSTOM_INSTALLMENTS_VERSION;
            $this->cache_key = 'woo_custom_installments_check_updates';
            $this->cache_data_base_key = 'woo_custom_installments_remote_data';
            $this->cache_allowed = true;
            $this->time_cache = DAY_IN_SECONDS;

            add_filter( 'plugins_api', array( $this, 'plugin_info' ), 20, 3 );
            add_filter( 'site_transient_update_plugins', array( $this, 'update_plugin' ) );
            add_action( 'upgrader_process_complete', array( $this, 'purge_cache' ), 10, 2 );
            add_filter( 'plugin_row_meta', array( $this, 'add_check_updates_link'), 10, 2 );
            add_filter( 'all_admin_notices', array( $this, 'check_manual_update_query_arg' ) );
        }


        /**
         * Request on remote server
         * 
         * @since 3.0.0
         * @return array
         */
        public function request() {
            $cached_data = wp_cache_get( $this->cache_key );
        
            if ( false === $cached_data ) {
                $remote = get_transient( $this->cache_data_base_key );
        
                if ( false === $remote ) {
                    $remote = wp_remote_get('https://raw.githubusercontent.com/meumouse/woo-custom-installments/main/dist/update-checker.json', [
                        'timeout' => 10,
                        'headers' => [
                            'Accept' => 'application/json'
                        ]
                    ]);
        
                    if ( !is_wp_error( $remote ) && 200 === wp_remote_retrieve_response_code( $remote ) ) {
                        $remote_data = json_decode( wp_remote_retrieve_body( $remote ) );
        
                        // set cache remote data for 1 day
                        set_transient( $this->cache_data_base_key, $remote_data, $this->time_cache );
                    } else {
                        return false;
                    }
                } else {
                    $remote_data = $remote;
                }
        
                // set cache remote data for 1 day
                wp_cache_set( $this->cache_key, $remote_data, $this->time_cache );
            } else {
                $remote_data = $cached_data;
            }
        
            return $remote_data;
        }


        /**
         * Get plugin info
         * 
         * @since 3.0.0
         * @param $response
         * @param $action
         * @param $args
         * @return array
         */
        public function plugin_info( $response, $action, $args ) {
            // do nothing if you're not getting plugin information right now
            if ( 'plugin_information' !== $action ) {
                return $response;
            }
    
            // do nothing if it is not our plugin
            if ( empty( $args->slug ) || $this->plugin_slug !== $args->slug ) {
                return $response;
            }
    
            // get updates
            $remote = $this->request();
    
            if ( ! $remote ) {
                return $response;
            }
    
            $response = new \stdClass();
    
            $response->name = $remote->name;
            $response->slug = $remote->slug;
            $response->version = $remote->version;
            $response->tested = $remote->tested;
            $response->requires = $remote->requires;
            $response->author = $remote->author;
            $response->author_profile = $remote->author_profile;
            $response->donate_link = $remote->donate_link;
            $response->homepage = $remote->homepage;
            $response->download_link = $remote->download_url;
            $response->trunk = $remote->download_url;
            $response->requires_php = $remote->requires_php;
            $response->last_updated = $remote->last_updated;
    
            $response->sections = [
                'description' => $remote->sections->description,
                'installation' => $remote->sections->installation,
                'changelog' => $remote->sections->changelog
            ];
    
            if ( ! empty( $remote->banners ) ) {
                $response->banners = [
                    'low' => $remote->banners->low,
                    'high' => $remote->banners->high
                ];
            }
    
            return $response;
        }


        /**
         * Update plugin
         * 
         * @since 3.0.0
         * @param $transient
         * @return string
         */
        public function update_plugin( $transient ) {
            if ( empty( $transient->checked ) ) {
                return $transient;
            }
        
            $cached_data = $this->request();
        
            if ( $cached_data && version_compare( $this->version, $cached_data->version, '<' ) && version_compare( $cached_data->requires, get_bloginfo( 'version' ), '<=' ) && version_compare( $cached_data->requires_php, PHP_VERSION, '<' ) ) {
                $this->update_available = $cached_data;

                $response = new \stdClass();
                $response->slug = $this->plugin_slug;
                $response->plugin = "{$this->plugin_slug}/{$this->plugin_slug}.php";
                $response->new_version = $cached_data->version;
                $response->tested = $cached_data->tested;
                $response->package = $cached_data->download_url;
                $transient->response[ $response->plugin ] = $response;
            }
        
            return $transient;
        }      


        /**
         * Purge cache on update plugin
         * 
         * @since 3.0.0
         * @param $upgrader
         * @param $options
         * @return void
         */
        public function purge_cache( $upgrader, $options ) {
            if ( $this->cache_allowed && 'update' === $options['action'] && 'plugin' === $options[ 'type' ] ) {
                delete_transient('woo_custom_installments_api_request_cache');
                delete_transient('woo_custom_installments_api_response_cache');
                delete_transient( $this->cache_key );
            }
        }


        /**
         * Add check updates link in the plugin_row_meta
         * 
         * @since 3.0.0
         * @param $actions
         * @param $plugin_file "woo-custom-installments.php"
         * @return string
         */
        public function add_check_updates_link( $actions, $plugin_file ) {
            if ( $plugin_file === $this->plugin_slug . '/' . $this->plugin_slug . '.php' ) {
                $check_updates_link = '<a href="' . esc_url( add_query_arg( 'woo_custom_installments_check_updates', '1' ) ) . '">' . esc_html__( 'Verificar atualizações', 'woo-custom-installments' ) . '</a>';
                $actions['woo_custom_installments_check_updates'] = $check_updates_link;
            }
            
            return $actions;
        }
        

        /**
         * Check manual updates
         * 
         * @since 3.0.0
         * @param $plugins
         * @return string
         */
        public function check_manual_update_query_arg( $plugins ) {
            if ( isset( $_GET['woo_custom_installments_check_updates'] ) && $_GET['woo_custom_installments_check_updates'] === '1' ) {

                // purge cache before request on server
                delete_transient('woo_custom_installments_api_request_cache');
                delete_transient('woo_custom_installments_api_response_cache');
                delete_transient( $this->cache_key );
                delete_transient( $this->cache_data_base_key );

                $remote_data = $this->request();
        
                if ( $remote_data ) {
                    $current_version = $this->version;
                    $latest_version = $remote_data->version;
        
                    // if the current version is lower than that of the remote server
                    if ( version_compare( $current_version, $latest_version, '<' ) ) {
                        $message = esc_html__('Uma nova versão do plugin Parcelas Customizadas para WooCommerce está disponível.', 'woo-custom-installments');
                        $class = 'notice-success';
                        ?>
                        <script type="text/javascript">
                            if ( !sessionStorage.getItem('reload_woo_custom_installments_update') ) {
                                sessionStorage.setItem('reload_woo_custom_installments_update', 'true');
                                window.location.reload();
                            }
                        </script>
                        <?php
                    } elseif ( version_compare( $current_version, $latest_version, '>=' ) ) {
                        $message = esc_html__('A versão do plugin Parcelas Customizadas para WooCommerce é a mais recente.', 'woo-custom-installments');
                        $class = 'notice-success';
                    }
                } else {
                    $message = esc_html__('Não foi possível verificar atualizações para o plugin Parcelas Customizadas para WooCommerce.', 'woo-custom-installments');
                    $class = 'notice-error';
                }

                // display notice
                echo '<div class="notice is-dismissible '. $class .'"><p>'. $message .'</p></div>';
            }
        
            return $plugins;
        }   
        
    }
}

Woo_Custom_Installments_Updater::run();