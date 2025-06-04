<?php

namespace MeuMouse\Woo_Custom_Installments\Admin;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Admin plugin actions
 *
 * @since 2.0.0
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Admin_Options {

	/**
	 * Construct function
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @return void
	 */
	public function __construct() {
		// add submenu page
		add_action( 'admin_menu', array( $this, 'add_woo_submenu' ) );

		// set default options
		add_action( 'admin_init', array( $this, 'set_default_options' ) );

		// render settings tabs
        add_action( 'Woo_Custom_Installments/Admin/Settings_Nav_Tabs', array( $this, 'render_settings_tabs' ) );
	}

	
	/**
     * Checks if the option exists and returns the indicated array item
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @param string $key | Array key
     * @return mixed | string or false
     */
    public static function get_setting( $key ) {
        $options = get_option('woo-custom-installments-setting', array());

        // check if array key exists and return key
        if ( isset( $options[$key] ) ) {
            return $options[$key];
        }

        return false;
    }


	/**
	 * Function for create submenu in WooCommerce
	 * 
	 * @since 2.0.0
	 * @version 5.0.0
	 * @return void
	 */
	public function add_woo_submenu() {
		add_submenu_page(
			'woocommerce', // parent page slug
			esc_html__( 'Parcelas Customizadas para WooCommerce', 'woo-custom-installments'), // page title
			esc_html__( 'Parcelas Customizadas', 'woo-custom-installments'), // submenu title
			'manage_woocommerce', // user capabilities
			'woo-custom-installments', // page slug
			array( $this, 'render_settings_page' ) // public function for print content page
		);
	}


	/**
     * Gets the items from the array and inserts them into the option if it is empty,
     * or adds new items with default value to the option
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @return void
     */
    public function set_default_options() {
        $get_options = Default_Options::set_default_data_options();
        $default_options = get_option('woo-custom-installments-setting', array());

        if ( empty( $default_options ) ) {
            update_option( 'woo-custom-installments-setting', $get_options );
        } else {
            foreach ( $get_options as $key => $value ) {
                if ( ! isset( $default_options[$key] ) ) {
                    $default_options[$key] = $value;
                }
            }

            update_option( 'woo-custom-installments-setting', $default_options );
        }
    }


	/**
     * Render settings nav tabs
     *
     * @since 5.4.0
	 * @return void
     */
    public function render_settings_tabs() {
        $tabs = self::register_settings_tabs();

        foreach ( $tabs as $tab ) {
            printf( '<a href="#%1$s" class="nav-tab">%2$s %3$s</a>', esc_attr( $tab['id'] ), $tab['icon'], $tab['label'] );
        }
    }


	/**
	 * Register settings tabs
	 * 
	 * @since 5.4.0
	 * @return array
	 */
	public static function register_settings_tabs() {
		return apply_filters( 'Woo_Custom_Installments/Admin/Register_Settings_Tabs', array(
            'general' => array(
                'id' => 'general',
                'label' => esc_html__('Geral', 'woo-custom-installments'),
                'icon' => '<svg class="woo-custom-installments-tab-icon"><path d="M7.5 14.5c-1.58 0-2.903 1.06-3.337 2.5H2v2h2.163c.434 1.44 1.757 2.5 3.337 2.5s2.903-1.06 3.337-2.5H22v-2H10.837c-.434-1.44-1.757-2.5-3.337-2.5zm0 5c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5S9 17.173 9 18s-.673 1.5-1.5 1.5zm9-11c-1.58 0-2.903 1.06-3.337 2.5H2v2h11.163c.434 1.44 1.757 2.5 3.337 2.5s2.903-1.06 3.337-2.5H22v-2h-2.163c-.434-1.44-1.757-2.5-3.337-2.5zm0 5c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5 1.5.673 1.5 1.5-.673 1.5-1.5 1.5z"></path><path d="M12.837 5C12.403 3.56 11.08 2.5 9.5 2.5S6.597 3.56 6.163 5H2v2h4.163C6.597 8.44 7.92 9.5 9.5 9.5s2.903-1.06 3.337-2.5h9.288V5h-9.288zM9.5 7.5C8.673 7.5 8 6.827 8 6s.673-1.5 1.5-1.5S11 5.173 11 6s-.673 1.5-1.5 1.5z"></path></svg>',
                'file' => WOO_CUSTOM_INSTALLMENTS_INC . 'Views/Settings/Tabs/General.php',
            ),
            'texts' => array(
                'id' => 'texts',
                'label' => esc_html__('Textos', 'woo-custom-installments'),
                'icon' => '<svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="M5 8h2V6h3.252L7.68 18H5v2h8v-2h-2.252L13.32 6H17v2h2V4H5z"></path></svg>',
                'file' => WOO_CUSTOM_INSTALLMENTS_INC . 'Views/Settings/Tabs/Texts.php',
            ),
            'discounts' => array(
                'id' => 'discounts',
                'label' => esc_html__('Descontos', 'woo-custom-installments'),
                'icon' => '<svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="M13.707 3.293A.996.996 0 0 0 13 3H4a1 1 0 0 0-1 1v9c0 .266.105.52.293.707l8 8a.997.997 0 0 0 1.414 0l9-9a.999.999 0 0 0 0-1.414l-8-8zM12 19.586l-7-7V5h7.586l7 7L12 19.586z"></path><circle cx="8.496" cy="8.495" r="1.505"></circle></svg>',
                'file' => WOO_CUSTOM_INSTALLMENTS_INC . 'Views/Settings/Tabs/Discounts.php',
            ),
			'interests' => array(
                'id' => 'interests',
                'label' => esc_html__('Juros', 'woo-custom-installments'),
                'icon' => '<svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="m10 10.414 4 4 5.707-5.707L22 11V5h-6l2.293 2.293L14 11.586l-4-4-7.707 7.707 1.414 1.414z"></path></svg>',
                'file' => WOO_CUSTOM_INSTALLMENTS_INC . 'Views/Settings/Tabs/Interests.php',
            ),
			'payment_methods' => array(
                'id' => 'payment_methods',
                'label' => esc_html__('Formas de pagamento', 'woo-custom-installments'),
                'icon' => '<svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"></path><path d="M12 11c-2 0-2-.63-2-1s.7-1 2-1 1.39.64 1.4 1h2A3 3 0 0 0 13 7.12V6h-2v1.09C9 7.42 8 8.71 8 10c0 1.12.52 3 4 3 2 0 2 .68 2 1s-.62 1-2 1c-1.84 0-2-.86-2-1H8c0 .92.66 2.55 3 2.92V18h2v-1.08c2-.34 3-1.63 3-2.92 0-1.12-.52-3-4-3z"></path></svg>',
                'file' => WOO_CUSTOM_INSTALLMENTS_INC . 'Views/Settings/Tabs/Payment_Methods.php',
            ),
			'styles' => array(
                'id' => 'styles',
                'label' => esc_html__('Estilos', 'woo-custom-installments'),
                'icon' => '<svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="M13.707 3.293A.996.996 0 0 0 13 3H4a1 1 0 0 0-1 1v9c0 .266.105.52.293.707l8 8a.997.997 0 0 0 1.414 0l9-9a.999.999 0 0 0 0-1.414l-8-8zM12 19.586l-7-7V5h7.586l7 7L12 19.586z"></path><circle cx="8.496" cy="8.495" r="1.505"></circle></svg>',
                'file' => WOO_CUSTOM_INSTALLMENTS_INC . 'Views/Settings/Tabs/Styles.php',
            ),
            'about' => array(
                'id' => 'about',
                'label' => esc_html__('Sobre', 'woo-custom-installments'),
                'icon' => '<svg class="woo-custom-installments-tab-icon"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>',
                'file' => WOO_CUSTOM_INSTALLMENTS_INC . 'Views/Settings/Tabs/About.php',
            ),
        ));
	}


	/**
	 * Plugin general setting page and save options
	 * 
	 * @since 2.0.0
	 * @version 4.5.0
	 * @return void
	 */
	public function render_settings_page() {
		include_once WOO_CUSTOM_INSTALLMENTS_INC . 'Views/Settings.php';
	}
}