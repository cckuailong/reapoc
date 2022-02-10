
jQuery.noConflict();
(function ($) {
    "use strict";
    $(document).on("click", ".oxi-image-support-reviews", function (e) {
        e.preventDefault();
        var notice = $(this).attr('sup-data'),
                $function = 'notice_dissmiss';
        $.ajax({
            url: ImageHoverUltimate.root + 'ImageHoverUltimate/v1/' + $function,
            method: 'POST',

            data: {
                _wpnonce: ImageHoverUltimate.nonce,
                notice: notice,
            }
        }).done(function (response) {
            $('.oxilab-image-hover-review-notice').remove();
        });
    });
})(jQuery);
