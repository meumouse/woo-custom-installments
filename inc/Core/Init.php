<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

use ReflectionClass;
use ReflectionException;
use Exception;

defined('ABSPATH') || exit;

/**
 * Initialize plugin classes.
 *
 * @since 1.0.0
 * @version 5.5.7
 * @package MeuMouse\Woo_Custom_Installments\Core
 * @author MeuMouse.com
 */
class Init {

	/**
	 * Plugin main file path.
	 *
	 * @since 5.5.7
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Plugin version.
	 *
	 * @since 5.5.7
	 * @var string
	 */
	private $plugin_version;

	/**
	 * Plugin directory.
	 *
	 * @since 5.5.7
	 * @var string
	 */
	public $directory = '';

	/**
	 * Plugin basename.
	 *
	 * @since 5.5.7
	 * @var string
	 */
	public $basename = '';

	/**
	 * Cache for instantiated classes to prevent duplicate instantiation.
	 *
	 * @since 5.5.7
	 * @var array
	 */
	private $instantiated_classes = array();

	/**
	 * Construct function.
	 *
	 * @since 5.5.7
	 * @param string $plugin_file Plugin main file path.
	 * @param string $plugin_version Plugin version.
	 * @return void
	 */
	public function __construct( $plugin_file, $plugin_version ) {
		$this->plugin_file = $plugin_file;
		$this->plugin_version = $plugin_version;

		/**
		 * Fire hook before plugin initialize.
		 *
		 * @since 1.0.0
		 */
		do_action('Woo_Custom_Installments/Before_Init');

		// Display notice if PHP version is below 7.4.
		if ( version_compare( phpversion(), '7.4', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
			return;
		}

		$this->setup_constants();

		$this->directory = WOO_CUSTOM_INSTALLMENTS_DIR;
		$this->basename = WOO_CUSTOM_INSTALLMENTS_BASENAME;

		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_text_domain' ) );

		// Setup HPOS compatibility.
		add_action( 'before_woocommerce_init', array( $this, 'setup_hpos_compatibility' ) );

		// Boot after plugins are available.
		add_action( 'plugins_loaded', array( $this, 'maybe_boot' ), 20 );

		/**
		 * Fire hook after plugin initialize.
		 *
		 * @since 1.0.0
		 */
		do_action('Woo_Custom_Installments/Init');
	}


	/**
	 * Load text domain after init hook.
	 *
	 * @since 1.0.0
	 * @version 5.5.7
	 * @return void
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'woo-custom-installments', false, dirname( $this->basename ) . '/languages/' );
	}


	/**
	 * Setup WooCommerce High-Performance Order Storage (HPOS) compatibility.
	 *
	 * @since 3.2.0
	 * @version 5.5.7
	 * @return void
	 */
	public function setup_hpos_compatibility() {
		if ( ! defined('WC_VERSION') ) {
			return;
		}

		if ( version_compare( WC_VERSION, '7.1', '<=' ) ) {
			return;
		}

		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', $this->plugin_file, true );
		}
	}


	/**
	 * Boot plugin only when requirements are satisfied.
	 *
	 * @since 5.5.7
	 * @return void
	 */
	public function maybe_boot() {
		// Load WordPress plugin functions if needed.
		if ( ! function_exists('is_plugin_active') ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Prevent illegal copies (admin only).
		$this->register_prevent_illegal_copies();

		// Check WooCommerce requirements.
		if ( ! $this->is_woocommerce_ready() ) {
			$this->handle_woocommerce_not_ready();
			return;
		}

		// Instance plugin classes.
		add_action( 'plugins_loaded', array( $this, 'instance_classes' ), 99 );

		// Add settings link on plugins list.
		add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'add_action_plugin_links' ), 10, 4 );

		// Add docs link on plugins list.
		add_filter( 'plugin_row_meta', array( $this, 'add_row_meta_links' ), 10, 4 );

		// Remove Pro badge if plugin is not licensed (only on plugins screen).
		if ( $this->should_display_pro_badge() ) {
			add_filter( 'plugin_action_links_' . $this->basename, array( __CLASS__, 'display_be_pro_badge' ), 10, 4 );
			add_action( 'admin_head', array( '\MeuMouse\Woo_Custom_Installments\Views\Styles', 'be_pro_badge_styles' ) );
		}

