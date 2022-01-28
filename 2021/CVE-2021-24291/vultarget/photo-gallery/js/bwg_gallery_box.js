var isPopUpOpened = false;
var bwg_overflow_initial_value = false;
var bwg_overflow_x_initial_value = false;
var bwg_overflow_y_initial_value = false;
var bwg_current_filmstrip_pos;
var total_thumbnail_count;
var key;
var startPoint;
var endPoint;
var bwg_image_info_pos;
var filmstrip_width;
var preloadCount;
var filmstrip_thumbnail_width;
var filmstrip_thumbnail_height;
var addthis_share;
var lightbox_comment_pos;
var bwg_transition_duration;
var bwg_playInterval;

function gallery_box_ready() {
  filmstrip_width;
  preloadCount;
  filmstrip_thumbnail_width = jQuery(".bwg_filmstrip_thumbnail").width();
  filmstrip_thumbnail_height = jQuery(".bwg_filmstrip_thumbnail").height();

  if ( gallery_box_data['open_with_fullscreen'] == 1 ) {
    filmstrip_width = jQuery( window ).width();
    filmstrip_height = jQuery( window ).height();
  } else {
    filmstrip_width = jQuery(".bwg_filmstrip_container").width();
    filmstrip_height = jQuery(".bwg_filmstrip_container").height();
  }
  if ( gallery_box_data['filmstrip_direction'] == 'horizontal' ) {
    preloadCount = parseInt(filmstrip_width/filmstrip_thumbnail_width) + gallery_box_data['preload_images_count'];
  } else {
    preloadCount = parseInt(filmstrip_height/filmstrip_thumbnail_height) + gallery_box_data['preload_images_count'];
  }
  total_thumbnail_count = jQuery(".bwg_filmstrip_thumbnail").length;

  key = parseInt(jQuery("#bwg_current_image_key").val());
  startPoint = 0;
  endPoint = key+preloadCount;

  jQuery(function() {
    bwg_load_visible_images( key, preloadCount, total_thumbnail_count );
    jQuery(".pge_tabs li a").on("click", function(){
      jQuery(".pge_tabs_container > div").hide();
      jQuery(".pge_tabs li").removeClass("pge_active");
      jQuery(jQuery(this).attr("href")).show();
      jQuery(this).closest("li").addClass("pge_active");
      jQuery("[name=type]").val(jQuery(this).attr("href").substr(1));
      return false;
    });

    var data_rated = jQuery("#bwg_rated").attr("data-params");
    data_rated = JSON.parse(data_rated);
    bwg_rating(data_rated['current_rate'], data_rated['current_rate_count'], data_rated['current_avg_rating'], data_rated['current_image_key']);
  });

  if ( gallery_box_data['is_pro'] == true && gallery_box_data['enable_addthis'] == 1 && gallery_box_data['addthis_profile_id'] ) {
    addthis_share = {
      url: gallery_box_data['share_url']
    }
  }
  lightbox_comment_pos = gallery_box_data['lightbox_comment_pos'];
  bwg_image_info_pos = (jQuery(".bwg_ctrl_btn_container").length) ? jQuery(".bwg_ctrl_btn_container").height() : 0;
  bwg_transition_duration = ((gallery_box_data['slideshow_interval'] < 4 * gallery_box_data['slideshow_effect_duration']) && (gallery_box_data['slideshow_interval'] != 0)) ? (gallery_box_data['slideshow_interval'] * 1000) / 4 : (gallery_box_data['slideshow_effect_duration'] * 1000);
  gallery_box_data['bwg_transition_duration'] = bwg_transition_duration;
  gallery_box_data['bwg_trans_in_progress'] = false;

  if ((jQuery("#spider_popup_wrap").width() >= jQuery(window).width()) || (jQuery("#spider_popup_wrap").height() >= jQuery(window).height())) {
    jQuery(".spider_popup_close").attr("class", "bwg_ctrl_btn spider_popup_close_fullscreen");
  }
  /* Stop autoplay.*/
  window.clearInterval(bwg_playInterval);
  /* Set watermark container size.*/

  bwg_current_filmstrip_pos = gallery_box_data['current_pos'];
  /* Set filmstrip initial position.*/
  jQuery(document).on('keydown', function (e) {
    if (jQuery("#bwg_name").is(":focus") || jQuery("#bwg_email").is(":focus") || jQuery("#bwg_comment").is(":focus") || jQuery("#bwg_captcha_input").is(":focus")) {
      return;
    }
    if (e.key == 'ArrowRight' ) { /* Right arrow.*/
      if (parseInt(jQuery('#bwg_current_image_key').val()) == gallery_box_data.data.length - 1) { /* are current image is last? */
        bwg_change_image(parseInt(jQuery('#bwg_current_image_key').val()), 0)
      }
      else {
        bwg_change_image(parseInt(jQuery('#bwg_current_image_key').val()), parseInt(jQuery('#bwg_current_image_key').val()) + 1)
      }
    }
    else if (e.key == 'ArrowLeft' ) { /* Left arrow.*/
      if (parseInt(jQuery('#bwg_current_image_key').val()) == 0) { /* are current image is first? */
        bwg_change_image(parseInt(jQuery('#bwg_current_image_key').val()), gallery_box_data.data.length - 1)
      }
      else {
        bwg_change_image(parseInt(jQuery('#bwg_current_image_key').val()), parseInt(jQuery('#bwg_current_image_key').val()) - 1)
      }
    }
    else if (e.key == 'Escape') { /* Esc.*/
      spider_destroypopup(1000);
    }
    else if ( e.key == 'Space' ) { /* Space.*/
      jQuery(".bwg_play_pause").trigger('click');
    }
  });
  jQuery(window).resize(function() {
    if (typeof jQuery().fullscreen !== 'undefined') {
      if (jQuery.isFunction(jQuery().fullscreen)) {
        if (!jQuery.fullscreen.isFullScreen()) {
          bwg_popup_resize();
        }
      }
    }
  });
  /* Popup current width/height.*/
  var bwg_popup_current_width = gallery_box_data['image_width'];
  var bwg_popup_current_height = gallery_box_data['image_height'];

  /* jQuery(document).ready(function () { */
  if ( gallery_box_data['is_pro'] == true ) {
    if ( gallery_box_data['enable_addthis'] == 1 && gallery_box_data['addthis_profile_id'] ) {
      jQuery(".at4-share-outer").show();
    }
    /* Increase image hit counter.*/
    spider_set_input_value('rate_ajax_task', 'save_hit_count');
    spider_rate_ajax_save('bwg_rate_form');
    var tempdata = gallery_box_data['data'];
    var current_image_key = gallery_box_data['current_image_key'];

    jQuery(".bwg_image_hits span").html(++tempdata[current_image_key]["hit_count"]);
    var bwg_hash = window.location.hash;
    if ( !bwg_hash || bwg_hash.indexOf("bwg") == "-1" ) {
      location.replace("#bwg"+gallery_box_data['gallery_id']+"/"+gallery_box_data['current_image_id']);
      history.replaceState(undefined, undefined, "#bwg"+gallery_box_data['gallery_id']+"/"+gallery_box_data['current_image_id']);
    }
  }
  if (gallery_box_data['image_right_click'] == 1) {
    /* Disable right click.*/
    jQuery(".bwg_image_wrap").bind("contextmenu", function (e) {
      return false;
    });
    jQuery(".bwg_image_wrap").css('webkitTouchCallout','none');
  }
  jQuery('#spider_popup_wrap').bind('touchmove', function (event) {
    event.preventDefault();
  });
  if (typeof jQuery().swiperight !== 'undefined') {
    if (jQuery.isFunction(jQuery().swiperight)) {
      jQuery('#spider_popup_wrap .bwg_image_wrap').swiperight(function () {
        bwg_change_image(parseInt(jQuery('#bwg_current_image_key').val()), (parseInt(jQuery('#bwg_current_image_key').val()) + gallery_box_data['data'].length - 1) % gallery_box_data['data'].length);
        return false;
      });
    }
  }
  if (typeof jQuery().swipeleft !== 'undefined') {
    if (jQuery.isFunction(jQuery().swipeleft)) {
      jQuery('#spider_popup_wrap .bwg_image_wrap').swipeleft(function () {
        bwg_change_image(parseInt(jQuery('#bwg_current_image_key').val()), (parseInt(jQuery('#bwg_current_image_key').val()) + 1) % gallery_box_data['data'].length);
        return false;
      });
    }
  }
  bwg_reset_zoom();
  var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
  var bwg_click = isMobile ? 'touchend' : 'click';
  jQuery("#spider_popup_left").on(bwg_click, function () {
    bwg_change_image(parseInt(jQuery('#bwg_current_image_key').val()), (parseInt(jQuery('#bwg_current_image_key').val()) + gallery_box_data['data'].length - 1) % gallery_box_data['data'].length);
    return false;
  });
  jQuery("#spider_popup_right").on(bwg_click, function () {
    bwg_change_image(parseInt(jQuery('#bwg_current_image_key').val()), (parseInt(jQuery('#bwg_current_image_key').val()) + 1) % gallery_box_data['data'].length);
    return false;
  });
  if (navigator.appVersion.indexOf("MSIE 10") != -1 || navigator.appVersion.indexOf("MSIE 9") != -1) {
    setTimeout(function () {
      bwg_popup_resize();
    }, 1);
  }
  else {
    bwg_popup_resize();
  }
  jQuery(".bwg_watermark").css({ display: 'none' });
  setTimeout(function () {
    bwg_change_watermark_container();
  }, 500);
  /* If browser doesn't support Fullscreen API.*/
  if (typeof jQuery().fullscreen !== 'undefined') {
    if (jQuery.isFunction(jQuery().fullscreen)) {
      if (!jQuery.fullscreen.isNativelySupported()) {
        jQuery(".bwg_fullscreen").hide();
      }
    }
  }
  /* Set image container height.*/
  if ( gallery_box_data['filmstrip_direction'] == 'horizontal' ) {
    jQuery(".bwg_image_container").height(jQuery(".bwg_image_wrap").height() - gallery_box_data['image_filmstrip_height']);
    jQuery(".bwg_image_container").width(jQuery(".bwg_image_wrap").width());
  }
  else {
    jQuery(".bwg_image_container").height(jQuery(".bwg_image_wrap").height());
    jQuery(".bwg_image_container").width(jQuery(".bwg_image_wrap").width() - gallery_box_data['image_filmstrip_width']);
  }
  /* Change default scrollbar in comments, ecommerce.*/
  if (typeof jQuery().mCustomScrollbar !== 'undefined' && jQuery.isFunction(jQuery().mCustomScrollbar)) {
    jQuery(".bwg_comments,.bwg_ecommerce_panel, .bwg_image_info").mCustomScrollbar({
      scrollInertia: 150,
      advanced:{
        updateOnContentResize: true
      }
    });
  }
    var mousewheelevt = (/Firefox/i.test(navigator.userAgent)) ? "DOMMouseScroll" : "mousewheel"; /*FF doesn't recognize mousewheel as of FF3.x*/
  jQuery('.bwg_filmstrip').on(mousewheelevt, function(e) {
    var evt = window.event || e; /* Equalize event object.*/
    evt = evt.originalEvent ? evt.originalEvent : evt; /* Convert to originalEvent if possible.*/
    var delta = evt.detail ? evt.detail*(-40) : evt.wheelDelta; /* Check for detail first, because it is used by Opera and FF.*/
    var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
    if (delta > 0) {
      /* Scroll up.*/
      jQuery(".bwg_filmstrip_left").trigger(isMobile ? 'touchend' : 'click');
    }
    else {
      /* Scroll down.*/
      jQuery(".bwg_filmstrip_right").trigger(isMobile ? 'touchend' : 'click');
    }
  });

  jQuery(".bwg_filmstrip_right").on(bwg_click, function () {
    jQuery( ".bwg_filmstrip_thumbnails" ).stop(true, false);
    if ( gallery_box_data['left_or_top'] == 'left' ) {
      if ( gallery_box_data['width_or_height'] == 'width' ) { /* left -X- width */
        if ( jQuery(".bwg_filmstrip_thumbnails").position().left >= - (jQuery(".bwg_filmstrip_thumbnails").width() - jQuery(".bwg_filmstrip").width()) ) {
          jQuery(".bwg_filmstrip_left").css({ opacity: 1 });
          if ( jQuery(".bwg_filmstrip_thumbnails").position().left < -(jQuery(".bwg_filmstrip_thumbnails").width() - jQuery(".bwg_filmstrip").width() - (gallery_box_data['filmstrip_thumb_right_left_space'] + gallery_box_data['image_filmstrip_width'] + gallery_box_data['all_images_right_left_space']))) {
            jQuery(".bwg_filmstrip_thumbnails").animate({
              left : -( jQuery(".bwg_filmstrip_thumbnails").width() - jQuery(".bwg_filmstrip").width() - gallery_box_data['all_images_right_left_space'])
            }, 500, 'linear');
          } else {
            jQuery(".bwg_filmstrip_thumbnails").animate({
              left : (jQuery(".bwg_filmstrip_thumbnails").position().left - (gallery_box_data['filmstrip_thumb_right_left_space'] + gallery_box_data['image_filmstrip_width']))
            }, 500, 'linear');
          }
        }
        /* Disable right arrow.*/
        window.setTimeout(function(){
          if (jQuery(".bwg_filmstrip_thumbnails").position().left == -(jQuery(".bwg_filmstrip_thumbnails").width() - jQuery(".bwg_filmstrip").width())) {
            jQuery(".bwg_filmstrip_right").css({opacity: 0.3});
          }
        }, 500);
      } else { /* left -X- height */
        if ( jQuery(".bwg_filmstrip_thumbnails").position().left >= - (jQuery(".bwg_filmstrip_thumbnails").height() - jQuery(".bwg_filmstrip").height()) ) {
          jQuery(".bwg_filmstrip_left").css({ opacity: 1 });
          if ( (jQuery(".bwg_filmstrip_thumbnails").position().left) < ( - (jQuery(".bwg_filmstrip_thumbnails").height() - jQuery(".bwg_filmstrip").height() - (gallery_box_data['filmstrip_thumb_right_left_space'] + gallery_box_data['image_filmstrip_width'] + gallery_box_data['all_images_right_left_space'])))) {
            jQuery(".bwg_filmstrip_thumbnails").animate({
              left : -( jQuery(".bwg_filmstrip_thumbnails").height() - jQuery(".bwg_filmstrip").height() - gallery_box_data['all_images_right_left_space'])
            }, 500, 'linear');
          } else {
            jQuery(".bwg_filmstrip_thumbnails").animate({
              left : (jQuery(".bwg_filmstrip_thumbnails").position().left - (gallery_box_data['filmstrip_thumb_right_left_space'] + gallery_box_data['image_filmstrip_width']))
            }, 500, 'linear');
          }
        }
        /* Disable right arrow.*/
        window.setTimeout(function(){
          if (jQuery(".bwg_filmstrip_thumbnails").position().left == -(jQuery(".bwg_filmstrip_thumbnails").height() - jQuery(".bwg_filmstrip").height())) {
            jQuery(".bwg_filmstrip_right").css({opacity: 0.3});
          }
        }, 500);
      }
    } else {
      if ( gallery_box_data['width_or_height'] == 'width' ) { /* top -X- width */
        if ( jQuery(".bwg_filmstrip_thumbnails").position().top >= - (jQuery(".bwg_filmstrip_thumbnails").width() - jQuery(".bwg_filmstrip").width()) ) {
          jQuery(".bwg_filmstrip_left").css({ opacity: 1 });
          if ( (jQuery(".bwg_filmstrip_thumbnails").position().top) < ( - (jQuery(".bwg_filmstrip_thumbnails").width() - jQuery(".bwg_filmstrip").width() - (gallery_box_data['filmstrip_thumb_right_left_space'] + gallery_box_data['image_filmstrip_width'] + gallery_box_data['all_images_right_left_space'])))) {
            jQuery(".bwg_filmstrip_thumbnails").animate({
              left : -( jQuery(".bwg_filmstrip_thumbnails").width() - jQuery(".bwg_filmstrip").width() - gallery_box_data['all_images_right_left_space'])
            }, 500, 'linear');
          } else {
            jQuery(".bwg_filmstrip_thumbnails").animate({
              left : (jQuery(".bwg_filmstrip_thumbnails").position().top - (gallery_box_data['filmstrip_thumb_right_left_space'] + gallery_box_data['image_filmstrip_width']))
            }, 500, 'linear');
          }
        }
        /* Disable right arrow.*/
        window.setTimeout(function(){
          if (jQuery(".bwg_filmstrip_thumbnails").position().left == -(jQuery(".bwg_filmstrip_thumbnails").width() - jQuery(".bwg_filmstrip").width())) {
            jQuery(".bwg_filmstrip_right").css({opacity: 0.3});
          }
        }, 500);
      } else { /* top -X- height */
        if ( jQuery(".bwg_filmstrip_thumbnails").position().top >= - (jQuery(".bwg_filmstrip_thumbnails").height() - jQuery(".bwg_filmstrip").height()) ) {
          jQuery(".bwg_filmstrip_left").css({ opacity: 1 });
          if ( (jQuery(".bwg_filmstrip_thumbnails").position().top) < ( - (jQuery(".bwg_filmstrip_thumbnails").height() - jQuery(".bwg_filmstrip").height() - (gallery_box_data['filmstrip_thumb_right_left_space'] + gallery_box_data['image_filmstrip_width'] + gallery_box_data['all_images_right_left_space'])))) {
            jQuery(".bwg_filmstrip_thumbnails").animate({
              top : -( jQuery(".bwg_filmstrip_thumbnails").height() - jQuery(".bwg_filmstrip").height() - gallery_box_data['all_images_right_left_space'])
            }, 500, 'linear');
          } else {
            jQuery(".bwg_filmstrip_thumbnails").animate({
              top : (jQuery(".bwg_filmstrip_thumbnails").position().top - (gallery_box_data['filmstrip_thumb_right_left_space'] + gallery_box_data['image_filmstrip_width']))
            }, 500, 'linear');
          }
        }
        /* Disable right arrow.*/
        window.setTimeout(function(){
          if (jQuery(".bwg_filmstrip_thumbnails").position().left == -(jQuery(".bwg_filmstrip_thumbnails").height() - jQuery(".bwg_filmstrip").height())) {
            jQuery(".bwg_filmstrip_right").css({opacity: 0.3});
          }
        }, 500);
      }
    }
  });

  if ( gallery_box_data['left_or_top'] == 'left' ) {
    jQuery(".bwg_filmstrip_left").on(bwg_click, function () {
      jQuery( ".bwg_filmstrip_thumbnails" ).stop(true, false);
      if ((jQuery(".bwg_filmstrip_thumbnails").position().left) < 0) {
        jQuery(".bwg_filmstrip_right").css({opacity: 1});
        if (jQuery(".bwg_filmstrip_thumbnails").position().left > - (gallery_box_data['filmstrip_thumb_right_left_space'] + gallery_box_data['image_filmstrip_width'])) {
          jQuery(".bwg_filmstrip_thumbnails").animate({ left: 0 }, 500, 'linear');
        }
        else {
          jQuery(".bwg_filmstrip_thumbnails").animate({ left : (jQuery(".bwg_filmstrip_thumbnails").position().left + gallery_box_data['image_filmstrip_width'] + gallery_box_data['filmstrip_thumb_right_left_space'])}, 500, 'linear');
        }
      }
      /* Disable left arrow.*/
      window.setTimeout(function(){
        if (jQuery(".bwg_filmstrip_thumbnails").position().left == 0) {
          jQuery(".bwg_filmstrip_left").css({opacity: 0.3});
        }
      }, 500);
    });
  }
  else {
    jQuery(".bwg_filmstrip_left").on(bwg_click, function () {
      jQuery( ".bwg_filmstrip_thumbnails" ).stop(true, false);
      if ((jQuery(".bwg_filmstrip_thumbnails").position().top) < 0) {
        jQuery(".bwg_filmstrip_right").css({opacity: 1});
		if (jQuery(".bwg_filmstrip_thumbnails").position().top > - (gallery_box_data['filmstrip_thumb_right_left_space'] + gallery_box_data['image_filmstrip_width'])) {
          jQuery(".bwg_filmstrip_thumbnails").animate({ top: 0 }, 500, 'linear');
        }
        else {
          jQuery(".bwg_filmstrip_thumbnails").animate({ top : (jQuery(".bwg_filmstrip_thumbnails").position().top + gallery_box_data['image_filmstrip_width'] + gallery_box_data['filmstrip_thumb_right_left_space'])}, 500, 'linear');
        }
      }
      /* Disable left arrow.*/
      window.setTimeout(function(){
        if (jQuery(".bwg_filmstrip_thumbnails").position().top == 0) {
          jQuery(".bwg_filmstrip_left").css({opacity: 0.3});
        }
      }, 500);
    });
  }

  if ( gallery_box_data['width_or_height'] == 'width' ) {
    /* Set filmstrip initial position.*/
    bwg_set_filmstrip_pos(jQuery(".bwg_filmstrip").width(), '', gallery_box_data);
  } else {
    /* Set filmstrip initial position.*/
    bwg_set_filmstrip_pos(jQuery(".bwg_filmstrip").height(), '', gallery_box_data);
  }
  /* Show/hide image title/description.*/
  jQuery(".bwg_info").on(bwg_click, function() {
    if (jQuery(".bwg_image_info_container1").css("display") == 'none') {
      jQuery(".bwg_image_info_container1").css("display", "table-cell");
      jQuery(".bwg_info").attr("title", bwg_objectsL10n.bwg_hide_info);
      var bwg_image_info_pos = (jQuery(".bwg_ctrl_btn_container").length) ? jQuery(".bwg_ctrl_btn_container").height() : 0;
      jQuery(".bwg_image_info").css("height","auto");
      bwg_info_height_set();
    }
    else {
      jQuery(".bwg_image_info_container1").css("display", "none");
      jQuery(".bwg_info").attr("title", bwg_objectsL10n.bwg_show_info);
    }
  });
  /* Show/hide image rating.*/
  jQuery(".bwg_rate").on(bwg_click, function() {
    if (jQuery(".bwg_image_rate_container1").css("display") == 'none') {
      jQuery(".bwg_image_rate_container1").css("display", "table-cell");
      jQuery(".bwg_rate").attr("title", bwg_objectsL10n.bwg_hide_rating);
    }
    else {
      jQuery(".bwg_image_rate_container1").css("display", "none");
      jQuery(".bwg_rate").attr("title", bwg_objectsL10n.bwg_show_rating);
    }
  });
  /* Open/close comments.*/
  jQuery(".bwg_comment, .bwg_comments_close_btn").on(bwg_click, function() { bwg_comment() });
  /* Open/close ecommerce.*/
  jQuery(".bwg_ecommerce, .bwg_ecommerce_close_btn").on(bwg_click, function() { bwg_ecommerce() });
  /* Open/close control buttons.*/
  jQuery(".bwg_toggle_container").on(bwg_click, function () {
      var bwg_open_toggle_btn_class = (gallery_box_data['lightbox_ctrl_btn_pos'] == 'top') ? 'bwg-icon-caret-up' : 'bwg-icon-caret-down';
      var bwg_close_toggle_btn_class = (gallery_box_data['lightbox_ctrl_btn_pos'] == 'top') ? 'bwg-icon-caret-down' : 'bwg-icon-caret-up';

      if (jQuery(".bwg_toggle_container i").hasClass(bwg_open_toggle_btn_class)) {
          if ((!gallery_box_data['enable_image_filmstrip'] || gallery_box_data['lightbox_filmstrip_pos'] != 'bottom') && gallery_box_data['lightbox_ctrl_btn_pos'] == 'bottom' && gallery_box_data['lightbox_rate_pos'] == 'bottom') {
              jQuery(".bwg_image_rate").animate({bottom: 0}, 500);
          }
          else if ((!gallery_box_data['enable_image_filmstrip'] || gallery_box_data['lightbox_filmstrip_pos'] != 'top') && gallery_box_data['lightbox_ctrl_btn_pos'] == 'top' && gallery_box_data['lightbox_rate_pos'] == 'top') {
              jQuery(".bwg_image_rate").animate({top: 0}, 500);
          }
          if ((!gallery_box_data['enable_image_filmstrip'] || gallery_box_data['lightbox_filmstrip_pos'] != 'bottom') && gallery_box_data['lightbox_ctrl_btn_pos'] == 'bottom' && gallery_box_data['lightbox_hit_pos'] == 'bottom') {
              jQuery(".bwg_image_hit").animate({bottom: 0}, 500);
          }
          else if ((!gallery_box_data['enable_image_filmstrip'] || gallery_box_data['lightbox_filmstrip_pos'] != 'top') && gallery_box_data['lightbox_ctrl_btn_pos'] == 'top' && gallery_box_data['lightbox_hit_pos'] == 'top') {
              jQuery(".bwg_image_hit").animate({top: 0}, 500);
          }
          if ( gallery_box_data['lightbox_ctrl_btn_pos'] == 'bottom' ) {
              jQuery(".bwg_ctrl_btn_container").animate({ bottom : '-' + jQuery(".bwg_ctrl_btn_container").height()}, 500).addClass('closed');
              jQuery(".bwg_toggle_container").animate({
                  bottom : 0
              }, {
                  duration: 500,
                  complete: function () { jQuery(".bwg_toggle_container i").attr("class", "bwg_toggle_btn " + bwg_close_toggle_btn_class) }
              });
          } else {
              jQuery(".bwg_ctrl_btn_container").animate({ top : '-' + jQuery(".bwg_ctrl_btn_container").height()}, 500).addClass('closed');
              jQuery(".bwg_toggle_container").animate({
                  top : 0
              }, {
                  duration: 500,
                  complete: function () { jQuery(".bwg_toggle_container i").attr("class", "bwg_toggle_btn " + bwg_close_toggle_btn_class) }
              });
          }
      }
      else {
          if ((!gallery_box_data['enable_image_filmstrip'] || gallery_box_data['lightbox_filmstrip_pos'] != 'bottom') && gallery_box_data['lightbox_ctrl_btn_pos'] == 'bottom' && gallery_box_data['lightbox_rate_pos'] == 'bottom') {
              jQuery(".bwg_image_rate").animate({bottom: jQuery(".bwg_ctrl_btn_container").height()}, 500);
          }
          else if ((!gallery_box_data['enable_image_filmstrip'] || gallery_box_data['lightbox_filmstrip_pos'] != 'top') && gallery_box_data['lightbox_ctrl_btn_pos'] == 'top' && gallery_box_data['lightbox_rate_pos'] == 'top') {
              jQuery(".bwg_image_rate").animate({top: jQuery(".bwg_ctrl_btn_container").height()}, 500);
          }
          if ((!gallery_box_data['enable_image_filmstrip'] || gallery_box_data['lightbox_filmstrip_pos'] != 'bottom') && gallery_box_data['lightbox_ctrl_btn_pos'] == 'bottom' && gallery_box_data['lightbox_hit_pos'] == 'bottom') {
              jQuery(".bwg_image_hit").animate({bottom: jQuery(".bwg_ctrl_btn_container").height()}, 500);
          }
          else if ((!gallery_box_data['enable_image_filmstrip'] || gallery_box_data['lightbox_filmstrip_pos'] != 'top') && gallery_box_data['lightbox_ctrl_btn_pos'] == 'top' && gallery_box_data['lightbox_hit_pos'] == 'top') {
              jQuery(".bwg_image_hit").animate({top: jQuery(".bwg_ctrl_btn_container").height()}, 500);
          }

          if ( gallery_box_data['lightbox_ctrl_btn_pos'] == 'bottom' ) {
              jQuery(".bwg_ctrl_btn_container").animate({ bottom: 0 }, 500).removeClass('closed');
              jQuery(".bwg_toggle_container").animate({
                  bottom : jQuery(".bwg_ctrl_btn_container").height()
              }, {
                  duration: 500,
                  complete: function () { jQuery(".bwg_toggle_container i").attr("class", "bwg_toggle_btn " + bwg_open_toggle_btn_class) }
              });
          } else {
              jQuery(".bwg_ctrl_btn_container").animate({ top: 0 }, 500).removeClass('closed');
              jQuery(".bwg_toggle_container").animate({
                  top : jQuery(".bwg_ctrl_btn_container").height()
              }, {
                  duration: 500,
                  complete: function () { jQuery(".bwg_toggle_container i").attr("class", "bwg_toggle_btn " + bwg_open_toggle_btn_class) }
              });
          }
      }
      bwg_info_position( true );
  });
  /* Set window height not full screen */
  var bwg_windowheight = window.innerHeight;
  /* Maximize/minimize.*/
  jQuery(".bwg_resize-full").on(bwg_click, function () {
    bwg_resize_full();
  });
  /* Fullscreen.*/
  /*Toggle with mouse click*/
  jQuery(".bwg_fullscreen").on(bwg_click, function () {
    jQuery(".bwg_watermark").css({display: 'none'});
    var comment_container_width = 0;
    if (jQuery(".bwg_comment_container").hasClass("bwg_open") || jQuery(".bwg_ecommerce_container").hasClass("bwg_open")) {
      comment_container_width = jQuery(".bwg_comment_container").width() || jQuery(".bwg_ecommerce_container").width();
    }
    function bwg_exit_fullscreen(windowheight) {
      if (jQuery(window).width() > gallery_box_data['image_width']) {
        bwg_popup_current_width = gallery_box_data['image_width'];
      }
      if (window.innerHeight > gallery_box_data['image_height']) {
        bwg_popup_current_height = gallery_box_data['image_height'];
      }
      /* "Full width lightbox" sets yes.*/
      if (gallery_box_data['open_with_fullscreen']) {
        bwg_popup_current_width = jQuery(window).width();
        bwg_popup_current_height = windowheight;
      }
      jQuery("#spider_popup_wrap").on("fscreenclose", function() {
        jQuery("#spider_popup_wrap").css({
          width: bwg_popup_current_width,
          height: bwg_popup_current_height,
          left: '50%',
          top: '50%',
          marginLeft: -bwg_popup_current_width / 2,
          marginTop: -bwg_popup_current_height / 2,
          zIndex: 100000
        });
        jQuery(".bwg_image_wrap").css({width: (bwg_popup_current_width - comment_container_width)});
        jQuery(".bwg_image_container").css({
          'height': (bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)),
          'width': (bwg_popup_current_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0))
        });
        jQuery(".bwg_image_info").css("height","auto");
        bwg_info_height_set();
        jQuery(".bwg_popup_image").css({
          'maxWidth': (bwg_popup_current_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0)),
          'maxHeight': (bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0))
        });
        jQuery(".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video").css({
          'maxWidth': (bwg_popup_current_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0)),
          'maxHeight': (bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0))
        });
        bwg_resize_instagram_post();
        /* Set watermark container size.*/
        bwg_change_watermark_container();
        if ( gallery_box_data['width_or_height'] == 'width' ) {
          jQuery(".bwg_filmstrip_container").css({width: bwg_popup_current_width - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0)});
          jQuery(".bwg_filmstrip").css({width: bwg_popup_current_width - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0)- 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height())});
          /* Set filmstrip initial position.*/
          bwg_set_filmstrip_pos( bwg_popup_current_width - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data );
        } else {
          jQuery(".bwg_filmstrip_container").css({height: bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0)});
          jQuery(".bwg_filmstrip").css({height: bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0)- 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height())});
          /* Set filmstrip initial position.*/
          bwg_set_filmstrip_pos( bwg_popup_current_height - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data );
          gallery_box_data['filmstrip_direction'] == 'horizontal' ? '' : jQuery(".bwg_filmstrip_right").css({ top: (bwg_popup_current_height - jQuery('.bwg_filmstrip_right').height() ) });
        }
        jQuery(".bwg_resize-full").show();
        jQuery(".bwg_resize-full").attr("class", "bwg-icon-expand bwg_ctrl_btn bwg_resize-full");
        jQuery(".bwg_resize-full").attr("title", bwg_objectsL10n.bwg_maximize);
        jQuery(".bwg_fullscreen").attr("class", "bwg-icon-arrows-out bwg_ctrl_btn bwg_fullscreen");
        jQuery(".bwg_fullscreen").attr("title", bwg_objectsL10n.bwg_fullscreen);
        if (jQuery("#spider_popup_wrap").width() < jQuery(window).width()) {
          if (jQuery("#spider_popup_wrap").height() < window.innerHeight) {
            jQuery(".spider_popup_close_fullscreen").attr("class", "spider_popup_close");
          }
        }
      });
    }
    if (typeof jQuery().fullscreen !== 'undefined') {
      if (jQuery.isFunction(jQuery().fullscreen)) {
        if (jQuery.fullscreen.isFullScreen()) {
          /* Exit Fullscreen.*/
          jQuery.fullscreen.exit();
          bwg_exit_fullscreen(bwg_windowheight);
        }
        else {
          /* Fullscreen.*/
          jQuery("#spider_popup_wrap").fullscreen();
          /*jQuery("#spider_popup_wrap").on("fscreenopen", function() {
          if (jQuery.fullscreen.isFullScreen()) {*/
          var screen_width = screen.width;
          var screen_height = screen.height;
          jQuery("#spider_popup_wrap").css({
            width: screen_width,
            height: screen_height,
            left: 0,
            top: 0,
            margin: 0,
            zIndex: 100000
          });
          jQuery(".bwg_image_wrap").css({width: screen_width - comment_container_width});
          jQuery(".bwg_image_container").css({height: (screen_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)), width: screen_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0)});
          jQuery(".bwg_image_info").css("height","auto");
          bwg_info_height_set();
          jQuery(".bwg_popup_image").css({
            maxWidth: (screen_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0)),
            maxHeight: (screen_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0))
          });

          jQuery(".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video").css({
            maxWidth: (screen_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0)),
            maxHeight: (screen_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0))
          });

          bwg_resize_instagram_post();

          /* Set watermark container size.*/
          bwg_change_watermark_container();
          if ( gallery_box_data['width_or_height'] == 'width') {
            jQuery(".bwg_filmstrip_container").css({width: screen_width - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0)}, 500);
            jQuery(".bwg_filmstrip").css({width: screen_width - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height())}, 500);
            /* Set filmstrip initial position.*/
            bwg_set_filmstrip_pos(screen_width - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data);
          } else {
            jQuery(".bwg_filmstrip_container").css({height: (screen_height - (gallery_box_data['filmstrip_direction'] == 'horizontal') ? 'comment_container_width' : 0)});
            jQuery(".bwg_filmstrip").css({height: (screen_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0) - 2 * (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height() ) ) });
            /* Set filmstrip initial position.*/
            bwg_set_filmstrip_pos(screen_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height() ), '' ,gallery_box_data);
            gallery_box_data['filmstrip_direction'] == 'horizontal' ? '' : jQuery(".bwg_filmstrip_right").css({ top: (screen_height - jQuery('.bwg_filmstrip_right').height() ) });
          }
          jQuery(".bwg_resize-full").hide();
          jQuery(".bwg_fullscreen").attr("class", "bwg-icon-compress bwg_ctrl_btn bwg_fullscreen");
          jQuery(".bwg_fullscreen").attr("title", bwg_objectsL10n.bwg_exit_fullscreen);
          jQuery(".spider_popup_close").attr("class", "bwg_ctrl_btn spider_popup_close_fullscreen");
        }
      }
    }

    return false;
  });
  /* Play/pause.*/
  jQuery(".bwg_play_pause").on(bwg_click, function () {
    if (jQuery(".bwg_play_pause").length && jQuery(".bwg_play_pause").hasClass("bwg-icon-play") && !jQuery(".bwg_comment_container").hasClass("bwg_open")) {
      /* PLay.*/
      bwg_play( gallery_box_data['data'] );
      jQuery(".bwg_play_pause").attr("title", bwg_objectsL10n.bwg_pause);
      jQuery(".bwg_play_pause").attr("class", "bwg-icon-pause bwg_ctrl_btn bwg_play_pause");
    }
    else {
      /* Pause.*/
      window.clearInterval(bwg_playInterval);
      jQuery(".bwg_play_pause").attr("title", bwg_objectsL10n.bwg_play);
      jQuery(".bwg_play_pause").attr("class", "bwg-icon-play bwg_ctrl_btn bwg_play_pause");
    }
  });
  /* Open with autoplay.*/
  if (gallery_box_data['open_with_autoplay']) {
    bwg_play( gallery_box_data['data'] );
    jQuery(".bwg_play_pause").attr("title", bwg_objectsL10n.bwg_pause);
    jQuery(".bwg_play_pause").attr("class", "bwg-icon-pause bwg_ctrl_btn bwg_play_pause");
  }
  /* Open with fullscreen.*/
  if (gallery_box_data['open_with_fullscreen']) {
    bwg_open_with_fullscreen();
  }

  jQuery(".bwg_popup_image").removeAttr("width");
  jQuery(".bwg_popup_image").removeAttr("height");
  /* }); */

  jQuery(window).focus(function() {
    /* event_stack = [];*/
    if (jQuery(".bwg_play_pause").length && !jQuery(".bwg_play_pause").hasClass("bwg-icon-play")) {
      bwg_play( gallery_box_data['data'] );
    }
    /*var i = 0;
    jQuery(".bwg_slider").children("span").each(function () {
      if (jQuery(this).css('opacity') == 1) {
        jQuery("#bwg_current_image_key").val(i);
      }
      i++;
    });*/
  });
  jQuery(window).blur(function() {
    event_stack = [];
    window.clearInterval(bwg_playInterval);
  });
  var lightbox_ctrl_btn_pos = gallery_box_data['lightbox_ctrl_btn_pos'];
  if ( gallery_box_data['open_ecommerce'] == 1) {
    setTimeout(function(){ bwg_ecommerce();  }, 400);
  }
  if ( gallery_box_data['open_comment'] == 1 ) {
    bwg_comment();
  }
}

