<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Secure_Copy_Content_Protection
 * @subpackage Secure_Copy_Content_Protection/public/partials
 */
global $wpdb;
$result                 = $wpdb->get_row("SELECT * FROM " . SCCP_TABLE . " WHERE id = 1", ARRAY_A);
$data                   = json_decode($result["options"], true);
$styles                 = json_decode($result["styles"], true);
$enable_left_click      = ((isset($data["left_click"]) && ($data["left_click"] == "checked"))) ? true : false;
$enable_developer_tools = ((isset($data["developer_tools"]) && ($data["developer_tools"] == "checked")) || !isset($data["left_click"])) ? true : false;
$enable_context_menu    = ((isset($data["context_menu"]) && ($data["context_menu"] == "checked")) || !isset($data["context_menu"])) ? true : false;
$enable_rclick_img      = ((isset($data["rclick_img"]) && ($data["rclick_img"] == "checked"))) ? true : false;
$enable_drag_start      = ((isset($data["drag_start"]) && ($data["drag_start"] == "checked")) || !isset($data["drag_start"])) ? true : false;
$enable_mobile_img      = ((isset($data["mobile_img"]) && ($data["mobile_img"] == "checked")) || !isset($data["mobile_img"])) ? true : false;
$enable_ctrlc           = ((isset($data["ctrlc"]) && ($data["ctrlc"] == "checked")) || !isset($data["ctrlc"])) ? true : false;
$enable_ctrlv           = ((isset($data["ctrlv"]) && ($data["ctrlv"] == "checked")) || !isset($data["ctrlv"])) ? true : false;
$enable_ctrls           = ((isset($data["ctrls"]) && ($data["ctrls"] == "checked")) || !isset($data["ctrls"])) ? true : false;
$enable_ctrla           = ((isset($data["ctrla"]) && ($data["ctrla"] == "checked")) || !isset($data["ctrla"])) ? true : false;
$enable_ctrlx           = ((isset($data["ctrlx"]) && ($data["ctrlx"] == "checked")) || !isset($data["ctrlx"])) ? true : false;
$enable_ctrlu           = ((isset($data["ctrlu"]) && ($data["ctrlu"] == "checked")) || !isset($data["ctrlu"])) ? true : false;
$enable_ctrlf           = ((isset($data["ctrlf"]) && ($data["ctrlf"] == "checked")) || !isset($data["ctrlf"])) ? true : false;
$enable_ctrlp           = ((isset($data["ctrlp"]) && ($data["ctrlp"] == "checked")) || !isset($data["ctrlp"])) ? true : false;
$enable_ctrlh           = (isset($data["ctrlh"]) && ($data["ctrlh"] == "checked")) ? true : false;
$enable_ctrll           = (isset($data["ctrll"]) && ($data["ctrll"] == "checked")) ? true : false;
$enable_ctrlk           = (isset($data["ctrlk"]) && ($data["ctrlk"] == "checked")) ? true : false;
$enable_ctrlo           = (isset($data["ctrlo"]) && ($data["ctrlo"] == "checked")) ? true : false;
$enable_f6              = (isset($data["sccp_f6"]) && ($data["sccp_f6"] == "checked")) ? true : false;
$enable_f3              = (isset($data["sccp_f3"]) && ($data["sccp_f3"] == "checked")) ? true : false;
$enable_altd            = (isset($data["sccp_altd"]) && ($data["sccp_altd"] == "checked")) ? true : false;
$enable_ctrle           = (isset($data["sccp_ctrle"]) && ($data["sccp_ctrle"] == "checked")) ? true : false;
$enable_f12             = ((isset($data["f12"]) && ($data["f12"] == "checked")) || !isset($data["f12"])) ? true : false;
$enable_printscreen     = ((isset($data["printscreen"]) && ($data["printscreen"] == "checked")) || !isset($data["printscreen"])) ? true : false;
$show_msg_only_once     = (isset($data["msg_only_once"]) && ($data["msg_only_once"] == "checked")) ? true : false;

