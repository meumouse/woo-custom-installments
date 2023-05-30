<?php

/**
 * Plugin Name: 			Parcelas Customizadas para WooCommerce
 * Description: 			Extensão que permite exibir o parcelamento, desconto e juros por forma de pagamento para lojas WooCommerce.
 * Plugin URI: 				https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/
 * Author: 					MeuMouse.com
 * Author URI: 				https://meumouse.com/
 * Version: 				2.4.0
 * WC requires at least: 	5.0.0
 * WC tested up to: 		7.7.0
 * Requires PHP: 			7.2
 * Tested up to:      		6.2.2
 * Text Domain: 			woo-custom-installments
 * Domain Path: 			/languages
 * License: 				GPL2
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
		 * @var object
		 * @since 1.0.0
		 */
		private static $instance = null;

		/**
		 * The token
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $token;

		/**
		 * The version number
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version;

		/**
		 * Constructor function.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'woo_custom_installments_load_checker' ), 5 );
		}
		
		public function woo_custom_installments_load_checker() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			// Display notice if PHP version is bottom 7.2
			if ( version_compare( phpversion(), '7.2', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'woo_custom_installments_php_version_notice' ) );
				return;
			}

			// display notice if exists other installment plugins
			if ( class_exists('WC_Parcelas') || class_exists('WC_Simulador_Parcelas') || class_exists('Alg_Woocommerce_Checkout_Fees') || class_exists('WC_Payment_Discounts') || class_exists('Woo_Payment_Discounts') ) {
				add_action( 'admin_notices', array( $this, 'woo_custom_installments_prevent_conflit' ) );
				return;
			}
		
			// Verifica se o Woocommerce está ativo
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		
				// add action and filters
				$this->token = 'woo-custom-installments';
				$this->version = '2.4.0';
		
				add_action( 'init', array( $this, 'load_plugin_textdomain' ), -1 );
				add_action( 'plugins_loaded', array( $this, 'woo_custom_installments_update_checker' ), 30 );
				add_action( 'plugins_loaded', array( $this, 'setup_constants' ), 15 );
				add_action( 'plugins_loaded', array( $this, 'setup_includes' ), 20 );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'woo_custom_installments_plugin_links' ), 10, 4 );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'get_pro_woo_custom_installments_link' ), 10, 4 );
		
				if( get_option( 'license_status' ) == 'valid' ) {
					remove_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'get_pro_woo_custom_installments_link' ), 10, 4 );
				}
			} else {
				deactivate_plugins( 'woo-custom-installments/woo-custom-installments.php' );
				add_action( 'admin_notices', array( $this, 'woo_custom_installments_wc_deactivate_notice' ) );
			}

			// display notice if WooCommerce version is bottom 6.0
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && version_compare( WC_VERSION, '6.0', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'woo_custom_installments_wc_version_notice' ) );
				return;
			}
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
		 * @param  string $type admin, ajax, cron or wciend.
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
				case 'wciend':
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
			// get array settings in $options
			$options = get_option( 'woo-custom-installments-setting' );

			/**
			 * Class init plugin
			 * 
			 * @since 1.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/class-woo-custom-installments-init.php';

			/**
			 * Core functions
			 * 
			 * @since 1.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/woo-custom-installments-functions.php';

			/**
			 * Discounts per payment mathod
			 * 
			 * @since 2.0.0
			 */
			if( get_option( 'license_status' ) == 'valid' && isset( $options['enable_all_discount_options'] ) == 'yes' ) {
				include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/classes/class-woo-custom-installments-add-discounts.php';
			}

			/**
			 * Interest per payment method
			 * 
			 * @since 2.3.5
			 */
			if( get_option( 'license_status' ) == 'valid' && isset( $options['enable_all_interest_options'] ) == 'yes' ) {
				include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/classes/class-woo-custom-installments-add-interest.php';
			}

			/**
			 * Admin options
			 * 
			 * @since 1.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/class-woo-custom-installments-admin-options.php';

			/**
			 * Front-end template
			 * 
			 * @since 1.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/classes/class-woo-custom-installments-frontend-template.php';

			/**
			 * Calculate values
			 * 
			 * @since 1.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/classes/class-woo-custom-installments-calculate-values.php';

			/**
			 * Load API settings
			 * 
			 * @since 2.0.0
			 */
			require_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/classes/class-woo-custom-installments-api.php';

			/**
			 * Custom design
			 * 
			 * @since 2.1.0
			 */
			require_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/classes/class-woo-custom-installments-custom-design.php';
			
		}


		/**
		 * WooCommerce version notice.
		 */
		public function woo_custom_installments_wc_version_notice() {
			echo '<div class="notice is-dismissible error">
					<p>' . __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer a versão do WooCommerce 5.0 ou maior. Faça a atualização do plugin WooCommerce.', 'woo-custom-installments' ) . '</p>
				</div>';
		}

		/**
		 * Notice if WooCommerce is deactivate
		 */
		public function woo_custom_installments_wc_deactivate_notice() {
			if ( !current_user_can( 'install_plugins' ) ) { return; }

			echo '<div class="notice is-dismissible error">
					<p>' . __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer que <strong>WooCommerce</strong> esteja instalado e ativado.', 'woo-custom-installments' ) . '</p>
				</div>';
		}

		/**
		 * PHP version notice
		 */
		public function woo_custom_installments_php_version_notice() {
			echo '<div class="notice is-dismissible error">
					<p>' . __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer a versão do PHP 7.2 ou maior. Contate o suporte da sua hospedagem para realizar a atualização.', 'woo-custom-installments' ) . '</p>
				</div>';
		}


		/**
		 * Detect if exists other plugins of installments
		 * 
		 * @since 2.3.5
		 */
		public function woo_custom_installments_prevent_conflit() {
			if ( !current_user_can( 'install_plugins' ) ) { return; }

			echo '<div class="notice error">
					<p>' . __( '<strong>Parcelas Customizadas para WooCommerce:</strong> Foram detectados outros plugins que tem funcionalidades de exibir parcelas e ou descontos. Desative-os para evitar conflitos.', 'woo-custom-installments' ) . '</p>
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
				'<a href="' . admin_url( 'admin.php?page=woo-custom-installments' ) . '">' . __( 'Configurar', 'woo-custom-installments' ) . '</a>',
				'<a href="https://meumouse.com/docs/plugins/parcelas-customizadas-para-woocommerce/" target="_blank">' . __( 'Ajuda', 'woo-custom-installments' ) . '</a>'
			);

			return array_merge( $plugins_links, $action_links );
		}


		/**
		 * Plugin action links Pro version
		 * 
		 * @since 2.0.0
		 * @return array
		 */
		public static function get_pro_woo_custom_installments_link( $action_links ) {
			$plugins_links = array(
			'<a id="get-pro-woo-custom-installments" target="_blank" href="https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugins-list&utm_campaign=wci">' . __( 'Seja PRO', 'woo-custom-installments' ) . '</a>'
			);
		
			return array_merge( $plugins_links, $action_links );
		}


		/**
		 * Plugin update checker
		 * 
		 * @since 1.0.0
		 */
		public function woo_custom_installments_update_checker(){
			require WOO_CUSTOM_INSTALLMENTS_DIR .'core/update-checker/plugin-update-checker.php';
			$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker( 'https://raw.githubusercontent.com/meumouse/woo-custom-installments/main/update-checker.json', __FILE__, 'woo-custom-installments' );
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