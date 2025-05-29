<?php

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
 * @version 5.4.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

if ( ! $product ) {
    global $product;
}

// check if product is defined
if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
    echo '<p>' . esc_html__( 'Produto não encontrado.', 'woo-custom-installments' ) . '</p>';

    return;
}

/**
 * Custom hook for display content before price
 * 
 * @since 5.4.0
 * @param object $product | Product object
 * @return void
 */
do_action( 'Woo_Custom_Installments/Product/Before_Price', $product );

/**
 * Add custom classes on woo-custom-installments-group element
 * 
 * @since 5.3.0
 * @version 5.4.0
 * @return string
 */
$group_classes = apply_filters( 'Woo_Custom_Installments/Price/Group_Classes', '', $product ); ?>

<p id="woo-custom-installments-product-price" class="woo-custom-installments-price-container <?php echo esc_attr( $group_classes ) ?> <?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
    <?php 
    var_dump( 'ID do produto: ' . $product->get_id() );
    echo $product->get_price_html(); ?>
</p>

<?php
/**
 * Custom hook for display content after price
 * 
 * @since 5.4.0
 * @param object $product | Product object
 * @return void
 */
do_action( 'Woo_Custom_Installments/Product/After_Price', $product ); ?>