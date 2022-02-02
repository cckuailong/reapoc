<?php

namespace wpdFormAttr\Field;

class RatingField extends Field {

    protected function dashboardForm() {
        ?>
        <div class="wpd-field-body" style="display: <?php echo esc_attr($this->display); ?>">
            <div class="wpd-field-option wpdiscuz-item">
                <input class="wpd-field-type" type="hidden" value="<?php echo esc_attr($this->type); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[type]" />
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[name]"><?php esc_html_e("Name", "wpdiscuz"); ?>:</label> 
                <input class="wpd-field-name" type="text" value="<?php echo esc_attr($this->fieldData["name"]); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[name]" id="<?php echo esc_attr($this->fieldInputName); ?>[name]" required />
            </div>
            <div class="wpd-field-option wpdiscuz-item">
                <input class="wpd-field-type" type="hidden" value="<?php echo esc_attr($this->type); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[type]" />
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[nameForTotal]"><?php esc_html_e("Name For Total", "wpdiscuz"); ?>:</label> 
                <input class="wpd-field-name" type="text" value="<?php echo esc_attr($this->fieldData["nameForTotal"]); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[nameForTotal]" id="<?php echo esc_attr($this->fieldInputName); ?>[nameForTotal]" />
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[desc]"><?php esc_html_e("Description", "wpdiscuz"); ?>:</label> 
                <input type="text" value="<?php echo esc_attr($this->fieldData["desc"]); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[desc]" id="<?php echo esc_attr($this->fieldInputName); ?>[desc]" />
                <p class="wpd-info"><?php esc_html_e("Field specific short description or some rule related to inserted information.", "wpdiscuz"); ?></p>
            </div>
            <div class="wpd-field-option">
                <div class="input-group">
                    <label for="<?php echo esc_attr($this->fieldInputName); ?>[icon]"><span class="input-group-addon"></span> <?php esc_html_e("Field icon", "wpdiscuz"); ?>:</label>
                    <input data-placement="bottom" class="icp icp-auto" value="<?php echo esc_attr(isset($this->fieldData["icon"]) ? $this->fieldData["icon"] : "fas fa-star"); ?>" type="text" name="<?php echo esc_attr($this->fieldInputName); ?>[icon]" id="<?php echo esc_attr($this->fieldInputName); ?>[icon]" />
                </div>
                <p class="wpd-info"><?php esc_html_e("Font-awesome icon library.", "wpdiscuz"); ?></p>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[required]"><?php esc_html_e("Field is required", "wpdiscuz"); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData["required"], 1, true); ?> name="<?php echo esc_attr($this->fieldInputName); ?>[required]" id="<?php echo esc_attr($this->fieldInputName); ?>[required]" />
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[is_show_on_comment]"><?php esc_html_e("Display on comment", "wpdiscuz"); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData["is_show_on_comment"], 1, true); ?> name="<?php echo esc_attr($this->fieldInputName); ?>[is_show_on_comment]" id="<?php echo esc_attr($this->fieldInputName); ?>[is_show_on_comment]" />
            </div>
            <div class="wpd-advaced-options wpd-field-option">
                <small class="wpd-advaced-options-title"><?php esc_html_e("Advanced Options", "wpdiscuz"); ?></small>
                <div class="wpd-field-option wpd-advaced-options-cont">
                    <div class="wpd-field-option">
                        <label for="<?php echo esc_attr($this->fieldInputName); ?>[meta_key]"><?php esc_html_e("Meta Key", "wpdiscuz"); ?>:</label> 
                        <input type="text" value="<?php echo esc_attr($this->name); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[meta_key]" id="<?php echo esc_attr($this->fieldInputName); ?>[meta_key]" required="required"/>
                    </div>
                    <div class="wpd-field-option">
                        <label for="<?php echo esc_attr($this->fieldInputName); ?>[meta_key_replace]"><?php esc_html_e("Replace old meta key", "wpdiscuz"); ?>:</label> 
                        <input type="checkbox" value="1" checked="checked" name="<?php echo esc_attr($this->fieldInputName); ?>[meta_key_replace]" id="<?php echo esc_attr($this->fieldInputName); ?>[meta_key_replace]" />
                    </div>
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function editCommentHtml($key, $value, $data, $comment) {
        if ($comment->comment_parent) {
            return "";
        }
        $html = "<tr class='" . esc_attr($key) . "-wrapper wpd-edit-rating'><td class='first'>";
        $html .= "<label for='" . esc_attr($key) . "'>" . esc_html($data["name"]) . ": </label>";
        $html .= "</td><td>";
        $uniqueId = uniqid();
        $required = $data["required"] ? " wpd-required-group " : "";
        $html .= "<div class='wpdiscuz-item wpd-field-group wpd-field-rating " . esc_attr($required) . "'>";
        $html .= "<fieldset class='wpdiscuz-rating'>";
        for ($i = 5; $i >= 1; $i--) {
            $checked = ($i == $value) ? " checked='checked'" : "";
            $html .= "<input type='radio' id='wpdiscuz-star_" . esc_attr($uniqueId) . "_" . esc_attr($i) . "' name='" . esc_attr($key) . "' value='" . esc_attr($i) . "'$checked/>";
            $html .= "<label class=' a full fa " . esc_attr($data["icon"]) . "' for='wpdiscuz-star_" . esc_attr($uniqueId) . "_" . esc_attr($i) . "' title='" . esc_attr($i) . "'></label>";
        }
        $html .= "</fieldset>";
        $html .= "</div>";
        $html .= "</td></tr>";
        return $html;
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (!$isMainForm)
            return;
        $hasDesc = $args["desc"] ? true : false;
        $required = $args["required"] ? " wpd-required-group" : "";
        $uniqueId = uniqid($uniqueId);
        ?>
        <div class="wpdiscuz-item wpd-field-group wpd-field-rating <?php echo esc_attr($name) . "-wrapper" . esc_attr($required) . ($hasDesc ? " wpd-has-desc" : ""); ?>">
            <div class="wpd-field-group-title">
                <?php esc_html_e($args["name"], "wpdiscuz"); ?>
                <?php if ($args["desc"]) { ?>
                    <div class="wpd-field-desc"><i class="far fa-question-circle"></i><span><?php echo esc_html($args["desc"]); ?></span></div>
                <?php } ?>
            </div>
            <div class="wpd-item-wrap">
                <fieldset class="wpdiscuz-rating">
                    <?php
                    for ($i = 5; $i >= 1; $i--) {
                        ?>
                        <input type="radio" id="wpdiscuz-star_<?php echo esc_attr($uniqueId) . "_" . esc_attr($i); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($i); ?>" />
                        <label class="<?php echo strpos(trim($args["icon"]), " ") ? esc_attr($args["icon"]) : "fas " . esc_attr($args["icon"]); ?> full" for="wpdiscuz-star_<?php echo esc_attr($uniqueId) . "_" . esc_attr($i); ?>" title="<?php echo esc_attr($i); ?>"></label>
                        <?php
                    }
                    ?>
                </fieldset>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
    }

    public function frontHtml($value, $args) {
        $html = "<div class='wpd-custom-field wpd-cf-rating'>";
        $html .= "<div class='wpd-cf-label'>" . esc_html($args["name"]) . " : </div><div class='wpd-cf-value'>";
        for ($i = 0; $i < 5; $i++) {
            $colorClass = ($i < $value) ? " wcf-active-star " : " wcf-pasiv-star ";
            $fa = strpos(trim($args["icon"]), " ") ? $args["icon"] : "fas " . $args["icon"];
            $html .= "<i class='" . esc_attr($fa) . " " . esc_attr($colorClass) . "' aria-hidden='true'></i>&nbsp;";
        }
        $html .= "</div></div>";
        return $html;
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        $value = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_NUMBER_INT);
        if (!$this->isCommentParentZero()) {
            return 0;
        }
        if (!$value && $args["required"]) {
            wp_die(esc_html__($args["name"], "wpdiscuz") . " : " . esc_html__("field is required!", "wpdiscuz"));
        }
        return $value;
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name" => "",
            "nameForTotal" => "",
            "desc" => "",
            "required" => "0",
            "loc" => "top",
            "icon" => "fas fa-star",
            "is_show_on_comment" => 1
        ];
    }

}
