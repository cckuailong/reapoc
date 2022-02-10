/* ========= INFORMATION ============================
	- author:    Dmytro Lobov
	- url:       https://wow-estore.com
==================================================== */
'use strick';

(function ($) {

    //region Send Form
    $('#wow-plugin').on('submit', function (event) {
        event.preventDefault();
        getTinymceContent();
        let dataform = $(this).serialize();
        let prefix = $('#prefix').val();
        let data = 'action=' + prefix + '_item_save&' + dataform;
        $('#submit').addClass('is-loading');
        setTimeout(function () {
            $.post(ajaxurl, data, function (response) {
                if (response.status == 'OK') {
                    $('#wow-message').addClass('notice notice-success is-dismissible');
                    $('#wow-message').html('<p>' + response.message + '</p>');
                    $('#add_action').val(2);
                    let tool_id = $('#tool_id').val();
                    $('.nav-tab.nav-tab-active').text('Update #' + tool_id);
                }
                $('#submit').removeClass('is-loading');
            });
        }, 500);
    });
    //endregion

    //region Tabs
    $('#tab li').on('click', function () {
        let tab = $(this).data('tab');
        $('#tab li').removeClass('is-active');
        $(this).addClass('is-active');
        $('#tab-content .tab-content').removeClass('is-active');
        $('[data-content="' + tab + '"]').addClass('is-active');
    });
    //endregion

    // Install Icon picker
    $('.icons').not('#clone .icons').fontIconPicker({
        theme: 'fip-darkgrey', emptyIcon: false, allCategoryText: 'Show all',
    });

    // Install the Icon Color
    $('.wp-color-picker-field').not('#clone .wp-color-picker-field').wpColorPicker();

    $('.toggle-preview').on('click', function () {
        $('.live-builder, .toggle-preview .plus, .toggle-preview .minus').toggleClass('is-hidden');
    });

    //region Accordion
    $('.accordion-title').on('click', function () {
        $('.accordion-title').removeClass('active');
        $('.accordion-content').slideUp('normal');
        if ($(this).next().is(':hidden') == true) {
            $(this).addClass('active');
            $(this).next().slideDown('normal');
        }
    });
    $('.accordion-content').hide();
    //endregion

    //region Save item
    $(document).on('click', '.wow-plugin-message .notice-dismiss', function () {
        let prefix = $('#prefix').val();
        $.ajax({
            url: ajaxurl, data: {
                action: prefix + '_message',
            },
        });
    });
    //endregion

    //region Share pluign
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
    //endregion



    //region Check Label
    $('.checkLabel')
        .each(function () {
            checkLabel(this);
        })
        .on('click', function () {
            checkLabel(this);
        });
    //endregion

    $('.checkBlock')
        .each(function () {
            checkBlock(this);
        })
        .on('click', function () {
            checkBlock(this);
        });


    closeLocation();
    closeType();
    enableTitle();
    triggers();
    useCookies();
    youtubeSupport();
    displayButton();
    buttonType();
    buttonPosition();

    setDate();
    userRole();
    showChange();

    shadowEnabled();

})(jQuery);

function checkLabel(that) {
    if (jQuery(that).prop('checked')) {
        jQuery(that).parent( 'label' ).siblings('.field').removeClass('is-hidden');
    } else {
        jQuery(that).parent( 'label' ).siblings('.field').addClass('is-hidden');
    }
}

function checkBlock(that) {
    if (jQuery(that).prop('checked')) {
        jQuery(that).closest( '.columns' ).children('.blockHidden').removeClass('is-hidden');
    } else {
        jQuery(that).closest( '.columns' ).children('.blockHidden').addClass('is-hidden');
    }
}

function getTinymceContent() {
    if (jQuery("#wp-popup_content-wrap").hasClass("tmce-active")) {
        let content = tinyMCE.get('popup_content').getContent();
        jQuery('#popup_content').val(content);
    }
    if (jQuery("#wp-admincontent-wrap").hasClass("tmce-active")) {
        let content = tinyMCE.get('admincontent').getContent();
        jQuery('#admincontent').val(content);
    }
    if (jQuery("#wp-usercontent-wrap").hasClass("tmce-active")) {
        let content = tinyMCE.get('usercontent').getContent();
        jQuery('#usercontent').val(content);
    }
}

function closeType() {
    let type = jQuery('#close_type').val();
    jQuery('.close-text, .close-icon').addClass('is-hidden');
    if (type === 'text') {
        jQuery('.close-text').removeClass('is-hidden');
    } else {
        jQuery('.close-icon').removeClass('is-hidden');
    }
}

function enableTitle() {
    if (jQuery('#popup_title').is(':checked')) {
        jQuery('.popup-title').removeClass('is-hidden');
    } else {
        jQuery('.popup-title').addClass('is-hidden');
    }
}

