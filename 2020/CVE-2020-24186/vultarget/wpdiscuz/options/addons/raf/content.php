<?php

if (!defined("ABSPATH")) {
    exit();
}

if (!$isRafExists) {
    ?>
    <style>
        .flag-inner input[type=text], .flag-inner input[type=email]{height: 30px;width: 95%;padding:3px 5px;}
        .flag-inner input[type=number]{height: 30px;padding:3px 5px;width:80px;}
        .flag-inner textarea{padding:10px;width: 95%;margin-left:10px;} 
        .add_new{border: 1px solid rgba(0, 91, 255, 0.34);border-radius: 4px;padding: 5px 10px !important;cursor: pointer;}
        .add_new:hover{background-color: lightgrey;}
        .report_remove{width: 30px;height: 30px; background:url("../images/minus-sign.png") center no-repeat; background-color: white; border: 0 !important;}
        .report_remove:hover{opacity: 0.8;cursor: pointer;}
        .flag-inner p.wpd-info{font-size:13px; line-height:15px; color:#999999; width:95%; padding:0px; margin:0px; font-style:italic; margin-top:5px;}
        .flag-inner label{ vertical-align:top; font-size:14px;}
    </style>
    <div class="flag-inner">
        <h3 style="padding:5px 10px 10px 10px; margin:0px; text-align:right; border-bottom:1px solid #ddd; max-width:60%; margin:0px 0px 20px auto; font-weight:normal;">
            Addon - Report &amp; Flagging
        </h3>
        <p style="border: 1px dotted #07B290; padding: 15px; font-size: 14px; text-align: center; margin: 10px; background: #EFFFF5">
            wpDiscuz Report &amp; Flagging addon is a comment reporting tools. Auto-moderates comments based on number of flags and dislikes.
            This is a demo admin page of the wpDiscuz Report &amp; Flagging addon. You can buy this addon on gVectors Team Store.
            <br>
            <a href="https://gvectors.com/product/wpdiscuz-report-flagging/" target="_blank" 
               style="padding: 6px 15px; background: #07B290; color: #fff; display: inline-block; margin: 15px 5px 5px 5px;">
                Addon Details and Screenshots &raquo;</a>
        </p>
        <table style="opacity: 0.6;">
            <tbody>
                <tr valign="top">
                    <th scope="row" style="width:55%;">
                        <label for="wpdiscuz_show_flag">
                            Show flag icon on comments                    </label>

                        <p class="wpd-info">If this option is disabled, comment auto-moderation will only be based on down votes / dislikes.</p>
                    </th>
                    <td>
                        <input name="" checked="checked" value="1" id="wpdiscuz_show_flag"  type="checkbox">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_allow_guest_flag">
                            Allow guests to flag and report comments                    </label>

                    </th>
                    <td>
                        <input name="" checked="checked" value="1" id="wpdiscuz_allow_guest_flag"  type="checkbox">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_allow_user_messag_flag">
                            Enable comment reporting pop-up form for registered users                    </label>

                        <p class="wpd-info">For security reasons comment reporting form is disabled for guests by default. Guests are still able to flag without sending message. However you can enable this for guests using the option below.</p>
                    </th>
                    <td>
                        <input name="" checked="checked" value="1" id="wpdiscuz_allow_user_messag_flag"  type="checkbox">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_allow_messag_flag_guest">
                            Enable comment reporting pop-up form for guests                    </label>

                    </th>
                    <td>
                        <input name="" value="1" id="wpdiscuz_allow_messag_flag_guest"  type="checkbox">
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">
                        Enable auto-moderation for flagged/disliked comments:
                        <p class="wpd-info">This will automatically Unapprove or Trash comments which reached the maximum number of flags or dislikes set below</p>
                    </th>
                    <td>
                        <input name="" checked="checked" value="unapprove" id="wpdiscuz_unapprove"  type="radio"> <label for="wpdiscuz_unapprove"> unapprove </label>
                        <input name="" value="trash" id="wpdiscuz_trash"  type="radio"> <label for="wpdiscuz_trash"> trash </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_flag_count">
                            Do auto-moderation if comment is flagged more than                    </label>

                    </th>
                    <td>
                        <input name="" value="5" id="wpdiscuz_flag_count"  type="number"> <span style="display:inline-block; vertical-align:bottom; font-size:14px; padding:5px;">times</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_vote_count">
                            Do auto-moderation if comment is down voted more than                    </label>

                    </th>
                    <td>
                        <input name="" value="10" id="wpdiscuz_vote_count"  type="number"> <span style="display:inline-block; vertical-align:bottom; font-size:14px; padding:5px;">times</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_notify_admin">
                            Notify admin when comment is auto-moderated:
                        </label>

                    </th>
                    <td>
                        <input name="" checked="checked" value="1" id="wpdiscuz_notify_admin"  type="checkbox">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_admin_email">
                            Admin Email:
                        </label>

                    </th>
                    <td>
                        <input name="" value="admin@example.com" id="wpdiscuz_admin_email"  type="email">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" colspan="2" style="border-top:3px solid #ddd;">
                        <h3 style="padding-bottom:5px; padding-top:20px; font-weight:normal; margin:0px;"> Comment reporting message from reporter to admin</h3>
                        <p class="wpd-info"> This message comes from comment reporting pop-up form. It includes reporter message and bad comment category (reason).</p>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_flagged_email_subject">
                            Report message subject                    </label>

                    </th>
                    <td>
                        <input name="" value="New comment report" id="wpdiscuz_flagged_email_subject"  type="text">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_flagged_email_message">
                            Report message body                    </label>

                        <p class="wpd-info">
                            <i> [userInfo] - username or user IP (guests)</i><br>
                            <i> [reason] - bad comment category</i><br>
                            <i> [message] - report message</i><br>
                            <i> [postTitle] - post title</i><br>
                            <i> [commentInfo] - comment text or URL</i>
                        </p>
                    </th>
                    <td>
                        <textarea name="" id="wpdiscuz_flagged_email_message" cols="50" rows="6">&lt;h2&gt;Report details:&lt;/h2&gt; 
        &lt;p&gt;Reporter IP | Name: [userInfo]&lt;/p&gt;
        &lt;p&gt;Reason: [reason],&lt;/p&gt;
        &lt;p&gt;Message: [message],&lt;/p&gt;
        &lt;p&gt;Post: [postTitle],&lt;/p&gt;
        &lt;p&gt;Comment URL | Text: [commentInfo]&lt;/p&gt;</textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" colspan="2" style="border-top:3px solid #ddd;">
                        <h3 style="padding-bottom:5px; padding-top:20px; font-weight:normal; margin:0px;"> Auto-moderation notification to admin</h3>
                        <p class="wpd-info"> This message will be sent to admin once maximum number of flags or dislikes is reached for certain comment and this comment is auto-moderated (trashed or unapproved)</p>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_moderated_email_subject">
                            Auto-moderation message subject                    </label>

                        <p class="wpd-info">Please do not remove %s variable at end of this phrase. This variable will be changed to auto-moderation mode "flags" or "dislikes".</p>
                    </th>
                    <td>
                        <input name="" value="New comment has reached to the maximum number of %s" id="wpdiscuz_moderated_email_subject"  type="text">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpdiscuz_reported_email_message">
                            Auto-moderation message body                    </label>

                        <p class="wpd-info">
                            <i> [status] - comment status</i><br>
                            <i> [postName] - post URL</i><br>
                            <i> [postTitle] - post title</i><br>
                            <i> [userLogin] - user login</i><br>
                            <i> [userIP] - user IP</i><br>
                            <i> [userEmail] - user email</i><br>
                            <i> [commentContent] - reported comment content</i>
                        </p>
                    </th>
                    <td>
                        <textarea name="" id="wpdiscuz_reported_email_message" cols="50" rows="6">You have a new [status] comment on the post [postTitle].&lt;br&gt;
        [postName]&lt;br&gt;
        &lt;b&gt;Comment details:&lt;/b&gt;&lt;br&gt;
        Author: [userLogin] (IP: [userIP])&lt;br&gt;
        Email: [userEmail]&lt;br&gt;
        URL:&lt;br&gt;
        Comment:
        [commentContent]</textarea>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    <?php

}