$enable_left_click_mess      = ((isset($data["left_click_mess"]) && ($data["left_click_mess"] == "checked"))) ? true : false;
$enable_developer_tools_mess = (isset($data["developer_tools_mess"]) && ($data["developer_tools_mess"] == "checked") || (!isset($data["developer_tools_mess"]))) ? true : false;
$enable_context_menu_mess    = (isset($data["context_menu_mess"]) && ($data["context_menu_mess"] == "checked") || (!isset($data["context_menu_mess"]))) ? true : false;
$enable_rclick_img_mess      = (isset($data["rclick_img_mess"]) && ($data["rclick_img_mess"] == "checked")) ? true : false;
$enable_mobile_img_mess      = (isset($data["mobile_img_mess"]) && ($data["mobile_img_mess"] == "checked") || (!isset($data["mobile_img_mess"]))) ? true : false;
$enable_drag_start_mess      = (isset($data["drag_start_mess"]) && ($data["drag_start_mess"] == "checked") || (!isset($data["drag_start_mess"]))) ? true : false;
$enable_ctrlc_mess           = (isset($data["ctrlc_mess"]) && ($data["ctrlc_mess"] == "checked") || (!isset($data["ctrlc_mess"]))) ? true : false;
$enable_ctrlv_mess           = (isset($data["ctrlv_mess"]) && ($data["ctrlv_mess"] == "checked") || (!isset($data["ctrlv_mess"]))) ? true : false;
$enable_ctrls_mess           = (isset($data["ctrls_mess"]) && ($data["ctrls_mess"] == "checked") || (!isset($data["ctrls_mess"]))) ? true : false;
$enable_ctrla_mess           = (isset($data["ctrla_mess"]) && ($data["ctrla_mess"] == "checked") || (!isset($data["ctrla_mess"]))) ? true : false;
$enable_ctrlx_mess           = (isset($data["ctrlx_mess"]) && ($data["ctrlx_mess"] == "checked") || (!isset($data["ctrlx_mess"]))) ? true : false;
$enable_ctrlu_mess           = (isset($data["ctrlu_mess"]) && ($data["ctrlu_mess"] == "checked") || (!isset($data["ctrlu_mess"]))) ? true : false;
$enable_ctrlf_mess           = (isset($data["ctrlf_mess"]) && ($data["ctrlf_mess"] == "checked") || (!isset($data["ctrlf_mess"]))) ? true : false;
$enable_ctrlp_mess           = (isset($data["ctrlp_mess"]) && ($data["ctrlp_mess"] == "checked") || (!isset($data["ctrlp_mess"]))) ? true : false;
$enable_ctrlh_mess           = (isset($data["ctrlh_mess"]) && ($data["ctrlh_mess"] == "checked")) ? true : false;
$enable_ctrll_mess           = (isset($data["ctrll_mess"]) && ($data["ctrll_mess"] == "checked")) ? true : false;
$enable_ctrlk_mess           = (isset($data["ctrlk_mess"]) && ($data["ctrlk_mess"] == "checked")) ? true : false;
$enable_ctrlo_mess           = (isset($data["ctrlo_mess"]) && ($data["ctrlo_mess"] == "checked")) ? true : false;
$enable_f6_mess              = (isset($data["f6_mess"]) && ($data["f6_mess"] == "checked")) ? true : false;
$enable_f3_mess              = (isset($data["f3_mess"]) && ($data["f3_mess"] == "checked")) ? true : false;
$enable_altd_mess            = (isset($data["altd_mess"]) && ($data["altd_mess"] == "checked")) ? true : false;
$enable_ctrle_mess           = (isset($data["ctrle_mess"]) && ($data["ctrle_mess"] == "checked")) ? true : false;
$enable_f12_mess             = (isset($data["f12_mess"]) && ($data["f12_mess"] == "checked")) ? true : false;
$enable_printscreen_mess     = (isset($data["printscreen_mess"]) && ($data["printscreen_mess"] == "checked") || (!isset($data["printscreen_mess"]))) ? true : false;

$enable_left_click_audio      = (isset($data["left_click_audio"]) && ($data["left_click_audio"] == "checked")) ? true : false;
$right_click_audio            = (isset($data["right_click_audio"]) && ($data["right_click_audio"] == "checked")) ? true : false;
$enabled_rclick_img_audio     = (isset($data["rclick_img_audio"]) && ($data["rclick_img_audio"] == "checked")) ? true : false;
$enable_developer_tools_audio = (isset($data["developer_tools_audio"]) && ($data["developer_tools_audio"] == "checked")) ? true : false;
$enable_drag_start_audio      = (isset($data["drag_start_audio"]) && ($data["drag_start_audio"] == "checked")) ? true : false;
$enable_mobile_img_audio      = (isset($data["mobile_img_audio"]) && ($data["mobile_img_audio"] == "checked")) ? true : false;
$enable_ctrlc_audio           = (isset($data["ctrlc_audio"]) && ($data["ctrlc_audio"] == "checked")) ? true : false;
$enable_ctrlv_audio           = (isset($data["ctrlv_audio"]) && ($data["ctrlv_audio"] == "checked")) ? true : false;
$enable_ctrls_audio           = (isset($data["ctrls_audio"]) && ($data["ctrls_audio"] == "checked")) ? true : false;
$enable_ctrla_audio           = (isset($data["ctrla_audio"]) && ($data["ctrla_audio"] == "checked")) ? true : false;
$enable_ctrlx_audio           = (isset($data["ctrlx_audio"]) && ($data["ctrlx_audio"] == "checked")) ? true : false;
$enable_ctrlu_audio           = (isset($data["ctrlu_audio"]) && ($data["ctrlu_audio"] == "checked")) ? true : false;
$enable_ctrlf_audio           = (isset($data["ctrlf_audio"]) && ($data["ctrlf_audio"] == "checked")) ? true : false;
$enable_ctrlp_audio           = (isset($data["ctrlp_audio"]) && ($data["ctrlp_audio"] == "checked")) ? true : false;
$enable_ctrlh_audio           = (isset($data["ctrlh_audio"]) && ($data["ctrlh_audio"] == "checked")) ? true : false;
$enable_ctrll_audio           = (isset($data["ctrll_audio"]) && ($data["ctrll_audio"] == "checked")) ? true : false;
$enable_ctrlk_audio           = (isset($data["ctrlk_audio"]) && ($data["ctrlk_audio"] == "checked")) ? true : false;
$enable_ctrlo_audio           = (isset($data["ctrlo_audio"]) && ($data["ctrlo_audio"] == "checked")) ? true : false;
$enable_f6_audio              = (isset($data["f6_audio"]) && ($data["f6_audio"] == "checked")) ? true : false;
$enable_f3_audio              = (isset($data["f3_audio"]) && ($data["f3_audio"] == "checked")) ? true : false;
$enable_altd_audio            = (isset($data["altd_audio"]) && ($data["altd_audio"] == "checked")) ? true : false;
$enable_ctrle_audio           = (isset($data["ctrle_audio"]) && ($data["ctrle_audio"] == "checked")) ? true : false;
$enable_f12_audio             = (isset($data["f12_audio"]) && ($data["f12_audio"] == "checked")) ? true : false;
$enable_printscreen_audio     = (isset($data["printscreen_audio"]) && ($data["printscreen_audio"] == "checked")) ? true : false;

