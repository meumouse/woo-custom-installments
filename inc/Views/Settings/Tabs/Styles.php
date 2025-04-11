<?php

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\Views\Components;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="styles" class="nav-content">
   <table class="form-table">
      <tr>
        <th>
           <?php esc_html_e( 'Forçar prioridade dos estilos', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php esc_html_e('Ative essa opção para que os estilos tenham maior prioridade.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="enable_force_styles" name="enable_force_styles" value="yes" <?php checked( Admin_Options::get_setting('enable_force_styles') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php esc_html_e( 'Centralizar grupo de preços na grade de produtos', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php esc_html_e('Se ativo, irá centralizar a melhor parcela e desconto na grade de produtos.', 'woo-custom-installments' ) ?></span>
         </th>

         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="center_group_elements_loop" name="center_group_elements_loop" value="yes" <?php checked( Admin_Options::get_setting('center_group_elements_loop') === 'yes' ); ?> />
            
               <button id="center_group_elements_trigger" class="btn btn-outline-primary ms-3 require-center-group-elements"><?php esc_html_e( 'Configurar', 'woo-custom-installments' ) ?></button>
            
               <div id="center_group_elements_container" class="popup-container">
                  <div class="popup-content">
                     <div class="popup-header">
                        <h5 class="popup-title"><?php esc_html_e( 'Configurar centralização de elementos', 'woo-custom-installments' ); ?></h5>
                        <button id="center_group_elements_close" class="btn-close fs-lg" aria-label="<?php esc_html_e( 'Fechar', 'woo-custom-installments' ) ?>"></button>
                     </div>

                     <div class="popup-body">
                        <table class="popup-table">
                           <tbody>
                              <tr>
                                 <th>
                                    <?php esc_html_e( 'Seletores alvo', 'woo-custom-installments' ) ?>
                                    <span class="woo-custom-installments-description"><?php esc_html_e('Permite definir os seletores a serem centralizados ao centro.', 'woo-custom-installments' ) ?></span>
                                 </th>
                                 <td>
                                    <textarea class="form-control" id="selectors_group_for_center_elements" name="selectors_group_for_center_elements"><?php echo Admin_Options::get_setting('selectors_group_for_center_elements'); ?></textarea>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </td>
      </tr>

      <tr>
        <th>
           <?php esc_html_e( 'Ativar widgets para Elementor', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php esc_html_e('Ative essa opção para que os widgets para Elementor sejam carregados.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="enable_elementor_widgets" name="enable_elementor_widgets" value="yes" <?php checked( Admin_Options::get_setting('enable_elementor_widgets') === 'yes' ); ?> />
           </div>
        </td>
      </tr>

      <tr>
        <th>
           <?php esc_html_e( 'Empilhamento de preços em widgets', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php esc_html_e('Se ativo, a opção "Ativar empilhamento de preços" será incluída nos widgets para Elementor.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="enable_price_grid_in_widgets" name="enable_price_grid_in_widgets" value="yes" <?php checked( Admin_Options::get_setting('enable_price_grid_in_widgets') == 'yes' ); ?> />
           </div>
        </td>
      </tr>

      <tr>
        <th>
           <?php esc_html_e( 'Ativar atualização de valores em elementos em produtos variáveis', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php esc_html_e('Ative esta opção para que elementos de preço dentro do popup ou sanfona em produtos variáveis sejam atualizados em AJAX.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="enable_update_variation_prices_elements" name="enable_update_variation_prices_elements" value="yes" <?php checked( Admin_Options::get_setting('enable_update_variation_prices_elements') === 'yes' ); ?> />
           </div>
        </td>
      </tr>

      <tr>
         <th>
            <?php esc_html_e( 'Formato de ícones', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php esc_html_e('Selecione o formato de exibição dos ícones dos elementos.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <select id="icon_format_elements" class="form-select input-control-wd-20" name="icon_format_elements">
               <option value="class" <?php echo ( Admin_Options::get_setting('icon_format_elements') === 'class' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'Classe de ícone (Font Awesome) - (Padrão)', 'woo-custom-installments' ) ?></option>
               <option value="upload" <?php echo ( Admin_Options::get_setting('icon_format_elements') === 'upload' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'Enviar ícones', 'woo-custom-installments' ) ?></option>
            </select>
         </td>
      </tr>

      <tr class="container-separator"></tr>

      <tr>
         <th>
            <?php esc_html_e( 'Estilos dos elementos', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php esc_html_e( 'Edite os estilos clicando no botão editar, ou reposicione a ordem dos elementos arrastando e soltando.', 'woo-custom-installments' ) ?></span>
         </th>

         <td>
            <div id="reorder_wci_elements">
               <ul class="sortable">
                  <?php foreach ( Admin_Options::get_setting('elements_design') as $element => $value ) : ?>

                     <li id="<?php esc_attr_e( $value['id'] ) ?>" class="tab-item" name="<?php esc_attr_e( $element ) ?>" style="">
                        <svg class="icon icon-lg icon-dark me-3 handle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M18 11h-5V6h3l-4-4-4 4h3v5H6V8l-4 4 4 4v-3h5v5H8l4 4 4-4h-3v-5h5v3l4-4-4-4z"></path></svg>

                        <input type="hidden" class="change-priority" name="elements_design[<?php esc_attr_e( $element ) ?>][order]" value="<?php echo isset( $value['order'] ) ? $value['order'] : ''; ?>"/>

                        <div class="d-flex align-items-center justify-content-between w-100 edit-elements-design">
                           <div class="preview <?php esc_attr_e( $element ) ?>" data-device="desktop">
                              <?php if ( isset( $value['icon'] ) ) {
                                 if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) : ?>
                                    <i class="me-1 icon-class-preview <?php esc_attr_e( $value['icon']['class'] ) ?>"></i>
                                 <?php else : ?>
                                    <img class="me-1 icon-image-preview" src="<?php esc_attr_e( $value['icon']['image'] ) ?>"/>
                                 <?php endif;
                              }

                              echo $value['preview'] ?>
                           </div>
                           
                           <div class="d-flex align-items-center">
                              <span class="ms-5 badge bg-translucent-primary text-primary rounded-pill"><?php echo esc_html( $value['settings_title'] ) ?></span>

                              <button id="<?php esc_attr_e( $value['id'] ) ?>_trigger" class="btn btn-sm btn-outline-primary ms-3 rounded-3 modal-trigger"><?php esc_html_e( 'Editar', 'woo-custom-installments' ) ?></button>

                              <div id="<?php esc_attr_e( $value['id'] ) ?>_container" class="popup-container">
                                 <div class="popup-content">
                                    <div class="popup-header">
                                       <h5 class="popup-title"><?php printf( esc_html__( 'Configurar estilos: %s', 'woo-custom-installments' ), $value['settings_title'] ) ?></h5>
                                       <button id="<?php esc_attr_e( $value['id'] ) ?>_close" class="btn-close fs-lg" aria-label="<?php esc_attr_e( 'Fechar', 'woo-custom-installments' ) ?>"></button>
                                    </div>

                                    <div class="popup-body p-5">
                                       <?php echo Components::devices_wrapper( $element, $value ) ?>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </li>
                  <?php endforeach; ?>
               </ul>
            </div>
         </td>
      </tr>

      <tr class="container-separator"></tr>

      <tr>
         <th>
            <?php esc_html_e( 'Cor do botão do popup de parcelas', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php esc_html_e( 'A cor do texto e borda será obtida a partir da cor informada.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="input-group color-container">
               <input type="text" id="button_popup_color" name="button_popup_color" class="form-control input-color" data-format="rgb" data-opacity="1" data-position="bottom" value="<?php echo Admin_Options::get_setting('button_popup_color') ?>" size="25">
               <button class="btn btn-outline-secondary btn-icon reset-color wci-tooltip" data-color="#008aff" data-text="<?php esc_html_e( 'Redefinir para cor padrão', 'woo-custom-installments' ) ?>">
                  <svg class="icon-button" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3-3 1.331-3 3 1.329 3 3 3z"></path><path d="M20.817 11.186a8.94 8.94 0 0 0-1.355-3.219 9.053 9.053 0 0 0-2.43-2.43 8.95 8.95 0 0 0-3.219-1.355 9.028 9.028 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a6.961 6.961 0 0 1 2.502 1.053 7.005 7.005 0 0 1 1.892 1.892A6.967 6.967 0 0 1 19 13a7.032 7.032 0 0 1-.55 2.725 7.11 7.11 0 0 1-.644 1.188 7.2 7.2 0 0 1-.858 1.039 7.028 7.028 0 0 1-3.536 1.907 7.13 7.13 0 0 1-2.822 0 6.961 6.961 0 0 1-2.503-1.054 7.002 7.002 0 0 1-1.89-1.89A6.996 6.996 0 0 1 5 13H3a9.02 9.02 0 0 0 1.539 5.034 9.096 9.096 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9.09 9.09 0 0 0 1.814-.183 9.014 9.014 0 0 0 3.218-1.355 8.886 8.886 0 0 0 1.331-1.099 9.228 9.228 0 0 0 1.1-1.332A8.952 8.952 0 0 0 21 13a9.09 9.09 0 0 0-.183-1.814z"></path></svg>
               </button>
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php esc_html_e( 'Estilo do botão do popup de parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <select id="button_popup_size" class="form-select input-control-wd-10" name="button_popup_size">
               <option value="small" <?php echo ( Admin_Options::get_setting('button_popup_size') === 'small' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'Pequeno', 'woo-custom-installments' ) ?></option>
               <option value="normal" <?php echo ( Admin_Options::get_setting('button_popup_size') === 'normal' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'Normal', 'woo-custom-installments' ) ?></option>
               <option value="large" <?php echo ( Admin_Options::get_setting('button_popup_size') === 'large' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'Grande', 'woo-custom-installments' ) ?></option>
               <option value="link" <?php echo ( Admin_Options::get_setting('button_popup_size') === 'link' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'Link', 'woo-custom-installments' ) ?></option>
            </select>
         </td>
      </tr>

      <tr>
         <th>
            <?php esc_html_e( 'Margem superior do popup/sanfona de parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_top_popup_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo Admin_Options::get_setting( 'margin_top_popup_installments' ) ?>"/>
               <select id="unit_margin_top_popup_installments" class="form-select" name="unit_margin_top_popup_installments">
                  <option value="px" <?php echo ( Admin_Options::get_setting('unit_margin_top_popup_installments') === 'px' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( Admin_Options::get_setting('unit_margin_top_popup_installments') === 'em' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( Admin_Options::get_setting('unit_margin_top_popup_installments') === 'rem' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php esc_html_e( 'Margem inferior do popup/sanfona de parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_bottom_popup_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo Admin_Options::get_setting( 'margin_bottom_popup_installments' ) ?>"/>
               <select id="unit_margin_bottom_popup_installments" class="form-select" name="unit_margin_bottom_popup_installments">
                  <option value="px" <?php echo ( Admin_Options::get_setting('unit_margin_bottom_popup_installments') === 'px' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( Admin_Options::get_setting('unit_margin_bottom_popup_installments') === 'em' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( Admin_Options::get_setting('unit_margin_bottom_popup_installments') === 'rem' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php esc_html_e( 'Arredondamento do botão de parcelas', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php esc_html_e( 'Raio da borda do botão de popup de parcelas.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="border_radius_popup_installments" class="form-control input-control-wd-5 allow-number-and-dots" value="<?php echo Admin_Options::get_setting( 'border_radius_popup_installments' ) ?>"/>
               <select id="unit_border_radius_popup_installments" class="form-select" name="unit_border_radius_popup_installments">
                  <option value="px" <?php echo ( Admin_Options::get_setting('unit_border_radius_popup_installments') === 'px' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( Admin_Options::get_setting('unit_border_radius_popup_installments') === 'em' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( Admin_Options::get_setting('unit_border_radius_popup_installments') === 'rem' ) ? "selected=selected" : ""; ?>><?php esc_html_e( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
   </table>
</div>