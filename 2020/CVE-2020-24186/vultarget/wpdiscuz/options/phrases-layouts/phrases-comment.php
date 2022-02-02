<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php esc_html_e("Comment Template Phrases", "wpdiscuz"); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row"><label for="wc_reply_text"><?php esc_html_e("Reply", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_reply_text"]); ?>" name="wc_reply_text" id="wc_submit_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_share_text"><?php esc_html_e("Share", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_share_text"]); ?>" name="wc_share_text" id="wc_share_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_edit_text"><?php esc_html_e("Edit", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_edit_text"]); ?>" name="wc_edit_text" id="wc_edit_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_share_facebook"><?php esc_html_e("Share On Facebook", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_share_facebook"]); ?>" name="wc_share_facebook" id="wc_share_facebook" /></td>
            </tr>
            <tr valign="top" >
                <th scope="row"><label for="wc_share_twitter"><?php esc_html_e("Share On Twitter", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_share_twitter"]); ?>" name="wc_share_twitter" id="wc_share_twitter" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_share_whatsapp"><?php esc_html_e("Share On WhatsApp", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_share_whatsapp"]); ?>" name="wc_share_whatsapp" id="wc_share_whatsapp" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_share_vk"><?php esc_html_e("Share On VKontakte", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_share_vk"]); ?>" name="wc_share_vk" id="wc_share_vk" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_share_ok"><?php esc_html_e("Share On Odnoklassniki", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_share_ok"]); ?>" name="wc_share_ok" id="wc_share_ok" /></td>
            </tr>
            <tr valign="top" >
                <th scope="row"><label for="wc_hide_replies_text"><?php esc_html_e("Hide Replies", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_hide_replies_text"]); ?>" name="wc_hide_replies_text" id="wc_hide_replies_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_show_replies_text"><?php esc_html_e("View Replies", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_show_replies_text"]); ?>" name="wc_show_replies_text" id="wc_show_replies_text" /></td>
            </tr>
            <?php
            $roles = $this->labels["blogRoles"];
            foreach ($roles as $roleName => $color) {
                $phraseRoleLabel = ucfirst(str_replace("_", " ", $roleName));

                if ($roleName == "administrator") {
                    $roleTitle = isset($this->phrases["wc_blog_role_" . $roleName]) ? $this->phrases["wc_blog_role_" . $roleName] : esc_html__("Admin", "wpdiscuz");
                } elseif ($roleName == "post_author") {
                    $roleTitle = isset($this->phrases["wc_blog_role_" . $roleName]) ? $this->phrases["wc_blog_role_" . $roleName] : esc_html__("Author", "wpdiscuz");
                } elseif ($roleName == "editor") {
                    $roleTitle = isset($this->phrases["wc_blog_role_" . $roleName]) ? $this->phrases["wc_blog_role_" . $roleName] : esc_html__("Editor", "wpdiscuz");
                } elseif ($roleName == "guest") {
                    $roleTitle = isset($this->phrases["wc_blog_role_" . $roleName]) ? $this->phrases["wc_blog_role_" . $roleName] : esc_html__("Guest", "wpdiscuz");
                } else {
                    $roleTitle = isset($this->phrases["wc_blog_role_" . $roleName]) ? $this->phrases["wc_blog_role_" . $roleName] : esc_html__("Member", "wpdiscuz");
                }
                ?>
                <tr valign="top">
                    <th scope="row"><label for="wc_blog_role_<?php echo esc_attr($roleName); ?>"><?php echo esc_html($phraseRoleLabel); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(isset($this->phrases["wc_blog_role_" . $roleName]) ? $this->phrases["wc_blog_role_" . $roleName] : $roleTitle); ?>" id="wc_blog_role_<?php echo esc_attr($roleName); ?>" name="wc_blog_role_<?php echo esc_attr($roleName); ?>"/></td>
                </tr>
                <?php
            }
            ?>
            <tr valign="top">
                <th scope="row"><label for="wc_vote_up"><?php esc_html_e("Vote Up", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_vote_up"]); ?>" name="wc_vote_up" id="wc_vote_up" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_vote_down"><?php esc_html_e("Vote Down", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_vote_down"]); ?>" name="wc_vote_down" id="wc_vote_down" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_comment_edit_save_button"><?php esc_html_e("Save edited comment button text", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_comment_edit_save_button"]); ?>" name="wc_comment_edit_save_button" id="wc_comment_edit_save_button" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_comment_edit_cancel_button"><?php esc_html_e("Cancel comment editing button text", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_comment_edit_cancel_button"]); ?>" name="wc_comment_edit_cancel_button" id="wc_comment_edit_cancel_button" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_read_more"><?php esc_html_e("Comment read more link text", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_read_more"]); ?>" name="wc_read_more" id="wc_read_more" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_anonymous"><?php esc_html_e("Anonymous commenter name", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_anonymous"]); ?>" name="wc_anonymous" id="wc_anonymous" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_stick_comment_btn_title"><?php esc_html_e("Stick button title", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_stick_comment_btn_title"]); ?>" name="wc_stick_comment_btn_title" id="wc_stick_comment_btn_title" /></td>
            </tr>            
            <tr valign="top">
                <th scope="row"><label for="wc_stick_comment"><?php esc_html_e("Stick", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_stick_comment"]); ?>" name="wc_stick_comment" id="wc_stick_comment" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_unstick_comment"><?php esc_html_e("Unstick", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_unstick_comment"]); ?>" name="wc_unstick_comment" id="wc_unstick_comment" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_sticky_comment_icon_title"><?php esc_html_e("Sticky comment icon title", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_sticky_comment_icon_title"]); ?>" name="wc_sticky_comment_icon_title" id="wc_sticky_comment_icon_title" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_close_comment_btn_title"><?php esc_html_e("Close button title", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_close_comment_btn_title"]); ?>" name="wc_close_comment_btn_title" id="wc_close_comment_btn_title" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_close_comment"><?php esc_html_e("Close", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_close_comment"]); ?>" name="wc_close_comment" id="wc_close_comment" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_open_comment"><?php esc_html_e("Open", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_open_comment"]); ?>" name="wc_open_comment" id="wc_open_comment" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_closed_comment_icon_title"><?php esc_html_e("Closed comment icon title", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_closed_comment_icon_title"]); ?>" name="wc_closed_comment_icon_title" id="wc_closed_comment_icon_title" /></td>
            </tr>
        </tbody>
    </table>
</div>