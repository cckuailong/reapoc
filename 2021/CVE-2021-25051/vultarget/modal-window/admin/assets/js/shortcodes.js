/**
 * main.js
 *
 * Handles toggling the navigation menu for small screens.
 */

'use strict';

(function ($) {

    $("#shortcodeInsert").on("click", function () {
        let shortcode = $('#shortcodeBox').text();

        if (jQuery('#wp-popup_content-editor-container > textarea').is(':visible')) {
            let val = jQuery('#wp-popup_content-editor-container > textarea').val() + shortcode;
            jQuery('#wp-popup_content-editor-container > textarea').val(val);
        } else {
            tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        }
        tb_remove();
    });

    $('#shortcodeBuilder').on('change', function () {
        buildShortcode();
    });

    $('#shortcodeBuilder').on('keyup', function () {
        buildShortcode();
    });

    $("#shortcodeBuilder .wp-color-picker-field").wpColorPicker(
        'option',
        'change',
        function (event, ui) {
            buildShortcode();
        }
    );

    $("#shortcode_type").on("click", function () {
        shortCodeType();
        buildShortcode();
    });
    $("#shortcode_btn_type").on("click", function () {
        shortcodeBtnType();
        buildShortcode();
    });

    shortcodeBtnType();
    shortCodeType();
    buildShortcode();

    function shortCodeType() {
        let shortcode = $('#shortcode_type').val();
        $('.button-box, .video-box, .icon-box, .shortcode-preview').addClass('is-hidden');
        if (shortcode === 'button') {
            $('.button-box, .shortcode-preview').removeClass('is-hidden');
        } else if (shortcode === 'video') {
            $('.video-box').removeClass('is-hidden');
        } else if (shortcode === 'icon') {
            $('.icon-box, .shortcode-preview').removeClass('is-hidden');
        }
    }

    function shortcodeBtnType() {
        let button = $('#shortcode_btn_type').val();
        $('.shortcode-btn-link').addClass('is-hidden');
        if (button === 'link') {
            $('.shortcode-btn-link').removeClass('is-hidden');
        }
    }

    function buildShortcode() {
        let type = $('#shortcode_type').val();
        let video_from = $('#shortcode_video_from').val();
        let video_id = $('#shortcode_video_id').val();
        let video_width = $('#shortcode_video_width').val();
        let video_height = $('#shortcode_video_height').val();
        let button = $('#shortcode_btn_type').val();
        let btn_size = $('#shortcode_btn_size').val();
        let btn_fullwidth = $('#shortcode_btn_fullwidth').val();
        let btn_text = $('#shortcode_btn_text').val();
        let btn_color = $('#shortcode_btn_color').val();
        let btn_bgcolor = $('#shortcode_btn_bgcolor').val();
        let btn_link = $('#shortcode_btn_link').val();
        let btn_target = $('#shortcode_btn_target').val();

        let name_icon = $("#icongenerate :selected").text();
        let color_icon = $('#color_icon').val();
        let size_icon = $('#size_icon').val();
        let link_icon = $('#link_icon').val();
        let target_icon = $('#target_icon').val();

        let shortcode;
        if (type === 'video') {
            shortcode = '[videoBox from="' + video_from + '" id="' + video_id + '" width="' + video_width + '" height="' + video_height + '"]';
        } else if (type === 'button') {
            let fullwidth;
            if (btn_fullwidth === '') {
                fullwidth = 'no';
            } else {
                fullwidth = 'yes';
                btn_fullwidth = 'is-fullwidth'
            }
            let btn_param = 'type="' + button + '" color="' + btn_color + '" bgcolor="' + btn_bgcolor + '" size="' + btn_size + '" fullwidth="' + fullwidth + '"';
            if (button === 'link') {
                btn_param += ' link="' + btn_link + '" target="' + btn_target + '"';
            }
            shortcode = '[buttonBox ' + btn_param + ']' + btn_text + '[/buttonBox]';

            let content_size = $('#content_size').val();
            $('#shortcodeBtnPreview').css({
                'font-size': content_size + 'px',
            });
            let style = 'color:' + btn_color + ';background:' + btn_bgcolor + ';';
            let btn_preview = '<button class="ds-button is-' + btn_size + ' ' + btn_fullwidth + '" style="' + style + '">' + btn_text + '</button>';
            $('#shortcodeBtnPreview').html(btn_preview);
        } else if (type === 'icon') {
            if (link_icon !== '') {
                shortcode = '[wow-icon name="' + name_icon + '" color="' + color_icon + '" size="' + size_icon + '" link="' + link_icon + '" target="' + target_icon + '"]';
            } else {
                shortcode = '[wow-icon name="' + name_icon + '" color="' + color_icon + '" size="' + size_icon + '"]';
            }
            let icon_preview = '<i class="' + name_icon + '" style="color:' + color_icon + ';font-size:' + size_icon + 'px;"></i>';

            $('#shortcodeBtnPreview').html(icon_preview);
        }

        $('#shortcodeBox').text(shortcode);
    }

})(jQuery);