function spider_createpopup(url, current_view, width, height, duration, description, lifetime, lightbox_ctrl_btn_pos) {
  url = url.replace(/&#038;/g, '&');
  if (isPopUpOpened) { return };
  isPopUpOpened = true;
  if (spider_isunsupporteduseragent()) {
    return;
  }
  bwg_overflow_initial_value = jQuery("html").css("overflow");
  bwg_overflow_x_initial_value = jQuery("html").css("overflow-x");
  bwg_overflow_y_initial_value = jQuery("html").css("overflow-y");
  jQuery("html").attr("style", "overflow:hidden !important;");
  jQuery("#bwg_spider_popup_loading_" + current_view).show();
  jQuery("#spider_popup_overlay_" + current_view).css({display: "block"});
  jQuery.ajax({
    type: "GET",
    url: url,
    success: function (data) {
      var popup = jQuery(
        '<div id="spider_popup_wrap" class="spider_popup_wrap" style="' +
        ' width:' + width + 'px;' +
        ' height:' + height + 'px;' +
        ' margin-top:-' + height / 2 + 'px;' +
        ' margin-left: -' + width / 2 + 'px; ">' +
        data +
        '</div>')
        .hide()
        .appendTo("body");
      gallery_box_ready();
      spider_showpopup(description, lifetime, popup, duration, lightbox_ctrl_btn_pos);
    },
    beforeSend: function() {},
    complete:function() {}
  });
}

function spider_showpopup( description, lifetime, popup, duration, lightbox_ctrl_btn_pos ) {
  var data = gallery_box_data['data'];
  var cur_image_key = parseInt(jQuery('#bwg_current_image_key').val());
  if ( typeof data[cur_image_key] != 'undefined' ) {
    isPopUpOpened = true;
    var is_embed = data[cur_image_key]['filetype'].indexOf("EMBED_") > -1 ? true : false;
    if ( !is_embed ) {
      if ( jQuery('#spider_popup_wrap .bwg_popup_image_spun img').prop('complete') ) {
        /* Already loaded. */
        bwg_first_image_load(popup, lightbox_ctrl_btn_pos);
      }
      else {
        jQuery('#spider_popup_wrap .bwg_popup_image_spun img').on('load error', function () {
          bwg_first_image_load(popup, lightbox_ctrl_btn_pos);
        });
      }
    }
    else {
      bwg_first_image_load(popup, lightbox_ctrl_btn_pos);
    }
    if ( data[cur_image_key]['filetype'] == 'EMBED_OEMBED_INSTAGRAM_POST' ) {
      if ( typeof instgrm !== 'undefined' && typeof instgrm.Embeds !== 'undefined' ) {
        instgrm.Embeds.process();
      }
    }
  }
}

function bwg_first_image_load(popup, lightbox_ctrl_btn_pos) {
  popup.show();

  var bwg_ctrl_btn_container_height = jQuery(".bwg_ctrl_btn_container").height();
  if (lightbox_ctrl_btn_pos == 'bottom') {
      jQuery(".bwg_toggle_container").css("bottom", bwg_ctrl_btn_container_height + "px");
  }
  else if (lightbox_ctrl_btn_pos == 'top') {
      jQuery(".bwg_toggle_container").css("top", bwg_ctrl_btn_container_height + "px");
  }
  jQuery( ".bwg_spider_popup_loading" ).hide();
  if ( gallery_box_data['preload_images'] == 1 ) {
    bwg_preload_images( parseInt( jQuery( '#bwg_current_image_key' ).val() ) );
  }
  bwg_load_filmstrip();
  bwg_info_height_set();
}

function spider_isunsupporteduseragent() {
  return (!window.XMLHttpRequest);
}

function spider_destroypopup(duration) {
  if (document.getElementById("spider_popup_wrap") != null) {
    if (typeof jQuery().fullscreen !== 'undefined' && jQuery.isFunction(jQuery().fullscreen)) {
      if (jQuery.fullscreen.isFullScreen()) {
        jQuery.fullscreen.exit();
      }
    }
    if (typeof enable_addthis != "undefined" && enable_addthis) {
      jQuery(".at4-share-outer").hide();
    }
    setTimeout(function () {
      jQuery(".spider_popup_wrap").remove();
      jQuery(".bwg_spider_popup_loading").css({display: "none"});
      jQuery(".spider_popup_overlay").css({display: "none"});
      jQuery(document).off("keydown");
      if ( bwg_overflow_initial_value !== false ) {
        jQuery("html").css("overflow", bwg_overflow_initial_value);
      }
      if ( bwg_overflow_x_initial_value !== false ) {
        jQuery("html").css("overflow-x", bwg_overflow_x_initial_value);
      }
      if ( bwg_overflow_y_initial_value !== false ) {
        jQuery("html").css("overflow-y", bwg_overflow_y_initial_value);
      }
    }, 20);
  }
  isPopUpOpened = false;
  var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
  var viewportmeta = document.querySelector('meta[name="viewport"]');
  if (isMobile && viewportmeta) {
    viewportmeta.content = 'width=device-width, initial-scale=1';
  }
  var scrrr = jQuery(document).scrollTop();
  if ( bwg_objectsL10n.is_pro ) {
    /*window.location.hash = "";*/
    location.replace("#");
  }
  jQuery(document).scrollTop(scrrr);
  if ( typeof gallery_box_data['bwg_playInterval'] != "undefined" ) {
    clearInterval(gallery_box_data['bwg_playInterval']);
  }
}

function get_ajax_pricelist(){
  var post_data = {};
  jQuery(".add_to_cart_msg").html("");
  post_data["ajax_task"] = "display";
  post_data["image_id"] = jQuery('#bwg_popup_image').attr('image_id');

  /* Loading. */
  jQuery("#ecommerce_ajax_loading").css('height', jQuery(".bwg_ecommerce_panel").css('height'));
  jQuery("#ecommerce_opacity_div").css('width', jQuery(".bwg_ecommerce_panel").css('width'));
  jQuery("#ecommerce_opacity_div").css('height', jQuery(".bwg_ecommerce_panel").css('height'));
  jQuery("#ecommerce_loading_div").css('width', jQuery(".bwg_ecommerce_panel").css('width'));
  jQuery("#ecommerce_loading_div").css('height', jQuery(".bwg_ecommerce_panel").css('height'));

  jQuery("#ecommerce_opacity_div").css('display', 'block');
  jQuery("#ecommerce_loading_div").css('display', 'table-cell');
  jQuery.ajax({
    type: "POST",
    url:  jQuery('#bwg_ecommerce_form').attr('action'),
    data: post_data,
    success: function (data) {
      jQuery(".pge_tabs li a").on("click", function(){
        jQuery(".pge_tabs_container > div").hide();
        jQuery(".pge_tabs li").removeClass("pge_active");
        jQuery(jQuery(this).attr("href")).show();
        jQuery(this).closest("li").addClass("pge_active");
        jQuery("[name=type]").val(jQuery(this).attr("href").substr(1));
        return false;
      });
      var manual = jQuery(data).find('.manual').html();
      jQuery('.manual').html(manual);

      var downloads = jQuery(data).find('.downloads').html();
      jQuery('.downloads').html(downloads);

      var pge_options = jQuery(data).find('.pge_options').html();
      jQuery('.pge_options').html(pge_options);

      var pge_add_to_cart = jQuery(data).find('.pge_add_to_cart').html();
      jQuery('.pge_add_to_cart').html(pge_add_to_cart);
    },
    beforeSend: function(){
    },
    complete:function(){
      jQuery("#ecommerce_opacity_div").css('display', 'none');
      jQuery("#ecommerce_loading_div").css('display', 'none');

      /*
      Update scrollbar.
      jQuery(".bwg_ecommece_panel").mCustomScrollbar({scrollInertia: 150 });
      jQuery(".bwg_ecommerce_close_btn").click(bwg_ecommerce);
      */
    }
  });
  return false;
}

/* Submit popup. */
function spider_ajax_save(form_id) {
  var post_data = {};
  post_data["bwg_name"] = jQuery("#bwg_name").val();
  post_data["bwg_comment"] = jQuery("#bwg_comment").val();
  post_data["bwg_email"] = jQuery("#bwg_email").val();
  post_data["bwg_captcha_input"] = jQuery("#bwg_captcha_input").val();
  post_data["ajax_task"] = jQuery("#ajax_task").val();
  post_data["image_id"] = jQuery("#image_id").val();
  post_data["comment_id"] = jQuery("#comment_id").val();

  /* Loading. */
  jQuery("#ajax_loading").css('height', jQuery(".bwg_comments").css('height'));
  jQuery("#opacity_div").css('width', jQuery(".bwg_comments").css('width'));
  jQuery("#opacity_div").css('height', jQuery(".bwg_comments").css('height'));
  jQuery("#loading_div").css('width', jQuery(".bwg_comments").css('width'));
  jQuery("#loading_div").css('height', jQuery(".bwg_comments").css('height'));
  document.getElementById("opacity_div").style.display = '';
  document.getElementById("loading_div").style.display = 'table-cell';
  jQuery.ajax({
    type: "POST",
    url:  jQuery('#' + form_id).attr('action'),
    data: post_data,
    success: function (data) {
      var str = jQuery(data).find('.bwg_comments').html();
      jQuery('.bwg_comments').html(str);
    },
    beforeSend: function(){
    },
    complete:function(){
      document.getElementById("opacity_div").style.display = 'none';
      document.getElementById("loading_div").style.display = 'none';
      /* Update scrollbar. */
      jQuery(".bwg_comments").mCustomScrollbar({scrollInertia: 150,
        advanced:{
          updateOnContentResize: true
        }
      });
      /* Bind comment container close function to close button. */
      jQuery(".bwg_comments_close_btn").click(bwg_comment);
      bwg_captcha_refresh('bwg_captcha');
    }
  });
  return false;
}

/* Submit rating. */
function spider_rate_ajax_save(form_id) {
  var post_data = {};
  post_data["image_id"] = jQuery("#" + form_id + " input[name='image_id']").val();
  post_data["rate"] = jQuery("#" + form_id + " input[name='score']").val();
  post_data["ajax_task"] = jQuery("#rate_ajax_task").val();
  return jQuery.ajax({
    type: "POST",
    url:   jQuery('#' + form_id).attr('action'),
    data: post_data,
    success: function (data) {
      var str = jQuery(data).find('#' + form_id).html();
      jQuery('#' + form_id).html(str);
    },
    beforeSend: function(){
    },
    complete:function(){}
  });
}

/* Set value by ID. */
function spider_set_input_value(input_id, input_value) {
  if (document.getElementById(input_id)) {
    document.getElementById(input_id).value = input_value;
  }
}

/* Submit form by ID. */
function spider_form_submit(event, form_id) {
  if (document.getElementById(form_id)) {
    document.getElementById(form_id).submit();
  }
  if (event.preventDefault) {
    event.preventDefault();
  }
  else {
    event.returnValue = false;
  }
}

/* Check if required field is empty. */
function spider_check_required(id, name) {
  if (jQuery('#' + id).val() == '') {
    alert(name + ' ' + bwg_objectsL10n.bwg_field_required);
    jQuery('#' + id).attr('style', 'border-color: #FF0000;');
    jQuery('#' + id).focus();
    return true;
  }
  else {
    return false;
  }
}

/* Check if privacy polic field is checked. */
function comment_check_privacy_policy() {
	var bwg_submit = jQuery('#bwg_submit');
	bwg_submit.removeClass('bwg-submit-disabled');
	bwg_submit.removeAttr("disabled");
	if ( !jQuery('#bwg_comment_privacy_policy').is(':checked') ) {
		bwg_submit.addClass('bwg-submit-disabled');
		bwg_submit.attr('disabled', 'disabled');
	}
}

/* Check Email. */
function spider_check_email(id) {
  if (jQuery('#' + id).val() != '') {
    var email = jQuery('#' + id).val().replace(/^\s+|\s+$/g, '');
    if (email.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1) {
      alert(bwg_objectsL10n.bwg_mail_validation);
      return true;
    }
    return false;
  }
}

/* Refresh captcha. */
function bwg_captcha_refresh(id) {
  if (document.getElementById(id + "_img") && document.getElementById(id + "_input")) {
    srcArr = document.getElementById(id + "_img").src.split("&r=");
    document.getElementById(id + "_img").src = srcArr[0] + '&r=' + Math.floor(Math.random() * 100);
    document.getElementById(id + "_img").style.display = "inline-block";
    document.getElementById(id + "_input").value = "";
  }
}

function bwg_play_instagram_video(obj,bwg) {
  jQuery(obj).parent().find("video").each(function () {
    if (jQuery(this).get(0).paused) {
      jQuery(this).get(0).play();
      jQuery(obj).children().hide();
    }
    else {
      jQuery(this).get(0).pause();
      jQuery(obj).children().show();
    }
  })
}

/**
 * Add comment.
 *
 * @returns {boolean}
 */
function bwg_add_comment() {
	var form = jQuery('#bwg_comment_form');
	var url = form.attr('action');
	var post_data = {};
	post_data['ajax_task'] = 'add_comment';
	post_data['comment_name'] = form.find('#bwg_name').val();
  post_data['comment_email'] = form.find('#bwg_email').val();
  post_data['comment_text'] = form.find('#bwg_comment').val();
  post_data['comment_captcha'] = form.find('#bwg_captcha_input').val();
  post_data['popup_enable_captcha'] = form.find('#bwg_popup_enable_captcha').val();
  post_data['privacy_policy'] = ( form.find('#bwg_comment_privacy_policy').is(":checked") ) ? 1 : 0;
	post_data['comment_image_id'] = jQuery('#bwg_popup_image').attr('image_id');
  post_data['comment_moderation'] = form.find('#bwg_comment_moderation').val();
	jQuery('.bwg_spider_ajax_loading').hide();
	jQuery.ajax({
		url:   url,
		type: "POST",
		dataType: 'json',
		data: post_data,
		success: function ( res ) {
			jQuery('.bwg_comment_error').text('');
			if ( res.error == true ) {
				jQuery.each(res.error_messages, function( index, value ) {
				  if ( value ) {
					  jQuery('.bwg_comment_'+ index +'_error').text(value);
				  }
				});
			}
			else {
        form.find('#bwg_comment').val('');
				jQuery('.bwg_comment_waiting_message').hide();
				if( res.published == 0 ) {
					jQuery('.bwg_comment_waiting_message').show();
				}
				if ( res.html_comments_block != '' ) {
					jQuery('#bwg_added_comments').html(res.html_comments_block).show();
				}
			}
		},
		beforeSend: function() {
			jQuery('.bwg_spider_ajax_loading').show();
		},
		complete: function() {
			if ( form.find('#bwg_comment_privacy_policy').length > 0 ) {
				form.find('#bwg_comment_privacy_policy').prop('checked', false);
				comment_check_privacy_policy();
			}
			bwg_captcha_refresh('bwg_captcha');
			jQuery('.bwg_spider_ajax_loading').hide();
		},
		error:function() {}
	});
	return false;
}

/**
 * Remove comment.
 *
 * @param id_comment
 * @returns {boolean}
 */
function bwg_remove_comment( id_comment ) {
	var form = jQuery('#bwg_comment_form');
	var url = form.attr('action');
	var post_data = {};
	post_data['ajax_task'] = 'delete_comment';
	post_data['id_image'] = jQuery('#bwg_popup_image').attr('image_id');
	post_data['id_comment'] = id_comment;
	jQuery.ajax({
		url:   url,
		type: "POST",
		dataType: 'json',
		data: post_data,
		success: function ( res ) {
			if ( res.error == false) {
				jQuery('#bwg_comment_block_' + id_comment ).fadeOut( "slow").remove();
			}
		},
		beforeSend: function() {
		},
		complete:function() {
		},
		error:function() {}
	});
	return false;
}

function bwg_gallery_box( image_id, bwg_container, openEcommerce, gallery_id ) {
  jQuery(".bwg-validate").each(function() {
    jQuery(this).on("keypress change", function () {
      jQuery(this).parent().next().find(".bwg_comment_error").html("");
    });
  });
  if ( typeof openEcommerce == "undefined" ) {
    openEcommerce = false;
  }
  var bwg = bwg_container.data('bwg');
  var bwg_lightbox_url;
  if ( bwg_container.find(".bwg-container").data('lightbox-url') ) {
    /* To read from updated container after ajax (e.g. open lightbox from gallery in albums).*/
    bwg_lightbox_url = bwg_container.find(".bwg-container").data('lightbox-url');
  }
  else {
    bwg_lightbox_url = bwg_container.data('lightbox-url');
  }
  var cur_gal_id = bwg_container.find('.cur_gal_id').val();
  var filterTags = jQuery('#bwg_tag_id_' + cur_gal_id).val();
  filterTags = filterTags ? filterTags : 0;
  var ecommerce = openEcommerce == true ? "&open_ecommerce=1" : "";
  var filtersearchname = jQuery("#bwg_search_input_" + bwg ).val();
  var filtersortby = jQuery("#bwg_order_" + bwg).val() ? "&filtersortby=" + jQuery("#bwg_order_" + bwg).val() : '';
	filtersearchname = filtersearchname ? filtersearchname : '';

  if ( typeof gallery_id != "undefined" ) {
    /* Open lightbox with hash.*/
    bwg_lightbox_url += '&gallery_id=' + gallery_id;
  }
  var open_comment = '';
  var open_comment_attr = jQuery('#bwg_blog_style_share_buttons_' + image_id).attr('data-open-comment');
  if (typeof open_comment_attr !== typeof undefined && open_comment_attr !== false) {
	open_comment = '&open_comment=1';
  }
  var bwg_random_seed = jQuery("#bwg_random_seed_"+bwg).val();
  spider_createpopup(bwg_lightbox_url + '&bwg_random_seed='+bwg_random_seed+'&image_id=' + image_id + "&filter_tag=" + filterTags + ecommerce + open_comment + '&filter_search_name=' + filtersearchname + filtersortby, bwg, bwg_container.data('popup-width'), bwg_container.data('popup-height'), 1, 'testpopup', 5, bwg_container.data('buttons-position'));
}

function bwg_change_image_lightbox(current_key, key, data, from_effect) {
  jQuery("#bwg_rate_form input[name='image_id']").val(data[key]["id"]);
  bwg_current_key = gallery_box_data['bwg_current_key'];
  /* var bwg_image_info_pos = jQuery(".bwg_ctrl_btn_container").height(); */
  jQuery(".bwg_image_info").css("height","auto");
  setTimeout(function(){
    bwg_info_height_set();
        if(jQuery('.bwg_image_description').height() > jQuery('.bwg_image_info').height() && jQuery('.mCSB_container').hasClass('mCS_no_scrollbar')){
            jQuery(".bwg_image_info").mCustomScrollbar("destroy");
        }
        if (!jQuery('.bwg_image_info').hasClass('mCustomScrollbar')) {
            if (typeof jQuery().mCustomScrollbar !== 'undefined' && jQuery.isFunction(jQuery().mCustomScrollbar)) {
                jQuery(".bwg_image_info").mCustomScrollbar({
                    scrollInertia: 150,
                    advanced:{
                        updateOnContentResize: true
                    }
                });
            }
        }
    }, 200);
  jQuery("#spider_popup_left").show();
  jQuery("#spider_popup_right").show();
  jQuery(".bwg_image_info").hide();
  if (gallery_box_data['enable_loop'] == 0) {
    if (key == (parseInt(data.length) - 1)) {
      jQuery("#spider_popup_right").hide();
    }
    if (key == 0) {
      jQuery("#spider_popup_left").hide();
    }
  }
  var ecommerceACtive = gallery_box_data['ecommerceACtive'];
  if( ecommerceACtive == 1 && gallery_box_data['enable_image_ecommerce'] == 1 ) {
    if( gallery_box_data['data'][key]["pricelist"] == 0) {
      jQuery(".bwg_ecommerce").hide();
    }
    else {
      jQuery(".bwg_ecommerce").show();
      jQuery(".pge_tabs li").hide();
      jQuery("#downloads").hide();
      jQuery("#manual").hide();
      var pricelistSections = gallery_box_data['data'][key]["pricelist_sections"].split(",");

      if(pricelistSections){
        jQuery("#" + pricelistSections[0]).show();
        jQuery("[name=type]").val(pricelistSections[0]);
        if(pricelistSections.length > 1){
          jQuery(".pge_tabs").show();
          for( k=0 ; k<pricelistSections.length; k++ ){
            jQuery("#" + pricelistSections[k] + "_li").show();
          }
        }
        else{
          jQuery(".pge_tabs").hide();
        }
      }
      else{
        jQuery("[name=type]").val("");
      }
    }
  }
  /* Pause videos.*/
  jQuery("#bwg_image_container").find("iframe").each(function () {
    jQuery(this)[0].contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
    jQuery(this)[0].contentWindow.postMessage('{ "method": "pause" }', "*");
    jQuery(this)[0].contentWindow.postMessage('pause', '*');
  });
  jQuery("#bwg_image_container").find("video").each(function () {
    jQuery(this).trigger('pause');
  });
  if(typeof data == 'undefined') {
    data = gallery_box_data['data'];
  }
  if ( typeof data[key] != 'undefined' ) {
    if (typeof data[current_key] != 'undefined') {
      if (jQuery(".bwg_play_pause").length && !jQuery(".bwg_play_pause").hasClass("bwg-icon-play")) {
        bwg_play( data );
      }

      if (!from_effect) {
        /* Change image key.*/
        jQuery("#bwg_current_image_key").val(key);
        /*if (current_key == '-1') {
          current_key = jQuery(".bwg_thumb_active").children("img").attr("image_key");
        }*/
      }
      if (gallery_box_data['bwg_trans_in_progress']) {
        gallery_box_data['event_stack'].push(current_key + '-' + key);
        return;
      }
      var direction = 'right';
      if (bwg_current_key > key) {
        var direction = 'left';
      }
      else if (bwg_current_key == key) {
        return;
      }

      jQuery(".bwg_image_count").html(data[key]["number"]);
      /* Set filmstrip initial position.*/
      jQuery(".bwg_watermark").css({display: 'none'});
      /* Set active thumbnail position.*/
      if ( gallery_box_data['width_or_height'] == 'width' ) {
        bwg_current_filmstrip_pos = key * (jQuery(".bwg_filmstrip_thumbnail").width() + 2 + 2 * gallery_box_data['lightbox_filmstrip_thumb_border_width']);
      } else if ( gallery_box_data['width_or_height'] == 'height' ) {
        bwg_current_filmstrip_pos = key * (jQuery(".bwg_filmstrip_thumbnail").height() + 2 + 2 * gallery_box_data['lightbox_filmstrip_thumb_border_width']);
      }
      gallery_box_data['bwg_current_key'] = key;
      if ( bwg_objectsL10n.is_pro ) {
        /* Change hash.*/
        /*window.location.hash = "bwg"+gallery_box_data['gallery_id'] +"/" + data[key]["id"];*/
        location.replace("#bwg" + gallery_box_data['gallery_id'] + "/" + data[key]["id"]);
        history.replaceState(undefined, undefined, "#bwg" + gallery_box_data['gallery_id'] + "/" + data[key]["id"]);
      }
      jQuery("#bwg_rate_form input[name='image_id']").val(data[key]["id"]);
      /* Change image id for rating.*/
      if (gallery_box_data['popup_enable_rate']) {
        jQuery("#bwg_star").attr("data-score", data[key]["avg_rating"]);
        jQuery("#bwg_star").removeAttr("title");
        data[key]['cur_key'] = key;
        bwg_rating( data[key]["rate"], data[key]["rate_count"], data[key]["avg_rating"], key );
      }
      /* Increase image hit counter.*/
      spider_set_input_value('rate_ajax_task', 'save_hit_count');

      spider_rate_ajax_save('bwg_rate_form');
      jQuery(".bwg_image_hits span").html(++data[key]["hit_count"]);
      /* Change image id.*/
      jQuery("#bwg_popup_image").attr('image_id', data[key]["id"]);
      /* Change image title, description.*/
      jQuery(".bwg_image_title").html(jQuery('<span />').html(data[key]["alt"]).text());
      jQuery(".bwg_image_description").html(jQuery('<span />').html(data[key]["description"]).text());
      /* Set active thumbnail.*/

      jQuery(".bwg_filmstrip_thumbnail").removeClass("bwg_thumb_active").addClass("bwg_thumb_deactive");
      jQuery("#bwg_filmstrip_thumbnail_" + key).removeClass("bwg_thumb_deactive").addClass("bwg_thumb_active");
      jQuery(".bwg_image_info").css("opacity", 1);
      if (data[key]["alt"].trim() == "") {
        if (data[key]["description"].trim() == "") {
          jQuery(".bwg_image_info").css("opacity", 0);
        }
      }
      if (jQuery(".bwg_image_info_container1").css("display") != 'none') {
        jQuery(".bwg_image_info_container1").css("display", "table-cell");
      }
      else {
        jQuery(".bwg_image_info_container1").css("display", "none");
      }
      /* Change image rating.*/
      if (jQuery(".bwg_image_rate_container1").css("display") != 'none') {
        jQuery(".bwg_image_rate_container1").css("display", "table-cell");
      }
      else {
        jQuery(".bwg_image_rate_container1").css("display", "none");
      }
      var current_image_class = jQuery(".bwg_popup_image_spun").css("zIndex") == 2 ? ".bwg_popup_image_spun" : ".bwg_popup_image_second_spun";
      var next_image_class = current_image_class == ".bwg_popup_image_second_spun" ? ".bwg_popup_image_spun" : ".bwg_popup_image_second_spun";

      var is_embed = data[key]['filetype'].indexOf("EMBED_") > -1 ? true : false;
      var is_embed_instagram_post = data[key]['filetype'].indexOf('INSTAGRAM_POST') > -1 ? true : false;
      var is_embed_instagram_video = data[key]['filetype'].indexOf('INSTAGRAM_VIDEO') > -1 ? true : false;
      var is_ifrem = ( jQuery.inArray( data[key]['filetype'] , ['EMBED_OEMBED_YOUTUBE_VIDEO', 'EMBED_OEMBED_VIMEO_VIDEO', 'EMBED_OEMBED_FACEBOOK_VIDEO', 'EMBED_OEMBED_DAILYMOTION_VIDEO'] ) !== -1 ) ? true : false;
      var cur_height = jQuery(current_image_class).height();
      var cur_width = jQuery(current_image_class).width();
      var innhtml = '<span class="bwg_popup_image_spun1" style="display: ' + ( !is_embed ? 'table' : 'block' ) + '; width: inherit; height: inherit;"><span class="bwg_popup_image_spun2" style="display:' + (!is_embed ? 'table-cell' : 'block') + '; vertical-align: middle;text-align: center;height: 100%;">';
      if ( !is_embed ) {
        jQuery(".bwg-loading").removeClass("bwg-hidden");
        jQuery("#bwg_download").removeClass("bwg-hidden");
        innhtml += '<img style="max-height: ' + cur_height + 'px; max-width: ' + cur_width + 'px;" class="bwg_popup_image bwg_popup_watermark" src="' + gallery_box_data['site_url'] + jQuery('<span />').html(decodeURIComponent(data[key]["image_url"])).text() + '" alt="' + data[key]["alt"] + '" />';
      }
      else { /*is_embed*/
        /* hide download button if image source is embed */
        jQuery("#bwg_download").addClass("bwg-hidden");
        innhtml += '<span class="bwg_popup_embed bwg_popup_watermark" style="display: ' + ( is_ifrem ? 'block' : 'table' ) + '; table-layout: fixed; height: 100%;">' + (is_embed_instagram_video ? '<div class="bwg_inst_play_btn_cont" onclick="bwg_play_instagram_video(this)" ><div class="bwg_inst_play"></div></div>' : ' ');
        if (is_embed_instagram_post) {
          var post_width = 0;
          var post_height = 0;
          if (cur_height < cur_width + 88) {
            post_height = cur_height;
            post_width = post_height - 88;
          }
          else {
            post_width = cur_width;
            post_height = post_width + 88;
          }
          innhtml += spider_display_embed(data[key]['filetype'], data[key]['image_url'], data[key]['filename'], { class:"bwg_embed_frame", 'data-width': data[key]['image_width'], 'data-height': data[key]['image_height'], frameborder: "0", allowfullscreen: "allowfullscreen", style: "width:" + post_width + "px; height:" + post_height + "px; vertical-align:middle; display:inline-block; position:relative;"});
        }
        else {
          innhtml += spider_display_embed(data[key]['filetype'], data[key]['image_url'], data[key]['filename'], { class:"bwg_embed_frame", frameborder:"0", allowfullscreen:"allowfullscreen", style: "display:" + ( is_ifrem ? 'block' : 'table-cell' ) + "; width:inherit; height:inherit; vertical-align:middle;" });
        }
        innhtml += "</span>";
      }
      innhtml += '</span></span>';
      jQuery(next_image_class).html(innhtml);
      jQuery(next_image_class).find("img").on("load error", function () {
        jQuery(".bwg-loading").addClass("bwg-hidden");
      });
      jQuery(".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video").css({
        maxWidth: cur_width,
        maxHeight: cur_height,
        height: 'auto',
      });

      function bwg_afterload() {
        if (gallery_box_data['preload_images']) {
          bwg_preload_images(key);
        }
        window['bwg_'+gallery_box_data['bwg_image_effect']](current_image_class, next_image_class, direction);
        jQuery(current_image_class).find('.bwg_fb_video').each(function () {
          jQuery(this).attr('src', '');
        });
        if ( !is_embed ) {
          jQuery("#bwg_fullsize_image").attr("href", gallery_box_data['site_url'] + decodeURIComponent(data[key]['image_url']));
          jQuery("#bwg_download").attr("href", gallery_box_data['site_url'] + decodeURIComponent(data[key]['thumb_url']).replace('/thumb/', '/.original/'));
        }
        else {
          jQuery("#bwg_fullsize_image").attr("href", decodeURIComponent(data[key]['image_url']));
        }
        var image_arr = decodeURIComponent(data[key]['image_url']).split("/");
        jQuery("#bwg_download").attr("download", image_arr[image_arr.length - 1].replace(/\?bwg=(\d+)$/, ""));
        /* Change image social networks urls.*/
        var bwg_share_url = encodeURIComponent(gallery_box_data['bwg_share_url']) + "=" + data[key]['id'] + encodeURIComponent('#bwg'+gallery_box_data['gallery_id']+'/') + data[key]['id'];

        if (is_embed) {
          var bwg_share_image_url = encodeURIComponent(data[key]['thumb_url']);
        }
        else {
          var bwg_share_image_url = gallery_box_data['bwg_share_image_url'] + encodeURIComponent(encodeURIComponent(data[key]['pure_image_url']));
        }
        bwg_share_image_url = bwg_share_image_url.replace(/%252F|%25252F/g, '%2F');
        if (typeof addthis_share != "undefined") {
          addthis_share.url = bwg_share_url;
        }
        jQuery("#bwg_facebook_a").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" + bwg_share_url);
        jQuery("#bwg_twitter_a").attr("href", "https://twitter.com/share?url=" + bwg_share_url);
        jQuery("#bwg_pinterest_a").attr("href", "http://pinterest.com/pin/create/button/?s=100&url=" + bwg_share_url + "&media=" + bwg_share_image_url + "&description=" + data[key]['alt'] + '%0A' + data[key]['description']);
        jQuery("#bwg_tumblr_a").attr("href", "https://www.tumblr.com/share/photo?source=" + bwg_share_image_url + "&caption=" + data[key]['alt'] + "&clickthru=" + bwg_share_url);
        /* Load comments.*/
        if (jQuery(".bwg_comment_container").hasClass("bwg_open")) {
          jQuery(".bwg_comments .mCSB_container").css("top","0");
          /* Todo: do not call comments if it's empty */
       /*   if (data[key]["comment_count"] == 0) {
            jQuery("#bwg_added_comments").hide();
          }
          else {*/
            jQuery("#bwg_added_comments").show();
            spider_set_input_value('ajax_task', 'display');
            spider_set_input_value('image_id', jQuery('#bwg_popup_image').attr('image_id'));
            spider_ajax_save('bwg_comment_form');
       /*   }*/
        }
        if (jQuery(".bwg_ecommerce_container").hasClass("bwg_open")) {
          /* Pricelist */
          if(data[key]["pricelist"] == 0){
            /* Close ecommerce.*/
            bwg_popup_sidebar_close(jQuery(".bwg_ecommerce_container"));
            bwg_animate_image_box_for_hide_sidebar();

            jQuery(".bwg_ecommerce_container").attr("class", "bwg_ecommerce_container bwg_close");
            jQuery(".bwg_ecommerce").attr("title", bwg_objectsL10n.bwg_show_ecommerce);
            jQuery(".spider_popup_close_fullscreen").show();
          }
          else{
            get_ajax_pricelist();
          }
        }
        /* Update custom scroll.*/
        if (typeof jQuery().mCustomScrollbar !== 'undefined') {
          if (jQuery.isFunction(jQuery().mCustomScrollbar)) {
            jQuery(".bwg_comments").mCustomScrollbar({
              advanced:{
                updateOnContentResize: true
              }
            });
          }
        }
        jQuery(".bwg_comments .mCSB_scrollTools").hide();
        if (gallery_box_data['enable_image_filmstrip']) {
          bwg_move_filmstrip();
        }
        bwg_resize_instagram_post();
      }
      if ( !is_embed ) {
        var cur_img = jQuery(next_image_class).find('img');
        cur_img.one('load', function() {
          bwg_afterload();
        }).each(function() {
          if(this.complete) jQuery(this).load();
        });
      }
      else {
        bwg_afterload();
      }
      if ( typeof instgrm !== 'undefined' && typeof instgrm.Embeds !== 'undefined' ) {
         instgrm.Embeds.process();
      }
    }
  }
}

function bwg_preload_images_lightbox( key ) {
  var data = gallery_box_data['data'];
  var count_all = data.length;
  var preloadCount = ( gallery_box_data['preload_images_count'] == 0 || gallery_box_data['preload_images_count'] >= count_all ) ? count_all : gallery_box_data['preload_images_count'];
  var indexedImgCount = 0;
  for ( var i = 1; indexedImgCount < preloadCount; i++ ) {
    var sign = 1;
    do {
      var index = ( key + i * sign + count_all ) % count_all;
      if ( typeof data[index] != "undefined" ) {
        var is_embed = data[index]['filetype'].indexOf( "EMBED_" ) > -1 ? true : false;
        if ( !is_embed ) {
          jQuery( "<img/>" ).attr( "src", gallery_box_data['site_url'] + jQuery( '<span />' ).html(decodeURIComponent(data[index]["image_url"])).text() );
        }
      }
      sign *= -1;
      indexedImgCount++;
    }
    while ( sign != 1 );
  }
}

/* open  popup sidebar  */
function bwg_popup_sidebar_open(obj){
  var comment_container_width = gallery_box_data['lightbox_comment_width'];
  var lightbox_comment_pos = gallery_box_data['lightbox_comment_pos'];
  if (comment_container_width > jQuery(window).width()) {
    comment_container_width = jQuery(window).width();
    obj.css({
      width: comment_container_width,
    });
    jQuery(".spider_popup_close_fullscreen").hide();
    jQuery(".spider_popup_close").hide();
    if (jQuery(".bwg_ctrl_btn").hasClass("bwg-icon-pause")) {
      var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
      jQuery(".bwg_play_pause").trigger(isMobile ? 'touchend' : 'click');
    }
  }
  else {
    jQuery(".spider_popup_close_fullscreen").show();
  }

  if(lightbox_comment_pos == 'left') {
    obj.animate({left: 0}, 100);
  } else {
    obj.animate({right: 0}, 100);
  }
}

/* Open/close comments.*/
function bwg_comment() {
  jQuery(".bwg_watermark").css({display: 'none'});
  jQuery(".bwg_ecommerce_wrap").css("z-index","-1");
  jQuery(".bwg_comment_wrap").css("z-index","25");
  if(jQuery(".bwg_ecommerce_container").hasClass("bwg_open") ){
    bwg_popup_sidebar_close(jQuery(".bwg_ecommerce_container"));
    jQuery(".bwg_ecommerce_container").attr("class", "bwg_ecommerce_container bwg_close");
    jQuery(".bwg_ecommerce").attr("title", bwg_objectsL10n.bwg_show_ecommerce);

  }
  if (jQuery(".bwg_comment_container").hasClass("bwg_open") ) {
    /* Close comment.*/
    /* Check if lightbox was play before oppening comment container */
    if( jQuery(".bwg_comment_container").attr("data-play-status") == "1" ) {
      jQuery(".bwg_ctrl_btn.bwg_play_pause").removeClass("bwg-icon-play").addClass("bwg-icon-pause").attr('title',bwg_objectsL10n.bwg_pause);
    }
    bwg_popup_sidebar_close(jQuery(".bwg_comment_container"));
    bwg_animate_image_box_for_hide_sidebar();
    jQuery(".bwg_comment_wrap").css("z-index","-1");
    jQuery(".bwg_comment_container").attr("class", "bwg_comment_container bwg_close");
    jQuery(".bwg_comment").attr("title", bwg_objectsL10n.bwg_show_comments);
    jQuery(".spider_popup_close_fullscreen").show();
  }
  else {
    /* Open comment.*/
    /* Check if lightbox is playing before oppening comment and set status */
    if ( jQuery(".bwg_play_pause").hasClass("bwg-icon-pause") ) {
      jQuery(".bwg_comment_container").attr("data-play-status", "1");
    } else {
      jQuery(".bwg_comment_container").attr("data-play-status", "0");
    }
    jQuery(".bwg_ctrl_btn.bwg_play_pause").removeClass("bwg-icon-pause").addClass("bwg-icon-play").attr('title', bwg_objectsL10n.bwg_play);
    bwg_popup_sidebar_open(jQuery(".bwg_comment_container"));
    bwg_animate_image_box_for_show_sidebar();
    jQuery(".bwg_comment_container").attr("class", "bwg_comment_container bwg_open");
    jQuery(".bwg_comment").attr("title", bwg_objectsL10n.bwg_hide_comments);
    /* Load comments.*/
    var cur_image_key = parseInt(jQuery("#bwg_current_image_key").val());
    if ( typeof gallery_box_data['current_image_key'] != "undefined" && gallery_box_data['data'][cur_image_key]['comment_count'] != 0) {
      jQuery("#bwg_added_comments").show();
      spider_set_input_value('ajax_task', 'display');
      spider_set_input_value('image_id', jQuery('#bwg_popup_image').attr('image_id'));
      spider_ajax_save('bwg_comment_form');
    }
  }
  jQuery(".bwg_comments").mCustomScrollbar("update", {scrollInertia: 150,
    advanced:{
      updateOnContentResize: true
    }
  });
}

/* Open/close ecommerce.*/
function bwg_ecommerce() {
  jQuery(".bwg_watermark").css({display: 'none'});
  jQuery(".bwg_ecommerce_wrap").css("z-index","25");
  jQuery(".bwg_comment_wrap").css("z-index","-1");
  if (jQuery(".bwg_comment_container").hasClass("bwg_open")) {
    bwg_popup_sidebar_close(jQuery(".bwg_comment_container"));
    jQuery(".bwg_comment_container").attr("class", "bwg_comment_container bwg_close");
    jQuery(".bwg_comment").attr("title", bwg_objectsL10n.bwg_show_comments);
  }
  if (jQuery(".bwg_ecommerce_container").hasClass("bwg_open")) {
    /* Close ecommerce.*/
    bwg_popup_sidebar_close(jQuery(".bwg_ecommerce_container"));
    bwg_animate_image_box_for_hide_sidebar();
    jQuery(".bwg_ecommerce_container").attr("class", "bwg_ecommerce_container bwg_close");
    jQuery(".bwg_ecommerce").attr("title", bwg_objectsL10n.bwg_show_ecommerce);
  }
  else {
    /* Open ecommerce.*/
    bwg_popup_sidebar_open(jQuery(".bwg_ecommerce_container"));
    bwg_animate_image_box_for_show_sidebar();
    jQuery(".bwg_ecommerce_container").attr("class", "bwg_ecommerce_container bwg_open");
    jQuery(".bwg_ecommerce").attr("title", bwg_objectsL10n.bwg_hide_ecommerce);
    get_ajax_pricelist();
  }
}

function bwg_popup_sidebar_close(obj){
  var border_width = parseInt(obj.css('borderRightWidth'));
  if (!border_width) {
    border_width = 0;
  }
  if ( lightbox_comment_pos == 'left' ) {
    obj.animate({left: -obj.width() - border_width}, 100);
  }
  else if ( lightbox_comment_pos == 'right' ) {
    obj.animate({right: -obj.width() - border_width}, 100);
  }
}

function bwg_animate_image_box_for_hide_sidebar() {
  if ( lightbox_comment_pos == 'left' ) {
    jQuery( ".bwg_image_wrap" ).animate( {
      left: 0,
      width: jQuery( "#spider_popup_wrap" ).width()
    }, 100 );
  } else if ( lightbox_comment_pos == 'right' ) {
    jQuery( ".bwg_image_wrap" ).animate( {
      right: 0,
      width: jQuery( "#spider_popup_wrap" ).width()
    }, 100 );
  }
  jQuery( ".bwg_image_container" ).animate( {
    width: jQuery( "#spider_popup_wrap" ).width() - ( gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0 )
  }, 100 );
  jQuery( ".bwg_popup_image" ).animate( {
    maxWidth: jQuery( "#spider_popup_wrap" ).width() - ( gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0 )
  }, {
    duration: 100,
    complete: function () {
      bwg_change_watermark_container();
    }
  } );

  jQuery( ".bwg_popup_embed" ).animate( {
    width: jQuery( "#spider_popup_wrap" ).width() - ( gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0 )
  }, {
    duration: 100,
    complete: function () {
      bwg_resize_instagram_post();
      bwg_change_watermark_container();
    }
  } );
  if ( gallery_box_data['width_or_height'] == 'width' ) {
    jQuery( ".bwg_filmstrip_container" ).animate( { width: jQuery( ".spider_popup_wrap" ).width() }, 100 );
    jQuery( ".bwg_filmstrip" ).animate( { width: jQuery( ".spider_popup_wrap" ).width() - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()) }, 100 );
  } else if ( gallery_box_data['width_or_height'] == 'height' ) {
    jQuery( ".bwg_filmstrip_container" ).animate( { height: jQuery( ".spider_popup_wrap" ).width() }, 100 );
    jQuery( ".bwg_filmstrip" ).animate( { height: jQuery( ".spider_popup_wrap" ).width() - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()) }, 100 );
  }
  /* Set filmstrip initial position.*/
  bwg_set_filmstrip_pos( jQuery( ".spider_popup_wrap" ).width() - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data );
  jQuery( ".spider_popup_close_fullscreen" ).show( 100 );
}

