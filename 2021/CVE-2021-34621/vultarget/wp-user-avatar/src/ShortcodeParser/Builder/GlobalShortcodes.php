<?php

namespace ProfilePress\Core\ShortcodeParser\Builder;

use ProfilePress\Core\Classes\UserAvatar;

class GlobalShortcodes
{
    /** @var \WP_User */
    static private $current_user;

    public static function initialize()
    {
        add_action('init', array(__CLASS__, 'get_current_user'));
        add_shortcode('pp-user-avatar', array(__CLASS__, 'user_avatar'));
        add_shortcode('user-avatar', array(__CLASS__, 'user_avatar')); // backward compat
        add_shortcode('pp-user-cover-image', array(__CLASS__, 'user_cover_image'));
        add_shortcode('pp-custom-html', array(__CLASS__, 'custom_html_block'));
        add_shortcode('link-registration', array(__CLASS__, 'link_registration'));
        add_shortcode('link-lost-password', array(__CLASS__, 'link_lost_password'));
        add_shortcode('link-login', array(__CLASS__, 'link_login'));
        add_shortcode('link-logout', array(__CLASS__, 'link_logout'));
        add_shortcode('link-edit-user-profile', array(__CLASS__, 'link_edit_profile'));
        add_shortcode('link-my-account', array(__CLASS__, 'link_edit_profile'));
        add_shortcode('pp-login-form', array(__CLASS__, 'login_form_tag'));
        add_shortcode('pp-registration-form', array(__CLASS__, 'registration_form_tag'));
        add_shortcode('pp-password-reset-form', array(__CLASS__, 'password_reset_form_tag'));
        add_shortcode('pp-edit-profile-form', array(__CLASS__, 'edit_profile_form_tag'));
        add_shortcode('pp-redirect-non-logged-in-users', array(__CLASS__, 'redirect_non_logged_in_users'));
        add_shortcode('pp-redirect-logged-in-users', array(__CLASS__, 'redirect_logged_in_users'));
        add_shortcode('pp-logged-users', array(__CLASS__, 'pp_log_in_users'));
        add_shortcode('pp-non-logged-users', array(__CLASS__, 'pp_non_log_in_users'));

        add_shortcode('password-hint', 'wp_get_password_hint');
        add_shortcode('pp-password-hint', 'wp_get_password_hint');

        // BbPress
        add_shortcode('bbp-topic-started-url', array(__CLASS__, 'bbp_topic_started_url'));
        add_shortcode('bbp-replies-created-url', array(__CLASS__, 'bbp_replies_created_url'));
        add_shortcode('bbp-favorites-url', array(__CLASS__, 'bbp_favorites_url'));
        add_shortcode('bbp-subscriptions-url', array(__CLASS__, 'bbp_subscriptions_url'));
    }

    /** Get the currently logged user */
    public static function get_current_user()
    {
        $current_user = wp_get_current_user();
        if ($current_user instanceof \WP_User) {
            self::$current_user = $current_user;
        }
    }

    private static function melange_hidden_fields()
    {
        $tag = '<input type="hidden" name="is_melange" value="true">';

        if (isset($GLOBALS['pp_melange_form_id'], $GLOBALS['pp_melange_form_redirect'])) {
            $form_id  = $GLOBALS['pp_melange_form_id'];
            $redirect = $GLOBALS['pp_melange_form_redirect'];

            $tag .= "<input type='hidden' name='pp_melange_id' class='pp_melange_id' value='$form_id'>";
            if ( ! empty($GLOBALS['pp_melange_form_redirect'])) {
                $tag .= "<input type='hidden' name='melange_redirect' value='$redirect'>";
            }
        }

        return $tag;
    }

    /**
     * Login form tag
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public static function login_form_tag($atts, $content)
    {
        $tag = '<form method="post" data-pp-form-submit="login">';
        $tag .= self::melange_hidden_fields();
        $tag .= '<input type="hidden" name="pp_current_url" value="' . ppress_get_current_url_raw() . '">';
        $tag .= do_shortcode($content);
        $tag .= '</form>';

        return $tag;
    }

    /**
     * Registration form tag
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public static function registration_form_tag($atts, $content)
    {
        $atts       = ppress_normalize_attributes($atts);
        $novalidate = isset($atts['novalidate']) ? ' novalidate' : '';

        $tag = sprintf('<form method="post" enctype="multipart/form-data" data-pp-form-submit="signup"%s>', $novalidate);
        $tag .= self::melange_hidden_fields();
        $tag .= do_shortcode($content);
        $tag .= '</form>';

        return $tag;

    }

    /**
     * Password reset form tag
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public static function password_reset_form_tag($atts, $content)
    {
        $tag = '<form method="post" data-pp-form-submit="passwordreset">';
        $tag .= self::melange_hidden_fields();
        $tag .= do_shortcode($content);
        $tag .= '</form>';

        return $tag;

    }

    /**
     * Edit profile form tag
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public static function edit_profile_form_tag($atts, $content)
    {
        $tag = '<form method="post" enctype="multipart/form-data" data-pp-form-submit="editprofile">';
        $tag .= self::melange_hidden_fields();
        $tag .= do_shortcode($content);
        $tag .= '</form>';

        return $tag;
    }

    /**
     * @return string
     */
    public static function custom_html_block($atts)
    {
        $atts = shortcode_atts(['custom_html' => ''], $atts);

        return do_shortcode(stripslashes($atts['custom_html']));
    }

