<?php

/**
 * Plugin Name: 			Parcelas Customizadas para WooCommerce
 * Description: 			Extensão que permite exibir o parcelamento, desconto e juros por forma de pagamento para lojas WooCommerce.
 * Plugin URI: 				https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/
 * Requires Plugins: 		woocommerce
 * Author: 					MeuMouse.com
 * Author URI: 				https://meumouse.com/
 * Version: 				5.2.3
 * WC requires at least: 	6.0.0
 * WC tested up to: 		9.3.1
 * Requires PHP: 			7.4
 * Tested up to:      		6.7.1
 * Text Domain: 			woo-custom-installments
 * Domain Path: 			/languages
 * License: 				GPL2
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
		 * The token
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
		public static $version = '5.2.3';

		/**
		 * Constructor function
		 *
		 * @since 1.0.0
		 * @version 5.2.0
		 * @return void
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ), 99 );
		}
		

		/**
		 * Check requeriments and load plugin
		 * 
		 * @since 1.0.0
		 * @version 5.2.0
		 * @return void
		 */
		public function init() {
			// Display notice if PHP version is bottom 7.4
			if ( version_compare( phpversion(), '7.4', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
				return;
			}

			$this->setup_constants();

			load_plugin_textdomain( 'woo-custom-installments', false, dirname( WOO_CUSTOM_INSTALLMENTS_BASENAME ) . '/languages/' );

			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
		
			// check if WooCommerce is active
			if ( is_plugin_active('woocommerce/woocommerce.php') && version_compare( WC_VERSION, '6.0', '>' ) ) {
				add_action( 'before_woocommerce_init', array( $this, 'setup_hpos_compatibility' ) );
				add_action( 'plugins_loaded', array( $this, 'setup_includes' ), 999 );
				add_filter( 'plugin_action_links_' . WOO_CUSTOM_INSTALLMENTS_BASENAME, array( $this, 'add_action_plugin_links' ), 10, 4 );
				add_filter( 'plugin_row_meta', array( $this, 'add_row_meta_links' ), 10, 4 );
				
				$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

				// remove Pro badge if plugin is licensed
				if ( get_option('woo_custom_installments_license_status') !== 'valid' && false !== strpos( $url, 'wp-admin/plugins.php' ) ) {
					add_filter( 'plugin_action_links_' . WOO_CUSTOM_INSTALLMENTS_BASENAME, array( $this, 'display_be_pro_badge' ), 10, 4 );
					add_action( 'admin_head', array( $this, 'be_pro_badge_styles' ) );
				}
			} else {
				add_action( 'admin_notices', array( $this, 'woo_version_notice' ) );
				deactivate_plugins('woo-custom-installments/woo-custom-installments.php');
				add_action( 'admin_notices', array( $this, 'require_woocommerce_notice' ) );
			}
		}


		/**
		 * Setup WooCommerce High-Performance Order Storage (HPOS) compatibility
		 * 
		 * @since 3.2.0
		 * @version 4.5.0
		 * @return void
		 */
		public function setup_hpos_compatibility() {
			if ( defined('WC_VERSION') && version_compare( WC_VERSION, '7.1', '>' ) ) {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
						'custom_order_tables', WOO_CUSTOM_INSTALLMENTS_FILE, true );
				}
			}
		}


		/**
		 * Ensures only one instance of Woo_Custom_Installments class is loaded or can be loaded
		 *
		 * @since 1.0.0
		 * @return Main Woo_Custom_Installments instance
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
		 * @version 5.2.0
		 * @return void
		 */
		public function setup_constants() {
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_INC', WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/' );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_ASSETS', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/' );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_FILE', __FILE__ );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_ABSPATH', dirname( WOO_CUSTOM_INSTALLMENTS_FILE ) . '/' );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_SLUG', self::$slug );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_VERSION', self::$version );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_ADMIN_EMAIL', get_option('admin_email') );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_DOCS_LINK', 'https://ajuda.meumouse.com/docs/woo-custom-installments/overview' );
		}


		/**
		 * Define constant if not already set
		 *
		 * @since 1.0.0
		 * @param string $name | Constant name
		 * @param string|bool $value Constant value
		 * @return void
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}


		/**
		 * Include required files
		 *
		 * @since 1.0.0
		 * @version 5.0.0
		 * @return void
		 */
		public function setup_includes() {
			$includes = apply_filters( 'woo_custom_installments_setup_includes', array(
				'functions.php',
				'class-init.php',
				'classes/class-license.php',
				'classes/class-admin-options.php',
				'classes/class-assets.php',
				'classes/class-ajax.php',
				'classes/class-helpers.php',
				'classes/class-frontend.php',
				'classes/class-shortcodes.php',
				'classes/class-custom-design.php',
				'classes/class-calculate-values.php',
				'classes/class-discounts.php',
				'classes/class-interests.php',
				'classes/class-schema.php',
				'classes/class-compat-autoloader.php',
				'classes/class-elementor-widgets.php',
				'classes/class-updater.php',
			));

			foreach ( $includes as $file ) {
				$file_path = WOO_CUSTOM_INSTALLMENTS_INC . $file;

				if ( file_exists( $file_path ) ) {
					include_once $file_path;
				}
			}
		}


		/**
		 * WooCommerce version notice
		 * 
		 * @since 2.0.0
		 * @version 4.5.0
		 * @return void
		 */
		public function woo_version_notice() {
			$class = 'notice notice-error is-dismissible';
			$message = __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer a versão do WooCommerce 6.0 ou maior. Faça a atualização do plugin WooCommerce.', 'woo-custom-installments' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}


		/**
		 * Notice if WooCommerce is deactivate
		 * 
		 * @since 2.0.0
		 * @version 4.5.0
		 * @return void
		 */
		public function require_woocommerce_notice() {
			if ( current_user_can('install_plugins') ) {
				$class = 'notice notice-error is-dismissible';
				$message = __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer que <strong>WooCommerce</strong> esteja instalado e ativado.', 'woo-custom-installments' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
			}
		}


		/**
		 * PHP version notice
		 * 
		 * @since 2.0.0
		 * @version 4.5.0
		 * @return void
		 */
		public function php_version_notice() {
			$class = 'notice notice-error is-dismissible';
			$message = __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer a versão do PHP 7.4 ou maior. Contate o suporte da sua hospedagem para realizar a atualização.', 'woo-custom-installments' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}


		/**
		 * Plugin action links
		 * 
		 * @since 1.0.0
		 * @version 4.5.0
		 * @return array
		 */
		public function add_action_plugin_links( $action_links ) {
			$plugins_links = array(
				'<a href="' . admin_url('admin.php?page=woo-custom-installments') . '">'. __( 'Configurar', 'woo-custom-installments' ) .'</a>',
			);

			return array_merge( $plugins_links, $action_links );
		}


		/**
		 * Add meta links on plugin
		 * 
		 * @since 4.0.0
		 * @version 4.5.0
		 * @param string $plugin_meta | An array of the plugin’s metadata, including the version, author, author URI, and plugin URI
		 * @param string $plugin_file | Path to the plugin file relative to the plugins directory
		 * @param array $plugin_data | An array of plugin data
		 * @param string $status | Status filter currently applied to the plugin list
		 * @return string
		 */
		public function add_row_meta_links( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( strpos( $plugin_file, WOO_CUSTOM_INSTALLMENTS_BASENAME ) !== false ) {
				$new_links = array(
					'docs' => '<a href="'. WOO_CUSTOM_INSTALLMENTS_DOCS_LINK .'" target="_blank">'. __( 'Documentação', 'woo-custom-installments' ) .'</a>',
				);
				
				$plugin_meta = array_merge( $plugin_meta, $new_links );
			}
		
			return $plugin_meta;
		}


		/**
		 * Plugin action links Pro version
		 * 
		 * @since 2.0.0
		 * @version 4.5.0
		 * @return array
		 */
		public static function display_be_pro_badge( $action_links ) {
			$plugins_links = array(
				'<a id="get-pro-woo-custom-installments" target="_blank" href="https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugins-list&utm_campaign=wci">' . __( 'Seja PRO', 'woo-custom-installments' ) . '</a>'
			);
		
			return array_merge( $plugins_links, $action_links );
		}


		/**
		 * Display badge in CSS for get pro in plugins page
		 * 
		 * @since 2.0.0
		 * @version 4.5.0
		 * @access public
		 */
		public function be_pro_badge_styles() {
			ob_start(); ?>

			#get-pro-woo-custom-installments {
				display: inline-block;
				padding: 0.35em 0.6em;
				font-size: 0.8125em;
				font-weight: 600;
				line-height: 1;
				color: #fff;
				text-align: center;
				white-space: nowrap;
				vertical-align: baseline;
				border-radius: 0.25rem;
				background-color: #008aff;
				transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
			}
			
			#get-pro-woo-custom-installments:hover {
				background-color: #0078ed;
			}

			<?php $css = ob_get_clean();
			$css = wp_strip_all_tags( $css );

			printf( __('<style>%s</style>'), $css );
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
 */
Woo_Custom_Installments::run();