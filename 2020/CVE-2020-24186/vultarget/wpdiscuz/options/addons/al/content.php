<?php
if (!defined("ABSPATH")) {
    exit();
}

if (!$isAlExists) {
    ?>
    <div style="width:100%;">
        <h3 style="padding:5px 10px 10px 10px; margin:0px; text-align:right; border-bottom:1px solid #ddd; max-width:60%; margin:0px 0px 20px auto; font-weight:normal;">
            Addon - Advanced Likers Settings
        </h3>
        <p style="border: 1px dotted #07B290; padding: 15px; font-size: 14px; text-align: center; margin: 10px; background: #EFFFF5">
            wpDiscuz Advanced Likers addon allows to see comment likers and voters. Adds user reputation and badges based on received likes. 
            This is a demo admin page of the wpDiscuz Advanced Likers addon. You can buy this addon on gVectors Team Store.
            <br>
            <a href="https://gvectors.com/product/wpdiscuz-advanced-likers/" target="_blank" 
               style="padding: 6px 15px; background: #07B290; color: #fff; display: inline-block; margin: 15px 5px 5px 5px;">
                Addon Details and Screenshots &raquo;</a>
        </p>
        <table class="wp-list-table widefat plugins" style="margin-top:10px; border:none; opacity: 0.6;">
            <tbody>
                <tr valign="top">
                    <th style="width:50%;">
                        <span class="wpdiscuz-option-title">
                            Display avatars on quick list of likers pop-up                    </span>

                    </th>
                    <td>
                        <label>
                            <input name="" value="yes" checked=""  type="radio"> yes                    </label>
                        &nbsp;&nbsp;&nbsp;
                        <label>
                            <input name="" value="no"  type="radio"> no                    </label>

                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">
                            Real time                    </span>

                    </th>
                    <td>
                        <label>
                            <input name="" checked="" value="yes"  type="radio"> yes                    </label>
                        &nbsp;&nbsp;&nbsp;
                        <label>
                            <input name="" value="no"  type="radio"> no                    </label>

                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">
                            View all                    </span>

                    </th>
                    <td>
                        <label>
                            <input class="wv-read-more-yes" name="" checked="" value="yes"  type="radio"> yes                    </label>
                        &nbsp;&nbsp;&nbsp;
                        <label>
                            <input name="" value="no"  type="radio"> no                    </label>

                    </td>
                </tr>
                <tr class="wv-all-get-avatar" valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">
                            Display avatars on full likers list                    </span>

                    </th>
                    <td>
                        <label>
                            <input name="" class="wv-get-avatars-yes" checked="" value="yes"  type="radio"> yes                    </label>
                        &nbsp;&nbsp;&nbsp;
                        <label>
                            <input name="" value="no"  type="radio"> no                    </label>

                    </td>
                </tr>
                <tr class="wv_avatar_style" style="display: table-row;" valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">
                            Avatar size                    </span>

                    </th>
                    <td>
                        <input name="" max="120" min="24" value="64" id="avatar-size" oninput="amount.value=wv_avatar_size.value" type="range">
                        <output name="" for="avatar-size">
                            64                    </output>
                        px &nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">
                            Max number of likers on pop-up window                    </span>

                    </th>
                    <td>
                        <input class="wv-counts" max="25" min="2" name="" value="8" type="number">
                    </td>
                </tr>
                <tr valign="top">
                    <th colspan="2">
                        <div style="margin-bottom:15px;">
                            <span class="wpdiscuz-option-title">
                                Comment Author Rating and Badge                        </span>

                        </div>
                        <style>
                            .wv_level_box {
                                border: 1px solid #ccc;
                                padding: 5px;
                                width: 19%;
                                margin-right: 1%;
                                float: left;
                                box-sizing: border-box;
                            }

                            .wv_level_box i {
                                font-size: 22px;
                            }

                            .wv_level_box input[type="text"] {
                                width: 100%;
                                font-size: 11px;
                            }

                            .wv_level_box input[type="number"] {
                                width: 100%;
                                font-size: 11px;
                                box-sizing: border-box;
                            }

                            .wv_level_box div {
                                padding: 5px;
                                border-bottom: 1px dotted #ccc;
                                line-height: 25px;
                            }
                        </style>
                        <div style="width:100%;">
                            <div class="wv_level_box">
                                <div style="text-align:center;"><i class="fas fa-user" style="color:#0CD85D"></i>
                                </div>
                                <div>
                                    <input placeholder="Total Votes" name="" value="5" type="number">
                                    <p style="font-size:12px; color:#999; margin:0px;">Total count of votes</p>
                                </div>
                                <div><input placeholder="Badge Icon" name="" value="fa-user" type="text"> <label><input name="" value="1" checked=""  type="checkbox"> Enable</label>
                                </div>
                                <div><input placeholder="Custom Label" name="" value="Member" type="text"> <label><input name="" value="1" checked=""  type="checkbox"> Enable</label>
                                </div>
                            </div>
                            <div class="wv_level_box">
                                <div style="text-align:center;"><i class="fas fa-star" style="color:#E5D600"></i>
                                </div>
                                <div>
                                    <input placeholder="Total Votes" name="" value="10" type="number">
                                    <p style="font-size:12px; color:#999; margin:0px;">Total count of votes</p>
                                </div>
                                <div><input placeholder="Badge Icon" name="" value="fa-star" type="text"> <label><input name="" value="1" checked=""  type="checkbox"> Enable</label>
                                </div>
                                <div><input placeholder="Custom Label" name="" value="Active Member" type="text"> <label><input name="" value="1" checked=""  type="checkbox"> Enable</label>
                                </div>
                            </div>
                            <div class="wv_level_box">
                                <div style="text-align:center;"><i class="fas fa-certificate" style="color:#FF812D"></i>
                                </div>
                                <div>
                                    <input placeholder="Total Votes" name="" value="50" type="number">
                                    <p style="font-size:12px; color:#999; margin:0px;">Total count of votes</p>
                                </div>
                                <div><input placeholder="Badge Icon" name="" value="fa-certificate" type="text"> <label><input name="" value="1" checked=""  type="checkbox"> Enable</label>
                                </div>
                                <div><input placeholder="Custom Label" name="" value="Trusted Member" type="text"> <label><input name="" value="1" checked=""  type="checkbox"> Enable</label>
                                </div>
                            </div>
                            <div class="wv_level_box">
                                <div style="text-align:center;"><i class="fas fa-shield-alt" style="color:#43A6DF"></i>
                                </div>
                                <div>
                                    <input placeholder="Total Votes" name="" value="100" type="number">
                                    <p style="font-size:12px; color:#999; margin:0px;">Total count of votes</p>
                                </div>
                                <div><input placeholder="Badge Icon" name="" value="fa-shield-alt" type="text"> <label><input name="" value="1" checked=""  type="checkbox"> Enable</label>
                                </div>
                                <div><input placeholder="Custom Label" name="" value="Noble Member" type="text"> <label><input name="" value="1" checked=""  type="checkbox"> Enable</label>
                                </div>
                            </div>
                            <div class="wv_level_box">
                                <div style="text-align:center;"><i class="fas fa-trophy" style="color:#E04A47;"></i>
                                </div>
                                <div>
                                    <input placeholder="Total Votes" name="" value="500" type="number">
                                    <p style="font-size:12px; color:#999; margin:0px;">Total count of votes</p>
                                </div>
                                <div><input placeholder="Badge Icon" name="" value="fa-trophy" type="text"> <label><input name="" value="1" checked=""  type="checkbox"> Enable</label>
                                </div>
                                <div><input placeholder="Custom Label" name="" value="Famed Member" type="text"> <label><input name="" value="1" checked=""  type="checkbox"> Enable</label>
                                </div>
                            </div>
                            <div style="clear:both;"></div>
                        </div>

                    </th>
                </tr>
                <tr sqope="row">
                    <td colspan="2">
                        <h3 style="padding:5px 10px 10px 10px; margin:0px;">Background and Colors</h3>
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">
                            Likers pop-up background color                    </span>

                    </th>
                    <td>
                        <input class="wpdiscuz-color-picker regular-text" value="#FDFDFD" id="voters_background_color" name="" placeholder="Example: #00FF00" style="background-color: rgb(253, 253, 253); color: rgb(34, 34, 34);" type="text">
                        <!--<label-->
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">
                            Likers pop-up border color                    </span>

                    </th>
                    <td>
                        <input class="wpdiscuz-color-picker regular-text" value="#000" id="voters_border_color" name="" placeholder="Example: #00FF00" style="background-color: rgb(0, 0, 0); color: rgb(221, 221, 221);" type="text">
                    </td>
                </tr>
                <!--
                        <tr valign="top">
                                <th >
                                        <span class="wpdiscuz-option-title">
            Vote like default color                                        </span>
                                </th>
                                <td>
                                                    <input type="text" class="wpdiscuz-color-picker regular-text" value="#666" id="voters_background_color" name="" placeholder="Example: #00FF00">
                                </td>
                        </tr>
                        <tr valign="top">
                                <th >
                                        <span class="wpdiscuz-option-title">
            Vote dislike default color                                        </span>
                                </th>
                                <td>
                                                    <input type="text" class="wpdiscuz-color-picker regular-text" value="#666" id="voters_background_color" name="" placeholder="Example: #00FF00">
                                </td>
                        </tr>
                -->
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">Like icon color</span>
                    </th>
                    <td>
                        <input class="wpdiscuz-color-picker regular-text" value="#00bc74" id="voters_background_color" name="" placeholder="Example: #00FF00" style="background-color: rgb(0, 188, 116); color: rgb(34, 34, 34);" type="text">
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">Dislike icon color</span>
                    </th>
                    <td>
                        <input class="wpdiscuz-color-picker regular-text" value="#ff5959" id="voters_background_color" name="" placeholder="Example: #00FF00" style="background-color: rgb(255, 89, 89); color: rgb(34, 34, 34);" type="text">
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">Vote result like color</span>
                    </th>
                    <td>
                        <input class="wpdiscuz-color-picker regular-text" value="#FFF" id="voters_background_color" name="" placeholder="Example: #00FF00" style="background-color: rgb(255, 255, 255); color: rgb(34, 34, 34);" type="text">
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <span class="wpdiscuz-option-title">Vote result dislike color</span>
                    </th>
                    <td>
                        <input class="wpdiscuz-color-picker regular-text" value="#FFF" id="voters_background_color" name="" placeholder="Example: #00FF00" style="background-color: rgb(255, 255, 255); color: rgb(34, 34, 34);" type="text">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}