function bwg_animate_image_box_for_show_sidebar() {
  var bwg_comment_container = jQuery( ".bwg_comment_container" ).width() || jQuery( ".bwg_ecommerce_container" ).width();
  if ( lightbox_comment_pos == 'left' ) {
    jQuery( ".bwg_image_wrap" ).animate( {
      left: bwg_comment_container,
      width: jQuery( "#spider_popup_wrap" ).width() - bwg_comment_container
    }, 100 );
  } else if ( lightbox_comment_pos == 'right' ) {
    jQuery( ".bwg_image_wrap" ).animate( {
      right: bwg_comment_container,
      width: jQuery( "#spider_popup_wrap" ).width() - bwg_comment_container
    }, 100 );
  }
  jQuery( ".bwg_image_container" ).animate( {
    width: jQuery( "#spider_popup_wrap" ).width() - ( gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0 ) - bwg_comment_container
  }, 100 );
  jQuery( ".bwg_popup_image" ).animate( {
    maxWidth: jQuery( "#spider_popup_wrap" ).width() - bwg_comment_container - ( gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0 )
  }, {
    duration: 100,
    complete: function () {
      bwg_change_watermark_container();
    }
  } );
  jQuery( ".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video" ).animate( {
    maxWidth: jQuery( "#spider_popup_wrap" ).width() - bwg_comment_container - ( gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0 )
  }, {
    duration: 100,
    complete: function () {
      bwg_resize_instagram_post();
      bwg_change_watermark_container();
    }
  } );
  if ( gallery_box_data['width_or_height'] == 'width' ) {
    jQuery( ".bwg_filmstrip_container" ).css( { width: jQuery( "#spider_popup_wrap" ).width() - ( gallery_box_data['filmstrip_direction'] == 'vertical' ? 0 : bwg_comment_container ) } );
    jQuery( ".bwg_filmstrip" ).animate( { width: jQuery( ".bwg_filmstrip_container" ).width() - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()) }, 100 );
    /* Set filmstrip initial position.*/
    bwg_set_filmstrip_pos( jQuery( ".bwg_filmstrip_container" ).width() - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data );
  }
}

