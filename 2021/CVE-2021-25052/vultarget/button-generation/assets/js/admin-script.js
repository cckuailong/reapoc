/* ========= INFORMATION ============================
	- document:  Button Generator!
	- author:    Wow-Company
	- copyright: 2019 Wow-Company
	- version:   1.0
	- email:     support@wow-company.com
==================================================== */

'use strict';

(function ($) {
  $("#wow-plugin").on('submit', function (event) {
    event.preventDefault();
    var dataform = $(this).serialize();
    var dataform = 'action=wow_item_save&' + dataform;
    $('.wow-plugin .saving').animate({opacity: "0.75"});

    $.post(ajaxurl, dataform, function (response) {
      if (response.status == 'OK') {
        $('#wow-message').addClass('notice notice-success is-dismissible');
        $('#wow-message').html('<p>' + response.message + '</p>');
        $('#add_action').val(2);
      }
      $('.wow-plugin .saving').animate({opacity: "0"});
    });

  });

  $('.wow-plugin .tab-nav li:first').addClass('select');
  $('.wow-plugin .tab-panels>div').hide().filter(':first').show();
  $('.wow-plugin .tab-nav a').on('click', function () {
    $('.wow-plugin .tab-panels>div').hide().filter(this.hash).show();
    $('.wow-plugin .tab-nav li').removeClass('select');
    $(this).parent().addClass('select');
    return false;
  });

  $('.wow-plugin input:checkbox:checked').each(function () {
    $(this).siblings('input[type="hidden"]').val('1');
  });

  $('.wow-plugin input:checkbox').on('click', function () {
      checkboxchecked(this);
    }
  );

  //* Include colorpicker
  $('.wp-color-picker-field').wpColorPicker();

  $('#icon').fontIconPicker({
    theme: 'fip-darkgrey',
    emptyIcon: false,
    allCategoryText: 'Show all'
  });


  $('input#depending_language:checkbox').each(function () {
    languages(this);
  });

  $('select#show').each(function () {
    showchange(this);
  });

  $('input.item_user:radio:checked').each(function () {
    usersroles(this);
  });

  $('input#include_mobile:checkbox').each(function () {
    screen_less(this);
  });

  $('input#include_more_screen:checkbox').each(function () {
    screen_more(this);
  });

  $('select.item-type').each(function () {
    itemtype(this);
  });

  $('body').on('hover', '.wow-help', function () {
    if ( $(this).hasClass( "dashicons-lock" ) ) {
      $(this).removeClass('dashicons-lock');
      $(this).addClass('dashicons-unlock');
    }
    else if ( $(this).hasClass( "dashicons-unlock" ) ){
      $(this).removeClass('dashicons-unlock');
      $(this).addClass('dashicons-lock');
    }
  })

  wow_attach_tooltips($(".wow-help"));

  buttontype();
  buttonlocation();
  buttonappearance();
  enablebadge();
  border();
  shadowblock();
  borderbadge();
  badgetype();

  $('[data-share]').on('click', function(event) {
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
        action: 'button_generation_message',
      },
    });
  });

})(jQuery);

  function buttontype() {
    var type = jQuery('#type').val();
    jQuery('.button-floating').css('display', 'none');
    if (type == 'floating') {
      jQuery('.button-floating').css('display', 'block');
    }
  }

  function buttonlocation() {
    var loc = jQuery('#location').val();
    jQuery('.top-bottom').css('visibility', 'visible');
    jQuery('.left-right').css('visibility', 'visible');
    jQuery('#lg-top').css('display', 'none');
    jQuery('#lg-bottom').css('display', 'none');
    jQuery('#lg-left').css('display', 'none');
    jQuery('#lg-right').css('display', 'none');
    if (loc == 'topLeft') {
      jQuery('#lg-top').css('display', 'block');
      jQuery('#lg-left').css('display', 'block');
    } else if (loc == 'topCenter') {
      jQuery('#lg-top').css('display', 'block');
      jQuery('.left-right').css('visibility', 'hidden');
    } else if (loc == 'topRight') {
      jQuery('#lg-top').css('display', 'block');
      jQuery('#lg-right').css('display', 'block');
    } else if (loc == 'bottomLeft') {
      jQuery('#lg-bottom').css('display', 'block');
      jQuery('#lg-left').css('display', 'block');
    } else if (loc == 'bottomCenter') {
      jQuery('#lg-bottom').css('display', 'block');
      jQuery('.left-right').css('visibility', 'hidden');
    } else if (loc == 'bottomRight') {
      jQuery('#lg-bottom').css('display', 'block');
      jQuery('#lg-right').css('display', 'block');
    } else if (loc == 'left') {
      jQuery('.top-bottom').css('visibility', 'hidden');
      jQuery('#lg-left').css('display', 'block');
    } else if (loc == 'right') {
      jQuery('.top-bottom').css('visibility', 'hidden');
      jQuery('#lg-right').css('display', 'block');
    }
  }

  function buttonappearance() {
    var type = jQuery('#appearance').val();
    jQuery('.button-text').css('display', 'none');
    jQuery('.button-icon').css('display', 'none');
    jQuery('.text-location').css('visibility', 'hidden');
    if (type == 'text') {
      jQuery('.button-text').css('display', 'block');
    } else if (type == 'text_icon') {
      jQuery('.button-text').css('display', 'block');
      jQuery('.button-icon').css('display', 'block');
      jQuery('.text-location').css('visibility', 'visible');
    } else if (type == 'icon') {
      jQuery('.button-icon').css('display', 'block');
    }
  }

  function badgetype() {
    let type = jQuery('#badge_type').val();
    if (type === 'actions') {
      jQuery('.badge-content').css('visibility', 'hidden');
    } else {
      jQuery('.badge-content').css('visibility', 'visible');
    }
  }

  function enablebadge() {
    if (jQuery('#enable_badge').is(':checked')) {
      jQuery('#notification-bage').css('visibility', 'visible');
      jQuery('.notification-bage').css('display', 'block');
    } else {
      jQuery('#notification-bage').css('visibility', 'hidden');
      jQuery('.notification-bage').css('display', 'none');
    }
  }


  function border() {
    var border = jQuery('#border_style').val();
    if (border == 'none') {
      jQuery('.border').css('display', 'none');
    } else {
      jQuery('.border').css('display', 'block');
    }
  }

  function borderbadge() {
    var border = jQuery('#badge_border_style').val();
    if (border == 'none') {
      jQuery('.border-badge').css('display', 'none');
    } else {
      jQuery('.border-badge').css('display', 'block');
    }
  }

  function shadowblock() {
    var shadow = jQuery('#shadow').val();
    if (shadow == 'none') {
      jQuery('.shadow').css('visibility', 'hidden');
      jQuery('.shadow-block').css('display', 'none');

    } else {
      jQuery('.shadow').css('visibility', 'visible');
      jQuery('.shadow-block').css('display', '');
    }
  }