    /** registration url */
    public static function link_registration($atts)
    {
        $atts = ppress_normalize_attributes($atts);

        if (( ! empty($atts['raw']) && ($atts['raw'] == true))) {
            return wp_registration_url();
        }

        $atts = shortcode_atts(
            array(
                'class' => '',
                'id'    => '',
                'title' => '',
                'label' => esc_html__('Sign Up', 'wp-user-avatar'),
                'raw'   => '',
            ),
            $atts
        );

        $class = 'class="' . $atts['class'] . '"';
        $id    = 'id="' . $atts['id'] . '"';
        $label = $atts['label'];
        $title = 'title="' . $atts['title'] . '"';


        $html = '<a href="' . wp_registration_url() . "\" {$title} {$class} {$id}>$label</a>";

        return $html;
    }

    /** Lost password url */
    public static function link_lost_password($atts)
    {
        $atts = ppress_normalize_attributes($atts);


        if (( ! empty($atts['raw']) && ($atts['raw'] == true))) {
            return wp_lostpassword_url();
        }

        $atts = shortcode_atts(
            array(
                'class' => '',
                'id'    => '',
                'title' => '',
                'label' => esc_html__('Reset Password', 'wp-user-avatar'),
                'raw'   => '',
            ),
            $atts
        );

        $class = 'class="' . $atts['class'] . '"';
        $id    = 'id="' . $atts['id'] . '"';
        $label = $atts['label'];
        $title = 'title="' . $atts['title'] . '"';

        $html = "<a href=\"" . wp_lostpassword_url() . "\" {$title} {$class} {$id}>$label</a>";

        return $html;
    }


    /** Login url */
    public static function link_login($atts)
    {
        $atts = ppress_normalize_attributes($atts);

        if (( ! empty($atts['raw']) && ($atts['raw'] == true))) {
            return wp_login_url();
        }

        $atts = shortcode_atts(
            array(
                'class' => '',
                'id'    => '',
                'title' => '',
                'label' => esc_html__('Login', 'wp-user-avatar'),
                'raw'   => '',
            ),
            $atts
        );

        $class = 'class="' . $atts['class'] . '"';
        $id    = 'id="' . $atts['id'] . '"';
        $label = $atts['label'];
        $title = 'title="' . $atts['title'] . '"';

        $html = '<a href="' . wp_login_url() . '" ' . "$title $class $id" . '>' . $label . '</a>';

        return $html;
    }

    /** Logout URL */
    public static function link_logout($atts)
    {
        if ( ! is_user_logged_in()) {
            return;
        }

        $atts = ppress_normalize_attributes($atts);

        if (( ! empty($atts['raw']) && ($atts['raw'] == true))) {
            return wp_logout_url();
        }

        $atts = shortcode_atts(
            array(
                'class' => '',
                'id'    => '',
                'title' => '',
                'label' => esc_html__('Log Out', 'wp-user-avatar'),
                'raw'   => '',
            ),
            $atts
        );

        $class = 'class="' . $atts['class'] . '"';
        $id    = 'id="' . $atts['id'] . '"';
        $label = $atts['label'];
        $title = 'title="' . $atts['title'] . '"';

        $html = '<a href="' . wp_logout_url() . '" ' . "$title $class $id" . '>' . $label . '</a>';

        return $html;

    }

    /**
     * URL to user edit page
     * @return string
     */
    public static function link_edit_profile($atts)
    {
        if ( ! is_user_logged_in()) return;

        $atts = ppress_normalize_attributes($atts);

        $atts = shortcode_atts(
            array(
                'class' => '',
                'id'    => '',
                'title' => '',
                'label' => esc_html__('Edit Profile', 'wp-user-avatar'),
                'raw'   => '',
            ),
            $atts
        );

        $class = 'class="' . $atts['class'] . '"';
        $id    = 'id="' . $atts['id'] . '"';
        $label = $atts['label'];
        $title = 'title="' . $atts['title'] . '"';


        $edit_profile_page_url = admin_url('profile.php');

        $edit_profile_page_id = ppress_get_setting('edit_user_profile_url');

        if ( ! empty($edit_profile_page_id)) {
            $edit_profile_page_url = get_permalink($edit_profile_page_id);
        }

        if ( ! empty($atts['raw']) && ($atts['raw'] == true)) {
            return $edit_profile_page_url;
        }

        $html = '<a href="' . $edit_profile_page_url . '" ' . "$title $class $id" . '>' . $label . '</a>';

        return $html;
    }

