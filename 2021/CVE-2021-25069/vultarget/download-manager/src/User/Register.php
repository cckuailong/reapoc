<?php


namespace WPDM\User;

use WPDM\__\__;
use WPDM\__\Crypt;
use WPDM\__\Email;
use WPDM\__\__MailUI;
use WPDM\__\Session;
use WPDM\__\Template;
use WPDM\Form\Form;


if(!defined("ABSPATH")) die("Shit happens!");

class Register
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
        add_shortcode('wpdm_reg_form', [$this, 'form']);

        add_action('registration_errors', [$this, 'verifyEmail'], 10, 3);

    }

    static function formFields($params = [])
    {
        $reg_data_fields['__phash'] = ['type' => 'hidden', 'attrs' => ['name' => '__phash', 'id' => '__phash', 'value' => Crypt::encrypt($params)]];
        $reg_data_fields['__reg_nonce'] = ['type' => 'hidden', 'attrs' => ['name' => '__reg_nonce', 'id' => '__reg_nonce', 'value' => '']];
        $reg_data_fields['loginurl'] = ['type' => 'hidden', 'attrs' => ['name' => 'loginurl', 'id' => 'loginurl', 'value' => $params['loginurl']]];
        $reg_data_fields['permalink'] = ['type' => 'hidden', 'attrs' => ['name' => 'permalink', 'id' => 'permalink', 'value' => $params['permalink']]];
        $reg_data_fields['reg_redirect'] = ['type' => 'hidden', 'attrs' => ['name' => 'reg_redirect', 'id' => 'reg_redirect', 'value' => $params['reg_redirect']]];
        $reg_data_fields['name'] = [
            'cols' => [
                ['label' => __("First name", "download-manager"), 'type' => 'text', 'grid_class' => 'col-md-6 col-sm-12', 'attrs' => ['name' => 'wpdm_reg[first_name]', 'id' => 'first_name', 'placeholder' => __('Your First Name', 'download-manager'), 'required' => 'required']],
                ['label' => __("Last name", "download-manager"), 'type' => 'text', 'grid_class' => 'col-md-6 col-sm-12', 'attrs' => ['name' => 'wpdm_reg[last_name]', 'id' => 'last_name', 'placeholder' => __('Your Last Name', 'download-manager'), 'required' => 'required']]
            ],
        ];
        $reg_data_fields['username'] = array(
            'label' => __("Username", "download-manager"),
            'type' => 'text',
            'attrs' => array('name' => 'wpdm_reg[user_login]', 'id' => 'user_login', 'placeholder' => __('User Login ID', 'download-manager'), 'required' => 'required'),
        );
        $reg_data_fields['email'] = [
            'label' => __("Email", "download-manager"),
            'type' => 'email',
            'attrs' => ['name' => 'wpdm_reg[user_email]', 'id' => 'user_email', 'placeholder' => __("Your Email Address", "download-manager"), 'required' => 'required'],
        ];

        if ((int)get_option('__wpdm_signup_email_verify', 0) === 0) {
            $reg_data_fields['password'] = array(
                'cols' => [
                    ['label' => __("Password", "download-manager"), 'type' => 'password', 'grid_class' => 'col-md-6 col-sm-12', 'attrs' => ['name' => 'wpdm_reg[user_pass]', 'id' => 'reg_password', 'placeholder' => __("Be Secure", "download-manager"), 'required' => 'required']],
                    ['label' => __("Confirm Password", "download-manager"), 'type' => 'password', 'grid_class' => 'col-md-6 col-sm-12', 'attrs' => ['name' => 'confirm_user_pass', 'data-match' => '#reg_password', 'id' => 'reg_confirm_pass', 'placeholder' => __("Do Not Forget", "download-manager"), 'required' => 'required']]
                ]
            );
        }
        if (!isset($params['captcha']) || $params['captcha'] === true) {
            $show_captcha = (int)get_option('__wpdm_recaptcha_regform', 0) === 1 && get_option('_wpdm_recaptcha_secret_key', '') != '';
            $show_captcha = apply_filters("signup_form_captcha", $show_captcha);
            if ($show_captcha) {
                $reg_data_fields['__recap'] = array(
                    'type' => 'reCaptcha',
                    'attrs' => array('name' => '__recap', 'id' => '__recap'),
                );
            }
        }
        $reg_data_fields = apply_filters("wpdm_register_form_fields", $reg_data_fields);
        $form = new Form($reg_data_fields, ['name' => 'wpdm_reg_form', 'id' => 'wpdm_reg_form', 'method' => 'POST', 'action' => '', 'submit_button' => [], 'noForm' => true]);
        return $form->render();

    }

    /**
     * @usage Short-code callback function for login form
     * @return string
     */
    function form($params = array())
    {

        if (!get_option('users_can_register')) return \WPDM\__\Messages::warning(__("User registration is disabled", "download-manager"), -1);

        if (!isset($params) || !is_array($params)) $params = array();

        ob_start();

        $_social_only = isset($params['social_only']) && ($params['social_only'] === 'true' || (int)$params['social_only'] === 1) ? true : false;
        $_verify_email = isset($params['verifyemail']) && ($params['verifyemail'] === 'true' || (int)$params['verifyemail'] === 1) ? true : false;
        $_show_captcha = !isset($params['captcha']) || ($params['captcha'] === 'true' || (int)$params['captcha'] === 1) ? true : false;
        $_auto_login = isset($params['autologin']) && ($params['autologin'] === 'true' || (int)$params['autologin'] === 1) ? true : false;


        $loginurl = wpdm_login_url();
        $reg_redirect = $loginurl;
        if (isset($params['autologin']) && (int)$params['autologin'] === 1) $reg_redirect = wpdm_user_dashboard_url();
        if (isset($params['redirect'])) $reg_redirect = esc_url_raw($params['redirect']);
        if (isset($_GET['redirect_to'])) $reg_redirect = esc_url_raw($_GET['redirect_to']);

        $force = uniqid();

        $up = parse_url($reg_redirect);
        if (isset($up['host']) && $up['host'] != $_SERVER['SERVER_NAME']) $reg_redirect = home_url('/');

        $reg_redirect = esc_attr(esc_url($reg_redirect));

        if (!isset($params['logo'])) $params['logo'] = get_site_icon_url();

        \WPDM\__\Session::set('__wpdm_reg_params', $params);

        $tmp_reg_info = \WPDM\__\Session::get('tmp_reg_info');

        $__wpdm_social_login = get_option('__wpdm_social_login');
        $__wpdm_social_login = is_array($__wpdm_social_login) ? $__wpdm_social_login : array();

        $params['loginurl'] = $loginurl;
        $params['reg_redirect'] = $reg_redirect;
        $params['permalink'] = get_permalink(get_the_ID());
        $form_html = self::formFields($params);

        //Template
        include(Template::locate('reg-form.php', __DIR__.'/views'));

        $content = ob_get_clean();
        return $content;
    }

    /**
     * @usage Register an user
     */
    function process()
    {
        global $wp_query, $wpdb;
        if (!isset($_POST['wpdm_reg'])) return;


        $shortcode_params = Crypt::decrypt(wpdm_query_var('__phash'), true);

        if (!is_array($shortcode_params)) $shortcode_params = array();

        $_verify_email = (int)get_option('__wpdm_signup_email_verify', 0);

        if (!isset($_REQUEST['__reg_nonce']) || !wp_verify_nonce($_REQUEST['__reg_nonce'], NONCE_KEY)) {
            $reg_error = apply_filters("wpdm_signup_error", __("Something is Wrong! Please refresh the page and try again", "download-manager"), $error_type = 'nonce');
            if (wpdm_is_ajax()) {
                wp_send_json(array('success' => false, 'message' => 'Error: ' . $reg_error));
                die();
            }
            Session::set('wpdm_signup_error', $reg_error, 300);
            wp_safe_redirect(wpdm_query_var('permalink', 'url'));
            die();
        }

        if(!isset($shortcode_params['captcha']) || $shortcode_params['captcha'] ===  true) {
            $active_captcha = (int)get_option('__wpdm_recaptcha_regform', 0) === 1 && get_option('_wpdm_recaptcha_secret_key', '') != '';
            $active_captcha = apply_filters("signup_form_captcha", $active_captcha);
            if ($active_captcha) {
                $ret = wpdm_remote_post('https://www.google.com/recaptcha/api/siteverify', array('secret' => get_option('_wpdm_recaptcha_secret_key', ''), 'response' => wpdm_query_var('__recap')));
                $ret = json_decode($ret);
                if (!$ret->success) {
                    $reg_error = apply_filters("wpdm_signup_error", __("Invalid CAPTCHA!", "download-manager"), $error_type = 'captcha');
                    if (wpdm_is_ajax()) {
                        wp_send_json(array('success' => false, 'message' => 'Error: ' . $reg_error));
                        die();
                    }
                    Session::set('wpdm_signup_error', $reg_error, 300);
                    wp_safe_redirect(wpdm_query_var('permalink', 'url'));
                    die();
                }
            }
        }

        if (!get_option('users_can_register') && isset($_POST['wpdm_reg'])) {
            $reg_error = apply_filters("wpdm_signup_error", __("Error: User registration is disabled!", "download-manager"), $error_type = 'signup_disabled');
            if (wpdm_is_ajax()) {
                wp_send_json(array('success' => false, 'message' => $reg_error));
                die();
            }
            Session::set('wpdm_signup_error', $reg_error, 300);
            wp_safe_redirect(wpdm_query_var('permalink', 'url'));
            die();
        }

        Session::set('tmp_reg_info', $_POST['wpdm_reg']);

        $first_name = wpdm_query_var('wpdm_reg/first_name', 'txt');
        $last_name = wpdm_query_var('wpdm_reg/last_name', 'txt');
        $user_login = wpdm_query_var('wpdm_reg/user_login', 'txt');
        $user_email = wpdm_query_var('wpdm_reg/user_email', 'email');
        $user_pass = wpdm_query_var('wpdm_reg/user_pass', 'html');
        $full_name = $first_name . " " . $last_name;
        $display_name = $full_name;

        $user_id = username_exists($user_login);

        //Check Username
        if ($user_login === '') {

            $reg_error = apply_filters("wpdm_signup_error", __("Username is Empty!", "download-manager"), $error_type = 'empty_username');

            if (wpdm_is_ajax()) {
                wp_send_json(array('success' => false, 'message' => $reg_error));
                die();
            }

            Session::set('wpdm_signup_error', $reg_error, 300);
            wp_safe_redirect(wpdm_query_var('permalink', 'url'));
            die();
        }

        //Check Email
        if (!is_email($user_email)) {

            $reg_error = apply_filters("wpdm_signup_error", __("Invalid email address!", "download-manager"), $error_type = 'invalid_email');

            if (wpdm_is_ajax()) {
                wp_send_json(array('success' => false, 'message' => $reg_error));
                die();
            }

            Session::set('wpdm_signup_error', $reg_error, 300);
            wp_safe_redirect(wpdm_query_var('permalink', 'url'));

        }

        //If username is available - no other user with the username
        if (!$user_id) {

            $user_id = email_exists($user_email);

            //If email is available - no other user with the email
            if (!$user_id) {

                $user_pass = $_verify_email || !isset($user_pass) || $user_pass == '' ? wp_generate_password(12, false) : $user_pass;

                if(!$_verify_email && isset($user_pass) && wpdm_query_var('comfirm_user_pass')){
                    if($user_pass !== wpdm_query_var('confirm_user_pass', 'html')){
                        $reg_error = apply_filters("wpdm_signup_error", __("Passwords not matched!", "download-manager"), $error_type = 'password_match');

                        if (wpdm_is_ajax()) {
                            wp_send_json(array('success' => false, 'message' => $reg_error));
                            die();
                        }

                        Session::set('wpdm_signup_error', $reg_error, 300);
                        wp_safe_redirect(wpdm_query_var('permalink', 'url'));
                    }
                }

                $errors = new \WP_Error();

                do_action('register_post', $user_login, $user_email, $errors);

                $errors = apply_filters('registration_errors', $errors, $user_login, $user_email);

                if ($errors->get_error_code()) {
                    if (wpdm_is_ajax()) {
                        wp_send_json(array('success' => false, 'message' => 'Error: ' . $errors->get_error_message()));
                        die();
                    }

                    Session::set('wpdm_signup_error', 'Error: ' . $errors->get_error_message(), 300);
                    wp_safe_redirect(wpdm_query_var('permalink', 'url'));
                    die();
                }

                $user_id = wp_create_user($user_login, $user_pass, $user_email);

                $user_meta = array('ID' => $user_id, 'display_name' => $display_name, 'first_name' => $first_name, 'last_name' => $last_name);
                wp_update_user($user_meta);

                if(!defined('WPDM_DISABLE_SIGNUP_ROLES') || WPDM_DISABLE_SIGNUP_ROLES === false) {
                    $valid_roles = get_option('__wpdm_signup_roles', array());

                    if (isset($shortcode_params['role']) && trim($shortcode_params['role']) !== '' && is_array($valid_roles) && count($valid_roles) > 0 && in_array($shortcode_params['role'], $valid_roles)) {
                        $role = $shortcode_params['role'];
                        //Block front-end signup as administrator or any role with edit_files cap
                        if($role !== 'administrator') {
                            $_role = get_role($role);
                            $caps = is_object($_role) && isset($_role->capabilities) ? $_role->capabilities : array();
                            if (!in_array('edit_files', $caps)) {
                                $user = get_user_by('id', $user_meta['ID']);
                                $user->add_role($role);
                            }
                        }

                    }
                }


                //To User
                $usparams = array('to_email' => $user_email, 'name' => $display_name, 'first_name' => $first_name, 'last_name' => $last_name, 'user_email' => $user_email, 'username' => $user_login, 'password' => $user_pass);

                foreach ($_POST['wpdm_reg'] as $key => $value) {
                    $usparams["user_" . $key] = $value;
                }

                //Filter user email params
                $usparams = apply_filters("wpdm_signup_user_mail_params", $usparams, $user_id);

                \WPDM\__\Email::send("user-signup", $usparams);

                //To Admin
                $ip = wpdm_get_client_ip();
                $data = array(
                    array('Name', $display_name),
                    array('Username', $user_login),
                    array('Email', $user_email),
                    array('IP', $ip)
                );
                $css = array('col' => array('background: #edf0f2 !important'), 'td' => 'border-bottom:1px solid #e6e7e8');
                $table = __MailUI::table(null, $data, $css);

                $params['name'] = $display_name;
                $params['username'] = $user_login;
                $params['email'] = $user_email;
                $params['user_ip'] = $ip;
                $params['edit_user_btn'] = "<a class='button' style='display:block;margin:10px 0 0;text-decoration: none;text-align:center;' href='" . admin_url('user-edit.php?user_id=' . $user_id) . "'> " . __("Edit User", "download-manager") . " </a>";
                //Include all data from signup form
                foreach ($_POST['wpdm_reg'] as $key => $value) {
                    $params["user_" . $key] = $value;
                }

                //Filter admin email params
                $params = apply_filters("wpdm_signup_admin_mail_params", $params, $user_id);
                \WPDM\__\Email::send("user-signup-admin", $params);

                Session::clear('guest_order');
                Session::clear('login_error');
                Session::clear('tmp_reg_info');

                $creds['user_login'] = $user_login;
                $creds['user_password'] = $user_pass;
                $creds['remember'] = true;

                if($_verify_email)
                    $reg_success = apply_filters("wpdm_signup_success", __("Your account has been created successfully and login info sent to your mail address.", "download-manager"));
                else
                    $reg_success = apply_filters("wpdm_signup_success", __("Your account has been created successfully.", "download-manager"));

                do_action("wpdm_user_signup", $user_id, wpdm_query_var('wpdm_reg') );

                $_auto_login = isset($shortcode_params['autologin']) && ($shortcode_params['autologin'] === 'true' || (int)$shortcode_params['autologin'] === 1) ? true : false;
                if ((int)get_option('__wpdm_signup_autologin', 0) === 1|| $_auto_login) {
                    $reg_success = apply_filters("wpdm_signup_success", __("Your account has been created successfully.", "download-manager"));
                    wp_signon($creds);
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                }

                \WPDM\__\Session::set('__wpdm_signup_success', $reg_success, 300);

                if (wpdm_is_ajax()) {
                    $redirect = wpdm_query_var('reg_redirect', 'url');
                    if(!$_auto_login)
                        $redirect = add_query_arg(['signedup' => 1], $redirect);
                    wp_send_json(array('success' => true, 'redirect_to' => $redirect, 'msg' => Session::get('__wpdm_signup_success')));
                    die();
                }
                wp_safe_redirect(wpdm_query_var('reg_redirect', 'url'));
                die();
            } else {
                $reg_error = apply_filters("wpdm_signup_error", __("Email already exist!", "download-manager"), $error_type = 'invalid_email');

                if (wpdm_is_ajax()) {
                    wp_send_json(array('success' => false, 'message' => $reg_error));
                    die();
                }

                \WPDM\__\Session::set('wpdm_signup_error', $reg_error);
                wp_safe_redirect(wpdm_query_var('permalink', 'url'));

                die();
            }
        } else {
            $reg_error = apply_filters("wpdm_signup_error", __("Username already exists.", "download-manager"), $error_type = 'username_exists');

            if (wpdm_is_ajax()) {
                wp_send_json(array('success' => false, 'message' => $reg_error));
                die();
            }

            Session::set('wpdm_signup_error', $reg_error, 300);
            wp_safe_redirect(wpdm_query_var('permalink', 'url'));
            die();
        }

    }


    function verifyEmail($errors, $sanitized_user_login, $user_email)
    {
        if (!$errors) $errors = new \WP_Error();
        if (!wpdm_verify_email($user_email)) {
            $emsg = get_option('__wpdm_blocked_domain_msg');
            if (trim($emsg) === '') $emsg = __('Your email address is blocked!', 'download-manager');
            $errors->add('blocked_email', $emsg);
        }
        return $errors;
    }

}

