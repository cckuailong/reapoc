<?php

if (!defined("ABSPATH")) {
    exit();
}

if (!$isCaiExists) {
    ?>
    <style>
        .wcai-options * {
            box-sizing: border-box;
        }

        .wcai-options td, 
        .wcai-options th {
            padding:2px 5px;
        }

        .wcai-options .wcai-chk-group, .wcai-options .wcai-input-group{
            float: left;
            display: inline-block;
            padding: 5px 0;
            margin-left: 3px;
            overflow: hidden;
            line-height: 20px;
        }
        .wcai-options .wcai-chk-switch {
            display: none;
            margin: 0;
            vertical-align: middle;
        }
        .wcai-options .wcai-chk-switch-label {
            /*margin-left:5px;*/
            white-space:nowrap;
            padding: 3px 6px;
            color: #fff;
            border: 1px solid #aeaeae;
            border-radius: 3px;
            width: 100%;    
            text-align: center;
            display: block;
            cursor: pointer;
        }
        .wcai-options .wcai-chk-switch-on {
            background: #00b38f;
        }
        .wcai-options .wcai-chk-switch-on:not(.wcai-actions-disabled):hover {
            background: #00c49c;
        }
        .wcai-options .wcai-chk-switch-off {
            background: #ca3c3c;
        }
        .wcai-options .wcai-chk-switch-off:not(.wcai-actions-disabled):hover {
            background: #d44949;
        }

        .wcai-options .wcai-input-field {
            margin: 0;
            padding: 3px 5px;
        }
        .wcai-options .w1{width:1%;}.wcai-options .w2{width:2%;}.wcai-options .w3{width:3%;}.wcai-options .w5{width:4%;}.wcai-options .w5{width:5%;}.wcai-options .w6{width:6%;}.wcai-options .w7{width:7%;}.wcai-options .w8{width:8%;}.wcai-options .w9{width:9%;}.wcai-options .w10{width:10%;}.wcai-options .w11{width:11%;}.wcai-options .w12{width:12%;}.wcai-options .w13{width:13%;}.wcai-options .w14{width:14%;}.wcai-options .w15{width:15%;}.wcai-options .w16{width:16%;}.wcai-options .w17{width:17%;}.wcai-options .w18{width:19%;}.wcai-options .w20{width:20%;}.wcai-options .w21{width:21%;}.wcai-options .w22{width:22%;}.wcai-options .w23{width:23%;}.wcai-options .w24{width:24%;}.wcai-options .w25{width:25%;}.wcai-options .w26{width:26%;}.wcai-options .w27{width:27%;}.wcai-options .w28{width:28%;}.wcai-options .w29{width:29%;}.wcai-options .w30{width:30%;}.wcai-options .w31{width:31%;}.wcai-options .w32{width:32%;}.wcai-options .w33{width:33%;}.wcai-options .w34{width:34%;}.wcai-options .w35{width:35%;}.wcai-options .w36{width:36%;}.wcai-options .w37{width:37%;}.wcai-options .w38{width:38%;}.wcai-options .w39{width:39%;}.wcai-options .w40{width:40%;}.wcai-options .w41{width:41%;}.wcai-options .w42{width:42%;}.wcai-options .w43{width:43%;}.wcai-options .w44{width:44%;}.wcai-options .w45{width:45%;}.wcai-options .w46{width:46%;}.wcai-options .w47{width:47%;}.wcai-options .w48{width:48%;}.wcai-options .w49{width:49%;}.wcai-options .w50{width:50%;}.wcai-options .w51{width:51%;}.wcai-options .w52{width:52%;}.wcai-options .w53{width:53%;}.wcai-options .w54{width:54%;}.wcai-options .w55{width:55%;}.wcai-options .w56{width:56%;}.wcai-options .w57{width:57%;}.wcai-options .w58{width:58%;}.wcai-options .w59{width:59%;}.wcai-options .w60{width:60%;}.wcai-options .w61{width:61%;}.wcai-options .w62{width:62%;}.wcai-options .w63{width:63%;}.wcai-options .w64{width:64%;}.wcai-options .w65{width:65%;}.wcai-options .w66{width:66%;}.wcai-options .w67{width:67%;}.wcai-options .w68{width:68%;}.wcai-options .w69{width:69%;}.wcai-options .w70{width:70%;}.wcai-options .w71{width:71%;}.wcai-options .w72{width:72%;}.wcai-options .w73{width:73%;}.wcai-options .w74{width:74%;}.wcai-options .w75{width:75%;}.wcai-options .w76{width:76%;}.wcai-options .w77{width:77%;}.wcai-options .w78{width:78%;}.wcai-options .w79{width:79%;}.wcai-options .w80{width:80%;}.wcai-options .w81{width:81%;}.wcai-options .w82{width:82%;}.wcai-options .w83{width:83%;}.wcai-options .w84{width:84%;}.wcai-options .w85{width:85%;}.wcai-options .w86{width:86%;}.wcai-options .w87{width:87%;}.wcai-options .w88{width:88%;}.wcai-options .w89{width:89%;}.wcai-options .w90{width:90%;}.wcai-options .w91{width:91%;}.wcai-options .w92{width:92%;}.wcai-options .w93{width:93%;}.wcai-options .w94{width:94%;}.wcai-options .w95{width:95%;}.wcai-options .w96{width:96%;}.wcai-options .w97{width:97%;}.wcai-options .w98{width:98%;}.wcai-options .w99{width:99%;}.wcai-options .w100{width:100%;}
        .wcai-options .wcai-description {color: #858585; margin: 2px 0px; font-weight: normal;}
        .wcai-options .wcai-note{color: #c74a4a;}
        .wcai-options .wcai-clearfix{clear: both;}
        .wcai-options .wcai-show{display: block;}
        .wcai-options .wcai-show-tr{display: table-row;}
        .wcai-options .wcai-hide{display: none;}
    </style>
    <div style="width:100%;">
        <h3 style="padding:5px 10px 10px 10px; margin:0px; text-align:right; border-bottom:1px solid #ddd; max-width:60%; margin:0px 0px 20px auto; font-weight:normal;">
            Addon - Comment Author Info
        </h3>
        <p style="border: 1px dotted #07B290; padding: 15px; font-size: 14px; text-align: center; margin: 10px; background: #EFFFF5">
            wpDiscuz Comment Author Info addon displays an extended information about comment author with Profile, Activity, Votes and Subscriptions Tabs on pop-up window.
            This is a demo admin page of the wpDiscuz Comment Author Info addon. You can buy this addon on gVectors Team Store.
            <br>
            <a href="https://gvectors.com/product/wpdiscuz-comment-author-info/" target="_blank" 
               style="padding: 6px 15px; background: #07B290; color: #fff; display: inline-block; margin: 15px 5px 5px 5px;">
                Addon Details and Screenshots &raquo;</a>
        </p>
        <table class="widefat wcai-options" style="margin-top:10px; border:none; width:100%; opacity: 0.6">
            <tbody>
                <tr>
                    <th class="w48">Comment Author Tabs</th>
                    <td>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="profile" name="" id="wcai-profile" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-profile" class="wcai-chk-switch-label wcai-chk-switch-on">Profile</label>
                        </div>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="activity" name="" id="wcai-activity" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-activity" class="wcai-chk-switch-label wcai-chk-switch-on">Activity</label>
                        </div>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="votes" name="" id="wcai-votes" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-votes" class="wcai-chk-switch-label wcai-chk-switch-on">Votes</label>
                        </div>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="subscriptions" name="" id="wcai-subscriptions" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-subscriptions" class="wcai-chk-switch-label wcai-chk-switch-on">Subscriptions</label>
                        </div>
                        <div class="wcai-clearfix"></div>
                    </td>
                </tr>
                <tr>
                    <th class="w48">Display comment author information for user roles</th>
                    <td>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="administrator" name="" id="wcai-administrator" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-administrator" class="wcai-chk-switch-label wcai-chk-switch-on">Administrator</label>
                        </div>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="editor" name="" id="wcai-editor" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-editor" class="wcai-chk-switch-label wcai-chk-switch-on">Editor</label>
                        </div>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="author" name="" id="wcai-author" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-author" class="wcai-chk-switch-label wcai-chk-switch-on">Author</label>
                        </div>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="contributor" name="" id="wcai-contributor" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-contributor" class="wcai-chk-switch-label wcai-chk-switch-on">Contributor</label>
                        </div>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="subscriber" name="" id="wcai-subscriber" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-subscriber" class="wcai-chk-switch-label wcai-chk-switch-on">Subscriber</label>
                        </div>
                        <div class="wcai-chk-group w24">
                            <input value="customer" name="" id="wcai-customer" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-customer" class="wcai-chk-switch-label wcai-chk-switch-off">Customer</label>
                        </div>
                        <div class="wcai-chk-group w24">
                            <input value="shop_manager" name="" id="wcai-shop_manager" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-shop_manager" class="wcai-chk-switch-label wcai-chk-switch-off">Shop manager</label>
                        </div>
                        <div class="wcai-clearfix"></div>
                    </td>
                </tr>
                <tr>
                    <th class="w48"><label for="wcai-showForGuests">Display comment author information for guests</label>
                    </th>
                    <td>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="1" name="" id="wcai-showForGuests" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-showForGuests" class="wcai-chk-switch-label wcai-chk-single wcai-chk-switch-on">Yes</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w48"><label for="wcai-shortInfoOnAvatarHover">Display comment author short information on avatar hover</label>
                    </th>
                    <td>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="1" name="" id="wcai-shortInfoOnAvatarHover" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-shortInfoOnAvatarHover" class="wcai-chk-switch-label wcai-chk-single wcai-chk-switch-on">Yes</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w48"><label for="wcai-fullInfoOnUsernameClick">Display comment author full information on username click</label>
                    </th>
                    <td>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="1" name="" id="wcai-fullInfoOnUsernameClick" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-fullInfoOnUsernameClick" class="wcai-chk-switch-label wcai-chk-single wcai-chk-switch-on">Yes</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w48">
                        <h3>Profile Information</h3>
                    </th>
                    <td></td>
                </tr>
                <tr>
                    <th class="w48"><label for="wcai-profileShowDisplayName">Display Name</label>
                    </th>
                    <td>
                        <div class="wcai-chk-group  w24">
                            <input checked="checked" value="1" name="" id="wcai-profileShowDisplayName" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-profileShowDisplayName" class="wcai-chk-switch-label wcai-chk-single wcai-chk-switch-on">Yes</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w48">
                        <label for="wcai-profileShowNickName">Display Nickname</label>
                    </th>
                    <td>
                        <div class="wcai-chk-group  w24">
                            <input checked="checked" value="1" name="" id="wcai-profileShowNickName" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-profileShowNickName" class="wcai-chk-switch-label wcai-chk-single wcai-chk-switch-on">Yes</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w48"><label for="wcai-profileShowBio">Display Comment Author Biography</label>
                    </th>
                    <td>
                        <div class="wcai-chk-group  w24">
                            <input checked="checked" value="1" name="" id="wcai-profileShowBio" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-profileShowBio" class="wcai-chk-switch-label wcai-chk-single wcai-chk-switch-on">Yes</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w48"><label for="wcai-profileShowWebUrl">Display Website</label>
                    </th>
                    <td>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="1" name="" id="wcai-profileShowWebUrl" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-profileShowWebUrl" class="wcai-chk-switch-label wcai-chk-single wcai-chk-switch-on">Yes</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w48">
                        <label for="wcai-profileShowStatistics">Display Comment Statistics</label>
                        <p class="howto wcai-description">This information is available for admins only</p>
                    </th>
                    <td>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="1" name="" id="wcai-profileShowStatistics" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-profileShowStatistics" class="wcai-chk-switch-label wcai-chk-single wcai-chk-switch-on">Yes</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w48">
                        <label for="wcai-profileShowMycredData">Display MyCRED Information</label>
                        <p class="howto wcai-description">User Badges and Rank</p>
                    </th>
                    <td>
                        <div class="wcai-chk-group w24">
                            <input checked="checked" value="1" name="" id="wcai-profileShowMycredData" class="wcai-chk-switch"  type="checkbox">
                            <label for="wcai-profileShowMycredData" class="wcai-chk-switch-label wcai-chk-single wcai-chk-switch-on">Yes</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w48"><label for="wcai-perPage">Pagination items per page</label>
                    </th>
                    <td>
                        <div class="wcai-input-group">
                            <input value="5" name="" id="wcai-perPage" class="wcai-input-field"  type="number">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:right; padding:5px;">
                        <div class="wcai-chk-group w10" style="float:right;">
                            <input class="wcai-chk-switch" disabled="disabled"  type="checkbox">
                            <label class="wcai-chk-switch-label wcai-chk-switch-on wcai-actions-disabled">Enabled</label>
                        </div>
                        <div class="wcai-chk-group w10" style="float:right;">
                            <input class="wcai-chk-switch" disabled="disabled"  type="checkbox">
                            <label class="wcai-chk-switch-label wcai-chk-switch-off wcai-actions-disabled">Disabled</label>
                        </div>
                    </td>
                </tr>


            </tbody>
        </table>
    </div>
    <?php

}