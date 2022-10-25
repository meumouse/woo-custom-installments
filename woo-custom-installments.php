<?php

/**
 * Plugin Name:     Parcelas Customizadas para WooCommerce
 * Description:     Extensão que permite exibir o parcelamento e preço com desconto para lojas WooCommerce.
 * Plugin URI:      https://meumouse.com/
 * Author:          MeuMouse.com
 * Author URI:      https://meumouse.com/
 * Version:         1.0.0
 * WC requires at least: 5.0.0
 * WC tested up to:      7.0.0
 * Text Domain:     woo-custom-installments
 * Domain Path:     /languages
 * License: GPL2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

// Define WOO_CUSTOM_INSTALLMENTS_PLUGIN_FILE.
if ( ! defined( 'WOO_CUSTOM_INSTALLMENTS_PLUGIN_FILE' ) ) {
	define( 'WOO_CUSTOM_INSTALLMENTS_PLUGIN_FILE', __FILE__ );
}

if ( ! class_exists( 'Woo_Custom_Installments' ) ) {
  
	/**
	 * Main Woo_Custom_Installments Class
	 *
	 * @class Woo_Custom_Installments
	 * @version 1.0.0
	 * @since 1.0.0
	 * @package MeuMouse.com
	 */
	final class Woo_Custom_Installments {

		/**
		 * Woo_Custom_Installments The single instance of Woo_Custom_Installments.
		 *
		 * @var     object
		 * @since   1.0.0
		 */
		private static $instance = null;

		/**
		 * The token.
		 *
		 * @var     string
		 * @since   1.0.0
		 */
		public $token;

		/**
		 * The version number.
		 *
		 * @var     string
		 * @since   1.0.0
		 */
		public $version;

		/**
		 * Constructor function.
		 *
		 * @since   1.0.0
		 * @return  void
		 */
		public function __construct() {
			$this->token   = 'woo-custom-installments';
			$this->version = '1.0.0';

		add_action( 'init', array( $this, 'load_plugin_textdomain' ), -1 );
		add_action( 'init', array( $this, 'woo_custom_installments_load_checker' ), 5 );
		add_action( 'plugins_loaded', array( $this, 'woo_custom_installments_update_checker' ), 10 );
		add_action( 'plugins_loaded', array( $this, 'setup_constants' ), 15 );
		add_action( 'plugins_loaded', array( $this, 'setup_includes' ), 20 );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'woo_custom_installments_plugin_links' ), 10, 4 );

		}

		/**
		 * Main Woo_Custom_Installments Instance
		 *
		 * Ensures only one instance of Woo_Custom_Installments is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see Woo_Custom_Installments()
		 * @return Main Woo_Custom_Installments instance
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Setup plugin constants
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function setup_constants() {

			// Plugin Folder Path.
			if ( ! defined( 'WOO_CUSTOM_INSTALLMENTS_DIR' ) ) {
				define( 'WOO_CUSTOM_INSTALLMENTS_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WOO_CUSTOM_INSTALLMENTS_URL' ) ) {
				define( 'WOO_CUSTOM_INSTALLMENTS_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WOO_CUSTOM_INSTALLMENTS_FILE' ) ) {
				define( 'WOO_CUSTOM_INSTALLMENTS_FILE', __FILE__ );
			}

			$this->define( 'WOO_CUSTOM_INSTALLMENTS_ABSPATH', dirname( WOO_CUSTOM_INSTALLMENTS_FILE ) . '/' );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_VERSION', $this->version );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param string      $name  Constant name.
		 * @param string|bool $value Constant value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 *
		 * @param  string $type admin, ajax, cron or woocustominstallmentsend.
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'woocustominstallmentsend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
			}
		}

		/**
		 * Include required files
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function setup_includes() {

      /**
       * Class init plugin
       */
      include_once WOO_CUSTOM_INSTALLMENTS_DIR . '/includes/class-woo-custom-installments-init.php';

      /**
       * Core functions
       */
      include_once WOO_CUSTOM_INSTALLMENTS_DIR . '/includes/woo-custom-installments-functions.php';

      /**
       * Admin options
       */
      include_once WOO_CUSTOM_INSTALLMENTS_DIR . '/includes/admin/class-woo-custom-installments-admin-options.php';

      /**
       * Front-end template
       */
      include_once WOO_CUSTOM_INSTALLMENTS_DIR . '/includes/classes/class-woo-custom-installments-frontend-template.php';

      /**
       * Calculate values
       */
      include_once WOO_CUSTOM_INSTALLMENTS_DIR . '/includes/classes/class-woo-custom-installments-calculate-values.php';

      /**
       * Integration with gateway PagSeguro
       */
      if ( class_exists( 'WC_PagSeguro' ) ) {
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . '/includes/integrations/class-woo-custom-installments-pagseguro-integration.php';
      }

	}

    /**
     * Check requirements for loading plugin
     * 
     * @since 1.0.0
     * @return bool
     */
    public function woo_custom_installments_load_checker(){

      // Display notice if PHP version is bottom 7.0
      if ( version_compare( phpversion(), '7.0', '<' ) ) {
        add_action( 'admin_notices', array( $this, 'woo_custom_installments_php_version_notice' ) );
        return;
      }

      // Check if WooCommerce is active
      if ( ! class_exists( 'WooCommerce' ) ) {
        return;
      }

      // Display notice if WooCommerce version is bottom 5.0
      if ( version_compare( WC_VERSION, '5.0', '<' ) ) {
        add_action( 'admin_notices', array( $this, 'woo_custom_installments_wc_version_notice' ) );
        return;
      }
    }

    /**
     * WooCommerce version notice.
     */
    public function woo_custom_installments_wc_version_notice() {
      echo '<div class="notice is-dismissible error">
            <p>' . __( 'O plugin Parcelas Customizadas para WooCommerce requer a versão do WooCommerce 5.0 ou maior. Faça a atualização do plugin WooCommerce.', 'woo-custom-installments' ) . '</p>
          </div>';
    }

    /**
     * PHP version notice.
     */
    public function woo_custom_installments_php_version_notice() {
      echo '<div class="notice is-dismissible error">
              <p>' . __( 'O plugin Parcelas Customizadas para WooCommerce requer a versão do PHP 7.0 ou maior. Contate o suporte da sua hospedagem para realizar a atualização.', 'woo-custom-installments' ) . '</p>
          </div>';
    }

    /**
     * Plugin action links
     * 
     * @since 1.0.0
     * @return array
     */
    public function woo_custom_installments_plugin_links( $action_links ) {
      $plugins_links = array(
        '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products&section=woo-custom-installments' ) . '">' . __( 'Configurar', 'woo-custom-installments' ) . '</a>',
        '<a href="https://meumouse.com/docs/plugins/parcelas-customizadas-para-woocommerce/" target="_blank">Ajuda</a>'
      );
      return array_merge( $plugins_links, $action_links );
    }

    /**
     * Load the plugin text domain for translation.
     */
    public static function load_plugin_textdomain() {
      load_plugin_textdomain( 'woo-custom-installments', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', WOO_CUSTOM_INSTALLMENTS_PLUGIN_FILE ) );
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Trapaceando?', 'woo-custom-installments' ), '1.0.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Trapaceando?', 'woo-custom-installments' ), '1.0.0' );
		}

		/**
		 * Plugin update checker dependencies (PLEASE DON'T TOUCH HERE!!! | POR FAVOR, NÃO MEXA AQUI!!!)
		 */
		public function woo_custom_installments_update_checker(){
			require  'core/update-checker/plugin-update-checker.php';
			$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker( 'https://raw.githubusercontent.com/meumouse/woo-custom-installments/main/update-checker.json', __FILE__, 'woo-custom-installments' );
		}
	}
}

/**
 * Returns the main instance of Woo_Custom_Installments to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Woo_Custom_Installments
 */
function Woo_Custom_Installments() { //phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return Woo_Custom_Installments::instance();
}

/**
 * Initialise the plugin
 */
Woo_Custom_Installments();