<?php

namespace wpdFormAttr\Field;

class CookiesConsent extends Field {

    private $cookiesConsent;

    protected function dashboardForm() {
        ?>
        <div class="wpd-field-body" style="display: <?php echo esc_attr($this->display); ?>">
            <div class="wpd-field-option wpdiscuz-item">
                <input class="wpd-field-type" type="hidden" value="<?php echo esc_attr($this->type); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[type]" />
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[name]"><?php esc_html_e("Name", "wpdiscuz"); ?>:</label> 
                <input class="wpd-field-name" type="text" value="<?php echo esc_attr($this->fieldData["name"]); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[name]" id="<?php echo esc_attr($this->fieldInputName); ?>[name]" required />
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[desc]"><?php esc_html_e("Description", "wpdiscuz"); ?>:</label> 
                <input type="text" value="<?php echo esc_attr($this->fieldData["desc"]); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[desc]" id="<?php echo esc_attr($this->fieldInputName); ?>[desc]" />
                <p class="wpd-info"><?php esc_html_e("Field specific short description or some rule related to inserted information.", "wpdiscuz"); ?></p>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[label]"><?php esc_html_e("Checkbox Label", "wpdiscuz"); ?>:</label>
                <textarea required="required" type="text" name="<?php echo esc_attr($this->fieldInputName); ?>[label]" id="<?php echo esc_attr($this->fieldInputName); ?>[label]" style="height: 75px;width:100%"><?php echo $this->fieldData["label"]; ?></textarea>
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function editCommentHtml($key, $value, $data, $comment) {
        
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if ($currentUser->exists()) {
            return;
        }
        $hasDesc = $args["desc"] ? true : false;
        $commenter = wp_get_current_commenter();
        $consent = empty($commenter["comment_author_email"]) ? "" : " checked='checked'";
        ?>
        <div class="wpdiscuz-item wpd-field-group wpd-field-checkbox wpd-field-cookies-consent wpd-field-single <?php echo "$name-wrapper" . ($hasDesc ? " wpd-has-desc" : ""); ?>">
            <div class="wpd-field-group-title">
                <div class="wpd-item">
                    <input id="<?php echo esc_attr($name) . "-1_" . esc_attr($uniqueId); ?>" name="<?php echo esc_attr($name); ?>" type="checkbox" value="1" <?php echo $consent; ?> class="<?php echo esc_attr($name); ?> wpd-field wpd-cookies-checkbox" />
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
        $this->cookiesConsent = filter_input(INPUT_POST, $fieldName, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if ($this->cookiesConsent === false) {
            $past = time() - YEAR_IN_SECONDS;
            setcookie("comment_author_" . COOKIEHASH, " ", $past, "/", COOKIE_DOMAIN);
            setcookie("comment_author_email_" . COOKIEHASH, " ", $past, "/", COOKIE_DOMAIN);
            setcookie("comment_author_url_" . COOKIEHASH, " ", $past, "/", COOKIE_DOMAIN);
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
        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name" => "Cookies Consent",
            "label" => esc_html__("Save my data for the next time I comment"),
            "desc" => "Save my name, email, and website in this browser cookies for the next time I comment.",
            "required" => "0",
            "show_for_guests" => "1",
            "show_for_users" => "0",
            "is_show_on_comment" => "0",
            "is_show_sform" => "1",
            "no_insert_meta" => "1"
        ];
        //  add_action('wpdiscuz_before_save_commentmeta', [$this, 'beforeSaveCommentmeta']);
    }

    public function beforeSaveCommentmeta($comment) {
        if ($this->cookiesConsent === false) {
            $past = time() - YEAR_IN_SECONDS;
            setcookie("comment_author_" . COOKIEHASH, " ", $past, "/", COOKIE_DOMAIN);
            setcookie("comment_author_email_" . COOKIEHASH, " ", $past, "/", COOKIE_DOMAIN);
            setcookie("comment_author_url_" . COOKIEHASH, " ", $past, "/", COOKIE_DOMAIN);
        }
    }

}
