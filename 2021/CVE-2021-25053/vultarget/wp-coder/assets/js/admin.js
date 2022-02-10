/* ========= INFORMATION ============================
	- author:    Dmytro Lobov
	- url:       https://wow-estore.com
	- email:     givememoney1982@gmail.com
==================================================== */

'use strict';

jQuery(document).ready(function ($) {
    //* Include colorpicker

    $('.wow-plugin .tab-nav li:first').addClass('select');
    $('.wow-plugin .tab-panels>div').hide().filter(':first').show();
    $('.wow-plugin .tab-nav a').click(function () {
        $('.wow-plugin .tab-panels>div').hide().filter(this.hash).show();
        $('.wow-plugin .tab-nav li').removeClass('select');
        $(this).parent().addClass('select');
        return false;
    });
    $('.wow-plugin input:checkbox:checked').each(function () {
        let str = $(this).attr("id");
        let check = str.replace("wow_", "");
        $("input[name='param[" + check + "]']").val(1);
    });

    $('.wow-plugin input[type="checkbox"]').change(function () {
        let str = $(this).attr("id");
        let check = str.replace("wow_", "");
        if ($(this).prop('checked')) {
            $("input[name='param[" + check + "]']").val(1);
        } else {
            $("input[name='param[" + check + "]']").val(0);
        }
    });

    $('.item-title').children('.faq-title').click(function () {
        let par = $(this).closest('.items');
        $(par).children(".inside").toggle(500);
        if ($(this).hasClass('togglehide')) {
            $(this).removeClass('togglehide');
            $(this).addClass("toggleshow");
            $(this).attr('title', 'Show');
        } else {
            $(this).removeClass('toggleshow');
            $(this).addClass("togglehide");
            $(this).attr('title', 'Hide');
        }
    });

    wow_attach_tooltips($(".wow-help"));

    $('[data-share]').on('click', function (event) {
        event.preventDefault();
        let network = $(this).data('share');
        let url = $('#wp-url').val();
        let title = $('#wp-title').val();

        let shareUrl;

        switch (network) {
            case 'facebook':
                shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + url;
                break;
            case 'vk':
                shareUrl = 'http://vk.com/share.php?url=' + url;
                break;
            case 'twitter':
                shareUrl = 'https://twitter.com/share?url=' + url + '&text=' + title;
                break;
            case 'linkedin':
                shareUrl = 'https://www.linkedin.com/shareArticle?url=' + url + '&title=' + title;
                break;
            case 'pinterest':
                shareUrl = 'https://pinterest.com/pin/create/button/?url=' + url;
                break;
            case 'xing':
                shareUrl = 'https://www.xing.com/spi/shares/new?url=' + url;
                break;
            case 'reddit':
                shareUrl = 'http://www.reddit.com/submit?url=' + url + '&title=' + title;
                break;
            case 'blogger':
                shareUrl = 'https://www.blogger.com/blog-this.g?u=' + url + '&n=' + title;
                break;
            case 'telegram':
                shareUrl = 'https://telegram.me/share/url?url=' + url + '&text=' + title;
                break;


            default:
                shareUrl = '';
        }

        let popupWidth = 550;
        let popupHeight = 450;
        let topPosition = (screen.height - popupHeight) / 2;
        let leftPosition = (screen.width - popupWidth) / 2;
        let popup = 'width=' + popupWidth + ', height=' + popupHeight + ', top=' + topPosition + ', left=' + leftPosition +
            ', scrollbars=0, resizable=1, menubar=0, toolbar=0, status=0';

        window.open(shareUrl, null, popup);

    });

    $(document).on('click', '.wow-plugin-message .notice-dismiss', function() {
        $.ajax({
            url: ajaxurl, data: {
                action: 'wp_coder_message',
            },
        });
    });

});


function wow_attach_tooltips(selector) {
    selector.tooltip({
        content: function () {
            return jQuery(this).prop("title")
        },
        tooltipClass: "wow-ui-tooltip",
        position: {
            my: "center top",
            at: "center bottom+10",
            collision: "flipfit"
        },
        hide: {
            duration: 200
        },
        show: {
            duration: 200
        }
    })
}

