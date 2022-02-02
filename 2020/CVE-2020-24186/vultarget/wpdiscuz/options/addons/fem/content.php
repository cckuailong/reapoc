<?php

if (!defined("ABSPATH")) {
    exit();
}

if (!$isFemExists) {
    ?>
    <style>
        .fa-toggle-on {
            color: #07B290;
        }

        .fa-toggle-off {
            color: #f00;
        }

        .fa-toggle-off, .fa-toggle-on {
            margin-left: 10px;
            font-size: 28px !important;
        }
    </style>

    <div style="width:100%;">
        <h3 style="padding:5px 10px 10px 10px; margin:0px; text-align:right; border-bottom:1px solid #ddd; max-width:60%; margin:0px 0px 20px auto; font-weight:normal;">
            Addon - Frontend Moderation Settings
        </h3>
        <p style="border: 1px dotted #07B290; padding: 15px; font-size: 14px; text-align: center; margin: 10px; background: #EFFFF5">
            wpDiscuz Frontend Moderation is an all in one powerful yet simple admin toolkit to moderate comments on front-end.
            This is a demo admin page of the wpDiscuz Frontend Moderation addon. You can buy this addon on gVectors Team Store.
            <br>
            <a href="https://gvectors.com/product/wpdiscuz-frontend-moderation/" target="_blank" 
               style="padding: 6px 15px; background: #07B290; color: #fff; display: inline-block; margin: 15px 5px 5px 5px;">
                Addon Details and Screenshots &raquo;</a>
        </p>
        <table class="wp-list-table widefat plugins" style="margin-top:10px; border:none; opacity: 0.6;">
            <tbody>            
                <tr valign="top">
                    <th style="width:50%;">
                        <span class="wpdiscuz-option-title">Allow Users Delete Comment</span>
                    </th>
                    <td>
                        <label>
                            <i class="fas fa-toggle-off" title="Off"></i>
                            <input style="display:none;" value="yes" id="mod_user_can_delete" name=""  type="checkbox">
                        </label>              

                    </td>                
                </tr>
                <tr scope="row">
                    <th colspan="2">
                        <h3 style="font-weight:normal; margin:0px; padding:10px 0px">Background and Colors</h3>
                    </th>
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">Moderation Form Background Color</span>
                    </th>
                    <td>
                        <input class="wpdiscuz-color-picker regular-text" style="padding: 3px 5px; background-color: rgb(255, 255, 255); color: rgb(34, 34, 34);" value="#FFFFFF" id="mod_form_bg_color" name="" placeholder="Example: #00FF00"  type="text">                    
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">Moderation Form Border Color</span>
                    </th>
                    <td>
                        <input class="wpdiscuz-color-picker regular-text" style="padding: 3px 5px; background-color: rgb(51, 51, 51); color: rgb(221, 221, 221);" value="#333333" id="mod_form_border_color" name="" placeholder="Example: #00FF00"  type="text">
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">Moderation Separator Color</span>
                    </th>
                    <td>
                        <input class="wpdiscuz-color-picker regular-text" style="padding: 3px 5px; background-color: rgb(221, 221, 221); color: rgb(34, 34, 34);" value="#DDDDDD" id="mod_text_bottom_line_color" name="" placeholder="Example: #00FF00"  type="text">
                    </td>
                </tr>
                <tr scope="row">
                    <th colspan="2">
                        <h3 style="font-weight:normal; margin:0px; padding:10px 0px">Front-end Phrases</h3>
                    </th>
                </tr>            
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_moderate"><span class="wpdiscuz-option-title">Moderate</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Moderate" id="mod_phrase_moderate" name=""  type="text">
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_approve"><span class="wpdiscuz-option-title">Approve</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Approve" id="mod_phrase_approve" name=""  type="text">
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_unapprove"><span class="wpdiscuz-option-title">Unapprove</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Unapprove" id="mod_phrase_unapprove" name=""  type="text">
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_trash"><span class="wpdiscuz-option-title">Trash</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Trash" id="mod_phrase_trash" name=""  type="text">
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_spam"><span class="wpdiscuz-option-title">Spam</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Spam" id="mod_phrase_spam" name=""  type="text">
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_email"><span class="wpdiscuz-option-title">Email</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Email" id="mod_phrase_email" name=""  type="text">
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_move"><span class="wpdiscuz-option-title">Move</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Move" id="mod_phrase_move" name=""  type="text">
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_blacklist"><span class="wpdiscuz-option-title">Blacklist</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Blacklist" id="mod_phrase_blacklist" name=""  type="text">
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_delete"><span class="wpdiscuz-option-title">Delete</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Delete" id="mod_phrase_delete" name=""  type="text">
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_restore"><span class="wpdiscuz-option-title">Restore</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Restore" id="mod_phrase_restore" name=""  type="text">
                    </td>                
                </tr>
                <tr valign="top">
                    <th>
                        <label for="mod_phrase_not_spam"><span class="wpdiscuz-option-title">Not Spam</span></label>
                    </th>
                    <td>
                        <input style="padding: 3px 5px; width: 100%;" value="Not Spam" id="mod_phrase_not_spam" name=""  type="text">
                    </td>                
                </tr>

            </tbody>
        </table>
    </div>
    <?php

}