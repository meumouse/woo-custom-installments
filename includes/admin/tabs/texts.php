<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit; } ?>

<div id="text-settings" class="nav-content ">
    <table class="form-table" >
        <tr>
            <th>
                <?php echo esc_html__( 'Texto antes do preço com desconto', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_before_price" value="<?php echo $this->getSetting( 'text_before_price' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto depois do preço com desconto', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_after_price" value="<?php echo $this->getSetting( 'text_after_price' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto inicial em produtos variáveis (A partir de)', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_initial_variables" value="<?php echo $this->getSetting( 'text_initial_variables' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Título do botão acionador de parcelas', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será exibido no botão do popup ou sanfona de parcelas.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_button_installments" value="<?php echo $this->getSetting( 'text_button_installments' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Título do container de transferências', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será usado como título do container de transferências.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_pix_container" value="<?php echo $this->getSetting( 'text_pix_container' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Título do container de boleto bancário', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será usado como título do container de boleto bancário.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_ticket_container" value="<?php echo $this->getSetting( 'text_ticket_container' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto de instruções de boleto bancário', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto de instruções que será exibido no container de boleto bancário.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_instructions_ticket_container" value="<?php echo $this->getSetting( 'text_instructions_ticket_container' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Título do container de cartões de crédito', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será usado como título do container de cartões de crédito.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_credit_card_container" value="<?php echo $this->getSetting( 'text_credit_card_container' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Título do container de cartões de débito', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será usado como título do container de cartões de débito.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_debit_card_container" value="<?php echo $this->getSetting( 'text_debit_card_container' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Título da tabela de parcelas', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será exibido antes da tabela de parcelas no popup ou sanfona.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_table_installments" value="<?php echo $this->getSetting( 'text_table_installments' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo de parcelas com juros', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será exibido depois da parcela.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_with_fee_installments" value="<?php echo $this->getSetting( 'text_with_fee_installments' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo de parcelas sem juros', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será exibido depois da parcela.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_without_fee_installments" value="<?php echo $this->getSetting( 'text_without_fee_installments' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Título do container das formas de pagamento', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_container_payment_forms" name="text_container_payment_forms" value="<?php echo $this->getSetting( 'text_container_payment_forms' ); ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto de exibição das parcelas (Formas de pagamento)', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Utilize as variáveis: {{ parcelas }}, {{ valor }}, {{ total }} e {{ juros }}', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_display_installments_payment_forms" value="<?php echo $this->getSetting( 'text_display_installments_payment_forms' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto de exibição das parcelas (Arquivos de produtos)', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Utilize as variáveis: {{ parcelas }}, {{ valor }}, {{ total }} e {{ juros }}', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_display_installments_loop" value="<?php echo $this->getSetting( 'text_display_installments_loop' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto de exibição das parcelas (Produto individual)', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Utilize as variáveis: {{ parcelas }}, {{ valor }}, {{ total }} e {{ juros }}', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_display_installments_single_product" value="<?php echo $this->getSetting( 'text_display_installments_single_product' ) ?>"/>
            </td>
        </tr>
    </table>
</div>

