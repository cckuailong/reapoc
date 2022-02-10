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

class EditProfile
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
        add_action("init", [$this, 'logout']);
        add_action('init', [$this, 'updateProfile']);
        add_shortcode("wpdm_edit_profile", [$this, 'editProfile']);
    }

    function editProfile()
    {
        ob_start();
        include Template::locate("dashboard/edit-profile.php", __DIR__ . '/views');
        return ob_get_clean();
    }

    function logout()
    {
        if (isset($_REQUEST['logout']) && wp_verify_nonce(wpdm_query_var('logout'), NONCE_KEY)) {
            wp_logout();
            wp_safe_redirect(wpdm_login_url());
            die();
        }
    }

    function updateProfile()
    {
        global $current_user;

        if (isset($_POST['wpdm_profile']) && is_user_logged_in() && wp_verify_nonce(wpdm_query_var('__wpdm_epnonce'), NONCE_KEY)) {

            $error = 0;

            $pfile_data['display_name'] = wpdm_sanitize_var($_POST['wpdm_profile']['display_name']);
            $pfile_data['user_email'] = sanitize_email($_POST['wpdm_profile']['user_email']);


            if ($_POST['password'] != $_POST['cpassword']) {
                Session::set('member_error', 'Password not matched');
                $error = 1;
            }
            if (!$error) {
                $pfile_data['ID'] = $current_user->ID;
                if ($_POST['password'] != '')
                    $pfile_data['user_pass'] = $_POST['password'];

                wp_update_user($pfile_data);

                update_user_meta($current_user->ID, 'payment_account', $_POST['payment_account']);
                Session::set('member_success', 'Profile data updated successfully.');
            }

            do_action("wpdm_update_profile", $current_user->ID);

            if (wpdm_is_ajax()) {
                if (ob_get_length() > 0) @ob_clean();
                if ($error == 1) {
                    $msg['type'] = 'danger';
                    $msg['title'] = 'ERROR!';
                    $msg['msg'] = Session::get('member_error');
                    Session::clear('member_error');
                    wp_send_json($msg);
                    die();
                } else {
                    $msg['type'] = 'success';
                    $msg['title'] = 'DONE!';
                    $msg['msg'] = Session::get('member_success');
                    Session::clear('member_success');
                    $msg['data'] = $pfile_data;
                    wp_send_json($msg);
                    die();
                }
            }
            header("location: " . $_SERVER['HTTP_REFERER']);
            die();
        }
    }
}
