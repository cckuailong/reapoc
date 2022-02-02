<?php
if (!defined("ABSPATH")) {
    exit();
}

if (!$isUcmExists) {
    ?>
    <div>
        <div class="ucm-inner">
            <h3 style="padding:5px 10px 15px 10px; margin:0px; text-align:right; border-bottom:1px solid #ddd; margin:0px 0px 20px auto; font-weight:normal;max-width:60%;">Addon - User and Comment Mentioning</h3>
            <p style="border: 1px dotted #07B290; padding: 15px; font-size: 14px; text-align: center; margin: 10px; background: #EFFFF5">
                wpDiscuz User &amp; Comment Mentioning addon allows to mention comments and users in comment text using #comment-id and @username tags. 
                This is a demo admin page of the wpDiscuz User &amp; Comment Mentioning addon. You can buy this addon on gVectors Team Store.
                <br><a href="https://gvectors.com/product/wpdiscuz-user-comment-mentioning/" target="_blank" style="padding: 6px 15px; background: #07B290; color: #fff; display: inline-block; margin: 15px 5px 5px 5px;">Addon Details and Screenshots &raquo;</a>
            </p>
            <table style="opacity: 0.6;">
                <tr valign="top">
                    <th scope="row" style="width:55%;">
                        <label for="ucm_at">
                            Display <span class="wpdiscuz-button wpdc_at"></span> button on comment form:                    </label>


                    </th>
                    <td>
                        <input type="checkbox" name="" checked='checked' value="1" id="ucm_at"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="width:55%;">
                        <label for="guestMentioning">Enable guest mentioning:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="" checked='checked' value="1" id="guestMentioning"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="width:55%;">
                        <label for="displayNicename">
                            Display user nicename (names for mentioning) in user search result:                    </label>


                    </th>
                    <td>
                        <input type="checkbox" name="" value="1" id="displayNicename"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="width:55%;">
                        <label for="ucm_view_avatar_in_comment">
                            Display mentioned  user avatar with username link:</label>

                    </th>
                    <td>
                        <input type="checkbox" name="" checked='checked' value="1" id="ucm_view_avatar_in_comment"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="width:55%;">
                        <label for="ucm_view_avatar_tooltip">
                            Display avatar in user pop-up information:                    </label>


                    </th>
                    <td>
                        <input type="checkbox" name="" checked='checked' value="1" id="ucm_view_avatar_tooltip"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="width:55%;">
                        <label for="ucm_viewid">
                            Mentione user by user ID in comment content                    </label>


                    </th>
                    <td>
                        <input type="checkbox" name="" value="1" id="ucm_viewid"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="ucm_text_length">
                            Text length in user/comment pop-up information:                    </label>


                    </th>
                    <td>
                        <input type="number" name="" min='0' value="150" id="ucm_text_length"/> <span style="display:inline-block; vertical-align:bottom; font-size:14px; padding:5px;">characters</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="ucm_user_count">
                            Maximum number of users in "User Selector" bar:                    </label>


                    </th>
                    <td>
                        <input type="number" name="" min='1' value="8" id="ucm_user_count"/> <span style="display:inline-block; vertical-align:bottom; font-size:14px; padding:5px;">items</span>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row" colspan="2" style="border-top:3px solid #ddd;">
                        <h3 style="padding-bottom:5px; padding-top:20px; font-weight:normal; margin:0px; text-align:right;"> Comment mentioning through #comment-id</h3>
                        <strong>Message to mentioned comment author</strong>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row" style="width:55%;">
                        <label for="ucm_send_admin">
                            Enable Email Notification                    </label>


                    </th>
                    <td>
                        <input type="checkbox" name="" checked='checked' value="1" id="ucm_send_admin"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="author_mail_subject">
                            Email Subject                    </label>


                    </th>
                    <td>
                        <input type="text" name="" class="text-inner" value="Your Comment has been mentioned" id="author_mail_subject"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="author_mail_message">
                            Email Body                    </label>


                        <p class="wpd-info">
                            <i> [mentionedUserName] - Mentioned comment author name</i><br/>
                            <i> [postTitle] - Post Title</i><br/>
                            <i> [authorUserName] - Comment author name</i><br/>
                            <i> [commentURL] - Comment URL</i>
                        </p>
                    </th>
                    <td>
                        <textarea name="" id="author_mail_message" cols="50" rows="6">Hi [mentionedUserName]!
            Your comment on "[postTitle]" post has been mentioned by [authorUserName].</br>
            </br>
            Comment URL: [commentURL]</textarea>


                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" colspan="2" style="border-top:3px solid #ddd;">
                        <h3 style="padding-bottom:5px; padding-top:20px; font-weight:normal; margin:0px; text-align:right;"> User mentioning through @username</h3>
                        <strong>Message to mentioned user</strong>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row" style="width:55%;">
                        <label for="ucm_send_user">
                            Enable Email Notification                    </label>


                    </th>
                    <td>
                        <input type="checkbox" name="" checked='checked' value="1" id="ucm_send_user"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="user_mail_subject">Email Subject</label>
                    </th>
                    <td>
                        <input type="text" name="" value="You have been mentioned in comment" id="user_mail_subject"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="user_mail_message">
                            Email Body                    </label>


                        <p class="wpd-info">
                            <i> [mentionedUserName] - Mentioned user name</i><br/>
                            <i> [postTitle] - Post Title</i><br/>
                            <i> [authorUserName] - Comment author name</i><br/>
                            <i> [commentURL] - Comment URL</i>
                        </p>
                    </th>
                    <td>
                        <textarea name="" id="wpdiscuz_reported_email_message" cols="50" rows="6">Hi [mentionedUserName]!
            You have been mentioned in a comment posted on "[postTitle]" post by [authorUserName].</br>
            </br>
            Comment URL: [commentURL]</textarea>


                    </td>
                </tr>
            </table>

        </div>
    </div>
    <?php
}