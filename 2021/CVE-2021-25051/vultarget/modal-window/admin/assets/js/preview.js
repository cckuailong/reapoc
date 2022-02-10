/* ========= INFORMATION ============================
	- author:    Wow-Company
	- url:       https://wow-estore.com
==================================================== */

'use strict';

(function ($) {

    let content = $('#popup_content').val();
    $('.modal-window-content').html(content);

    $('#postoptions').on('change', function () {
        builder();
    });

    $('#postoptions').on('keyup', function () {
        builder();
    });

    $(".wp-color-picker-field").wpColorPicker(
        'option',
        'change',
        function (event, ui) {
            builder();
        }
    );

    builder();

    function builder() {
        $('.wow-modal-overlay').removeAttr('style');
        $('.wow-modal-window').removeAttr('style');

        if ($('#include_overlay').is(':checked')) {
            let background = $('#overlay_color').val();
            $('.wow-modal-overlay').css("background", background);
        }

        // Modal window style
        let modal_width = $('#modal_width').val();
        let modal_width_par = $('#modal_width_par').val();
        if (modal_width_par === 'pr') {
            modal_width_par = '%';
        }
        $('.wow-modal-window').css("width", modal_width+modal_width_par);

        let modal_height = $('#modal_height').val();
        let modal_height_par = $('#modal_height_par').val();

        if (modal_height_par === 'pr') {
            modal_height_par = '%';
        }

        let height = modal_height+modal_height_par;
        if(modal_height_par === 'auto') {
            height = 'auto';
        }

        $('.wow-modal-window').css("height", height);

        let bg_color = $('#bg_color').val();
        $('.wow-modal-window').css("background-color", bg_color);

        let modal_padding = $('#modal_padding').val();
        let content_size = $('#content_size').val();
        let content_font = $('#content_font').val();
        let border_radius = $('#border_radius').val();
        let border_style = $('#border_style').val();
        let border_width = $('#border_width').val();
        let border_color = $('#border_color').val();

        $('.wow-modal-window').css({
            'padding' : modal_padding + 'px',
            'font-size': content_size + 'px',
            'font-family': content_font,
            'border-radius': border_radius + 'px',
            'border' : border_width + 'px ' +border_style + ' '+border_color,
        });
        $('.wow-modal-window p').css({
            'font-size': content_size + 'px',
        });

        // Shadow
        let shadow = $('#shadow').val();
        let shadow_h_offset = $('#shadow_h_offset').val() + 'px ';
        let shadow_v_offset = $('#shadow_v_offset').val() + 'px ';
        let shadow_blur = $('#shadow_blur').val() + 'px ';
        let shadow_spread = $('#shadow_spread').val() + 'px ';
        let shadow_color = $('#shadow_color').val();

        if (shadow == 'outset') {
            $('.wow-modal-window').css({
                'box-shadow': shadow_h_offset + shadow_v_offset + shadow_blur + shadow_spread + shadow_color,
            });
        } else if (shadow == 'inset') {
            $('.wow-modal-window').css({
                'box-shadow': 'inset ' + shadow_h_offset + shadow_v_offset + shadow_blur + shadow_spread + shadow_color,
            });
        }
        $( ".mw-title" ).remove();
        if ($('#popup_title').is(':checked')) {
            let title = $('#item-title').val();
            $(".wow-modal-window").prepend('<div class="mw-title">'+title+'</div>');

            let title_size = $('#title_size').val() + 'px';
            let title_line_height = $('#title_line_height').val() + 'px';
            let title_font = $('#title_font').val();
            let title_font_weight = $('#title_font_weight').val();
            let title_font_style = $('#title_font_style').val();
            let title_align = $('#title_align').val();
            let title_color = $('#title_color').val();

            $('.mw-title').css({
                'font-size': title_size,
                'line-height': title_line_height,
                'font-family': title_font,
                'font-weight': title_font_weight,
                'font-style': title_font_style,
                'text-align': title_align,
                'color': title_color,
            });

        }

        let builder_height = $('.wow-modal-window').outerHeight() +50;
        $('.live-builder').css({
            'height': builder_height + 'px',
        });

    }

    $('#popup_content').on('keydown', function () {
        let content = $('#popup_content').val();
        $('.modal-window-content').html(content);
        builder();
    });

    window.onload = function () {
        if (typeof window.parent.tinymce !== 'undefined') {
            tinymce.get('popup_content').on('keydown', function (e) {
                let content = this.getContent();
                $('.modal-window-content').html(content);
                builder();
            });
            tinymce.get('popup_content').on('change', function (e) {
                let content = this.getContent();
                $('.modal-window-content').html(content);
                builder();
            });
        }
    }

})(jQuery);




