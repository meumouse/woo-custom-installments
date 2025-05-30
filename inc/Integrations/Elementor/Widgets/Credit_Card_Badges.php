<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

use MeuMouse\Woo_Custom_Installments\API\License;
use MeuMouse\Woo_Custom_Installments\Views\Components;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Elementor widget for add payment methods popup on single product page
 * 
 * @since 5.0.0
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Credit_Card_Badges extends Widget_Base {

	/**
	 * Get widget name
	 *
     * @since 5.0.0
	 * @return string Widget name
	 */
	public function get_name() {
		return 'wci_credit_card_badges';
	}


	/**
	 * Get widget title
	 *
     * @since 5.0.0
	 * @return string Widget title
	 */
	public function get_title() {
		return esc_html__( 'Bandeiras de cartão de crédito', 'woo-custom-installments' );
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
        return [ 'badges', 'bandeiras', 'formas de pagamento', 'cartão de crédito', 'cartão', 'credit card' ];
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
            'upgrade_url' => esc_url( 'https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=elementor_editor&utm_medium=widgets&utm_campaign=elementor_widgets#buy-pro' ),
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
            'wci_credit_card_badges_style_section',
            array(
                'label' => esc_html__( 'Estilos - bandeiras cartão de crédito', 'woo-custom-installments' ),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
			'wci_credit_card_badges_width',
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
					'{{WRAPPER}} .container-badge-icon.credit-card' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

        $this->add_responsive_control(
            'wci_credit_card_badges_margin',
            array(
                'label' => esc_html__( 'Margem externa', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em', 'rem' ),
                'selectors' => array(
                    '{{WRAPPER}} .credit-card-container-badges' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );
    
        $this->add_responsive_control(
            'wci_credit_card_badges_padding',
            array(
                'label' => esc_html__( 'Margem interna', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em', 'rem' ),
                'selectors' => array(
                    '{{WRAPPER}} .container-badge-icon.credit-card .size-badge-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );

        $this->end_controls_section();
    }    


    /**
	 * Render the widget output on the frontend
     * 
     * @since 5.0.0
	 * @version 5.4.0
     * @return void
	 */
	protected function render() {
        if ( License::is_valid() ) {
            $settings = wp_parse_args(
                $this->get_settings_for_display(), array(
                    'product_id' => false,
                ),
            );

            echo '<div class="credit-card-container-badges">';
				// instance components class
				$components = new Components();

				echo $components->render_credit_card_flags();
            echo '</div>';
        } else {
            echo License::render_widget_license_message();
        }
	}
}

$widgets_manager->register( new Credit_Card_Badges() );