<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; ?>

<div id="design-settings" class="nav-content">
   <table class="form-table" >
      <tr>
        <th>
           <?php echo esc_html__( 'Centralizar melhor parcela e desconto na grade de produtos', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá centralizar a melhor parcela e desconto na grade de produtos.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="center_group_elements_loop" name="center_group_elements_loop" value="yes" <?php checked( self::get_setting('center_group_elements_loop') == 'yes' ); ?> />
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
            <div class="color-container input-group">
               <input type="color" name="discount_main_price_color" class="form-control-color" value="<?php echo self::get_setting( 'discount_main_price_color' ) ?>"/>
               <input type="text" class="get-color-selected form-control" value="<?php echo self::get_setting( 'discount_main_price_color' ) ?>"/>
               <button class="btn btn-outline-secondary btn-icon reset-color tooltip" data-color="#22c55e" data-text="<?php echo esc_html__( 'Redefinir para cor padrão', 'woo-custom-installments' ) ?>">
                  <svg class="icon-button" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3-3 1.331-3 3 1.329 3 3 3z"></path><path d="M20.817 11.186a8.94 8.94 0 0 0-1.355-3.219 9.053 9.053 0 0 0-2.43-2.43 8.95 8.95 0 0 0-3.219-1.355 9.028 9.028 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a6.961 6.961 0 0 1 2.502 1.053 7.005 7.005 0 0 1 1.892 1.892A6.967 6.967 0 0 1 19 13a7.032 7.032 0 0 1-.55 2.725 7.11 7.11 0 0 1-.644 1.188 7.2 7.2 0 0 1-.858 1.039 7.028 7.028 0 0 1-3.536 1.907 7.13 7.13 0 0 1-2.822 0 6.961 6.961 0 0 1-2.503-1.054 7.002 7.002 0 0 1-1.89-1.89A6.996 6.996 0 0 1 5 13H3a9.02 9.02 0 0 0 1.539 5.034 9.096 9.096 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9.09 9.09 0 0 0 1.814-.183 9.014 9.014 0 0 0 3.218-1.355 8.886 8.886 0 0 0 1.331-1.099 9.228 9.228 0 0 0 1.1-1.332A8.952 8.952 0 0 0 21 13a9.09 9.09 0 0 0-.183-1.814z"></path></svg>
               </button>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Tamanho da fonte do preço com desconto', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="font_size_discount_price" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'font_size_discount_price' ) ?>"/>
               <select id="unit_font_size_discount_price" class="form-select" name="unit_font_size_discount_price">
                  <option value="px" <?php echo ( self::get_setting( 'unit_font_size_discount_price' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_font_size_discount_price' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_font_size_discount_price' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
               <input type="text" name="margin_top_discount_price" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'margin_top_discount_price' ) ?>"/>
               <select id="unit_margin_top_discount_price" class="form-select" name="unit_margin_top_discount_price">
                  <option value="px" <?php echo ( self::get_setting( 'unit_margin_top_discount_price' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_margin_top_discount_price' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_margin_top_discount_price' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
               <input type="text" name="margin_bottom_discount_price" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'margin_bottom_discount_price' ) ?>"/>
               <select id="unit_margin_bottom_discount_price" class="form-select" name="unit_margin_bottom_discount_price">
                  <option value="px" <?php echo ( self::get_setting( 'unit_margin_bottom_discount_price' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_margin_bottom_discount_price' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_margin_bottom_discount_price' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
               <input type="text" name="border_radius_discount_main_price" class="form-control input-control-wd-5 allow-number-and-dots" value="<?php echo self::get_setting( 'border_radius_discount_main_price' ) ?>"/>
               <select id="unit_border_radius_discount_main_price" class="form-select" name="unit_border_radius_discount_main_price">
                  <option value="px" <?php echo ( self::get_setting( 'unit_border_radius_discount_main_price' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_border_radius_discount_main_price' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_border_radius_discount_main_price' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
            <input type="text" name="icon_main_price" class="form-control input-control-wd-10" placeholder="fa-brands fa-pix" value="<?php echo self::get_setting( 'icon_main_price' ) ?>"/>
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
            <div class="color-container input-group">
               <input type="color" name="discount_ticket_color_badge" class="form-control-color" value="<?php echo self::get_setting( 'discount_ticket_color_badge' ) ?>"/>
               <input type="text" class="get-color-selected form-control" value="<?php echo self::get_setting( 'discount_ticket_color_badge' ) ?>"/>
               <button class="btn btn-outline-secondary btn-icon reset-color tooltip" data-color="#ffba08" data-text="<?php echo esc_html__( 'Redefinir para cor padrão', 'woo-custom-installments' ) ?>">
                  <svg class="icon-button" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3-3 1.331-3 3 1.329 3 3 3z"></path><path d="M20.817 11.186a8.94 8.94 0 0 0-1.355-3.219 9.053 9.053 0 0 0-2.43-2.43 8.95 8.95 0 0 0-3.219-1.355 9.028 9.028 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a6.961 6.961 0 0 1 2.502 1.053 7.005 7.005 0 0 1 1.892 1.892A6.967 6.967 0 0 1 19 13a7.032 7.032 0 0 1-.55 2.725 7.11 7.11 0 0 1-.644 1.188 7.2 7.2 0 0 1-.858 1.039 7.028 7.028 0 0 1-3.536 1.907 7.13 7.13 0 0 1-2.822 0 6.961 6.961 0 0 1-2.503-1.054 7.002 7.002 0 0 1-1.89-1.89A6.996 6.996 0 0 1 5 13H3a9.02 9.02 0 0 0 1.539 5.034 9.096 9.096 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9.09 9.09 0 0 0 1.814-.183 9.014 9.014 0 0 0 3.218-1.355 8.886 8.886 0 0 0 1.331-1.099 9.228 9.228 0 0 0 1.1-1.332A8.952 8.952 0 0 0 21 13a9.09 9.09 0 0 0-.183-1.814z"></path></svg>
               </button>
            </div>
         </td>
      </tr>
      <tr class="admin-container-ticket">
         <th>
            <?php echo esc_html__( 'Tamanho da fonte do desconto no boleto bancário', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="font_size_discount_ticket" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'font_size_discount_ticket' ) ?>"/>
               <select id="unit_font_size_discount_ticket" class="form-select" name="unit_font_size_discount_ticket">
                  <option value="px" <?php echo ( self::get_setting( 'unit_font_size_discount_ticket' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_font_size_discount_ticket' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_font_size_discount_ticket' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
               <input type="text" name="margin_top_discount_ticket" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'margin_top_discount_ticket' ) ?>"/>
               <select id="unit_margin_top_discount_ticket" class="form-select" name="unit_margin_top_discount_ticket">
                  <option value="px" <?php echo ( self::get_setting( 'unit_margin_top_discount_ticket' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_margin_top_discount_ticket' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_margin_top_discount_ticket' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
               <input type="text" name="margin_bottom_discount_ticket" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'margin_bottom_discount_ticket' ) ?>"/>
               <select id="unit_margin_bottom_discount_ticket" class="form-select" name="unit_margin_bottom_discount_ticket">
                  <option value="px" <?php echo ( self::get_setting( 'unit_margin_bottom_discount_ticket' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_margin_bottom_discount_ticket' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_margin_bottom_discount_ticket' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
               <input type="text" name="border_radius_discount_ticket" class="form-control input-control-wd-5 allow-number-and-dots" value="<?php echo self::get_setting( 'border_radius_discount_ticket' ) ?>"/>
               <select id="unit_border_radius_discount_ticket" class="form-select" name="unit_border_radius_discount_ticket">
                  <option value="px" <?php echo ( self::get_setting( 'unit_border_radius_discount_ticket' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_border_radius_discount_ticket' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_border_radius_discount_ticket' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
            <input type="text" name="ticket_discount_icon" class="form-control input-control-wd-10" placeholder="fa-solid fa-barcode" value="<?php echo self::get_setting( 'ticket_discount_icon' ) ?>"/>
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
            <div class="color-container input-group">
               <input type="color" name="button_popup_color" class="form-control-color" value="<?php echo self::get_setting( 'button_popup_color' ) ?>"/>
               <input type="text" class="get-color-selected form-control" value="<?php echo self::get_setting( 'button_popup_color' ) ?>"/>
               <button class="btn btn-outline-secondary btn-icon reset-color tooltip" data-color="#008aff" data-text="<?php echo esc_html__( 'Redefinir para cor padrão', 'woo-custom-installments' ) ?>">
                  <svg class="icon-button" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3-3 1.331-3 3 1.329 3 3 3z"></path><path d="M20.817 11.186a8.94 8.94 0 0 0-1.355-3.219 9.053 9.053 0 0 0-2.43-2.43 8.95 8.95 0 0 0-3.219-1.355 9.028 9.028 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a6.961 6.961 0 0 1 2.502 1.053 7.005 7.005 0 0 1 1.892 1.892A6.967 6.967 0 0 1 19 13a7.032 7.032 0 0 1-.55 2.725 7.11 7.11 0 0 1-.644 1.188 7.2 7.2 0 0 1-.858 1.039 7.028 7.028 0 0 1-3.536 1.907 7.13 7.13 0 0 1-2.822 0 6.961 6.961 0 0 1-2.503-1.054 7.002 7.002 0 0 1-1.89-1.89A6.996 6.996 0 0 1 5 13H3a9.02 9.02 0 0 0 1.539 5.034 9.096 9.096 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9.09 9.09 0 0 0 1.814-.183 9.014 9.014 0 0 0 3.218-1.355 8.886 8.886 0 0 0 1.331-1.099 9.228 9.228 0 0 0 1.1-1.332A8.952 8.952 0 0 0 21 13a9.09 9.09 0 0 0-.183-1.814z"></path></svg>
               </button>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Estilo do botão do popup de parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <select id="button_popup_size" class="form-select input-control-wd-10" name="button_popup_size">
               <option value="small" <?php echo ( self::get_setting( 'button_popup_size' ) == 'small' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Pequeno', 'woo-custom-installments' ) ?></option>
               <option value="normal" <?php echo ( self::get_setting( 'button_popup_size' ) == 'normal' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Normal', 'woo-custom-installments' ) ?></option>
               <option value="large" <?php echo ( self::get_setting( 'button_popup_size' ) == 'large' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Grande', 'woo-custom-installments' ) ?></option>
               <option value="link" <?php echo ( self::get_setting( 'button_popup_size' ) == 'link' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Link', 'woo-custom-installments' ) ?></option>
            </select>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Margem superior do popup/sanfona de parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="margin_top_popup_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'margin_top_popup_installments' ) ?>"/>
               <select id="unit_margin_top_popup_installments" class="form-select" name="unit_margin_top_popup_installments">
                  <option value="px" <?php echo ( self::get_setting( 'unit_margin_top_popup_installments' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_margin_top_popup_installments' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_margin_top_popup_installments' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
               <input type="text" name="margin_bottom_popup_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'margin_bottom_popup_installments' ) ?>"/>
               <select id="unit_margin_bottom_popup_installments" class="form-select" name="unit_margin_bottom_popup_installments">
                  <option value="px" <?php echo ( self::get_setting( 'unit_margin_bottom_popup_installments' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_margin_bottom_popup_installments' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_margin_bottom_popup_installments' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
               <input type="text" name="border_radius_popup_installments" class="form-control input-control-wd-5 allow-number-and-dots" value="<?php echo self::get_setting( 'border_radius_popup_installments' ) ?>"/>
               <select id="unit_border_radius_popup_installments" class="form-select" name="unit_border_radius_popup_installments">
                  <option value="px" <?php echo ( self::get_setting( 'unit_border_radius_popup_installments' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_border_radius_popup_installments' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_border_radius_popup_installments' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
            <div class="color-container input-group">
            <input type="color" name="best_installments_color" class="form-control-color" value="<?php echo self::get_setting( 'best_installments_color' ) ?>"/>
               <input type="text" class="get-color-selected form-control" value="<?php echo self::get_setting( 'best_installments_color' ) ?>"/>
               <button class="btn btn-outline-secondary btn-icon reset-color tooltip" data-color="#343a40" data-text="<?php echo esc_html__( 'Redefinir para cor padrão', 'woo-custom-installments' ) ?>">
                  <svg class="icon-button" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3-3 1.331-3 3 1.329 3 3 3z"></path><path d="M20.817 11.186a8.94 8.94 0 0 0-1.355-3.219 9.053 9.053 0 0 0-2.43-2.43 8.95 8.95 0 0 0-3.219-1.355 9.028 9.028 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a6.961 6.961 0 0 1 2.502 1.053 7.005 7.005 0 0 1 1.892 1.892A6.967 6.967 0 0 1 19 13a7.032 7.032 0 0 1-.55 2.725 7.11 7.11 0 0 1-.644 1.188 7.2 7.2 0 0 1-.858 1.039 7.028 7.028 0 0 1-3.536 1.907 7.13 7.13 0 0 1-2.822 0 6.961 6.961 0 0 1-2.503-1.054 7.002 7.002 0 0 1-1.89-1.89A6.996 6.996 0 0 1 5 13H3a9.02 9.02 0 0 0 1.539 5.034 9.096 9.096 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9.09 9.09 0 0 0 1.814-.183 9.014 9.014 0 0 0 3.218-1.355 8.886 8.886 0 0 0 1.331-1.099 9.228 9.228 0 0 0 1.1-1.332A8.952 8.952 0 0 0 21 13a9.09 9.09 0 0 0-.183-1.814z"></path></svg>
               </button>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Tamanho da fonte de exibição das parcelas', 'woo-custom-installments' ) ?>
         </th>
         <td>
            <div class="input-group">
               <input type="text" name="font_size_best_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'font_size_best_installments' ) ?>"/>
               <select id="unit_font_size_best_installments" class="form-select" name="unit_font_size_best_installments">
                  <option value="px" <?php echo ( self::get_setting( 'unit_font_size_best_installments' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_font_size_best_installments' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_font_size_best_installments' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
               <input type="text" name="margin_top_best_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'margin_top_best_installments' ) ?>"/>
               <select id="unit_margin_top_best_installments" class="form-select" name="unit_margin_top_best_installments">
                  <option value="px" <?php echo ( self::get_setting( 'unit_margin_top_best_installments' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_margin_top_best_installments' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_margin_top_best_installments' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
               <input type="text" name="margin_bottom_best_installments" class="form-control input-control-wd-5 design-parameters" value="<?php echo self::get_setting( 'margin_bottom_best_installments' ) ?>"/>
               <select id="unit_margin_bottom_best_installments" class="form-select" name="unit_margin_bottom_best_installments">
                  <option value="px" <?php echo ( self::get_setting( 'unit_margin_bottom_best_installments' ) === 'px' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'px', 'woo-custom-installments' ) ?></option>
                  <option value="em" <?php echo ( self::get_setting( 'unit_margin_bottom_best_installments' ) === 'em' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'em', 'woo-custom-installments' ) ?></option>
                  <option value="rem" <?php echo ( self::get_setting( 'unit_margin_bottom_best_installments' ) === 'rem' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'rem', 'woo-custom-installments' ) ?></option>
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
            <input type="text" name="icon_best_installments" class="form-control input-control-wd-10" placeholder="fa-regular fa-credit-card" value="<?php echo self::get_setting( 'icon_best_installments' ) ?>"/>
         </td>
      </tr>
   </table>
</div>