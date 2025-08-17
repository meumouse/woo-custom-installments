<?php

namespace MeuMouse\Woo_Custom_Installments\Views;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Change colors on front-end
 *
 * @since 2.1.0
 * @version 5.5.1
 * @package MeuMouse.com
 */
class Styles {

    /**
     * Construct function
     * 
     * @since 2.1.0
     * @version 5.4.0
     * @return void
     */
    public function __construct() {
        // add frontend styles on header
        add_action( 'wp_head', array( $this, 'render_styles' ) );

        add_action( 'admin_head', array( $this, 'adjustment_styles_product_list' ) );
    }


    /**
     * Custom CSS for frontend
     * 
     * @since 2.0.0
     * @version 5.5.1
     * @return string
     */
    public function render_styles() {
        $button_popup_color = Admin_Options::get_setting('button_popup_color');
        $button_popup_size = Admin_Options::get_setting('button_popup_size');
        $discount_pix_styles = Admin_Options::get_setting('elements_design')['discount_pix']['styles'];
        $economy_pix_styles = Admin_Options::get_setting('elements_design')['pix_economy']['styles'];
        $installments_styles = Admin_Options::get_setting('elements_design')['installments']['styles'];
        $ticket_styles = Admin_Options::get_setting('elements_design')['discount_slip_bank']['styles'];
        $price_styles = Admin_Options::get_setting('elements_design')['price']['styles'];

        /**
         * Filter to disable important styles
         * 
         * @since 5.0.0
         * @version 5.5.1
         * @param bool $important
         * @return bool
         */
        $force_important = apply_filters( 'Woo_Custom_Installments/Styles/Force_Important', false );

        $important = $force_important ? '!important' : '';

        $css = "
            .woo-custom-installments-group .woo-custom-installments-offer {
                order: ". Admin_Options::get_setting('elements_design')['discount_pix']['order'] .";
            }

            .woo-custom-installments-group .woo-custom-installments-economy-pix-badge {
                order: ". Admin_Options::get_setting('elements_design')['pix_economy']['order'] .";
            }

            .woo-custom-installments-group .woo-custom-installments-card-container {
                order: ". Admin_Options::get_setting('elements_design')['installments']['order'] .";
            }

            .woo-custom-installments-group .woo-custom-installments-ticket-discount {
                order: ". Admin_Options::get_setting('elements_design')['discount_slip_bank']['order'] .";
            }

            .woo-custom-installments-group .woo-custom-installments-group-main-price {
                order: ". Admin_Options::get_setting('elements_design')['price']['order'] .";
            }
                 
            @media screen and (max-width: 992px) {
                .woo-custom-installments-group .woo-custom-installments-offer {
                    font-size: ". $discount_pix_styles['mobile']['font_size'] . $discount_pix_styles['mobile']['font_unit'] .";
                    font-weight: ". $discount_pix_styles['mobile']['font_weight'] .";
                    color: ". $discount_pix_styles['mobile']['font_color'] .";
                    background-color: ". $discount_pix_styles['mobile']['background_color'] .";
                    margin: ". Components::format_box_property( $discount_pix_styles['mobile']['margin'], $discount_pix_styles['mobile']['margin']['unit'] ?? 'px' ) .";
                    padding: ". Components::format_box_property( $discount_pix_styles['mobile']['padding'], $discount_pix_styles['mobile']['padding']['unit'] ?? 'px' ) .";
                    border-radius: ". Components::format_box_property( $discount_pix_styles['mobile']['border_radius'], $discount_pix_styles['mobile']['border_radius']['unit'] ?? 'px' ) .";
                }

                .woo-custom-installments-group .woo-custom-installments-offer .amount {
                    font-size: ". $discount_pix_styles['mobile']['font_size'] . $discount_pix_styles['mobile']['font_unit'] . $important .";
                    font-weight: ". $discount_pix_styles['mobile']['font_weight'] .";
                    color: ". $discount_pix_styles['mobile']['font_color'] .";
                }

                .woo-custom-installments-group .woo-custom-installments-economy-pix-badge {
                    font-size: ". $economy_pix_styles['mobile']['font_size'] . $economy_pix_styles['mobile']['font_unit'] .";
                    font-weight: ". $economy_pix_styles['mobile']['font_weight'] .";
                    color: ". $economy_pix_styles['mobile']['font_color'] .";
                    background-color: ". $economy_pix_styles['mobile']['background_color'] .";
                    margin: ". Components::format_box_property( $economy_pix_styles['mobile']['margin'], $economy_pix_styles['mobile']['margin']['unit'] ?? 'px' ) .";
                    padding: ". Components::format_box_property( $economy_pix_styles['mobile']['padding'], $economy_pix_styles['mobile']['padding']['unit'] ?? 'px' ) .";
                    border-radius: ". Components::format_box_property( $economy_pix_styles['mobile']['border_radius'], $economy_pix_styles['mobile']['border_radius']['unit'] ?? 'px' ) .";
                }

                .woo-custom-installments-group .woo-custom-installments-economy-pix-badge .amount {
                    font-size: ". $economy_pix_styles['mobile']['font_size'] . $economy_pix_styles['mobile']['font_unit'] . $important .";
                    font-weight: ". $economy_pix_styles['mobile']['font_weight'] .";
                    color: ". $economy_pix_styles['mobile']['font_color'] .";
                }

                .woo-custom-installments-group .woo-custom-installments-card-container {
                    font-size: ". $installments_styles['mobile']['font_size'] . $installments_styles['mobile']['font_unit'] .";
                    font-weight: ". $installments_styles['mobile']['font_weight'] .";
                    color: ". $installments_styles['mobile']['font_color'] .";
                    background-color: ". $installments_styles['mobile']['background_color'] .";
                    margin: ". Components::format_box_property( $installments_styles['mobile']['margin'], $installments_styles['mobile']['margin']['unit'] ?? 'px' ) .";
                    padding: ". Components::format_box_property( $installments_styles['mobile']['padding'], $installments_styles['mobile']['padding']['unit'] ?? 'px' ) .";
                    border-radius: ". Components::format_box_property( $installments_styles['mobile']['border_radius'], $installments_styles['mobile']['border_radius']['unit'] ?? 'px' ) .";
                }

                .woo-custom-installments-group .woo-custom-installments-card-container .amount {
                    font-size: ". $installments_styles['mobile']['font_size'] . $installments_styles['mobile']['font_unit'] . $important .";
                    font-weight: ". $installments_styles['mobile']['font_weight'] .";
                    color: ". $installments_styles['mobile']['font_color'] .";
                }

                .woo-custom-installments-group .woo-custom-installments-ticket-discount {
                    font-size: ". $ticket_styles['mobile']['font_size'] . $ticket_styles['mobile']['font_unit'] .";
                    font-weight: ". $ticket_styles['mobile']['font_weight'] .";
                    color: ". $ticket_styles['mobile']['font_color'] .";
                    background-color: ". $ticket_styles['mobile']['background_color'] .";
                    margin: ". Components::format_box_property( $ticket_styles['mobile']['margin'], $ticket_styles['mobile']['margin']['unit'] ?? 'px' ) .";
                    padding: ". Components::format_box_property( $ticket_styles['mobile']['padding'], $ticket_styles['mobile']['padding']['unit'] ?? 'px' ) .";
                    border-radius: ". Components::format_box_property( $ticket_styles['mobile']['border_radius'], $ticket_styles['mobile']['border_radius']['unit'] ?? 'px' ) .";
                }

                .woo-custom-installments-group .woo-custom-installments-ticket-discount .amount {
                    font-size: ". $ticket_styles['mobile']['font_size'] . $ticket_styles['mobile']['font_unit'] . $important .";
                    font-weight: ". $ticket_styles['mobile']['font_weight'] .";
                    color: ". $ticket_styles['mobile']['font_color'] .";
                }

                .woo-custom-installments-group .woo-custom-installments-group-main-price {
                    font-size: ". $price_styles['mobile']['font_size'] . $price_styles['mobile']['font_unit'] .";
                    font-weight: ". $price_styles['mobile']['font_weight'] .";
                    color: ". $price_styles['mobile']['font_color'] .";
                    background-color: ". $price_styles['mobile']['background_color'] .";
                    margin: ". Components::format_box_property( $price_styles['mobile']['margin'], $price_styles['mobile']['margin']['unit'] ?? 'px' ) .";
                    padding: ". Components::format_box_property( $price_styles['mobile']['padding'], $price_styles['mobile']['padding']['unit'] ?? 'px' ) .";
                    border-radius: ". Components::format_box_property( $price_styles['mobile']['border_radius'], $price_styles['mobile']['border_radius']['unit'] ?? 'px' ) .";
                }
                    
                .woo-custom-installments-group .woo-custom-installments-group-main-price .amount {
                    font-size: ". $price_styles['mobile']['font_size'] . $price_styles['mobile']['font_unit'] . $important .";
                    font-weight: ". $price_styles['mobile']['font_weight'] .";
                    color: ". $price_styles['mobile']['font_color'] .";
                }
            }

            @media screen and (min-width: 1024px) {
                .woo-custom-installments-group .woo-custom-installments-offer {
                    font-size: ". $discount_pix_styles['desktop']['font_size'] . $discount_pix_styles['desktop']['font_unit'] .";
                    font-weight: ". $discount_pix_styles['desktop']['font_weight'] .";
                    color: ". $discount_pix_styles['desktop']['font_color'] .";
                    background-color: ". $discount_pix_styles['desktop']['background_color'] .";
                    margin: ". Components::format_box_property( $discount_pix_styles['desktop']['margin'], $discount_pix_styles['desktop']['margin']['unit'] ?? 'px' ) .";
                    padding: ". Components::format_box_property( $discount_pix_styles['desktop']['padding'], $discount_pix_styles['desktop']['padding']['unit'] ?? 'px' ) .";
                    border-radius: ". Components::format_box_property( $discount_pix_styles['desktop']['border_radius'], $discount_pix_styles['desktop']['border_radius']['unit'] ?? 'px' ) .";
                }

                .woo-custom-installments-group .woo-custom-installments-offer .amount {
                    font-size: ". $discount_pix_styles['desktop']['font_size'] . $discount_pix_styles['desktop']['font_unit'] . $important .";
                    font-weight: ". $discount_pix_styles['desktop']['font_weight'] .";
                    color: ". $discount_pix_styles['desktop']['font_color'] .";
                }

                .woo-custom-installments-group .woo-custom-installments-economy-pix-badge {
                    font-size: ". $economy_pix_styles['desktop']['font_size'] . $economy_pix_styles['desktop']['font_unit'] .";
                    font-weight: ". $economy_pix_styles['desktop']['font_weight'] .";
                    color: ". $economy_pix_styles['desktop']['font_color'] .";
                    background-color: ". $economy_pix_styles['desktop']['background_color'] .";
                    margin: ". Components::format_box_property( $economy_pix_styles['desktop']['margin'], $economy_pix_styles['desktop']['margin']['unit'] ?? 'px' ) .";
                    padding: ". Components::format_box_property( $economy_pix_styles['desktop']['padding'], $economy_pix_styles['desktop']['padding']['unit'] ?? 'px' ) .";
                    border-radius: ". Components::format_box_property( $economy_pix_styles['desktop']['border_radius'], $economy_pix_styles['desktop']['border_radius']['unit'] ?? 'px' ) .";
                }

                .woo-custom-installments-group .woo-custom-installments-economy-pix-badge .amount {
                    font-size: ". $economy_pix_styles['desktop']['font_size'] . $economy_pix_styles['desktop']['font_unit'] . $important .";
                    font-weight: ". $economy_pix_styles['desktop']['font_weight'] .";
                    color: ". $economy_pix_styles['desktop']['font_color'] .";
                }
            }

            @media screen and (min-width: 1024px) {
                .woo-custom-installments-group .woo-custom-installments-card-container {
                    font-size: ". $installments_styles['desktop']['font_size'] . $installments_styles['desktop']['font_unit'] .";
                    font-weight: ". $installments_styles['desktop']['font_weight'] .";
                    color: ". $installments_styles['desktop']['font_color'] .";
                    background-color: ". $installments_styles['desktop']['background_color'] .";
                    margin: ". Components::format_box_property( $installments_styles['desktop']['margin'], $installments_styles['desktop']['margin']['unit'] ?? 'px' ) .";
                    padding: ". Components::format_box_property( $installments_styles['desktop']['padding'], $installments_styles['desktop']['padding']['unit'] ?? 'px' ) .";
                    border-radius: ". Components::format_box_property( $installments_styles['desktop']['border_radius'], $installments_styles['desktop']['border_radius']['unit'] ?? 'px' ) .";
                }

                .woo-custom-installments-group .woo-custom-installments-card-container .amount {
                    font-size: ". $installments_styles['desktop']['font_size'] . $installments_styles['desktop']['font_unit'] . $important .";
                    font-weight: ". $installments_styles['desktop']['font_weight'] .";
                    color: ". $installments_styles['desktop']['font_color'] .";
                }

                .woo-custom-installments-group .woo-custom-installments-ticket-discount {
                    font-size: ". $ticket_styles['desktop']['font_size'] . $ticket_styles['desktop']['font_unit'] .";
                    font-weight: ". $ticket_styles['desktop']['font_weight'] .";
                    color: ". $ticket_styles['desktop']['font_color'] .";
                    background-color: ". $ticket_styles['desktop']['background_color'] .";
                    margin: ". Components::format_box_property( $ticket_styles['desktop']['margin'], $ticket_styles['desktop']['margin']['unit'] ?? 'px' ) .";
                    padding: ". Components::format_box_property( $ticket_styles['desktop']['padding'], $ticket_styles['desktop']['padding']['unit'] ?? 'px' ) .";
                    border-radius: ". Components::format_box_property( $ticket_styles['desktop']['border_radius'], $ticket_styles['desktop']['border_radius']['unit'] ?? 'px' ) .";
                }

                .woo-custom-installments-group .woo-custom-installments-ticket-discount .amount {
                    font-size: ". $ticket_styles['desktop']['font_size'] . $ticket_styles['desktop']['font_unit'] . $important .";
                    font-weight: ". $ticket_styles['desktop']['font_weight'] .";
                    color: ". $ticket_styles['desktop']['font_color'] .";
                }
                    
                .woo-custom-installments-group .woo-custom-installments-group-main-price {
                    font-size: ". $price_styles['desktop']['font_size'] . $price_styles['desktop']['font_unit'] .";
                    font-weight: ". $price_styles['desktop']['font_weight'] .";
                    color: ". $price_styles['desktop']['font_color'] .";
                    background-color: ". $price_styles['desktop']['background_color'] .";
                    margin: ". Components::format_box_property( $price_styles['desktop']['margin'], $price_styles['desktop']['margin']['unit'] ?? 'px' ) .";
                    padding: ". Components::format_box_property( $price_styles['desktop']['padding'], $price_styles['desktop']['padding']['unit'] ?? 'px' ) .";
                    border-radius: ". Components::format_box_property( $price_styles['desktop']['border_radius'], $price_styles['desktop']['border_radius']['unit'] ?? 'px' ) .";
                }

                .woo-custom-installments-group .woo-custom-installments-group-main-price .amount {
                    font-size: ". $price_styles['desktop']['font_size'] . $price_styles['desktop']['font_unit'] . $important .";
                    font-weight: ". $price_styles['desktop']['font_weight'] .";
                    color: ". $price_styles['desktop']['font_color'] .";
                }

                .woo-custom-installments-group .woo-custom-installments-price.original-price.has-discount .amount,
                .woo-custom-installments-group .woo-custom-installments-group-main-price del .amount {
                    font-size: calc(". $price_styles['desktop']['font_size'] . $price_styles['desktop']['font_unit'] ." - 0.3rem)" . $important .";
                }
            }

            button.wci-open-popup, #wci-accordion-installments {
                margin-top: " . Admin_Options::get_setting('margin_top_popup_installments') . Admin_Options::get_setting('unit_margin_top_popup_installments') .";
                margin-bottom: " . Admin_Options::get_setting('margin_bottom_popup_installments') . Admin_Options::get_setting('unit_margin_bottom_popup_installments') .";
            }

