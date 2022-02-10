<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if (!class_exists('QUBELY_Options')) {

    class QUBELY_Options
    {

        public $settings;

        public function __construct()
        {
            add_action('init', array($this, 'init'));
            add_action('admin_menu', array($this, 'admin_menu'));
        }

        public function init() {
            require __DIR__ . '/admin-views/Settings.php';
            $this->settings = new QUBELY_Settings();
        }

        /**
         * Add Menu Page Callback
         *
         * @since 1.0.0
         */
        public function admin_menu()
        {
            require __DIR__ . '/admin-views/Getting_Started.php';
            $getting_started = new QUBELY_Getting_Started();

            $parent_slug = 'qubely-settings';
            $cap = 'manage_options';

            add_menu_page(
                esc_html__('Qubely', 'qubely'),
                esc_html__('Qubely', 'qubely'),
                $cap,
                $parent_slug,
                array($this->settings, 'markup'),
                QUBELY_DIR_URL . 'assets/img/qubely-logo-white.svg'
            );

            add_submenu_page(
                $parent_slug,
                esc_html__('Qubely Settings', 'qubely'),
                esc_html__('Settings', 'qubely'),
                $cap,
                'qubely-settings',
                array($this->settings, 'markup')
            );

            add_submenu_page(
                $parent_slug,
                esc_html__('Getting Started', 'qubely'),
                esc_html__('Getting Started', 'qubely'),
                $cap,
                'qubely',
                array($getting_started, 'markup')
            );
        }
    }
}
