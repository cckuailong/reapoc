<?php

namespace wpdFormAttr\Field;

class DateField extends Field {

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
        $html = "<tr class='" . esc_attr($key) . "-wrapper wpd-edit-date'><td class='first'>";
        $html .= "<label for='" . esc_attr($key) . "'>" . esc_html($data["name"]) . ": </label>";
        $html .= "</td><td>";
        $html .= "<div class='wpdiscuz-item'>";
        $required = $data["required"] ? "required='required'" : "";
        $html .= "<input " . $required . " class='wpd-field wpd-field-date' type='date' id='" . esc_attr($key) . "' value='" . esc_attr($value) . "'  name='" . esc_attr($key) . "' pattern='^(1[0-2]|0[1-9])/(3[01]|[12][0-9]|0[1-9])/[0-9]{4}$' title='03/28/2016'>";
        $html .= "</div>";
        $html .= "</td></tr>";
        return $html;
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (!$isMainForm && !$args["is_show_sform"]) {
            return;
        }
        $hasIcon = $args["icon"] ? true : false;
        $hasDesc = $args["desc"] ? true : false;
        ?>
        <div class="wpdiscuz-item wpd-field-date <?php echo esc_attr($name) . "-wrapper" . ($hasIcon ? " wpd-has-icon" : "") . ($hasDesc ? " wpd-has-desc" : ""); ?>">
            <div class="wpd-field-title">
                <?php echo esc_html($args["name"]); ?>
            </div>
            <?php if ($hasIcon) { ?>
                <div class="wpd-field-icon"><i style="opacity: 0.8;" class="<?php echo strpos(trim($args["icon"]), " ") ? esc_attr($args["icon"]) : "fas " . esc_attr($args["icon"]); ?>"></i></div>
            <?php } ?>
            <?php $required = $args["required"] ? "required='required'" : ""; ?>
            <input id="<?php echo esc_attr($name) . "-" . $uniqueId; ?>" <?php echo $required; ?> class="<?php echo esc_attr($name); ?> wpd-field wpd-field-date" type="date" name="<?php echo esc_attr($name); ?>" value="" placeholder="03/28/2016<?php echo!empty($args["required"]) ? "*" : "" ?>"  pattern="^(1[0-2]|0[1-9])/(3[01]|[12][0-9]|0[1-9])/[0-9]{4}$" title="03/28/2016">
            <label for="<?php echo esc_attr($name) . "-" . $uniqueId; ?>" class="wpdlb"><?php echo esc_attr($args["name"]) . (!empty($args["required"]) ? "*" : ""); ?></label>
            <?php if ($args["desc"]) { ?>
                <div class="wpd-field-desc"><i class="far fa-question-circle"></i><span><?php echo esc_html($args["desc"]); ?></span></div>
                    <?php } ?>
        </div>
        <?php
    }

    public function frontHtml($value, $args) {
        $html = "<div class='wpd-custom-field wpd-cf-text'>";
        $html .= "<div class='wpd-cf-label'>" . esc_html($args["name"]) . "</div> <div class='wpd-cf-value'> " . esc_html(apply_filters("wpdiscuz_custom_field_date", $value, $args)) . "</div>";
        $html .= "</div>";
        return $html;
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        if (!$this->isCommentParentZero() && !$args["is_show_sform"]) {
            return "";
        }
        $value = trim(filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_STRING));
        if ($value && !preg_match("@^[0-9]{4}-[0-9]{2}-[0-9]{2}$@is", $value)) {
            $value = "";
        }
        if (!$value && $args["required"]) {
            wp_die(esc_html__($args["name"], "wpdiscuz") . " : " . esc_html__("field is required!", "wpdiscuz"));
        }
        return $value;
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name" => "",
            "desc" => "",
            "icon" => "fa-calendar-alt",
            "required" => "0",
            "loc" => "bottom",
            "is_show_on_comment" => 1,
            "is_show_sform" => 1,
        ];
    }

}
