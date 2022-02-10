<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if (! class_exists('QUBELY_Initial_Setup')) {
    class QUBELY_Initial_Setup{
        protected static $_instance = null;
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        // Constructor
        public function __construct() {
            register_activation_hook(__FILE__, array($this, 'qubely_option_data'));
        }

        // Init Options Data Init
        public function qubely_option_data() {
            $option_data = array('css_save_as' => 'wp_head');
            if (!get_option('qubely_options')) {
                update_option('qubely_options', $option_data);
            }
        }

        // PHP Error Notice
        public static function php_error_notice(){
            $message = sprintf( esc_html__( 'QUBELY Blocks requires PHP version %s or more.', 'qubely' ), '5.4' );
            $html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
            echo wp_kses_post( $html_message );
        }

        // Wordpress Error Notice
        public static function wordpress_error_notice(){
            $message = sprintf( esc_html__( 'QUBELY Blocks requires WordPress version %s or more.', 'qubely'), '4.7' );
            $html_message = sprintf( '<div class="notice notice-error is-dismissible">%s</div>', wpautop( $message ) );
            echo wp_kses_post( $html_message );
        }
    }
}
