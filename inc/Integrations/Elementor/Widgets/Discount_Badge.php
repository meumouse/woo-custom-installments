<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

use MeuMouse\Woo_Custom_Installments\Core\Helpers;
use MeuMouse\Woo_Custom_Installments\Views\Components;
use MeuMouse\Woo_Custom_Installments\API\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Elementor widget for add discount badge
 * 
 * @since 5.4.0
 * @version 5.5.1
 * @package MeuMouse\Woo_Custom_Installments\Integrations\Elementor
 * @author MeuMouse.com
 */
class Discount_Badge extends Widget_Base {

    /**
     * Widget slug
     * 
     * @since 5.4.0
     * @return string
     */
    public function get_name() {
        return 'wci_sale_badge';
    }


    /**
     * Widget title
     * 
     * @since 5.4.0
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Emblema de desconto', 'woo-custom-installments' );
    }


    /**
     * Widget icon
     * 
     * @since 5.4.0
     * @return string
     */
    public function get_icon() {
        return 'eicon-info-circle-o';
    }


    /**
     * Widget categories
     * 
     * @since 5.4.0
     * @return array
     */
    public function get_categories() {
        return array('woo-custom-installments');
    }

    /**
     * Search widget by keywords
     * 
     * @since 5.4.0
     * @return array
     */
    public function get_keywords() {
        return array( 'desconto', 'badge', 'emblema', 'oferta', 'promoção', 'parcelas' );
    }


    /**
	 * Show in panel
	 *
     * @since 5.4.0
	 * @return bool Whether to show the widget in the panel or not
	 */
	public function show_in_panel() {
        return true;
    }


    /**
	 * Get widget promotion data
	 * Retrieve the widget promotion data
	 *
	 * @since 5.4.0
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
     * @since 5.4.0
     * @return void
     */
    protected function register_controls() {
        $this->start_controls_section(
            'wci_sale_badge_style_section',
            [
                'label' => esc_html__( 'Estilo do emblema', 'woo-custom-installments' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Tipografia', 'woo-custom-installments' ),
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .wci-sale-badge',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'Cor do texto', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wci-sale-badge' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => esc_html__( 'Cor de fundo', 'woo-custom-installments' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wci-sale-badge' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__( 'Borda', 'woo-custom-installments' ),
                'selector' => '{{WRAPPER}} .wci-sale-badge',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__( 'Raio da borda', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .wci-sale-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'margin',
            [
                'label' => esc_html__( 'Margem externa', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .wci-sale-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => esc_html__( 'Margem interna', 'woo-custom-installments' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .wci-sale-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'hidden',
            [
                'label' => esc_html__( 'Ocultar emblema de desconto', 'woo-custom-installments' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Ocultar', 'woo-custom-installments' ),
                'label_off' => esc_html__( 'Mostrar', 'woo-custom-installments' ),
                'return_value' => 'none',
                'default' => 'block',
                'selectors' => [
                    '{{WRAPPER}} .wci-sale-badge' => 'display: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }


    /**
     * Render widget output on the frontend
     * 
     * @since 5.4.0
     * @return void
     */
    protected function render() {
        if ( ! License::is_valid() ) {
            echo License::render_widget_license_message();
            return;
        }

        $product_id = Helpers::get_product_id_from_post();
        $product = wc_get_product( $product_id );

        // instance of components class
		$components = new Components();

		echo $components->sale_badge( $product );
    }
}