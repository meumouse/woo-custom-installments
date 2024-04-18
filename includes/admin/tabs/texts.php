<?php

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="text" class="nav-content">
    <table class="form-table">
        <tr>
            <th>
                <?php echo esc_html__( 'Texto antes do preço com desconto', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_before_price" value="<?php echo self::get_setting( 'text_before_price' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto depois do preço com desconto', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_after_price" value="<?php echo self::get_setting( 'text_after_price' ) ?>"/>
            </td>
        </tr>
        <tr class="tr-custom-text-after-price">
            <th>
                <?php echo esc_html__( 'Texto depois do preço do produto', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="custom_text_after_price_front" value="<?php echo self::get_setting( 'custom_text_after_price_front' ) ?>"/>
            </td>
        </tr>
        <tr class="starting-from">
            <th>
                <?php echo esc_html__( 'Texto inicial em produtos variáveis (A partir de)', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_initial_variables" value="<?php echo self::get_setting( 'text_initial_variables' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo para economia no Pix', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será exibido no emblema de economia ao pagar com Pix. Obs.: É necessário que a variável %s esteja na frase para exibir o valor.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_economy_pix_badge" value="<?php echo self::get_setting( 'text_economy_pix_badge' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo para desconto por quantidade', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será exibido no emblema de desconto por quantidade. Obs.: É necessário que a variável %d (Quantidade) e %s (Desconto) estejam na frase para exibir o valor.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_discount_per_quantity_message" value="<?php echo self::get_setting( 'text_discount_per_quantity_message' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Título do botão acionador de parcelas', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será exibido no botão do popup ou sanfona de parcelas.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_button_installments" value="<?php echo self::get_setting( 'text_button_installments' ) ?>"/>
            </td>
        </tr>
        <tr>
            <td class="container-separator"></td>
        </tr>
        <tr class="admin-container-transfers">
            <th>
                <?php echo esc_html__( 'Título do container de transferências', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será usado como título do container de transferências.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_pix_container" value="<?php echo self::get_setting( 'text_pix_container' ) ?>"/>
            </td>
        </tr>
        <tr class="admin-container-ticket">
            <th>
                <?php echo esc_html__( 'Título do container de boleto bancário', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será usado como título do container de boleto bancário.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_ticket_container" value="<?php echo self::get_setting( 'text_ticket_container' ) ?>"/>
            </td>
        </tr>
        <tr class="admin-container-ticket">
            <th>
                <?php echo esc_html__( 'Texto de instruções de boleto bancário', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto de instruções que será exibido no container de boleto bancário.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_instructions_ticket_container" value="<?php echo self::get_setting( 'text_instructions_ticket_container' ) ?>"/>
            </td>
        </tr>
        <tr class="admin-container-ticket">
            <th>
                <?php echo esc_html__( 'Texto antes do preço com desconto no boleto bancário', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_before_discount_ticket" value="<?php echo self::get_setting( 'text_before_discount_ticket' ) ?>"/>
            </td>
        </tr>
        <tr class="admin-container-ticket">
            <th>
                <?php echo esc_html__( 'Texto depois do preço com desconto no boleto bancário', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_after_discount_ticket" value="<?php echo self::get_setting( 'text_after_discount_ticket' ) ?>"/>
            </td>
        </tr>
        <tr class="admin-container-credit-card">
            <th>
                <?php echo esc_html__( 'Título do container de cartões de crédito', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será usado como título do container de cartões de crédito.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_credit_card_container" value="<?php echo self::get_setting( 'text_credit_card_container' ) ?>"/>
            </td>
        </tr>
        <tr class="admin-container-debit-card">
            <th>
                <?php echo esc_html__( 'Título do container de cartões de débito', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será usado como título do container de cartões de débito.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_debit_card_container" value="<?php echo self::get_setting( 'text_debit_card_container' ) ?>"/>
            </td>
        </tr>
        <tr>
            <td class="container-separator payment-forms d-none"></td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Título da tabela de parcelas', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será exibido antes da tabela de parcelas no popup ou sanfona.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_table_installments" value="<?php echo self::get_setting( 'text_table_installments' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo de parcelas com juros', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será exibido depois da parcela.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_with_fee_installments" value="<?php echo self::get_setting( 'text_with_fee_installments' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo de parcelas sem juros', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Texto que será exibido depois da parcela.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_without_fee_installments" value="<?php echo self::get_setting( 'text_without_fee_installments' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Título do container das formas de pagamento', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_container_payment_forms" name="text_container_payment_forms" value="<?php echo self::get_setting( 'text_container_payment_forms' ); ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto de exibição das parcelas (Formas de pagamento)', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description mb-2"><?php echo esc_html__( 'Utilize as variáveis:', 'woo-custom-installments' ) ?></span>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ parcelas }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar o número de parcelas.', 'woo-custom-installments' ) ?></span>
                </div>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ valor }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar o valor da parcela.', 'woo-custom-installments' ) ?></span>
                </div>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ juros }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar se a parcela é com ou sem juros.', 'woo-custom-installments' ) ?></span>
                </div>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ total }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar o valor total da soma das parcelas.', 'woo-custom-installments' ) ?></span>
                </div>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_display_installments_payment_forms" value="<?php echo self::get_setting( 'text_display_installments_payment_forms' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto de exibição das parcelas (Arquivos de produtos)', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description mb-2"><?php echo esc_html__( 'Utilize as variáveis:', 'woo-custom-installments' ) ?></span>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ parcelas }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar o número de parcelas.', 'woo-custom-installments' ) ?></span>
                </div>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ valor }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar o valor da parcela.', 'woo-custom-installments' ) ?></span>
                </div>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ juros }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar se a parcela é com ou sem juros.', 'woo-custom-installments' ) ?></span>
                </div>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ total }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar o valor total da soma das parcelas.', 'woo-custom-installments' ) ?></span>
                </div>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_display_installments_loop" value="<?php echo self::get_setting( 'text_display_installments_loop' ) ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Texto de exibição das parcelas (Produto individual)', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description mb-2"><?php echo esc_html__( 'Utilize as variáveis:', 'woo-custom-installments' ) ?></span>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ parcelas }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar o número de parcelas.', 'woo-custom-installments' ) ?></span>
                </div>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ valor }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar o valor da parcela.', 'woo-custom-installments' ) ?></span>
                </div>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ juros }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar se a parcela é com ou sem juros.', 'woo-custom-installments' ) ?></span>
                </div>
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ total }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php echo esc_html__( 'Para recuperar o valor total da soma das parcelas.', 'woo-custom-installments' ) ?></span>
                </div>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_display_installments_single_product" value="<?php echo self::get_setting( 'text_display_installments_single_product' ) ?>"/>
            </td>
        </tr>
    </table>
</div>

