<?php
/** no direct access **/

wp_enqueue_style('mec-font-icon', $this->main->asset('css/iconfonts.css'));
wp_enqueue_style( 'wp-color-picker');
wp_enqueue_script( 'wp-color-picker');
$settings = $this->main->get_settings();
$archive_skins = $this->main->get_archive_skins();
?>
<style>
.m-e-calendar_page_MEC-wizard {
    background: #F5F5F5;
}
.mec-wizard-wrap {
    background: #FFFFFF;
    padding: 40px 130px;
    max-width: 520px;
    margin:100px auto 0;
    border-radius: 22px;
    box-shadow: 0 3px 40px rgba(0,0,0,0.1);
    position: relative;
}
.mec-wizard-wrap > h3 {
    color: #393C40;
    text-align: center;
    font-weight: 400;
    font-size:21px;
    margin-top: 5px;
    margin-bottom: 41px;
}
.mec-wizard-wrap .mec-wizard-starter-video a {
    padding: 33px 20px 19px;
    margin-top:40px;
    display: block;
    text-decoration: none;
}

.mec-wizard-button-style {
    color: #52595E;
    text-align: center;
    font-weight: 400;
    font-size: 13px;
    border-radius: 11px;
    box-shadow: 0 2px 0 rgb(0 0 0 / 2%);
    border: 1px solid #DFE1E5;
    transition: all 0.2s ease;
}
.mec-wizard-import-events {
    float: left;
}
.mec-wizard-import-shortcodes {
    float: right;
}
.mec-wizard-import-box {
    border: 1px solid #DFE1E5;
    border-radius: 11px;
    padding: 22px 18px;
    width: 245px;
    font-size: 13px;
    background: #fff;
    text-align: left;
    color: #52595E;
}
.mec-wizard-import-dummy {
    margin-top: 40px;
}
.mec-wizard-import-box svg {
    vertical-align: middle;
    margin-right: 10px;
}
.mec-wizard-button-style:hover {
    border-color: #C3EAF3;
    color: #00B0DD;
    box-shadow: 0 2px 0 rgb(0 176 221 / 5%);
    cursor: pointer;
}
.mec-wizard-button-style svg path{
    transition: all 0.2s ease;
}
.mec-wizard-button-style:hover svg path,.mec-wizard-back-button:hover svg path {
    fill: #00B0DD;
}
.mec-wizard-open-popup-box {
    margin-top: 40px;
}
button.mec-wizard-open-popup {
    background: #fff;
    border: 1px solid #DFE1E5;
    border-radius: 11px;
    padding: 22px 18px;
    width: 130px;
    height: 130px;
    font-size: 13px;
    text-align: center;
    color: #52595E;
    float: left;
    margin-right: 65px;
}
.mec-wizard-open-popup-box button:last-of-type {
    margin: 0 !important;
}
button.mec-wizard-open-popup span {
    display: block;
    margin-top: 13px;
}
.mec-wizard-back-box {
    text-align: center;
}
.mec-wizard-back-button {
    margin-top: 55px;
    text-align: center;
    color: #959DA4;
    background: #fff;
    border: none;
    cursor: pointer;
    position: relative;
    padding-left: 20px;
}
.mec-wizard-back-button span {
    vertical-align: bottom;
}
.mec-wizard-back-button svg {
    position: absolute;
    left: 0;
    top: 2px;
}
.mec-wizard-back-button:hover {
    color: #00B0DD;
}
/* Settings Wizard */
.m-e-calendar_page_MEC-wizard .mec-wizard-content {
    box-shadow: 0 3px 20px 0 rgb(91 188 190 / 55%);
    border-radius: 10px;
    height: 100%;
    overflow: hidden;
}
.mec-setup-wizard-wrap {
    box-shadow: 0 3px 20px 0 rgb(204 204 204 / 55%)
    border-radius: 10px;
    max-width: 930px;
    height: 620px;
    margin: 0 auto;
}
.mec-wizard-content {
    background: #fff;
    overflow: hidden;
    display: flex;
    width: 100%;
    border-radius: 10px;
    height: 100%;
}
.mec-steps-container ul li {
    height: 60px;
}
.mec-wizard-content .mec-steps-container ul li:first-of-type {
    height: 41px;
}
.mec-wizard-content .mec-steps-container ul li:after, .mec-wizard-content .mec-steps-container ul li:before {
    height: 19px;
}
.mec-wizard-content .mec-steps-container ul {
    margin-top: 42px;
}
.mec-hide-button,.mec-step-wizard-content {
    display: none;
}
.mec-step-wizard-content {
    height: 100%;
}
.mec-step-wizard-content.mec-active-step {
    display: block;
}
.mec-step-wizard-content .mec-form-row {
    padding: 10px 0;
}
.wp-picker-holder {
    position: absolute;
    z-index: 9999;
}
.mec-next-previous-buttons .mec-button-dashboard,.mec-next-previous-buttons .mec-button-skip {
    float: right;
    background: #008aff;
    border: none;
    color: #fff;
    cursor: pointer;
    width: 135px;
    text-align: left;
    padding: 9px 18px 9px;
    border-radius: 3px;
    font-size: 14px;
    box-shadow: 0 5px 10px 0 rgb(0 138 255 / 30%);
    transition: all .3s ease;
    outline: 0;
    text-decoration: none;
    position: relative;
}
.mec-next-previous-buttons .mec-button-skip {
    float: right;
    background: #808080;
    width: 87px;
    box-shadow: 0 5px 10px 0 rgb(85 89 93 / 30%);
    margin-left: 13px;
}
.mec-next-previous-buttons .mec-button-dashboard img,.mec-next-previous-buttons .mec-button-skip img {
    position: absolute;
    top: 16px;
    right: 18px;
}
.mec-button-next svg {
    position: absolute;
    top: 11px;
    right: 14px;
}
.mec-button-next svg, .mec-button-next svg path {
    fill: #fff;
}
.mec-next-previous-buttons button.mec-button-next {
    width: 88px;
}
.mec-wizard-inner-button {
    background: #008aff;
    border: none;
    color: #fff;
    cursor: pointer;
    width: auto;
    text-align: left;
    padding: 8px 18px 9px;
    border-radius: 3px;
    font-size: 14px;
    box-shadow: 0 5px 10px 0 rgb(0 138 255 / 30%);
    transition: all .3s ease;
    outline: 0;
    display: block;
    margin-top: 10px;
}
.mec-wizard-inner-button img {
    display: none;
}
.mec-wizard-inner-button:hover,.mec-next-previous-buttons button.mec-button-next:hover,.mec-next-previous-buttons .mec-button-dashboard:hover {
    background: #000;
    box-shadow: 0 5px 10px 0 rgb(0 0 0 / 30%);
}
.mec-next-previous-buttons button.mec-button-next{
    background: #2dcb73;
    padding: 9px 18px 9px;
    box-shadow: 0 5px 10px 0 rgb(48 171 46 / 30%);
}
.mec-setup-wizard-wrap .mec-step-wizard-content[data-step="1"] {
    background: url(<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/add-event-first-step.png'; ?>) no-repeat 100% 40%
}
.mec-setup-wizard-wrap .mec-step-wizard-content[data-step="2"] {
    background: url(<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/sixth-step.png'; ?>) no-repeat 100% 40%
}
.mec-setup-wizard-wrap .mec-step-wizard-content[data-step="3"] {
    background: url(<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/fifth-step.png'; ?>) no-repeat 100% 40%
}
.mec-setup-wizard-wrap .mec-step-wizard-content[data-step="4"] {
    background: url(<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/add-organizer.png'; ?>) no-repeat 100% 40%
}
.mec-setup-wizard-wrap .mec-step-wizard-content[data-step="5"] {
    background: url(<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/fourth-step.png'; ?>) no-repeat 100% 40%
}
.mec-setup-wizard-wrap .mec-step-wizard-content[data-step="6"] {
    background: url(<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/fifth-step.png'; ?>) no-repeat 100% 40%
}
.mec-setup-wizard-wrap .mec-step-wizard-content[data-step="7"] {
    background: url(<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/sixth-step.png'; ?>) no-repeat 100% 40%
}
.mec-box label {
    width: 116px !important;
    display: inline-block;
    margin-bottom: 6px;
}
div#mec_related_events_container_toggle label ,#mec_next_previous_events_container_toggle label{
    display: inline-block;
    padding-top:0;
}
ul#mec_export_module_options {
    margin: 0;
    padding: 0;
}
.m-e-calendar_page_MEC-wizard #mec_settings_fes_thankyou_page_url,
.m-e-calendar_page_MEC-wizard input[type=number],
.m-e-calendar_page_MEC-wizard input[type=text],
.m-e-calendar_page_MEC-wizard select,
.m-e-calendar_page_MEC-wizard textarea,
.m-e-calendar_page_MEC-wizard #mec_settings_default_skin_archive,
.m-e-calendar_page_MEC-wizard #mec_settings_default_skin_category {
    min-width: 200px;
    max-width: 200px;
}
@media(max-width: 480px) {
    .mec-steps-panel {
        overflow-y: scroll;
        padding: 20px;
    }
    .mec-steps-panel .mec-step-wizard-content.mec-active-step {
        background-image: unset;
    }
    .mec-steps-panel .mec-next-previous-buttons button {
        display: block;
        margin: 12px 5px;
        width: calc(50% - 10px);
    }
    .mec-steps-panel .mec-next-previous-buttons .mec-button-prev {
        display: block;
        margin: 12px 5px;
        width: calc(100% - 10px);
    }
}
.mec-steps-panel .mec-form-row input[type=checkbox], .mec-steps-panel .mec-form-row input[type=radio] {
    background: #fff;
}
.mec-steps-panel .mec-form-row .mec-box input[type=checkbox], .mec-box .mec-steps-panel .mec-form-row input[type=radio] {
    background: #f7f8f9;
}
.mec-steps-panel .mec-form-row .mec-box input[type=checkbox]:checked, .mec-steps-panel .mec-form-row .mec-box input[type=radio]:checked {
    background: #fff;
}
.mec-steps-header {
    display: flex;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 3px 22px 0 rgb(11 121 125 / 1%);
    padding: 12px 22px;
    margin: -15px -15px 65px;
}
.mec-add-event-popup button.lity-close:hover, .mec-add-shortcode-popup button.lity-close:hover {
    background: #ff6262;
    color: #fff;
    box-shadow: 0 3px 8px 0 rgb(249 162 162 / 55%);
}
.mec-steps-header-userinfo span.mec-steps-header-name {
    font-size: 14px;
    color: #778182;
    font-weight: 600;
    text-transform: capitalize;
}
#mec_popup_event label[for=mec_location_dont_show_map] {
    font-size: 14px;
    margin-left: 4px;
    margin-top: 3px!important;
    display: inline-block;
    margin-bottom: 7px;
}
.mec-steps-container img {
    margin-top: 30px;
}
.mec-steps-container ul li span {
    border-radius: 50px;
    background-color: rgba(26,175,251,.16);
    width: 22px;
    height: 22px;
    display: inline-block;
    padding-top: 2px;
    font-size: 11px;
    font-weight: 700;
    color: #1aaffb;
    box-sizing: border-box;
}
.mec-wizard-loading {
    background: #ffffff54;
    width: 100%;
    height: 100%;
    position: absolute;
    left: 0px;
    bottom: 0;
    top: 0;
    border-radius: 22px;
    display: none;
    z-index: 99;
}
.mec-loader {
    top: calc(50% - 25px);
    left: calc(50% - 25px);
}
.lity.mec-settings {
    background-color: #b7e4e3;
}
.mec-settings button.lity-close {
    right: 0;
    top: -52px;
    border-radius: 50%;
    width: 37px;
    height: 37px;
    background: #fff;
    color: #a2afbc;
    text-shadow: none;
    padding-top: 1px;
    transition: all .2s ease;
    position: absolute;
    box-shadow: 0 3px 8px 0 rgb(91 188 190 / 55%);
}
.mec-settings button.lity-close:hover {
    background: #ff6262;
    color: #fff;
    box-shadow: 0 3px 8px 0 rgb(249 162 162 / 55%);
}
.m-e-calendar_page_MEC-wizard #adminmenumain, .m-e-calendar_page_MEC-wizard .error, .m-e-calendar_page_MEC-wizard .notice, .m-e-calendar_page_MEC-wizard .update-nag, .m-e-calendar_page_MEC-wizard .updated ,.m-e-calendar_page_MEC-wizard div#wpadminbar,.m-e-calendar_page_MEC-wizard div#wpfooter
{
    display: none;
}

.m-e-calendar_page_MEC-wizard #wpwrap {
    top: 0;
}

.m-e-calendar_page_MEC-wizard #wpcontent, .m-e-calendar_page_MEC-wizard #wpbody-content {
    overflow-x: inherit!important;
}

.m-e-calendar_page_MEC-wizard #wpcontent {
    margin-left: 0!important;
}

