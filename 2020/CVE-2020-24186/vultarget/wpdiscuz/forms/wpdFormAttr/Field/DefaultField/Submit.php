<?php

namespace wpdFormAttr\Field\DefaultField;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\Field;

class Submit extends Field {

    protected $name = wpdFormConst::WPDISCUZ_FORMS_SUBMIT_FIELD;
    protected $isDefault = true;

    protected function dashboardForm() {
        ?>
        <div class="wpd-field-body" style="display: <?php echo esc_attr($this->display); ?>">
            <div class="wpd-field-option wpdiscuz-item">
                <input class="wpd-field-type" type="hidden" value="<?php echo esc_attr($this->type); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[type]" />
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[name]"><?php esc_html_e("Name", "wpdiscuz"); ?>:</label> 
                <input class="wpd-field-name" type="text" value="<?php echo esc_attr($this->fieldData["name"]); ?>" name="<?php echo esc_attr($this->fieldInputName); ?>[name]" id="<?php echo esc_attr($this->fieldInputName); ?>[name]" required />
                <p class="wpd-info"><?php esc_html_e("Button Text", "wpdiscuz"); ?></p>
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        global $post;
        do_action("wpdiscuz_submit_button_before", $currentUser, $uniqueId, $isMainForm);
        $wpdiscuz = wpDiscuz();
        ?>
        <div class="wc-field-submit">
            <?php
            do_action("wpdiscuz_before_submit_button_in_wrapper", $currentUser, $uniqueId, $isMainForm);
            if ($isMainForm && (current_user_can("moderate_comments") || ($post && isset($post->post_author) && $post->post_author == $currentUser->ID))) {
                ?>
                <label class="wpd_label" wpd-tooltip="<?php echo esc_attr($options->phrases["wc_stick_comment_btn_title"]); ?>">
                    <input id="wc_sticky_comment" class="wpd_label__checkbox" value="1" type="checkbox" name="wc_sticky_comment"/>
                    <span class="wpd_label__text">
                        <span class="wpd_label__check">
                            <i class="fas fa-thumbtack wpdicon wpdicon-on"></i>
                            <i class="fas fa-thumbtack wpdicon wpdicon-off"></i>
                        </span>
                    </span>
                </label>
                <label class="wpd_label" wpd-tooltip="<?php echo esc_attr($options->phrases["wc_close_comment_btn_title"]); ?>">
                    <input id="wc_closed_comment" class="wpd_label__checkbox" value="1" type="checkbox" name="wc_closed_comment"/>
                    <span class="wpd_label__text">
                        <span class="wpd_label__check">
                            <i class="fas fa-lock wpdicon wpdicon-on"></i>
                            <i class="fas fa-unlock-alt wpdicon wpdicon-off"></i>
                        </span>
                    </span>
                </label>
                <?php
            }
            ?>
            <?php if ($options->wp["threadComments"] || class_exists("Prompt_Comment_Form_Handling")) { ?>
                <?php
                $isShowSubscribeWrapper = false;
                if ($options->subscription["showReplyCheckbox"]) {
                    if ($currentUser->ID) {
                        $isShowSubscribeWrapper = !$wpdiscuz->subscriptionData || ($wpdiscuz->subscriptionData && $wpdiscuz->subscriptionData["type"] == $wpdiscuz::SUBSCRIPTION_COMMENT) ? true : false;
                    } else {
                        $isShowSubscribeWrapper = true;
                    }
                }
                if ($isShowSubscribeWrapper) {
                    $isReplyDefaultChecked = $options->subscription["isReplyDefaultChecked"] ? "checked='checked'" : "";
                    ?>    
                    <?php
                    if (class_exists("Prompt_Comment_Form_Handling") && $options->subscription["usePostmaticForCommentNotification"]) {
                        ?>
                        <label class="wpd_label" wpd-tooltip="<?php echo esc_attr($options->phrases["wc_postmatic_subscription_label"]); ?>">
                            <input id="wc_notification_new_comment-<?php echo esc_attr($uniqueId); ?>" class="wc_notification_new_comment-<?php echo esc_attr($uniqueId); ?> wpd_label__checkbox" value="post" type="checkbox" name="wpdiscuz_notification_type" <?php echo $isReplyDefaultChecked; ?>/>
                            <span class="wpd_label__text">
                                <span class="wpd_label__check">
                                    <i class="fas fa-bell wpdicon wpdicon-on"></i>
                                    <i class="fas fa-bell-slash wpdicon wpdicon-off"></i>
                                </span>
                            </span>
                        </label>
                        <?php
                    } else {
                        ?>
                        <label class="wpd_label" wpd-tooltip="<?php echo esc_attr($options->phrases["wc_notify_on_new_reply"]); ?>">
                            <input id="wc_notification_new_comment-<?php echo esc_attr($uniqueId); ?>" class="wc_notification_new_comment-<?php echo esc_attr($uniqueId); ?> wpd_label__checkbox" value="comment" type="checkbox" name="wpdiscuz_notification_type" <?php echo $isReplyDefaultChecked; ?>/>
                            <span class="wpd_label__text">
                                <span class="wpd_label__check">
                                    <i class="fas fa-bell wpdicon wpdicon-on"></i>
                                    <i class="fas fa-bell-slash wpdicon wpdicon-off"></i>
                                </span>
                            </span>
                        </label>
                        <?php
                    }
                    ?>
                <?php } ?>
            <?php } ?>
            <input id="wpd-field-submit-<?php echo esc_attr($uniqueId); ?>" class="wc_comm_submit wpd_not_clicked wpd-prim-button" type="submit" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($args["name"]); ?>" />
        </div>
        <?php
    }

    public function sanitizeFieldData($data) {
        $cleanData = [];
        $cleanData["type"] = $data["type"];
        if (isset($data["name"])) {
            $name = trim(strip_tags($data["name"]));
            $cleanData["name"] = $name ? $name : $this->fieldDefaultData["name"];
        }
        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name" => esc_html__("Post Comment", "wpdiscuz"),
        ];
    }

    public function frontHtml($value, $args) {
        
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        
    }

    public function editCommentHtml($key, $value, $data, $comment) {
        
    }

}