            button.wci-open-popup {
                color: ". $button_popup_color .";
                border-color: ". $button_popup_color .";
                border-radius: " . Admin_Options::get_setting('border_radius_popup_installments') . Admin_Options::get_setting('unit_border_radius_popup_installments') .";
            }

            button.wci-open-popup:hover {
                background-color: ". $button_popup_color .";
            }";

        if ( $button_popup_size === 'small' ) {
            $css .= "
                button.wci-open-popup {
                    padding: 0.475rem 1.25rem;
                    font-size: 0.75rem;
                }";
        } elseif ( $button_popup_size === 'normal' ) {
            $css .= "
                button.wci-open-popup {
                    padding: 0.625rem 1.75rem;
                    font-size: 0.875rem;
                }";
        } elseif ($button_popup_size === 'large') {
            $css .= "
                button.wci-open-popup {
                    padding: 0.785rem 2rem;
                    font-size: 1rem;
                }";
        } else {
            $css .= "
                button.wci-open-popup {
                    display: inline-block;
                    position: relative;
                    font-weight: 500;
                    text-decoration: none;
                    border: 0;
                    padding: 0;
                    color: {$button_popup_color};
                }

                button.wci-open-popup::after {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    width: 100%;
                    height: 0.0625rem;
                    background-color: {$button_popup_color};
                }

                button.wci-open-popup:hover {
                    text-decoration: none;
                    color: {$button_popup_color};
                    background-color: transparent;
                }

                button.wci-open-popup:hover::after {
                    -webkit-animation: linkUnderline .6s ease-in-out;
                    animation: linkUnderline .6s ease-in-out;
                }
                    
                @-webkit-keyframes linkUnderline {
                    0% { width: 100%; }
                    50% { width: 0; }
                    100% { left: 0; width: 100%; }
                }

                @keyframes linkUnderline {
                    0% { width: 100%; }
                    50% { width: 0; }
                    100% { left: 0; width: 100%; }
                }";
        }

