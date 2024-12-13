<?php

use MeuMouse\Woo_Custom_Installments\Helpers;

/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package MeuMouse.com
 * @since 4.5.0
 * @version 5.2.6
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

$product = wc_get_product( Helpers::get_product_id_from_post() );

if ( $product === false ) {
    global $product;
}

// check if product is defined
if ( ! $product || ! is_a( $product, 'WC_Product' ) ) :
    echo '<p>' . esc_html__( 'Produto não encontrado.', 'woo-custom-installments' ) . '</p>';
    return;
endif; ?>

<p id="woo-custom-installments-product-price" class="woo-custom-installments-price-container <?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
    <?php echo $product->get_price_html(); ?>
</p>