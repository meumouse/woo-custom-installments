<?php

namespace MeuMouse\Woo_Custom_Installments\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\Helpers;
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
class Wci_Accordion extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 *
     * @since 5.0.0
	 * @return string Widget name
	 */
	public function get_name() {
		return 'wci_accordion_payment_methods';
	}


	/**
	 * Get widget title
	 *
     * @since 5.0.0
	 * @return string Widget title
	 */
	public function get_title() {
		return esc_html__( 'Sanfona - Formas de pagamento', 'woo-custom-installments' );
	}
    

	/**
	 * Get widget icon
	 *
     * @since 5.0.0
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-accordion';
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
        return [ 'accordion', 'sanfona', 'formas de pagamento', 'parcelas', 'installments', 'pix' ];
    }


    /**
	 * Show in panel
	 *
     * @since 5.0.0
	 * @return bool Whether to show the widget in the panel or not
	 */
	public function show_in_panel() {
        return Helpers::is_editing_single_product_in_elementor();
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
            'wci_accordion_payment_methods_style_section',
            array(
                'label' => esc_html__( 'Estilos - formas de pagamento', 'woo-custom-installments' ),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->start_controls_tabs('wci_accordion_tabs_button_style');

        $this->start_controls_tab(
            'wci_accordion_tab_button_normal',
            array(
                'label' => esc_html__('Normal', 'woo-custom-installments'),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'label' => esc_html__( 'Tipografia', 'woo-custom-installments' ),
                'name' => 'wci_accordion_payment_methods_typography',
                'selector' => '{{WRAPPER}} .wci-accordion-header',
            )
        );
    
        $this->add_control(
            'wci_accordion_payment_methods_text_color',
            array(
                'label' => esc_html__( 'Cor do texto', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wci-accordion-header' => 'color: {{VALUE}} !important;',
                ),
            )
        );
    
        $this->add_control(
            'wci_accordion_payment_methods_background_color',
            array(
                'label' => esc_html__( 'Cor de fundo', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wci-accordion-header' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );
    
        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'wci_accordion_payment_methods_border',
                'label' => esc_html__( 'Borda', 'woo-custom-installments' ),
                'selector' => '{{WRAPPER}} .wci-accordion-item',
            )
        );
    
        $this->add_control(
            'wci_accordion_payment_methods_border_radius',
            array(
                'label' => esc_html__( 'Raio da borda', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => array(
                    '{{WRAPPER}} .wci-accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );
    
        $this->add_responsive_control(
            'wci_accordion_payment_methods_margin',
            array(
                'label' => esc_html__( 'Margem externa', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem'],
                'selectors' => array(
                    '{{WRAPPER}} #wci-accordion-installments' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );
    
        $this->add_responsive_control(
            'wci_accordion_payment_methods_padding',
            array(
                'label' => esc_html__( 'Margem interna', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem'],
                'selectors' => array(
                    '{{WRAPPER}} .wci-accordion-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );

        $this->add_control(
			'wci_accordion_payment_methods_height',
			[
				'label' => esc_html__( 'Altura do botão', 'woo-custom-installments' ),
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
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .wci-accordion-header, {{WRAPPER}} .wci-accordion-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
    
        $this->end_controls_tab();
    
        $this->start_controls_tab(
            'tab_button_hover',
            array(
                'label' => esc_html__('Ao passar o mouse', 'woo-custom-installments'),
            )
        );
    
        $this->add_control(
            'wci_accordion_payment_methods_hover_text_color',
            array(
                'label' => esc_html__( 'Cor do texto ao passar o mouse', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wci-accordion-header:hover' => 'color: {{VALUE}} !important;',
                ),
            )
        );
    
        $this->add_control(
            'wci_accordion_payment_methods_hover_background_color',
            array(
                'label' => esc_html__( 'Cor de fundo ao passar o mouse', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wci-accordion-header:hover' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );
    
        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'wci_accordion_payment_methods_hover_border',
                'label' => esc_html__( 'Borda ao passar o mouse', 'woo-custom-installments' ),
                'selector' => '{{WRAPPER}} .wci-accordion-item:hover',
            )
        );
    
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_active',
            array(
                'label' => esc_html__('Ativo', 'woo-custom-installments'),
            )
        );
    
        $this->add_control(
            'wci_accordion_payment_methods_active_text_color',
            array(
                'label' => esc_html__( 'Cor do texto do botão ativo', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wci-accordion-item.active .wci-accordion-header' => 'color: {{VALUE}} !important;',
                ),
            )
        );
    
        $this->add_control(
            'wci_accordion_payment_methods_active_background_color',
            array(
                'label' => esc_html__( 'Cor de fundo do botão ativo', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wci-accordion-item.active .wci-accordion-header' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );
    
        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'wci_accordion_payment_methods_active_border',
                'label' => esc_html__( 'Borda do botão ativo', 'woo-custom-installments' ),
                'selector' => '{{WRAPPER}} .wci-accordion-item.active .wci-accordion-item',
            )
        );
    
        $this->end_controls_tab();

        $this->end_controls_tabs();

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
            if ( Init::get_setting('hook_payment_form_single_product') === 'widget' ) {
                $settings = wp_parse_args(
                    $this->get_settings_for_display(), array(
                        'product_id' => false,
                    ),
                );
    
                $product_id = Helpers::get_product_id_from_post();
                $product = wc_get_product( $product_id );
    
                if ( $product === false ) {
                    global $product;
                }
                
                echo Frontend::get_instance()->accordion_container( $product );
            }
        } else {
            echo License::render_widget_license_message();
        }
	}
}

$widgets_manager->register( new Wci_Accordion() );