<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Provides logging functionality for classes that use it
 * Allows setting a source for logs and optionally logs only critical events
 * 
 * @since 5.4.0
 * @package MeuMouse\Woo_Custom_Installments\Core
 * @author MeuMouse.com
 */
class Logger {
  
    /**
     * The source identifier for the log entries
     *
     * @since 5.4.0
     * @var string
     */
    public static $source;

    /**
     * Flag to determine if only critical logs should be saved
     *
     * @since 5.4.0
     * @var bool
     */
    public static $critical_only;

    /**
     * WooCommerce logger instance
     *
     * @since 5.4.0
     * @var WC_Logger
     */
    public static $log;

    
    /**
     * Set the source for the logger and whether to log only critical events.
     *
     * @since 5.4.0
     * @param string $set | The source identifier for the logs.
     * @param bool $critical_only | Whether to log only critical events, default true.
     * @return void
     */
    public static function set_logger_source( $set, $critical_only = true ) {
        self::$source = $set;
        self::$critical_only = $critical_only;
    }


    /**
     * Log an event
     *
     * Logs a message with the given severity level. If $critical_only is true,
     * only logs messages with levels 'emergency', 'alert', or 'critical'
     *
     * @since 5.4.0
     * @param string $message | The log message
     * @param string $level | Optional, defaults to 'info'. Valid levels: emergency|alert|critical|error|warning|notice|info|debug
     * @return void
     */
    public static function register_log( $message, $level = 'info' ) {
        if ( ! self::$source ) {
            return;
        }

        // check if the level is valid
        if ( self::$critical_only && ! in_array( $level, array( 'emergency', 'alert', 'critical' ) ) ) {
            return;
        }

        // Ensure the message is a string
        $message = is_string( $message ) ? $message : print_r( $message, true );

        if ( ! isset( self::$log ) ) {
            self::$log = wc_get_logger();
        }

        // Log the message
        self::$log->log( $level, $message, array( 'source' => self::$source ) );
    }
}