function bwg_reset_zoom() {
  var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
  var viewportmeta = document.querySelector('meta[name="viewport"]');
  if (isMobile) {
    if (viewportmeta) {
      viewportmeta.content = 'width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=0';
    }
  }
}

/* Open with fullscreen.*/
function bwg_open_with_fullscreen() {
  jQuery(".bwg_watermark").css({display: 'none'});
  var comment_container_width = 0;
  if (jQuery(".bwg_comment_container").hasClass("bwg_open") || jQuery(".bwg_ecommerce_container").hasClass("bwg_open")) {
    comment_container_width = jQuery(".bwg_comment_container").width() || jQuery(".bwg_ecommerce_container").width();
  }
  bwg_popup_current_width = jQuery(window).width();
  bwg_popup_current_height = window.innerHeight;
  jQuery("#spider_popup_wrap").css({
    width: jQuery(window).width(),
    height: window.innerHeight,
    left: 0,
    top: 0,
    margin: 0,
    zIndex: 100002
  });
  jQuery(".bwg_image_wrap").css({width: (jQuery(window).width() - comment_container_width)});
  jQuery(".bwg_image_container").css({height: (bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)), width: bwg_popup_current_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0)});
  jQuery(".bwg_popup_image").css({
    maxWidth: jQuery(window).width() - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0),
    maxHeight: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
  },  {
    complete: function () { bwg_change_watermark_container(); }
  });
  jQuery(".bwg_popup_video").css({
    width: jQuery(window).width() - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0),
    height: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
  },  {
    complete: function () { bwg_change_watermark_container(); }
  });
  jQuery(".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video").css({
    maxWidth: jQuery(window).width() - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0),
    maxHeight: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
  },  {
    complete: function () {
      bwg_resize_instagram_post();
      bwg_change_watermark_container(); }
  });
  if ( gallery_box_data['width_or_height'] == 'width' ) {
    jQuery(".bwg_filmstrip_container").css({width: jQuery(window).width() - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0)});
    jQuery(".bwg_filmstrip").css({width: jQuery(window).width() - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height())});
    /* Set filmstrip initial position.*/
    bwg_set_filmstrip_pos(jQuery(window).width() - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data);
  } else {
    jQuery(".bwg_filmstrip_container").css({height: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0)});
    jQuery(".bwg_filmstrip").css({height: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height())});
    /* Set filmstrip initial position.*/
    bwg_set_filmstrip_pos(window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? 'comment_container_width' : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data);
  }
  jQuery(".bwg_resize-full").attr("class", "bwg-icon-compress bwg_ctrl_btn bwg_resize-full");
  jQuery(".bwg_resize-full").attr("title", bwg_objectsL10n.bwg_restore);
  jQuery(".spider_popup_close").attr("class", "bwg_ctrl_btn spider_popup_close_fullscreen");
}

