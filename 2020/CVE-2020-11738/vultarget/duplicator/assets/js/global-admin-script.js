jQuery(document).ready(function($) {
    $('div.notice.duplicator-message-dismissed, .duplicator-message .notice-dismiss').on('click', function (event) {
        event.preventDefault();
        $.post(ajaxurl, {
            action: 'duplicator_set_admin_notice_viewed',
            notice_id: $(this).closest('.duplicator-message-dismissed').data('notice_id')
        });
        var $wrapperElm = $(this).closest('.duplicator-message-dismissed');
        $wrapperElm.fadeTo(100, 0, function () {
            $wrapperElm.slideUp(100, function () {
                $wrapperElm.remove();
            });
        });
    });   
});

