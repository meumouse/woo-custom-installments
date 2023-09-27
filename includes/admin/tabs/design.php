<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
  
?>

<div id="design-settings" class="nav-content ">
   <table class="form-table" >
      <tr>
        <th>
           <?php echo esc_html__( 'Centralizar melhor parcela e desconto na grade de produtos', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá centralizar a melhor parcela e desconto na grade de produtos.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="center_group_elements_loop" name="center_group_elements_loop" value="yes" <?php checked( $this->getSetting( 'center_group_elements_loop') == 'yes' ); ?> />
           </div>
        </td>
      </tr>
      <tr>
         <td class="container-separator"></td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Cor do preço com desconto', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'A cor de fundo será gerada automaticamente com 10% de transparência a partir da cor informada. Esta configuração também altera a cor do emblema de aprovação imediata e desconto no checkout.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <input type="color" name="discount_main_price_color" class="form-control-color" value="<?php echo $this->getSetting( 'discount_main_price_color' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Tamanho da fonte do preço com desconto', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="font_size_discount_price" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'font_size_discount_price' ) ?>"/>
               <select id="unit_font_size_discount_price" class="form-select" name="unit_font_size_discount_price">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_font_size_discount_price' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_font_size_discount_price' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_font_size_discount_price' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Margem superior do preço com desconto', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_top_discount_price" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'margin_top_discount_price' ) ?>"/>
               <select id="unit_margin_top_discount_price" class="form-select" name="unit_margin_top_discount_price">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_margin_top_discount_price' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_margin_top_discount_price' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_margin_top_discount_price' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Margem inferior do preço com desconto', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_bottom_discount_price" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'margin_bottom_discount_price' ) ?>"/>
               <select id="unit_margin_bottom_discount_price" class="form-select" name="unit_margin_bottom_discount_price">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_margin_bottom_discount_price' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_margin_bottom_discount_price' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_margin_bottom_discount_price' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Arredondamento do preço com desconto', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Raio da borda do emblema do preço com desconto.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="border_radius_discount_main_price" class="form-control input-control-wd-5 allow-number-and-dots" value="<?php echo $this->getSetting( 'border_radius_discount_main_price' ) ?>"/>
               <select id="unit_border_radius_discount_main_price" class="form-select" name="unit_border_radius_discount_main_price">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_border_radius_discount_main_price' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_border_radius_discount_main_price' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_border_radius_discount_main_price' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Ícone do preço com desconto', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Informe uma classe de ícone do Font Awesome. Ou deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span><a class="fancy-link mt-2" href="https://fontawesome.com/search?o=r&m=free" target="_blank"><?php echo esc_html__( 'Acessar Font Awesome', 'woo-custom-installments' ) ?></a>
         </th>
         <td>
            <input type="text" name="icon_main_price" class="form-control input-control-wd-10" placeholder="fa-brands fa-pix" value="<?php echo $this->getSetting( 'icon_main_price' ) ?>"/>
         </td>
      </tr>
      <tr>
         <td class="container-separator admin-container-ticket"></td>
      </tr>
      <tr class="admin-container-ticket">
         <th>
            <?php echo esc_html__( 'Cor do desconto no boleto bancário', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'A cor de fundo será gerada automaticamente com 10% de transparência a partir da cor informada.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <input type="color" name="discount_ticket_color_badge" class="form-control-color" value="<?php echo $this->getSetting( 'discount_ticket_color_badge' ) ?>"/>
         </td>
      </tr>
      <tr class="admin-container-ticket">
         <th>
            <?php echo esc_html__( 'Tamanho da fonte do desconto no boleto bancário', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="font_size_discount_ticket" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'font_size_discount_ticket' ) ?>"/>
               <select id="unit_font_size_discount_ticket" class="form-select" name="unit_font_size_discount_ticket">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_font_size_discount_ticket' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_font_size_discount_ticket' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_font_size_discount_ticket' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr class="admin-container-ticket">
         <th>
            <?php echo esc_html__( 'Margem superior do desconto no boleto bancário', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_top_discount_ticket" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'margin_top_discount_ticket' ) ?>"/>
               <select id="unit_margin_top_discount_ticket" class="form-select" name="unit_margin_top_discount_ticket">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_margin_top_discount_ticket' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_margin_top_discount_ticket' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_margin_top_discount_ticket' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr class="admin-container-ticket">
         <th>
            <?php echo esc_html__( 'Margem inferior do desconto no boleto bancário', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_bottom_discount_ticket" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'margin_bottom_discount_ticket' ) ?>"/>
               <select id="unit_margin_bottom_discount_ticket" class="form-select" name="unit_margin_bottom_discount_ticket">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_margin_bottom_discount_ticket' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_margin_bottom_discount_ticket' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_margin_bottom_discount_ticket' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr class="admin-container-ticket">
         <th>
            <?php echo esc_html__( 'Arredondamento do desconto no boleto bancário', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Raio da borda do emblema de desconto no boleto bancário.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="border_radius_discount_ticket" class="form-control input-control-wd-5 allow-number-and-dots" value="<?php echo $this->getSetting( 'border_radius_discount_ticket' ) ?>"/>
               <select id="unit_border_radius_discount_ticket" class="form-select" name="unit_border_radius_discount_ticket">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_border_radius_discount_ticket' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_border_radius_discount_ticket' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_border_radius_discount_ticket' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Ícone do desconto no boleto bancário', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Informe uma classe de ícone do Font Awesome. Ou deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <input type="text" name="ticket_discount_icon" class="form-control input-control-wd-10" placeholder="fa-solid fa-barcode" value="<?php echo $this->getSetting( 'ticket_discount_icon' ) ?>"/>
         </td>
      </tr>
      <tr>
         <td class="container-separator"></td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Cor do botão do popup de parcelas', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'A cor do texto e borda será obtida a partir da cor informada.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <input type="color" name="button_popup_color" class="form-control-color" value="<?php echo $this->getSetting( 'button_popup_color' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Estilo do botão do popup de parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <select id="button_popup_size" class="form-select input-control-wd-10" name="button_popup_size">
               <option value="small" <?php echo ( $this->getSetting( 'button_popup_size' ) == 'small' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Pequeno', 'woo-custom-installments' ) ?></option>
               <option value="normal" <?php echo ( $this->getSetting( 'button_popup_size' ) == 'normal' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Normal', 'woo-custom-installments' ) ?></option>
               <option value="large" <?php echo ( $this->getSetting( 'button_popup_size' ) == 'large' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Grande', 'woo-custom-installments' ) ?></option>
               <option value="link" <?php echo ( $this->getSetting( 'button_popup_size' ) == 'link' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Link', 'woo-custom-installments' ) ?></option>
            </select>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Margem superior do popup/sanfona de parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_top_popup_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'margin_top_popup_installments' ) ?>"/>
               <select id="unit_margin_top_popup_installments" class="form-select" name="unit_margin_top_popup_installments">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_margin_top_popup_installments' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_margin_top_popup_installments' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_margin_top_popup_installments' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Margem inferior do popup/sanfona de parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_bottom_popup_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'margin_bottom_popup_installments' ) ?>"/>
               <select id="unit_margin_bottom_popup_installments" class="form-select" name="unit_margin_bottom_popup_installments">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_margin_bottom_popup_installments' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_margin_bottom_popup_installments' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_margin_bottom_popup_installments' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Arredondamento do botão de parcelas', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Raio da borda do botão de popup de parcelas.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="border_radius_popup_installments" class="form-control input-control-wd-5 allow-number-and-dots" value="<?php echo $this->getSetting( 'border_radius_popup_installments' ) ?>"/>
               <select id="unit_border_radius_popup_installments" class="form-select" name="unit_border_radius_popup_installments">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_border_radius_popup_installments' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_border_radius_popup_installments' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_border_radius_popup_installments' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <td class="container-separator"></td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Cor de exibição das parcelas', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Cor para ser utilizada na informação de melhor parcela com ou sem juros.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <input type="color" name="best_installments_color" class="form-control-color" value="<?php echo $this->getSetting( 'best_installments_color' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Tamanho da fonte de exibição das parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="font_size_best_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'font_size_best_installments' ) ?>"/>
               <select id="unit_font_size_best_installments" class="form-select" name="unit_font_size_best_installments">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_font_size_best_installments' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_font_size_best_installments' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_font_size_best_installments' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Margem superior da exibição das parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_top_best_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'margin_top_best_installments' ) ?>"/>
               <select id="unit_margin_top_best_installments" class="form-select" name="unit_margin_top_best_installments">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_margin_top_best_installments' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_margin_top_best_installments' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_margin_top_best_installments' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Margem inferior da exibição das parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_bottom_best_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo $this->getSetting( 'margin_bottom_best_installments' ) ?>"/>
               <select id="unit_margin_bottom_best_installments" class="form-select" name="unit_margin_bottom_best_installments">
                  <option value="px" <?php echo ( $this->getSetting( 'unit_margin_bottom_best_installments' ) == 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( $this->getSetting( 'unit_margin_bottom_best_installments' ) == 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( $this->getSetting( 'unit_margin_bottom_best_installments' ) == 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Ícone de exibição das parcelas', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Informe uma classe de ícone do Font Awesome. Ou deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <input type="text" name="icon_best_installments" class="form-control input-control-wd-10" placeholder="fa-regular fa-credit-card" value="<?php echo $this->getSetting( 'icon_best_installments' ) ?>"/>
         </td>
      </tr>
   </table>
</div>