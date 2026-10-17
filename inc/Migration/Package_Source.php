<?php

namespace MeuMouse\Woo_Custom_Installments\Migration;

use MeuMouse\Woo_Custom_Installments\Core\Logger;

use MeuMouse\MDS\SDK\Api\ApiException;
use MeuMouse\MDS\SDK\Api\Client;
use MeuMouse\MDS\SDK\Config\Product;
use MeuMouse\MDS\SDK\Security\SignatureVerifier;
use MeuMouse\MDS\SDK\Support\Environment;
use MeuMouse\MDS\SDK\Support\Logger as SDK_Logger;

use WP_Error;
use Throwable;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Where each 6.x package is downloaded from
 *
 * The two plugins are distributed differently, and that difference is the whole
 * reason this class exists.
 *
 * The free plugin is a `free` product on the Modular Distribution Service, so
 * its package URL is public, permanent and the same for every store. The Pro
 * add-on is `licensed`, so its URL only exists for a store that is entitled to
 * it: the licensing service answers the update check with a signed envelope
 * carrying a package URL whose grant is re-judged when it is redeemed.
 *
 * Both are asked for by version rather than hard-coded, because this release
 * will be migrating stores for months after it ships.
 *
 * @since 6.0.0
 * @package MeuMouse\Woo_Custom_Installments\Migration
 * @author MeuMouse.com
 */
class Package_Source {

	/**
	 * Slug of the free plugin, as registered in MDS and on disk
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const FREE_SLUG = 'wc-custom-installments';

	/**
	 * Slug of the Pro add-on, as registered in MDS and on disk
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const PRO_SLUG = 'wc-custom-installments-pro';

	/**
	 * Version of the free plugin this release was built against
	 *
	 * Used when the server cannot be asked which version is current. It is a
	 * floor, never a ceiling: a newer answer from the server always wins.
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const FREE_FALLBACK_VERSION = '6.0.0';

	/**
	 * API path of the signed update check
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const UPDATE_CHECK_PATH = '/v2/update-check';

	/**
	 * API path of the public latest-version lookup
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const LATEST_VERSION_PATH = '/v2/update-check/';


	/**
	 * Resolve the package of the free plugin
	 *
	 * Never fails: a store with no route to the version lookup still gets the
	 * version this release knows about.
	 *
	 * @since 6.0.0
	 * @return array | Package with `version` and `url`
	 */
	public static function free() {
		$version = self::latest_free_version();

		return array(
			'version' => $version,
			'url' => self::download_url( self::FREE_SLUG, $version ),
		);
	}


	/**
	 * Resolve the package of the Pro add-on for this store's licence
	 *
	 * @since 6.0.0
	 * @return array|WP_Error | Package with `version` and `url`, or why there is none
	 */
	public static function pro() {
		$license_key = self::license_key();

		if ( '' === $license_key ) {
			return new WP_Error(
				'no_license',
				esc_html__( 'Nenhuma chave de licença está salva neste site, então a versão Pro não será instalada.', 'woo-custom-installments' )
			);
		}

		$failures = Requirements::pro_check();

		if ( ! empty( $failures ) ) {
			return new WP_Error( 'pro_requirements', Requirements::to_message( $failures ) );
		}

		if ( ! class_exists( Product::class ) || ! class_exists( Client::class ) ) {
			return new WP_Error(
				'sdk_missing',
				esc_html__( 'O componente que fala com o servidor de licenças não está disponível nesta instalação.', 'woo-custom-installments' )
			);
		}

		try {
			$product = self::pro_product();

			$client = new Client(
				$product,
				new SignatureVerifier( $product->public_key() ),
				new SDK_Logger('wci-migration')
			);

			$body = array_merge( Environment::request_meta( $product ), array(
				'license_key' => $license_key,
				'product_slug' => self::PRO_SLUG,
				'current_version' => $product->current_version(),
			) );

			$response = $client->post( self::UPDATE_CHECK_PATH, $body );
		} catch ( ApiException $exception ) {
			self::log( 'The licensing service could not be asked for the Pro package: ' . $exception->getMessage() );

			// A transport failure is not a refusal, and the two must not lead to
			// the same place: one is worth retrying, the other is an answer.
			$code = $exception->is_transport() ? 'transport' : 'api';

			return new WP_Error( $code, self::service_message( $exception->is_transport() ) );
		} catch ( Throwable $exception ) {
			self::log( 'Unexpected failure while resolving the Pro package: ' . $exception->getMessage() );

			return new WP_Error( 'unexpected', self::service_message( false ) );
		}

		$release = (array) $response->data( 'release', array() );

		if ( empty( $release['package_url'] ) ) {
			return new WP_Error( 'not_granted', self::verdict_message( $response ) );
		}

		return array(
			'version' => isset( $release['version'] ) ? (string) $release['version'] : '',
			'url' => (string) $release['package_url'],
		);
	}


	/**
	 * The licence key this store has been using on 5.x
	 *
	 * @since 6.0.0
	 * @return string
	 */
	public static function license_key() {
		$license_key = get_option('woo_custom_installments_license_key');

		return is_string( $license_key ) ? trim( $license_key ) : '';
	}