function bwg_resize_full() {
  jQuery(".bwg_watermark").css({display: 'none'});
  var comment_container_width = 0;
  if (jQuery(".bwg_comment_container").hasClass("bwg_open") || jQuery(".bwg_ecommerce_container").hasClass("bwg_open") ) {
    comment_container_width = jQuery(".bwg_comment_container").width() || jQuery(".bwg_ecommerce_container").width();
  }
  /* Resize to small from full.*/
  if ( jQuery(".bwg_resize-full").hasClass("bwg-icon-compress") ) {
    if ( jQuery(window).width() > gallery_box_data['image_width'] ) {
      bwg_popup_current_width = gallery_box_data['image_width'];
    }
    if ( window.innerHeight > gallery_box_data['image_height'] ) {
      bwg_popup_current_height = gallery_box_data['image_height'];
    }
    /* Minimize.*/
    jQuery("#spider_popup_wrap").animate({
      width: bwg_popup_current_width,
      height: bwg_popup_current_height,
      left: '50%',
      top: '50%',
      marginLeft: -bwg_popup_current_width / 2,
      marginTop: -bwg_popup_current_height / 2,
      zIndex: 100002
    }, 500);
    jQuery(".bwg_image_wrap").animate({width: bwg_popup_current_width - comment_container_width}, 500);
    jQuery(".bwg_image_container").animate({height: bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0), width: bwg_popup_current_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0)}, 500);
    jQuery(".bwg_popup_image").animate({
      maxWidth: bwg_popup_current_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0),
      maxHeight: bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
    }, {
      duration: 500,
      complete: function () {
        bwg_change_watermark_container();
        if ((jQuery("#spider_popup_wrap").width() < jQuery(window).width())) {
          if (jQuery("#spider_popup_wrap").height() < window.innerHeight) {
            jQuery(".spider_popup_close_fullscreen").attr("class", "spider_popup_close");
          }
        }
      }
    });
    jQuery(".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video").animate({
      maxWidth: bwg_popup_current_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0),
      maxHeight: bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
    }, {
      duration: 500,
      complete: function () {
        bwg_resize_instagram_post();
        bwg_change_watermark_container();
        if (jQuery("#spider_popup_wrap").width() < jQuery(window).width()) {
          if (jQuery("#spider_popup_wrap").height() < window.innerHeight) {
            jQuery(".spider_popup_close_fullscreen").attr("class", "spider_popup_close");
          }
        }
      }
    });
    if ( gallery_box_data['width_or_height'] == 'width' ) {
      jQuery(".bwg_filmstrip_container").animate({width: bwg_popup_current_width - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0)}, 500);
      jQuery(".bwg_filmstrip").animate({width: bwg_popup_current_width - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height())}, 500);
      /* Set filmstrip initial position.*/
      bwg_set_filmstrip_pos(bwg_popup_current_width - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data);
    } else {
      jQuery(".bwg_filmstrip_container").animate({height: bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0)}, 500);
      jQuery(".bwg_filmstrip").animate({height: bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height())}, 500);
      /* Set filmstrip initial position.*/
      bwg_set_filmstrip_pos(bwg_popup_current_height - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data);
      gallery_box_data['filmstrip_direction'] == 'horizontal' ? '' : jQuery(".bwg_filmstrip_right").css({ top: (bwg_popup_current_height - jQuery('.bwg_filmstrip_right').height() ) });
    }
    jQuery(".bwg_resize-full").attr("class", "bwg-icon-expand bwg_ctrl_btn bwg_resize-full");
    jQuery(".bwg_resize-full").attr("title", bwg_objectsL10n.bwg_maximize);
    setTimeout(function () {
      bwg_info_height_set();
    }, 500);
  }
  else { /* Resize to full from small.*/
    bwg_popup_current_width = jQuery(window).width();
    bwg_popup_current_height = window.innerHeight;
    /* Maximize.*/
    jQuery("#spider_popup_wrap").animate({
      width: jQuery(window).width(),
      height: window.innerHeight,
      left: 0,
      top: 0,
      margin: 0,
      zIndex: 100002
    }, 500);
    jQuery(".bwg_image_wrap").animate({width: (jQuery(window).width() - comment_container_width)}, 500);
    jQuery(".bwg_image_container").animate({height: (bwg_popup_current_height - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)), width: bwg_popup_current_width - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0)}, 500);
    jQuery(".bwg_popup_image").animate({
      maxWidth: jQuery(window).width() - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0),
      maxHeight: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
    }, {
      duration: 500,
      complete: function () { bwg_change_watermark_container(); }
    });
    jQuery(".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video").animate({
      maxWidth: jQuery(window).width() - comment_container_width - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0),
      maxHeight: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
    }, {
      duration: 500,
      complete: function () {
        bwg_resize_instagram_post();
        bwg_change_watermark_container(); }
    });
    if ( gallery_box_data['width_or_height'] == 'width' ) {
      jQuery(".bwg_filmstrip_container").animate({width: jQuery(window).width() - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0)}, 500);
      jQuery(".bwg_filmstrip").animate({width: jQuery(window).width() - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height())}, 500);
      /* Set filmstrip initial position.*/
      bwg_set_filmstrip_pos(jQuery(window).width() - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data);
    } else {
      jQuery(".bwg_filmstrip_container").animate({height: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0)}, 500);
      jQuery(".bwg_filmstrip").animate({height: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height())}, 500);
      /* Set filmstrip initial position.*/
      bwg_set_filmstrip_pos(window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? comment_container_width : 0) - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()), '' ,gallery_box_data);
      gallery_box_data['filmstrip_direction'] == 'horizontal' ? '' : jQuery(".bwg_filmstrip_right").css({ top: (window.innerHeight - jQuery('.bwg_filmstrip_right').height() ) });
    }
    jQuery(".bwg_resize-full").attr("class", "bwg-icon-compress bwg_ctrl_btn bwg_resize-full");
    jQuery(".bwg_resize-full").attr("title", bwg_objectsL10n.bwg_restore);
    jQuery(".spider_popup_close").attr("class", "bwg_ctrl_btn spider_popup_close_fullscreen");
  }
  setTimeout(function () {
    bwg_info_height_set();
  }, 500);
}

