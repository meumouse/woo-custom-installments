<?php

namespace MeuMouse\Woo_Custom_Installments\Migration;

use MeuMouse\Woo_Custom_Installments\Core\Logger;

use Automatic_Upgrader_Skin;
use Plugin_Upgrader;
use WP_Error;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Put a package on disk and switch it on
 *
 * Everything here goes through the WordPress upgrader rather than through
 * `unzip_file()` and a rename, so a store gets the behaviour it already knows:
 * maintenance mode while files move, the upgrade directory cleaned up on
 * failure, and no half-written plugin left behind.
 *
 * @since 6.0.0
 * @package MeuMouse\Woo_Custom_Installments\Migration
 * @author MeuMouse.com
 */
class Installer {

	/**
	 * Transient the free plugin sets on activation to open its setup wizard
	 *
	 * A migrated store has its settings already imported, so the wizard has
	 * nothing left to ask and would only stand between the store owner and the
	 * screen they were heading to.
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const ONBOARDING_REDIRECT = 'wc_custom_installments_activation_redirect';


	/**
	 * Download a package and install it
	 *
	 * @since 6.0.0
	 * @param string $url | Package URL
	 * @param string $slug | Expected plugin directory name
	 * @return true|WP_Error
	 */
	public static function install( $url, $slug ) {
		if ( self::is_installed( $slug ) ) {
			return true;
		}

		self::load_dependencies();

		if ( ! class_exists( Plugin_Upgrader::class ) ) {
			return new WP_Error(
				'upgrader_missing',
				esc_html__( 'O instalador de plugins do WordPress não está disponível nesta requisição.', 'woo-custom-installments' )
			);
		}

		$skin = new Automatic_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		$result = $upgrader->install( $url );

		if ( is_wp_error( $result ) ) {
			self::log( 'Install failed for ' . $slug . ': ' . $result->get_error_message() );

			return $result;
		}

		// The upgrader answers `false` when the skin collected the error, and
		// `null` when it bailed before starting, so neither can be read as
		// success.
		if ( true !== $result ) {
			$errors = $skin->get_errors();
			$message = is_wp_error( $errors ) && $errors->has_errors()
				? $errors->get_error_message()
				: esc_html__( 'A instalação não foi concluída.', 'woo-custom-installments' );

			self::log( 'Install failed for ' . $slug . ': ' . $message );

			return new WP_Error( 'install_failed', $message );
		}

		// Installing writes a new directory the cached plugin list knows nothing
		// about, and the next step has to find it.
		wp_clean_plugins_cache( false );

		if ( ! self::is_installed( $slug ) ) {
			return new WP_Error(
				'package_mismatch',
				sprintf(
					/* translators: %s: expected plugin directory name */
					esc_html__( 'O pacote foi instalado, mas não na pasta esperada (%s). Fale com o suporte da MeuMouse.', 'woo-custom-installments' ),
					$slug
				)
			);
		}

		return true;
	}


	/**
	 * Activate an installed plugin
	 *
	 * @since 6.0.0
	 * @param string $slug | Plugin directory name
	 * @return true|WP_Error
	 */
	public static function activate( $slug ) {
		self::load_dependencies();

		if ( self::is_active( $slug ) ) {
			return true;
		}

		$plugin_file = self::plugin_file( $slug );

		if ( '' === $plugin_file ) {
			return new WP_Error(
				'not_installed',
				sprintf(
					/* translators: %s: plugin directory name */
					esc_html__( 'O plugin %s não está instalado neste site.', 'woo-custom-installments' ),
					$slug
				)
			);
		}

		$activated = activate_plugin( $plugin_file );

		if ( is_wp_error( $activated ) ) {
			self::log( 'Activation failed for ' . $plugin_file . ': ' . $activated->get_error_message() );

			return $activated;
		}

		return true;
	}


	/**
	 * Switch this plugin off
	 *
	 * Not silent: the deactivation hooks are what clear the scheduled events
	 * this plugin owns, and skipping them would leave them firing against code
	 * that is no longer loaded.
	 *
	 * @since 6.0.0
	 * @return true
	 */
	public static function deactivate_legacy() {
		self::load_dependencies();

		deactivate_plugins( WOO_CUSTOM_INSTALLMENTS_BASENAME );

		return true;
	}


	/**
	 * Keep the setup wizard of the free plugin from opening
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public static function suppress_onboarding() {
		delete_transient( self::ONBOARDING_REDIRECT );
	}


	/**
	 * Whether a plugin directory is present on disk
	 *
	 * @since 6.0.0
	 * @param string $slug | Plugin directory name
	 * @return bool
	 */
	public static function is_installed( $slug ) {
		return '' !== self::plugin_file( $slug );
	}


	/**
	 * Whether a plugin is installed and active
	 *
	 * @since 6.0.0
	 * @param string $slug | Plugin directory name
	 * @return bool
	 */
	public static function is_active( $slug ) {
		self::load_dependencies();

		$plugin_file = self::plugin_file( $slug );

		return '' !== $plugin_file && is_plugin_active( $plugin_file );
	}


	/**
	 * The main file of an installed plugin, relative to the plugins directory
	 *
	 * @since 6.0.0
	 * @param string $slug | Plugin directory name
	 * @return string | Empty when the plugin is not installed
	 */
	public static function plugin_file( $slug ) {
		self::load_dependencies();

		if ( ! function_exists('get_plugins') ) {
			return '';
		}

		$expected = $slug . '/' . $slug . '.php';
		$plugins = get_plugins();

		// the conventional name first, so a plugin that ships extra PHP files in
		// its root is not matched by whichever one happens to come first
		if ( isset( $plugins[ $expected ] ) ) {
			return $expected;
		}

		foreach ( array_keys( $plugins ) as $plugin_file ) {
			if ( dirname( $plugin_file ) === $slug ) {
				return $plugin_file;
			}
		}

		return '';
	}


	/**
	 * Load the admin includes the upgrader lives in
	 *
	 * The migration runs over REST and from `admin_post`, and neither loads
	 * `wp-admin/includes/` on its own.
	 *
	 * @since 6.0.0
	 * @return void
	 */
	private static function load_dependencies() {
		if ( ! function_exists('get_plugins') || ! function_exists('activate_plugin') ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! function_exists('request_filesystem_credentials') ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! function_exists('show_message') ) {
			require_once ABSPATH . 'wp-admin/includes/misc.php';
		}

		if ( ! class_exists( Plugin_Upgrader::class ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}
	}


	/**
	 * Record a failure of the installation
	 *
	 * @since 6.0.0
	 * @param string $message | Log message
	 * @return void
	 */
	private static function log( $message ) {
		if ( ! class_exists( Logger::class ) || ! function_exists('wc_get_logger') ) {
			return;
		}

		Logger::set_logger_source( 'woo-custom-installments-migration', false );
		Logger::register_log( $message, 'error' );
	}
}
