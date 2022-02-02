<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php esc_html_e("User Settings Phrases", "wpdiscuz"); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row"><label for="wc_content_and_settings"><?php esc_html_e("My content and settings", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_content_and_settings"]); ?>" name="wc_content_and_settings" id="wc_content_and_settings" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_activity"><?php esc_html_e("Activity", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_activity"]); ?>" name="wc_user_settings_activity" id="wc_user_settings_activity" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_subscriptions"><?php esc_html_e("Subscriptions", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_subscriptions"]); ?>" name="wc_user_settings_subscriptions" id="wc_user_settings_subscriptions" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_follows"><?php esc_html_e("Follows", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_follows"]); ?>" name="wc_user_settings_follows" id="wc_user_settings_follows" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_response_to"><?php esc_html_e("In response to:", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_response_to"]); ?>" name="wc_user_settings_response_to" id="wc_user_settings_response_to" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_email_me_delete_links"><?php esc_html_e("Bulk management via email", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_email_me_delete_links"]); ?>" name="wc_user_settings_email_me_delete_links" id="wc_user_settings_email_me_delete_links" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_email_me_delete_links_desc"><?php esc_html_e('"Bulk management via email" description', "wpdiscuz"); ?></label></th>
                <td colspan="3"><textarea type="text"  name="wc_user_settings_email_me_delete_links_desc" id="wc_user_settings_email_me_delete_links_desc"><?php echo esc_html($this->phrases["wc_user_settings_email_me_delete_links_desc"]); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_no_data"><?php esc_html_e("No data found!", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_no_data"]); ?>" name="wc_user_settings_no_data" id="wc_user_settings_no_data" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_request_deleting_comments"><?php esc_html_e("Delete all my comments", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_request_deleting_comments"]); ?>" name="wc_user_settings_request_deleting_comments" id="wc_user_settings_request_deleting_comments" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_cancel_subscriptions"><?php esc_html_e("Cancel all comment subscriptions", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_cancel_subscriptions"]); ?>" name="wc_user_settings_cancel_subscriptions" id="wc_user_settings_cancel_subscriptions" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_clear_cookie"><?php esc_html_e("Clear cookies with my personal data", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_clear_cookie"]); ?>" name="wc_user_settings_clear_cookie" id="wc_user_settings_clear_cookie" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_delete_links"><?php esc_html_e("Bulk management via email", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_delete_links"]); ?>" name="wc_user_settings_delete_links" id="wc_user_settings_delete_links" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="wc_user_settings_delete_all_comments"><?php esc_html_e("Delete all my comments", "wpdiscuz"); ?></label>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?>:
                    <div class="wc_available_variables">                                                
                        <div class="wc_available_variable">[SITE_URL]</div>
                        <div class="wc_available_variable">[BLOG_TITLE]</div>
                    </div>
                    </p>
                </th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_delete_all_comments"]); ?>" name="wc_user_settings_delete_all_comments" id="wc_user_settings_delete_all_comments" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="wc_user_settings_delete_all_comments_message"><?php esc_html_e("Delete all comments email text", "wpdiscuz"); ?></label>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?>:
                    <div class="wc_available_variables">                                                
                        <div class="wc_available_variable">[SITE_URL]</div>
                        <div class="wc_available_variable">[BLOG_TITLE]</div>
                        <div class="wc_available_variable">[DELETE_COMMENTS_URL]</div>
                    </div>
                    </p>
                </th>
                <td colspan="3"><?php wp_editor($this->phrases["wc_user_settings_delete_all_comments_message"], "wc_user_settings_delete_all_comments_message", ["textarea_rows" => 7, "teeny" => true]); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="wc_user_settings_delete_all_subscriptions"><?php esc_html_e("Delete all my subscriptions", "wpdiscuz"); ?></label>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?>:
                    <div class="wc_available_variables">                                                
                        <div class="wc_available_variable">[SITE_URL]</div>
                        <div class="wc_available_variable">[BLOG_TITLE]</div>
                    </div>
                    </p>
                </th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_delete_all_subscriptions"]); ?>" name="wc_user_settings_delete_all_subscriptions" id="wc_user_settings_delete_all_subscriptions" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="wc_user_settings_delete_all_subscriptions_message"><?php esc_html_e("Delete all subscriptions email text", "wpdiscuz"); ?></label>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?>:
                    <div class="wc_available_variables">                                                
                        <div class="wc_available_variable">[SITE_URL]</div>
                        <div class="wc_available_variable">[BLOG_TITLE]</div>
                        <div class="wc_available_variable">[DELETE_SUBSCRIPTIONS_URL]</div>
                    </div>
                    </p>
                </th>
                <td colspan="3"><?php wp_editor($this->phrases["wc_user_settings_delete_all_subscriptions_message"], "wc_user_settings_delete_all_subscriptions_message", ["textarea_rows" => 7, "teeny" => true]); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="wc_user_settings_delete_all_follows"><?php esc_html_e("Delete all my follows", "wpdiscuz"); ?></label>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?>:
                    <div class="wc_available_variables">                                                
                        <div class="wc_available_variable">[SITE_URL]</div>
                        <div class="wc_available_variable">[BLOG_TITLE]</div>
                    </div>
                    </p>
                </th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_delete_all_follows"]); ?>" name="wc_user_settings_delete_all_follows" id="wc_user_settings_delete_all_follows" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="wc_user_settings_delete_all_follows_message"><?php esc_html_e("Delete all follows email text", "wpdiscuz"); ?></label>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?>:
                    <div class="wc_available_variables">                                                
                        <div class="wc_available_variable">[SITE_URL]</div>
                        <div class="wc_available_variable">[BLOG_TITLE]</div>
                        <div class="wc_available_variable">[DELETE_FOLLOWS_URL]</div>
                    </div>
                    </p>
                </th>
                <td colspan="3"><?php wp_editor($this->phrases["wc_user_settings_delete_all_follows_message"], "wc_user_settings_delete_all_follows_message", ["textarea_rows" => 7, "teeny" => true]); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_subscribed_to_replies"><?php esc_html_e("subscribed to this comment", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_subscribed_to_replies"]); ?>" name="wc_user_settings_subscribed_to_replies" id="wc_user_settings_subscribed_to_replies" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_subscribed_to_replies_own"><?php esc_html_e("subscribed to my comments", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_subscribed_to_replies_own"]); ?>" name="wc_user_settings_subscribed_to_replies_own" id="wc_user_settings_subscribed_to_replies_own" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_subscribed_to_all_comments"><?php esc_html_e("subscribed to all follow-up comments of this post", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_subscribed_to_all_comments"]); ?>" name="wc_user_settings_subscribed_to_all_comments" id="wc_user_settings_subscribed_to_all_comments" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_check_email"><?php esc_html_e("Please check your email.", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_check_email"]); ?>" name="wc_user_settings_check_email" id="wc_user_settings_check_email" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_user_settings_email_error"><?php esc_html_e("Error : Can't send email.", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_user_settings_email_error"]); ?>" name="wc_user_settings_email_error" id="wc_user_settings_email_error" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_confirm_comment_delete"><?php esc_html_e("Are you sure you want to delete this comment?", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_confirm_comment_delete"]); ?>" name="wc_confirm_comment_delete" id="wc_confirm_comment_delete" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_confirm_cancel_subscription"><?php esc_html_e("Are you sure you want to cancel this subscription?", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_confirm_cancel_subscription"]); ?>" name="wc_confirm_cancel_subscription" id="wc_confirm_cancel_subscription" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_confirm_cancel_follow"><?php esc_html_e("Are you sure you want to cancel this follow?", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_confirm_cancel_follow"]); ?>" name="wc_confirm_cancel_follow" id="wc_confirm_cancel_follow" /></td>
            </tr>
        </tbody>
    </table>
</div>