function bwg_popup_resize_lightbox() {
  if (typeof jQuery().fullscreen !== 'undefined') {
    if (jQuery.isFunction(jQuery().fullscreen)) {
      if (!jQuery.fullscreen.isFullScreen()) {
        jQuery(".bwg_resize-full").show();
        if(!jQuery('.bwg_resize-full').hasClass('bwg-icon-compress')) {
          jQuery(".bwg_resize-full").attr("class", "bwg-icon-expand bwg_ctrl_btn bwg_resize-full");
        }
        jQuery(".bwg_resize-full").attr("title", bwg_objectsL10n.bwg_maximize);
        jQuery(".bwg_fullscreen").attr("class", "bwg-icon-arrows-out bwg_ctrl_btn bwg_fullscreen");
        jQuery(".bwg_fullscreen").attr("title", bwg_objectsL10n.fullscreen);
      }
    }
  }
  var comment_container_width = 0;
  if (jQuery(".bwg_comment_container").hasClass("bwg_open") || jQuery(".bwg_ecommerce_container").hasClass("bwg_open")) {
    comment_container_width = gallery_box_data['lightbox_comment_width'];
  }
  if (comment_container_width > jQuery(window).width()) {
    comment_container_width = jQuery(window).width();
    jQuery(".bwg_comment_container").css({
      width: comment_container_width
    });
    jQuery(".bwg_ecommerce_container").css({
      width: comment_container_width
    });
    jQuery(".spider_popup_close_fullscreen").hide();
  }
  else {
    jQuery(".spider_popup_close_fullscreen").show();
  }
  if (!(!(window.innerHeight > gallery_box_data['image_height']) || !(gallery_box_data['open_with_fullscreen'] != 1)) && !jQuery('.bwg_resize-full').hasClass('bwg-icon-compress')) {
    jQuery("#spider_popup_wrap").css({
      height: gallery_box_data['image_height'],
      top: '50%',
      marginTop: -gallery_box_data['image_height'] / 2,
      zIndex: 100002
    });
    jQuery(".bwg_image_container").css({height: (gallery_box_data['image_height'] - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0))});
    jQuery(".bwg_image_info").css("height","auto");
    bwg_info_height_set();
    jQuery(".bwg_popup_image").css({
      maxHeight: gallery_box_data['image_height'] - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
    });
    jQuery(".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video").css({
      maxHeight: gallery_box_data['image_height'] - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
    });
    if (gallery_box_data['filmstrip_direction'] == 'vertical') {
      jQuery(".bwg_filmstrip_container").css({height: gallery_box_data['image_height']});
      jQuery(".bwg_filmstrip").css({height: (gallery_box_data['image_height'] - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()))})
    }
    bwg_popup_current_height = gallery_box_data['image_height'];
  }
  else {
    jQuery("#spider_popup_wrap").css({
      height: window.innerHeight,
      top: 0,
      marginTop: 0,
      zIndex: 100002
    });
    jQuery(".bwg_image_container").css({height: (window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0))});
    jQuery(".bwg_image_info").css("height","auto");
    bwg_info_height_set();
    jQuery(".bwg_popup_image").css({
      maxHeight: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
    });
    jQuery(".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video").css({
      maxHeight: window.innerHeight - (gallery_box_data['filmstrip_direction'] == 'horizontal' ? gallery_box_data['image_filmstrip_height'] : 0)
    });
    if (gallery_box_data['filmstrip_direction'] == 'vertical') {
      jQuery(".bwg_filmstrip_container").css({height: (window.innerHeight)});
      jQuery(".bwg_filmstrip").css({height: (window.innerHeight - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()))});
    }
    bwg_popup_current_height = window.innerHeight;
  }
  if (!(!(jQuery(window).width() >= gallery_box_data['image_width']) || !(gallery_box_data['open_with_fullscreen'] != 1))) {
    jQuery("#spider_popup_wrap").css({
      width: gallery_box_data['image_width'],
      left: '50%',
      marginLeft: -gallery_box_data['image_width'] / 2,
      zIndex: 100002
    });
    jQuery(".bwg_image_wrap").css({width: gallery_box_data['image_width'] - comment_container_width});
    jQuery(".bwg_image_container").css({width: (gallery_box_data['image_width'] - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0) - comment_container_width)});
    jQuery(".bwg_image_info").css("height","auto");
    bwg_info_height_set();
    jQuery(".bwg_popup_image").css({
      maxWidth: gallery_box_data['image_width'] - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0) - comment_container_width
    });
    jQuery(".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video").css({
      maxWidth: gallery_box_data['image_width'] - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0) - comment_container_width
    });
    if (gallery_box_data['filmstrip_direction'] == 'horizontal') {
      jQuery(".bwg_filmstrip_container").css({width: gallery_box_data['image_width'] - comment_container_width});
      jQuery(".bwg_filmstrip").css({width: (gallery_box_data['image_width']  - comment_container_width- 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()))});
    }
    bwg_popup_current_width = gallery_box_data['image_width'];
  }
  else {
    jQuery("#spider_popup_wrap").css({
      width: jQuery(window).width(),
      left: 0,
      marginLeft: 0,
      zIndex: 100002
    });
    jQuery(".bwg_image_wrap").css({width: (jQuery(window).width() - comment_container_width)});
    jQuery(".bwg_image_container").css({width: (jQuery(window).width() - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0) - comment_container_width)});
    jQuery(".bwg_popup_image").css({
      maxWidth: jQuery(window).width() - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0) - comment_container_width
    });
    jQuery(".bwg_popup_embed > .bwg_embed_frame > img, .bwg_popup_embed > .bwg_embed_frame > video").css({
      maxWidth: jQuery(window).width() - (gallery_box_data['filmstrip_direction'] == 'vertical' ? gallery_box_data['image_filmstrip_width'] : 0) - comment_container_width
    });
    if (gallery_box_data['filmstrip_direction'] == 'horizontal') {
      jQuery(".bwg_filmstrip_container").css({width: (jQuery(window).width() - comment_container_width)});
      jQuery(".bwg_filmstrip").css({width: (jQuery(window).width() - comment_container_width - 2* (gallery_box_data['filmstrip_direction'] == 'horizontal' ? jQuery('.bwg_filmstrip_right').width():jQuery('.bwg_filmstrip_right').height()))});
    }
    bwg_popup_current_width = jQuery(window).width();
  }
  /* Set watermark container size.*/
  bwg_resize_instagram_post();
  bwg_change_watermark_container();
  if (!(!(window.innerHeight > gallery_box_data['image_height'] - 2 * gallery_box_data['lightbox_close_btn_top']) || !(jQuery(window).width() >= gallery_box_data['image_width'] - 2 * gallery_box_data['lightbox_close_btn_right']) || !(gallery_box_data['open_with_fullscreen'] != 1))) {
    jQuery(".spider_popup_close_fullscreen").attr("class", "spider_popup_close");
  }
  else {
    if (!(!(jQuery("#spider_popup_wrap").width() < jQuery(window).width()) || !(jQuery("#spider_popup_wrap").height() < jQuery(window).height()))) {
      jQuery(".spider_popup_close").attr("class", "bwg_ctrl_btn spider_popup_close_fullscreen");
    }
  }
  var bwg_ctrl_btn_container_height = jQuery(".bwg_ctrl_btn_container").height();
  if ( gallery_box_data['lightbox_ctrl_btn_pos'] == 'bottom' ) {
    if (jQuery(".bwg_toggle_container i").hasClass('bwg-icon-caret-down')) {
      jQuery(".bwg_toggle_container").css("bottom", bwg_ctrl_btn_container_height + "px");
    }
  }
  if ( gallery_box_data['lightbox_ctrl_btn_pos'] == 'top') {
    if (jQuery(".bwg_toggle_container i").hasClass('bwg-icon-caret-up')) {
      jQuery(".bwg_toggle_container").css("top", bwg_ctrl_btn_container_height + "px");
    }
  }
}

