<?php

namespace ProfilePress\Core\ContentProtection;

use ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage;
use ProfilePress\Core\Classes\PROFILEPRESS_sql;
use ProfilePress\Custom_Settings_Page_Api;

class SettingsPage extends AbstractSettingsPage
{
    public $content_protection_rule_errors;

    /** @var WPListTable */
    private $myListTable;

    const META_DATA_KEY = 'content_restrict_data';

    function __construct()
    {
        add_action('admin_menu', array($this, 'register_cpf_settings_page'));
        add_filter('set-screen-option', [__CLASS__, 'set_screen'], 10, 3);
        add_filter('set_screen_option_rules_per_page', [__CLASS__, 'set_screen'], 10, 3);
    }

    public function admin_page_title()
    {
        $title = esc_html__('Content Protection', 'wp-user-avatar');

        if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            $title = esc_html__('Edit Protection Rule', 'wp-user-avatar');
        }

        if (isset($_GET['add']) && $_GET['add'] == 'new') {
            $title = esc_html__('Add Protection Rule', 'wp-user-avatar');
        }

        return $title;
    }

    public function register_cpf_settings_page()
    {
        $hook = add_submenu_page(
            PPRESS_SETTINGS_SLUG,
            $this->admin_page_title() . ' - ProfilePress',
            esc_html__('Content Protection', 'wp-user-avatar'),
            'manage_options',
            PPRESS_CONTENT_PROTECTION_SETTINGS_SLUG,
            array($this, 'settings_page_function'));

        add_action("load-$hook", array($this, 'add_options'));
    }

    public static function set_screen($status, $option, $value)
    {
        return $value;
    }

    public function add_options()
    {
        $args = [
            'label'   => esc_html__('Protection Rules', 'wp-user-avatar'),
            'default' => 10,
            'option'  => 'rules_per_page'
        ];

        add_screen_option('per_page', $args);

        $this->myListTable = new WPListTable();
    }

    public function sanitize_data($data)
    {
        if (is_string($data)) {
            return sanitize_text_field($data);
        }

        $sanitized_data = [];

        foreach ($data as $key => $value) {

            if (is_array($data[$key])) {
                $sanitized_data[$key] = self::sanitize_data($data[$key]);
            } else {
                $sanitized_data[$key] = sanitize_text_field($data[$key]);
            }
        }

        return $sanitized_data;
    }

    public function save_rule($type)
    {
        if ( ! isset($_POST['ppress_save_rule'])) return;

        check_admin_referer('wp-csa-nonce', 'wp_csa_nonce');

        if ( ! current_user_can('manage_options')) return;

        if (empty($_POST['ppress_cc_data']['title'])) {
            return $this->content_protection_rule_errors = esc_html__('Title cannot be empty.', 'wp-user-avatar');
        }

        if ('add' == $type) {
            $id = PROFILEPRESS_sql::add_meta_data(self::META_DATA_KEY, $this->sanitize_data($_POST['ppress_cc_data']));

            if (is_int($id)) {
                wp_safe_redirect(add_query_arg(['action' => 'edit', 'id' => $id, 'rule-updated' => 'true'], PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE));
                exit;
            }
        }

        if ('edit' == $type) {
            $rule_id  = absint($_GET['id']);
            $response = PROFILEPRESS_sql::update_meta_value($rule_id, self::META_DATA_KEY, $this->sanitize_data($_POST['ppress_cc_data']));

            if (false !== $response) {
                wp_safe_redirect(add_query_arg(['action' => 'edit', 'id' => $rule_id, 'rule-updated' => 'true'], PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE));
                exit;
            }
        }
    }

    public function admin_notices()
    {
        if ( ! isset($_GET['rule-updated']) && ! isset($this->content_protection_rule_errors)) return;

        $status = 'updated';
        if (isset($this->content_protection_rule_errors)) {
            $message = $this->content_protection_rule_errors;
            $status  = 'error';
        }

        if (isset($_GET['rule-updated'])) {
            $message = esc_html__('Changes saved.', 'wp-user-avatar');
        }

        printf('<div id="message" class="%s notice is-dismissible"><p>%s</strong></p></div>', $status, $message);
    }

    public function settings_page_function()
    {
        add_action('wp_cspa_main_content_area', array($this, 'admin_page_callback'), 10, 2);
        add_action('wp_cspa_before_closing_header', [$this, 'add_new_button']);

        $instance = Custom_Settings_Page_Api::instance();
        $instance->option_name('ppview'); // adds ppview css class to #poststuff
        $instance->page_header($this->admin_page_title());
        $this->register_core_settings($instance, true);
        $instance->build(true);
    }

    public function add_new_button()
    {
        if ( ! isset($_GET['add']) && ! isset($_GET['action'])) {
            $url = esc_url_raw(add_query_arg('add', 'new', PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE));
            echo "<a class=\"add-new-h2\" href=\"$url\">" . esc_html__('Add a Protection Rule', 'wp-user-avatar') . '</a>';
        }
    }

    public function admin_page_callback()
    {
        $this->myListTable->prepare_items(); // has to be here.

        if (isset($_GET['add']) && $_GET['add'] == 'new') {
            $this->save_rule('add');
        }

        if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            $this->save_rule('edit');
        }

        if (isset($_GET['add']) || isset($_GET['action'])) {
            $this->admin_notices();
            require_once dirname(__FILE__) . '/views/include.view.php';

            return;
        }

        echo '<form method="post">';
        $this->myListTable->display();
        echo '</form>';

        do_action('ppress_content_protection_wp_list_table_bottom');
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}