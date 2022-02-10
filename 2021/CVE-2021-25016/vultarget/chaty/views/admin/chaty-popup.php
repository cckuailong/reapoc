<?php if (!defined('ABSPATH')) { exit; } ?>
<div class="chaty-popup" id="chaty-intro-popup">
    <div class="chaty-popup-box">
        <div class="chaty-popup-header">
            Welcome to Chaty &#127881;
            <button class="close-chaty-popup"><span class="dashicons dashicons-no-alt"></span></button>
            <div class="clear"></div>
        </div>
        <div class="chaty-popup-content">
            Select chat channels that you'd like to add to your store, and fill out your info. For more info visit our <a target="_blank" href="https://premio.io/help/chaty/?utm_soruce=wordpresschaty">Help Center</a> and check the video.
            <iframe width="420" height="240" src="https://www.youtube.com/embed/uaqjRp3HAqU?rel=0&start=18"></iframe>
        </div>
        <div class="chaty-popup-footer">
            <button type="button">Go to Chaty</button>
        </div>
        <input type="hidden" id="chaty_update_popup_status" value="<?php echo wp_create_nonce("chaty_update_popup_status") ?>">
    </div>
</div>