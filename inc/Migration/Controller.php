<?php

namespace MeuMouse\Woo_Custom_Installments\Migration;

use MeuMouse\Woo_Custom_Installments\API\Updater;

use WP_REST_Response;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * The screen that migrates the store, and the endpoints behind it
 *
 * The migration is driven from a screen of its own rather than from whatever
 * admin page happens to load first. Three reasons, in order of how badly they
 * bite:
 *
 *   1. Downloading and unpacking two plugins inside a random admin request is
 *      how a store owner meets a white screen on a slow host.
 *   2. A failure has to be visible to somebody. On this screen there is always
 *      someone reading it.
 *   3. One step per request keeps every step inside the host's time limit, and
 *      records where it stopped when one does not finish.
 *
 * @since 6.0.0
 * @package MeuMouse\Woo_Custom_Installments\Migration
 * @author MeuMouse.com
 */
class Controller {

	/**
	 * Admin page slug of the migration screen
	 *
	 * Deliberately not prefixed with the settings page slug: `Helpers::check_admin_page()`
	 * matches by substring, and a slug starting with `woo-custom-installments`
	 * would pull the whole settings bundle onto this screen.
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const PAGE = 'wci-migration';

	/**
	 * REST route that runs one step
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const REST_ROUTE = '/migration/step';

	/**
	 * Capability required to run the migration
	 *
	 * @since 6.0.0
	 * @var string
	 */
	const CAPABILITY = 'install_plugins';

	/**
	 * How long the notice stays quiet when the store owner asks for later
	 *
	 * @since 6.0.0
	 * @var int
	 */
	const SNOOZE = WEEK_IN_SECONDS;


	/**
	 * Register the screen and the endpoints that drive it
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register_page' ) );
		add_action( 'admin_init', array( $this, 'maybe_redirect' ) );

		// no-JavaScript path, and the one the retry button posts to
		add_action( 'admin_post_wci_migration_step', array( $this, 'handle_step' ) );
		add_action( 'admin_post_wci_migration_retry', array( $this, 'handle_retry' ) );
		add_action( 'admin_post_wci_migration_snooze', array( $this, 'handle_snooze' ) );
	}


	/**
	 * Register the migration screen, hidden from the menu
	 *
	 * Registered under WooCommerce and then removed from the submenu: the page
	 * keeps a real hook suffix and a real capability check, and nothing new
	 * appears in a menu the store owner did not ask for.
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function register_page() {
		add_submenu_page(
			'woocommerce',
			esc_html__( 'Atualizar o Parcelas Customizadas', 'woo-custom-installments' ),
			esc_html__( 'Atualizar o Parcelas Customizadas', 'woo-custom-installments' ),
			self::CAPABILITY,
			self::PAGE,
			array( $this, 'render_page' )
		);

		remove_submenu_page( 'woocommerce', self::PAGE );
	}


	/**
	 * Send the store owner to the migration screen, once
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function maybe_redirect() {
		global $pagenow;

		if ( wp_doing_ajax() || wp_doing_cron() || is_network_admin() ) {
			return;
		}

		if ( ! current_user_can( self::CAPABILITY ) ) {
			return;
		}

		// only the very first admin request after the update
		if ( State::STATUS_NEW !== State::value('status') ) {
			return;
		}

		// Never interrupt a request that is doing something else: an activation,
		// an update, a form being submitted. The dashboard and the plugins list
		// are where a store owner lands after updating, and that is enough.
		if ( ! in_array( $pagenow, array( 'index.php', 'plugins.php' ), true ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reads only which screen is being opened.
		if ( ! empty( $_GET['action'] ) || ! empty( $_GET['activate-multi'] ) || ! empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		State::start();

		wp_safe_redirect( self::url() );

		exit;
	}


	/**
	 * Register the REST route that runs one step
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function register_rest_routes() {
		register_rest_route( Updater::REST_NAMESPACE, self::REST_ROUTE, array(
			'methods' => 'POST',
			'callback' => array( $this, 'step_response' ),
			'permission_callback' => array( $this, 'step_permission' ),
		) );
	}


	/**
	 * Whether the current user may run the migration
	 *
	 * @since 6.0.0
	 * @return bool
	 */
	public function step_permission() {
		return current_user_can( self::CAPABILITY ) && current_user_can('activate_plugins');
	}


	/**
	 * Run one step and describe where the migration now stands
	 *
	 * @since 6.0.0
	 * @return WP_REST_Response
	 */
	public function step_response() {
		Runner::advance();

		return new WP_REST_Response( self::payload(), 200 );
	}


