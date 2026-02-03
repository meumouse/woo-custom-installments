<?php
/**
 * Plugin Name: 				Parcelas Customizadas para WooCommerce
 * Description: 				Extensão que permite exibir o parcelamento, desconto e juros por forma de pagamento para lojas WooCommerce.
 * Plugin URI: 					https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugins_list&utm_campaign=parcelas_customizadas
 * Requires Plugins: 			woocommerce
 * Author: 						MeuMouse.com
 * Author URI: 					https://meumouse.com/?utm_source=wordpress&utm_medium=plugins_list&utm_campaign=parcelas_customizadas
 * Version: 					5.5.8
 * Requires at least: 			6.0
 * WC requires at least: 		6.0.0
 * WC tested up to: 			10.4.3
 * Requires PHP: 				7.4
 * Tested up to:      			6.9
 * Text Domain: 				woo-custom-installments
 * Domain Path: 				/languages
 *
 * @package						Parcelas Customizadas para WooCommerce - MeuMouse.com
 * @author						MeuMouse.com
 * @copyright 					2026 MeuMouse.com
 * @license 					Proprietary - See license.md for details
 */

use MeuMouse\Woo_Custom_Installments\Core\Init;

defined('ABSPATH') || exit;

// Load Composer autoloader if available.
$autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

$plugin_version = '5.5.8';

// Initialize the plugin.
new Init( __FILE__, $plugin_version );