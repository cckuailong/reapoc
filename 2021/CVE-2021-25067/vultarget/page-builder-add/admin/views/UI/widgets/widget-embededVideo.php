<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
    <label>Video Type</label>
    <select class="widgetEvidVideoType" data-optname="widgetEvidVideoType" >
        <option value="youtube">Youtube</option>
        <option value="vimeo">Vimeo</option>
    </select>
    <br><br><hr><br>
    <label>Video Link</label>
    <input type="url" class="widgetEvidVideoLink" data-optname="widgetEvidVideoLink" >
    <br><br><hr><br><br>
    <h4>Video Options</h4>
    <br>
    <label>Autoplay</label>
    <select class="widgetEvidVideoAutoplay" data-optname="widgetEvidVideoAutoplay" >
        <option value="true">Yes</option>
        <option value="false">No</option>
    </select>
    <br><br><hr><br>
    <label>Player Controls</label>
    <select class="widgetEvidVideoPlayerControls" data-optname="widgetEvidVideoPlayerControls" >
        <option value="true">Yes</option>
        <option value="false">No</option>
    </select>
    <br><br><hr><br>
    <label>Video Title</label>
    <select class="widgetEvidVideoTitle" data-optname="widgetEvidVideoTitle" >
        <option value="true">Yes</option>
        <option value="false">No</option>
    </select>
    <br><br><hr><br>
    <label>Suggested Content</label>
    <select class="widgetEvidVideoSuggested" data-optname="widgetEvidVideoSuggested" >
        <option value="true">Yes</option>
        <option value="false">No</option>
    </select>
    <br><br><hr><br><br>
    <h4>Thumbnail Image</h4>
    <br>
    <label>Thumbnail Image</label>
    <select class="widgetEvidImageOverlay" data-optname="widgetEvidImageOverlay" >
        <option value="true">Yes</option>
        <option value="false">No</option>
    </select> 
    <br><br><hr><br>
    <label>Select Image :</label>
    <input id="image_location66" type="text" class="widgetEvidImageUrl upload_image_button66"  name='lpp_add_img_1' value='' placeholder='Insert URL here' style="width:40%;"  data-optname="widgetEvidImageUrl" />
    <label></label>
    <input id="image_location66" type="button" class="upload_bg" data-id="66" value="Upload" />
    <br><br><br><br><hr><br>
    <label> Play Icon </label>
    <select class="widgetEvidImageIcon" data-optname="widgetEvidImageIcon">
        <option value="block">Yes</option>
        <option value="none">No</option>
    </select> 
    <br><br><hr><br>
    <label> Icon Color </label>
    <input type="text" class="color-picker_btn_two widgetEvidImageIconColor" id="widgetEvidImageIconColor" value="#333" data-optname="widgetEvidImageIconColor">
    <br><br><hr><br><br><br><br>

</div>