.m-e-calendar_page_MEC-wizard #wpcontent, .m-e-calendar_page_MEC-wizard #wpbody-content {
    padding: 0;
    overflow-x: hidden!important;
    min-height: calc(100vh - 32px);
}
@media (max-width: 860px) {
    .mec-wizard-wrap {padding: 30px 80px;}
}
@media (max-width: 690px) {
    .mec-wizard-wrap {padding: 30px 40px; margin-top: 50px;}
    .mec-wizard-open-popup-box,
    .mec-wizard-import-dummy {margin-top: 20px;}
    .mec-wizard-import-dummy .mec-wizard-import-events,
    .mec-wizard-import-dummy .mec-wizard-import-shortcodes {display: block; width: 100%; margin: 20px 0; float: none;}
    .mec-wizard-import-box {width: 100%; text-align: center;}
    .mec-wizard-open-popup-box {display: flex;}
    button.mec-wizard-open-popup {margin: 0;width: 100%;}
    button.mec-wizard-open-popup {margin-right: 20px;}
}
@media (max-width: 480px) {
    .mec-wizard-wrap {padding: 30px 40px; margin-top: 50px;}
    .mec-wizard-open-popup-box {display: block;}
    button.mec-wizard-open-popup {margin: 0 0 20px 0; padding: 1.161rem 18px; width: 100%; height: auto;}
    button.mec-wizard-open-popup span,
    button.mec-wizard-open-popup svg {display: inline-block; margin: 0;}
    button.mec-wizard-open-popup svg {vertical-align: middle; }
    button.mec-wizard-open-popup span {padding-left: 10px;}
}
@media (max-width: 320px) {
    .mec-wizard-wrap {padding: 20px 20px;}
}

