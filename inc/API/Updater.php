<?php

namespace MeuMouse\Woo_Custom_Installments\API;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\Core\Logger;

use MeuMouse\MDS\SDK\SDK;
use MeuMouse\MDS\SDK\Integration;
use MeuMouse\MDS\SDK\Support\Cache;
use MeuMouse\MDS\SDK\Updates\AbstractUpdater;

use WP_REST_Response;
use Exception;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Update channel of the plugin
 *
 * The remote work belongs to the MDS SDK, which signs, caches and injects releases
 * into the core update transients. This class boots it, bridges the license key
 * managed by API\License and adds what the store owner sees: the manual check, the
 * update notice and the auto update toggle.
 *
 * It has two entry points on purpose. `bootstrap()` is static and runs from
 * Core\Init while the plugin file loads, because the SDK elects its newest embedded
 * copy on `plugins_loaded` at priority -100. The constructor runs later, on `init`,
 * through the automatic class instantiation, and only registers the admin surface.
 *
 * @since 3.0.0
 * @version 5.5.9
 * @package MeuMouse\Woo_Custom_Installments\API
 * @author MeuMouse.com
 */
class Updater {

    /**
     * Path of the SDK loader, relative to the plugin directory
     *
     * @since 5.5.9
     * @var string
     */
    const LOADER = 'vendor/meumouse/mds-php-sdk/mds-sdk.php';

    /**
     * API path of the update check, the only request the license key is attached to
     *
     * @since 5.5.9
     * @var string
     */
    const UPDATE_CHECK_PATH = '/v2/update-check';

    /**
     * REST namespace of the manual update check
     *
     * @since 5.5.9
     * @var string
     */
    const REST_NAMESPACE = 'woo-custom-installments/v1';

    /**
     * REST route of the manual update check
     *
     * @since 5.5.9
     * @var string
     */
    const REST_ROUTE = '/check-updates';

    /**
     * Whether the SDK loader has already been required
     *
     * @since 5.5.9
     * @var bool
     */
    private static $booted = false;

    /**
     * Plugin slug
     *
     * @since 3.0.0
     * @var string
     */
    public $plugin_slug = WOO_CUSTOM_INSTALLMENTS_SLUG;

    /**
     * Installed plugin version
     *
     * @since 3.0.0
     * @var string
     */
    public $version = WOO_CUSTOM_INSTALLMENTS_VERSION;

    /**
     * Plugin basename
     *
     * @since 5.5.9
     * @var string
     */
    public $basename = WOO_CUSTOM_INSTALLMENTS_BASENAME;


    /**
     * Construct function
     *
     * @since 3.0.0
     * @version 5.5.9
     * @return void
     */
    public function __construct() {
        $this->maybe_migrate_legacy_state();

        // manual update check, answered over the REST API
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

        add_filter( 'plugin_row_meta', array( $this, 'add_check_updates_link' ), 10, 2 );

        // enable auto updates
        if ( Admin_Options::get_setting('enable_auto_updates') === 'yes' ) {
            add_filter( 'auto_update_plugin', array( $this, 'enable_auto_update' ), 10, 2 );
        }

        // display new update on plugins list
        if ( Admin_Options::get_setting('enable_update_notices') === 'yes' ) {
            add_action( 'admin_notices', array( $this, 'admin_update_notice' ) );
        }
    }


    /**
     * Load the SDK and hook the product registration
     *
     * @since 5.5.9
     * @return void
     */
    public static function bootstrap() {
        if ( self::$booted ) {
            return;
        }

        self::$booted = true;

        $loader = WOO_CUSTOM_INSTALLMENTS_DIR . self::LOADER;

        if ( ! is_readable( $loader ) ) {
            return;
        }

        require_once $loader;

        // register on the SDK boot hook, never earlier: it fires after the newest embedded copy wins
        add_action( 'mds_sdk_loaded', array( __CLASS__, 'register_product' ) );

        // drop scheduled events owned by the SDK when the plugin is deactivated
        register_deactivation_hook( WOO_CUSTOM_INSTALLMENTS_FILE, array( __CLASS__, 'shutdown' ) );
    }


