<?php

namespace MeuMouse\Woo_Custom_Installments\Compat;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Autoloader classes for compatibility with themes and plugins
 * 
 * @since 4.5.0
 * @package MeuMouse.com
 */
class Autoloader {

    /**
     * Constructor
     * 
     * @since 4.5.0
     * @return void
     */
    public function __construct() {
        $this->load_and_run();
    }
    

    /**
     * Load and run all compatibility classes
     * 
     * @since 4.5.0
     * @return void
     */
    public function load_and_run() {
        // iterate for each compat class on directory
        foreach ( glob( WOO_CUSTOM_INSTALLMENTS_INC . 'classes/compat/class-compat-*.php' ) as $file ) {
            include_once $file;
        }
    }
}

new Autoloader();