        if ( Admin_Options::get_setting('center_group_elements_loop') === 'yes' ) {
            $css .= Admin_Options::get_setting('selectors_group_for_center_elements') . " {
                justify-items: center !important;
                align-items: center !important;
                justify-content: center !important;
            }";
        }

        if ( Admin_Options::get_setting('remove_price_range') === 'yes' ) {
            $css .= ".woocommerce-variation-price {
                display: none !important;
            }";
        }

        printf( '<style type="text/css">%s</style>', $css );
    }


    /**
     * Display badge in CSS for get pro in plugins page
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @access public
     */
    public static function be_pro_badge_styles() {
        ob_start(); ?>

        #get-pro-woo-custom-installments {
            display: inline-block;
            padding: 0.35em 0.6em;
            font-size: 0.8125em;
            font-weight: 600;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            background-color: #008aff;
            transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
        }
        
        #get-pro-woo-custom-installments:hover {
            background-color: #0078ed;
        }

        <?php $css = ob_get_clean();
        $css = wp_strip_all_tags( $css );

        printf( __('<style>%s</style>'), $css );
    }

    
    /**
	 * Hide installments info on WooCommerce product table on admin page
	 * 
	 * @since 4.3.5
	 * @version 5.4.0
	 * @return void
	 */
	public function adjustment_styles_product_list() {
        ob_start(); ?>

		.woo-custom-installments-offer,
		.woo-custom-installments-economy-pix-badge,
		.woo-custom-installments-ticket-discount,
		.wci-sale-badge {
			display: none;
		}

		.woo-custom-installments-starting-from {
			margin-right: 0.25rem;
		}

		table.wp-list-table .column-price {
			width: 10rem;
		}

		.woo-custom-installments-price {
			display: block;
		}

		.woo-custom-installments-price.has-discount {
			text-decoration: line-through;
		}

        <?php $css = ob_get_clean();
        $css = wp_strip_all_tags( $css );

		printf( __('<style>%s</style>'), $css );
	}
}