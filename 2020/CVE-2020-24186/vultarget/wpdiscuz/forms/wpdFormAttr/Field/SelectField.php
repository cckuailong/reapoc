<?php

namespace wpdFormAttr\Field;

class SelectField extends Field {

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
                <label for="<?php echo $this->fieldInputName; ?>[values]"><?php esc_html_e("Values", "wpdiscuz"); ?>:</label> 
                <textarea required name="<?php echo $this->fieldInputName; ?>[values]" id="<?php echo $this->fieldInputName; ?>[values]"><?php echo esc_html($values); ?></textarea>
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
                        <input type="text" value="<?php echo $this->name; ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[meta_key]" id="<?php echo esc_attr($this->fieldInputName); ?>[meta_key]" required="required"/>
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
        $html = "<tr class='" . esc_attr($key) . "-wrapper wpd-edit-select'><td class='first'>";
        $html .= "<label for='" . esc_attr($key) . "'>" . esc_html($data["name"]) . ": </label>";
        $html .= "</td><td>";
        $required = $data["required"] ? " required='required' " : "";
        $html .= "<div class='wpdiscuz-item  wpd-field-group wpd-field-select'>";
        $html .= "<select name='" . esc_attr($key) . "' class='" . esc_attr($key) . " wpd-field wpd-field-select wpdiscuz_select'$required>";
        $html .= "<option value=''>...</option>";
        foreach ($data["values"] as $index => $val) {
            $selected = $value == $val ? " selected='selected' " : "";
            $index = $index + 1;
            $html .= "<option " . $selected . " value='" . esc_attr($index) . "'>" . esc_html($val) . "</option>";
        }
        $html .= "</select>";
        $html .= "</div>";
        $html .= "</td></tr>";
        return $html;
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (empty($args["values"]) || (!$isMainForm && !$args["is_show_sform"]))
            return;
        $hasDesc = $args["desc"] ? true : false;
        ?>
        <?php $required = $args["required"] ? " required='required' " : ""; ?>
        <div class="wpdiscuz-item wpd-field-group wpd-field-select <?php echo esc_attr($name) . "-wrapper" . ($hasDesc ? " wpd-has-desc" : ""); ?>">
            <select <?php echo $required; ?> name="<?php echo esc_attr($name); ?>" class="<?php echo esc_attr($name); ?> wpd-field wpdiscuz_select">
                <option value=""><?php echo htmlentities($args["name"]); ?></option>
                <?php foreach ($args["values"] as $index => $val) { ?>
                    <option value="<?php echo esc_attr($index + 1); ?>"><?php echo htmlentities($val); ?></option>
                <?php } ?>
            </select>
            <?php if ($args["desc"]) { ?>
                <div class="wpd-field-desc"><i class="far fa-question-circle"></i><span><?php echo esc_html($args["desc"]); ?></span></div>
            <?php } ?>
        </div>
        <?php
    }

    public function frontHtml($value, $args) {
        $html = "<div class='wpd-custom-field wpd-cf-text'>";
        $html .= "<div class='wpd-cf-label'>" . esc_html($args["name"]) . "</div> <div class='wpd-cf-value'> " . esc_html(apply_filters("wpdiscuz_custom_field_select", $value, $args)) . "</div>";
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
