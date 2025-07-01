<?php

/**
 * Plugin Name: 				Parcelas Customizadas para WooCommerce
 * Description: 				Extensão que permite exibir o parcelamento, desconto e juros por forma de pagamento para lojas WooCommerce.
 * Plugin URI: 					https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugins_list&utm_campaign=parcelas_customizadas
 * Requires Plugins: 			woocommerce
 * Author: 						MeuMouse.com
 * Author URI: 					https://meumouse.com/?utm_source=wordpress&utm_medium=plugins_list&utm_campaign=parcelas_customizadas
 * Version: 					5.4.9
 * Requires at least: 			6.0
 * WC requires at least: 		6.0.0
 * WC tested up to: 			9.9.5
 * Requires PHP: 				7.4
 * Tested up to:      			6.8.1
 * Text Domain: 				woo-custom-installments
 * Domain Path: 				/languages
 * License: 					GPL2
 * 
 * @package						Parcelas Customizadas para WooCommerce - MeuMouse.com
 * @author						MeuMouse.com
 * @copyright 					2025 MeuMouse.com
 * @license 					Proprietary - See license.md for details
 */

namespace MeuMouse\Woo_Custom_Installments;

// Exit if accessed directly.
defined('ABSPATH') || exit;

if ( ! class_exists('Woo_Custom_Installments') ) {
	/**
	 * Main class for load plugin
	 *
	 * @since 1.0.0
	 * @version 5.0.0
	 * @package MeuMouse.com
	 */
	class Woo_Custom_Installments {

		/**
		 * The single instance of Woo_Custom_Installments class
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance = null;

		/**
		 * The text domain
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public static $slug = 'woo-custom-installments';

		/**
		 * The version number
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public static $version = '5.4.9';

		/**
		 * Constructor function
		 *
		 * @since 1.0.0
		 * @version 5.4.8
		 * @return void
		 */
		public function __construct() {
			// hook before plugin init
			do_action('Woo_Custom_Installments/Before_Init');

			// load plugin after wooocommerce is loaded
			add_action( 'woocommerce_loaded', array( $this, 'init' ), 99 );

			// hook after plugin init
			do_action('Woo_Custom_Installments/Init');
		}
		

		/**
		 * Check requeriments and load plugin
		 * 
		 * @since 1.0.0
		 * @version 5.4.0
		 * @return void
		 */
		public function init() {
			// display notice if PHP version is bottom 7.4
			if ( version_compare( phpversion(), '7.4', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
				return;
			}

			// define constants
			self::setup_constants();

			// load Composer
			require_once WOO_CUSTOM_INSTALLMENTS_DIR . 'vendor/autoload.php';

			// initialize classes
			new \MeuMouse\Woo_Custom_Installments\Core\Init;
		}


		/**
		 * PHP version notice
		 * 
		 * @since 2.0.0
		 * @version 5.4.0
		 * @return void
		 */
		public function php_version_notice() {
			$class = 'notice notice-error is-dismissible';
			$message = __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer a versão do PHP 7.4 ou maior. Contate o suporte da sua hospedagem para realizar a atualização.', 'woo-custom-installments' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}


		/**
		 * Ensures only one instance of Woo_Custom_Installments class is loaded or can be loaded
		 *
		 * @since 1.0.0
		 * @return object | Woo_Custom_Installments instance
		 */
		public static function run() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @since 1.0.0
		 * @version 5.4.0
		 * @return void
		 */
		public static function setup_constants() {
			$base_file = __FILE__;
			$base_dir = plugin_dir_path( $base_file );
			$base_url = plugin_dir_url( $base_file );

			$constants = array(
				'WOO_CUSTOM_INSTALLMENTS_BASENAME' => plugin_basename( $base_file ),
				'WOO_CUSTOM_INSTALLMENTS_FILE' => $base_file,
				'WOO_CUSTOM_INSTALLMENTS_DIR' => $base_dir,
				'WOO_CUSTOM_INSTALLMENTS_INC' => $base_dir . 'inc/',
				'WOO_CUSTOM_INSTALLMENTS_URL' => $base_url,
				'WOO_CUSTOM_INSTALLMENTS_ASSETS' => $base_url . 'assets/',
				'WOO_CUSTOM_INSTALLMENTS_ABSPATH' => dirname( $base_file ) . '/',
				'WOO_CUSTOM_INSTALLMENTS_TEMPLATES_DIR' => $base_dir . 'templates/',
				'WOO_CUSTOM_INSTALLMENTS_SLUG' => self::$slug,
				'WOO_CUSTOM_INSTALLMENTS_VERSION' => self::$version,
				'WOO_CUSTOM_INSTALLMENTS_ADMIN_EMAIL' => get_option('admin_email'),
				'WOO_CUSTOM_INSTALLMENTS_DOCS_LINK' => 'https://ajuda.meumouse.com/docs/woo-custom-installments/overview',
				'WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE' => false,
				'WOO_CUSTOM_INSTALLMENTS_DEV_MODE' => false,
			);

			// iterate for each constant item
			foreach ( $constants as $key => $value ) {
				if ( ! defined( $key ) ) {
					define( $key, $value );
				}
			}
		}


		/**
		 * Cloning is forbidden
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Trapaceando?', 'woo-custom-installments' ), '1.0.0' );
		}


		/**
		 * Unserializing instances of this class is forbidden
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Trapaceando?', 'woo-custom-installments' ), '1.0.0' );
		}

	}
}

/**
 * Initialise the plugin
 * 
 * @since 1.0.0
 * @return object Woo_Custom_Installments
 */
Woo_Custom_Installments::run();