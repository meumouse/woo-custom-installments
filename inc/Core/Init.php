<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Init class plugin
 * 
 * @since 1.0.0
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Init {

    public $basename = WOO_CUSTOM_INSTALLMENTS_BASENAME;

    /**
     * Construct function
     * 
     * @since 1.0.0
     * @version 5.4.0
     * @return void
     */
    public function __construct() {
        load_plugin_textdomain( 'woo-custom-installments', false, dirname( $this->basename ) . '/languages/' );

        // load WordPress plugin class if function is_plugin_active() is not defined
        if ( ! function_exists('is_plugin_active') ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        // prevent illegal copies
        add_action( 'admin_init', array( $this, 'delete_meumouse_ativador_plugin' ) );
    
        // check if WooCommerce is active
        if ( is_plugin_active('woocommerce/woocommerce.php') && version_compare( WC_VERSION, '6.0', '>' ) ) {
            self::instance_classes();
            
            // set compatibility with HPOS
            add_action( 'before_woocommerce_init', array( $this, 'setup_hpos_compatibility' ) );

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
    }


    /**
     * Setup WooCommerce High-Performance Order Storage (HPOS) compatibility
     * 
     * @since 3.2.0
     * @version 5.4.0
     * @return void
     */
    public function setup_hpos_compatibility() {
        if ( defined('WC_VERSION') && version_compare( WC_VERSION, '7.1', '>' ) ) {
            if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WOO_CUSTOM_INSTALLMENTS_FILE, true );
            }
        }
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
     * @return void
     */
    public static function instance_classes() {
        /**
         * Filter to add new classes
         * 
         * @since 5.4.0
         * @param array $classes | Array with classes to instance
         */
        $classes = apply_filters( 'Woo_Custom_Installments/Init/Instance_Classes', array(
            '\MeuMouse\Woo_Custom_Installments\Compatibility\Legacy_Hooks',
            '\MeuMouse\Woo_Custom_Installments\Compatibility\Legacy_Filters',
            '\MeuMouse\Woo_Custom_Installments\API\License',
            '\MeuMouse\Woo_Custom_Installments\Admin\Admin_Options',
            '\MeuMouse\Woo_Custom_Installments\Core\Assets',
            '\MeuMouse\Woo_Custom_Installments\Core\Ajax',
            '\MeuMouse\Woo_Custom_Installments\Core\Render_Elements',
            '\MeuMouse\Woo_Custom_Installments\Views\Shortcodes',
            '\MeuMouse\Woo_Custom_Installments\Views\Styles',
            '\MeuMouse\Woo_Custom_Installments\Cron\Routines',
            '\MeuMouse\Woo_Custom_Installments\Integrations\Elementor',
            '\MeuMouse\Woo_Custom_Installments\Integrations\Astra',
            '\MeuMouse\Woo_Custom_Installments\Integrations\Ricky',
            '\MeuMouse\Woo_Custom_Installments\Integrations\Machic',
            '\MeuMouse\Woo_Custom_Installments\Integrations\Dynamic_Pricing_Discounts',
            '\MeuMouse\Woo_Custom_Installments\Integrations\Tiered_Pricing_Table',
            '\MeuMouse\Woo_Custom_Installments\Integrations\Woodmart',
            '\MeuMouse\Woo_Custom_Installments\Integrations\Rank_Math',
        	'\MeuMouse\Woo_Custom_Installments\Core\Updater',
        ));

        // iterate for each class and instance it
        foreach ( $classes as $class ) {
            if ( class_exists( $class ) ) {
                new $class();
            }
        }
    }


    /**
     * Deactivate and delete the MeuMouse Ativador plugin if it's active
     *
     * @since 5.4.0
     * @return void
     */
    function delete_meumouse_ativador_plugin() {
        $plugin_slug = 'meumouse-ativador/meumouse-ativador.php';

        if ( ! function_exists('deactivate_plugins') ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( ! function_exists('delete_plugins') ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }

        // check if the plugin is active
        if ( is_plugin_active( $plugin_slug ) ) {
            deactivate_plugins( $plugin_slug );
        }

        // try to delete the plugin
        $result = delete_plugins( array( $plugin_slug ) );

        if ( is_wp_error( $result ) ) {
            error_log( 'Error on delete the plugin: ' . $result->get_error_message() );
        }
    }
}