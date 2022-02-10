
jQuery.noConflict();
(function ($) {

    var custom_uploader;
    $(document.body).on("click", ".shortcode-addons-media-control", function (e) {
        var link = $(this).siblings("input").attr('id');
        $('#oxi-addons-preview-data').prepend('<input type="hidden" id="shortcode-addons-body-image-upload-hidden" value="#' + link + '" />');
        e.preventDefault();
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        custom_uploader.on('select', function () {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            var url = attachment.url;
            var alt = attachment.alt;
            if ((jQuery("#oxi-addons-list-data-modal").data('bs.modal') || {})._isShown) {
                jQuery("#oxi-addons-list-data-modal").css({
                    "overflow-x": "hidden",
                    "overflow-y": "auto"

                });
            }

            var lnkdata = $("#shortcode-addons-body-image-upload-hidden").val();
            $(lnkdata).val(url).change();
            $(lnkdata+'-alt').val(alt).change();
            $(lnkdata).siblings('.shortcode-addons-media-control').removeClass('shortcode-addons-media-control-hidden-button');
            $(lnkdata).siblings('.shortcode-addons-media-control').children('.shortcode-addons-media-control-image-load').css('background-image', 'url(' + url + ')');
        });
        custom_uploader.open();
    }).on('click', '.shortcode-addons-media-control-image-load-delete-button', function (e) {
        $(this).parent().parent().addClass('shortcode-addons-media-control-hidden-button');
        $(this).parent('.shortcode-addons-media-control-image-load').css('background-image', 'url()');
        $(this).parent().parent().siblings('input').val("").change();
        e.stopPropagation();
    });

})(jQuery)