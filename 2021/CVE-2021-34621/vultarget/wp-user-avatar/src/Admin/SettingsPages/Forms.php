<?php

namespace ProfilePress\Core\Admin\SettingsPages;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\DragDropBuilder;
use ProfilePress\Core\Classes\FormRepository as FR;
use ProfilePress\Custom_Settings_Page_Api;

// Exit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}

class Forms extends AbstractSettingsPage
{
    /**
     * @var FormList
     */
    protected $forms_instance;

    protected $DragDropClassInstance;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'register_settings_page'));

        add_filter('set-screen-option', array($this, 'set_screen'), 10, 3);
        add_filter('set_screen_option_forms_per_page', array($this, 'set_screen'), 10, 3);

        $this->DragDropClassInstance = DragDropBuilder::get_instance();

        do_action('ppress_admin_forms_class_constructor');
    }

    public function admin_page_title()
    {
        $page_title = esc_html__('Forms & Profiles', 'wp-user-avatar');

        if (isset($_GET['view'])) {
            $page_title = esc_html__('Edit Form', 'wp-user-avatar');
        }

        if (isset($_GET['view']) && $_GET['view'] == 'add-new-form') {
            $page_title = esc_html__('Add Form', 'wp-user-avatar');
        }

        if (isset($_GET['view']) && $_GET['view'] == 'edit-shortcode-user-profile') {
            $page_title = esc_html__('Edit Frontend Profile', 'wp-user-avatar');
        }

        if (isset($_GET['view'], $_GET['form-type']) && $_GET['form-type'] == FR::USER_PROFILE_TYPE) {
            $page_title = esc_html__('Edit Frontend Profile', 'wp-user-avatar');
        }

        return $page_title;
    }

    public function register_settings_page()
    {
        $hook = add_submenu_page(
            PPRESS_SETTINGS_SLUG,
            $this->admin_page_title() . ' - ProfilePress',
            esc_html__('Forms & Profiles', 'wp-user-avatar'),
            'manage_options',
            PPRESS_FORMS_SETTINGS_SLUG,
            array($this, 'settings_admin_page_callback')
        );

        add_action("load-$hook", array($this, 'screen_option'));
    }

    /**
     * Sub-menu header for form types.
     */
    public function form_sub_header()
    {
        if ( ! empty($_GET['page']) && $_GET['page'] == PPRESS_FORMS_SETTINGS_SLUG) {
            $melange_jbox       = esc_html__('Melange combines login, registration & password reset forms in a single form.', 'wp-user-avatar');
            $login_url          = add_query_arg('form-type', FR::LOGIN_TYPE, PPRESS_FORMS_SETTINGS_PAGE);
            $registration_url   = add_query_arg('form-type', FR::REGISTRATION_TYPE, PPRESS_FORMS_SETTINGS_PAGE);
            $password_reset_url = add_query_arg('form-type', FR::PASSWORD_RESET_TYPE, PPRESS_FORMS_SETTINGS_PAGE);
            $edit_profile_url   = add_query_arg('form-type', FR::EDIT_PROFILE_TYPE, PPRESS_FORMS_SETTINGS_PAGE);
            $melange_url        = add_query_arg('form-type', FR::MELANGE_TYPE, PPRESS_FORMS_SETTINGS_PAGE);
            $user_profile_url   = add_query_arg('form-type', FR::USER_PROFILE_TYPE, PPRESS_FORMS_SETTINGS_PAGE);

            $login_menu_active          = (isset($_GET['page']) && ! isset($_GET['form-type'])) || isset($_GET['form-type']) && $_GET['page'] == PPRESS_FORMS_SETTINGS_SLUG && $_GET['form-type'] == FR::LOGIN_TYPE ? 'pp-type-active' : null;
            $registration_menu_active   = isset($_GET['form-type']) && $_GET['page'] == PPRESS_FORMS_SETTINGS_SLUG && $_GET['form-type'] == FR::REGISTRATION_TYPE ? 'pp-type-active' : null;
            $password_reset_menu_active = isset($_GET['form-type']) && $_GET['page'] == PPRESS_FORMS_SETTINGS_SLUG && $_GET['form-type'] == FR::PASSWORD_RESET_TYPE ? 'pp-type-active' : null;
            $edit_profile_menu_active   = isset($_GET['form-type']) && $_GET['page'] == PPRESS_FORMS_SETTINGS_SLUG && $_GET['form-type'] == FR::EDIT_PROFILE_TYPE ? 'pp-type-active' : null;
            $melange_menu_active        = isset($_GET['form-type']) && $_GET['page'] == PPRESS_FORMS_SETTINGS_SLUG && $_GET['form-type'] == FR::MELANGE_TYPE ? 'pp-type-active' : null;
            $user_profile_menu_active   = isset($_GET['form-type']) && $_GET['page'] == PPRESS_FORMS_SETTINGS_SLUG && $_GET['form-type'] == FR::USER_PROFILE_TYPE ? 'pp-type-active' : null;
            ?>
            <div id="pp-sub-bar">
                <div class="pp-new-toolbar pp-clear">
                    <h4><?php _e('Filter by Type', 'wp-user-avatar'); ?></h4>
                    <ul class="pp-design-options">
                        <li>
                            <a href="<?php echo $login_url; ?>" class="<?php echo $login_menu_active; ?>">
                                <?php _e('Login', 'wp-user-avatar'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $registration_url; ?>" class="<?php echo $registration_menu_active; ?>">
                                <?php _e('Registration', 'wp-user-avatar'); ?>
                            </a>
                        </li>
                        <li>
                        <li>
                            <a href="<?php echo $password_reset_url; ?>" class="<?php echo $password_reset_menu_active; ?>">
                                <?php _e('Password Reset', 'wp-user-avatar'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $edit_profile_url; ?>" class="<?php echo $edit_profile_menu_active; ?>">
                                <?php _e('Edit Profile', 'wp-user-avatar'); ?>
                            </a>
                        </li>

                        <?php if ( class_exists('ProfilePress\Libsodium\Libsodium')) : ?>
                            <li>
                                <a href="<?php echo $melange_url; ?>" class="<?php echo $melange_menu_active; ?>">
                                    <?php _e('Melange', 'wp-user-avatar'); ?>
                                </a>
                                <span class="pp-melange-jbox dashicons dashicons-editor-help" title="<?php echo $melange_jbox; ?>"></span>
                            </li>
                        <?php endif; ?>

                        <li>
                            <a href="<?php echo $user_profile_url; ?>" class="<?php echo $user_profile_menu_active; ?>">
                                <?php _e('User Profile', 'wp-user-avatar'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php }
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
            'label'   => esc_html__('Forms', 'wp-user-avatar'),
            'default' => 10,
            'option'  => 'forms_per_page',
        ];

        add_screen_option('per_page', $args);

        $this->forms_instance = FormList::get_instance();
    }

    public function live_form_preview_btn($echo = true)
    {
        if ( ! isset($_GET['view'])) return;

        $form_type = isset($_GET['form-type']) ? sanitize_text_field($_GET['form-type']) : '';

        switch ($_GET['view']) {
            case 'edit-shortcode-login':
                $form_type = FR::LOGIN_TYPE;
                break;
            case 'edit-shortcode-registration':
                $form_type = FR::REGISTRATION_TYPE;
                break;
            case 'edit-shortcode-password-reset':
                $form_type = FR::PASSWORD_RESET_TYPE;
                break;
            case 'edit-shortcode-melange':
                $form_type = FR::MELANGE_TYPE;
                break;
            case 'edit-shortcode-edit-profile':
                $form_type = FR::EDIT_PROFILE_TYPE;
                break;
            case 'edit-shortcode-user-profile':
                $form_type = FR::USER_PROFILE_TYPE;
                break;
        }

        $preview_url = add_query_arg(
            ['pp_preview_form' => absint($_GET['id']), 'type' => $form_type],
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

        if ( ! empty($_GET['view']) && $_GET['view'] == 'add-new-form') {
            return AddNewForm::get_instance()->settings_admin_page();
        }


        $short_circuit = apply_filters('ppress_forms_settings_admin_page_short_circuit', false);

        if (false !== $short_circuit) return $short_circuit;

        if ( ! empty($_GET['view'])) {

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
        add_action('wp_cspa_before_post_body_content', array($this, 'form_sub_header'), 10, 2);
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
        $url = add_query_arg('view', 'add-new-form', PPRESS_FORMS_SETTINGS_PAGE);
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

        $this->forms_instance->prepare_items();

        ob_start();

        $this->forms_instance->display();

        return ob_get_clean();
    }

    /**
     * @return Forms
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