.mec-steps-header-dashboard a:hover,.mec-steps-header-settings a:hover{
    color: #1aaffb;
}
button.mec-wizard-open-popup span.wizard-notification-text {
    margin-top: 3px;
    font-size: 10px;
    color: #bfbfbf;
    letter-spacing: 0.5px;
}

a.mec-wizard-close-button {
    position: absolute;
    top: 18px;
    right: 23px;
    box-shadow: 0 2px 0 rgb(0 0 0 / 2%);
    border: 1px solid #DFE1E5;
    transition: all 0.2s ease;
    width: 20px;
    height: 20px;
    padding: 6px;
    border-radius: 50px;
}
a.mec-wizard-close-button:hover {
    border-color: #fb1919;
}
a.mec-wizard-close-button svg {
    width: 20px;
    height: 20px;
}

a.mec-wizard-close-button svg path {
    fill: #9e9e9e;
    transition: all 0.2s ease;
}

a.mec-wizard-close-button:hover svg path {
    fill: #fb1919;
}
</style>
<div class="mec-wizard-wrap">
    <div class="mec-wizard-loading"><div class="mec-loader"></div></div>
    <h3><?php esc_html_e('Modern Events Calendar' , 'modern-events-calendar-lite'); ?></h3>
    <div class="mec-wizard-starter-video">
        <a href="https://www.youtube.com/embed/FV_X341oyiw" class="mec-wizard-button-style"><svg xmlns="http://www.w3.org/2000/svg" width="44.098" height="33" viewBox="0 0 44.098 33"><path d="M24.4,9A90.306,90.306,0,0,0,8.3,10.2a5.55,5.55,0,0,0-4.5,4.3A65.024,65.024,0,0,0,3,25a54.425,54.425,0,0,0,.9,10.5,5.691,5.691,0,0,0,4.5,4.3A92.024,92.024,0,0,0,24.5,41a91.941,91.941,0,0,0,16.1-1.2,5.545,5.545,0,0,0,4.5-4.3,75.529,75.529,0,0,0,1-10.6,54.229,54.229,0,0,0-1-10.6A5.681,5.681,0,0,0,40.6,10,124.79,124.79,0,0,0,24.4,9Zm0,2a99.739,99.739,0,0,1,15.8,1.1,3.669,3.669,0,0,1,2.9,2.7,54.775,54.775,0,0,1,1,10.1,73.687,73.687,0,0,1-1,10.3c-.3,1.9-2.3,2.5-2.9,2.7a91.694,91.694,0,0,1-15.6,1.2c-6,0-12.1-.4-15.6-1.2a3.668,3.668,0,0,1-2.9-2.7A39.331,39.331,0,0,1,5,25a55.674,55.674,0,0,1,.8-10.1c.3-1.9,2.4-2.5,2.9-2.7A87.752,87.752,0,0,1,24.4,11ZM19,17V33l14-8Zm2,3.4L29,25l-8,4.6Z" transform="translate(-2.5 -8.5)" fill="#959da4" stroke="#fff" stroke-width="1"/></svg><p><?php esc_html_e('Getting Started Video' , 'modern-events-calendar-lite'); ?></p></a>
    </div>
    <div class="mec-wizard-import-dummy">
        <div class="mec-wizard-import-events">
            <button class="mec-wizard-import-box mec-button-import-events mec-wizard-button-style">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="20.251" viewBox="0 0 24 20.251"><path d="M11.625,10.375v11.11L9.281,19.094l-.562.61,3,3.047h.563l3-3.047-.562-.562-2.344,2.344V10.375ZM9,16H.75V3.25h22.5V16H15v.75h9V2.5H0V16.75H9Z" transform="translate(0 -2.5)" fill="#959da4"/></svg>
                <span><?php esc_html_e('Import Dummy Events' , 'modern-events-calendar-lite'); ?></span>
            </button>
        </div>
        <div class="mec-wizard-import-shortcodes">
            <button class="mec-wizard-import-box mec-button-import-shortcodes mec-wizard-button-style">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="20.251" viewBox="0 0 24 20.251"><path d="M11.625,10.375v11.11L9.281,19.094l-.562.61,3,3.047h.563l3-3.047-.562-.562-2.344,2.344V10.375ZM9,16H.75V3.25h22.5V16H15v.75h9V2.5H0V16.75H9Z" transform="translate(0 -2.5)" fill="#959da4"/></svg>
                <span><?php esc_html_e('Import Dummy Shortcodes' , 'modern-events-calendar-lite'); ?></span>
            </button>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="mec-wizard-open-popup-box">
        <button class="mec-wizard-open-popup add-event mec-wizard-button-style">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="27" viewBox="0 0 32 27"><path d="M26.5,2.5h-4v2H9.5v-2h-4v2H0v25H32V4.5H26.5Zm-3,1h2v3h-2Zm-17,0h2v3h-2ZM1,28.5V9.5H31v19Zm30-23v3H1v-3H5.5v2h4v-2h13v2h4v-2ZM4.5,17.5h6v-6h-6Zm1-5h4v4h-4Zm-1,14h6v-6h-6Zm1-5h4v4h-4Zm16-4h6v-6h-6Zm1-5h4v4h-4Zm-1,14h6v-6h-6Zm1-5h4v4h-4Zm-9.5-4h6v-6H13Zm1-5h4v4H14Zm-1,14h6v-6H13Zm1-5h4v4H14Z" transform="translate(0 -2.5)" fill="#959da4"/></svg>
            <span><?php esc_html_e('Add Event' , 'modern-events-calendar-lite'); ?></span>
            <span class="wizard-notification-text"><?php esc_html_e('Wizard' , 'modern-events-calendar-lite'); ?></span>
        </button>
        <button class="mec-wizard-open-popup add-shortcode mec-wizard-button-style">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="26" viewBox="0 0 32 26"><path d="M0,29H32V3H0Zm1-1V8H31V28ZM31,4V7H1V4ZM3,5H5V6H3ZM7,5H9V6H7Zm4,0h2V6H11ZM3,12H16.5v1H3Zm0,4H16.5v1H3Zm0,4H16.5v1H3Zm15.5,1H29V12H18.5Zm1-8H28v7H19.5Z" transform="translate(0 -3)" fill="#959da4"/></svg>
            <span><?php esc_html_e('Add Shortcode' , 'modern-events-calendar-lite'); ?></span>
            <span class="wizard-notification-text"><?php esc_html_e('Wizard' , 'modern-events-calendar-lite'); ?></span>
        </button>
        <button class="mec-wizard-open-popup mec-settings mec-wizard-button-style">
            <svg xmlns="http://www.w3.org/2000/svg" width="32.002" height="32.002" viewBox="0 0 32.002 32.002"><path d="M26.563,10.125,29.688,7,25,2.312,21.875,5.437,19.5,4.624V0h-7V4.624l-2.375.813L7,2.312,2.312,7l3.125,3.125L4.624,12.5H0v7H4.624l.813,2.375L2.312,25,7,29.688l3.125-3.125,2.375.813V32h7V27.376l2.375-.813L25,29.688,29.688,25l-3.125-3.125.813-2.375H32v-7H27.376ZM31,18.5H26.625l-1.188,3.625L28.312,25,25,28.313l-2.875-2.875L18.5,26.626V31h-5V26.626L9.874,25.438,7,28.313,3.686,25l2.875-2.875L5.373,18.5H1v-5H5.373L6.561,9.875,3.686,7,7,3.687,9.874,6.562,13.5,5.374V1h5V5.374l3.625,1.188L25,3.687,28.312,7,25.437,9.875,26.625,13.5H31Zm-15-6A3.5,3.5,0,1,0,19.5,16,3.494,3.494,0,0,0,16,12.5Zm0,6A2.5,2.5,0,1,1,18.5,16,2.507,2.507,0,0,1,16,18.5Z" transform="translate(0.001 0.001)" fill="#959da4"/></svg>
            <span><?php esc_html_e('Settings' , 'modern-events-calendar-lite'); ?></span>
            <span class="wizard-notification-text"><?php esc_html_e('Wizard' , 'modern-events-calendar-lite'); ?></span>
        </button>
        <div style="clear:both"></div>
    </div>
    <div class="mec-wizard-back-box">
        <button class="mec-wizard-back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16.812" height="16.812" viewBox="0 0 16.812 16.812"><path d="M11.739,13.962a.437.437,0,0,1-.624,0L7.377,10.226a.442.442,0,0,1,0-.626l3.559-3.562a.442.442,0,0,1,.626.624L8.314,9.912l3.425,3.426a.442.442,0,0,1,0,.624M18.406,10A8.406,8.406,0,1,1,10,1.594,8.4,8.4,0,0,1,18.406,10m-.885,0A7.521,7.521,0,1,0,10,17.521,7.528,7.528,0,0,0,17.521,10" transform="translate(-1.594 -1.594)" fill="#959da4"/></svg>
            <span><?php esc_html_e('Back to WordPress Dashboard' , 'modern-events-calendar-lite'); ?></span>
        </button>
    </div>
    <a href="<?php echo admin_url('/admin.php?page=mec-intro'); ?>" class="mec-wizard-close-button">
        <svg enable-background="new 0 0 256 256" id="Layer_1" version="1.1" viewBox="0 0 256 256" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M137.051,128l75.475-75.475c2.5-2.5,2.5-6.551,0-9.051s-6.551-2.5-9.051,0L128,118.949L52.525,43.475  c-2.5-2.5-6.551-2.5-9.051,0s-2.5,6.551,0,9.051L118.949,128l-75.475,75.475c-2.5,2.5-2.5,6.551,0,9.051  c1.25,1.25,2.888,1.875,4.525,1.875s3.275-0.625,4.525-1.875L128,137.051l75.475,75.475c1.25,1.25,2.888,1.875,4.525,1.875  s3.275-0.625,4.525-1.875c2.5-2.5,2.5-6.551,0-9.051L137.051,128z"/></svg>
    </a>
</div>


<?php 
$path_event = MEC::import('app.features.popup.event', true, true);
include $path_event;

$path_shortcode = MEC::import('app.features.popup.shortcode', true, true);
include $path_shortcode;


$path_settings = MEC::import('app.features.popup.settings', true, true);
include $path_settings;
?>
<script>
    jQuery(document).on('lity:close', function(event, instance) {
        jQuery("body").css("overflow", "auto")
    });
    jQuery('.mec-wizard-back-button').on('click', function(e)
    {
        window.location.replace('<?php echo admin_url('/admin.php?page=mec-intro'); ?>')
    });
</script>