<?php

namespace ProfilePress\Core\Classes;

use ProfilePress\Core\Admin\SettingsPages\FormList;
use ProfilePress\Core\Base;
use ProfilePress\Core\Themes\Shortcode\ThemesRepository as ShortcodeThemesRepository;
use ProfilePress\Core\Themes\DragDrop\ThemesRepository as DragDropThemesRepository;
use ProfilePress\Core\Widgets\TabbedWidgetDependency;
use ProfilePress\Core\Classes\FormRepository as FR;

class AjaxHandler
{
    public function __construct()
    {
        add_action('wp_ajax_pp_ajax_editprofile', [$this, 'ajax_editprofile_func']);

        add_action('wp_ajax_pp_del_avatar', [$this, 'ajax_delete_avatar']);
        add_action('wp_ajax_pp_del_cover_image', [$this, 'ajax_delete_profile_cover_image']);

        add_action('wp_ajax_pp_profile_fields_sortable', [$this, 'profile_fields_sortable_func']);

        add_action('wp_ajax_nopriv_pp_ajax_login', [$this, 'ajax_login_func']);
        add_action('wp_ajax_pp_ajax_login', [$this, 'ajax_login_func']);

        add_action('wp_ajax_nopriv_pp_ajax_signup', [$this, 'ajax_signup_func']);
        add_action('wp_ajax_pp_ajax_signup', [$this, 'ajax_signup_func']);

        add_action('wp_ajax_pp_contact_info_sortable', [$this, 'pp_contact_info_sortable_func']);

        add_action('wp_ajax_nopriv_pp_ajax_passwordreset', [$this, 'ajax_passwordreset_func']);

        add_action('wp_ajax_pp_ajax_passwordreset', [$this, 'ajax_passwordreset_func']);

        add_action('wp_ajax_pp_get_forms_by_builder_type', [$this, 'get_forms_by_builder_type']);

        add_action('wp_ajax_pp_form_type_selection', [$this, 'form_type_selection']);

        add_action('wp_ajax_pp_create_form', [$this, 'create_form']);
    }