function bwg_rating( current_rate, rate_count, avg_rating, cur_key ) {
  lightbox_rate_stars_count = gallery_box_data['lightbox_rate_stars_count'];
  lightbox_rate_size = gallery_box_data['lightbox_rate_size'];
  lightbox_rate_icon = gallery_box_data['lightbox_rate_icon'];
  var avg_rating_message = "Not rated yet.";
  if (avg_rating != 0) {
    if (avg_rating != "") {
      avg_rating_message = parseFloat(avg_rating).toFixed(1) + "\n Votes: " + rate_count;
    }
  }
  if (typeof jQuery().raty !== 'undefined') {
    if (jQuery.isFunction(jQuery().raty)) {
      jQuery("#bwg_star").raty({
        score: function() {
          return jQuery(this).attr("data-score");
        },
        starType: 'i',
        number : lightbox_rate_stars_count,
      size : lightbox_rate_size,
      readOnly : function() {
        return (current_rate ? true : false);
      },
      noRatedMsg : "Not rated yet.",
        click : function(score, evt) {
        jQuery("#bwg_star").hide();
        jQuery("#bwg_rated").show();
        spider_set_input_value('rate_ajax_task', 'save_rate');
        jQuery.when(spider_rate_ajax_save('bwg_rate_form')).then( function () {
          gallery_box_data['data'][cur_key]["rate"] = score;
          ++gallery_box_data['data'][cur_key]["rate_count"];
          var curr_score = parseFloat(jQuery("#bwg_star").attr("data-score"));
          gallery_box_data['data'][cur_key]["avg_rating"] = curr_score ? ((curr_score + score) / 2).toFixed(1) : score.toFixed(1);
          bwg_rating(gallery_box_data['data'][cur_key]["rate"], gallery_box_data['data'][cur_key]["rate_count"], gallery_box_data['data'][cur_key]["avg_rating"], gallery_box_data['current_image_key']);
        });
      },
        starHalf : 'bwg-icon-' + lightbox_rate_icon + ((lightbox_rate_icon == 'star') ? '-half' : '') + '-o',
        starOff : 'bwg-icon-' + lightbox_rate_icon + '-o',
        starOn : 'bwg-icon-' + lightbox_rate_icon,
        cancelOff : 'bwg-icon-minus-square-o',
        cancelOn : 'bwg-icon-minus-square-o',
        cancel : false,
        /*target : '#bwg_hint',
        targetType : 'number',
        targetKeep : true,*/
        cancelHint : 'Cancel your rating.',
        hints : [avg_rating_message, avg_rating_message, avg_rating_message, avg_rating_message, avg_rating_message],
        alreadyRatedMsg : parseFloat(avg_rating).toFixed(1) + "\n" + "You have already rated.\nVotes: " + rate_count,
    });
    }
  }
}

