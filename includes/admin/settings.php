<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; } ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

<h1 class="wp-heading-inline wci_main_title"><?php echo get_admin_page_title() ?></h1>
<div id="wci_section_title-description">
    <p><?php echo esc_html__( 'Configure abaixo o parcelamento e descontos de produtos. Se precisar de ajuda para configurar, acesse nossa', 'woo-custom-installments' ) ?>
        <a class="fancy-link" href="https://meumouse.com/docs/plugins/parcelas-customizadas-para-woocommerce/" target="_blank"><?php echo esc_html__( 'Central de ajuda', 'woo-custom-installments' ) ?></a>
    </p>
    <?php
        if ( class_exists('WC_PagSeguro') || class_exists('PagSeguro_Internacional__WooCommerce') || class_exists( 'WC_PagSeguro_Parceled' ) ) {
            ?>
                <span class="woo-custom-installments-description"><?php echo __( 'O PagSeguro utiliza fator de multiplicação para calcular o juros.', 'woo-custom-installments' ) ?></span>
                <a class="fancy-link" href="https://meumouse.com/docs/plugins/parcelas-customizadas-para-woocommerce/#convert-fee" target="__blank"><?php echo __( 'Como converter fator de multiplicação para juros.', 'woo-custom-installments' ) ?></a>
            <?php
        }
    ?>
</div>

<?php

    if( $settingSaves === true) { ?>
        <div class="toast update-notice-wci">
            <div class="toast-header bg-success text-white">
                <i class="fa-regular fa-circle-check me-3"></i>
                <span class="me-auto"><?php _e( 'Salvo com sucesso', 'woo-custom-installments' ); ?></span>
                <button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"><?php _e( 'As configurações foram atualizadas!', 'woo-custom-installments' ); ?></div>
        </div>
        <?php
    }

    if( $activateLicense === true) { ?>
        <div class="toast update-notice-wci">
            <div class="toast-header bg-success text-white">
                <i class="fa-regular fa-circle-check me-3"></i>
                <span class="me-auto"><?php _e( 'Licença ativada com sucesso!', 'woo-custom-installments' ); ?></span>
                <button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"><?php _e( 'Todos os recursos da versão Pro agora estão ativos!', 'woo-custom-installments' ); ?></div>
        </div>
        <?php
    }

    if( $deactivateLicense === true) { ?>
        <div class="toast toast-warning update-notice-wci">
            <div class="toast-header bg-warning text-white">
                <i class="fa-regular fa-circle-check me-3"></i>
                <span class="me-auto"><?php _e( 'A licença foi desativada', 'woo-custom-installments' ); ?></span>
                <button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"><?php _e( 'Todos os recursos da versão Pro agora estão desativados!', 'woo-custom-installments' ); ?></div>
        </div>
        <?php
    }

    if( !empty( $this->showMessage ) && !empty( $this->licenseMessage ) ) { ?>
        <div class="toast toast-danger update-notice-wci">
            <div class="toast-header bg-danger text-white">
                <i class="fa-regular fa-circle-check me-3"></i>
                <span class="me-auto"><?php _e( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ); ?></span>
                <button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"><?php _e( $this->licenseMessage, 'woo-custom-installments' ); ?></div>
        </div>
        <?php
    }

    settings_errors(); ?>

<div class="woo-custom-installments-wrapper">
    <div class="nav-tab-wrapper woo-custom-installments-tab-wrapper">
        <a href="#general-settings" class="nav-tab nav-tab-active"><?php echo esc_html__( 'Geral', 'woo-custom-installments' ) ?></a>
        <a href="#text-settings" class="nav-tab "><?php echo esc_html__( 'Textos', 'woo-custom-installments' ) ?></a>
        <a href="#discount-settings" class="nav-tab "><?php echo esc_html__( 'Descontos', 'woo-custom-installments' ) ?></a>
        <a href="#interests-settings" class="nav-tab "><?php echo esc_html__( 'Acréscimos', 'woo-custom-installments' ) ?></a>
        <a href="#payment-form-settings" class="nav-tab "><?php echo esc_html__( 'Formas de pagamento', 'woo-custom-installments' ) ?></a>
        <a href="#design-settings" class="nav-tab "><?php echo esc_html__( 'Personalizar', 'woo-custom-installments' ) ?></a>
        <a href="#about-settings" class="nav-tab "><?php echo esc_html__( 'Sobre', 'woo-custom-installments' ) ?></a>
    </div>

    <form method="post" action="" class="woo-custom-installments-form" name="woo-custom-installments">
        <input type="hidden" name="woo-custom-installments" value="1"/>
        <?php
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/options.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/texts.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/discounts.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/interests.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/payment_forms.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/design.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/about.php';
        ?>
        <button id="save_settings" name="save_settings" class="btn btn-primary m-5 button-loading" type="submit">
            <span class="span-inside-button-loader"><?php esc_attr_e( 'Salvar alterações', 'woo-custom-installments' ); ?></span>
        </button>
    </form>
</div>