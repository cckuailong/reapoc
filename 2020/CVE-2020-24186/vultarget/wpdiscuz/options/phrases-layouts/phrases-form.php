<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php esc_html_e("Form Template Phrases", "wpdiscuz"); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row"><label for="wc_comment_start_text"><?php esc_html_e("Comment Field Start", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_comment_start_text"]); ?>" name="wc_comment_start_text" id="wc_comment_start_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_comment_join_text"><?php esc_html_e("Comment Field Join", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_comment_join_text"]); ?>" name="wc_comment_join_text" id="wc_comment_join_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_most_reacted_comment"><?php esc_html_e("Most reacted comment", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_most_reacted_comment"]); ?>" name="wc_most_reacted_comment" id="wc_most_reacted_comment" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_hottest_comment_thread"><?php esc_html_e("Hottest comment thread", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_hottest_comment_thread"]); ?>" name="wc_hottest_comment_thread" id="wc_hottest_comment_thread" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_inline_comments"><?php esc_html_e("Inline Comments", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_inline_comments"]); ?>" name="wc_inline_comments" id="wc_inline_comments" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_email_text"><?php esc_html_e("Email Field", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_email_text"]); ?>" name="wc_email_text" id="wc_email_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_subscribe_anchor"><?php esc_html_e("Subscribe", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_subscribe_anchor"]); ?>" name="wc_subscribe_anchor" id="wc_subscribe_anchor" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_notify_of"><?php esc_html_e("Notify of", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_notify_of"]); ?>" name="wc_notify_of" id="wc_notify_of" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_notify_on_new_comment"><?php esc_html_e("Notify on new comments", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_notify_on_new_comment"]); ?>" name="wc_notify_on_new_comment" id="wc_notify_on_new_comment" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_notify_on_all_new_reply"><?php esc_html_e("Notify on all new replies", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_notify_on_all_new_reply"]); ?>" name="wc_notify_on_all_new_reply" id="wc_notify_on_all_new_reply" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_notify_on_new_reply"><?php esc_html_e("Notify on new replies to this comment", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_notify_on_new_reply"]); ?>" name="wc_notify_on_new_reply" id="wc_notify_on_new_reply" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_sort_by"><?php esc_html_e("Sort by", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_sort_by"]); ?>" name="wc_sort_by" id="wc_sort_by" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_newest"><?php esc_html_e("Newest", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_newest"]); ?>" name="wc_newest" id="wc_newest" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_oldest"><?php esc_html_e("Oldest", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_oldest"]); ?>" name="wc_oldest" id="wc_oldest" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_most_voted"><?php esc_html_e("Most Voted", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_most_voted"]); ?>" name="wc_most_voted" id="wc_most_voted" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_subscribed_on_comment"><?php esc_html_e("Subscribed on this comment replies", "wpdiscuz"); ?></label></th>
                <td colspan="3"><textarea name="wc_subscribed_on_comment" id="wc_subscribed_on_comment"><?php echo esc_html($this->phrases["wc_subscribed_on_comment"]); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_subscribed_on_all_comment"><?php esc_html_e("Subscribed on all your comments replies", "wpdiscuz"); ?></label></th>
                <td colspan="3"><textarea name="wc_subscribed_on_all_comment" id="wc_subscribed_on_all_comment"><?php echo esc_html($this->phrases["wc_subscribed_on_all_comment"]); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_subscribed_on_post"><?php esc_html_e("Subscribed on this post", "wpdiscuz"); ?></label></th>
                <td colspan="3"><textarea name="wc_subscribed_on_post" id="wc_subscribed_on_post"><?php echo esc_html($this->phrases["wc_subscribed_on_post"]); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_form_subscription_submit"><?php esc_html_e("Form subscription button", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_form_subscription_submit"]); ?>" name="wc_form_subscription_submit" id="wc_form_subscription_submit" /></td>
            </tr>
        </tbody>
    </table>
</div>