$enable_text_selecting = (isset($data["enable_text_selecting"]) && ($data["enable_text_selecting"] == "checked")) ? true : false;
$timeout               = (isset($data["timeout"]) && $data["timeout"] > 0) ? absint($data["timeout"]) : 1000;

$exclude_inp_textarea = (isset($data["exclude_inp_textarea"]) && ($data["exclude_inp_textarea"] == "checked")) ? true : false;

$tooltip_position = (isset($styles["tooltip_position"])) ? $styles["tooltip_position"] : "mouse";

$exclude_css_selector = (isset($data["exclude_css_selector"]) && ($data["exclude_css_selector"] == "checked")) ? true : false;
$exclude_css_selectors = ($exclude_css_selector && isset($styles["exclude_css_selectors"])) ? $styles["exclude_css_selectors"] : "";

$enable_copyright_text = (isset($data["enable_copyright_text"]) && ($data["enable_copyright_text"] == "on")) ? true : false;
$copyright_text = (isset($data["copyright_text"]) && ($data["copyright_text"] != "")) ? $data["copyright_text"] : '';
$copyright_include_url = (isset($data["copyright_include_url"]) &&  $data['copyright_include_url'] == "on") ? true : false;

$sccp_enable_copyright_word = (isset($data["enable_sccp_copyright_word"]) && ($data["enable_sccp_copyright_word"] == "on")) ? true : false;
$sccp_copyright_word = (isset($data["sccp_copyright_word"]) && ($data["sccp_copyright_word"] != "")) ? esc_attr($data["sccp_copyright_word"]) : '';

$is_mobile = Secure_Copy_Content_Protection_Public::isMobileDevice();

