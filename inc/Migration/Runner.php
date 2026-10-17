<?php

namespace MeuMouse\Woo_Custom_Installments\Migration;

use MeuMouse\Woo_Custom_Installments\Core\Logger;

use WP_Error;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Run the migration one step at a time
 *
 * One step per request, because the two that matter download a package over
 * the network: doing them all in one call is how a migration dies to a PHP
 * timeout halfway through, with nothing recorded about where it stopped.
 *
 * The order of the steps and the rule that decides what a failure means are
 * both here. Nothing in this class knows about HTTP, screens or notices.
 *
 * @since 6.0.0
 * @package MeuMouse\Woo_Custom_Installments\Migration
 * @author MeuMouse.com
 */
class Runner {

	/**
	 * Error code that means the host cannot run the new plugins at all
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const CODE_REQUIREMENTS = 'requirements';

	/**
	 * Reasons the Pro add-on is not installed that are answers, not failures
	 *
	 * A store that has no licence, or whose licence the service refuses, is not
	 * entitled to the add-on: carrying on with the free plugin is the correct
	 * outcome and retrying would never change it. Everything else — the service
	 * being unreachable, a download that broke — is worth trying again, and
	 * leaves the 5.x plugin running in the meantime.
	 *
	 * @since 6.0.0
	 * @var array
	 */
	const PRO_SETTLED_CODES = array(
		'no_license',
		'pro_requirements',
		'not_granted',
	);


	/**
	 * Run the step the store is on and move to the next one
	 *
	 * @since 6.0.0
	 * @return array | The state after this step
	 */
	public static function advance() {
		$state = State::get();

		if ( State::STATUS_DONE === $state['status'] || State::STATUS_BLOCKED === $state['status'] ) {
			return $state;
		}

		$step = (string) $state['step'];
		$result = self::run( $step );

		if ( is_wp_error( $result ) ) {
			if ( self::CODE_REQUIREMENTS === $result->get_error_code() ) {
				// What blocked the host is more useful than the fact that
				// something did, so the specific codes are what get recorded.
				$code = $result->get_error_data();

				return State::block(
					is_string( $code ) && '' !== $code ? $code : $result->get_error_code(),
					$result->get_error_message()
				);
			}

			return State::fail( $step, $result->get_error_code(), $result->get_error_message() );
		}

		$next = State::next_step( $step );

		if ( '' === $next ) {
			self::log( 'Migration to the 6.x plugins finished.', 'info' );

			/**
			 * Fire when the store has been migrated to the 6.x plugins
			 *
			 * @since 6.0.0
			 * @param array $state | The migration state
			 */
			do_action( 'Woo_Custom_Installments/Migration/Finished', State::get() );

			return State::finish();
		}

		return State::patch( array(
			'status' => State::STATUS_RUNNING,
			'step' => $next,
			'code' => '',
			'message' => '',
		) );
	}


	/**
	 * Run one step
	 *
	 * @since 6.0.0
	 * @param string $step | Step to run
	 * @return true|WP_Error
	 */
	private static function run( $step ) {
		switch ( $step ) {
			case State::STEP_CHECK:
				return self::check();

			case State::STEP_INSTALL_FREE:
				return self::install_free();

			case State::STEP_ACTIVATE_FREE:
				return self::activate_free();

			case State::STEP_INSTALL_PRO:
				return self::install_pro();

			case State::STEP_ACTIVATE_PRO:
				return self::activate_pro();

			case State::STEP_DEACTIVATE_LEGACY:
				return self::deactivate_legacy();
		}

		return new WP_Error(
			'unknown_step',
			esc_html__( 'A migração parou em uma etapa desconhecida. Fale com o suporte da MeuMouse.', 'woo-custom-installments' )
		);
	}


