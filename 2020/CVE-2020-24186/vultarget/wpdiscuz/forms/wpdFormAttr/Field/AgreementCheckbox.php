<?php

namespace wpdFormAttr\Field;

class AgreementCheckbox extends Field {

    protected function dashboardForm() {
        ?>
        <div class="wpd-field-body" style="display: <?php echo esc_attr($this->display); ?>">
            <div class="wpd-field-option wpdiscuz-item">
                <input class="wpd-field-type" type="hidden" value="<?php echo esc_attr($this->type); ?>" name="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[type]" />
                <label for="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[name]"><?php esc_html_e("Name", "wpdiscuz"); ?>:</label> 
                <input class="wpd-field-name" type="text" value="<?php echo htmlentities($this->fieldData["name"], ENT_QUOTES); ?>" name="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[name]" id="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[name]" required />
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[desc]"><?php esc_html_e("Description", "wpdiscuz"); ?>:</label> 
                <input type="text" value="<?php echo htmlentities($this->fieldData["desc"], ENT_QUOTES); ?>" name="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[desc]" id="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[desc]" />
                <p class="wpd-info"><?php esc_html_e("Field specific short description or some rule related to inserted information.", "wpdiscuz"); ?></p>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[label]"><?php esc_html_e("Checkbox Label", "wpdiscuz"); ?>:</label>
                <p class="wpd-info"><?php esc_html_e("You can use HTML tags to add links to website Terms and Privacy Policy pages. For example: ", "wpdiscuz"); ?><br>
                    <code><?php echo esc_html("I agree to the <a href='https://example.com/terms/' target='_blank'>Terms</a> and <a href='https://example.com/privacy/' target='_blank'>Privacy Policy</a>"); ?></code>
                </p>
                <textarea required="required" type="text" name="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[label]" id="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[label]" style="height: 75px;width:100%"><?php echo htmlentities($this->fieldData["label"]); ?></textarea>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[required]"><?php esc_html_e("Field is required", "wpdiscuz"); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData["required"], 1, true); ?> name="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[required]" id="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[required]" />
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[is_show_sform]"><?php esc_html_e("Display on reply form", "wpdiscuz"); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData["is_show_sform"], 1, true); ?> name="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[is_show_sform]" id="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[is_show_sform]" />
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[show_for_guests]"><?php esc_html_e("Display for Guests", "wpdiscuz"); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData["show_for_guests"], 1, true); ?> name="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[show_for_guests]" id="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[show_for_guests]" />
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[show_for_users]"><?php esc_html_e("Display for Registered Users", "wpdiscuz"); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData["show_for_users"], 1, true); ?> name="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[show_for_users]" id="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[show_for_users]" />
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[donot_show_again_if_checked]"><?php esc_html_e("Don't show again if the agreement is accepted once", "wpdiscuz"); ?>:</label>
                <input type="checkbox" value="1" <?php checked($this->fieldData["donot_show_again_if_checked"], 1, true); ?> name="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[donot_show_again_if_checked]" id="<?php echo htmlentities($this->fieldInputName, ENT_QUOTES); ?>[donot_show_again_if_checked]" />
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function editCommentHtml($key, $value, $data, $comment) {
        if (current_user_can("moderate_comments") || ($comment->comment_parent && !$data["is_show_sform"]) || !$this->displayField($key, $data)) {
            return "";
        }
        $showAgainClass = $data["donot_show_again_if_checked"] == 1 ? " wpd_agreement_hide " : "";
        $uniqueId = uniqid();
        $html = "<tr class='" . esc_attr($key) . "-wrapper'><td class='first'>";
        $html .= "</td><td>";
        $required = $data["required"] ? " wpd-required-group" : "";
        $html .= "<div class='wpdiscuz-item" . esc_attr($required) . " wpd-field-group'>";
        $html .= "<input checked='checked'  id='" . esc_attr($key) . "-1_" . esc_attr($uniqueId) . "' type='checkbox' name='" . esc_attr($key) . "' value='1' class='" . esc_attr($key) . " wpd-field wpd-agreement-checkbox " . esc_attr($showAgainClass) . "' > <label class='wpd-field-label wpd-cursor-pointer' for='" . esc_attr($key) . "-1_" . esc_attr($uniqueId) . "'>" . $data["label"] . "</label>";
        $html .= "</div>";
        $html .= "</td></tr>";
        return $html;
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (empty($args["label"]) || (!$isMainForm && !$args["is_show_sform"]) || (!$args["show_for_users"] && $currentUser->exists()) || (!$args["show_for_guests"] && !$currentUser->exists()) || !$this->displayField($name, $args))
            return;
        $showAagainClass = $args["donot_show_again_if_checked"] == 1 ? " wpd_agreement_hide " : "";
        $hasDesc = $args["desc"] ? true : false;
        $required = $args["required"] ? " wpd-required-group" : "";
        ?>
        <div class="wpdiscuz-item wpd-field-group wpd-field-checkbox wpd-field-agreement wpd-field-single <?php echo esc_attr($name) . "-wrapper" . esc_attr($required) . ($hasDesc ? " wpd-has-desc" : ""); ?>">
            <div class="wpd-field-group-title">
                <div class="wpd-item">
                    <input id="<?php echo esc_attr($name) . "-1_" . esc_attr($uniqueId); ?>" type="checkbox" name="<?php echo esc_attr($name); ?>" value="1" class="<?php echo esc_attr($name); ?> wpd-field wpd-agreement-checkbox <?php echo esc_attr($showAagainClass); ?>"  <?php echo $args["required"] ? "required" : ""; ?>>
                    <label class="wpd-field-label wpd-cursor-pointer" for="<?php echo esc_attr($name) . "-1_" . esc_attr($uniqueId); ?>"><?php echo $args["label"]; ?></label>
                </div>
            </div>
            <?php if ($args["desc"]) { ?>
                <div class="wpd-field-desc">
                    <i class="far fa-question-circle"></i><span><?php echo esc_html($args["desc"]); ?></span>
                </div>
            <?php } ?>
        </div>
        <?php
    }

    public function frontHtml($value, $args) {
        
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        if (current_user_can("moderate_comments") || (!$this->isCommentParentZero() && !$args["is_show_sform"]) || !$this->displayField($fieldName, $args)) {
            return;
        }
        $value = filter_input(INPUT_POST, $fieldName, FILTER_VALIDATE_INT, FILTER_SANITIZE_NUMBER_INT);
        if (($args["show_for_users"] && $currentUser->exists() && $args["required"] && !$value ) || ($args["show_for_guests"] && !$currentUser->exists() && $args["required"] && !$value)) {
            wp_die(esc_html__($args["name"], "wpdiscuz") . " : " . esc_html__("field is required!", "wpdiscuz"));
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
            $cleanData["desc"] = trim($data["desc"]);
        }
        if (isset($data["label"])) {
            $cleanData["label"] = trim($data["label"]);
        }
        if (isset($data["required"])) {
            $cleanData["required"] = intval($data["required"]);
        } else {
            $cleanData["required"] = "0";
        }
        if (isset($data["is_show_sform"])) {
            $cleanData["is_show_sform"] = intval($data["is_show_sform"]);
        } else {
            $cleanData["is_show_sform"] = "0";
        }
        if (isset($data["donot_show_again_if_checked"])) {
            $cleanData["donot_show_again_if_checked"] = intval($data["donot_show_again_if_checked"]);
        } else {
            $cleanData["donot_show_again_if_checked"] = "0";
        }
        if (isset($data["show_for_guests"])) {
            $cleanData["show_for_guests"] = intval($data["show_for_guests"]);
        } else {
            $cleanData["show_for_guests"] = "0";
        }
        if (isset($data["show_for_users"])) {
            $cleanData["show_for_users"] = intval($data["show_for_users"]);
        }
        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    private function displayField($name, $args) {
        if ($args["donot_show_again_if_checked"] == 1 && key_exists($name . "_" . COOKIEHASH, $_COOKIE)) {
            return false;
        }
        return true;
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name" => "",
            "label" => "",
            "desc" => "",
            "required" => "1",
            "show_for_guests" => "1",
            "show_for_users" => "0",
            "is_show_on_comment" => "0",
            "is_show_sform" => "1",
            "donot_show_again_if_checked" => "1",
            "no_insert_meta" => "1",
        ];
    }

}