    /**
     * Display avatar of currently logged in user
     *
     * @param $atts
     *
     * @return string
     */
    public static function user_avatar($atts)
    {
        $atts = shortcode_atts(
            array(
                'user'  => '',
                'class' => '',
                'id'    => '',
                'size'  => 300,
                'alt'   => '',
            ),
            $atts
        );

        $class = $atts['class'];
        $id    = $atts['id'];
        $size  = absint($atts['size']);
        $alt   = $atts['alt'];

        $user_id = self::$current_user->ID;

        if ( ! empty($atts['user'])) {

            $user_id = is_numeric($atts['user']) ? absint($atts['user']) : get_user_by('login', $atts['user']);

            if ($user_id instanceof \WP_User) {
                $user_id = $user_id->ID;
            }
        }

        return UserAvatar::get_avatar_img($user_id, $size, $alt, $class, $id);
    }

    public static function user_cover_image($atts)
    {
        $atts = shortcode_atts([
            'user'  => '',
            'class' => '',
            'id'    => '',
            'alt'   => '',
        ], $atts);

        $class = $atts['class'];
        $id    = $atts['id'];
        $alt   = sanitize_text_field($atts['alt']);

        if ( ! empty($id)) {
            $id = " id='$id'";
        }

        $user_id = self::$current_user->ID;

        if ( ! empty($atts['user'])) {

            $user_id = is_numeric($atts['user']) ? absint($atts['user']) : get_user_by('login', $atts['user']);

            if ($user_id instanceof \WP_User) {
                $user_id = $user_id->ID;
            }
        }

        $url = ppress_get_cover_image_url($user_id);

        $avatar = "<img data-del=\"cover-image\" alt='{$alt}' src='{$url}' class='pp-user-cover-image {$class}'{$id}>";

        return $avatar;
    }

    /**
     * Redirect non logged users to login page.
     *
     * @param array $atts
     */
    public static function redirect_non_logged_in_users($atts)
    {
        if (is_user_logged_in()) {
            return;
        }

        $atts = shortcode_atts(
            array(
                'url' => '',
            ),
            $atts
        );

        $url = empty($atts['url']) ? ppress_login_url() : $atts['url'];

        wp_safe_redirect($url);
        exit;
    }


    /**
     * Redirect logged users to login page.
     *
     * @param array $atts
     */
    public static function redirect_logged_in_users($atts)
    {
        if ( ! is_user_logged_in()) {
            return;
        }

        $atts = shortcode_atts(
            array(
                'url' => '',
            ),
            $atts
        );

        $url = empty($atts['url']) ? ppress_login_url() : $atts['url'];

        wp_safe_redirect($url);
        exit;
    }


    /**
     * Only logged user can view content.
     *
     * @param array $atts
     * @param mixed $content
     *
     * @return mixed
     */
    public static function pp_log_in_users($atts, $content)
    {
        if (is_user_logged_in()) {
            return do_shortcode($content);
        }
    }


    /**
     * Only non-logged user can view content.
     *
     * @param array $atts
     * @param mixed $content
     *
     * @return mixed
     */
    public static function pp_non_log_in_users($atts, $content)
    {
        if ( ! is_user_logged_in()) {
            return do_shortcode($content);
        }
    }


    /**
     * URL to topics started by users.
     *
     * @return string
     */
    public static function bbp_topic_started_url()
    {
        if (function_exists('bbp_get_user_topics_created_url')) {
            return esc_url(bbp_get_user_topics_created_url(self::$current_user->ID));
        }
    }


    /**
     * URL to topics started by users.
     *
     * @return string
     */
    public static function bbp_replies_created_url()
    {
        if (function_exists('bbp_user_replies_created_url')) {
            return esc_url_raw(bbp_get_user_replies_created_url(self::$current_user->ID));
        }
    }


    /**
     * URL to topics started by users.
     *
     * @return string
     */
    public static function bbp_favorites_url()
    {
        if (function_exists('bbp_get_favorites_permalink')) {
            return esc_url(bbp_get_favorites_permalink(self::$current_user->ID));
        }
    }


    /**
     * URL to topics started by users.
     *
     * @return string
     */
    public static function bbp_subscriptions_url()
    {
        if (function_exists('bbp_get_subscriptions_permalink')) {
            return esc_url(bbp_get_subscriptions_permalink(self::$current_user->ID));
        }
    }

}