    /**
     * Register this product on the elected SDK copy
     *
     * @since 5.5.9
     * @return void
     */
    public static function register_product() {
        if ( ! class_exists( SDK::class ) ) {
            return;
        }

        $config = self::get_config();

        // stay completely inert until the product credentials are in place
        if ( empty( $config['api_key'] ) || empty( $config['public_key'] ) ) {
            return;
        }

        // every response is verified with ed25519; without sodium the SDK cannot trust anything
        if ( ! function_exists('sodium_crypto_sign_verify_detached') ) {
            add_action( 'admin_notices', array( __CLASS__, 'missing_sodium_notice' ) );

            return;
        }

        if ( defined('WOO_CUSTOM_INSTALLMENTS_DEV_MODE') && WOO_CUSTOM_INSTALLMENTS_DEV_MODE === true ) {
            add_filter( 'https_ssl_verify', '__return_false' );
            add_filter( 'https_local_ssl_verify', '__return_false' );
            add_filter( 'http_request_host_is_external', '__return_true' );
        }

        try {
            $integration = SDK::register( array(
                'product_slug' => WOO_CUSTOM_INSTALLMENTS_SLUG,
                'type' => 'plugin',
                'file' => WOO_CUSTOM_INSTALLMENTS_BASENAME,
                'current_version' => WOO_CUSTOM_INSTALLMENTS_VERSION,
                'api_base_url' => $config['api_base_url'],
                'api_key' => $config['api_key'],
                'public_key' => $config['public_key'],
                'item_name' => 'Parcelas Customizadas para WooCommerce',
                'text_domain' => 'woo-custom-installments',
                // licensing stays with API\License, so the SDK only owns the update channel
                'mode' => 'updates_only',
                'update_check_ttl' => $config['update_check_ttl'],
            ) );
        } catch ( Exception $e ) {
            self::log( 'Failed to register the product on the MDS SDK: ' . $e->getMessage() );

            return;
        }

        if ( ! $integration instanceof Integration ) {
            return;
        }

        // forward the license key managed by API\License on the update check
        add_filter( $integration->product()->key('request_body'), array( __CLASS__, 'set_license_key' ), 10, 2 );
    }


    /**
     * Get the MDS product configuration
     *
     * @since 5.5.9
     * @return array
     */
    public static function get_config() {
        $defaults = array(
            'api_base_url' => WOO_CUSTOM_INSTALLMENTS_MDS_API_URL,
            'api_key' => WOO_CUSTOM_INSTALLMENTS_MDS_API_KEY,
            'public_key' => WOO_CUSTOM_INSTALLMENTS_MDS_PUBLIC_KEY,
            'update_check_ttl' => DAY_IN_SECONDS,
        );

        /**
         * Filter the MDS product configuration
         *
         * @since 5.5.9
         * @param array $defaults | MDS product configuration
         * @return array
         */
        $config = apply_filters( 'Woo_Custom_Installments/Updates/Config', $defaults );

        return is_array( $config ) ? array_merge( $defaults, $config ) : $defaults;
    }


    /**
     * Attach the stored license key to the update check payload
     *
     * The key is sent whenever one is stored, without checking its local validity:
     * the server can waive the gate for a given license, and a client that judged
     * by itself would refuse the request before the server could honour the waiver.
     *
     * @since 5.5.9
     * @param array $body | Outbound request payload
     * @param string $path | API path of the request
     * @return array
     */
    public static function set_license_key( $body, $path ) {
        if ( self::UPDATE_CHECK_PATH !== $path || ! is_array( $body ) ) {
            return $body;
        }

        $license_key = get_option('woo_custom_installments_license_key');

        if ( is_string( $license_key ) && trim( $license_key ) !== '' ) {
            $body['license_key'] = trim( $license_key );
        }

        return $body;
    }