	/**
	 * Run one step from a form post, for a browser with no JavaScript
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function handle_step() {
		check_admin_referer('wci_migration_step');

		if ( ! $this->step_permission() ) {
			wp_die( esc_html__( 'Você não tem permissão para instalar plugins neste site.', 'woo-custom-installments' ) );
		}

		Runner::advance();

		wp_safe_redirect( self::url() );

		exit;
	}


	/**
	 * Clear a failure and send the store owner back to the screen
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function handle_retry() {
		check_admin_referer('wci_migration_retry');

		if ( ! $this->step_permission() ) {
			wp_die( esc_html__( 'Você não tem permissão para instalar plugins neste site.', 'woo-custom-installments' ) );
		}

		// A blocked host starts over from the requirements, because what blocked
		// it is exactly what may have been fixed since.
		if ( State::STATUS_BLOCKED === State::value('status') ) {
			State::patch( array( 'step' => State::STEP_CHECK ) );
		}

		State::retry();

		wp_safe_redirect( self::url() );

		exit;
	}


	/**
	 * Hold the notice back and send the store owner where they were going
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function handle_snooze() {
		check_admin_referer('wci_migration_snooze');

		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'Você não tem permissão para instalar plugins neste site.', 'woo-custom-installments' ) );
		}

		State::snooze( self::SNOOZE );

		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );

		exit;
	}


	/**
	 * Everything the screen needs to render the current state
	 *
	 * @since 6.0.0
	 * @return array
	 */
	public static function payload() {
		$state = State::get();
		$steps = array();

		foreach ( State::steps() as $step ) {
			$steps[] = array(
				'id' => $step,
				'label' => self::step_label( $step ),
			);
		}

		return array(
			'status' => $state['status'],
			'step' => $state['step'],
			'step_label' => self::step_label( $state['step'] ),
			'message' => $state['message'],
			'code' => $state['code'],
			'attempts' => (int) $state['attempts'],
			'pro' => $state['pro'],
			'pro_message' => $state['pro_message'],
			'steps' => $steps,
			'done' => State::STATUS_DONE === $state['status'],
			'blocked' => State::STATUS_BLOCKED === $state['status'],
			'failed' => State::STATUS_FAILED === $state['status'],
			'settings_url' => self::settings_url(),
		);
	}


	/**
	 * Render the migration screen
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'Você não tem permissão para instalar plugins neste site.', 'woo-custom-installments' ) );
		}

		$state = State::get();

		echo '<div class="wrap wci-migration">';

		printf(
			'<h1>%s</h1><p class="wci-migration-intro">%s</p>',
			esc_html__( 'Atualizar o Parcelas Customizadas', 'woo-custom-installments' ),
			esc_html__( 'O Parcelas Customizadas agora é instalado como dois plugins: a versão gratuita, que exibe os preços, e a versão Pro, que libera os recursos da sua licença. Suas configurações são importadas automaticamente e nada é apagado.', 'woo-custom-installments' )
		);

		if ( State::STATUS_DONE === $state['status'] ) {
			$this->render_done( $state );
		} elseif ( State::STATUS_BLOCKED === $state['status'] ) {
			$this->render_blocked( $state );
		} else {
			$this->render_progress( $state );
		}

		echo '</div>';
	}


	/**
	 * Render the screen of a store that has been migrated
	 *
	 * @since 6.0.0
	 * @param array $state | Migration state
	 * @return void
	 */
	private function render_done( $state ) {
		printf(
			'<div class="wci-migration-panel is-done"><h2>%s</h2><p>%s</p>',
			esc_html__( 'Tudo pronto', 'woo-custom-installments' ),
			esc_html__( 'A versão nova está ativa e este plugin foi desativado. Suas configurações já estão lá.', 'woo-custom-installments' )
		);

		if ( State::PRO_SKIPPED === $state['pro'] && '' !== $state['pro_message'] ) {
			printf( '<p class="wci-migration-note">%s</p>', esc_html( $state['pro_message'] ) );
		}

		printf(
			'<p><a class="button button-primary" href="%1$s">%2$s</a> <a class="button" href="%3$s">%4$s</a></p></div>',
			esc_url( self::settings_url() ),
			esc_html__( 'Abrir as configurações', 'woo-custom-installments' ),
			esc_url( admin_url('plugins.php') ),
			esc_html__( 'Ver os plugins instalados', 'woo-custom-installments' )
		);
	}