function closeLocation() {

    let loc = jQuery('#close_location').val();
    jQuery('#close-top, #close-bottom, #close-left, #close-right').addClass('is-hidden');

    if (loc == 'topLeft') {
        jQuery('#close-top').removeClass('is-hidden');
        jQuery('#close-left').removeClass('is-hidden');
    } else if (loc == 'topRight') {
        jQuery('#close-top').removeClass('is-hidden');
        jQuery('#close-right').removeClass('is-hidden');
    } else if (loc == 'bottomLeft') {
        jQuery('#close-bottom').removeClass('is-hidden');
        jQuery('#close-left').removeClass('is-hidden');
    } else if (loc == 'bottomRight') {
        jQuery('#close-bottom').removeClass('is-hidden');
        jQuery('#close-right').removeClass('is-hidden');
    }
}

function triggers() {
    let trigger = jQuery('#modal_show').val();
    if (trigger === 'scroll') {
        jQuery('.scrolled').removeClass('is-hidden');
    } else {
        jQuery('.scrolled').addClass('is-hidden');
    }
}

function useCookies() {
    let cookie = jQuery('#use_cookies').val();
    if (cookie === 'yes') {
        jQuery('.cookie').removeClass('is-hidden');
    } else {
        jQuery('.cookie').addClass('is-hidden');
    }
}

function youtubeSupport() {
    let youtube = jQuery('#video_support').val();
    if (youtube === '2') {
        jQuery('.youtube').removeClass('is-hidden');
    } else {
        jQuery('.youtube').addClass('is-hidden');
    }
}

function setDate() {
    if (jQuery('#set_dates').prop('checked')) {
        jQuery('.date-set').removeClass('is-hidden');
    } else {
        jQuery('.date-set').addClass('is-hidden');
    }
}

function userRole() {
    let user = jQuery('#item_user').val();
    if (user === '2') {
        jQuery('.user-role').removeClass('is-hidden');
    } else {
        jQuery('.user-role').addClass('is-hidden');
    }
}

function showChange() {
    let show = jQuery('#show').val();
    if (show === 'posts' || show === 'pages' || show === 'expost' || show === 'expage' || show === 'taxonomy' || show === 'postsincat') {
        jQuery('.id-post').removeClass('is-hidden');
        jQuery('.shortcode').addClass('is-hidden');
    } else if (show === 'shortecode') {
        jQuery('.shortcode').removeClass('is-hidden');
        jQuery('.id-post').addClass('is-hidden');
    } else {
        jQuery('.shortcode').addClass('is-hidden');
        jQuery('.id-post').addClass('is-hidden');
    }
    if (show === 'taxonomy') {
        jQuery('.taxonomy').removeClass('is-hidden');
    } else {
        jQuery('.taxonomy').addClass('is-hidden');
    }
}

function displayButton() {
    let button = jQuery('#umodal_button').val();
    if (button === 'yes') {
        jQuery('.show-button').removeClass('is-hidden');
    } else {
        jQuery('.show-button').addClass('is-hidden');
    }
    buttonType();
}

function buttonType() {
    let button = jQuery('#umodal_button').val();
    let type = jQuery('#button_type').val();
    if (button === 'yes') {
        if (type === '1') {
            jQuery('.button-text').removeClass('is-hidden');
            jQuery('.button-icon').addClass('is-hidden');
        } else if (type === '2') {
            jQuery('.button-text').removeClass('is-hidden');
            jQuery('.button-icon').removeClass('is-hidden');
            jQuery('.button-text-icon').removeClass('is-hidden');
            jQuery('.button-shape').addClass('is-hidden');
        } else if (type === '3') {
            jQuery('.button-text').addClass('is-hidden');
            jQuery('.button-icon').removeClass('is-hidden');
            jQuery('.button-text-icon').addClass('is-hidden');
            jQuery('.button-shape').removeClass('is-hidden');
        }
    }
}

function buttonPosition() {
    let position = jQuery('#umodal_button_position').val();

    if (position === 'wow_modal_button_right') {
        jQuery('.button-position label').text('Top position');
        jQuery('.button-margin label').text('Margin-right');
    } else if (position === 'wow_modal_button_left') {
        jQuery('.button-position label').text('Top position');
        jQuery('.button-margin label').text('Margin-left');
    } else if (position === 'wow_modal_button_top') {
        jQuery('.button-position label').text('Left position');
        jQuery('.button-margin label').text('Margin-top');
    } else if (position === 'wow_modal_button_bottom') {
        jQuery('.button-position label').text('Left position');
        jQuery('.button-margin label').text('Margin-bottom');
    }
}

function shadowEnabled() {
    let shadow = jQuery('#shadow').val();
    if(shadow === 'none') {
        jQuery('.shadow-block').addClass('is-hidden');
    } else {
        jQuery('.shadow-block').removeClass('is-hidden');
    }
}