<?php

use ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage;
use ProfilePress\Custom_Settings_Page_Api;

/**
 * Defines all of administrative, activation, and deactivation settings.
 *
 *
 */
class WP_User_Avatar_Admin extends AbstractSettingsPage
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Settings saved to wp_options
        add_action('admin_init', array($this, 'wpua_options'));
        // Admin menu settings
        add_filter('ppress_settings_page_tabs', [$this, 'menu_tab']);
        add_filter('ppress_general_settings_admin_page_short_circuit', [$this, 'settings_page_callback']);
        add_filter('ppress_general_settings_admin_page_title', [$this, 'change_page_title']);

        add_action('admin_init', array($this, 'wpua_register_settings'));
        // Default avatar
        add_filter('default_avatar_select', array($this, 'wpua_add_default_avatar'), 10);

        if (function_exists('add_allowed_options')) {
            add_filter('allowed_options', array($this, 'wpua_whitelist_options'), 10);
        } else {
            add_filter('whitelist_options', array($this, 'wpua_whitelist_options'), 10);
        }
    }

    /**
     * Settings saved to wp_options
     */
    public function wpua_options()
    {
        add_option('show_avatars', '1');
        add_option('avatar_default_wp_user_avatar', "");
        add_option('wp_user_avatar_disable_gravatar', '0');
        add_option('wp_user_avatar_resize_crop', '0');
        add_option('wp_user_avatar_resize_h', '96');
        add_option('wp_user_avatar_resize_upload', '0');
        add_option('wp_user_avatar_resize_w', '96');
        add_option('wp_user_cover_upload_size_limit', '1024000');
        add_option('wp_user_avatar_upload_size_limit', '1024000');
    }

    /**
     * Add options page and settings
     */
    public function menu_tab($tabs)
    {
        $tabs[35] = [
            'url'   => add_query_arg('view', 'wp-user-avatar', PPRESS_SETTINGS_SETTING_PAGE),
            'label' => esc_html__('Profile & Cover Photo', 'wp-user-avatar')
        ];

        return $tabs;
    }

    public function change_page_title($title)
    {
        if (isset($_GET['view']) && $_GET['view'] == 'wp-user-avatar') {
            $title = esc_html__('Profile & Cover Photo', 'wp-user-avatar');
        }

        return $title;
    }


    public function sidebar_args()
    {
        $sidebar_args = [
            [
                'section_title' => esc_html__('Available Shortcodes', 'wp-user-avatar'),
                'content'       => $this->available_shortcode_doc(),
            ],
            [
                'section_title' => esc_html__('Check out MailOptin', 'wp-user-avatar'),
                'content'       => $this->mailoptin_ad_block(),
            ]
        ];

        return $sidebar_args;
    }

    public function available_shortcode_doc()
    {
        $content = '<p style="border-bottom: 1px solid #eee">';
        $content .= sprintf(
            esc_html__('%s[avatar]%s displays a user\'s profile pictures.', 'wp-user-avatar'),
            '<code>', '</code>'
        );
        $content .= '</p>';

        $content .= '<p style="border-bottom: 1px solid #eee">';
        $content .= sprintf(
            esc_html__('%s[avatar_upload]%s adds a standalone avatar uploader visible only to logged-in users.', 'wp-user-avatar'),
            '<code>', '</code>'
        );
        $content .= '</p>';

        $content .= '<p>';
        /** @todo add doc link here */
        $content .= '<strong><a href="#" target="_blank">' . esc_html__('Learn more', 'wp-user-avatar') . '</a></strong>';
        $content .= '</p>';

        return $content;
    }

    public function settings_page_callback($page)
    {
        if (isset($_GET['view']) && $_GET['view'] == 'wp-user-avatar') {

            add_filter('wp_cspa_main_content_area', function () {
                ob_start();
                require_once(WPUA_INC . 'wpua-options-page.php');

                return ob_get_clean();
            });

            add_action('wp_cspa_form_tag', function () {
                echo sprintf('action="%s"', admin_url('options.php'));
            });

            $instance = Custom_Settings_Page_Api::instance();
            $instance->option_name('ppress_wp_user_avatar_options');
            $instance->page_header(esc_html__('Profile Picture', 'wp-user-avatar'));
            $this->register_core_settings($instance);
            $instance->tab($this->settings_tab_args());
            $instance->build();

            return true;
        }

        return $page;
    }


    /**
     * Checks if current page is settings page
     * @return bool
     */
    public function wpua_is_menu_page()
    {
        return isset($_GET['page'], $_GET['view']) && $_GET['view'] == 'wp-user-avatar' ? true : false;
    }

    /**
     * Save per page setting
     *
     * @param int $status
     * @param string $option
     * @param int $value
     *
     * @return int $status
     */
    public function wpua_set_media_screen_option($status, $option, $value)
    {
        $status = ($option == 'upload_per_page') ? $value : $status;

        return $status;
    }

    /**
     * Whitelist settings
     * @return array
     */
    public function wpua_register_settings()
    {
        register_setting('wpua-settings-group', 'avatar_default');
        register_setting('wpua-settings-group', 'avatar_default_wp_user_avatar');
        register_setting('wpua-settings-group', 'wp_user_avatar_disable_gravatar', 'intval');
        register_setting('wpua-settings-group', 'wp_user_avatar_resize_crop', 'intval');
        register_setting('wpua-settings-group', 'wp_user_avatar_resize_h', 'intval');
        register_setting('wpua-settings-group', 'wp_user_avatar_resize_upload', 'intval');
        register_setting('wpua-settings-group', 'wp_user_avatar_resize_w', 'intval');
        register_setting('wpua-settings-group', 'wp_user_cover_upload_size_limit', 'intval');
        register_setting('wpua-settings-group', 'wp_user_avatar_upload_size_limit', 'intval');
        register_setting('wpua-settings-group', 'wp_user_cover_default_image_url', 'sanitize_text_field');
    }

    /**
     * Add default avatar_default to whitelist
     *
     * @param array $options
     *
     * @return array $options
     */
    public function wpua_whitelist_options($options)
    {
        $options['discussion'][] = 'avatar_default_wp_user_avatar';

        return $options;
    }

    /**
     * Add default avatar
     * @return string
     */
    public function wpua_add_default_avatar()
    {
        global $avatar_default, $mustache_admin, $wpua_avatar_default, $wpua_disable_gravatar, $wpua_functions;
        // Set avatar_list variable
        $avatar_list = "";
        // Set avatar defaults
        $avatar_defaults = array(
            'mystery'          => __('Mystery Man', 'wp-user-avatar'),
            'blank'            => __('Blank', 'wp-user-avatar'),
            'gravatar_default' => __('Gravatar Logo', 'wp-user-avatar'),
            'identicon'        => __('Identicon (Generated)', 'wp-user-avatar'),
            'wavatar'          => __('Wavatar (Generated)', 'wp-user-avatar'),
            'monsterid'        => __('MonsterID (Generated)', 'wp-user-avatar'),
            'retro'            => __('Retro (Generated)', 'wp-user-avatar')
        );

        $avatar_defaults = apply_filters('avatar_defaults', $avatar_defaults);

        // No Default Avatar, set to Mystery Man
        if (empty($avatar_default)) {
            $avatar_default = 'mystery';
        }
        // Take avatar_defaults and get examples for unknown@gravatar.com
        foreach ($avatar_defaults as $default_key => $default_name) {
            $avatar      = get_avatar('unknown@gravatar.com', 32, $default_key, '', array('force_default' => true));
            $selected    = ($avatar_default == $default_key) ? 'checked="checked" ' : "";
            $avatar_list .= "\n\t<label><input type='radio' name='avatar_default' id='avatar_{$default_key}' value='" . esc_attr($default_key) . "' {$selected}/> ";
            $avatar_list .= preg_replace("/src='(.+?)'/", "src='\$1&amp;forcedefault=1'", $avatar);
            $avatar_list .= ' ' . $default_name . '</label>';
            $avatar_list .= '<br />';
        }
        // Show remove link if custom Default Avatar is set
        if ( ! empty($wpua_avatar_default) && wp_attachment_is_image($wpua_avatar_default)) {
            $avatar_thumb_src = $wpua_functions->wpua_get_attachment_image_src($wpua_avatar_default, array(32, 32));
            $avatar_thumb     = $avatar_thumb_src[0];
            $hide_remove      = "";
        } else {
            $avatar_thumb = $mustache_admin;
            $hide_remove  = ' class="wpua-hide"';
        }
        // Default Avatar is wp_user_avatar, check the radio button next to it
        $selected_avatar = ((bool)$wpua_disable_gravatar == 1 || $avatar_default == 'wp_user_avatar') ? ' checked="checked" ' : "";
        // Wrap WPUA in div
        $avatar_thumb_img = '<div id="wpua-preview"><img src="' . $avatar_thumb . '" width="32" /></div>';
        // Add WPUA to list
        $wpua_list = "\n\t<label><input type='radio' name='avatar_default' id='wp_user_avatar_radio' value='wp_user_avatar'$selected_avatar /> ";
        $wpua_list .= preg_replace("/src='(.+?)'/", "src='\$1'", $avatar_thumb_img);
        $wpua_list .= ' ' . __('Default Profile Picture', 'wp-user-avatar') . '</label>';
        $wpua_list .= '<p id="wpua-edit"><button type="button" class="button" id="wpua-add" name="wpua-add" data-avatar_default="true" data-title="' . __('Choose Image') . ': ' . __('Default Profile Picture') . '">' . __('Choose Image', 'wp-user-avatar') . '</button>';
        $wpua_list .= '<span id="wpua-remove-button"' . $hide_remove . '><a href="#" id="wpua-remove">' . __('Remove', 'wp-user-avatar') . '</a></span><span id="wpua-undo-button"><a href="#" id="wpua-undo">' . __('Undo', 'wp-user-avatar') . '</a></span></p>';
        $wpua_list .= '<input type="hidden" id="wp-user-avatar" name="avatar_default_wp_user_avatar" value="' . $wpua_avatar_default . '">';
        $wpua_list .= '<div id="wpua-modal"></div>';
        if ((bool)$wpua_disable_gravatar != 1) {
            return $wpua_list . '<div id="wp-avatars">' . $avatar_list . '</div>';
        } else {
            return $wpua_list . '<div id="wp-avatars" style="display:none;">' . $avatar_list . '</div>';
        }
    }
}

/**
 * Initialize
 */
function wpua_admin_init()
{
    global $wpua_admin;
    $wpua_admin = new WP_User_Avatar_Admin();
}

add_action('init', 'wpua_admin_init');