	/**
	 * Read the host, and decide up front whether the Pro add-on is expected
	 *
	 * @since 6.0.0
	 * @return true|WP_Error
	 */
	private static function check() {
		$failures = Requirements::check();

		if ( ! empty( $failures ) ) {
			self::log( 'Migration blocked by host requirements: ' . Requirements::to_code( $failures ) );

			return new WP_Error(
				self::CODE_REQUIREMENTS,
				Requirements::to_message( $failures ),
				Requirements::to_code( $failures )
			);
		}

		if ( ! Package_Source::expects_pro() ) {
			State::patch( array(
				'pro' => State::PRO_SKIPPED,
				'pro_message' => esc_html__( 'Nenhuma chave de licença está salva neste site, então só a versão gratuita será instalada.', 'woo-custom-installments' ),
			) );
		}

		return true;
	}


	/**
	 * Install the free plugin
	 *
	 * @since 6.0.0
	 * @return true|WP_Error
	 */
	private static function install_free() {
		$package = Package_Source::free();

		$installed = Installer::install( $package['url'], Package_Source::FREE_SLUG );

		if ( is_wp_error( $installed ) ) {
			return $installed;
		}

		State::patch( array( 'free_version' => $package['version'] ) );

		return true;
	}


	/**
	 * Activate the free plugin
	 *
	 * @since 6.0.0
	 * @return true|WP_Error
	 */
	private static function activate_free() {
		$activated = Installer::activate( Package_Source::FREE_SLUG );

		if ( is_wp_error( $activated ) ) {
			return $activated;
		}

		// the settings were imported, so the wizard has nothing left to ask
		Installer::suppress_onboarding();

		return true;
	}


	/**
	 * Install the Pro add-on, when this store is entitled to it
	 *
	 * @since 6.0.0
	 * @return true|WP_Error
	 */
	private static function install_pro() {
		if ( State::PRO_PENDING !== State::value('pro') ) {
			return true;
		}

		$package = Package_Source::pro();

		if ( is_wp_error( $package ) ) {
			if ( ! in_array( $package->get_error_code(), self::PRO_SETTLED_CODES, true ) ) {
				return $package;
			}

			// An answer, not a failure: record it and carry on with the free
			// plugin, so the storefront stops waiting on a step that will never
			// succeed.
			self::log( 'The Pro add-on will not be installed: ' . $package->get_error_code(), 'notice' );

			State::patch( array(
				'pro' => State::PRO_SKIPPED,
				'pro_message' => $package->get_error_message(),
			) );

			return true;
		}

		$installed = Installer::install( $package['url'], Package_Source::PRO_SLUG );

		if ( is_wp_error( $installed ) ) {
			return $installed;
		}

		State::patch( array( 'pro_version' => $package['version'] ) );

		return true;
	}


	/**
	 * Activate the Pro add-on
	 *
	 * @since 6.0.0
	 * @return true|WP_Error
	 */
	private static function activate_pro() {
		if ( State::PRO_PENDING !== State::value('pro') ) {
			return true;
		}

		$activated = Installer::activate( Package_Source::PRO_SLUG );

		if ( is_wp_error( $activated ) ) {
			return $activated;
		}

		State::patch( array( 'pro' => State::PRO_DONE ) );

		return true;
	}


	/**
	 * Switch this plugin off, now that its replacement is running
	 *
	 * @since 6.0.0
	 * @return true|WP_Error
	 */
	private static function deactivate_legacy() {
		// Never leave a store with no plugin rendering prices. The free plugin
		// withholds its storefront while this one is active, so this is the
		// moment prices start coming from the new code.
		if ( ! Installer::is_active( Package_Source::FREE_SLUG ) ) {
			return new WP_Error(
				'free_inactive',
				esc_html__( 'A versão nova não está ativa, por isso o plugin antigo não foi desativado. Tente novamente.', 'woo-custom-installments' )
			);
		}

		return Installer::deactivate_legacy();
	}


	/**
	 * Record what the migration did
	 *
	 * @since 6.0.0
	 * @param string $message | Log message
	 * @param string $level | Log level
	 * @return void
	 */
	private static function log( $message, $level = 'error' ) {
		if ( ! class_exists( Logger::class ) || ! function_exists('wc_get_logger') ) {
			return;
		}

		Logger::set_logger_source( 'woo-custom-installments-migration', false );
		Logger::register_log( $message, $level );
	}
}
