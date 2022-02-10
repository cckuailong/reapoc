
jQuery.noConflict();
(function ($) {
    "use strict";
    $(document).on("click", ".oxi-image-admin-recommended-dismiss", function (e) {
        e.preventDefault();
        var notice = $(this).attr('sup-data'),
                $function = 'oxi_recommended';
        $.ajax({
            url: ImageHoverUltimate.root + 'ImageHoverUltimate/v1/' + $function,
            method: 'POST',

            data: {
                _wpnonce: ImageHoverUltimate.nonce,
                notice: notice,
            }
        }).done(function (response) {
            $('.oxi-addons-admin-notifications').remove();
        });
        return false;
    });
})(jQuery);
