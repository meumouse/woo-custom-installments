<?php

namespace MeuMouse\Woo_Custom_Installments\Cron;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\Core\Updater;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for handle with Cron routines
 * 
 * @since 5.4.0
 * @package MeuMouse.com
 */
class Routines {

	/**
	 * Construct function
	 *
	 * @since 5.4.0
	 * @return void
	 */
	public function __construct() {
        // enable auto updates
        if ( Admin_Options::get_setting('enable_auto_updates') === 'yes' ) {
            // Schedule the cron event if not already scheduled
            if ( ! wp_next_scheduled('Woo_Custom_Installments/Updates/Auto_Updates') ) {
                wp_schedule_event( time(), 'daily', 'Woo_Custom_Installments/Updates/Auto_Updates' );
            }

            $updater = new Updater();

            // auto update plugin action
            add_action( 'Woo_Custom_Installments/Updates/Auto_Updates', array( $updater, 'auto_update_plugin' ) );
        }

        // schedule daily updates
        if ( ! wp_next_scheduled('Woo_Custom_Installments/Updates/Check_Daily_Updates') ) {
            wp_schedule_event( time(), 'daily', 'Woo_Custom_Installments/Updates/Check_Daily_Updates' );
        }

        // check daily updates
        add_action( 'Woo_Custom_Installments/Updates/Check_Daily_Updates', array( '\MeuMouse\Woo_Custom_Installments\Core\Updater', 'check_daily_updates' ) );
	}
}