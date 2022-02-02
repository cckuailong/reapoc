<?php

namespace wpdFormAttr\Field;

class RadioField extends Field {

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
            <div class="wpd-field-option wpdiscuz-item">
                <?php
                $values = "";
                foreach ($this->fieldData["values"] as $k => $value) {
                    $values .= $value . "\n";
                }
                ?>
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[values]"><?php esc_html_e("Values", "wpdiscuz"); ?>:</label> 
                <textarea required name="<?php echo esc_attr($this->fieldInputName); ?>[values]" id="<?php echo esc_attr($this->fieldInputName); ?>[values]"><?php echo esc_html($values); ?></textarea>
                <p class="wpd-info"><?php esc_html_e("New value new line", "wpdiscuz"); ?></p>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[required]"><?php esc_html_e("Field is required", "wpdiscuz"); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData["required"], 1, true); ?> name="<?php echo esc_attr($this->fieldInputName); ?>[required]" id="<?php echo esc_attr($this->fieldInputName); ?>[required]" />
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[is_show_sform]"><?php esc_html_e("Display on reply form", "wpdiscuz"); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData["is_show_sform"], 1, true); ?> name="<?php echo esc_attr($this->fieldInputName); ?>[is_show_sform]" id="<?php echo esc_attr($this->fieldInputName); ?>[is_show_sform]" />
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
        if ($comment->comment_parent && !$data["is_show_sform"]) {
            return "";
        }
        $html = "<tr class='" . esc_attr($key) . "-wrapper wpd-edit-radio'><td class='first'>";
        $html .= "<label for='" . esc_attr($key) . "'>" . esc_html($data["name"]) . ": </label>";
        $html .= "</td><td>";
        $required = $data["required"] ? " wpd-required-group" : "";
        $html .= "<div class='wpdiscuz-item" . esc_attr($required) . " wpd-field-group'>";
        foreach ($data["values"] as $index => $val) {
            $uniqueId = uniqid();
            $checked = $value == $val ? " checked='checked' " : "";
            $index = $index + 1;
            $html .= "<div class='wpd-item'><input $checked id='" . esc_attr($key) . "-" . esc_attr($index) . "_" . esc_attr($uniqueId) . "' type='radio' name='" . esc_attr($key) . "' value='" . esc_attr($index) . "' class='" . esc_attr($key) . " wpd-field wpd-field-radio'> <label class='wpd-field-label wpd-cursor-pointer' for='" . esc_attr($key) . "-" . esc_attr($index) . "_" . esc_attr($uniqueId) . "'>" . esc_html($val) . "</label></div>";
        }
        $html .= "</div>";
        $html .= "</td></tr>";
        return $html;
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (empty($args["values"]) || (!$isMainForm && !$args["is_show_sform"]))
            return;
        $hasDesc = $args["desc"] ? true : false;
        ?>
        <?php $required = $args["required"] ? " wpd-required-group" : ""; ?>
        <div class="wpdiscuz-item wpd-field-group wpd-field-radio <?php echo esc_attr($name) . "-wrapper" . esc_attr($required) . ($hasDesc ? " wpd-has-desc" : ""); ?>">
            <div class="wpd-field-group-title">
                <?php esc_html_e($args["name"], "wpdiscuz"); ?>
                <?php if ($args["desc"]) { ?>
                    <div class="wpd-field-desc"><i class="far fa-question-circle"></i><span><?php echo esc_html($args["desc"]); ?></span></div>
                <?php } ?>
            </div>
            <div class="wpd-item-wrap">
                <?php
                foreach ($args["values"] as $index => $val) {
                    ?>
                    <div class="wpd-item">
                        <input id="<?php echo esc_attr($name) . "-" . esc_attr($index + 1) . "_" . esc_attr($uniqueId); ?>" type="radio" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($index + 1); ?>" class="<?php echo esc_attr($name); ?> wpd-field" >
                        <label class="wpd-field-label wpd-cursor-pointer" for="<?php echo esc_attr($name) . "-" . esc_attr($index + 1) . "_" . esc_attr($uniqueId); ?>"><?php echo esc_html($val); ?></label>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function frontHtml($value, $args) {
        $html = "<div class='wpd-custom-field wpd-cf-text'>";
        $html .= "<div class='wpd-cf-label'>" . esc_html($args["name"]) . "</div> <div class='wpd-cf-value'> " . esc_html(apply_filters("wpdiscuz_custom_field_radio", $value, $args)) . "</div>";
        $html .= "</div>";
        return $html;
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        if (!$this->isCommentParentZero() && !$args["is_show_sform"]) {
            return "";
        }
        $value = filter_input(INPUT_POST, $fieldName, FILTER_VALIDATE_INT);
        if (is_int($value) && $value > 0 && key_exists($value - 1, $args["values"])) {
            $value = $args["values"][$value - 1];
        } else {
            $value = "";
        }
        if (!$value && $args["required"]) {
            wp_die(esc_html__($args["name"], "wpdiscuz") . " : " . esc_html__("field is required!", "wpdiscuz"));
        }
        return $value;
    }

}
