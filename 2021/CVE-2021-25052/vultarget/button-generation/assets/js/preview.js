/* ========= INFORMATION ============================
- document:  Button Generator!
- author:    Wow-Company
- copyright: 2019 Wow-Company
- version:   1.0
- email:     support@wow-company.com
==================================================== */

'use strict';

(function ($) {
  jQuery('#postoptions').on('change', function () {
    preview_button();
    preview_badge();
  })
  $(".wp-color-picker-field").wpColorPicker(
    'option',
    'change',
    function (event, ui) {
      preview_button();
      preview_badge();
    }
  );
  preview_button();
  preview_badge();
})(jQuery);

// Build button
function preview_button() {
  var appearance = jQuery('#appearance').val();
  var btn_text = jQuery('#text').val();
  var icon = jQuery('#icon').val();
  var text_location = jQuery('#text_location').val();
  var rotate_icon = jQuery('#rotate_icon').val();
  var rotate_button = jQuery('#rotate_button').val();


  var width = jQuery('#width').val();
  var height = jQuery('#height').val();
  var color = jQuery('#color').val();
  var background = jQuery('#background').val();


  var border_radius = jQuery('#border_radius').val();
  var border_style = jQuery('#border_style').val();
  var border_color = jQuery('#border_color').val();
  var border_width = jQuery('#border_width').val();


  var shadow = jQuery('#shadow').val();
  var shadow_h_offset = jQuery('#shadow_h_offset').val();
  var shadow_v_offset = jQuery('#shadow_v_offset').val();
  var shadow_blur = jQuery('#shadow_blur').val();
  var shadow_spread = jQuery('#shadow_spread').val();
  var shadow_color = jQuery('#shadow_color').val();


  var font_size = jQuery('#font_size').val();
  var font_family = jQuery('#font_family').val();
  var font_weight = jQuery('#font_weight').val();
  var font_style = jQuery('#font_style').val();


  if (shadow == 'none') {
    var boxshadow = '';
  } else if (shadow == 'outset') {
    var boxshadow = shadow_h_offset + 'px ' + shadow_v_offset + 'px ' + shadow_blur + 'px ' + shadow_spread + 'px ' + shadow_color;
  } else if (shadow == 'inset') {
    var boxshadow = 'inset ' + shadow_h_offset + 'px ' + shadow_v_offset + 'px ' + shadow_blur + 'px ' + shadow_spread + 'px ' + shadow_color;
  }
  jQuery('#button-preview').css({
    'width': width,
    'height': height,
    'line-height': 'normal',
    'background': background,
    'color': color,
    'border-radius': border_radius,
    'border-style': border_style,
    'border-color': border_color,
    'border-width': border_width + 'px',
    'box-shadow': boxshadow,
    'font-size': font_size + 'px',
    'font-family': font_family,
    'font-weight': font_weight,
    'font-style': font_style,
    'text-align': 'center',
    'position': 'relative',
    'transform': 'rotate(' + rotate_button + ')',
  });

  if (appearance == 'text') {
    jQuery('#button-preview .content').html(btn_text);
  } else if (appearance == 'text_icon') {
    if (text_location == 'after') {
      jQuery('#button-preview .content').html('<i class="' + icon + ' ' + rotate_icon + '"></i> ' + btn_text);
    } else {
      jQuery('#button-preview .content').html(btn_text + ' <i class="' + icon + ' ' + rotate_icon + '"></i>');
    }

  } else if (appearance == 'icon') {
    jQuery('#button-preview .content').html('<i class="' + icon + ' ' + rotate_icon + '"></i>');
  }
}

function preview_badge() {
  if (jQuery('#enable_badge').is(':checked')) {
    jQuery('#button-preview .badge').css('display', '');

    var badge_text = jQuery('#badge_content').val();
    var badge_action = jQuery('#tool_action').html();


    let type = jQuery('#badge_type').val();
    if (type === 'actions') {
      var content = badge_action;
    } else {
      var content = badge_text;
    }


    var width = jQuery('#badge_width').val();
    var height = jQuery('#badge_height').val();
    var color = jQuery('#badge_color').val();
    var background = jQuery('#badge_background').val();

    var border_radius = jQuery('#badge_border_radius').val();
    var border_style = jQuery('#badge_border_style').val();
    var border_color = jQuery('#badge_border_color').val();
    var border_width = jQuery('#badge_border_width').val();

    var font_size = jQuery('#badge_font_size').val();
    var font_family = jQuery('#badge_font_family').val();
    var font_weight = jQuery('#badge_font_weight').val();
    var font_style = jQuery('#badge_font_style').val();

    var position_top = jQuery('#badge_position_top').val();
    var position_right = jQuery('#badge_position_right').val();

    jQuery('#button-preview .badge').text(content);

    jQuery('#button-preview .badge').css({
      'width': width,
      'height': height,
      'line-height': height,
      'background': background,
      'color': color,
      'border-radius': border_radius,
      'border-style': border_style,
      'border-color': border_color,
      'border-width': border_width + 'px',
      'font-size': font_size + 'px',
      'font-family': font_family,
      'font-weight': font_weight,
      'font-style': font_style,
      'text-align': 'center',
      'position': 'absolute',
      'top': position_top + 'px',
      'right': position_right + 'px',
    });


  } else {
    jQuery('#button-preview .badge').css('display', 'none');

  }


}
