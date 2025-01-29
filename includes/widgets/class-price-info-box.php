<?php

namespace MeuMouse\Woo_Custom_Installments\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use MeuMouse\Woo_Custom_Installments\Helpers;
use MeuMouse\Woo_Custom_Installments\License;
use MeuMouse\Woo_Custom_Installments\Frontend;
use MeuMouse\Woo_Custom_Installments\Calculate_Values;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * 
 */
class Price_Info_Box extends \Elementor\Widget_Base {

    /**
     * Get widget name
     *
     * @return string Widget name
     */
    public function get_name() {
        return 'wci_price_info_box';
    }


    /**
     * Get widget title
     *
     * @return string Widget title
     */
    public function get_title() {
        return esc_html__('Caixa de informação de preço', 'woo-custom-installments');
    }
    

    /**
     * Get widget icon
     *
     * @return string Widget icon
     */
    public function get_icon() {
        return 'eicon-product-info';
    }


    /**
     * Get widget categories
     *
     * @return array Widget categories
     */
    public function get_categories() {
        return ['woo-custom-installments'];
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
            'wci_price_info_box_content',
            [
                'label' => esc_html__('Configurações', 'woo-custom-installments'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'wci_price_info_box_price_type',
            [
                'label' => esc_html__('Escolha o preço', 'woo-custom-installments'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'pix' => esc_html__('Preço no Pix', 'woo-custom-installments'),
                    'ticket' => esc_html__('Preço no Boleto', 'woo-custom-installments'),
                    'economy_pix' => esc_html__('Economia no Pix', 'woo-custom-installments'),
                ],
                'default' => 'pix',
            ]
        );

        $this->add_control(
            'wci_price_info_box_text_before',
            [
                'label' => esc_html__('Texto antes do preço', 'woo-custom-installments'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Preço com desconto:', 'woo-custom-installments'),
            ]
        );

        $this->add_control(
            'wci_price_info_box_text_after',
            [
                'label' => esc_html__('Texto depois do preço', 'woo-custom-installments'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('à vista', 'woo-custom-installments'),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'wci_price_info_box_style',
            [
                'label' => esc_html__('Estilo', 'woo-custom-installments'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'wci_price_info_box_typography',
                'selector' => '{{WRAPPER}} .wci-price-info-text, {{WRAPPER}} .wci-price-info-text .amount',
            ]
        );

        $this->add_control(
            'wci_price_info_box_text_color',
            [
                'label' => esc_html__('Cor do texto', 'woo-custom-installments'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wci-price-info-text, {{WRAPPER}} .wci-price-info-text .amount' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'wci_price_info_box_background',
                'label' => esc_html__('Cor de fundo', 'woo-custom-installments'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .wci-price-info-box',
            ]
        );

        $this->add_responsive_control(
            'wci_price_info_box_padding',
            [
                'label' => esc_html__('Margem interna', 'woo-custom-installments'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wci-price-info-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'wci_price_info_box_margin',
            [
                'label' => esc_html__('Margem externa', 'woo-custom-installments'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wci-price-info-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'wci_price_info_box_border',
                'selector' => '{{WRAPPER}} .wci-price-info-box',
            ]
        );

        $this->add_control(
            'wci_price_info_box_border_radius',
            array(
                'label' => esc_html__( 'Raio da borda', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => array(
                    '{{WRAPPER}} .wci-price-info-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }


    /**
     * Render widget output on the frontend
     * 
     * @since 5.0.0
     * @return void
     */
    protected function render() {
        if ( License::is_valid() ) {
            $settings = $this->get_settings_for_display();
            $price_type = $settings['wci_price_info_box_price_type'];
            $text_before = $settings['wci_price_info_box_text_before'];
            $text_after = $settings['wci_price_info_box_text_after'];
            $price = '';
            $product_id = Helpers::get_product_id_from_post();
            $product = wc_get_product( $product_id );

            switch ( $price_type ) {
                case 'pix':
                    $price = wc_price( Calculate_Values::get_discounted_price( $product, 'main' ) );
                    break;
                case 'ticket':
                    $price = wc_price( Calculate_Values::get_discounted_price( $product, 'ticket' ) );
                    break;
                case 'economy_pix':
                    $price = wc_price( Frontend::calculate_pix_economy( $product ) );
                    break;
            }

            echo '<div class="wci-price-info-box">';
            echo '<span class="wci-price-info-text">' . sprintf( __( '%s %s %s' ), esc_html( $text_before ), $price, esc_html( $text_after ) ). '</span>';
            echo '</div>';
        } else {
            echo License::render_widget_license_message();
        }
    }
}

$widgets_manager->register( new Price_Info_Box() );