    public function menu_bar($builder_type)
    {
        $melange_jbox = esc_html__('Melange is a way to combine login, registration & password reset forms in a single form.', 'wp-user-avatar');
        ?>
        <div id="pp-sub-bar">
            <div class="pp-new-toolbar pp-clear">
                <h4><?php _e('Select Form Type', 'wp-user-avatar'); ?></h4>
                <span class="sr-only"><?php esc_html__('Loading...', 'wp-user-avatar'); ?></span>
                <ul class="pp-design-options">
                    <li>
                        <a href="#" class="pp-select-form-type pp-type-active" data-form-type="<?= FR::LOGIN_TYPE ?>" data-builder-type="<?= $builder_type ?>">
                            <?php _e('Login', 'wp-user-avatar'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="pp-select-form-type" data-form-type="<?= FR::REGISTRATION_TYPE ?>" data-builder-type="<?= $builder_type ?>">
                            <?php _e('Registration', 'wp-user-avatar'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="pp-select-form-type" data-form-type="<?= FR::PASSWORD_RESET_TYPE ?>" data-builder-type="<?= $builder_type ?>">
                            <?php _e('Password Reset', 'wp-user-avatar'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="pp-select-form-type" data-form-type="<?= FR::EDIT_PROFILE_TYPE ?>" data-builder-type="<?= $builder_type ?>">
                            <?php _e('Edit Profile', 'wp-user-avatar'); ?>
                        </a>
                    </li>
                    <?php if ($builder_type == 'shortcodeBuilder') : ?>
                        <li>
                            <a href="#" class="pp-select-form-type" data-form-type="<?= FR::MELANGE_TYPE ?>" data-builder-type="<?= $builder_type ?>">
                                <?php _e('Melange', 'wp-user-avatar'); ?>
                            </a>
                            <span class="pp-melange-jbox dashicons dashicons-editor-help" title="<?php echo $melange_jbox; ?>"></span>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="#" class="pp-select-form-type" data-form-type="<?= FR::USER_PROFILE_TYPE ?>" data-builder-type="<?= $builder_type ?>">
                            <?php _e('User Profile', 'wp-user-avatar'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <?php
    }

    public function drag_drop_build_your_own_tmp($builder_type, $form_type)
    {
        $type = FR::DRAG_DROP_BUILDER_TYPE;

        if ($builder_type == 'shortcodeBuilder') {
            $type = FR::SHORTCODE_BUILDER_TYPE;
        }

        if ($builder_type == 'dragDropBuilder' && in_array($form_type, [FR::USER_PROFILE_TYPE, FR::MEMBERS_DIRECTORY_TYPE])) return;

        $label = esc_html__('Create from Scratch', 'wp-user-avatar');
        ?>
        <div id="pp-optin-theme-list" class="pp-optin-theme ppress-allow-activate" data-builder-type="<?php echo $type; ?>" data-theme-type="<?php echo $form_type; ?>">
            <div class="pp-optin-theme-screenshot">
                <div style="position: absolute;top: 40%;display: block;width: 100%;">
                    <h2 style="text-transform: uppercase;margin: 0 0 5px;font-size: 2em;padding: 0;text-align: center;">
                        <?= esc_html__('Do it Yourself', 'wp-user-avatar') ?>
                    </h2>
                    <div style="text-align: center;font-size: 1em;">
                        <?= esc_html__('Get Started Creating Your Own Form', 'wp-user-avatar') ?>
                    </div>
                </div>
            </div>
            <h3 class="pp-optin-theme-name"><?= $label ?></h3>
            <div class="pp-optin-theme-actions">
                <a class="button button-primary pp-theme-select"><?= esc_html__('Build Now', 'wp-user-avatar') ?></a>
            </div>
        </div>
        <?php
    }

    public function form_template_single($theme, $builder_type)
    {
        $screenshot  = $theme['screenshot'];
        $name        = $theme['name'];
        $theme_class = $theme['theme_class'];
        $theme_type  = $theme['theme_type'];

        $upgrade_url = 'https://profilepress.net/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=premium_template';

        /** @todo add preview support of templates */

        $is_premium_theme_disallowed = ! ExtensionManager::is_premium() && ppress_var($theme, 'flag') == 'premium';

        $extra_class = $is_premium_theme_disallowed ? '' : ' ppress-allow-activate';
        $url         = $is_premium_theme_disallowed ? $upgrade_url : '#';
        $url_target  = $is_premium_theme_disallowed ? ' target="_blank"' : '';

        ?>
        <div class="pp-optin-theme<?= $extra_class; ?>" data-builder-type="<?= $builder_type; ?>" data-theme-class="<?= $theme_class; ?>" data-theme-type="<?= $theme_type; ?>">
            <a <?= $url_target; ?> href="<?= $url; ?>">
                <div class="pp-optin-theme-screenshot">
                    <img src="<?= $screenshot; ?>" alt="<?= $name; ?>">
                </div>
                <?php if ($is_premium_theme_disallowed) : ?>
                    <div class="ppress-premium-flag"></div>
                <?php endif; ?>
                <h3 class="pp-optin-theme-name"><?= $name; ?></h3>
            </a>
            <div class="pp-optin-theme-actions">
                <a <?= $url_target; ?> href="<?= $url; ?>" class="button button-primary pp-theme-select" title="<?php _e('Select this template', 'wp-user-avatar'); ?>">
                    <?php _e('Select Template', 'wp-user-avatar'); ?>
                </a>
            </div>
        </div>
        <?php
    }

    public function form_name_field($label = '', $placeholder = '')
    {
        $label       = ! empty($label) ? $label : esc_html__('Enter a Name', 'wp-user-avatar');
        $placeholder = ! empty($placeholder) ? $placeholder : '';
        ?>
        <div class="pp-form-new-list pp-optin-clear">
            <h4><?php echo $label; ?>
                <input type="text" id="pp-add-form-title" placeholder="<?= $placeholder; ?>">
                <span class="spinner pp-dash-spinner"></span>
            </h4>
        </div>
        <?php
    }

    public function get_forms_by_builder_type($form_type = FR::LOGIN_TYPE, $builder_type = false)
    {
        $form_type    = ! empty($form_type) ? $form_type : FR::LOGIN_TYPE;
        $builder_type = ! $builder_type ? sanitize_text_field($_POST['data']) : $builder_type;

        $this->form_name_field();

        if ($form_type != FR::MEMBERS_DIRECTORY_TYPE) {
            $this->menu_bar($builder_type);
        }

        echo '<div class="meta-box-sortables ui-sortable">';
        printf('<input id="pp_plugin_nonce" type="hidden" name="pp_plugin_nonce" value="%s">', wp_create_nonce('pp-plugin-nonce'));
        echo '<div class="pp-optin-themes pp-optin-clear">';

        $this->theme_listing($builder_type, $form_type);

        echo '</div>';
        echo '</div>';
        exit;
    }

    public function theme_listing($builder_type, $form_type)
    {
        $this->drag_drop_build_your_own_tmp($builder_type, $form_type);
        if ($builder_type == 'shortcodeBuilder') {
            foreach (ShortcodeThemesRepository::get_by_type($form_type) as $theme) {
                $this->form_template_single($theme, FR::SHORTCODE_BUILDER_TYPE);
            }
        } else {
            foreach (DragDropThemesRepository::get_by_type($form_type) as $theme) {
                $this->form_template_single($theme, FR::DRAG_DROP_BUILDER_TYPE);
            }
        }
    }

    /**
     * Filter forms by type.
     */
    public function form_type_selection()
    {
        check_ajax_referer('pp-plugin-nonce', 'nonce');

        if (empty($_POST['form-type'])) {
            wp_send_json_error(__('Unexpected error. Please try again.', 'wp-user-avatar'));
        }

        $form_type    = sanitize_text_field($_POST['form-type']);
        $builder_type = sanitize_text_field($_POST['builder-type']);

        echo '<div class="meta-box-sortables ui-sortable">';
        printf('<input id="pp_plugin_nonce" type="hidden" name="pp_plugin_nonce" value="%s">', wp_create_nonce('pp-plugin-nonce'));
        echo '<div class="pp-optin-themes pp-optin-clear">';
        $this->theme_listing($builder_type, $form_type);
        echo '</div>';
        echo '</div>';
        exit;
    }

    /**
     * Create new form.
     */
    public function create_form()
    {
        check_ajax_referer('pp-plugin-nonce', 'nonce');

        if (empty($_REQUEST['title']) || empty($_REQUEST['theme_type']) || empty($_REQUEST['builder_type'])) {
            wp_send_json_error(__('Unexpected error. Please try again.', 'wp-user-avatar'));
        }

        $title            = sanitize_text_field($_POST['title']);
        $form_theme_class = sanitize_text_field($_POST['theme_class']);
        $form_type        = sanitize_text_field($_POST['theme_type']);
        $builder_type     = sanitize_text_field($_POST['builder_type']);

        if (FR::name_exist($title)) {
            wp_send_json_error(__('Form with similar name exist already.', 'wp-user-avatar'));
        }

        do_action('ppress_before_add_form');

        $form_id = FR::add_form($title, $form_type, $form_theme_class, $builder_type);

        if (is_int($form_id)) {

            do_action('ppress_after_add_form', $form_id);

            wp_send_json_success(
                ['redirect' => FormList::customize_url($form_id, $form_type, $builder_type)]
            );
        }

        wp_send_json_error();
    }

    function ajax_delete_avatar()
    {
        if (current_user_can('read')) {
            if ( ! wp_verify_nonce($_POST['nonce'], 'ppress-frontend-nonce')) {
                wp_send_json(array('error' => 'nonce_failed'));
            }

            EditUserProfile::remove_avatar_core();

            wp_send_json(array('success' => true, 'default' => get_avatar_url(get_current_user_id(), '300')));
        }
    }

    public function ajax_delete_profile_cover_image()
    {
        if (current_user_can('read')) {

            if ( ! wp_verify_nonce($_POST['nonce'], 'ppress-frontend-nonce')) {
                wp_send_json(['error' => 'nonce_failed']);
            }

            EditUserProfile::remove_cover_image();

            $default = get_option('wp_user_cover_default_image_url', '');

            wp_send_json(['success' => true, 'default' => $default]);
        }
    }

    function profile_fields_sortable_func()
    {
        if (current_user_can('manage_options')) {
            global $wpdb;

            $posted_data       = array_map('absint', $_POST['data']);
            $profile_field_ids = PROFILEPRESS_sql::get_profile_field_ids();
            $table_name        = Base::profile_fields_db_table();

            /* Alter the IDs of the custom fields in DB incrementally starting from the last ID number of the record. */

            // set the index to the last profile field ID
            $index = array_pop($profile_field_ids) + 1;

            foreach ($posted_data as $id) {

                $wpdb->update(
                    $table_name,
                    array(
                        'id' => $index,
                    ),
                    array('id' => $id),
                    array(
                        '%d',
                    ),
                    array('%d')
                );

                $index++;
            }


            /* Reorder the profile fields ID starting from 1 incrementally. */

            $index_2 = 1;

            // fetch the profile fields again
            $profile_field_ids_2 = PROFILEPRESS_sql::get_profile_field_ids();

            foreach ($profile_field_ids_2 as $id) {
                $wpdb->update(
                    $table_name,
                    array(
                        'id' => $index_2,
                    ),
                    array('id' => $id),
                    array(
                        '%d',
                    ),
                    array('%d')
                );

                $index_2++;
            }
        }

        wp_die();
    }

    function pp_contact_info_sortable_func()
    {
        if (current_user_can('manage_options')) {

            $posted_data = array_map('esc_attr', $_POST['data']);
            $db_data     = get_option(PPRESS_CONTACT_INFO_OPTION_NAME, array());

            $newArray = array();

            foreach ($posted_data as $key) {
                $newArray[$key] = $db_data[$key];
            }

            update_option(PPRESS_CONTACT_INFO_OPTION_NAME, $newArray);
        }

        wp_die();
    }

    function ajax_login_func()
    {
        if ( ! defined('W3GUY_LOCAL') && is_user_logged_in()) wp_send_json_error();

        if (isset($_REQUEST['data'])) {
            parse_str($_REQUEST['data'], $data); //tabbed-login-name

            // populate global $_POST variable.
            $_POST = $data;

            $login_form_id = absint(@$data['login_form_id']);

            // $login_username, $login_password, $login_remember, $login_redirect, $ogin_form_id are all populated by parse_str()
            $login_status_css_class = apply_filters('ppress_login_error_css_class', 'profilepress-login-status', $login_form_id);

            $login_username = ! empty($data['tabbed-login-name']) ? $data['tabbed-login-name'] : $data['login_username'];
            $login_password = ! empty($data['tabbed-login-password']) ? $data['tabbed-login-password'] : $data['login_password'];
            $login_remember = ! empty($data['tabbed-login-remember-me']) ? $data['tabbed-login-remember-me'] : @$data['login_remember'];

            $login_username = trim($login_username);
            $login_remember = sanitize_text_field($login_remember);

            $login_redirect = ! empty($data['login_redirect']) ? esc_url_raw($data['login_redirect']) : '';
            if ( ! empty($data['melange_redirect'])) {
                $login_redirect = esc_url_raw($data['melange_redirect']);
            }

            /** @var \WP_Error|string $response */
            $response = LoginAuth::login_auth($login_username, $login_password, $login_remember, $login_form_id, $login_redirect);

            $ajax_response = array('success' => true, 'redirect' => $response);

            if (isset($response) && is_wp_error($response)) {
                $login_error = '<div class="' . $login_status_css_class . '">';
                $login_error .= $response->get_error_message();
                $login_error .= '</div>';

                $ajax_response = array('success' => false, 'message' => $login_error);
            }

            wp_send_json($ajax_response);
        }

        wp_die();
    }

    function ajax_signup_func()
    {
        if ( ! defined('W3GUY_LOCAL') && is_user_logged_in()) wp_send_json_error();

        if (isset($_REQUEST)) {

            $is_melange = ( ! empty($_POST['is_melange']) && $_POST['is_melange'] == 'true');

            $form_id = ! empty($_POST['melange_id']) ? $_POST['melange_id'] : @$_POST['signup_form_id'];
            $form_id = absint($form_id);

            $redirect = ppressPOST_var('signup_redirect', '', true);
            if ( ! empty($_POST['melange_redirect'])) {
                $redirect = esc_url_raw($_POST['melange_redirect']);
            }

            $no_login_redirect = esc_url_raw(@$_POST['signup_no_login_redirect']);

            // if this is tab widget.
            if (isset($_POST['is-pp-tab-widget']) && $_POST['is-pp-tab-widget'] == 'true') {
                $widget_status = @TabbedWidgetDependency::registration(
                    $_POST['tabbed-reg-username'],
                    $_POST['tabbed-reg-password'],
                    $_POST['tabbed-reg-email']
                );

                if ( ! empty($widget_status)) {
                    $response = '<div class="pp-tab-status">' . $widget_status . '</div>';
                }

            } else {
                $response = RegistrationAuth::register_new_user($_POST, $form_id, $redirect, $is_melange, $no_login_redirect);
            }

            // display form generated messages
            if ( ! empty($response)) {
                if (is_array($response)) {
                    $ajax_response = ['redirect' => $response[0]];
                } else {
                    $ajax_response = ['message' => html_entity_decode($response)];
                }

                wp_send_json($ajax_response);
            }
        }

        wp_die();
    }

    function ajax_passwordreset_func()
    {
        if (isset($_REQUEST['data'])) {
            parse_str($_REQUEST['data'], $data);

            // populate global $_POST and $_REQUEST variable.
            $_POST = $_REQUEST = $data;

            // variable is populated by parse_str()
            $user_login = ! empty($data['tabbed-user-login']) ? $data['tabbed-user-login'] : $data['user_login'];
            $user_login = sanitize_text_field($user_login);

            $is_melange = ( ! empty($_POST['is_melange']) && $_POST['is_melange'] == 'true');

            $form_id = ! empty($data['melange_id']) ? $data['melange_id'] : $data['passwordreset_form_id'];
            $form_id = absint($form_id);

            // do password reset
            if ( ! empty($data['reset_key']) && ! empty($data['reset_login'])) {
                // needed for checking if this is for do password reset.
                $_REQUEST['reset_password'] = true;
                $response                   = PasswordReset::do_password_reset();
            } else {
                // response is WP_Error on error or redirect url on success.
                $response = PasswordReset::password_reset_status($user_login, $form_id, $is_melange);
            }

            $ajax_response            = array();
            $ajax_response['status']  = is_array($response) ? true : false;
            $ajax_response['message'] = is_array($response) ? html_entity_decode($response[0]) : html_entity_decode($response);

            wp_send_json($ajax_response);
        }

        wp_die();
    }

    function ajax_editprofile_func()
    {
        if (isset($_REQUEST)) {

            $is_melange = ( ! empty($_POST['is_melange']) && $_POST['is_melange'] == 'true');

            $form_id = absint(! empty($_POST['melange_id']) ? $_POST['melange_id'] : ppressPOST_var('editprofile_form_id'));

            $redirect = ppressPOST_var('editprofile_redirect', '', true);

            if ( ! empty($_POST['melange_redirect'])) {
                $redirect = esc_url_raw($_POST['melange_redirect']);
            }

            // check to see if the submitted nonce matches with the generated nonce we created earlier
            if ( ! wp_verify_nonce($_REQUEST['nonce'], 'ppress-frontend-nonce')) {

                wp_send_json([
                    'success' => false,
                    'message' => '<div class="profilepress-edit-profile-status">' . esc_html__('Security validation failed. Try again', 'wp-user-avatar') . '</div>'
                ]);
            }

            $response = EditUserProfile::process_func($form_id, $redirect, $is_melange);

            // display form generated messages
            if (isset($response) && is_array($response)) {
                wp_send_json($response);
            }
        }

        wp_die();
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