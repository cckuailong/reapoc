<?php


namespace WPDM\User;

use WPDM\__\__;
use WPDM\__\Crypt;
use WPDM\__\Email;
use WPDM\__\Session;
use WPDM\__\Template;
use WPDM\Form\Form;


if(!defined("ABSPATH")) die("Shit happens!");

class Login
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {

        add_action('init', [$this, 'process']);

        add_action("wp_ajax_nopriv_updatePassword", [$this, 'updatePassword']);
        add_action("wp_ajax_nopriv_resetPassword", [$this, 'resetPassword']);

        // Login Form shortcode
        add_shortcode('wpdm_login_form', [$this, 'form']);
        // Modal Login form trigger button shortcode
        add_shortcode('wpdm_modal_login_form', [$this, 'modalLoginFormBtn']);
        // Logout url shortcode
        add_shortcode('wpdm_logout_url', array($this, 'logoutURLShortcode'));


        add_action('authenticate', [$this, 'verifyLoginEmail'], 999999, 3);

    }

    function formFields($params = [])
    {
        $login_data_fields['__phash'] = ['type' => 'hidden', 'attrs' => ['name' => '__phash', 'id' => '__phash', 'value' => Crypt::encrypt($params)]];
        $login_data_fields['log'] = array(
            'label' => __("Login ID", "download-manager"),
            'type' => 'text',
            'attrs' => array('name' => 'wpdm_login[log]', 'id' => 'user_login', 'required' => 'required', 'placeholder' => __('Username or Email', "download-manager")),
        );
        $login_data_fields['password'] = array(
            'label' => __("Password", "download-manager"),
            'type' => 'password',
            'attrs' => array('name' => 'wpdm_login[pwd]', 'id' => 'password', 'required' => 'required', 'placeholder' => __("Enter Password", "download-manager")),
        );
        if (!isset($params['captcha']) || $params['captcha'] === true) {
            $show_captcha = (int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_secret_key', '') != '';
            $show_captcha = apply_filters("signin_form_captcha", $show_captcha);
            if ($show_captcha) {
                $login_data_fields['__recap'] = array(
                    'type' => 'reCaptcha',
                    'attrs' => array('name' => '__recap', 'id' => '__recap'),
                );
            }
        }
        $login_data_fields = apply_filters("wpdm_login_form_fields", $login_data_fields);
        $form = new Form($login_data_fields, ['name' => 'wpdm_login_form', 'id' => 'wpdm_login_form', 'method' => 'POST', 'action' => '', 'submit_button' => [], 'noForm' => true]);
        return $form->render();

    }

    /**
     * @usage Short-code callback function for login form
     * @return string
     */
    function form($params = array())
    {

        global $current_user;

        if (!isset($params) || !is_array($params)) $params = array();

        if (isset($params) && is_array($params))
            extract($params);
        if (!isset($redirect)) $redirect = get_permalink(get_option('__wpdm_user_dashboard'));

        if (!isset($regurl)) {
            $regurl = get_option('__wpdm_register_url');
            if ($regurl > 0)
                $regurl = get_permalink($regurl);
        }
        $log_redirect = $_SERVER['REQUEST_URI'];
        if (isset($params['redirect'])) $log_redirect = esc_url($params['redirect']);
        if (isset($_GET['redirect_to'])) $log_redirect = esc_url($_GET['redirect_to']);

        $up = parse_url($log_redirect);
        if (isset($up['host']) && $up['host'] != $_SERVER['SERVER_NAME']) $log_redirect = $_SERVER['REQUEST_URI'];

        $log_redirect = strip_tags($log_redirect);

        if (!isset($params['logo'])) $params['logo'] = get_site_icon_url();

        $__wpdm_social_login = get_option('__wpdm_social_login');
        $__wpdm_social_login = is_array($__wpdm_social_login) ? $__wpdm_social_login : array();

        ob_start();

        if (is_user_logged_in())
            $template = Template::locate("already-logged-in.php", __DIR__.'/views');
        else {
            if (__::query_var('action') === 'lostpassword')
                $template = Template::locate('lost-password-form.php', __DIR__.'/views');
            else if (__::query_var('action') === 'rp')
                $template = Template::locate('reset-password-form.php', __DIR__.'/views');
            else
                $template = Template::locate('login-form.php', __DIR__.'/views');
        }

        include($template);

        $content = ob_get_clean();
        $content = apply_filters("wpdm_login_form_html", $content);

        return $content;
    }

    function process()
    {

        global $wp_query, $post, $wpdb;
        if (!isset($_POST['wpdm_login'])) return;

        $shortcode_params = Crypt::decrypt(wpdm_query_var('__phash'), true);

        $login_try = (int)Session::get('login_try');
        $login_try++;
        Session::set('login_try', $login_try);

        if ($login_try > 30) wp_die("Slow Down!");

        if(!isset($shortcode_params['captcha']) || $shortcode_params['captcha'] ===  true) {
            $active_captcha = (int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_secret_key', '') != '';
            $active_captcha = apply_filters("signin_form_captcha", $active_captcha);
            if ($active_captcha) {
                $ret = wpdm_remote_post('https://www.google.com/recaptcha/api/siteverify', array('secret' => get_option('_wpdm_recaptcha_secret_key', ''), 'response' => wpdm_query_var('__recap')));
                $ret = json_decode($ret);
                if (!$ret->success) {
                    $login_error = __("Invalid CAPTCHA!", "download-manager");
                    if (wpdm_is_ajax()) {
                        wp_send_json(array('success' => false, 'message' => 'Error: ' . $login_error));
                        die();
                    }
                    Session::set('login_error', $login_error);
                    wp_safe_redirect(wpdm_query_var('permalink', 'url'));
                    die();
                }
            }
        }

        Session::clear('login_error');
        $creds = array();
        $creds['user_login'] = isset($_POST['wpdm_login']['log']) ? $_POST['wpdm_login']['log'] : '';
        $creds['user_password'] = isset($_POST['wpdm_login']['pwd']) ? $_POST['wpdm_login']['pwd'] : '';
        $creds['remember'] = isset($_POST['rememberme']) ? $_POST['rememberme'] : false;
        $user = wp_signon($creds, false);
        if (is_wp_error($user)) {
            $login_error = $user->get_error_message();
            if (wpdm_is_ajax())
                wp_send_json(array('success' => false, 'message' => $login_error));

            Session::set('login_error', $login_error);
            wp_safe_redirect($_SERVER['HTTP_REFERER']);
            die();
        } else {
            wp_set_auth_cookie($user->ID);
            wp_set_current_user($user->ID);
            update_user_meta($user->ID, '__wpdm_last_login_time', time());
            Session::set('login_try', 0);
            //do_action('wp_login', $creds['user_login'], $user);

            if (wpdm_is_ajax())
                wp_send_json(array('success' => true, 'message' => __("Success! Redirecting...", "download-manager")));

            wp_safe_redirect(wpdm_query_var('redirect_to', 'url'));
            die();
        }
    }

    /**
     * Handles password reset request
     */
    function resetPassword()
    {
        if (wpdm_query_var('__wpdm_reset_pass')) {

            if (empty($_POST['user_login'])) {
                die('error');
            } elseif (strpos($_POST['user_login'], '@')) {
                $user_data = get_user_by('email', trim(wp_unslash($_POST['user_login'])));
                if (empty($user_data))
                    die('error');
            } else {
                $login = trim($_POST['user_login']);
                $user_data = get_user_by('login', $login);
            }
            if (Session::get('__reset_time') && time() - Session::get('__reset_time') < 60) {
                echo "toosoon";
                exit;
            }
            if (!is_object($user_data) || !isset($user_data->user_login)) die('error');
            $user_login = Crypt::encrypt($user_data->user_login);
            $user_email = $user_data->user_email;
            $key = get_password_reset_key($user_data);


            $reseturl = add_query_arg(array('action' => 'rp', 'key' => $key, 'login' => $user_login), wpdm_login_url());

            $params = array('reset_password' => $reseturl, 'to_email' => $user_email);
            Email::send('password-reset', $params);
            Session::set('__reset_time', time());
            echo 'ok';
            exit;

        }
    }

    /**
     * Set new password
     */
    function updatePassword()
    {
        if (__::query_var('__wpdm_update_pass')) {

            if (wp_verify_nonce(__::query_var('__wpdm_update_pass'), NONCE_KEY)) {
                $pass = __::query_var('password','html');
                if ($pass == '') die('error');
                $user = Crypt::decrypt(__::query_var('__up_user'));
                if (is_object($user) && isset($user->ID)) {

                    if(user_can($user->ID, 'manage_options'))
                        wp_send_json(array('success' => false, 'message' => apply_filters('wpdm_update_password_error', __('Password update is disabled for this user!', 'download-manager'))));

                    wp_set_current_user($user->ID, $user->user_login);
                    wp_set_auth_cookie($user->ID);
                    //do_action('wp_login', $user->user_login);
                    wp_set_password($pass, $user->ID);
                    //print_r($user);
                    wp_send_json(array('success' => true, 'message' => ''));
                } else wp_send_json(array('success' => false, 'message' => apply_filters('wpdm_update_password_error', __('Session Expired! Please try again.', 'download-manager'))));
            } else
                wp_send_json(array('success' => false, 'message' => apply_filters('wpdm_update_password_error', __('Session Expired! Please try again.', 'download-manager'))));

        }
    }

    /**
     * Login url
     * @param string $redirect
     * @return string
     */
    function url($redirect = '')
    {
        $id = get_option('__wpdm_login_url', 0);
        if ($id > 0) {
            $page = get_post($id);
            if ($page->post_status == 'publish') {
                $url = get_page_link($page);
                //$url = $page->guid;
                if ($redirect != '')
                    $url = add_query_arg(array('redirect_to' => $redirect), $url);
            } else $url = wp_login_url($redirect);
        } else $url = wp_login_url($redirect);
        return $url;
    }


    /**
     * @param array $params
     * @return false|string
     */
    function modalLoginFormBtn($params = array())
    {
        if ((int)get_option('__wpdm_modal_login', 0) !== 1) return "";
        $defaults = array('class' => '', 'redirect' => '', 'logo' => '', 'label' => __('Login', 'download-manager'), 'id' => 'wpdmmodalloginbtn');
        $params = shortcode_atts($defaults, $params, 'wpdm_modal_login_form');
        $redirect = isset($params['redirect']) && $params['redirect'] !== '' ? "data-redirect='{$params['redirect']}'" : '';
        $logo = isset($params['logo']) && $params['logo'] !== '' ? "data-logo='{$params['logo']}'" : '';
        ob_start();
        ?>
        <div class="w3eden d-inline-block"><a href="#" <?php echo $redirect; ?> <?php echo $logo; ?> type="button"
                                              id="<?php echo $params['id']; ?>" class="<?php echo $params['class']; ?>"
                                              data-toggle="modal"
                                              data-target="#wpdmloginmodal"><?php echo $params['label']; ?></a></div>
        <?php
        $btncode = ob_get_clean();
        return $btncode;
    }

    /**
     * Modal login form
     * @param array $params
     * @return mixed|void
     */
    function modalPopupForm($params = array())
    {

        global $current_user;

        if (!isset($params) || !is_array($params)) $params = array();

        if (isset($params) && is_array($params))
            extract($params);
        if (!isset($redirect)) $redirect = get_permalink(get_option('__wpdm_user_dashboard'));

        if (!isset($regurl)) {
            $regurl = get_option('__wpdm_register_url');
            if ($regurl > 0)
                $regurl = get_permalink($regurl);
        }
        $log_redirect = $_SERVER['REQUEST_URI'];
        if (isset($params['redirect'])) $log_redirect = esc_url($params['redirect']);
        if (isset($_GET['redirect_to'])) $log_redirect = esc_url($_GET['redirect_to']);

        $up = parse_url($log_redirect);
        if (isset($up['host']) && $up['host'] != $_SERVER['SERVER_NAME']) $log_redirect = $_SERVER['REQUEST_URI'];

        $log_redirect = strip_tags($log_redirect);

        if (!isset($params['logo']) || $params['logo'] == '') $params['logo'] = get_site_icon_url();

        $__wpdm_social_login = get_option('__wpdm_social_login');
        $__wpdm_social_login = is_array($__wpdm_social_login) ? $__wpdm_social_login : array();

        ob_start();
        //get_option('__wpdm_modal_login', 0)

        include(Template::locate('modal-login-form.php', __DIR__.'/views'));

        $content = ob_get_clean();
        $content = apply_filters("wpdm_login_modal_form_html", $content);

        return $content;
    }

    function logoutURLShortcode($params)
    {
        $redirect = isset($params['r']) ? $params['r'] : '';
        return wpdm_logout_url($redirect);
    }

    function verifyLoginEmail($user, $user_login, $user_pass)
    {

        $user_email = null;
        if(!is_email($user_login) && !$user) {
            $_user = get_user_by('user_login', $user_login);
            if($_user)
                $user_email = $_user->user_email;
        } else if(is_email($user_login))
            $user_email = $user_login;
        else if($user && isset($user->user_email))
            $user_email = $user->user_email;

        if (is_email($user_email) && !wpdm_verify_email($user_email)) {
            $user = new \WP_Error();
            $emsg = get_option('__wpdm_blocked_domain_msg');
            if (trim($emsg) === '') $emsg = __('Your email address is blocked!', 'download-manager');
            $user->add('blocked_email', $emsg);
        }
        return $user;
    }

    /**
     * Modal login form
     * @param array $params
     * @return mixed|void
     */
    function modalForm($params = array())
    {

        global $current_user;

        if (!isset($params) || !is_array($params)) $params = array();

        if (isset($params) && is_array($params))
            extract($params);
        if (!isset($redirect)) $redirect = get_permalink(get_option('__wpdm_user_dashboard'));

        if (!isset($regurl)) {
            $regurl = get_option('__wpdm_register_url');
            if ($regurl > 0)
                $regurl = get_permalink($regurl);
        }
        $log_redirect = $_SERVER['REQUEST_URI'];
        if (isset($params['redirect'])) $log_redirect = esc_url($params['redirect']);
        if (isset($_GET['redirect_to'])) $log_redirect = esc_url($_GET['redirect_to']);

        $up = parse_url($log_redirect);
        if (isset($up['host']) && $up['host'] != $_SERVER['SERVER_NAME']) $log_redirect = $_SERVER['REQUEST_URI'];

        $log_redirect = strip_tags($log_redirect);

        if (!isset($params['logo']) || $params['logo'] == '') $params['logo'] = get_site_icon_url();

        $__wpdm_social_login = get_option('__wpdm_social_login');
        $__wpdm_social_login = is_array($__wpdm_social_login) ? $__wpdm_social_login : array();

        ob_start();
        //get_option('__wpdm_modal_login', 0)

        include(Template::locate('modal-login-form.php', __DIR__.'/views'));

        $content = ob_get_clean();
        $content = apply_filters("wpdm_login_modal_form_html", $content);

        return $content;
    }


}
