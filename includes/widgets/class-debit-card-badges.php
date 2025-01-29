<?php

namespace MeuMouse\Woo_Custom_Installments\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\License;
use MeuMouse\Woo_Custom_Installments\Frontend;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Elementor widget for add payment methods popup on single product page
 * 
 * @since 5.0.0
 * @package MeuMouse.com
 */
class Debit_Card_Badges extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 *
     * @since 5.0.0
	 * @return string Widget name
	 */
	public function get_name() {
		return 'wci_debit_card_badges';
	}


	/**
	 * Get widget title
	 *
     * @since 5.0.0
	 * @return string Widget title
	 */
	public function get_title() {
		return esc_html__( 'Bandeiras de cartão de débito', 'woo-custom-installments' );
	}
    

	/**
	 * Get widget icon
	 *
     * @since 5.0.0
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-carousel';
	}

	/**
	 * Get widget categories
	 *
     * @since 5.0.0
	 * @return array Widget categories
	 */
	public function get_categories() {
		return array('woo-custom-installments');
	}


    /**
     * Search widget by keywords
     * 
     * @since 5.0.0
     * @return array
     */
    public function get_keywords() {
        return [ 'badges', 'bandeiras', 'formas de pagamento', 'cartão de débito', 'cartão', 'debit card' ];
    }


    /**
	 * Show in panel
	 *
     * @since 5.0.0
	 * @return bool Whether to show the widget in the panel or not
	 */
	public function show_in_panel() {
        return true;
    }


	/**
	 * Get widget promotion data
	 * Retrieve the widget promotion data
	 *
	 * @since 5.0.0
	 * @return array Widget promotion data
	 */
	protected function get_upsale_data() {
        return array(
            'condition' => ! License::is_valid(),
            'image' => esc_url( WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/go-pro.svg' ),
            'image_alt' => esc_attr__( 'Seja Pro', 'woo-custom-installments' ),
            'title' => esc_html__( 'Seja Pro - Parcelas Customizadas', 'woo-custom-installments' ),
            'description' => esc_html__( 'Adquira uma licença Pro do Parcelas Customizadas para WooCommerce para liberar todos os recursos e opções de estilização.', 'woo-custom-installments' ),
            'upgrade_url' => esc_url( 'https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/#buy-pro' ),
            'upgrade_text' => esc_html__( 'Comprar agora', 'woo-custom-installments' ),
        );
	}


	/**
	 * Register the widget controls
     * 
     * @since 5.0.0
     * @return void
	 */
	protected function register_controls() {
        $this->start_controls_section(
            'wci_debit_card_badges_style_section',
            array(
                'label' => esc_html__( 'Estilos - bandeiras cartão de crédito', 'woo-custom-installments' ),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
			'wci_debit_card_badges_width',
			[
				'label' => esc_html__( 'Tamanho da bandeira', 'woo-custom-installments' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
                    'rem' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'default' => [
					'unit' => 'rem',
					'size' => 3,
				],
				'selectors' => [
					'{{WRAPPER}} .container-badge-icon.debit-card' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

        $this->add_responsive_control(
            'wci_debit_card_badges_margin',
            array(
                'label' => esc_html__( 'Margem externa', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem'],
                'selectors' => array(
                    '{{WRAPPER}} .debit-card-container-badges' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );
    
        $this->add_responsive_control(
            'wci_debit_card_badges_padding',
            array(
                'label' => esc_html__( 'Margem interna', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem'],
                'selectors' => array(
                    '{{WRAPPER}} .container-badge-icon.debit-card .size-badge-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );

        $this->end_controls_section();
    }


    /**
	 * Render the widget output on the frontend
     * 
     * @since 5.0.0
     * @return void
	 */
	protected function render() {
        if ( License::is_valid() ) {
            $settings = wp_parse_args(
                $this->get_settings_for_display(), array(
                    'product_id' => false,
                ),
            );

            echo '<div class="debit-card-container-badges">';
            echo Frontend::get_instance()->generate_card_flags( 'debit-card', 'debit' );
            echo '</div>';
        } else {
            echo License::render_widget_license_message();
        }
	}
}

$widgets_manager->register( new Debit_Card_Badges() );