<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php esc_html_e("Notification Phrases", "wpdiscuz"); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row"><label for="wc_subscribed_to"><?php esc_html_e("You're subscribed to", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_subscribed_to"]); ?>" name="wc_subscribed_to" id="wc_subscribed_to" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_subscribe_message"><?php esc_html_e("You've successfully subscribed.", "wpdiscuz"); ?></label></th>
                <td colspan="3"><textarea name="wc_subscribe_message" id="wc_subscribe_message"><?php echo esc_html($this->phrases["wc_subscribe_message"]); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_unsubscribe_message"><?php esc_html_e("You've successfully unsubscribed.", "wpdiscuz"); ?></label></th>
                <td colspan="3"><textarea name="wc_unsubscribe_message" id="wc_unsubscribe_message"><?php echo esc_html($this->phrases["wc_unsubscribe_message"]); ?></textarea></td>
            </tr>
            <?php if (class_exists("Prompt_Comment_Form_Handling") && $this->subscription["usePostmaticForCommentNotification"]) { ?>
                <tr valign="top">
                    <th scope="row"><label for="wc_postmatic_subscription_label"><?php esc_html_e("Postmatic subscription label", "wpdiscuz"); ?></label></th>
                    <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_postmatic_subscription_label"]); ?>" name="wc_postmatic_subscription_label" id="wc_postmatic_subscription_label" /></td>
                </tr>
            <?php } ?>
            <tr valign="top">
                <th scope="row"><label for="wc_log_in"><?php esc_html_e("Login", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_log_in"]); ?>" name="wc_log_in" id="wc_log_in" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_login_please"><?php esc_html_e("Please %s to comment", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_login_please"]); ?>" name="wc_login_please" id="wc_login_please" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><label for="wc_you_must_be_text"><?php esc_html_e("You must be", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_you_must_be_text"]); ?>" name="wc_you_must_be_text" id="wc_you_must_be_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_logged_in_text"><?php esc_html_e("Logged In", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_logged_in_text"]); ?>" name="wc_logged_in_text" id="wc_logged_in_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_to_post_comment_text"><?php esc_html_e("To post a comment", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_to_post_comment_text"]); ?>" name="wc_to_post_comment_text" id="wc_to_post_comment_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_logged_in_as"><?php esc_html_e("Logged in as", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_logged_in_as"]); ?>" name="wc_logged_in_as" id="wc_logged_in_as" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_log_out"><?php esc_html_e("Log out", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_log_out"]); ?>" name="wc_log_out" id="wc_log_out" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_vote_counted"><?php esc_html_e("Vote Counted", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_vote_counted"]); ?>" name="wc_vote_counted" id="wc_vote_counted" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_login_to_vote"><?php esc_html_e("Login To Vote", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_login_to_vote"]); ?>" name="wc_login_to_vote" id="wc_login_to_vote" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_awaiting_for_approval"><?php esc_html_e("Awaiting for approval", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_awaiting_for_approval"]); ?>" name="wc_awaiting_for_approval" id="wc_awaiting_for_approval" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_roles_cannot_comment_message"><?php esc_html_e("Message if commenting disabled by user role", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_roles_cannot_comment_message"]); ?>" name="wc_roles_cannot_comment_message" id="wc_roles_cannot_comment_message" /></td>
            </tr>
        </tbody>
    </table>
</div>