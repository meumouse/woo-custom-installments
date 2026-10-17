<?php

namespace MeuMouse\Woo_Custom_Installments\Migration;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * What a host needs before the 6.x plugins may be installed
 *
 * This runs on the host that has not been updated yet, which is the whole
 * point: 5.x asks for PHP 7.4 and the plugins that replace it ask for 8.1, so
 * a store that is behind must be told instead of being handed a fatal error.
 *
 * Nothing here is a preference. Every item is something that would break the
 * store if the migration went ahead anyway.
 *
 * @since 6.0.0
 * @package MeuMouse\Woo_Custom_Installments\Migration
 * @author MeuMouse.com
 */
class Requirements {

	/**
	 * PHP version the 6.x plugins require
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const MIN_PHP = '8.1';

	/**
	 * WordPress version the 6.x plugins require
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const MIN_WP = '6.5';

	/**
	 * WooCommerce version the 6.x plugins require
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const MIN_WC = '8.0';


	/**
	 * Read everything the migration depends on
	 *
	 * @since 6.0.0
	 * @return array | One entry per unmet requirement, empty when the host is ready
	 */
	public static function check() {
		$failures = array();

		if ( version_compare( PHP_VERSION, self::MIN_PHP, '<' ) ) {
			$failures[] = array(
				'code' => 'php',
				'message' => sprintf(
					/* translators: 1: required PHP version, 2: PHP version running on the server */
					esc_html__( 'A nova versão exige PHP %1$s ou superior, e este servidor está com o PHP %2$s. Peça à sua hospedagem para atualizar o PHP e volte aqui.', 'woo-custom-installments' ),
					self::MIN_PHP,
					PHP_VERSION
				),
			);
		}

		if ( version_compare( self::wp_version(), self::MIN_WP, '<' ) ) {
			$failures[] = array(
				'code' => 'wp',
				'message' => sprintf(
					/* translators: 1: required WordPress version, 2: WordPress version of this site */
					esc_html__( 'A nova versão exige o WordPress %1$s ou superior, e este site está com o %2$s. Atualize o WordPress e volte aqui.', 'woo-custom-installments' ),
					self::MIN_WP,
					self::wp_version()
				),
			);
		}

		if ( ! defined('WC_VERSION') || version_compare( WC_VERSION, self::MIN_WC, '<' ) ) {
			$failures[] = array(
				'code' => 'wc',
				'message' => sprintf(
					/* translators: 1: required WooCommerce version, 2: WooCommerce version of this site */
					esc_html__( 'A nova versão exige o WooCommerce %1$s ou superior, e este site está com o %2$s. Atualize o WooCommerce e volte aqui.', 'woo-custom-installments' ),
					self::MIN_WC,
					defined('WC_VERSION') ? WC_VERSION : esc_html__( 'não instalado', 'woo-custom-installments' )
				),
			);
		}

		// A network install has one plugin directory and many option tables, so
		// installing is a network act while importing the settings is a per-site
		// one. Getting that wrong silently leaves some stores without their
		// configuration, so the migration does not attempt it.
		if ( is_multisite() ) {
			$failures[] = array(
				'code' => 'multisite',
				'message' => esc_html__( 'Este é um site em rede (multisite), onde a troca precisa ser feita manualmente, site por site. Fale com o suporte da MeuMouse para receber as instruções.', 'woo-custom-installments' ),
			);
		}

		if ( ! self::can_write_plugins() ) {
			$failures[] = array(
				'code' => 'filesystem',
				'message' => esc_html__( 'O WordPress não consegue gravar na pasta de plugins deste servidor, por isso a instalação automática não é possível. Instale os plugins novos manualmente ou fale com sua hospedagem.', 'woo-custom-installments' ),
			);
		}

		/**
		 * Filter the unmet requirements of the migration
		 *
		 * @since 6.0.0
		 * @param array $failures | One entry per unmet requirement
		 * @return array
		 */
		$failures = apply_filters( 'Woo_Custom_Installments/Migration/Requirements', $failures );

		return is_array( $failures ) ? $failures : array();
	}


	/**
	 * Read what the Pro add-on needs on top of the above
	 *
	 * Kept apart from `check()` because a host that fails this is not blocked:
	 * the free plugin still installs, and the store carries on without the paid
	 * features rather than without prices.
	 *
	 * @since 6.0.0
	 * @return array | One entry per unmet requirement, empty when the host is ready
	 */
	public static function pro_check() {
		$failures = array();

		// Every MDS response is verified with ed25519, and the licensed package
		// is only handed over on a verified response.
		if ( ! function_exists('sodium_crypto_sign_verify_detached') ) {
			$failures[] = array(
				'code' => 'sodium',
				'message' => esc_html__( 'A extensão sodium do PHP não está disponível neste servidor, e sem ela não é possível baixar a versão Pro com segurança. Peça à sua hospedagem para habilitá-la.', 'woo-custom-installments' ),
			);
		}

		return $failures;
	}


	/**
	 * Turn a list of failures into one sentence
	 *
	 * @since 6.0.0
	 * @param array $failures | Failures as returned by `check()`
	 * @return string
	 */
	public static function to_message( $failures ) {
		if ( ! is_array( $failures ) || empty( $failures ) ) {
			return '';
		}

		$messages = array();

		foreach ( $failures as $failure ) {
			if ( isset( $failure['message'] ) ) {
				$messages[] = (string) $failure['message'];
			}
		}

		return implode( ' ', $messages );
	}


	/**
	 * Turn a list of failures into one machine readable code
	 *
	 * @since 6.0.0
	 * @param array $failures | Failures as returned by `check()`
	 * @return string
	 */
	public static function to_code( $failures ) {
		if ( ! is_array( $failures ) || empty( $failures ) ) {
			return '';
		}

		$codes = array();

		foreach ( $failures as $failure ) {
			if ( isset( $failure['code'] ) ) {
				$codes[] = (string) $failure['code'];
			}
		}

		return implode( ',', $codes );
	}


	/**
	 * Whether WordPress can write to the plugins directory on its own
	 *
	 * Only the direct method is accepted. The others need credentials typed
	 * into a form, which a stepped migration driven over REST cannot ask for.
	 *
	 * @since 6.0.0
	 * @return bool
	 */
	public static function can_write_plugins() {
		if ( ! function_exists('get_filesystem_method') ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		return 'direct' === get_filesystem_method( array(), WP_PLUGIN_DIR, true );
	}


	/**
	 * Version of this WordPress install
	 *
	 * @since 6.0.0
	 * @return string
	 */
	private static function wp_version() {
		return (string) get_bloginfo('version');
	}
}