function changeDownloadsTotal(obj) {
  var totalPrice = 0;
  var showdigitalItemsCount = jQuery("[name=option_show_digital_items_count]").val();
  if( showdigitalItemsCount == 0 ){
    jQuery("[name=selected_download_item]:checked").each(function(){
      totalPrice += Number(jQuery(this).closest("tr").attr("data-price"));
    });
  }
  else{
    jQuery(".digital_image_count").each(function(){
      if(Number(jQuery(this).val()) != 0){
        totalPrice += Number(jQuery(this).closest("tr").attr("data-price")) * Number(jQuery(this).val());
      }
    });
  }
  totalPrice = totalPrice.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
  jQuery(".product_downloads_price").html(totalPrice);
}

function changeMenualTotal(obj) {
  if(Number(jQuery(obj).val()) <= 0){
    jQuery(obj).val("1");
  }
  var count =  Number(jQuery(obj).val());
  var totalPrice = Number(jQuery(".product_manual_price").attr("data-actual-price"));
  totalPrice = count*totalPrice;

  totalPrice = totalPrice.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
  jQuery(".product_manual_price").html(totalPrice);
}

function onSelectableParametersChange(obj) {
  var parametersPrise = 0;
  var productPrice = gallery_box_data["data"][jQuery('#bwg_current_image_key').val()]["pricelist_manual_price"] ? gallery_box_data["data"][jQuery('#bwg_current_image_key').val()]["pricelist_manual_price"] : '0';
  productPrice = parseFloat(productPrice.replace(",",""));

  var type = jQuery(obj).closest('.image_selected_parameter').attr("data-parameter-type");
  var priceInfo = jQuery(obj).val();
  priceInfo = priceInfo.split("*");
  var priceValue = parseFloat(priceInfo[1]);
  var sign = priceInfo[0];
  var alreadySelectedValues = Number(jQuery(obj).closest('.image_selected_parameter').find(".already_selected_values").val());
  if ( type == "4" || type == "5" ) {
    var newPriceVlaueSelectRadio = parseFloat( sign + priceValue );
    jQuery(obj).closest('.image_selected_parameter').find(".already_selected_values").val(newPriceVlaueSelectRadio);
  }
  else if ( type == "6" ) {
    if ( jQuery(obj).is(":checked") == false ) {
      var  newPriceVlaueCheckbox = alreadySelectedValues - parseFloat( sign + priceValue );
    }
    else {
      var newPriceVlaueCheckbox = alreadySelectedValues + parseFloat( sign + priceValue );
    }
    jQuery(obj).closest('.image_selected_parameter').find(".already_selected_values").val(newPriceVlaueCheckbox);
  }

  jQuery(".already_selected_values").each( function() {
    parametersPrise += Number(jQuery(this).val());
  });
  productPrice = productPrice + parametersPrise;
  jQuery(".product_manual_price").attr("data-actual-price",productPrice);
  var count = Number(jQuery(".image_count").val()) <= 0 ? 1 : Number(jQuery(".image_count").val());
  productPrice = count * productPrice;
  productPrice = productPrice.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
  jQuery(".product_manual_price").html(productPrice);
}

function onBtnClickAddToCart() {
  var type = jQuery("[name=type]").val();
  if ( type != "" ) {
    var data = {};
    if ( type == "manual" ) {
      var count = jQuery(".image_count").val();
      var parameters = {};
      jQuery(".manual").find(".image_selected_parameter").each(function () {
        var parameterId = jQuery(this).attr("data-parameter-id");
        var parameterTypeId = jQuery(this).attr("data-parameter-type");
        var parameterValue = "";
        switch (parameterTypeId) {
          case '2':
            parameterValue = jQuery(this).find("input").val();
            break;
          case '3':
            parameterValue = jQuery(this).find("textarea").val();
            break;
          case '4':
            parameterValue = jQuery(this).find('select :selected').val();
            break;
          case '5':
            parameterValue = jQuery(this).find('[type=radio]:checked').val();
            break;
          case '6':
            var checkbox_parameter_values = [];
            jQuery(this).find("[type=checkbox]:checked").each(function () {
              checkbox_parameter_values.push(jQuery(this).val());
            });
            parameterValue = checkbox_parameter_values;
            break;
        }

        parameters[parameterId] = parameterValue;
      });
      data.count = count;
      data.parameters = parameters;
      data.price = jQuery(".product_manual_price").attr("data-price").replace(",","");
    }
    else {
      var downloadItems = [];
      var showdigitalItemsCount = jQuery("[name=option_show_digital_items_count]").val();
      if( showdigitalItemsCount == 0 ){
        if(jQuery("[name=selected_download_item]:checked").length == 0){
          jQuery(".add_to_cart_msg").html("You must select at least one item.");
          return;
        }
        jQuery("[name=selected_download_item]:checked").each(function () {
          var downloadItem = {};
          downloadItem.id = jQuery(this).val();
          downloadItem.count = 1;
          downloadItem.price = jQuery(this).closest("tr").attr("data-price");
          downloadItems.push(downloadItem);
        });
      }
      else{
        jQuery(".digital_image_count").each(function () {
          var downloadItem = {};
          if(jQuery(this).val() > 0){
            downloadItem.id = jQuery(this).closest("tr").attr("data-id");
            downloadItem.price = jQuery(this).closest("tr").attr("data-price");
            downloadItem.count = jQuery(this).val();
            downloadItems.push(downloadItem);
          }
        });
      }
      data.downloadItems = downloadItems;
      if(downloadItems.length == 0)	{
        jQuery(".add_to_cart_msg").html("Please select at least one item");
        return ;
      }
    }

    var ajaxurl = jQuery("#ajax_url").val();
    var post_data = {
      'action': 'add_cart',
      'task': 'add_cart',
      'controller': 'checkout',
      "image_id": jQuery('#bwg_popup_image').attr('image_id'),
      "type": type,
      "data": JSON.stringify(data)
    };

    jQuery.ajax({
      type: "POST",
      url: ajaxurl,
      data: post_data,
      success: function (response) {
        responseData = JSON.parse(response);
        jQuery(".add_to_cart_msg").html(responseData["msg"]);
        jQuery(".products_in_cart").html(responseData["products_in_cart"]);
        if(responseData["redirect"] == 1){
          window.location.href = "<?php echo get_permalink($options->checkout_page);?>";
        }
      },
      beforeSend: function(){
      },
      complete:function(){
      }
    });
  }
  else {
    jQuery(".add_to_cart_msg").html("Please select Prints and products or Downloads");
  }
}

function onBtnViewCart(){
  var checkoutPage = jQuery("[name=option_checkout_page]").val();
  jQuery("#bwg_ecommerce_form").attr("action",checkoutPage);
  jQuery("#bwg_ecommerce_form").submit();
}

/* Load visible images in filmstrip.*/
function bwg_load_visible_images( key, preloadCount, total_thumbnail_count ) {
  if((key - preloadCount) >= 0) {
    startPoint = key - preloadCount;
  }
  if((key + preloadCount) > total_thumbnail_count) {
    endPoint = total_thumbnail_count;
  }
  for( var i = startPoint; i <= endPoint; i++) {
    var filmstrip_image = jQuery("#bwg_filmstrip_thumbnail_" + i + " img");
    filmstrip_image.removeClass('bwg-hidden');
    filmstrip_image.attr('src', filmstrip_image.data('url'));
  }
}

/* Load filmstrip not visible images. */
function bwg_load_filmstrip() {
  for(var i = 1; i <= total_thumbnail_count; i++) {
    leftIndex = startPoint - i;
    rightIndex = endPoint + i;

    if ( rightIndex < total_thumbnail_count ) {  /* check if right index is greater than max index */
      var filmstrip_image = jQuery("#bwg_filmstrip_thumbnail_" + rightIndex + " img");
      filmstrip_image.removeClass('bwg-hidden');
      filmstrip_image.attr('src', filmstrip_image.data('url'));
    }
    /* Left from key indexes */
    if ( leftIndex >= 0 ) {
      var filmstrip_image = jQuery("#bwg_filmstrip_thumbnail_" + leftIndex + " img");
      filmstrip_image.removeClass('bwg-hidden');
      filmstrip_image.attr('src', filmstrip_image.data('url'));
    }
  }
  jQuery(".bwg_filmstrip_thumbnail").each(function () {
    var image = jQuery(this).find("img");
    /* For embed types */
    if (typeof image.attr("style") != 'undefined') {
      return;
    }
    var bwg_filmstrip_thumbnail;
    if (image.width() == 0) {
      image.on("load", function () {
        bwg_filmstrip_thumbnail = jQuery(this).find(".bwg_filmstrip_thumbnail_img_wrap");
        bwg_filmstrip_thumb_view( image );
      });
    }
    else {
      bwg_filmstrip_thumbnail = jQuery(this).find(".bwg_filmstrip_thumbnail_img_wrap");
      bwg_filmstrip_thumb_view( image );
    }
  });
}

/* thumb size and position correction for filmstrip*/
function bwg_filmstrip_thumb_view( image ) {
  var image_filmstrip_height_space = gallery_box_data['image_filmstrip_height'];
  var image_filmstrip_width_space = gallery_box_data['image_filmstrip_width'];

  var image_filmstrip_width  = image_filmstrip_width_space - gallery_box_data['filmstrip_thumb_right_left_space'];
  var image_filmstrip_height = image_filmstrip_height_space;

  var scale = Math.max(image_filmstrip_width_space / image.width(), image_filmstrip_height_space / image.height());
  var image_width = image.width() * scale;
  var image_height = image.height() * scale;

  image.css({
    width:image_width,
    height: image_height,
    marginLeft: (image_filmstrip_width - image_width) / 2,
    marginTop: (image_filmstrip_height - image_height) / 2
  });
}

/* Set image info height */
function bwg_info_height_set(){
    bwg_info_position( false );
    if ( jQuery(".mCustomScrollBox").length && jQuery(".bwg_image_info_container1").height() < jQuery(".mCustomScrollBox").height() + jQuery(".bwg_toggle_container").height() + bwg_image_info_pos + 2*( parseInt(gallery_box_data['lightbox_info_margin']))){
        jQuery(".bwg_image_info").css({height:jQuery(".bwg_image_info_container1").height()- jQuery(".bwg_toggle_container").height()- bwg_image_info_pos - 2*(parseInt(gallery_box_data['lightbox_info_margin']))});
    }
}

/* Set image info position */
function bwg_info_position( toggle ) {
    var number = 0;
    var type = 'none';

    if (gallery_box_data['lightbox_ctrl_btn_pos'] == 'top') {
        if (gallery_box_data['lightbox_info_pos'] == 'top') {
            type = 'top';
      }
    }
    else {
        if (gallery_box_data['lightbox_info_pos'] == 'bottom') {
            type = 'bottom';
        }
    }

    if ( !jQuery('.bwg_ctrl_btn_container').hasClass('closed') ) {
        if (gallery_box_data['lightbox_ctrl_btn_pos'] == 'top') {
            if (gallery_box_data['lightbox_info_pos'] == 'top') {
                number =  jQuery(".bwg_ctrl_btn_container").height();
            }
        }
        else {
            if (gallery_box_data['lightbox_info_pos'] == 'bottom') {
                number = jQuery(".bwg_ctrl_btn_container").height();
            }
        }
    }

    if (type == 'top') {
        if (toggle == false) {
            jQuery(".bwg_image_info").css('top', number);
        }
        else {
            jQuery(".bwg_image_info").animate({top: number + "px"}, 500);
        }
    }
    else if (type == 'bottom') {
        if (toggle == false) {
            jQuery(".bwg_image_info").css('bottom', number);
        }
        else {
            jQuery(".bwg_image_info").animate({bottom: number + "px"}, 500);
        }
  }
}