    /**
     * Get the registered SDK integration
     *
     * @since 5.5.9
     * @return Integration|null
     */
    public static function integration() {
        if ( ! class_exists( SDK::class ) ) {
            return null;
        }

        return SDK::get( WOO_CUSTOM_INSTALLMENTS_SLUG );
    }


    /**
     * Check if the update channel is registered and running
     *
     * @since 5.5.9
     * @return bool
     */
    public static function is_active() {
        return self::integration() instanceof Integration;
    }


    /**
     * Purge the cached update payload and force WordPress to check again
     *
     * @since 5.5.9
     * @return void
     */
    public static function clear_cache() {
        $integration = self::integration();

        if ( $integration instanceof Integration ) {
            $cache = new Cache( $integration->product() );
            $cache->delete( AbstractUpdater::CACHE_UPDATE );
        }

        // force core to rebuild its own update transient on the next check
        delete_site_transient('update_plugins');
    }


    /**
     * Get the update offered by the server for this plugin, if any
     *
     * @since 5.5.9
     * @return object|null
     */
    public static function get_available_update() {
        $transient = get_site_transient('update_plugins');

        if ( ! is_object( $transient ) || empty( $transient->response[ WOO_CUSTOM_INSTALLMENTS_BASENAME ] ) ) {
            return null;
        }

        $update = $transient->response[ WOO_CUSTOM_INSTALLMENTS_BASENAME ];

        if ( empty( $update->new_version ) || version_compare( WOO_CUSTOM_INSTALLMENTS_VERSION, $update->new_version, '>=' ) ) {
            return null;
        }

        return $update;
    }


    /**
     * Remove the events scheduled by the SDK
     *
     * @since 5.5.9
     * @return void
     */
    public static function shutdown() {
        $integration = self::integration();

        if ( $integration instanceof Integration ) {
            $integration->shutdown();
        }
    }


    /**
     * Register the REST route of the manual update check
     *
     * @since 5.5.9
     * @return void
     */
    public function register_rest_routes() {
        register_rest_route( self::REST_NAMESPACE, self::REST_ROUTE, array(
            'methods' => 'POST',
            'callback' => array( $this, 'check_updates_response' ),
            'permission_callback' => array( $this, 'check_updates_permission' ),
        ));
    }


    /**
     * Check if the current user may run a manual update check
     *
     * @since 5.5.9
     * @return bool
     */
    public function check_updates_permission() {
        return current_user_can('update_plugins');
    }


