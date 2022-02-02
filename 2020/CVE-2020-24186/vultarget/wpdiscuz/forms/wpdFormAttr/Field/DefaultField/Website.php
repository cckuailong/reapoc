<?php

namespace wpdFormAttr\Field\DefaultField;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\Field;

class Website extends Field {

    protected $name = wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD;
    protected $isDefault = true;

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
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[enable]"><?php esc_html_e("Enable", "wpdiscuz"); ?>:</label>
                <input type="checkbox" value="1" <?php checked($this->fieldData["enable"], 1, true); ?> name="<?php echo esc_attr($this->fieldInputName); ?>[enable]" id="<?php echo esc_attr($this->fieldInputName); ?>[enable]" />
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (!$currentUser->ID) {
            if ($args["enable"]) {
                $hasIcon = $args["icon"] ? true : false;
                ?>
                <div class="wpdiscuz-item <?php echo esc_attr($name) . "-wrapper" . ($hasIcon ? " wpd-has-icon" : ""); ?>">
                    <?php if ($hasIcon) { ?>
                        <div class="wpd-field-icon"><i class="<?php echo strpos(trim($args["icon"]), " ") ? esc_attr($args["icon"]) : "fas " . esc_attr($args["icon"]); ?>"></i></div>
                    <?php } ?>
                    <input id="<?php echo esc_attr($name) . "-" . $uniqueId; ?>" value="" class="<?php echo $name; ?> wpd-field" type="text" name="<?php echo htmlentities($name, ENT_QUOTES); ?>" placeholder="<?php echo htmlentities($args["name"], ENT_QUOTES); ?>" />
                    <label for="<?php echo esc_attr($name) . "-" . $uniqueId; ?>" class="wpdlb"><?php echo htmlentities($args["name"], ENT_QUOTES); ?></label>
                    <?php if ($args["desc"]) { ?>
                        <div class="wpd-field-desc"><i class="far fa-question-circle"></i><span><?php echo esc_html($args["desc"]); ?></span></div>
                            <?php } ?>
                </div>
                <?php
            }
        }
    }

    public function sanitizeFieldData($data) {
        $cleanData = [];
        $cleanData["type"] = $data["type"];
        if (isset($data["name"])) {
            $name = trim(strip_tags($data["name"]));
            $cleanData["name"] = $name ? $name : $this->fieldDefaultData["name"];
        }
        if (isset($data["desc"])) {
            $cleanData["desc"] = trim(strip_tags($data["desc"]));
        }
        if (isset($data["icon"])) {
            $cleanData["icon"] = trim(strip_tags($data["icon"]));
        }
        if (isset($data["enable"])) {
            $cleanData["enable"] = intval($data["enable"]);
        }
        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name" => esc_html__("Website", "wpdiscuz"),
            "desc" => "",
            "icon" => "fas fa-link",
            "enable" => "0",
        ];
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        $website_url = trim(filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_STRING));
        if ($website_url != "") {
            if (strpos($website_url, "http://") !== 0 && strpos($website_url, "https://") !== 0) {
                $website_url = "http://" . $website_url;
            }

            if (filter_var($website_url, FILTER_VALIDATE_URL) === false) {
                $messageArray["code"] = "wc_error_url_text";
                wp_die(json_encode($messageArray));
            }
        }
        return esc_url_raw($website_url, ["http", "https"]);
    }

    public function editCommentHtml($key, $value, $data, $comment) {
        
    }

    public function frontHtml($value, $args) {
        
    }

}
