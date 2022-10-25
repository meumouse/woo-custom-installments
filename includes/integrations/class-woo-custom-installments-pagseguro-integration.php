<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit; }

class Woo_Custom_Installments_Pagseguro_Integration {

  public function __construct() {
    add_filter( 'woo_custom_installments_settings', array( $this, 'pagseguro_fields' ) );
    add_action( 'woocommerce_admin_field_pagseguro_fees', array( $this, 'pagseguro_fees_field' ) );
    add_filter( 'woo_custom_installments_with_fee', array( $this, 'pagseguro_installment_with_fee' ), 200, 4 );
    add_filter( 'woo_custom_installments_dynamic_table_params', array( $this, 'add_pagseguro_fees' ) );
  }

  function pagseguro_fields( $settings ) {
    if ( class_exists( 'WC_PagSeguro' ) ) {
      $new_settings = array(
        array(
          'name' => 'PagSeguro',
          'type' => 'title',
          'desc' => 'O PagSeguro possui uma forma específica de calcular os juros. Por isso, se você usa PagSeguro, pode seguir <a href="https://meumouse.com/docs/plugins/parcelas-customizadas-para-woocommerce/" target="_blank">este passo a passo</a> para configurar suas taxas adequadamente. Deixe em branco para não usar este recurso.'
        ),
        array(
          'type' => 'pagseguro_fees',
          'id'   => 'woo_custom_installments_pagseguro_fees',
          'title' => 'Taxas de juros',
        ),
        array(
          'title'   => __( 'Registros PagSeguro', 'woo-custom-installments' ),
          'desc'    => __( 'Registro dos cálculos feitos para o PagSeguro. Desative após analisar os dados', 'woo-custom-installments' ),
          'id'      => 'woo_custom_installments_debug_pagseguro',
          'default' => 'no',
          'type'    => 'checkbox'
        ),
        array(
          'type' => 'sectionend',
          'id' => 'wcsp-pagseguro-fees-end',
        ),
      );

      $settings = array_merge( $settings, $new_settings );
    }

    return $settings;
  }


  public function pagseguro_fees_field( $value ) {
    $option_value = get_option( $value['id'], $value['default'] );
    ?>
    <tr valign="top">
      <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
      </th>
      <td class="forminp forminp-text">
        <fieldset>
          <?php
            for ( $i = 1; $i <= 24; $i++ ) :
              $interest = isset( $option_value[ $i ] ) ? $option_value[ $i ] : '';
          ?>
          <p data-installment="<?php echo $i; ?>">
            <input class="small-input" type="text" value="<?php echo $i; ?>"
              <?php disabled( 1, true ); ?> />
            <input class="small-input" type="text"
              placeholder="0,00"
              name="<?php echo esc_attr( $value['id'] ); ?>[<?php echo $i; ?>]"
              id="<?php echo esc_attr( $value['id'] ); ?>" value="<?php echo wc_format_localized_price( $interest ) ?>" />
          </p>
          <?php endfor; ?>
        </fieldset>
      </td>
    </tr>
    <?php
  }


  public function pagseguro_installment_with_fee( $installment_price, $value, $fee, $installments ) {
    $fator = get_option( 'woo_custom_installments_pagseguro_fees', array() );
    $fator = array_filter( $fator );

    $this->log( 'Regras disponíveis: ' . print_r( $fator, true ) );

    if ( isset( $fator[ $installments ] ) && '' !== $fator[ $installments ] ) {
      $this->log( $installments . ' parcelas. Valor original: ' . $installment_price );

      $value = $value * wc_format_decimal( $fator[ $installments ], 6 );
      $installment_price = $value / $installments;

      $this->log( $installments . ' parcelas. Valor com PagSeguro: ' . $installment_price );
    } else {
      $this->log( 'Pagseguro não definido para ' . $installments );
    }

    return $installment_price;
  }


  public function log( $message ) {
    if ( 'yes' === get_option( 'woo_custom_installments_debug_pagseguro', 'no' ) ) {
      $log = new WC_Logger();
      $log->add( 'wcsp-pagseguro', $message );
    }
  }

  public function add_pagseguro_fees( $args ) {
    $fator = get_option( 'woo_custom_installments_pagseguro_fees', array() );
    $fator = array_filter( $fator );

    $args['pagseguro_fees'] = array_map( function($value) {
      return wc_format_decimal( $value, 6 );
    }, $fator );

    return $args;
  }
}

new Woo_Custom_Installments_Pagseguro_Integration();