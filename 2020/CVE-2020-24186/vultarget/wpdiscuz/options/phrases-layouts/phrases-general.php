<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php esc_html_e("General Phrases", "wpdiscuz"); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row"><label for="wc_be_the_first_text"><?php esc_html_e("Be the first to comment", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_be_the_first_text"]); ?>" name="wc_be_the_first_text" id="wc_be_the_first_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_load_more_submit_text"><?php esc_html_e("Load More Button", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_load_more_submit_text"]); ?>" name="wc_load_more_submit_text" id="wc_load_more_submit_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_load_rest_comments_submit_text"><?php esc_html_e("Load Rest of Comments", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_load_rest_comments_submit_text"]); ?>" name="wc_load_rest_comments_submit_text" id="wc_load_rest_comments_submit_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_copied_to_clipboard"><?php esc_html_e("Copied to clipboard!", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_copied_to_clipboard"]); ?>" name="wc_copied_to_clipboard" id="wc_copied_to_clipboard" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_feedback_shortcode_tooltip"><?php esc_html_e("Select a part of text and ask readers for feedback (inline commenting)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_feedback_shortcode_tooltip"]); ?>" name="wc_feedback_shortcode_tooltip" id="wc_feedback_shortcode_tooltip" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_feedback_popup_title"><?php esc_html_e("Ask for Feedback", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_feedback_popup_title"]); ?>" name="wc_feedback_popup_title" id="wc_feedback_popup_title" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_please_leave_feebdack"><?php esc_html_e("Please leave a feedback on this", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_please_leave_feebdack"]); ?>" name="wc_please_leave_feebdack" id="wc_please_leave_feebdack" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_feedback_content_text"><?php esc_html_e("Feedback on post content", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_feedback_content_text"]); ?>" name="wc_feedback_content_text" id="wc_feedback_content_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_feedback_comment_success"><?php esc_html_e("Thank you for your feedback", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_feedback_comment_success"]); ?>" name="wc_feedback_comment_success" id="wc_feedback_comment_success" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_commenting_is_closed"><?php esc_html_e("Commenting is closed", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_commenting_is_closed"]); ?>" name="wc_commenting_is_closed" id="wc_commenting_is_closed" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_closed_comment_thread"><?php esc_html_e("This is closed comment thread", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_closed_comment_thread"]); ?>" name="wc_closed_comment_thread" id="wc_closed_comment_thread" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_bubble_invite_message"><?php esc_html_e("Would love your thoughts, please comment", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_bubble_invite_message"]); ?>" name="wc_bubble_invite_message" id="wc_bubble_invite_message" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_vote_phrase"><?php esc_html_e("vote", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_vote_phrase"]); ?>" name="wc_vote_phrase" id="wc_vote_phrase" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_votes_phrase"><?php esc_html_e("votes", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_votes_phrase"]); ?>" name="wc_votes_phrase" id="wc_votes_phrase" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_comment_link"><?php esc_html_e("Comment Link", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_comment_link"]); ?>" name="wc_comment_link" id="wc_comment_link" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_not_allowed_to_comment_more_than"><?php esc_html_e("We are sorry, you are not allowed to comment more than %d time(s)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_not_allowed_to_comment_more_than"]); ?>" name="wc_not_allowed_to_comment_more_than" id="wc_not_allowed_to_comment_more_than" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_not_allowed_to_create_comment_thread_more_than"><?php esc_html_e("We are sorry, you are not allowed to create a new comment thread more than %d time(s)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_not_allowed_to_create_comment_thread_more_than"]); ?>" name="wc_not_allowed_to_create_comment_thread_more_than" id="wc_not_allowed_to_create_comment_thread_more_than" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_not_allowed_to_reply_more_than"><?php esc_html_e("We are sorry, you are not allowed to reply more than %d time(s)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_not_allowed_to_reply_more_than"]); ?>" name="wc_not_allowed_to_reply_more_than" id="wc_not_allowed_to_reply_more_than" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_inline_form_comment"><?php esc_html_e("Your comment here...", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_inline_form_comment"]); ?>" name="wc_inline_form_comment" id="wc_inline_form_comment" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_inline_form_notify"><?php esc_html_e("Notify me via email when a new reply is posted", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_inline_form_notify"]); ?>" name="wc_inline_form_notify" id="wc_inline_form_notify" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_inline_form_name"><?php esc_html_e("Your Name*", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_inline_form_name"]); ?>" name="wc_inline_form_name" id="wc_inline_form_name" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_inline_form_email"><?php esc_html_e("Your Email", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_inline_form_email"]); ?>" name="wc_inline_form_email" id="wc_inline_form_email" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_inline_form_comment_button"><?php esc_html_e("COMMENT", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_inline_form_comment_button"]); ?>" name="wc_inline_form_comment_button" id="wc_inline_form_comment_button" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_inline_comments_view_all"><?php esc_html_e("View all comments", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_inline_comments_view_all"]); ?>" name="wc_inline_comments_view_all" id="wc_inline_comments_view_all" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_inline_feedbacks"><?php esc_html_e("Inline Feedbacks", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_inline_feedbacks"]); ?>" name="wc_inline_feedbacks" id="wc_inline_feedbacks" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_unable_sent_email"><?php esc_html_e("Unable to send an email", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_unable_sent_email"]); ?>" name="wc_unable_sent_email" id="wc_unable_sent_email" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_subscription_fault"><?php esc_html_e("Subscription Fault", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_subscription_fault"]); ?>" name="wc_subscription_fault" id="wc_subscription_fault" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_comments_are_deleted"><?php esc_html_e("Your comments have been deleted from database", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_comments_are_deleted"]); ?>" name="wc_comments_are_deleted" id="wc_comments_are_deleted" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_cancel_subs_success"><?php esc_html_e("You cancel all your subscriptions successfully", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_cancel_subs_success"]); ?>" name="wc_cancel_subs_success" id="wc_cancel_subs_success" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_cancel_follows_success"><?php esc_html_e("You cancel all your follows successfully", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_cancel_follows_success"]); ?>" name="wc_cancel_follows_success" id="wc_cancel_follows_success" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_follow_confirm_success"><?php esc_html_e("Follow has been confirmed successfully", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_follow_confirm_success"]); ?>" name="wc_follow_confirm_success" id="wc_follow_confirm_success" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_follow_cancel_success"><?php esc_html_e("Follow has been canceled successfully", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_follow_cancel_success"]); ?>" name="wc_follow_cancel_success" id="wc_follow_cancel_success" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_login_to_comment"><?php esc_html_e("Please login to comment", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_login_to_comment"]); ?>" name="wc_login_to_comment" id="wc_login_to_comment" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_view_comments"><?php esc_html_e("View Comments", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_view_comments"]); ?>" name="wc_view_comments" id="wc_view_comments" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_spoiler"><?php esc_html_e("Spoiler", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_spoiler"]); ?>" name="wc_spoiler" id="wc_spoiler" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_last_edited"><?php esc_html_e('Last edited %1$s by %2$s', "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_last_edited"]); ?>" name="wc_last_edited" id="wc_last_edited" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_reply_to"><?php esc_html_e("Reply to", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_reply_to"]); ?>" name="wc_reply_to" id="wc_reply_to" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_manage_comment"><?php esc_html_e("Manage Comment", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_manage_comment"]); ?>" name="wc_manage_comment" id="wc_manage_comment" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_spoiler_title"><?php esc_html_e("Spoiler Title", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_spoiler_title"]); ?>" name="wc_spoiler_title" id="wc_spoiler_title" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_cannot_rate_again"><?php esc_html_e("You cannot rate again", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_cannot_rate_again"]); ?>" name="wc_cannot_rate_again" id="wc_cannot_rate_again" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_not_allowed_to_rate"><?php esc_html_e("You're not allowed to rate here", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_not_allowed_to_rate"]); ?>" name="wc_not_allowed_to_rate" id="wc_not_allowed_to_rate" /></td>
            </tr>
        </tbody>
    </table>
</div>