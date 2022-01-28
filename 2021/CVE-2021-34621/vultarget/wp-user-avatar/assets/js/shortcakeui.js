jQuery(document).ready(function ($) {
    $('.pp-insert-shortcake').click(function () {
        var wp_media_frame = wp.media.frames.wp_media_frame = wp.media({
            frame: "post",
            state: 'shortcode-ui'
        });
        wp_media_frame.open();
    });
});