	/**
	 * Render the screen of a host that cannot run the new plugins
	 *
	 * @since 6.0.0
	 * @param array $state | Migration state
	 * @return void
	 */
	private function render_blocked( $state ) {
		printf(
			'<div class="wci-migration-panel is-blocked"><h2>%s</h2><p>%s</p><p>%s</p>',
			esc_html__( 'Este servidor ainda não pode receber a versão nova', 'woo-custom-installments' ),
			esc_html( $state['message'] ),
			esc_html__( 'Nada foi alterado: sua loja continua funcionando com a versão atual do plugin.', 'woo-custom-installments' )
		);

		echo $this->retry_form( esc_html__( 'Verificar novamente', 'woo-custom-installments' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped parts.

		echo '</div>';
	}


	/**
	 * Render the screen while the steps are running
	 *
	 * @since 6.0.0
	 * @param array $state | Migration state
	 * @return void
	 */
	private function render_progress( $state ) {
		echo '<div class="wci-migration-panel" id="wci-migration-panel">';

		echo '<ol class="wci-migration-steps">';

		foreach ( State::steps() as $step ) {
			printf(
				'<li class="wci-migration-step" data-step="%1$s"><span class="wci-migration-step-mark" aria-hidden="true"></span><span class="wci-migration-step-label">%2$s</span></li>',
				esc_attr( $step ),
				esc_html( self::step_label( $step ) )
			);
		}

		echo '</ol>';

		printf(
			'<p class="wci-migration-status" id="wci-migration-status" role="status">%s</p>',
			esc_html( State::STATUS_FAILED === $state['status'] ? $state['message'] : self::step_label( $state['step'] ) )
		);

		echo '<div class="wci-migration-actions" id="wci-migration-actions">';

		echo $this->step_form( State::STATUS_FAILED === $state['status'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped parts.

		printf(
			' <a class="button-link wci-migration-later" href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( admin_url('admin-post.php?action=wci_migration_snooze'), 'wci_migration_snooze' ) ),
			esc_html__( 'Fazer isso mais tarde', 'woo-custom-installments' )
		);

		echo '</div></div>';
	}


	/**
	 * The form that runs one step without JavaScript
	 *
	 * @since 6.0.0
	 * @param bool $is_retry | Whether the last attempt failed
	 * @return string
	 */
	private function step_form( $is_retry ) {
		return sprintf(
			'<form method="post" action="%1$s" class="wci-migration-form">%2$s<input type="hidden" name="action" value="wci_migration_step" /><button type="submit" class="button button-primary">%3$s</button></form>',
			esc_url( admin_url('admin-post.php') ),
			wp_nonce_field( 'wci_migration_step', '_wpnonce', true, false ),
			$is_retry
				? esc_html__( 'Tentar novamente', 'woo-custom-installments' )
				: esc_html__( 'Começar', 'woo-custom-installments' )
		);
	}


	/**
	 * The form that clears a failure
	 *
	 * @since 6.0.0
	 * @param string $label | Button label, already escaped
	 * @return string
	 */
	private function retry_form( $label ) {
		return sprintf(
			'<form method="post" action="%1$s" class="wci-migration-form">%2$s<input type="hidden" name="action" value="wci_migration_retry" /><button type="submit" class="button button-primary">%3$s</button></form>',
			esc_url( admin_url('admin-post.php') ),
			wp_nonce_field( 'wci_migration_retry', '_wpnonce', true, false ),
			$label
		);
	}


	/**
	 * What a step is called on screen
	 *
	 * @since 6.0.0
	 * @param string $step | Step identifier
	 * @return string
	 */
	public static function step_label( $step ) {
		$labels = array(
			State::STEP_CHECK => esc_html__( 'Verificando este servidor', 'woo-custom-installments' ),
			State::STEP_INSTALL_FREE => esc_html__( 'Baixando a versão nova', 'woo-custom-installments' ),
			State::STEP_ACTIVATE_FREE => esc_html__( 'Ativando a versão nova', 'woo-custom-installments' ),
			State::STEP_INSTALL_PRO => esc_html__( 'Baixando a versão Pro', 'woo-custom-installments' ),
			State::STEP_ACTIVATE_PRO => esc_html__( 'Ativando a versão Pro', 'woo-custom-installments' ),
			State::STEP_DEACTIVATE_LEGACY => esc_html__( 'Desativando a versão anterior', 'woo-custom-installments' ),
		);

		return isset( $labels[ $step ] ) ? $labels[ $step ] : '';
	}


	/**
	 * Address of the migration screen
	 *
	 * @since 6.0.0
	 * @return string
	 */
	public static function url() {
		return admin_url( 'admin.php?page=' . self::PAGE );
	}


	/**
	 * Address of the settings screen of the new plugin
	 *
	 * @since 6.0.0
	 * @return string
	 */
	public static function settings_url() {
		return admin_url( 'admin.php?page=' . Package_Source::FREE_SLUG );
	}


	/**
	 * Whether the migration screen is the one being rendered
	 *
	 * @since 6.0.0
	 * @return bool
	 */
	public static function is_current_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reads only which screen is being opened.
		return isset( $_GET['page'] ) && self::PAGE === sanitize_key( $_GET['page'] );
	}
}
