<?php

namespace ProfilePress\Core\Admin\SettingsPages;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\DragDropBuilder;
use ProfilePress\Core\Classes\FormRepository as FR;
use ProfilePress\Custom_Settings_Page_Api;

// Exit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}

class MemberDirectories extends AbstractSettingsPage
{
    /**
     * @var FormList
     */
    protected $wplist_instance;
    protected $EditShortcodeMemberDirectoriesInstance;
    protected $DragDropClassInstance;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'register_settings_page'));

        add_filter('set-screen-option', array($this, 'set_screen'), 10, 3);
        add_filter('set_screen_option_forms_per_page', array($this, 'set_screen'), 10, 3);

        $this->DragDropClassInstance = DragDropBuilder::get_instance();
    }

    public function admin_page_title()
    {
        $page_title = esc_html__('Member Directories', 'wp-user-avatar');

        if (isset($_GET['page'], $_GET['view']) && $_GET['page'] == PPRESS_MEMBER_DIRECTORIES_SLUG) {
            $page_title = esc_html__('Add Member Directory', 'wp-user-avatar');
        }

        if (isset($_GET['view'], $_GET['form-type']) && $_GET['form-type'] == FR::MEMBERS_DIRECTORY_TYPE) {
            $page_title = esc_html__('Edit Member Directory', 'wp-user-avatar');
        }

        return $page_title;
    }

    public function register_settings_page()
    {
        $hook = add_submenu_page(
            PPRESS_SETTINGS_SLUG,
            $this->admin_page_title() . ' - ProfilePress',
            esc_html__('Member Directories', 'wp-user-avatar'),
            'manage_options',
            PPRESS_MEMBER_DIRECTORIES_SLUG,
            array($this, 'settings_admin_page_callback')
        );

        add_action("load-$hook", array($this, 'screen_option'));
    }


    /**
     * Save screen option.
     *
     * @param string $status
     * @param string $option
     * @param string $value
     *
     * @return mixed
     */
    public function set_screen($status, $option, $value)
    {
        return $value;
    }

    /**
     * Screen options
     */
    public function screen_option()
    {
        if (isset($_GET['page'], $_GET['view']) && strpos($_GET['view'], 'edit-shortcode') !== false) return;

        $args = [
            'label'   => esc_html__('Member Directories', 'wp-user-avatar'),
            'default' => 10,
            'option'  => 'forms_per_page',
        ];

        add_screen_option('per_page', $args);

        $this->wplist_instance = MembersDirectoryList::get_instance();
    }

    public function live_form_preview_btn($echo = true)
    {
        if ( ! isset($_GET['view'])) return;

        $preview_url = add_query_arg(
            ['pp_preview_form' => absint($_GET['id']), 'type' => FR::MEMBERS_DIRECTORY_TYPE],
            home_url()
        );

        $html = "<a target='_blank' class=\"add-new-h2\" href=\"$preview_url\">" . esc_html__('Live Preview', 'wp-user-avatar') . '</a>';

        if ($echo === false) {
            return $html;
        }

        echo $html;
    }

    public function no_form_exist_redirect($form_id, $form_type)
    {
        if ( ! FR::form_id_exist($form_id, $form_type)) {
            wp_safe_redirect(add_query_arg('form-type', $form_type, PPRESS_FORMS_SETTINGS_PAGE));
            exit;
        }
    }

    /**
     * Build the settings page structure. I.e tab, sidebar.
     *
     * @return mixed|void
     */
    public function settings_admin_page_callback()
    {
        remove_all_actions('media_buttons');
        remove_all_filters('media_buttons_context');
        remove_all_filters('mce_buttons', 10);
        remove_all_filters('mce_external_plugins', 10);

        add_action('media_buttons', 'media_buttons');

        if ( ! empty($_GET['view']) && $_GET['view'] == 'add-new') {
            echo '<script type="text/javascript">var pp_is_member_directory = true;</script>';
            return AddNewForm::get_instance()->settings_admin_page();
        }

        if ( ! empty($_GET['view'])) {

            $form_id = absint($_GET['id']);

            $page_header = $this->admin_page_title();

            $shortcode_builder_page_header = sprintf(
                '<div class="wrap ppSCB"><h2>%s %s</h2><form method="post">%s',
                $page_header,
                $this->live_form_preview_btn(false),
                ppress_nonce_field()
            );

            if ($_GET['view'] == 'edit-shortcode-members-directory') {
                Forms::get_instance()->no_form_exist_redirect($form_id, FR::MEMBERS_DIRECTORY_TYPE);
                echo $shortcode_builder_page_header;
                $this->EditShortcodeMemberDirectoriesInstance->edit_screen();
                echo '</form></div>';

                return;
            }

            add_filter('wp_cspa_settings_page_sidebar', [$this->DragDropClassInstance, 'sidebar_section']);
            add_action('wp_cspa_before_closing_header', [$this, 'live_form_preview_btn']);

            add_action('wp_cspa_main_content_area', function ($content, $option_name) {
                if ($option_name != 'pp_edit_form') return $content;

                if ($_GET['view'] == 'drag-drop-builder') {
                    ob_start();
                    $this->DragDropClassInstance->admin_page();

                    return ob_get_clean();
                }
            }, 10, 2);

            $instance = Custom_Settings_Page_Api::instance();
            $instance->option_name('pp_edit_form');
            $instance->add_wrap_classes('pp-dnd-form-builder-wrap');
            $instance->page_header($this->admin_page_title());
            $this->register_core_settings($instance);

            return $instance->build();
        }

        add_action('wp_cspa_main_content_area', array($this, 'wp_list_table'), 10, 2);
        add_action('wp_cspa_before_closing_header', [$this, 'add_new_form_button']);

        $instance = Custom_Settings_Page_Api::instance();
        $instance->option_name(PPRESS_FORMS_DB_OPTION_NAME);
        $instance->page_header($this->admin_page_title());
        $this->register_core_settings($instance, true);
        echo '<div class="pp-form-listing pp-forms">';
        $instance->build(true);
        echo '</div>';
    }

    public function add_new_form_button()
    {
        $url = add_query_arg('view', 'add-new', PPRESS_MEMBER_DIRECTORIES_SETTINGS_PAGE);
        echo "<a class=\"add-new-h2\" href=\"$url\">" . esc_html__('Add New', 'wp-user-avatar') . '</a>';
    }

    /**
     * @param string $content
     * @param string $option_name settings Custom_Settings_Page_Api option name.
     *
     * @return string
     */
    public function wp_list_table($content, $option_name)
    {
        if ($option_name != PPRESS_FORMS_DB_OPTION_NAME) return $content;

        $this->wplist_instance->prepare_items(FR::MEMBERS_DIRECTORY_TYPE);

        ob_start();

        $this->wplist_instance->display();

        return ob_get_clean();
    }

    /**
     * @return self
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}