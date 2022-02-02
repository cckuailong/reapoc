<?php
if (!defined("ABSPATH")) {
    exit();
}

if (!$isMuExists) {
    ?>
    <div style="width:100%;">
        <h3 style="padding:5px 10px 10px 10px; margin:0px; text-align:right; border-bottom:1px solid #ddd; max-width:60%; margin:0px 0px 20px auto; font-weight:normal;">
            Addon - Media Uploader Settings
        </h3>
        <p style="border: 1px dotted #07B290; padding: 15px; font-size: 14px; text-align: center; margin: 10px; background: #EFFFF5">
            wpDiscuz Media Uploader is an extended comment attachment system. Allows to upload images, videos, audios and other file types. 
            This is a demo admin page of the wpDiscuz Media Uploader addon. You can buy this addon on gVectors Team Store.
            <br>
            <a href="https://gvectors.com/product/wpdiscuz-media-uploader/" target="_blank" 
               style="padding: 6px 15px; background: #07B290; color: #fff; display: inline-block; margin: 15px 5px 5px 5px;">
                Addon Details and Screenshots &raquo;</a>
        </p>
        <table class="widefat wpdiscuz-mu" style="margin-top:10px; border:none; width:100%; opacity: 0.6">
            <tbody>
                <tr scope="row">
                    <th valign="top" style="width:42%;">Post types supports uploading:</th>
                    <td>
                        <div style="float:left; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                            <input type="checkbox" checked='checked' value="post" name="" id="wmupost" style="margin:0px; vertical-align: middle;"/>
                            <label for="wmupost" style="white-space:nowrap; font-size:13px;">post</label>
                        </div>
                        <div style="float:left; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                            <input type="checkbox" checked='checked' value="page" name="" id="wmupage" style="margin:0px; vertical-align: middle;"/>
                            <label for="wmupage" style="white-space:nowrap; font-size:13px;">page</label>
                        </div>
                        <div style="float:left; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                            <input type="checkbox" checked='checked' value="attachment" name="" id="wmuattachment" style="margin:0px; vertical-align: middle;"/>
                            <label for="wmuattachment" style="white-space:nowrap; font-size:13px;">attachment</label>
                        </div>

                    </td>
                </tr>
                <tr scope="row">
                    <th valign="top" style="width:42%;">User roles allowed to upload files:</th>
                    <td>
                        <div style="float:left; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                            <input type="checkbox" checked='checked' value="administrator" name="" id="wmuadministrator" style="margin:0px; vertical-align: middle;"/>
                            <label for="wmuadministrator" style="white-space:nowrap; font-size:13px;">Administrator</label>
                        </div>
                        <div style="float:left; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                            <input type="checkbox" checked='checked' value="editor" name="" id="wmueditor" style="margin:0px; vertical-align: middle;"/>
                            <label for="wmueditor" style="white-space:nowrap; font-size:13px;">Editor</label>
                        </div>
                        <div style="float:left; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                            <input type="checkbox" checked='checked' value="author" name="" id="wmuauthor" style="margin:0px; vertical-align: middle;"/>
                            <label for="wmuauthor" style="white-space:nowrap; font-size:13px;">Author</label>
                        </div>
                        <div style="float:left; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                            <input type="checkbox" checked='checked' value="contributor" name="" id="wmucontributor" style="margin:0px; vertical-align: middle;"/>
                            <label for="wmucontributor" style="white-space:nowrap; font-size:13px;">Contributor</label>
                        </div>
                        <div style="float:left; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                            <input type="checkbox" checked='checked' value="subscriber" name="" id="wmusubscriber" style="margin:0px; vertical-align: middle;"/>
                            <label for="wmusubscriber" style="white-space:nowrap; font-size:13px;">Subscriber</label>
                        </div>
                        <div style="float:left; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                            <input type="checkbox" value="customer" name="" id="wmucustomer" style="margin:0px; vertical-align: middle;"/>
                            <label for="wmucustomer" style="white-space:nowrap; font-size:13px;">Customer</label>
                        </div>
                        <div style="float:left; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                            <input type="checkbox" value="shop_manager" name="" id="wmushop_manager" style="margin:0px; vertical-align: middle;"/>
                            <label for="wmushop_manager" style="white-space:nowrap; font-size:13px;">Shop manager</label>
                        </div>

                    </td>
                </tr>
                <tr scope="row">
                    <th><label for="wmuIsShowFilesDashboard">Show uploaded files in dashboard comments</label>
                    </th>
                    <td><input type="checkbox" checked='checked' value="1" name="" id="wmuIsShowFilesDashboard"/>
                    </td>
                </tr>
                <tr scope="row">
                    <th><label for="wmuIsGuestAllowed">Allow guests to upload files</label>
                    </th>
                    <td><input type="checkbox" checked='checked' value="1" name="" id="wmuIsGuestAllowed"/>
                    </td>
                </tr>
                <tr scope="row">
                    <th><label for="wmuIsImagesAllowed">Enable Image uploader</label>
                    </th>
                    <td><input type="checkbox" checked='checked' value="1" name="" id="wmuIsImagesAllowed"/> &nbsp; <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/options/addons/images/image.png") ?>"/>
                    </td>
                </tr>
                <tr scope="row">
                    <th><label for="wmuIsVideosAllowed">Enable Video and Audio uploader</label>
                    </th>
                    <td><input type="checkbox" checked='checked' value="1" name="" id="wmuIsVideosAllowed"/> &nbsp; <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/options/addons/images/video.png") ?>"/>
                    </td>
                </tr>
                <tr scope="row">
                    <th>
                        <label for="wmuIsFilesAllowed">Enable other files uploader</label>
                        <p style="font-size:13px; color:#999999; width:98%; padding-left:0px; margin-left:0px;">This uploader allows to attach non-image and non-video files like zip, doc, pdf, txt...</p>
                    </th>
                    <td><input type="checkbox" checked='checked' value="1" name="" id="wmuIsFilesAllowed"/> &nbsp; <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/options/addons/images/file.png") ?>"/>
                    </td>
                </tr>
                <tr scope="row">
                    <th>
                        <label for="wmuIsEmbed">Allow media embedding function</label>
                        <p style="font-size:13px; color:#999999; width:98%; padding-left:0px; margin-left:0px;">This is the auto-embed function which converts e.g. <strong>YouTube</strong> video URL to Embedded Player and so on...</p>
                    </th>
                    <td><input type="checkbox" checked='checked' value="1" name="" id="wmuIsEmbed"/> &nbsp;&nbsp; <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/options/addons/images/html5-youtub-player.png") ?>" style="vertical-align:middle; height:65px;" title="Player Screenshot"/>
                    </td>
                </tr>
                <tr scope="row" class="tr-wmuIsEmbedContent">
                    <th>
                        <label for="wmuIsEmbedContent">Allow content embedding function</label>
                        <p style="font-size:13px; color:#999999; width:98%; padding-left:0px; margin-left:0px;">This is the auto-embed function which converts site content</p>
                    </th>
                    <td><input type="checkbox" value="1" name="" id="wmuIsEmbedContent"/> &nbsp;&nbsp; <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/options/addons/images/embedded-content.png") ?>" style="vertical-align:middle; height:250px;" title="Embedded Content Screenshot"/>
                    </td>
                </tr>
                <tr scope="row">
                    <th>
                        <label for="wmuIsHtml5Video">Enable HTML5 video player</label>
                        <p style="font-size:13px; color:#999999; width:98%; padding-left:0px; margin-left:0px;">Uploaded video files will be added as a download link under comment text. However if file format is .mp4, .webm or .ogg, it'll convert link to HTML5 video player.</p>
                    </th>
                    <td><input type="checkbox" checked='checked' value="1" name="" id="wmuIsHtml5Video"/> &nbsp; <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/options/addons/images/html5-video-player.png") ?>" style="vertical-align:middle; height:70px;" title="Player Screenshot"/>
                    </td>
                    </td>
                </tr>
                <tr scope="row">
                    <th><label for="wmuIsHtml5Audio">Enable HTML5 audio player</label>
                    </th>
                    <td><input type="checkbox" checked='checked' value="1" name="" id="wmuIsHtml5Audio"/> &nbsp; <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/options/addons/images/html5-audio-player.png") ?>" style="vertical-align:middle;" title="Player Screenshot"/>
                    </td>
                </tr>
                <tr scope="row">
                    <th><label for="wmuIsLightbox">Enable Lightbox for images</label>
                    </th>
                    <td><input type="checkbox" checked='checked' value="1" name="" id="wmuIsLightbox"/>
                    </td>
                </tr>
                <tr scope="row">
                    <th><label>File attachment mode</label>
                    </th>
                    <td>
                        <fieldset>
                            <label for="wmuAttachSingle"><input  type="radio" value="0" id="wmuAttachSingle" class="wmuAttachSingle wmuAttachMethod" name="" />&nbsp;&nbsp;<span>Single</span></label>
                            <label for="wmuAttachMultiple"><input  checked='checked' type="radio" value="1" id="wmuAttachMultiple" class="wmuAttachMultiple wmuAttachMethod" name="" />&nbsp;&nbsp;<span>Multiple</span></label>
                        </fieldset>
                    </td>
                </tr>
                <tr scope="row" class="tr-wmuMaxFileCount">
                    <th><label for="wmuMaxFileCount">Max number of files per type</label>
                    </th>
                    <td><input type="number" value="3" name="" id="wmuMaxFileCount" class="wmu-number"/>
                    </td>
                </tr>
                <tr scope="row">
                    <th>
                        <label for="wmuMaxFileSize">Max allowed files size</label>
                        <p style="font-size:13px; color:#999999; width:98%; padding-left:0px; margin-left:0px;">You can not set this value more than "upload_max_filesize" and "post_max_size". If you want to increase server parameters please contact to your hosting service support.</p>
                    </th>
                    <td>
                        <input type="number" value="30" name="" id="wmuMaxFileSize" class="wmu-number"/>
                        <p style="padding-top:5px;">
                            Server "upload_max_filesize" is 1000M<br/>Server "post_max_size" is 1000M </p>
                    </td>
                </tr>
                <tr scope="row">
                    <th><label for="wmuVideoWidth">Video player sizes</label>
                    </th>
                    <td>
                        <input type="number" value="320" name="" id="wmuVideoWidth" class="wmu-number"/><span> width (px) </span>
                        <input type="number" value="200" name="" id="wmuVideoHeight" class="wmu-number"/><span> height (px) </span>
                    </td>
                </tr>
                <tr scope="row">
                    <th><label for="wmuEmbedVideoWidth">Embed Video player sizes</label>
                    </th>
                    <td>
                        <input type="number" value="400" name="" id="wmuEmbedVideoWidth" class="wmu-number"/><span> width (px) </span>
                        <input type="number" value="300" name="" id="wmuEmbedVideoHeight" class="wmu-number"/><span> height (px) </span>
                    </td>
                </tr>
                <tr scope="row">
                    <th><label for="wmuSingleImageWidth">Single Image sizes on comment</label>
                    </th>
                    <td>
                        <input type="number" value="320" name="" id="wmuSingleImageWidth" class="wmu-number"/><span> width (px) </span>
                        <input type="number" value="200" name="" id="wmuSingleImageHeight" class="wmu-number"/><span> height (px) </span>
                    </td>
                </tr>
                <tr scope="row">
                    <th><label for="wmuImageWidth">Image sizes on comment</label>
                    </th>
                    <td>
                        <input type="number" value="90" name="" id="wmuImageWidth" class="wmu-number"/><span> width (px) </span>
                        <input type="number" value="90" name="" id="wmuImageHeight" class="wmu-number"/><span> height (px) </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}