//Elementor plugin conflict solution
if (!isset($_GET['elementor-preview'])): ?>
    <style>
        <?php if(!$enable_text_selecting): ?>
        *:not(input):not(textarea)::selection {
            background-color: transparent !important;
            color: inherit !important;
        }

        *:not(input):not(textarea)::-moz-selection {
            background-color: transparent !important;
            color: inherit !important;
        }

        <?php endif;

         if($is_mobile):?>
        *:not(input):not(textarea):not(button) {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0) !important;
            -webkit-touch-callout: none !important;
        }

        <?php endif;?>
    </style>
    <script>
        // window.addEventListener("DOMContentLoaded",function(){
            function stopPrntScr() {
                var inpFld = document.createElement("input");
                inpFld.setAttribute("value", "Access Denied");
                inpFld.setAttribute("width", "0");
                inpFld.style.height = "0px";
                inpFld.style.width = "0px";
                inpFld.style.border = "0px";
                document.body.appendChild(inpFld);
                inpFld.select();
                document.execCommand("copy");
                inpFld.remove(inpFld);
            }

            (function ($) {
                'use strict';
                $(function () {
                    let all = $('*').not('script, meta, link, style, noscript, title'),
                        tooltip = $('#ays_tooltip'),
                        tooltipClass = "<?=$tooltip_position?>";
                    if (tooltipClass == "mouse" || tooltipClass == "mouse_first_pos") {
    	                <?php if($is_mobile):?>
                        let startTime, endTime;
                        all.on('touchstart', function (e) {
                            startTime = Date.now();
                        });

                        all.on('touchend', function (e) {
                            endTime = Date.now();
                            if (endTime - startTime > 1000) {
                                e.preventDefault();
                            }
                            let cordinate_x = e.pageX || e.originalEvent.changedTouches[0].pageX;
                            let cordinate_y = (e.pageY || e.originalEvent.changedTouches[0].pageY) - 30;
                            let windowWidth = $(window).width();
                            if (cordinate_y < tooltip.outerHeight()) {
                                tooltip.css({'top': (cordinate_y + tooltip.outerHeight() - 10) + 'px'});
                            } else {
                                tooltip.css({'top': (cordinate_y - tooltip.outerHeight()) + 'px'});
                            }
                            if (cordinate_x > (windowWidth - tooltip.outerWidth())) {
                                tooltip.css({'left': (cordinate_x - tooltip.outerWidth()) + 'px'});
                            } else {
                                tooltip.css({'left': (cordinate_x + 5) + 'px'});
                            }

                        });
    	                <?php else:?>
                        $(document).on('mousemove', function (e) {
                            let cordinate_x = e.pageX;
                            let cordinate_y = e.pageY;
                            let windowWidth = $(window).width();
                            if (cordinate_y < tooltip.outerHeight()) {
                                tooltip.css({'top': (cordinate_y + 10) + 'px'});
                            } else {
                                tooltip.css({'top': (cordinate_y - tooltip.outerHeight()) + 'px'});
                            }
                            if (cordinate_x > (windowWidth - tooltip.outerWidth())) {
                                tooltip.css({'left': (cordinate_x - tooltip.outerWidth()) + 'px'});
                            } else {
                                tooltip.css({'left': (cordinate_x + 5) + 'px'});
                            }

                        });
    	                <?php endif;?>
                    } else {
                        tooltip.addClass(tooltipClass);
                    }
    				<?php if($enable_printscreen) : ?>
                    $(window).on('keyup', function (e) {
                        let keyCode = e.keyCode ? e.keyCode : e.which;
                        if (keyCode == 44) {
                            stopPrntScr();
                            show_tooltip(<?php echo $enable_printscreen_mess?> );
                            audio_play(<?php echo $enable_printscreen_audio ?>);
                        }
                    });
    				<?php endif; ?>


    				<?php if($enable_context_menu) : ?>
                    $(document).on('contextmenu', function (e) {
                        let target = $(event.target);
                        if (!target.is("<?=$exclude_css_selectors?>")) {
                            let t = e || window.event;
                            let n = t.target || t.srcElement;
                            if (n.nodeName !== "A") {
                                show_tooltip(<?php echo $enable_context_menu_mess?> );
                                audio_play(<?php echo $right_click_audio?>);
                            }
                            return false;
                        }
                    });
    	            <?php endif; ?>


                    <?php if($enable_rclick_img) : ?>
                    $(document).on('contextmenu', function (e) {
                        let target = $(event.target);
                        if (target.is("img") || target.is("div.ays_tooltip_class")) {
                            let t = e || window.event;
                            let n = t.target || t.srcElement;
                            if (n.nodeName !== "A") {
                                show_tooltip(<?php echo $enable_rclick_img_mess?> );
                                audio_play(<?php echo $enabled_rclick_img_audio?>);
                            }
                            return false;
                        }
                    });
                    <?php endif; ?>

    	            <?php if($enable_mobile_img) : ?>
                    all.on('touchstart', function (event) {
                        let target = $(event.target);
                        if (target.is("img")) {
                            show_tooltip(<?php echo $enable_mobile_img_mess?> );
                            audio_play(<?php echo $enable_mobile_img_audio?>);
                            event.preventDefault();
                            event.stopPropagation();
                            event.stopImmediatePropagation();
                            return false;
                        }
                    });
    				<?php endif; ?>

    				<?php if($enable_drag_start) : ?>
                    $(document).on('dragstart', function () {
                        let target = $(event.target);
                        if (!target.is("<?=$exclude_css_selectors?>")) {
                            show_tooltip(<?php echo $enable_drag_start_mess?> );
                            audio_play(<?php echo $enable_drag_start_audio?>);
                            return false;
                        }
                    });
    				<?php endif; ?>

    				<?php if($enable_left_click) : ?>

                    $(document).on('mousedown', function (e) {
                        let target = $(event.target);
                        if (!target.is("<?=$exclude_css_selectors?>")) {
                            let event = e || window.event;
                            if (event.which == 1) {
                                show_tooltip(<?php echo $enable_left_click_mess?> );
                                audio_play(<?php echo $enable_left_click_audio?>);
                                return false;
                            }
                        }
                    });
    				<?php endif; ?>

                    $(window).on('keydown', function (event) {
                        var sccp_selObj = window.getSelection();
                        var check_selectors = true;
                        if (!sccp_selObj.rangeCount < 1) {
                            var sccp_selRange = sccp_selObj.getRangeAt(0);
                            var sccp_selection_selector = sccp_selRange.startContainer.parentElement;
                            check_selectors = !$(sccp_selection_selector).is("<?=$exclude_css_selectors?>");
                        }

                        if (check_selectors) {
                            var isOpera = (BrowserDetect.browser === "Opera");

                            var isFirefox = (BrowserDetect.browser === 'Firefox');

                            var isSafari = (BrowserDetect.browser === 'Safari');

                            var isIE = (BrowserDetect.browser === 'Explorer');
                            var isChrome = (BrowserDetect.browser === 'Chrome');
                            var isMozilla = (BrowserDetect.browser === 'Mozilla');

                            if (BrowserDetect.OS === 'Windows') {
        						<?php if($enable_developer_tools) : ?>
                                if (isChrome) {
                                    if (((event.ctrlKey && event.shiftKey) && (
                                        event.keyCode === 73 ||
                                        event.keyCode === 74 ||
                                        event.keyCode === 68 ||
                                        event.keyCode === 67))) {
                                        show_tooltip(<?php echo $enable_developer_tools_mess ?>);
                                        audio_play(<?php echo $enable_developer_tools_audio?>);
                                        return false;
                                    }
                                }
                                if (isFirefox) {
                                    if (((event.ctrlKey && event.shiftKey) && (
                                        event.keyCode === 73 ||
                                        event.keyCode === 74 ||
                                        event.keyCode === 67 ||
                                        event.keyCode === 75 ||
                                        event.keyCode === 69)) ||
                                        event.keyCode === 118 ||                                    
                                        (event.keyCode === 112 && event.shiftKey) ||
                                        (event.keyCode === 115 && event.shiftKey) ||
                                        (event.keyCode === 118 && event.shiftKey) ||
                                        (event.keyCode === 120 && event.shiftKey)) {
                                        show_tooltip(<?php echo $enable_developer_tools_mess ?>);
                                        audio_play(<?php echo $enable_developer_tools_audio?>);
                                        return false;
                                    }
                                }
                                if (isOpera) {
                                    if (((event.ctrlKey && event.shiftKey) && (
                                        event.keyCode === 73 ||
                                        event.keyCode === 74 ||
                                        event.keyCode === 67 ||
                                        event.keyCode === 88 ||
                                        event.keyCode === 69))) {
                                        show_tooltip(<?php echo $enable_developer_tools_mess ?>);
                                        audio_play(<?php echo $enable_developer_tools_audio?>);
                                        return false;
                                    }
                                }
                                if (isIE) {
                                    if ((event.keyCode === 123 && event.shiftKey)) {
                                        show_tooltip(<?php echo $enable_developer_tools_mess ?>);
                                        audio_play(<?php echo $enable_developer_tools_audio?>);
                                        return false;
                                    }
                                }                         
                                if (isMozilla) {
                                    if ((event.ctrlKey && event.keyCode === 73) || 
                                        (event.altKey && event.keyCode === 68)) {
                                        show_tooltip(<?php echo $enable_developer_tools_mess ?>);
                                        audio_play(<?php echo $enable_developer_tools_audio?>);
                                        return false;
                                    }
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrls) : ?>
                                if ((event.keyCode === 83 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrls_mess ?>);
                                    audio_play(<?php echo $enable_ctrls_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrla) : ?>
                                if ((event.keyCode === 65 && event.ctrlKey)) {

                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrla_mess ?>);
                                            audio_play(<?php echo $enable_ctrla_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrla_mess ?>);
                                        audio_play(<?php echo $enable_ctrla_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlc) : ?>
                                if (event.keyCode === 67 && event.ctrlKey && !event.shiftKey) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrlc_mess ?>);
                                            audio_play(<?php echo $enable_ctrlc_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrlc_mess ?>);
                                        audio_play(<?php echo $enable_ctrlc_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlv) : ?>
                                if ((event.keyCode === 86 && event.ctrlKey)) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrlv_mess ?>);
                                            audio_play(<?php echo $enable_ctrlv_audio?>);
                                            return false;
                                         }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrlv_mess ?>);
                                        audio_play(<?php echo $enable_ctrlv_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlx) : ?>
                                if ((event.keyCode === 88 && event.ctrlKey)) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrlx_mess ?> );
                                            audio_play(<?php echo $enable_ctrlx_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrlx_mess ?> );
                                        audio_play(<?php echo $enable_ctrlx_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlu) : ?>
                                if ((event.keyCode === 85 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlu_mess ?> );
                                    audio_play(<?php echo $enable_ctrlu_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlf) : ?>
                                if ((event.keyCode === 70 && event.ctrlKey) || (event.keyCode === 71 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlf_mess ?> );
                                    audio_play(<?php echo $enable_ctrlf_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlp) : ?>
                                if ((event.keyCode === 80 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlp_mess ?> );
                                    audio_play(<?php echo $enable_ctrlp_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

                                <?php if($enable_ctrlh) : ?>
                                if ((event.keyCode === 72 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlh_mess ?> );
                                    audio_play(<?php echo $enable_ctrlh_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_ctrll) : ?>
                                if ((event.keyCode === 76 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrll_mess ?> );
                                    audio_play(<?php echo $enable_ctrll_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_ctrlk) : ?>
                                if ((event.keyCode === 75 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlk_mess ?> );
                                    audio_play(<?php echo $enable_ctrlk_audio?>);
                                    return false;
                                }
                                <?php endif; ?>                                

                                <?php if($enable_ctrlo) : ?>
                                if ((event.keyCode === 79 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlo_mess ?> );
                                    audio_play(<?php echo $enable_ctrlo_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_f6) : ?>
                                if (event.keyCode === 117 || (event.keyCode === 117 && event.shiftKey)) {
                                    show_tooltip(<?php echo $enable_f6_mess ?> );
                                    audio_play(<?php echo $enable_f6_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_f3) : ?>
                                if (event.keyCode === 114 || (event.keyCode === 114 && event.shiftKey)) {
                                    show_tooltip(<?php echo $enable_f3_mess ?> );
                                    audio_play(<?php echo $enable_f3_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_altd) : ?>
                                if (event.keyCode === 68 && event.altKey) {
                                    show_tooltip(<?php echo $enable_altd_mess ?> );
                                    audio_play(<?php echo $enable_altd_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_ctrle) : ?>
                                if (event.keyCode === 69 && event.ctrlKey) {
                                    show_tooltip(<?php echo $enable_ctrle_mess ?> );
                                    audio_play(<?php echo $enable_ctrle_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

        						<?php if($enable_f12) : ?>
                                if (event.keyCode === 123 || (event.keyCode === 123 && event.shiftKey)) {
                                    show_tooltip(<?php echo $enable_f12_mess ?>);
                                    audio_play(<?php echo $enable_f12_audio?>);
                                    return false;
                                }
        						<?php endif; ?>
                            } else if (BrowserDetect.OS === 'Linux') {
        						<?php if($enable_developer_tools) : ?>
                                if (isChrome) {
                                    if (
                                        (
                                            (event.ctrlKey && event.shiftKey) &&
                                            (event.keyCode === 73 ||
                                                event.keyCode === 74 ||
                                                event.keyCode === 67
                                            )
                                        ) ||
                                        (event.ctrlKey && event.keyCode === 85)
                                    ) {
                                        show_tooltip(<?php echo $enable_developer_tools_mess ?>);
                                        audio_play(<?php echo $enable_developer_tools_audio?>);
                                        return false;
                                    }
                                }
                                if (isFirefox) {
                                    if (((event.ctrlKey && event.shiftKey) && (event.keyCode === 73 || event.keyCode === 74 || event.keyCode === 67 || event.keyCode === 75 || event.keyCode === 69)) || event.keyCode === 118 || event.keyCode === 116 || (event.keyCode === 112 && event.shiftKey) || (event.keyCode === 115 && event.shiftKey) || (event.keyCode === 118 && event.shiftKey) || (event.keyCode === 120 && event.shiftKey) || (event.keyCode === 85 && event.ctrlKey)) {
                                        show_tooltip(<?php echo $enable_developer_tools_mess ?>);
                                        audio_play(<?php echo $enable_developer_tools_audio?>);
                                        return false;
                                    }
                                }
                                if (isOpera) {
                                    if (((event.ctrlKey && event.shiftKey) && (event.keyCode === 73 || event.keyCode === 74 || event.keyCode === 67 || event.keyCode === 88 || event.keyCode === 69)) || (event.ctrlKey && event.keyCode === 85)) {
                                        show_tooltip(<?php echo $enable_developer_tools_mess ?>);
                                        audio_play(<?php echo $enable_developer_tools_audio?>);
                                        return false;
                                    }
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrls) : ?>
                                if ((event.keyCode === 83 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrls_mess ?>);
                                    audio_play(<?php echo $enable_ctrls_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrla) : ?>
                                if (event.keyCode === 65 && event.ctrlKey) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrla_mess ?>);
                                            audio_play(<?php echo $enable_ctrla_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrla_mess ?>);
                                        audio_play(<?php echo $enable_ctrla_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlc) : ?>
                                if (event.keyCode === 67 && event.ctrlKey && !event.shiftKey) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrlc_mess ?>);
                                            audio_play(<?php echo $enable_ctrlc_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrlc_mess ?>);
                                        audio_play(<?php echo $enable_ctrlc_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlv) : ?>
                                if ((event.keyCode === 86 && event.ctrlKey)) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrlv_mess ?>);
                                            audio_play(<?php echo $enable_ctrlv_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrlv_mess ?>);
                                        audio_play(<?php echo $enable_ctrlv_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlx) : ?>
                                if ((event.keyCode === 88 && event.ctrlKey)) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrlx_mess ?>);
                                            audio_play(<?php echo $enable_ctrlx_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrlx_mess ?>);
                                        audio_play(<?php echo $enable_ctrlx_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlu) : ?>
                                if ((event.keyCode === 85 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlu_mess ?> );
                                    audio_play(<?php echo $enable_ctrlu_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlf) : ?>
                                if ((event.keyCode === 70 && event.ctrlKey) || (event.keyCode === 71 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlf_mess ?> );
                                    audio_play(<?php echo $enable_ctrlf_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlp) : ?>
                                if ((event.keyCode === 80 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlp_mess ?> );
                                    audio_play(<?php echo $enable_ctrlp_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

                                <?php if($enable_ctrlh) : ?>
                                if ((event.keyCode === 72 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlh_mess ?> );
                                    audio_play(<?php echo $enable_ctrlh_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_ctrll) : ?>
                                if ((event.keyCode === 76 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrll_mess ?> );
                                    audio_play(<?php echo $enable_ctrll_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_ctrlk) : ?>
                                if ((event.keyCode === 75 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlk_mess ?> );
                                    audio_play(<?php echo $enable_ctrlk_audio?>);
                                    return false;
                                }
                                <?php endif; ?>                                

                                <?php if($enable_ctrlo) : ?>
                                if ((event.keyCode === 79 && event.ctrlKey)) {
                                    show_tooltip(<?php echo $enable_ctrlo_mess ?> );
                                    audio_play(<?php echo $enable_ctrlo_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_f6) : ?>
                                if (event.keyCode === 117 || (event.keyCode === 117 && event.shiftKey)) {
                                    show_tooltip(<?php echo $enable_f6_mess ?> );
                                    audio_play(<?php echo $enable_f6_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_f3) : ?>
                                if (event.keyCode === 114 || (event.keyCode === 114 && event.shiftKey)) {
                                    show_tooltip(<?php echo $enable_f3_mess ?> );
                                    audio_play(<?php echo $enable_f3_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_altd) : ?>
                                if (event.keyCode === 68 && event.altKey) {
                                    show_tooltip(<?php echo $enable_altd_mess ?> );
                                    audio_play(<?php echo $enable_altd_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_ctrle) : ?>
                                if (event.keyCode === 69 && event.ctrlKey) {
                                    show_tooltip(<?php echo $enable_ctrle_mess ?> );
                                    audio_play(<?php echo $enable_ctrle_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

        						<?php if($enable_f12) : ?>
                                if (event.keyCode === 123 || (event.keyCode === 123 && event.shiftKey)) {
                                    show_tooltip(<?php echo $enable_f12_mess ?>);
                                    audio_play(<?php echo $enable_f12_audio?>);
                                    return false;
                                }
        						<?php endif; ?>
                            } else if (BrowserDetect.OS === 'Mac') {
        						<?php if($enable_developer_tools) : ?>
                                if (isChrome || isSafari || isOpera || isFirefox) {
                                    if (event.metaKey && (
                                        event.keyCode === 73 ||
                                        event.keyCode === 74 ||
                                        event.keyCode === 69 ||
                                        event.keyCode === 75)) {
                                        show_tooltip(<?php echo $enable_developer_tools_mess ?>);
                                        audio_play(<?php echo $enable_developer_tools_audio?>);
                                        return false;
                                    }
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrls) : ?>
                                if ((event.keyCode === 83 && event.metaKey)) {
                                    show_tooltip(<?php echo $enable_ctrls_mess ?>);
                                    audio_play(<?php echo $enable_ctrls_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrla) : ?>
                                if ((event.keyCode === 65 && event.metaKey)) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrla_mess ?>);
                                            audio_play(<?php echo $enable_ctrla_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrla_mess ?>);
                                        audio_play(<?php echo $enable_ctrla_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlc) : ?>
                                if ((event.keyCode === 67 && event.metaKey)) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrlc_mess ?>);
                                            audio_play(<?php echo $enable_ctrlc_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrlc_mess ?>);
                                        audio_play(<?php echo $enable_ctrlc_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlv) : ?>
                                if ((event.keyCode === 86 && event.metaKey)) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrlv_mess ?>);
                                            audio_play(<?php echo $enable_ctrlv_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrlv_mess ?>);
                                        audio_play(<?php echo $enable_ctrlv_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlx) : ?>
                                if ((event.keyCode === 88 && event.metaKey)) {
                                    <?php if($exclude_inp_textarea) : ?>
                                        if (!(event.target.nodeName == 'INPUT' || event.target.nodeName == 'TEXTAREA')) {
                                            show_tooltip(<?php echo $enable_ctrlx_mess ?>);
                                            audio_play(<?php echo $enable_ctrlx_audio?>);
                                            return false;
                                        }
                                    <?php else: ?>
                                        show_tooltip(<?php echo $enable_ctrlx_mess ?>);
                                        audio_play(<?php echo $enable_ctrlx_audio?>);
                                        return false;
                                    <?php endif; ?>
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlu) : ?>
                                if ((event.keyCode === 85 && event.metaKey)) {
                                    show_tooltip(<?php echo $enable_ctrlu_mess ?> );
                                    audio_play(<?php echo $enable_ctrlu_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlf) : ?>
                                if ((event.keyCode === 70 && event.metaKey) || (event.keyCode === 71 && event.metaKey)) {
                                    show_tooltip(<?php echo $enable_ctrlf_mess ?> );
                                    audio_play(<?php echo $enable_ctrlf_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

        						<?php if($enable_ctrlp) : ?>
                                if ((event.keyCode === 80 && event.metaKey)) {
                                    show_tooltip(<?php echo $enable_ctrlp_mess ?> );
                                    audio_play(<?php echo $enable_ctrlp_audio?>);
                                    return false;
                                }
        						<?php endif; ?>

                                <?php if($enable_ctrlh) : ?>
                                if ((event.keyCode === 72 && event.metaKey)) {
                                    show_tooltip(<?php echo $enable_ctrlh_mess ?> );
                                    audio_play(<?php echo $enable_ctrlh_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_ctrll) : ?>
                                if ((event.keyCode === 76 && event.metaKey)) {
                                    show_tooltip(<?php echo $enable_ctrll_mess ?> );
                                    audio_play(<?php echo $enable_ctrll_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_ctrlk) : ?>
                                if ((event.keyCode === 75 && event.metaKey)) {
                                    show_tooltip(<?php echo $enable_ctrlk_mess ?> );
                                    audio_play(<?php echo $enable_ctrlk_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_ctrlo) : ?>
                                if ((event.keyCode === 79 && event.metaKey)) {
                                    show_tooltip(<?php echo $enable_ctrlo_mess ?> );
                                    audio_play(<?php echo $enable_ctrlo_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_f6) : ?>
                                if (event.keyCode === 117) {
                                    show_tooltip(<?php echo $enable_f6_mess ?> );
                                    audio_play(<?php echo $enable_f6_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_f3) : ?>
                                if (event.keyCode === 114) {
                                    show_tooltip(<?php echo $enable_f3_mess ?> );
                                    audio_play(<?php echo $enable_f3_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_altd) : ?>
                                if (event.keyCode === 68 && event.altKey) {
                                    show_tooltip(<?php echo $enable_altd_mess ?> );
                                    audio_play(<?php echo $enable_altd_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

                                <?php if($enable_ctrle) : ?>
                                if (event.keyCode === 69 && event.metaKey) {
                                    show_tooltip(<?php echo $enable_ctrle_mess ?> );
                                    audio_play(<?php echo $enable_ctrle_audio?>);
                                    return false;
                                }
                                <?php endif; ?>

        						<?php if($enable_f12) : ?>
                                if (event.keyCode === 123) {
                                    show_tooltip(<?php echo $enable_f12_mess ?>);
                                    audio_play(<?php echo $enable_f12_audio?>);
                                    return false;
                                }
        						<?php endif; ?>
                            }
                        }
                    });

                    function disableSelection(e) {
                        if (typeof e.onselectstart !== "undefined")
                            e.onselectstart = function () {
                                show_tooltip(<?php echo $enable_left_click_mess ?> );
                                audio_play(<?php echo $enable_left_click_audio?>);
                                return false
                            };
                        else if (typeof e.style.MozUserSelect !== "undefined")
                            e.style.MozUserSelect = "none";
                        else e.onmousedown = function () {
                                show_tooltip(<?php echo $enable_left_click_mess ?>);
                                audio_play(<?php echo $enable_left_click_audio?>);
                                return false
                            };
                        e.style.cursor = "default"
                    }

                    var msg_count = 1; 
                    function show_tooltip(mess) {
                        if (mess && msg_count == 1) {
                            if (tooltipClass == 'mouse_first_pos') {
                                if ($('#ays_tooltip2').length > 0) {
                                    $('#ays_tooltip2').remove();
                                }
                                var tooltip2 = tooltip.clone().prop('id','ays_tooltip2').insertBefore(tooltip);
                                $('#ays_tooltip2').addClass('ays_tooltip_class');
                                tooltip2.css({'display': 'table'});
                                $('#ays_tooltip').fadeOut();
                                setTimeout(function () {
                                    tooltip2.remove();
                                }, <?=$timeout?>);
                            }else{
                                tooltip.css({'display': 'table'});
                                setTimeout(function () {
                                    $('#ays_tooltip').fadeOut(<?=$timeout / 2?>);
                                }, <?=$timeout?>);
                            }
                        }

                        <?php if ($show_msg_only_once) :?>
                            msg_count++;
                        <?php endif; ?>
                    }

                    function audio_play(audio) {
                        if (audio) {
                            var audio = document.getElementById("sccp_public_audio");
                            if (audio) {
                                audio.currentTime = 0;
                                audio.play();
                            }

                        }
                    }


                });
            })(jQuery);
            var copyrightText = '';
            var copyrightIncludeUrl = '';
            var copyrightWord = '';
            <?php if($enable_copyright_text) : ?>

                <?php if($copyright_text != '') : ?>
                    copyrightText = ' <?php echo $copyright_text ?>';;
                 <?php endif; ?>
                
                <?php if($copyright_include_url) : ?>
                    copyrightIncludeUrl = ' ' + window.location.href;
                 <?php endif; ?>
    
                window.addEventListener("copy",function(){          
                    var text = window.navigator.clipboard.readText().then(
                        text => window.navigator.clipboard.writeText(text + copyrightText + copyrightIncludeUrl) 
                    );
                });

            <?php endif; ?>
            <?php if($sccp_enable_copyright_word) : ?>
                copyrightWord = '<?php echo $sccp_copyright_word ?>';
    
                window.addEventListener("copy", function(){
                    var text = window.navigator.clipboard.readText().then(
                        text => window.navigator.clipboard.writeText(copyrightWord + copyrightText + copyrightIncludeUrl) 
                    );
                });
            <?php endif; ?>

            var BrowserDetect = {
                init: function () {
                    this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
                    this.version = this.searchVersion(navigator.userAgent) || this.searchVersion(navigator.appVersion) || "an unknown version";
                    this.OS = this.searchString(this.dataOS) || "an unknown OS";
                },
                searchString: function (data) {
                    for (var i = 0; i < data.length; i++) {
                        var dataString = data[i].string;
                        var dataProp = data[i].prop;
                        this.versionSearchString = data[i].versionSearch || data[i].identity;
                        if (dataString) {
                            if (dataString.indexOf(data[i].subString) !== -1) return data[i].identity;
                        } else if (dataProp) return data[i].identity;
                    }
                },
                searchVersion: function (dataString) {
                    var index = dataString.indexOf(this.versionSearchString);
                    if (index === -1) return;
                    return parseFloat(dataString.substring(index + this.versionSearchString.length + 1));
                },
                dataBrowser: [{
                    string: navigator.userAgent,
                    subString: "Chrome",
                    identity: "Chrome"
                }, {
                    string: navigator.userAgent,
                    subString: "OmniWeb",
                    versionSearch: "OmniWeb/",
                    identity: "OmniWeb"
                }, {
                    string: navigator.vendor,
                    subString: "Apple",
                    identity: "Safari",
                    versionSearch: "Version"
                }, {
                    prop: window.opera,
                    identity: "Opera",
                    versionSearch: "Version"
                }, {
                    string: navigator.vendor,
                    subString: "iCab",
                    identity: "iCab"
                }, {
                    string: navigator.vendor,
                    subString: "KDE",
                    identity: "Konqueror"
                }, {
                    string: navigator.userAgent,
                    subString: "Firefox",
                    identity: "Firefox"
                }, {
                    string: navigator.vendor,
                    subString: "Camino",
                    identity: "Camino"
                }, { // for newer Netscapes (6+)
                    string: navigator.userAgent,
                    subString: "Netscape",
                    identity: "Netscape"
                }, {
                    string: navigator.userAgent,
                    subString: "MSIE",
                    identity: "Explorer",
                    versionSearch: "MSIE"
                }, {
                    string: navigator.userAgent,
                    subString: "Gecko",
                    identity: "Mozilla",
                    versionSearch: "rv"
                }, { // for older Netscapes (4-)
                    string: navigator.userAgent,
                    subString: "Mozilla",
                    identity: "Netscape",
                    versionSearch: "Mozilla"
                }],
                dataOS: [{
                    string: navigator.platform,
                    subString: "Win",
                    identity: "Windows"
                }, {
                    string: navigator.platform,
                    subString: "Mac",
                    identity: "Mac"
                }, {
                    string: navigator.userAgent,
                    subString: "iPhone",
                    identity: "iPhone/iPod"
                }, {
                    string: navigator.platform,
                    subString: "Linux",
                    identity: "Linux"
                }]
            };
            BrowserDetect.init();
        // }, false);
    </script>
<?php endif; ?>