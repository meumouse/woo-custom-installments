<?php

use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="discount" class="nav-content">
    <table class="form-table">
      <tr>
        <th>
            <?php echo esc_html__( 'Ativar funções de descontos', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Ative esta opção para habilitar todas as opções relacionadas a desconto.', 'woo-custom-installments' ) ?></span>
        </th>

        <td>
            <div class="form-check form-switch">
                <input type="checkbox" class="toggle-switch" id="enable_all_discount_options" name="enable_all_discount_options" value="yes" <?php checked( self::get_setting( 'enable_all_discount_options') == 'yes' ); ?> />
            </div>
        </td>
      </tr>
      <tr class="display-enable-all-discount-options">
        <th>
            <?php echo esc_html__( 'Exibir preço com desconto no carrinho', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibir o valor do preço com desconto também no carrinho. (Recomendado)', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
            <div class="form-check form-switch">
                <input type="checkbox" class="toggle-switch" id="display_installments_cart" name="display_installments_cart" value="yes" <?php checked( self::get_setting( 'display_installments_cart') == 'yes' ); ?> />
            </div>
        </td>
      </tr>
      <tr class="display-enable-all-discount-options">
        <th>
            <?php echo esc_html__( 'Incluir valor de frete no desconto do pedido', 'woo-custom-installments' );

            if ( ! License::is_valid() ) : ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
               </span>
            <?php endif; ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá incluir o valor de frete no cálculo de desconto na finalização da compra. (Recomendado)', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
            <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="include_shipping_value_in_discounts" name="include_shipping_value_in_discounts" value="yes" <?php checked( self::get_setting( 'include_shipping_value_in_discounts') == 'yes' && License::is_valid() ); ?> />
            </div>
        </td>
      </tr>
      <tr class="display-enable-all-discount-options">
         <th>
               <?php echo esc_html__( 'Exibir preço com desconto em dados estruturados (Schema.org)', 'woo-custom-installments' );

               if ( ! License::is_valid() ) {

                  ?>
                  <span class="badge pro bg-primary rounded-pill ms-2">
                     <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                     <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
                  </span>
                  <?php
               }
               ?>
               <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibir o preço com desconto em serviços que fazem a leitura de dados estruturados ou "Rich snippets", para ajudar o produto em SEO. (Recomendado)', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
               <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="display_discount_price_schema" name="display_discount_price_schema" value="yes" <?php checked( self::get_setting( 'display_discount_price_schema') == 'yes' && License::is_valid() ); ?> />
               </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options">
         <th>
               <?php echo esc_html__( 'Ativar preço com desconto no Pix em Post Meta para Feed XML', 'woo-custom-installments' );

               if ( ! License::is_valid() ) {

                  ?>
                  <span class="badge pro bg-primary rounded-pill ms-2">
                     <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                     <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
                  </span>
                  <?php
               }
               ?>
               <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, o post meta "_product_price_on_pix" será criado para adicionar em Feed XML, com plugins compatíveis com esse recurso.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
               <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_post_meta_feed_xml_price" name="enable_post_meta_feed_xml_price" value="yes" <?php checked( self::get_setting( 'enable_post_meta_feed_xml_price') === 'yes' && License::is_valid() ); ?> />
               </div>
         </td>
      </tr>

      <tr class="display-enable-all-discount-options container-separator"></tr>

      <tr class="display-enable-all-discount-options">
         <th>
            <?php echo esc_html__( 'Desconto no preço do produto', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define qual será o valor de desconto sobre o preço do produto, para . Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="input-group">
               <span id="symbol_discount_pix" class="input-group-text justify-content-center">
                  <?php echo ( self::get_setting('product_price_discount_method') === 'percentage' ) ? '%' : get_woocommerce_currency_symbol(); ?>
               </span>

               <input type="text" id="discount_main_price" class="form-control input-control-wd-5 allow-number-and-dots" name="discount_main_price" placeholder="20" value="<?php echo self::get_setting( 'discount_main_price' ) ?>">
               
               <select id="product_price_discount_method" class="form-select" name="product_price_discount_method">
                  <option value="percentage" <?php echo ( self::get_setting('product_price_discount_method') == 'percentage' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></option>
                  <option value="fixed" <?php echo ( self::get_setting('product_price_discount_method') == 'fixed' ) ? "selected=selected" : ""; ?>><?php echo sprintf( __( 'Valor fixo (%s)', 'woo-custom-installments' ), get_woocommerce_currency_symbol() ) ?></option>
               </select>
            </div>
         </td>
      </tr>

      <tr class="display-enable-all-discount-options admin-discount-ticket-option">
         <th>
            <?php echo esc_html__( 'Desconto no boleto bancário', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define qual será o valor de desconto sobre o preço do produto para boleto bancário. Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
         </th>

         <td>
            <div class="input-group">
               <span id="symbol_discount_slip_bank" class="input-group-text justify-content-center">
                  <?php echo ( self::get_setting('discount_method_ticket') === 'percentage' ) ? '%' : get_woocommerce_currency_symbol(); ?>
               </span>

               <input type="text" id="discount_ticket" class="form-control input-control-wd-5 allow-number-and-dots" name="discount_ticket" placeholder="20" value="<?php echo self::get_setting( 'discount_ticket' ) ?>">
               
               <select id="discount_method_ticket" class="form-select get-discount-method-ticket" name="discount_method_ticket">
                  <option value="percentage" <?php echo ( self::get_setting( 'discount_method_ticket' ) == 'percentage' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></option>
                  <option value="fixed" <?php echo ( self::get_setting( 'discount_method_ticket' ) == 'fixed' ) ? "selected=selected" : ""; ?>><?php echo sprintf( __( 'Valor fixo (%s)', 'woo-custom-installments' ), get_woocommerce_currency_symbol() ) ?></option>
               </select>
            </div>
         </td>
      </tr>

      <tr class="display-enable-all-discount-options container-separator"></tr>

      <tr class="display-enable-all-discount-options">
         <th>
            <?php echo esc_html__( 'Ativar funções de desconto por quantidade', 'woo-custom-installments' );

            if ( ! License::is_valid() ) : ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
               </span>
            <?php endif; ?>

            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá ser adicionado um desconto a partir de uma quantidade mínima do produto no carrinho, para todos os produtos da loja.', 'woo-custom-installments' ) ?></span>
         </th>

         <td class="d-flex align-items-center">
            <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_functions_discount_per_quantity" name="enable_functions_discount_per_quantity" value="yes" <?php checked( self::get_setting( 'enable_functions_discount_per_quantity') == 'yes' && License::is_valid() ); ?> />
            </div>
            
            <button id="discount_per_quantity_trigger" class="btn btn-outline-primary ms-3 discount-per-quantity-option"><?php echo esc_html__( 'Configurar', 'woo-custom-installments' ) ?></button>
            
            <div id="discount_per_quantity_container">
               <div class="popup-content">
                  <div class="popup-header">
                     <h5 class="popup-title"><?php echo esc_html__( 'Configure o desconto por quantidade', 'woo-custom-installments' ); ?></h5>
                     <button id="discount_per_quantity_close" class="btn-close fs-lg" aria-label="<?php echo esc_html__( 'Fechar', 'woo-custom-installments' ) ?>"></button>
                  </div>
                  <div class="popup-body">
                     <table class="popup-table">
                        <tr class=" disable-discount-per-product-global">
                           <th>
                              <?php echo esc_html__( 'Método do desconto por quantidade', 'woo-custom-installments' );

                              if ( ! License::is_valid() ) {

                                 ?>
                                 <span class="badge pro bg-primary rounded-pill ms-2">
                                    <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                    <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
                                 </span>
                                 <?php
                              }
                              ?>
                              <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá ser adicionado um desconto a partir de uma quantidade mínima do produto no carrinho, para todos os produtos da loja.', 'woo-custom-installments' ) ?></span>
                           </th>
                           <td>
                              <select id="enable_discount_per_quantity_method" class="form-select get-discount-per-quantity-method" name="enable_discount_per_quantity_method">
                                 <option value="global" <?php echo ( self::get_setting( 'enable_discount_per_quantity_method' ) == 'global' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Para todos os produtos', 'woo-custom-installments' ) ?></option>
                                 <option value="product" <?php echo ( self::get_setting( 'enable_discount_per_quantity_method' ) == 'product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Configurar para cada produto', 'woo-custom-installments' ) ?></option>
                              </select>
                           </td>
                        </tr>

                        <tr class="">
                           <th>
                              <?php echo esc_html__( 'Ativar cálculo de desconto para cada unidade do produto elegível', 'woo-custom-installments' ) ?>
                              <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá ser considerado o desconto para cada unidade do produto.', 'woo-custom-installments' ) ?></span>
                           </th>
                           <td>
                              <div class="form-check form-switch">
                                 <input type="checkbox" class="toggle-switch" id="enable_discount_per_unit_discount_per_quantity" name="enable_discount_per_unit_discount_per_quantity" value="yes" <?php checked( self::get_setting( 'enable_discount_per_unit_discount_per_quantity') == 'yes' ); ?> />
                              </div>
                           </td>
                        </tr>

                        <tr class="">
                           <th>
                              <?php echo esc_html__( 'Ativar mensagem nos produtos elegíveis para desconto por quantidade', 'woo-custom-installments' ) ?>
                              <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá ser adicionado a mensagem informando o usuário que o produto é elegível para desconto por quantidade.', 'woo-custom-installments' ) ?></span>
                           </th>
                           <td>
                              <div class="form-check form-switch">
                                 <input type="checkbox" class="toggle-switch" id="message_discount_per_quantity" name="message_discount_per_quantity" value="yes" <?php checked( self::get_setting( 'message_discount_per_quantity') == 'yes' ); ?> />
                              </div>
                           </td>
                        </tr>

                        <tr class="global-discount-required">
                           <th>
                              <?php echo esc_html__( 'Valor do desconto por quantidade', 'woo-custom-installments' ) ?>
                              <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define qual será o valor de desconto por quantidade do produto.', 'woo-custom-installments' ) ?></span>
                           </th>
                           <td>
                              <div class="input-group">
                                 <span id="symbol_discount_quantity" class="input-group-text justify-content-center">
                                    <?php echo ( self::get_setting('discount_per_quantity_method') === 'percentage' ) ? '%' : get_woocommerce_currency_symbol(); ?>
                                 </span>
                                 <input type="text" id="value_for_discount_per_quantity" class="form-control input-control-wd-5 allow-number-and-dots" name="value_for_discount_per_quantity" placeholder="20" value="<?php echo self::get_setting( 'value_for_discount_per_quantity' ) ?>">
                                 
                                 <select id="discount_per_quantity_method" class="form-select" name="discount_per_quantity_method">
                                    <option value="percentage" <?php echo ( self::get_setting('discount_per_quantity_method') == 'percentage' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></option>
                                    <option value="fixed" <?php echo ( self::get_setting('discount_per_quantity_method') == 'fixed' ) ? "selected=selected" : ""; ?>><?php echo sprintf( __( 'Valor fixo (%s)', 'woo-custom-installments' ), get_woocommerce_currency_symbol() ) ?></option>
                                 </select>
                              </div>
                           </td>
                        </tr>

                        <tr class="global-discount-required">
                           <th>
                              <?php echo esc_html__( 'Quantidade mínima para oferecer desconto', 'woo-custom-installments' );

                              if ( ! License::is_valid() ) {

                                 ?>
                                 <span class="badge pro bg-primary rounded-pill ms-2">
                                    <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                    <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
                                 </span>
                                 <?php
                              }
                              ?>
                              <span class="woo-custom-installments-description"><?php echo esc_html__( 'Informe a quantidade mínima do produto para oferecer desconto no carrinho.', 'woo-custom-installments' ) ?></span>
                           </th>
                           <td>
                              <input type="number" id="set_quantity_enable_discount" class="form-control allow-numbers-be-1 input-control-wd-7-7rem" name="set_quantity_enable_discount" value="<?php echo self::get_setting( 'set_quantity_enable_discount' ) ?>"/>
                           </td>
                        </tr>
                     </table>
                  </div>
               </div>
            </div>
         </td>
      </tr>

      <tr class="display-enable-all-discount-options container-separator"></tr>

      <tr class="display-enable-all-discount-options">
         <th>
            <?php echo esc_html__( 'Mostrar informação de desconto na forma de pagamento', 'woo-custom-installments' );

            if ( ! License::is_valid() ) : ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
               </span>
            <?php endif; ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, será exibido o emblema de desconto ao lado do título da forma de pagamento configurada.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="display_tag_discount_price_checkout" name="display_tag_discount_price_checkout" value="yes" <?php checked( self::get_setting( 'display_tag_discount_price_checkout') == 'yes' && License::is_valid() ); ?> />
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options">
         <th class="w-100">
            <?php echo esc_html__( 'Desconto por método de pagamento', 'woo-custom-installments' );

            if ( ! License::is_valid() ) : ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
               </span>
            <?php endif; ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Informe um desconto por método de pagamento para ser adicionado na finalização da compra.', 'woo-custom-installments' ) ?></span>
         </th>
      </tr>

      <tr id="wci-discount-header" class="display-enable-all-discount-options">
         <th>
            <?php echo __( 'Método de pagamento', 'woo-custom-installments' ); ?>
         </th>
         <th class="w-50">
            <?php echo __( 'Valor', 'woo-custom-installments' ); ?>
         </th>
         <th>
            <?php echo __( 'Método do desconto', 'woo-custom-installments' ); ?>
         </th>
      </tr>
      <?php
      $payment_gateways = WC()->payment_gateways->payment_gateways();
      $discount_settings = maybe_unserialize( get_option('woo_custom_installments_discounts_setting', array()) );

      foreach ( $payment_gateways as $gateway ) :
         $current = isset( $discount_settings[$gateway->id]['amount'] ) ? $discount_settings[$gateway->id]['amount'] : '0'; ?>

         <tr id="wci-discount-methods-<?php echo esc_attr( $gateway->id ); ?>" class="display-enable-all-discount-options foreach-method-discount wci-discount-methods">
            <th class="wci-title-method-discount-header">
               <label for="woo_custom_installments_payment_discounts_<?php echo esc_attr( $gateway->id ); ?>"><?php echo esc_attr( $gateway->title ); ?></label>
            </th>
            <td class="wci-title-method-discount-div">
               <div class="input-group wci-method-discount-selector" id="foreach-payment-<?php echo esc_attr( $gateway->id ); ?>-method-discount" name="form-discount-<?php echo esc_attr( $gateway->id ); ?>-method">
                  <span id="discount-method-result-payment-method-<?php echo esc_attr( $gateway->id ); ?>" class="input-group-text discount-method-result-payment-method" name="discount-method-result-payment-method[<?php echo esc_attr( $gateway->id ); ?>][type]">
                     <?php echo ( isset( $discount_settings[$gateway->id]['type'] ) && $discount_settings[$gateway->id]['type'] === 'percentage' ) ? '%' : get_woocommerce_currency_symbol(); ?>
                  </span>

                  <input type="text" class="form-control allow-number-and-dots input-control-wd-5 text-center <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>" value="<?php echo esc_attr( $current ); ?>" id="woo_custom_installments_payment_discounts_<?php echo esc_attr( $gateway->id ); ?>" name="woo_custom_installments_discounts[<?php echo esc_attr( $gateway->id ); ?>][amount]"/>
                  
                  <select class="form-select get-discount-method-payment-method <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>" id="woo-custom-installments-payment-discounts-type-<?php echo esc_attr( $gateway->id ); ?>" name="woo_custom_installments_discounts[<?php echo esc_attr( $gateway->id ); ?>][type]">
                     <option value="fixed" <?php echo ( isset( $discount_settings[$gateway->id]['type'] ) && $discount_settings[$gateway->id]['type'] === 'fixed' ) ? 'selected="selected"' : ''; ?> ><?php echo sprintf( __( 'Valor fixo (%s)', 'woo-custom-installments' ), get_woocommerce_currency_symbol() ) ?></span></option>
                     <option value="percentage" <?php echo ( isset( $discount_settings[$gateway->id]['type'] ) && $discount_settings[$gateway->id]['type'] === 'percentage' ) ? 'selected="selected"' : ''; ?> ><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></span></option>
                  </select>
               </div>
            </td>
         </tr>
      <?php endforeach; ?>
    </table>
</div>