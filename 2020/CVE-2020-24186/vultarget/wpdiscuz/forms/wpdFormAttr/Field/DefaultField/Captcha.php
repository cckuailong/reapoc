<?php

namespace wpdFormAttr\Field\DefaultField;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\Field;
use wpdFormAttr\Field\DefaultField\ReCaptcha;

class Captcha extends Field {

    protected $name = wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD;
    protected $isDefault = true;
    private $reCaptcha;

    protected function dashboardForm() {
        ?>
        <div class="wpd-field-body" style="display: <?php echo esc_attr($this->display); ?>">
            <a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . \WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=" . \WpdiscuzCore::TAB_RECAPTCHA)); ?>"><?php esc_html_e("reCAPTCHA Settings", "wpdiscuz"); ?></a>
            <input class="wpd-field-type" type="hidden" value="<?php echo esc_attr($this->type); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[type]" />
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    private function initRecaptcha($generalOptions) {
        $secretKey = apply_filters("wpdiscuz_recaptcha_secret", $generalOptions->recaptcha["secretKey"]);
        try {
            $requestMethod = $this->createRequestMethod($generalOptions);
            $this->reCaptcha = new ReCaptcha\ReCaptcha($secretKey, $requestMethod);
        } catch (\RuntimeException $ex) {
            wp_die("reCAPTCHA Exception : " . $ex->getMessage());
        }
    }

    private function createRequestMethod($generalOptions) {
        if ($generalOptions->recaptcha["requestMethod"] != "auto") {
            if ($generalOptions->recaptcha["requestMethod"] === "socket") {
                return new ReCaptcha\RequestMethod\SocketPost();
            } else if ($generalOptions->recaptcha["requestMethod"] === "curl") {
                return new ReCaptcha\RequestMethod\CurlPost();
            } else if ($generalOptions->recaptcha["requestMethod"] === "post") {
                return new ReCaptcha\RequestMethod\Post();
            }
        } else {
            if (extension_loaded("curl")) {
                return new ReCaptcha\RequestMethod\CurlPost();
            }

            if (function_exists("fsockopen")) {
                return new ReCaptcha\RequestMethod\SocketPost();
            }

            if (ini_get("allow_url_fopen")) {
                return new ReCaptcha\RequestMethod\Post();
            }
        }
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        $version = apply_filters("wpdiscuz_recaptcha_version", $options->recaptcha["version"]);
        $key = apply_filters("wpdiscuz_recaptcha_site_key", $options->recaptcha["siteKey"]);
        $secret = apply_filters("wpdiscuz_recaptcha_secret", $options->recaptcha["secretKey"]);
        if ($this->isShowCaptcha($currentUser->ID, $options) && $key && $secret && $version == "2.0") {
            ?>
            <div class="wpd-field-captcha wpdiscuz-item">
                <div class="wpdiscuz-recaptcha" id='wpdiscuz-recaptcha-<?php echo esc_attr($uniqueId); ?>'></div>
                <input id='wpdiscuz-recaptcha-field-<?php echo esc_attr($uniqueId); ?>' type='hidden' name='wc_captcha' value="" required="required" class="wpdiscuz_reset"/>
                <div class="clearfix"></div>
            </div>
            <?php
        }
        do_action("wpdiscuz_captcha_field", $args, $currentUser, $uniqueId, $isMainForm);
    }

    public function sanitizeFieldData($data) {
        $cleanData = [];
        $cleanData["type"] = $data["type"];
        if (isset($data["show_for_guests"])) {
            $cleanData["show_for_guests"] = intval($data["show_for_guests"]);
        }
        if (isset($data["show_for_users"])) {
            $cleanData["show_for_users"] = intval($data["show_for_users"]);
        }
        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        if ($currentUser && $this->isShowCaptcha($currentUser->ID, $options)) {
            $this->initRecaptcha($options);
            $recaptchaResponse = filter_input(INPUT_POST, "g-recaptcha-response", FILTER_SANITIZE_STRING);
            $resp = $this->reCaptchaVerify($recaptchaResponse, $options, "wpdiscuz/addComment");
            if (!$resp->isSuccess()) {
                $errorMesage = esc_html__("reCAPTCHA  verification failed.", "wpdiscuz");
                $errors = $resp->getErrorCodes();
                if ($errors) {
                    $errorMesage = "";
                    $errorMesages = [
                        "missing-input-secret" => esc_html__("The secret parameter is missing.", "wpdiscuz"),
                        "invalid-input-secret" => esc_html__("The secret parameter is invalid or malformed.", "wpdiscuz"),
                        "missing-input-response" => esc_html__("The response parameter is missing.", "wpdiscuz"),
                        "invalid-input-response" => esc_html__("The response parameter is invalid or malformed.", "wpdiscuz"),
                        "bad-request" => esc_html__("The request is invalid or malformed.", "wpdiscuz"),
                        "timeout-or-duplicate" => esc_html__("The response is no longer valid: either is too old or has been used previously.", "wpdiscuz"),
                    ];
                    foreach ($errors as $error) {
                        if (isset($errorMesages[$error])) {
                            $errorMesage .= $errorMesages[$error] . "<br>";
                        } else {
                            $errorMesage .= esc_html__("reCaptcha validation fails. Error code: ", "wpdiscuz") . $error . "<br>";
                        }
                    }
                }
                wp_die($errorMesage);
            }
        }
    }

    public function subscribtionRecaptchaHtml($options) {
        $version = apply_filters("wpdiscuz_recaptcha_version", $options->recaptcha["version"]);
        $key = apply_filters("wpdiscuz_recaptcha_site_key", $options->recaptcha["siteKey"]);
        $secret = apply_filters("wpdiscuz_recaptcha_secret", $options->recaptcha["secretKey"]);
        if (!is_user_logged_in() && $options->recaptcha["isShowOnSubscribeForm"] && $key && $secret) {
            if ($version == "2.0") {
                ?>
                <div class="wpd-field-captcha wpdiscuz-item">
                    <div class="wpdiscuz-recaptcha" id='wpdiscuz-recaptcha-subscribe-form'></div>
                    <input id='wpdiscuz-recaptcha-field-subscribe-form' type='hidden' name='wpdiscuz_recaptcha_subscribe_form' value="" required="required" class="wpdiscuz_reset"/>
                    <div class="clearfix"></div>
                </div>
                <?php
            } else {
                ?>
                <input id='wpdiscuz-recaptcha-field-subscribe-form' type='hidden' name='g-recaptcha-response' value=""  class="wpdiscuz_reset"/>
                <?php
            }
        }
    }

    public function reCaptchaValidate($options) {
        $valid = true;
        $recaptchaResponse = filter_input(INPUT_POST, "g-recaptcha-response", FILTER_SANITIZE_STRING);
        $this->initRecaptcha($options);
        if ($recaptchaResponse) {
            $resp = $this->reCaptchaVerify($recaptchaResponse, $options, "wpdiscuz/wpdAddSubscription");
            if (!$resp->isSuccess()) {
                $valid = false;
            }
        } else {
            $valid = false;
        }
        return $valid;
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name" => "",
            "desc" => "",
            "show_for_guests" => "0",
            "show_for_users" => "0"
        ];
    }

    private function reCaptchaVerify($token, $options, $action = "") {
        $recaptchaVersion = apply_filters("wpdiscuz_recaptcha_version", $options->recaptcha["version"]);
        if ($recaptchaVersion == "2.0") {
            $resp = $this->reCaptcha->verify($token, $this->getIP());
        } else {
            $score = apply_filters("wpdiscuz_recaptcha_score", $options->recaptcha["score"]);
            $resp = $this->reCaptcha->setExpectedAction($action)
                    ->setScoreThreshold($score)
                    ->verify($token, $this->getIP());
        }
        return $resp;
    }

    private function getIP() {
        $ip = "";
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        return $ip;
    }

    /**
     * check if the captcha field show or not
     * @return type boolean 
     */
    public function isShowCaptcha($isUserLoggedIn, $options) {
        return ($isUserLoggedIn && $options->recaptcha["showForUsers"]) || (!$isUserLoggedIn && $options->recaptcha["showForGuests"]);
    }

    public function editCommentHtml($key, $value, $data, $comment) {
        
    }

    public function frontHtml($value, $args) {
        
    }

}
