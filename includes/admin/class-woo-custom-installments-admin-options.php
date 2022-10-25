<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit; }

/**
 * WooCommerce Webhooks Table List
 *
 * @package  MeuMouse.com
 * @version  1.0.0
 */

class Woo_Custom_Installments_Admin_Options {

  public function __construct() {
    add_filter( 'woocommerce_get_sections_products', array( $this, 'add_products_section' ), 10, 1 );
    add_filter( 'woocommerce_get_settings_products', array( $this, 'get_settings' ), 10, 2 );
  }

  public function add_products_section( $sections ) {
    $sections['woo-custom-installments'] = __( 'Parcelas customizadas', 'woo-custom-installments' );

    return $sections;
  }


  public function get_settings( $settings, $current_section ) {
    if ( 'woo-custom-installments' == $current_section ) {

      $woo_custom_installments_settings = array(
        array(
          'name' => __( 'Parcelas Customizadas para WooCommerce', 'woo-custom-installments' ),
          'type' => 'title',
          'desc' => __( 'Configure abaixo o parcelamento e desconto no pagamento a vista. <a href="https://meumouse.com/docs/plugins/parcelas-customizadas-para-woocommerce/" target="_blank">Preciso de ajuda</a>', 'woo-custom-installments' ),
        ),

        array(
          'title'    => __( 'Desconto no preço principal', 'woo-custom-installments' ),
          'desc'     => __( 'Se preenchido, o valor com desconto irá subtituir o valor principal do produto', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_main_price',
          'type'     => 'number',
          'custom_attributes' => array(
            'min'  => 0,
            'max'  => 100,
            'step' => 'any'
          ),
          'default'  => 10,
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Texto depois do preço', 'woo-custom-installments' ),
          'desc'     => __( 'Exibe apenas se o campo "Desconto no preço principal" for preenchido. Ex.: "no Pix" irá retornar "R$XX,xx no Pix."', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_text_after_price',
          'type'     => 'text',
          'default'  => __( 'no Pix', 'woo-custom-installments' ),
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'   => __( 'Sempre exibir o preço do boleto', 'woo-custom-installments' ),
          'desc'    => __( 'Por padrão, o valor no boleto é exibido apenas quando há um desconto. Marque para exibir até mesmo quando nenhum desconto é definido.', 'woo-custom-installments' ),
          'id'      => 'woo_custom_installments_always_show_ticket',
          'default' => 'no',
          'type'    => 'checkbox'
        ),

        array(
          'title'   => __( 'Exibir no carrinho', 'woo-custom-installments' ),
          'desc'    => __( 'Se ativo, irá exibir o valor do preço com desconto também no carrinho', 'woo-custom-installments' ),
          'id'      => 'woo_custom_installments_show_in_cart',
          'default' => 'yes',
          'type'    => 'checkbox'
        ),

        array(
          'title'    => __( 'Exibição do preço com desconto', 'woo-custom-installments' ),
          'desc'     => __( 'Selecione onde o preço com desconto deve ser exibido', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_ticket_visibility',
          'class'    => 'wc-enhanced-select',
          'css'      => 'min-width:300px;',
          'default'  => 'both',
          'type'     => 'select',
          'options'  => array(
            'both'         => __( 'Página do produto e arquivos', 'woo-custom-installments' ),
            'main_price'   => __( 'Apenas página do produto', 'woo-custom-installments' ),
            'loop'         => __( 'Apenas arquivos de produtos', 'woo-custom-installments' ),
          ),
          'desc_tip' =>  true,
        ),

        array(
          'title'    => __( 'Taxa de juros no parcelamento', 'woo-custom-installments' ),
          'desc'     => __( 'Informe a taxa de juros para o parcelamento dos produtos', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_fee_interest',
          'type'     => 'number',
          'custom_attributes' => array(
            'min'  => 0,
            'max'  => 100,
            'step' => 'any'
          ),
          'default'  => 2,
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Máximo de parcelas', 'woo-custom-installments' ),
          'desc'     => __( 'Informe o máximo de vezes que é possível parcelar as compras.', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_max_installments',
          'type'     => 'number',
          'custom_attributes' => array(
            'min'  => 1,
            'max'  => 100,
            'step' => 1
          ),
          'default'  => 12,
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Máximo de parcelas sem juros', 'woo-custom-installments' ),
          'desc'     => __( 'Informe em até quantas vezes o usuário pode comprar sem juros.', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_max_installments_without_interest',
          'type'     => 'number',
          'custom_attributes' => array(
            'min'  => 0,
            'max'  => 100,
            'step' => 1
          ),
          'default'  => 3,
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Valor mínimo da parcela', 'woo-custom-installments' ),
          'desc'     => __( 'Informe o valor mínimo de cada parcela.', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_min_value_installment',
          'type'     => 'number',
          'custom_attributes' => array(
            'min'  => 0,
            'step' => 'any'
          ),
          'default'  => 20,
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Exibição nos arquivos de produtos', 'woo-custom-installments' ),
          'desc'     => __( 'Selecione as opções de parcelas que você deseja exibir na listagem de produtos', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_display_shop_page',
          'class'    => 'wc-enhanced-select',
          'css'      => 'min-width:300px;',
          'default'  => 'best_no_fee',
          'type'     => 'select',
          'options'  => array(
            ''              => __( 'Não exibir nada', 'woo-custom-installments' ),
            'best_no_fee'   => __( 'Melhor parcela sem juros', 'woo-custom-installments' ),
            'best_with_fee' => __( 'Melhor parcela com juros', 'woo-custom-installments' ),
            'both'          => __( 'Melhor parcela com e sem juros', 'woo-custom-installments' ),
          ),
          'desc_tip' =>  true,
        ),

        array(
          'title'    => __( 'Exibição na página de produto único', 'woo-custom-installments' ),
          'desc'     => __( 'Selecione onde você pretende exibir o parcelamento dos produtos', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_display_single_product',
          'class'    => 'wc-enhanced-select',
          'css'      => 'min-width:300px;',
          'default'  => 'best_no_fee',
          'type'     => 'select',
          'options'  => array(
            ''              => __( 'Não exibir nada', 'woo-custom-installments' ),
            'best_no_fee'   => __( 'Melhor parcela sem juros', 'woo-custom-installments' ),
            'best_with_fee' => __( 'Melhor parcela com juros', 'woo-custom-installments' ),
            'both'          => __( 'Melhor parcela com e sem juros', 'woo-custom-installments' ),
          ),
          'desc_tip' =>  true,
        ),

        array(
          'title'    => __( 'Exibição do popup das parcelas', 'woo-custom-installments' ),
          'desc'     => __( 'Selecione onde você pretende exibir a tabela completa de parcelamento', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_display_full_table',
          'class'    => 'wc-enhanced-select',
          'css'      => 'min-width:300px;',
          'default'  => 'display_before_cart_form',
          'type'     => 'select',
          'options'  => array(
            ''                       => __( 'Não exibir', 'woo-custom-installments' ),
            'display_before_cart_form'   => __( 'Antes do carrinho', 'woo-custom-installments' ),
            'display_product_tabs'        => __( 'Como uma aba do produto', 'woo-custom-installments' ),
          ),
          'desc_tip' =>  true,
        ),

        array(
          'title'   => __( 'Exibir menor preço no Schema', 'woo-custom-installments' ),
          'desc'    => __( 'Se ativado, irá exibir o preço com desconto em serviços de comparação de preços que fazem leitura de schema', 'woo-custom-installments' ),
          'id'      => 'woo_custom_installments_change_schema',
          'default' => 'yes',
          'type'    => 'checkbox'
        ),

        array(
          'title'    => __( 'Estilo', 'woo-custom-installments' ),
          'desc'     => __( 'Selecione o estilo de design do plugin.', 'woo-custom-installments' ),
          'id'       => 'woo_custom_installments_style',
          'class'    => 'wc-enhanced-select',
          'css'      => 'min-width:300px;',
          'default'  => '',
          'type'     => 'select',
          'options'  => woo_custom_installments_get_available_styles(),
          'desc_tip' =>  true,
        ),

        array(
          'type' => 'sectionend',
          'id' => 'woo-custom-installments'
        ),
      );

      return apply_filters( 'woo_custom_installments_settings', $woo_custom_installments_settings );
    } 

    else {
      return $settings;
    }
  }

}

new Woo_Custom_Installments_Admin_Options();