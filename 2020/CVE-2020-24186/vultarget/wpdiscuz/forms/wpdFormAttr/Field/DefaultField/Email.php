<?php

namespace wpdFormAttr\Field\DefaultField;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\Field;

class Email extends Field {

    protected $name = wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD;
    protected $isDefault = true;
    private $isAnonymous;

    protected function dashboardForm() {
        ?>
        <div class="wpd-field-body" style="display: <?php echo esc_attr($this->display); ?>">
            <div class="wpd-field-option wpdiscuz-item">
                <input class="wpd-field-type" type="hidden" value="<?php echo esc_attr($this->type); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[type]" />
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[name]"><?php esc_html_e("Name", "wpdiscuz"); ?>:</label> 
                <input class="wpd-field-name" type="text" value="<?php echo esc_attr($this->fieldData["name"]); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[name]" id="<?php echo esc_attr($this->fieldInputName); ?>[name]" required />
                <p class="wpd-info"><?php esc_html_e("Also used for field placeholder", "wpdiscuz"); ?></p>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[desc]"><?php esc_html_e("Description", "wpdiscuz"); ?>:</label> 
                <input type="text" value="<?php echo esc_attr($this->fieldData["desc"]); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[desc]" id="<?php echo esc_attr($this->fieldInputName); ?>[desc]" />
                <p class="wpd-info"><?php esc_html_e("Field specific short description or some rule related to inserted information.", "wpdiscuz"); ?></p>
            </div>
            <div class="wpd-field-option">
                <div class="input-group">
                    <label for="<?php echo esc_attr($this->fieldInputName); ?>[icon]"><span class="input-group-addon"></span> <?php esc_html_e("Field icon", "wpdiscuz"); ?>:</label>
                    <input data-placement="bottom" class="icp icp-auto" value="<?php echo esc_attr($this->fieldData["icon"]); ?>" type="text" name="<?php echo esc_attr($this->fieldInputName); ?>[icon]" id="<?php echo esc_attr($this->fieldInputName); ?>[icon]" />
                </div>
                <p class="wpd-info"><?php esc_html_e("Font-awesome icon library.", "wpdiscuz"); ?></p>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[required]"><?php esc_html_e("Field is required", "wpdiscuz"); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData["required"], 1, true); ?> name="<?php echo esc_attr($this->fieldInputName); ?>[required]" id="<?php echo esc_attr($this->fieldInputName); ?>[required]" />
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (!$currentUser->ID) {
            $hasIcon = $args["icon"] ? true : false;
            ?>
            <div class="wpdiscuz-item <?php echo esc_attr($name) . "-wrapper" . ($hasIcon ? " wpd-has-icon" : ""); ?>">
                <?php if ($hasIcon) { ?>
                    <div class="wpd-field-icon"><i class="<?php echo strpos(trim($args["icon"]), " ") ? esc_attr($args["icon"]) : "fas " . esc_attr($args["icon"]); ?>"></i></div>
                    <?php
                }
                $required = $args["required"] ? "required='required'" : "";
                ?>
                <input id="<?php echo esc_attr($name) . "-" . $uniqueId; ?>" value="" <?php echo $required; ?> class="<?php echo esc_attr($name); ?> wpd-field" type="email" name="<?php echo esc_attr($name); ?>" placeholder="<?php echo esc_attr($args["name"]) . (!empty($args["required"]) ? "*" : ""); ?>" />
                <label for="<?php echo esc_attr($name) . "-" . $uniqueId; ?>" class="wpdlb"><?php echo esc_attr($args["name"]) . (!empty($args["required"]) ? "*" : ""); ?></label>
                <?php if ($args["desc"]) { ?>
                    <div class="wpd-field-desc"><i class="far fa-question-circle"></i><span><?php echo esc_html($args["desc"]); ?></span></div>
                        <?php } ?>
            </div>
            <?php
        }
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name" => esc_html__("Email", "wpdiscuz"),
            "desc" => "",
            "icon" => "fas fa-at",
            "required" => "0",
        ];
    }

    public function isAnonymous() {
        return $this->isAnonymous;
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        $email = isset($_POST[$fieldName]) ? trim($_POST[$fieldName]) : "";
        if (!$args["required"]) {
            if (!$email) {
                $email = uniqid() . "@example.com";
                $this->isAnonymous = true;
            }
        }

        if ($email != "" && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $messageArray["code"] = "wc_error_email_text";
            wp_die(json_encode($messageArray));
        }

        return $email;
    }

    public function frontHtml($value, $args) {
        
    }

    public function editCommentHtml($key, $value, $data, $comment) {
        
    }

}