//* Change item type
  function itemtype(that) {
    var type = jQuery(that).val();
    var parent = jQuery(that).parents('.container');
    jQuery(parent).find('.type-link-blank').css('visibility', 'hidden ');
    jQuery(that).parents('.menu_block').find('.button_id').css('visibility', 'visible');
    jQuery(that).parents('.menu_block').find('.button_class').css('visibility', 'visible');
    if (type === 'link' || type === 'smoothscroll' || type === 'email' || type === 'telephone') {
      jQuery(parent).find('.type-link').css('display', 'block');
      jQuery(parent).find('.type-share').css('display', 'none');
      jQuery(parent).find('.type-modal').css('display', 'none');
      jQuery(parent).find('.type-link-text').text('Link');
      if (type === 'link') {
        jQuery(parent).find('.type-link-blank').css('visibility', 'visible');
      } else if (type === 'email') {
        jQuery(parent).find('.type-link-text').text('Email');
      } else if (type === 'telephone') {
        jQuery(parent).find('.type-link-text').text('Telephone');
      }

    } else if (type === 'share') {
      jQuery(parent).find('.type-link').css('display', 'none');
      jQuery(parent).find('.type-share').css('display', 'block');
      jQuery(parent).find('.type-modal').css('display', 'none');
    } else if (type === 'login' || type === 'logout' || type === 'lostpassword') {
      jQuery(parent).find('.type-link').css('display', 'block');
      jQuery(parent).find('.type-share').css('display', 'none');
      jQuery(parent).find('.type-link-text').text('Redirect URL');
      jQuery(parent).find('.type-modal').css('display', 'none');

    } else {
      jQuery(parent).find('.type-link').css('display', 'none');
      jQuery(parent).find('.type-share').css('display', 'none');
      jQuery(parent).find('.type-modal').css('display', 'none');
    }
  }

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


  function checkboxchecked(that) {
    if (jQuery(that).prop('checked')) {
      jQuery(that).siblings('input[type="hidden"]').val('1');
    } else {
      jQuery(that).siblings('input[type="hidden"]').val('0');
    }
  }


//* Show language
  function languages(that) {
    if (jQuery(that).is(':checked')) {
      jQuery('#language').css('display', '');
    } else {
      jQuery('#language').css('display', 'none');
    }
  }

//* When show
  function showchange(that) {
    var show = jQuery(that).val();
    if (show === 'posts' || show === 'pages' || show === 'expost' || show === 'expage' || show === 'taxonomy') {
      jQuery('#id_post').css('display', '');
      jQuery('#shortcode').css('display', 'none');
    } else if (show === 'shortecode') {
      jQuery('#shortcode').css('display', '');
      jQuery('#id_post').css('display', 'none');
    } else {
      jQuery('#shortcode').css('display', 'none');
      jQuery('#id_post').css('display', 'none');
    }
    if (show === 'taxonomy') {
      jQuery('#taxonomy').css('display', '');
    } else {
      jQuery('#taxonomy').css('display', 'none');
    }
  }

//* Show screen
  function screen_less(that) {
    if (jQuery(that).is(':checked')) {
      jQuery('#screen').css('display', '');
    } else {
      jQuery('#screen').css('display', 'none');
    }
  }

  function screen_more(that) {
    if (jQuery(that).is(':checked')) {
      jQuery('#screenmore').css('display', '');
    } else {
      jQuery('#screenmore').css('display', 'none');
    }
  }

  function usersroles(that) {
    var users = jQuery(that).val();
    if (users == 2) {
      jQuery('#users_roles').css('display', '');
    } else {
      jQuery('#users_roles').css('display', 'none');
    }
  }

  function resetcounts(tool_id) {
    var data = 'action=btg_count&count_type=reset&tool_id=' + tool_id;
    jQuery.post(btg_count.ajaxurl, data, function (msg) {
      var result = msg.result;
      if (result == 'OK') {
        jQuery('#tool_view').html('0');
        jQuery('#tool_action').html('0');
        jQuery('#conversion').html('0%');
      }
    });
  }