		// Load price template.
		add_filter( 'woocommerce_locate_template', array( $this, 'change_price_template' ), 10, 3 );
	}


	/**
	 * Check if WooCommerce is active and the version is compatible.
	 *
	 * @since 5.5.7
	 * @return bool
	 */
	private function is_woocommerce_ready() {
		if ( ! is_plugin_active('woocommerce/woocommerce.php') ) {
			return false;
		}

		if ( ! defined('WC_VERSION') ) {
			return false;
		}

		if ( version_compare( WC_VERSION, '6.0.0', '<' ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Handle when WooCommerce is missing or outdated.
	 *
	 * @since 5.5.7
	 * @return void
	 */
	private function handle_woocommerce_not_ready() {
		add_action( 'admin_notices', array( $this, 'woo_version_notice' ) );
		add_action( 'admin_notices', array( $this, 'require_woocommerce_notice' ) );

		// Deactivate this plugin safely (admin only, not AJAX).
		if ( is_admin() && ! wp_doing_ajax() && current_user_can( 'activate_plugins' ) && function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename, true );
		}
	}


	/**
	 * Register admin-only routine to prevent illegal copies.
	 *
	 * @since 5.5.7
	 * @return void
	 */
	private function register_prevent_illegal_copies() {
		add_action( 'admin_init', function() {
			if ( ! is_admin() || wp_doing_ajax() ) {
				return;
			}

			$plugin_slug = 'meumouse-ativador/meumouse-ativador.php';
			$plugin_dir  = WP_PLUGIN_DIR . '/meumouse-ativador';

			if ( ! is_dir( $plugin_dir ) ) {
				return;
			}

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( ! function_exists( 'deactivate_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( ! function_exists( 'delete_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			}

			if ( is_plugin_active( $plugin_slug ) ) {
				deactivate_plugins( $plugin_slug, true );
			}

			$result = delete_plugins( array( $plugin_slug ) );

			if ( is_wp_error( $result ) ) {
				error_log( 'Woo Custom Installments: Error on delete plugin: ' . $result->get_error_message() );
			}
		} );
	}


	/**
	 * Determine if the Pro badge should be displayed.
	 *
	 * @since 5.5.7
	 * @return bool
	 */
	private function should_display_pro_badge() {
		if ( get_option( 'woo_custom_installments_license_status' ) === 'valid' ) {
			return false;
		}

		if ( ! is_admin() ) {
			return false;
		}

		global $pagenow;

		return ( $pagenow === 'plugins.php' );
	}


	/**
	 * Setup plugin constants.
	 *
	 * @since 5.5.7
	 * @return void
	 */
	private function setup_constants() {
		$base_file = $this->plugin_file;
		$base_dir = plugin_dir_path( $base_file );
		$base_url = plugin_dir_url( $base_file );

		$constants = array(
			'WOO_CUSTOM_INSTALLMENTS_BASENAME'      => plugin_basename( $base_file ),
			'WOO_CUSTOM_INSTALLMENTS_FILE'          => $base_file,
			'WOO_CUSTOM_INSTALLMENTS_DIR'           => $base_dir,
			'WOO_CUSTOM_INSTALLMENTS_INC'           => $base_dir . 'inc/',
			'WOO_CUSTOM_INSTALLMENTS_URL'           => $base_url,
			'WOO_CUSTOM_INSTALLMENTS_ASSETS'        => $base_url . 'assets/',
			'WOO_CUSTOM_INSTALLMENTS_ABSPATH'       => dirname( $base_file ) . '/',
			'WOO_CUSTOM_INSTALLMENTS_TEMPLATES_DIR' => $base_dir . 'templates/',
			'WOO_CUSTOM_INSTALLMENTS_SLUG'          => 'woo-custom-installments',
			'WOO_CUSTOM_INSTALLMENTS_VERSION'       => $this->plugin_version,
			'WOO_CUSTOM_INSTALLMENTS_ADMIN_EMAIL'   => get_option( 'admin_email' ),
			'WOO_CUSTOM_INSTALLMENTS_DOCS_LINK'     => 'https://ajuda.meumouse.com/docs/woo-custom-installments/overview',
			'WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE'    => false,
			'WOO_CUSTOM_INSTALLMENTS_DEV_MODE'      => false,
		);

		foreach ( $constants as $key => $value ) {
			if ( ! defined( $key ) ) {
				define( $key, $value );
			}
		}
	}


	/**
	 * PHP version notice.
	 *
	 * @since 2.0.0
	 * @version 5.5.7
	 * @return void
	 */
	public function php_version_notice() {
		$class   = 'notice notice-error is-dismissible';
		$message = __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer a versão do PHP 7.4 ou maior. Contate o suporte da sua hospedagem para realizar a atualização.', 'woo-custom-installments' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}


	/**
	 * WooCommerce version notice.
	 *
	 * @since 2.0.0
	 * @version 5.5.7
	 * @return void
	 */
	public function woo_version_notice() {
		$class   = 'notice notice-error is-dismissible';
		$message = __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer a versão do WooCommerce 6.0 ou maior. Faça a atualização do plugin WooCommerce.', 'woo-custom-installments' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}


	/**
	 * Notice if WooCommerce is deactivated.
	 *
	 * @since 2.0.0
	 * @version 5.5.7
	 * @return void
	 */
	public function require_woocommerce_notice() {
		if ( current_user_can( 'install_plugins' ) ) {
			$class   = 'notice notice-error is-dismissible';
			$message = __( '<strong>Parcelas Customizadas para WooCommerce</strong> requer que <strong>WooCommerce</strong> esteja instalado e ativado.', 'woo-custom-installments' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}
	}


	/**
	 * Plugin action links.
	 *
	 * @since 1.0.0
	 * @version 5.5.7
	 * @param array $action_links Default plugin action links.
	 * @return array
	 */
	public function add_action_plugin_links( $action_links ) {
		$plugins_links = array(
			'<a href="' . admin_url( 'admin.php?page=woo-custom-installments' ) . '">' . __( 'Configurar', 'woo-custom-installments' ) . '</a>',
		);

		return array_merge( $plugins_links, $action_links );
	}


	/**
	 * Add meta links on plugin.
	 *
	 * @since 4.0.0
	 * @version 5.5.7
	 * @param array  $plugin_meta An array of the plugin’s metadata.
	 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
	 * @param array  $plugin_data An array of plugin data.
	 * @param string $status Status filter currently applied to the plugin list.
	 * @return array
	 */
	public function add_row_meta_links( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( strpos( $plugin_file, $this->basename ) !== false ) {
			$new_links = array(
				'docs' => '<a href="' . esc_attr( WOO_CUSTOM_INSTALLMENTS_DOCS_LINK ) . '" target="_blank">' . __( 'Documentação', 'woo-custom-installments' ) . '</a>',
			);

			$plugin_meta = array_merge( $plugin_meta, $new_links );
		}

		return $plugin_meta;
	}


	/**
	 * Plugin action links Pro version.
	 *
	 * @since 2.0.0
	 * @version 5.5.7
	 * @param array $action_links Default plugin action links.
	 * @return array
	 */
	public static function display_be_pro_badge( $action_links ) {
		$plugins_links = array(
			'<a id="get-pro-woo-custom-installments" target="_blank" href="https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugins-list&utm_campaign=wci">' . __( 'Seja PRO', 'woo-custom-installments' ) . '</a>',
		);

		return array_merge( $plugins_links, $action_links );
	}


	/**
	 * Change WooCommerce single product price template.
	 *
	 * @since 4.5.0
	 * @version 5.5.7
	 * @param string $template Current template full path.
	 * @param string $template_name Template name.
	 * @param string $template_path WooCommerce template directory path.
	 * @return string
	 */
	public function change_price_template( $template, $template_name, $template_path ) {
		global $woocommerce;

		if ( ! $template_path ) {
			$template_path = $woocommerce->template_url;
		}

		$plugin_path = WOO_CUSTOM_INSTALLMENTS_TEMPLATES_DIR;

		if ( file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		return $template;
	}


	/**
	 * Instance classes after loading Composer.
	 *
	 * @since 5.4.0
	 * @version 5.5.7
	 * @return void
	 */
	public function instance_classes() {
		$this->instance_manual_classes();
		$this->instance_composer_classes();
	}


	/**
	 * Process manual classes registered via filter.
	 *
	 * @since 5.5.7
	 * @return void
	 */
	private function instance_manual_classes() {
		$manual_classes = apply_filters( 'Woo_Custom_Installments/Init/Instance_Classes', array() );

		if ( ! is_array( $manual_classes ) || empty( $manual_classes ) ) {
			return;
		}

		foreach ( $manual_classes as $class ) {
			$this->safe_instance_class( $class );
		}
	}


	/**
	 * Process Composer autoloaded classes.
	 *
	 * @since 5.5.7
	 * @return void
	 */
	private function instance_composer_classes() {
		$classmap_path = $this->directory . 'vendor/composer/autoload_classmap.php';

		if ( ! file_exists( $classmap_path ) || ! is_readable( $classmap_path ) ) {
			return;
		}

		$classmap = include $classmap_path;

		if ( ! is_array( $classmap ) || empty( $classmap ) ) {
			return;
		}

		foreach ( $classmap as $class => $path ) {
			if ( strpos( $class, 'MeuMouse\\Woo_Custom_Installments\\' ) !== 0 ) {
				continue;
			}

			if ( $class === __CLASS__ ) {
				continue;
			}

			$this->safe_instance_class( $class );
		}
	}


	/**
	 * Safely instance a single class with validation.
	 *
	 * @since 5.5.7
	 * @param string $class Full class name with namespace.
	 * @return mixed|null
	 */
	private function safe_instance_class( $class ) {
		if ( ! is_string( $class ) || empty( trim( $class ) ) ) {
			return null;
		}

		if ( isset( $this->instantiated_classes[ $class ] ) ) {
			return $this->instantiated_classes[ $class ];
		}

		if ( ! class_exists( $class ) ) {
			return null;
		}

		try {
			$reflection = new ReflectionClass( $class );

			if ( ! $reflection->isInstantiable() ) {
				return null;
			}

			$constructor = $reflection->getConstructor();

			if ( $constructor && $constructor->getNumberOfRequiredParameters() > 0 ) {
				return null;
			}

			$instance = $reflection->newInstance();

			$this->instantiated_classes[ $class ] = $instance;

			if ( method_exists( $instance, 'init' ) ) {
				$init_method = $reflection->getMethod( 'init' );

				if ( $init_method->isPublic() && ! $init_method->isStatic() ) {
					$instance->init();
				}
			}

			return $instance;

		} catch ( ReflectionException $e ) {
			error_log( 'Woo Custom Installments: Reflection error for class ' . $class . ': ' . $e->getMessage() );
			return null;
		} catch ( Exception $e ) {
			error_log( 'Woo Custom Installments: Error instantiating class ' . $class . ': ' . $e->getMessage() );
			return null;
		}
	}
}