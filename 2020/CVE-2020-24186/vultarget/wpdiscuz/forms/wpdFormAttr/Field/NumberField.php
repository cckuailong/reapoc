<?php

namespace wpdFormAttr\Field;

class NumberField extends Field {

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
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[min]"><?php esc_html_e("Min Value", "wpdiscuz"); ?>:</label> 
                <input type="number" value="<?php echo esc_attr($this->fieldData["min"]); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[min]" id="<?php echo esc_attr($this->fieldInputName); ?>[min]" />
                <p class="wpd-info"><?php esc_html_e("Field specific short description or some rule related to inserted information.", "wpdiscuz"); ?></p>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[max]"><?php esc_html_e("Max Value", "wpdiscuz"); ?>:</label> 
                <input type="number" value="<?php echo esc_attr($this->fieldData["max"]); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[max]" id="<?php echo esc_attr($this->fieldInputName); ?>[max]" />
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
        $html = "<tr class='" . esc_attr($key) . "-wrapper wpd-edit-number'><td class='first'>";
        $html .= "<label for='" . esc_attr($key) . "'>" . esc_html($data["name"]) . ": </label>";
        $html .= "</td><td>";
        $html .= "<div class='wpdiscuz-item'>";
        $required = $data["required"] ? "required='required'" : "";
        $min = is_numeric($data["min"]) ? "min='" . $data["min"] . "'" : "";
        $max = is_numeric($data["max"]) ? "max='" . $data["max"] . "'" : "";
        $html .= "<input " . $required . " class='wpd-field wpd-field-number' type='number' id='" . esc_attr($key) . "' value='" . esc_attr($value) . "'  name='" . esc_attr($key) . "'  " . $min . " " . $max . ">";
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
        <div class="wpdiscuz-item <?php echo esc_attr($name) . "-wrapper" . ($hasIcon ? " wpd-has-icon" : "") . ($hasDesc ? " wpd-has-desc" : ""); ?>">
            <?php if ($hasIcon) { ?>
                <div class="wpd-field-icon"><i style="opacity: 0.8;" class="<?php echo strpos(trim($args["icon"]), " ") ? esc_attr($args["icon"]) : "fas " . esc_attr($args["icon"]); ?>"></i></div>
            <?php } ?>
            <?php
            $required = $args["required"] ? "required='required'" : "";
            $min = is_numeric($args["min"]) ? "min='" . $args["min"] . "'" : "";
            $max = is_numeric($args["max"]) ? "max='" . $args["max"] . "'" : "";
            ?>
            <input id="<?php echo esc_attr($name) . "-" . $uniqueId; ?>" <?php echo $required; ?> class="<?php echo esc_attr($name); ?> wpd-field wpd-field-number" type="number" name="<?php echo esc_attr($name); ?>" value="" placeholder="<?php echo esc_html__($args["name"], "wpdiscuz") . (!empty($args["required"]) ? "*" : ""); ?>" <?php echo $min . " " . $max; ?>>
            <label for="<?php echo esc_attr($name) . "-" . $uniqueId; ?>" class="wpdlb"><?php echo esc_html__($args["name"], "wpdiscuz") . (!empty($args["required"]) ? "*" : ""); ?></label>
            <?php if ($args["desc"]) { ?>
                <div class="wpd-field-desc"><i class="far fa-question-circle"></i><span><?php echo $args["desc"]; ?></span></div>
                    <?php } ?>
        </div>
        <?php
    }

    public function frontHtml($value, $args) {
        $html = "<div class='wpd-custom-field wpd-cf-text'>";
        $html .= "<div class='wpd-cf-label'>" . esc_html($args["name"]) . "</div> <div class='wpd-cf-value'> " . esc_html(apply_filters("wpdiscuz_custom_field_number", $value, $args)) . "</div>";
        $html .= "</div>";
        return $html;
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        if (!$this->isCommentParentZero() && !$args["is_show_sform"]) {
            return "";
        }
        $value = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_NUMBER_INT);
        if (!$value && $args["required"]) {
            wp_die(esc_html__($args["name"], "wpdiscuz") . " : " . esc_html__("field is required!", "wpdiscuz"));
        }
        $value = intval($value);
        if (is_int($args["min"]) && $value < $args["min"]) {
            wp_die(esc_html__($args["name"], "wpdiscuz") . " : " . esc_html__("value can not be less than", "wpdiscuz") . " " . esc_html($args["min"]));
        }
        if (is_int($args["max"]) && $value > $args["max"]) {
            wp_die(esc_html__($args["name"], "wpdiscuz") . " : " . esc_html__("value can not be more than", "wpdiscuz") . " " . esc_html($args["max"]));
        }

        return $value;
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
        if (isset($data["values"])) {
            $values = array_filter(explode("\n", trim(strip_tags($data["values"]))));
            foreach ($values as $k => $value) {
                $cleanData["values"][] = trim($value);
            }
        }
        if (isset($data["icon"])) {
            $cleanData["icon"] = trim(strip_tags($data["icon"]));
        }
        if (isset($data["required"])) {
            $cleanData["required"] = intval($data["required"]);
        }
        if (isset($data["min"]) && trim($data["min"]) != "") {
            $cleanData["min"] = intval($data["min"]);
        } else {
            $cleanData["min"] = "";
        }
        if (isset($data["max"]) && trim($data["max"]) != "") {
            $cleanData["max"] = intval($data["max"]);
        } else {
            $cleanData["max"] = "";
        }
        if (isset($data["is_show_on_comment"])) {
            $cleanData["is_show_on_comment"] = intval($data["is_show_on_comment"]);
        } else {
            $cleanData["is_show_on_comment"] = 0;
        }
        if (isset($data["is_show_sform"])) {
            $cleanData["is_show_sform"] = intval($data["is_show_sform"]);
        } else {
            $cleanData["is_show_sform"] = 0;
        }

        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name" => "",
            "desc" => "",
            "values" => [],
            "icon" => "",
            "required" => "0",
            "loc" => "bottom",
            "min" => "",
            "max" => "",
            "is_show_sform" => 0,
            "is_show_on_comment" => 1
        ];
    }

}
