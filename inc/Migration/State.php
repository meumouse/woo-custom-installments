<?php

namespace MeuMouse\Woo_Custom_Installments\Migration;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Record of how far the migration to the 6.x plugins has got
 *
 * The whole migration is one option, read on every admin request and written
 * once per step. It is deliberately small, and deliberately kept after the
 * migration finishes: a store owner who rolled back to 5.x needs to see what
 * happened, and support needs to know which step refused.
 *
 * @since 6.0.0
 * @package MeuMouse\Woo_Custom_Installments\Migration
 * @author MeuMouse.com
 */
class State {

	/**
	 * Option holding the whole migration state
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const OPTION = 'woo_custom_installments_migration';

	/**
	 * Nothing has been attempted yet
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STATUS_NEW = 'new';

	/**
	 * The host cannot run the new plugins, so nothing will be attempted
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STATUS_BLOCKED = 'blocked';

	/**
	 * Steps are being run
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STATUS_RUNNING = 'running';

	/**
	 * A step failed and can be tried again
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STATUS_FAILED = 'failed';

	/**
	 * Every step ran and this plugin has stood down
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STATUS_DONE = 'done';

	/**
	 * Step that reads the host requirements
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STEP_CHECK = 'check';

	/**
	 * Step that downloads and installs the free plugin
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STEP_INSTALL_FREE = 'install_free';

	/**
	 * Step that activates the free plugin
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STEP_ACTIVATE_FREE = 'activate_free';

	/**
	 * Step that downloads and installs the Pro add-on
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STEP_INSTALL_PRO = 'install_pro';

	/**
	 * Step that activates the Pro add-on
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STEP_ACTIVATE_PRO = 'activate_pro';

	/**
	 * Step that switches this plugin off
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const STEP_DEACTIVATE_LEGACY = 'deactivate_legacy';

	/**
	 * The Pro add-on is expected and has not been dealt with yet
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const PRO_PENDING = 'pending';

	/**
	 * The Pro add-on will not be installed, and the reason is recorded
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const PRO_SKIPPED = 'skipped';

	/**
	 * The Pro add-on is installed and active
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const PRO_DONE = 'done';


	/**
	 * The steps, in the only order they may run in
	 *
	 * Activating the free plugin before the add-on is not a preference: the
	 * add-on declares `Requires Plugins: wc-custom-installments`, and WordPress
	 * 6.5 refuses to activate it while that dependency is inactive.
	 *
	 * @since 6.0.0
	 * @return array
	 */
	public static function steps() {
		return array(
			self::STEP_CHECK,
			self::STEP_INSTALL_FREE,
			self::STEP_ACTIVATE_FREE,
			self::STEP_INSTALL_PRO,
			self::STEP_ACTIVATE_PRO,
			self::STEP_DEACTIVATE_LEGACY,
		);
	}


	/**
	 * The state of a store that has never run the migration
	 *
	 * @since 6.0.0
	 * @return array
	 */
	public static function defaults() {
		return array(
			'status' => self::STATUS_NEW,
			'step' => self::STEP_CHECK,
			'code' => '',
			'message' => '',
			'attempts' => 0,
			'pro' => self::PRO_PENDING,
			'pro_message' => '',
			'free_version' => '',
			'pro_version' => '',
			'snoozed_until' => 0,
			'started_at' => 0,
			'updated_at' => 0,
		);
	}


	/**
	 * Read the whole state
	 *
	 * @since 6.0.0
	 * @return array
	 */
	public static function get() {
		$state = get_option( self::OPTION, array() );

		return wp_parse_args( is_array( $state ) ? $state : array(), self::defaults() );
	}


	/**
	 * Read one field of the state
	 *
	 * @since 6.0.0
	 * @param string $key | Field name
	 * @return mixed
	 */
	public static function value( $key ) {
		$state = self::get();

		return isset( $state[ $key ] ) ? $state[ $key ] : null;
	}


	/**
	 * Merge changes into the state
	 *
	 * @since 6.0.0
	 * @param array $changes | Fields to overwrite
	 * @return array
	 */
	public static function patch( $changes ) {
		$state = self::get();

		if ( is_array( $changes ) ) {
			$state = array_merge( $state, $changes );
		}

		$state['updated_at'] = time();

		// autoloaded: every admin request reads this to decide whether to ask
		update_option( self::OPTION, $state, true );

		return $state;
	}


	/**
	 * Record that the migration is under way, from the first step
	 *
	 * @since 6.0.0
	 * @return array
	 */
	public static function start() {
		return self::patch( array(
			'status' => self::STATUS_RUNNING,
			'step' => self::STEP_CHECK,
			'code' => '',
			'message' => '',
			'started_at' => time(),
		) );
	}


	/**
	 * Record a step that cannot be retried on this host
	 *
	 * @since 6.0.0
	 * @param string $code | Machine readable reason
	 * @param string $message | What the store owner reads
	 * @return array
	 */
	public static function block( $code, $message ) {
		return self::patch( array(
			'status' => self::STATUS_BLOCKED,
			'code' => (string) $code,
			'message' => (string) $message,
		) );
	}


	/**
	 * Record a step that failed and may be tried again
	 *
	 * @since 6.0.0
	 * @param string $step | Step that failed
	 * @param string $code | Machine readable reason
	 * @param string $message | What the store owner reads
	 * @return array
	 */
	public static function fail( $step, $code, $message ) {
		$state = self::get();

		return self::patch( array(
			'status' => self::STATUS_FAILED,
			'step' => (string) $step,
			'code' => (string) $code,
			'message' => (string) $message,
			'attempts' => (int) $state['attempts'] + 1,
		) );
	}


	/**
	 * Record that every step has run
	 *
	 * @since 6.0.0
	 * @return array
	 */
	public static function finish() {
		return self::patch( array(
			'status' => self::STATUS_DONE,
			'step' => self::STEP_DEACTIVATE_LEGACY,
			'code' => '',
			'message' => '',
		) );
	}


	/**
	 * The step that follows the given one, or an empty string after the last
	 *
	 * @since 6.0.0
	 * @param string $step | Current step
	 * @return string
	 */
	public static function next_step( $step ) {
		$steps = self::steps();
		$index = array_search( $step, $steps, true );

		if ( false === $index || ! isset( $steps[ $index + 1 ] ) ) {
			return '';
		}

		return $steps[ $index + 1 ];
	}


	/**
	 * Whether the migration has finished
	 *
	 * @since 6.0.0
	 * @return bool
	 */
	public static function is_done() {
		return self::STATUS_DONE === self::value('status');
	}


	/**
	 * Whether the store owner asked to be left alone for now
	 *
	 * @since 6.0.0
	 * @return bool
	 */
	public static function is_snoozed() {
		return (int) self::value('snoozed_until') > time();
	}


	/**
	 * Hold the notice back for a while
	 *
	 * @since 6.0.0
	 * @param int $seconds | How long to stay quiet
	 * @return array
	 */
	public static function snooze( $seconds ) {
		return self::patch( array(
			'snoozed_until' => time() + (int) $seconds,
		) );
	}


	/**
	 * Clear a failure so the steps can be tried again
	 *
	 * The step is kept: a retry resumes where it stopped instead of
	 * reinstalling what is already in place.
	 *
	 * @since 6.0.0
	 * @return array
	 */
	public static function retry() {
		return self::patch( array(
			'status' => self::STATUS_RUNNING,
			'code' => '',
			'message' => '',
			'snoozed_until' => 0,
		) );
	}
}
