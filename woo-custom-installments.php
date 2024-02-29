<?php

/**
 * Plugin Name: 			Parcelas Customizadas para WooCommerce
 * Description: 			Extensão que permite exibir o parcelamento, desconto e juros por forma de pagamento para lojas WooCommerce.
 * Plugin URI: 				https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/
 * Author: 					MeuMouse.com
 * Author URI: 				https://meumouse.com/
 * Version: 				3.8.0
 * WC requires at least: 	5.0.0
 * WC tested up to: 		8.3.1
 * Requires PHP: 			7.2
 * Tested up to:      		6.4.1
 * Text Domain: 			woo-custom-installments
 * Domain Path: 			/languages
 * License: 				GPL2
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Woo_Custom_Installments' ) ) {
  
/**
 * Main Woo_Custom_Installments Class
 *
 * @class Woo_Custom_Installments
 * @version 3.6.0
 * @since 1.0.0
 * @package MeuMouse.com
 */
class Woo_Custom_Installments {

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
		public static $slug = 'woo-custom-installments';

		/**
		 * The version number
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public static $version = '3.8.0';

		/**
		 * Constructor function.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {
			$this->setup_constants();

			add_action( 'init', array( $this, 'load_plugin_textdomain' ), -1 );
			add_action( 'plugins_loaded', array( $this, 'woo_custom_installments_load_checker' ), 5 );
		}
		

		/**
		 * Check requeriments and load plugin
		 * 
		 * @since 1.0.0
		 * @return void
		 */
		public function woo_custom_installments_load_checker() {
			if ( !function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
		
			// check if WooCommerce is active
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				add_action( 'before_woocommerce_init', array( $this, 'setup_hpos_compatibility' ) );
				add_action( 'plugins_loaded', array( $this, 'setup_includes' ), 10 );
				add_filter( 'plugin_action_links_' . WOO_CUSTOM_INSTALLMENTS_BASENAME, array( $this, 'woo_custom_installments_plugin_links' ), 10, 4 );
				add_filter( 'plugin_action_links_' . WOO_CUSTOM_INSTALLMENTS_BASENAME, array( $this, 'get_pro_woo_custom_installments_link' ), 10, 4 );
				
				$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

				if ( false !== strpos( $url, 'wp-admin/plugins.php' ) ) {
					add_action( 'admin_head', array( $this, 'badge_pro_woo_custom_installments' ) );
				}

				// remove Pro badge if plugin is licensed
				if ( get_option( 'woo_custom_installments_license_status' ) == 'valid' ) {
					remove_filter( 'plugin_action_links_' . WOO_CUSTOM_INSTALLMENTS_BASENAME, array( $this, 'get_pro_woo_custom_installments_link' ), 10, 4 );
					remove_action( 'admin_head', array( $this, 'badge_pro_woo_custom_installments' ) );
				}
			} else {
				deactivate_plugins( 'woo-custom-installments/woo-custom-installments.php' );
				add_action( 'admin_notices', array( $this, 'woo_custom_installments_wc_deactivate_notice' ) );
			}

			// Display notice if PHP version is bottom 7.2
			if ( version_compare( phpversion(), '7.2', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'woo_custom_installments_php_version_notice' ) );
				return;
			}

			// display notice if WooCommerce version is bottom 6.0
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && version_compare( WC_VERSION, '6.0', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'woo_custom_installments_wc_version_notice' ) );
				return;
			}
		}


		/**
		 * Setup WooCommerce High-Performance Order Storage (HPOS) compatibility
		 * 
		 * @since 3.2.0
		 * @return void
		 */
		public function setup_hpos_compatibility() {
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '7.1', '<' ) ) {
				return;
			}

			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
					'custom_order_tables', WOO_CUSTOM_INSTALLMENTS_FILE, true );
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
		public static function run() {
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
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_INC', WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/' );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_ASSETS', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/' );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_FILE', __FILE__ );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_ABSPATH', dirname( WOO_CUSTOM_INSTALLMENTS_FILE ) . '/' );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_SLUG', self::$slug );
			$this->define( 'WOO_CUSTOM_INSTALLMENTS_VERSION', self::$version );
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
			 * 
			 * @since 1.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_INC . 'class-woo-custom-installments-init.php';

			/**
			 * Core functions
			 * 
			 * @since 1.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_INC . 'woo-custom-installments-functions.php';

			/**
			 * Discounts per payment mathod
			 * 
			 * @since 2.0.0
			 */
			if ( get_option( 'woo_custom_installments_license_status' ) == 'valid' && Woo_Custom_Installments_Init::get_setting('enable_all_discount_options') === 'yes' ) {
				include_once WOO_CUSTOM_INSTALLMENTS_INC . 'classes/class-woo-custom-installments-add-discounts.php';
			}

			/**
			 * Interest per payment method
			 * 
			 * @since 2.3.5
			 */
			if ( get_option( 'woo_custom_installments_license_status' ) == 'valid' && Woo_Custom_Installments_Init::get_setting('enable_all_interest_options') === 'yes' ) {
				include_once WOO_CUSTOM_INSTALLMENTS_INC . 'classes/class-woo-custom-installments-add-interest.php';
			}

			/**
			 * Admin options
			 * 
			 * @since 1.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_INC . 'admin/class-woo-custom-installments-admin-options.php';

			/**
			 * Front-end template
			 * 
			 * @since 1.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_INC . 'classes/class-woo-custom-installments-frontend-template.php';

			/**
			 * Calculate values
			 * 
			 * @since 1.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_INC . 'classes/class-woo-custom-installments-calculate-values.php';

			/**
			 * Custom design
			 * 
			 * @since 2.1.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_INC . 'classes/class-woo-custom-installments-custom-design.php';

			/**
			 * Load API settings
			 * 
			 * @since 2.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_INC . 'classes/class-woo-custom-installments-api.php';

			/**
			 * Update checker
			 * 
			 * @since 3.0.0
			 */
			include_once WOO_CUSTOM_INSTALLMENTS_INC . 'classes/class-woo-custom-installments-updater.php';
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
		 * Plugin action links
		 * 
		 * @since 1.0.0
		 * @return array
		 */
		public function woo_custom_installments_plugin_links( $action_links ) {
			$plugins_links = array(
				'<a href="' . admin_url( 'admin.php?page=woo-custom-installments' ) . '">'. __( 'Configurar', 'woo-custom-installments' ) .'</a>',
				'<a href="https://meumouse.com/docs-category/parcelas-customizadas-para-woocommerce/" target="_blank">'. __( 'Ajuda', 'woo-custom-installments' ) .'</a>'
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
		 * Display badge in CSS for get pro in plugins page
		 * 
		 * @since 2.0.0
		 * @access public
		 */
		public function badge_pro_woo_custom_installments() {
			echo '<style>
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
			</style>';
		}


		/**
		 * Load the plugin text domain for translation.
		 */
		public static function load_plugin_textdomain() {
			load_plugin_textdomain( 'woo-custom-installments', false, dirname( WOO_CUSTOM_INSTALLMENTS_BASENAME ) . '/languages/' );
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
 * Initialise the plugin
 */
Woo_Custom_Installments::run();