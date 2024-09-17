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
 * Elementor widget for add discount per quantity message
 * 
 * @since 5.2.0
 * @package MeuMouse.com
 */
class Discount_Per_Quantity extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 *
     * @since 5.2.0
	 * @return string Widget name
	 */
	public function get_name() {
		return 'wci_discount_per_quantity_message';
	}


	/**
	 * Get widget title
	 *
     * @since 5.2.0
	 * @return string Widget title
	 */
	public function get_title() {
		return esc_html__( 'Mensagem de desconto por quantidade', 'woo-custom-installments' );
	}
    

	/**
	 * Get widget icon
	 *
     * @since 5.2.0
	 * @return string Widget icon
	 */
	public function get_icon() {
		return 'eicon-product-info';
	}

	/**
	 * Get widget categories
	 *
     * @since 5.2.0
	 * @return array Widget categories
	 */
	public function get_categories() {
		return array('woo-custom-installments');
	}


    /**
     * Search widget by keywords
     * 
     * @since 5.2.0
     * @return array
     */
    public function get_keywords() {
        return [ 'discount', 'desconto', 'quantidade', 'desconto por quantidade', 'discount per quantity' ];
    }


    /**
	 * Show in panel
	 *
     * @since 5.2.0
	 * @return bool Whether to show the widget in the panel or not
	 */
	public function show_in_panel() {
        return true;
    }


	/**
	 * Get widget promotion data
	 * Retrieve the widget promotion data
	 *
	 * @since 5.2.0
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
     * @since 5.2.0
     * @return void
     */
    protected function register_controls() {
        $this->start_controls_section(
            'wci_discount_message_style_section',
            [
                'label' => esc_html__( 'Estilos - Mensagem de desconto por quantidade', 'woo-custom-installments' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'wci_discount_message_text_color',
            [
                'label' => esc_html__( 'Cor do texto', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woo-custom-installments-discount-per-quantity-message span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'wci_discount_message_background_color',
            [
                'label' => esc_html__( 'Cor de fundo', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woo-custom-installments-discount-per-quantity-message' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'wci_discount_message_typography',
                'label' => esc_html__( 'Tipografia', 'woo-custom-installments' ),
                'selector' => '{{WRAPPER}} .woo-custom-installments-discount-per-quantity-message span',
            ]
        );

        $this->add_control(
            'wci_discount_message_icon_color',
            [
                'label' => esc_html__( 'Cor do ícone', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woo-custom-installments-discount-per-quantity-message i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'wci_discount_message_icon_size',
            [
                'label' => esc_html__( 'Tamanho do ícone', 'woo-custom-installments' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 5,
                    ],
                    'rem' => [
                        'min' => 0.5,
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .woo-custom-installments-discount-per-quantity-message i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'wci_discount_message_margin',
            [
                'label' => esc_html__( 'Margem externa', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .woo-custom-installments-discount-per-quantity-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'wci_discount_message_padding',
            [
                'label' => esc_html__( 'Margem interna', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .woo-custom-installments-discount-per-quantity-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }


    /**
	 * Render the widget output on the frontend
     * 
     * @since 5.2.0
     * @return void
	 */
	protected function render() {
        if ( License::is_valid() && Init::get_setting('discount_per_qtd_message_method') === 'widget' ) {
            $settings = wp_parse_args(
                $this->get_settings_for_display(), array(
                    'product_id' => false,
                ),
            );

            $product_id = Helpers::get_product_id_from_post();

            echo Frontend::get_instance()->display_message_discount_per_quantity( $product_id );
        } else {
            echo License::render_widget_license_message();
        }
	}
}

$widgets_manager->register( new Discount_Per_Quantity() );