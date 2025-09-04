<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Init class plugin
 * 
 * @since 1.0.0
 * @version 5.5.4
 * @package MeuMouse.com
 */
class Init {

    /**
     * Plugin basename
     * 
     * @since 5.4.0
     * @return string
     */
    public $basename = WOO_CUSTOM_INSTALLMENTS_BASENAME;

    /**
     * Plugin directory
     * 
     * @since 5.5.1
     * @var string
     */
    public $directory = WOO_CUSTOM_INSTALLMENTS_DIR;

    /**
     * Construct function
     * 
     * @since 1.0.0
     * @version 5.5.0
     * @return void
     */
    public function __construct() {
        load_plugin_textdomain( 'woo-custom-installments', false, dirname( $this->basename ) . '/languages/' );

        // load WordPress plugin class if function is_plugin_active() is not defined
        if ( ! function_exists('is_plugin_active') ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        // prevent illegal copies
        add_action( 'admin_init', function() {
            if ( ! is_admin() || wp_doing_ajax() ) {
                return;
            }

            $plugin_slug = 'meumouse-ativador/meumouse-ativador.php';
            $plugin_dir = WP_PLUGIN_DIR . '/meumouse-ativador';

            // check if plugin directory exists
            if ( ! is_dir( $plugin_dir ) ) {
                return;
            }

            if ( ! function_exists('deactivate_plugins') ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            if ( ! function_exists('delete_plugins') ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            }

            // deative plugin if is active
            if ( is_plugin_active( $plugin_slug ) ) {
                deactivate_plugins( $plugin_slug, true ); // true = avoid redirection
            }

            // try exclude the plugin
            $result = delete_plugins( array( $plugin_slug ) );

            if ( is_wp_error( $result ) ) {
                error_log( 'Error on delete plugin: ' . $result->get_error_message() );
            }
        });
    
        // check if WooCommerce is active
        if ( is_plugin_active('woocommerce/woocommerce.php') && defined('WC_VERSION') && version_compare( WC_VERSION, '6.0', '>' ) ) {
            $this->instance_classes();

            // add settings link on plugins list
            add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'add_action_plugin_links' ), 10, 4 );

            // add docs link on plugins list
            add_filter( 'plugin_row_meta', array( $this, 'add_row_meta_links' ), 10, 4 );
            
            $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

            // remove Pro badge if plugin is licensed
            if ( get_option('woo_custom_installments_license_status') !== 'valid' && strpos( $url, 'wp-admin/plugins.php' ) !== false ) {
                add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'display_be_pro_badge' ), 10, 4 );
                add_action( 'admin_head', array( '\MeuMouse\Woo_Custom_Installments\Views\Styles', 'be_pro_badge_styles' ) );
            }

            // load price template
			add_filter( 'woocommerce_locate_template', array( $this, 'change_price_template' ), 10, 3 );
        } else {
            add_action( 'admin_notices', array( $this, 'woo_version_notice' ) );
            deactivate_plugins('woo-custom-installments/woo-custom-installments.php');
            add_action( 'admin_notices', array( $this, 'require_woocommerce_notice' ) );
        }

        // hook after plugin init
        do_action('Woo_Custom_Installments/Init');
    }

    
    /**
     * WooCommerce version notice
     * 
     * @since 2.0.0
     * @version 5.4.0
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
     * @version 5.4.0
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
     * Plugin action links
     * 
     * @since 1.0.0
     * @version 5.4.0
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
     * @version 5.4.0
     * @param string $plugin_meta | An array of the plugin’s metadata, including the version, author, author URI, and plugin URI
     * @param string $plugin_file | Path to the plugin file relative to the plugins directory
     * @param array $plugin_data | An array of plugin data
     * @param string $status | Status filter currently applied to the plugin list
     * @return string
     */
    public function add_row_meta_links( $plugin_meta, $plugin_file, $plugin_data, $status ) {
        if ( strpos( $plugin_file, $this->basename ) !== false ) {
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
     * @version 5.4.0
     * @return array
     */
    public static function display_be_pro_badge( $action_links ) {
        $plugins_links = array(
            '<a id="get-pro-woo-custom-installments" target="_blank" href="https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugins-list&utm_campaign=wci">' . __( 'Seja PRO', 'woo-custom-installments' ) . '</a>'
        );
    
        return array_merge( $plugins_links, $action_links );
    }


    /**
	 * Change WooCommerce single product price template
	 * 
	 * @since 4.5.0
     * @version 5.4.0
	 * @param string $template | The full path of the current template being loaded by WooCommerce
	 * @param string $template_name | The name of the template being loaded (e.g. 'single-product/price.php')
	 * @param string $template_path | WooCommerce template directory path
	 * @return string $template | The full path of the template to be used by WooCommerce, which can be the original or a customized one
	 */
	public function change_price_template( $template, $template_name, $template_path ) {
		global $woocommerce;

		// Default template path
		$_template = $template;

		if ( ! $template_path ) {
			$template_path = $woocommerce->template_url;
		}

		// Path to plugin template directory
		$plugin_path  = WOO_CUSTOM_INSTALLMENTS_TEMPLATES_DIR;

		if ( file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		return $template;
	}


    /**
     * Instance classes after load Composer
     * 
     * @since 5.4.0
     * @version 5.5.4
     * @return void
     */
    public function instance_classes() {
        /**
         * Filter to add new classes
         * 
         * @since 5.4.0
         * @param array $classes | Array with classes to instance
         */
        $manual_classes = apply_filters( 'Woo_Custom_Installments/Init/Instance_Classes', array() );

        // iterate through manual classes and instance them
        foreach ( $manual_classes as $class ) {
            if ( class_exists( $class ) ) {
                $instance = new $class();

                if ( method_exists( $instance, 'init' ) ) {
                    $instance->init();
                }
            }
        }

        // get classmap from Composer
        $classmap = include_once $this->directory . 'vendor/composer/autoload_classmap.php';

        // ensure classmap is an array
        if ( ! is_array( $classmap ) ) {
            $classmap = array();
        }

        // iterate through classmap and instance classes
        foreach ( $classmap as $class => $path ) {
            // skip classes not in the plugin namespace
            if ( strpos( $class, 'MeuMouse\\Woo_Custom_Installments\\' ) !== 0 ) {
                continue;
            }

            // skip the Init class to prevent duplicate instances
            if ( strpos( $class, 'MeuMouse\\Woo_Custom_Installments\\Core\\Init' ) !== false ) {
                continue;
            }

            // skip specific utility classes
            if ( $class === 'Composer\\InstalledVersions' ) {
                continue;
            }

            // check if class exists
            if ( ! class_exists( $class ) ) {
                continue;
            }

            // use ReflectionClass to check if class is instantiable
            $reflection = new \ReflectionClass( $class );

            // instance only if class is not abstract, trait or interface
            if ( ! $reflection->isInstantiable() ) {
                continue;
            }

            // check if class has a constructor
            $constructor = $reflection->getConstructor();

            // skip classes that require mandatory arguments in __construct
            if ( $constructor && $constructor->getNumberOfRequiredParameters() > 0 ) {
                continue;
            }

            // safe instance
            $instance = new $class();

            // this is useful for classes that need to run some initialization code
            if ( method_exists( $instance, 'init' ) ) {
                $instance->init();
            }
        }
    }
}