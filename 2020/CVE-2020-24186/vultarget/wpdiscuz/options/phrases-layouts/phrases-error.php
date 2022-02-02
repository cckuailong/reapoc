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
                <th scope="row"><label for="wc_error_empty_text"><?php esc_html_e("Error message for empty field", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_error_empty_text"]); ?>" name="wc_error_empty_text" id="wc_error_empty_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_error_email_text"><?php esc_html_e("Error message for invalid email field", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_error_email_text"]); ?>" name="wc_error_email_text" id="wc_error_email_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_error_url_text"><?php esc_html_e("Error message for invalid website url field", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_error_url_text"]); ?>" name="wc_error_url_text" id="wc_error_url_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_vote_only_one_time"><?php esc_html_e("You can vote only 1 time", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_vote_only_one_time"]); ?>" name="wc_vote_only_one_time" id="wc_vote_only_one_time" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_voting_error"><?php esc_html_e("Voting Error", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_voting_error"]); ?>" name="wc_voting_error" id="wc_voting_error" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_self_vote"><?php esc_html_e("You Cannot Vote On Your Comment", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_self_vote"]); ?>" name="wc_self_vote" id="wc_self_vote" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_deny_voting_from_same_ip"><?php esc_html_e("You are not allowed to vote for this comment (Voting from same IP)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_deny_voting_from_same_ip"]); ?>" name="wc_deny_voting_from_same_ip" id="wc_deny_voting_from_same_ip" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_invalid_captcha"><?php esc_html_e("Invalid Captcha Code", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_invalid_captcha"]); ?>" name="wc_invalid_captcha" id="wc_invalid_captcha" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_invalid_field"><?php esc_html_e("Some of field value is invalid", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_invalid_field"]); ?>" name="wc_invalid_field" id="wc_invalid_field" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_msg_input_min_length"><?php esc_html_e("Message if input text length is too short", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_msg_input_min_length"]); ?>" name="wc_msg_input_min_length" id="wc_msg_input_min_length" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_msg_input_max_length"><?php esc_html_e("Message if input text length is too long", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_msg_input_max_length"]); ?>" name="wc_msg_input_max_length" id="wc_msg_input_max_length" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_comment_not_updated"><?php esc_html_e("Message if comment was not updated", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_comment_not_updated"]); ?>" name="wc_comment_not_updated" id="wc_comment_not_updated" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_comment_edit_not_possible"><?php esc_html_e("Message if comment no longer possible to edit", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_comment_edit_not_possible"]); ?>" name="wc_comment_edit_not_possible" id="wc_comment_edit_not_possible" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_comment_not_edited"><?php esc_html_e("Message if comment text not changed", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_comment_not_edited"]); ?>" name="wc_comment_not_edited" id="wc_comment_not_edited" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_msg_required_fields"><?php esc_html_e("Please fill out required fields", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_msg_required_fields"]); ?>" name="wc_msg_required_fields" id="wc_msg_required_fields" /></td>
            </tr>
        </tbody>
    </table>
</div>