    /**
     * Run a manual update check and describe the outcome
     *
     * @since 5.5.9
     * @return WP_REST_Response
     */
    public function check_updates_response() {
        if ( ! self::is_active() ) {
            return new WP_REST_Response( array(
                'success' => false,
                'has_update' => false,
                'message' => esc_html__( 'Não foi possível verificar atualizações para o plugin Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' ),
            ), 200 );
        }

        // purge the cached payload and ask the server again
        self::clear_cache();
        wp_update_plugins();

        $update = self::get_available_update();

        if ( ! $update ) {
            return new WP_REST_Response( array(
                'success' => true,
                'has_update' => false,
                'current_version' => $this->version,
                'message' => esc_html__( 'A versão do plugin é a mais recente.', 'woo-custom-installments' ),
            ), 200 );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'has_update' => true,
            'current_version' => $this->version,
            'new_version' => $update->new_version,
            'update_url' => $this->get_update_url(),
            'message' => sprintf(
                /* translators: %s: new plugin version */
                esc_html__( 'A versão %s está disponível.', 'woo-custom-installments' ),
                $update->new_version
            ),
        ), 200 );
    }


    /**
     * Add check updates link in the plugin_row_meta
     *
     * @since 3.0.0
     * @version 5.5.9
     * @param string $plugin_meta | An array of the plugin’s metadata, including the version, author, author URI, and plugin URI
     * @param string $plugin_file | Path to the plugin file relative to the plugins directory
     * @return array
     */
    public function add_check_updates_link( $plugin_meta, $plugin_file ) {
        if ( $plugin_file === $this->basename ) {
            $plugin_meta['woo_custom_installments_check_updates'] = sprintf(
                '<a href="#" class="wci-check-updates">%s</a>',
                esc_html__( 'Verificar atualizações', 'woo-custom-installments' )
            );
        }

        return $plugin_meta;
    }


    /**
     * Enable auto-update only for this plugin
     *
     * @since 5.4.0
     * @version 5.5.9
     * @param bool $update | Whether to enable automatic update
     * @param object $item | The plugin object being checked
     * @return bool
     */
    public function enable_auto_update( $update, $item ) {
        if ( isset( $item->plugin ) && $item->plugin === $this->basename ) {
            return true; // enable only this plugin
        }

        return $update;
    }


    /**
     * Display update notice in the admin panel
     *
     * @since 5.4.0
     * @version 5.5.9
     * @return void
     */
    public function admin_update_notice() {
        $update = self::get_available_update();

        if ( ! $update ) {
            return;
        }

        $message = sprintf(
            __( 'Uma nova versão do plugin <strong>Parcelas Customizadas para WooCommerce</strong> (%s) está disponível. <a href="%s">Atualize agora</a>.', 'woo-custom-installments' ),
            esc_html( $update->new_version ),
            esc_url( $this->get_update_url() )
        );

        printf( '<div class="%1$s"><p>%2$s</p></div>', 'notice notice-success is-dismissible', wp_kses_post( $message ) );
    }


    /**
     * Notice displayed when the sodium extension is missing
     *
     * @since 5.5.9
     * @return void
     */
    public static function missing_sodium_notice() {
        if ( ! current_user_can('update_plugins') ) {
            return;
        }

        $message = __( 'A extensão <strong>sodium</strong> do PHP não está disponível neste servidor, por isso o plugin <strong>Parcelas Customizadas para WooCommerce</strong> não pode verificar atualizações. Contate o suporte da sua hospedagem para habilitá-la.', 'woo-custom-installments' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', 'notice notice-warning is-dismissible', wp_kses_post( $message ) );
    }


    /**
     * Get the URL that starts the update of this plugin
     *
     * @since 5.5.9
     * @return string
     */
    private function get_update_url() {
        return add_query_arg(
            array(
                'action' => 'upgrade-plugin',
                'plugin' => $this->basename,
                '_wpnonce' => wp_create_nonce( 'upgrade-plugin_' . $this->basename ),
            ),
            admin_url('update.php')
        );
    }


    /**
     * Remove the state left behind by the update channel used until 5.5.8
     *
     * @since 5.5.9
     * @return void
     */
    private function maybe_migrate_legacy_state() {
        if ( get_option('woo_custom_installments_mds_migration') === 'yes' ) {
            return;
        }

        // events replaced by the update check of the SDK, which rides the WordPress update cron
        wp_clear_scheduled_hook('Woo_Custom_Installments/Updates/Auto_Updates');
        wp_clear_scheduled_hook('Woo_Custom_Installments/Updates/Check_Daily_Updates');

        delete_option('woo_custom_installments_update_available');
        delete_transient('woo_custom_installments_check_updates');
        delete_transient('woo_custom_installments_remote_data');

        update_option( 'woo_custom_installments_mds_migration', 'yes' );
    }


    /**
     * Log a bootstrap failure of the update channel
     *
     * @since 5.5.9
     * @param string $message | Log message
     * @return void
     */
    private static function log( $message ) {
        // WooCommerce may not be loaded yet on `plugins_loaded` priority -100
        if ( ! function_exists('wc_get_logger') ) {
            error_log( 'Woo Custom Installments: ' . $message );

            return;
        }

        Logger::set_logger_source( 'woo-custom-installments-updates', false );
        Logger::register_log( $message, 'error' );
    }
}
