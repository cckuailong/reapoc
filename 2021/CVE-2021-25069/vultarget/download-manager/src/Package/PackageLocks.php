<?php

namespace WPDM\Package;

global $gp1c, $tbc;


use WPDM\__\__;
use WPDM\__\Crypt;
use WPDM\__\Session;
use WPDM\__\Template;


class PackageLocks
{

    public function __construct(){

    }

    public static function askPassword($package){
        ob_start();
        $unqid = uniqid();
        $field_id = $unqid.'_'.$package['ID'];
        include Template::locate("lock-options/password-lock.php", __DIR__.'/views');
        $data = ob_get_clean();
        return $data;
    }

    public static function reCaptchaLock($package, $buttononly = false){
        ob_start();
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        include Template::locate("lock-options/recaptcha-lock.php", __DIR__.'/views');
        return ob_get_clean();
    }

    function validateCaptcha()
    {
        $limit = get_option('__wpdm_private_link_usage_limit', 3);
        $xpire_period = ((int)get_option('__wpdm_private_link_expiration_period', 3)) * ((int)get_option('__wpdm_private_link_expiration_period_unit', 60));
        $xpire_period = $xpire_period > 0 ? $xpire_period : 3600;
        $ret = wpdm_remote_post('https://www.google.com/recaptcha/api/siteverify', array('secret' => get_option('_wpdm_recaptcha_secret_key'), 'response' => wpdm_query_var('reCaptchaVerify'), 'remoteip' => $_SERVER['REMOTE_ADDR']));
        $ret = json_decode($ret);
        if ($ret->success == 1) {
            $download_url = WPDM()->package->expirableDownloadLink(wpdm_query_var('__wpdm_ID', 'int'), $limit, $xpire_period);
            $data['downloadurl'] = $download_url;
        } else {
            $data['error'] = __("Captcha Verification Failed!", "wpmdpro");
        }

        wp_send_json($data);
        die();
    }

    function validatePassword()
    {
        $password = wpdm_query_var('password', ['validate' => 'html']);
        $packageID = wpdm_query_var('__wpdm_ID', ['validate' => 'int']);
        $passwords = WPDM()->package->isPasswordProtected($packageID);
        $passwordUsage = maybe_unserialize(get_post_meta($packageID, '__wpdm_password_usage', true));
        $passwordUsageLimit = (int)get_post_meta($packageID, 'password_usage_limit', true);

        $limit = get_option('__wpdm_private_link_usage_limit', 3);
        $expirePeriod = ((int)get_option('__wpdm_private_link_expiration_period', 60)) * ((int)get_option('__wpdm_private_link_expiration_period_unit', 60));
        $expirePeriod = $expirePeriod > 0 ? $expirePeriod : 3600;

        $data = [ 'success' => false ];
        $error = false;
        //Check if the given password is matched
        if ($passwords && $password != $passwords && substr_count($passwords, "[$password]") < 1) {
            $data['message'] = __("Wrong Password!", "download-manager") . " &nbsp; <span><i class='fas fa-redo'></i> " . __("Try Again", "download-manager") . " </span>";
            $error = true;
        }

        //Check if given password is empty
        if ($passwords && $password == '') {
            $data['message'] = __("Wrong Password!", "download-manager") . " &nbsp; <span class='color-blue'><i class='fas fa-redo'></i> " . __("Try Again", "download-manager") . " </span>";
            $file = array();
            $error = true;
        }

        $passwordUsed = wpdm_valueof($passwordUsage, $password, ['validate' => 'int']);
        if( $passwordUsageLimit > 0  && $passwordUsed >= $passwordUsageLimit)
            $data['message'] = __("Password usages limit exceeded", "download-manager");
        else if(!$error){
            if(!is_array($passwordUsage)) $passwordUsage = [];
            Session::set("pass_verified_" . $packageID, 1);
            $passwordUsage[$password] = wpdm_valueof($passwordUsage, $password, ['validate' => 'int']) + 1;
            update_post_meta($packageID, '__wpdm_password_usage', $passwordUsage);
            $data = ['success' => true, 'downloadurl' => WPDM()->package->expirableDownloadLink($packageID, $limit, $expirePeriod)];
        }
        wp_send_json($data);
    }


}