	/**
	 * Whether this store looks entitled to the Pro add-on
	 *
	 * Read from what 5.x stored, which is the only thing available before the
	 * licensing service is asked. The server has the final word — a store that
	 * passes this check can still be refused, and that refusal is what decides.
	 *
	 * @since 6.0.0
	 * @return bool
	 */
	public static function expects_pro() {
		return '' !== self::license_key();
	}


	/**
	 * Base URL of the distribution service
	 *
	 * @since 6.0.0
	 * @return string
	 */
	private static function api_url() {
		return untrailingslashit( WOO_CUSTOM_INSTALLMENTS_MDS_API_URL );
	}


	/**
	 * Build the stable package URL of a product version
	 *
	 * @since 6.0.0
	 * @param string $slug | Product slug
	 * @param string $version | Version to download
	 * @return string
	 */
	private static function download_url( $slug, $version ) {
		return sprintf( '%s/v2/download/%s/%s.zip', self::api_url(), $slug, $version );
	}


	/**
	 * Ask the service which version of the free plugin is current
	 *
	 * The route is public — no licence and no API key — and the answer is not
	 * signed, which is acceptable here because it carries no entitlement: the
	 * worst a tampered answer achieves is a version that does not exist, and
	 * the install step then fails loudly.
	 *
	 * @since 6.0.0
	 * @return string
	 */
	private static function latest_free_version() {
		$response = wp_remote_get( self::api_url() . self::LATEST_VERSION_PATH . self::FREE_SLUG, array(
			'timeout' => 15,
			'user-agent' => 'wc-custom-installments-migration/' . WOO_CUSTOM_INSTALLMENTS_VERSION,
		) );

		if ( is_wp_error( $response ) ) {
			return self::FREE_FALLBACK_VERSION;
		}

		$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) || empty( $body['success'] ) || empty( $body['data']['latest_version'] ) ) {
			return self::FREE_FALLBACK_VERSION;
		}

		$latest = (string) $body['data']['latest_version'];

		// never install something older than the release this one was built for
		return version_compare( $latest, self::FREE_FALLBACK_VERSION, '>' ) ? $latest : self::FREE_FALLBACK_VERSION;
	}


	/**
	 * Product context for the Pro add-on, used only to make this one request
	 *
	 * The add-on is not installed yet, so there is nothing to register with the
	 * SDK: this is a throwaway value object. `current_version` is `0.0.0` on
	 * purpose, so the server answers with the release rather than with "you are
	 * up to date", and the public key is this plugin's because MDS signs every
	 * response with the same key.
	 *
	 * @since 6.0.0
	 * @return Product
	 */
	private static function pro_product() {
		return new Product( array(
			'product_slug' => self::PRO_SLUG,
			'type' => 'plugin',
			'file' => self::PRO_SLUG . '/' . self::PRO_SLUG . '.php',
			'current_version' => '0.0.0',
			'api_base_url' => self::api_url(),
			'api_key' => WOO_CUSTOM_INSTALLMENTS_MDS_API_KEY,
			'public_key' => WOO_CUSTOM_INSTALLMENTS_MDS_PUBLIC_KEY,
			'item_name' => 'Parcelas Customizadas para WooCommerce Pro',
			'text_domain' => self::PRO_SLUG,
			'mode' => 'updates_only',
		) );
	}


	/**
	 * Explain, in the store owner's terms, why the service handed over nothing
	 *
	 * @since 6.0.0
	 * @param \MeuMouse\MDS\SDK\Api\ApiResponse $response | The signed answer
	 * @return string
	 */
	private static function verdict_message( $response ) {
		$license = (string) $response->data('license_status', '');

		if ( 'expired' === $license ) {
			return esc_html__( 'Sua licença expirou, por isso a versão Pro não pode ser baixada. Renove a licença e tente novamente.', 'woo-custom-installments' );
		}

		if ( in_array( $license, array( 'cancelled', 'refunded', 'blocked', 'inactive' ), true ) ) {
			return esc_html__( 'Sua licença não está ativa, por isso a versão Pro não pode ser baixada. Fale com o suporte da MeuMouse.', 'woo-custom-installments' );
		}

		if ( 'active' === $license ) {
			return esc_html__( 'Sua licença está ativa, mas não libera a versão Pro para este domínio. Ative o domínio na sua conta ou fale com o suporte da MeuMouse.', 'woo-custom-installments' );
		}

		return esc_html__( 'O servidor de licenças não liberou o download da versão Pro para este site. Confira sua licença na sua conta MeuMouse.', 'woo-custom-installments' );
	}


	/**
	 * What the store owner reads when the service itself was the problem
	 *
	 * @since 6.0.0
	 * @param bool $is_transport | Whether the request never reached the service
	 * @return string
	 */
	private static function service_message( $is_transport ) {
		if ( $is_transport ) {
			return esc_html__( 'Não foi possível acessar o servidor de licenças. Verifique a conexão do servidor e tente novamente.', 'woo-custom-installments' );
		}

		return esc_html__( 'O servidor de licenças respondeu com um erro ao liberar a versão Pro. Tente novamente em alguns minutos.', 'woo-custom-installments' );
	}


	/**
	 * Record a failure of the package resolution
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
