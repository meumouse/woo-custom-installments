<?php

namespace MeuMouse\Woo_Custom_Installments\Migration;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Tell the store owner that the plugin has been replaced
 *
 * One notice, on every admin screen except the migration screen itself, with a
 * single action: open the screen that does the work. What it says depends on
 * what the migration found, because "atualize agora" and "este servidor não
 * pode receber a versão nova" are not the same message and must not look alike.
 *
 * @since 6.0.0
 * @package MeuMouse\Woo_Custom_Installments\Migration
 * @author MeuMouse.com
 */
class Notice {

	/**
	 * Register the notice
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_notices', array( $this, 'render' ) );
	}


	/**
	 * Render the notice, when there is something to say
	 *
	 * @since 6.0.0
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( Controller::CAPABILITY ) ) {
			return;
		}

		// the screen says all of this, at length
		if ( Controller::is_current_page() ) {
			return;
		}

		$state = State::get();

		if ( State::STATUS_DONE === $state['status'] || State::is_snoozed() ) {
			return;
		}

		if ( State::STATUS_BLOCKED === $state['status'] ) {
			$this->render_notice(
				'warning',
				esc_html__( 'O Parcelas Customizadas foi dividido em dois plugins, mas este servidor ainda não atende aos requisitos da versão nova.', 'woo-custom-installments' ),
				esc_html( $state['message'] ),
				esc_html__( 'Ver o que falta', 'woo-custom-installments' )
			);

			return;
		}

		if ( State::STATUS_FAILED === $state['status'] ) {
			$this->render_notice(
				'warning',
				esc_html__( 'A atualização do Parcelas Customizadas não foi concluída.', 'woo-custom-installments' ),
				esc_html( $state['message'] ),
				esc_html__( 'Continuar a atualização', 'woo-custom-installments' )
			);

			return;
		}

		$this->render_notice(
			'info',
			esc_html__( 'O Parcelas Customizadas agora é instalado como dois plugins, e esta versão será substituída.', 'woo-custom-installments' ),
			esc_html__( 'A troca é automática, leva menos de um minuto e suas configurações são importadas. Nada é apagado.', 'woo-custom-installments' ),
			esc_html__( 'Atualizar agora', 'woo-custom-installments' )
		);
	}


	/**
	 * Print one notice
	 *
	 * @since 6.0.0
	 * @param string $type | Notice type, as WordPress names them
	 * @param string $title | First sentence, already escaped
	 * @param string $body | Second sentence, already escaped
	 * @param string $label | Button label, already escaped
	 * @return void
	 */
	private function render_notice( $type, $title, $body, $label ) {
		printf(
			'<div class="notice notice-%1$s"><p><strong>%2$s</strong></p><p>%3$s</p><p><a class="button button-primary" href="%4$s">%5$s</a></p></div>',
			esc_attr( $type ),
			$title, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by the caller.
			$body, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by the caller.
			esc_url( Controller::url() ),
			$label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by the caller.
		);
	}
}
