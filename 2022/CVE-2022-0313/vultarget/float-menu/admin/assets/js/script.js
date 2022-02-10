'use strict';

(function($) {

  // Submit Form for save
  $('#wow-plugin').on('submit', function(event) {
    event.preventDefault();
    get_tinymce_content();
    const dataform = $(this).serialize();
    let prefix = $('#prefix').val();
    let data = 'action=' + prefix + '_item_save&' + dataform;
    $('.wow-plugin .saving').animate({opacity: '0.75'});
    $.post(ajaxurl, data, function(response) {
      if (response.status == 'OK') {
        $('#wow-message').addClass('notice notice-success is-dismissible');
        $('#wow-message').html('<p>' + response.message + '</p>');
        $('#add_action').val(2);
        let tool_id = $('#tool_id').val();
        $('.nav-tab.nav-tab-active').text('Update #' + tool_id);
      }
      $('.wow-plugin .saving').animate({opacity: '0'});
    });
  });

  // Get Tinymce contetn
  function get_tinymce_content() {
    if ($('#wp-popupcontent-wrap').hasClass('tmce-active')) {
      let content = tinyMCE.activeEditor.getContent();
      $('#popupcontent').val(content);
    }
  }

  // Tabs
  $('#tab li').on('click', function() {
    const selected = $(this).data('tab');
    $('#tab li').removeClass('is-active');
    $(this).addClass('is-active');
    $('.tab-content').addClass('is-hidden');
    $('[data-content="' + selected + '"]').removeClass('is-hidden');
  });

  // Tooltip
  wow_attach_tooltips($('.wow-help'));

  function wow_attach_tooltips(selector) {
    selector.tooltip({
      content: function() {
        return $(this).prop('title');
      },
      tooltipClass: 'wow-ui-tooltip',
      position: {
        my: 'center top',
        at: 'center bottom+10',
        collision: 'flipfit',
      },
      hide: {
        duration: 200,
      },
      show: {
        duration: 200,
      },
    });
  }

  // CheckLabel

  $('.checkLabel').each(function() {
    checkLabel(this);
  }).on('click', function() {
    checkLabel(this);
  });

  function checkLabel(el) {
    const check = $(el).find('input');
    if (check.prop('checked')) {
      $(el).parent().find('.field').removeClass('is-hidden');
    } else {
      $(el).parent().find('.field').addClass('is-hidden');
    }
  }

  // CheckBLock
  $('.checkBlock').each(function() {
    checkBlock(this);
  }).on('click', function() {
    checkBlock(this);
  });

  function checkBlock(el) {
    const check = $(el).find('input');
    if (check.prop('checked')) {
      $(el).closest('.columns').children('.blockHidden').removeClass('is-hidden');
    } else {
      $(el).closest('.columns').children('.blockHidden').addClass('is-hidden');
    }
  }

  // Publish
  publish();
  $('#show').on('change', publish);

  function publish() {
    const type = $('#show').val();
    switch (type) {
      case 'shortecode':
        $('#shortcode').fadeIn();
        break;
      case 'taxonomy':
        $('#taxonomy, #id-post').fadeIn();
        break;
      case 'posts':
      case 'pages':
      case 'expost':
      case 'expage':
      case 'postsincat':
        $('#id-post').fadeIn();
        break;
      default:
        $('#taxonomy, #id-post, #shortcode').hide();
        break;
    }
  }

  // Set value in input hidden for checkbox
  $('.wow-plugin input:checkbox:checked').each(function() {
    $(this).siblings('input[type="hidden"]').val('1');
  });

  $('body').on('click', '.wow-plugin input:checkbox', function() {
    checkboxchecked(this);
  });

  function checkboxchecked(el) {
    if ($(el).prop('checked')) {
      $(el).siblings('input[type="hidden"]').val('1');
    } else {
      $(el).siblings('input[type="hidden"]').val('0');
    }
  }

  // Change user role
  usersroles();
  $('#item_user').on('change', function() {
    usersroles();
  });

  function usersroles() {
    const type = $('#item_user').val();
    $('.item-user').fadeOut('200');
    if (type == '2') {
      $('.item-user').fadeIn('200');
    }
  }

  // Select Item type
  $('select.item-type').each(function() {
    itemtype(this);
  });

  $('body').on('change', '.item-type', function() {
    itemtype(this);
  });

  function itemtype(el) {
    const type = $(el).val();
    const typeText = $(el).find('option:selected').text();
    const parent = $(el).parents('.tabs-content');
    const itemLink = $(parent).find('.item-link');
    const itemShare = $(parent).find('.item-share');
    const itemTranslate = $(parent).find('.item-translate');
    const itemLinkBlank = $(parent).find('.item-link-blank');
    const itemModal = $(parent).find('.item-modal');
    const itemText = $(parent).find('.item-link-text');
    $(el).parents('.panel').find('.element-type').text(typeText);
    $(itemLink).hide();
    $(itemShare).hide();
    $(itemTranslate).hide();
    $(itemLinkBlank).hide();
    $(itemModal).hide();
    switch (type) {
      case 'link':
      case 'smoothscroll':
      case 'email':
      case 'telephone':
      case 'login':
      case 'logout':
      case 'lostpassword':
        $(itemLink).show();
        break;
      case 'share':
        $(itemShare).show();
        break;
      case 'id':
      case 'class':
      case 'modal':
        $(itemModal).show();
        break;
      case 'translate':
        $(itemTranslate).show();
        break;
    }

    switch (type) {
      case 'link':
      case 'smoothscroll':
      case 'login':
      case 'logout':
      case 'lostpassword':
        $(itemText).text('Link');
        break;
      case 'email':
        $(itemText).text('Email');
        break;
      case 'telephone':
        $(itemText).text('Telephone');
        break;
    }

    if (type === 'link' || type === 'dynamic') {
      $(itemLinkBlank).show();
    }



  }

  // Color Picker
  $('.wp-color-picker-field').wpColorPicker({
    change: function(event, ui){ panelHeadingIconColor(this); },
  });


  // Label keydown
  $('body').on('keyup', '.item-tooltip', function() {
    const val = $(this).val();
    const parent = $(this).parents('.panel');
    $(parent).find('.item-label-text').html(val);
    if (val == '') {
      $(parent).find('.item-label-text').html('(no label)');
    }
  });

  // Toogle menu item

  $('body').on('click', '.toogle-element .dashicons-arrow-down', function() {
    const parent = $(this).parents('.panel');
    $(parent).find('.toogle-content').removeClass('is-hidden');
    $(this).addClass('is-hidden');
    $(parent).find('.dashicons-arrow-up').removeClass('is-hidden');
  });

  $('body').on('click', '.toogle-element .dashicons-arrow-up', function() {
    const parent = $(this).parents('.panel');
    $(parent).find('.toogle-content').addClass('is-hidden');
    $(this).addClass('is-hidden');
    $(parent).find('.dashicons-arrow-down').removeClass('is-hidden');
  });

  // Check sub-item
  $('input.sub-item:checkbox').each(function() {
    subitem(this);
  });
  $('body').on('change', 'input.sub-item', function() {
    subitem(this);
  });

  function subitem(el) {
    const parent = $(el).parents('.panel');
    if ($(el).prop('checked')) {
      $(parent).find('.is-submenu').removeClass('is-hidden');
    } else {
      $(parent).find('.is-submenu').addClass('is-hidden');
    }
  }

  // Check custom icon
  $('.icons').fontIconPicker({
    theme: 'fip-darkgrey',
    emptyIcon: false,
    allCategoryText: 'Show all',
  });


  $('input.custom-icon:checkbox').each(function() {
    customicon(this);
  });
  $('body').on('click', '.custom-icon', function() {
    customicon(this);
  });

  function customicon(el) {
    const parent = $(el).parents('.tabs-content');
    const iconDefault = $(parent).find('.icon-default');
    const iconCustom = $(parent).find('.icon-custom');
    const iconText = $(parent).find('.icon-text');
    const iconTextField = $(parent).find('.icon-text-field');
    const checkText = $(parent).find('.custom-icon-text');
    if ($(el).prop('checked')) {
      $(iconDefault).hide();
      $(iconCustom).show();
      $(iconText).hide();
      $(checkText).prop('checked', false);
      $(iconTextField).addClass('is-hidden');
    } else {
      $(iconDefault).show();
      $(iconCustom).hide();
      $(iconText).show();
    }

  }

  $('input.custom-icon-text:checkbox').each(function() {
    customIconText(this);
  });
  $('body').on('click', '.custom-icon-text', function() {
    customIconText(this);
  });

  function customIconText(el) {
    const parent = $(el).parents('.tabs-content');
    const iconDefault = $(parent).find('.icon-default');
    const iconCustom = $(parent).find('.icon-custom');
    const iconText = $(parent).find('.icon-text-field');
    const checkCustomIcon = $(parent).find('.custom-icon');
    if ($(el).prop('checked')) {
      $(iconDefault).hide();
      $(iconCustom).hide();
      $(iconText).removeClass('is-hidden');
      $(checkCustomIcon).prop('checked', false);
    } else {
      $(iconText).addClass('is-hidden');
      $(iconDefault).show();
    }
  }

  // Hide plugin message

  $(document).on('click', '.wow-plugin-message .notice-dismiss', function() {
    $.ajax({
      url: ajaxurl, data: {
        action: 'float_menu_message',
      },
    });
  });

  // Add new Menu item
  $('.add-item').on('click', function() {
    const element = document.getElementById('clone').innerHTML;
    document.querySelector('.menu-items').insertAdjacentHTML('beforeend', element);
    refreashel();
  });

  // Refreash the functions
  function refreashel() {
    $('select.item-type').each(function() {
      itemtype(this);
    });
    $('input.custom-icon:checkbox').each(function() {
      customicon(this);
    });
    $('.wp-color-picker-field').wpColorPicker({
      change: function(event, ui){ panelHeadingIconColor(this); },
    });

    wow_attach_tooltips($('.wow-help'));
    $('.icons').fontIconPicker({
      theme: 'fip-darkgrey',
      emptyIcon: false,
      allCategoryText: 'Show all',
    });

    $('.icons-selector').on('click', function() {
      const parent = $(this).parents('.panel');
      selectIcon(parent);
    });

    $('body').on('click keyup', '[data-tab-content="2"]', function() {
      const parent = $(this).parents('.panel');
      selectIcon(parent);
    });

  }

  // Remove Items
  $('body').on('click', '.item-delete', function() {
    const parent = $(this).parents('.panel');
    $(parent).remove();
  });

  // Panel tabs
  $('body').on('click', '.panel-tabs a', function() {
    panelTab(this);
  });

  function panelTab(el) {
    const selected = $(el).data('tab');
    const parent = $(el).parents('.panel');
    $(parent).find('.panel-tabs a').removeClass('is-active');
    $(el).addClass('is-active');
    $(parent).find('.tabs-content').addClass('is-hidden');
    $(parent).find('[data-tab-content="' + selected + '"]').removeClass('is-hidden');
  }



  $('.wp-color-picker-field').each(function() {
    panelHeadingIconColor(this);
  });
  $('body').on('change', '.wp-color-picker-field', function() {
    panelHeadingIconColor(this);
  });

  function panelHeadingIconColor(el) {
    const attr = $(el).attr('name');
    const color = $(el).val();
    const parent = $(el).parents('.panel');
    if (attr.includes('[color]')) {
      $(parent).find('.icon-select').css('color', color);
    } else {
      $(parent).find('.icon-select').css('background-color', color);
    }
  }


  $('.icons-selector').on('click', function() {
    const parent = $(this).parents('.panel');
    selectIcon(parent);
  });


  $('body').on('click keyup', '[data-tab-content="2"]', function() {
    const parent = $(this).parents('.panel');
    selectIcon(parent);
  });

  $('.panel').each(function() {
    selectIcon(this);
  });

  function selectIcon(el) {
    let icon;
    const iconCustom = $(el).find('.custom-icon');
    const iconText = $(el).find('.custom-icon-text');
    const iconDefault = $(el).find('.icons').val();
    if ($(iconCustom).prop('checked')) {
      const img = $(el).find('.custom-icon-url').val();
      icon = '<img src="'+img+'">';
    } else if ($(iconText).prop('checked')) {
      const text = $(el).find('.icon-custom-text').val();
      icon = text;
    } else {
      icon = '<i class="'+iconDefault+'"></i>';
    }
    $(el).find('.icon-select').html(icon);

  }

  $('.menu-items').sortable();





})(jQuery);
