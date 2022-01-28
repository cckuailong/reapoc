var bwg = 0;
var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
var bwg_click = isMobile ? 'touchend' : 'click';

/* Slideshow params */
var bwg_params = [];
/* Image browser params */
var bwg_params_ib = [];
/* Carousel params */
var bwg_params_carousel = [];
jQuery(function () {
  /**
   * @param {boolean} isAll   do you need to stop all slideshows?.
   */
  function bwg_hiddenFunction(isAll) {
    bwg_slideshow_blur(isAll);
    if (jQuery('.bwg_play_pause').length > 0) {
      window.clearInterval(bwg_playInterval);
      jQuery(".bwg_play_pause").attr("title", bwg_objectsL10n.bwg_play);
      jQuery(".bwg_play_pause").attr("class", "bwg-icon-play bwg_ctrl_btn bwg_play_pause");
    }
  }

  function bwg_visibleFunction() {
    bwg_slideshow_focus();
    if (jQuery(".bwg_play_pause").length && jQuery(".bwg_play_pause").hasClass("bwg-icon-play") && !jQuery(".bwg_comment_container").hasClass("bwg_open")) {
      bwg_play(gallery_box_data['data']);
      jQuery(".bwg_play_pause").attr("title", bwg_objectsL10n.bwg_pause);
      jQuery(".bwg_play_pause").attr("class", "bwg-icon-pause bwg_ctrl_btn bwg_play_pause");
    }
  }

  var bwg_error = false;
  jQuery(".bwg_container").each(function () {
    if (jQuery(this).find(".wd_error").length > 0) {
      bwg_error = true;
    }
    if (!bwg_error) {
      var $element = jQuery(this);
      setTimeout(function () {
        if ($element[0].offsetHeight) {
          var bwg_visibilityCounter = 1;
        }
        else {
          var bwg_visibilityCounter = 0;
        }
        setInterval(function () {
          if ($element[0].offsetHeight) {
            if (bwg_visibilityCounter == 1) {
              bwg_visibilityCounter = 0;
              bwg_visibleFunction();
            }
          }
          else {
            if (bwg_visibilityCounter == 0) {
              bwg_visibilityCounter = 1;
              bwg_hiddenFunction(false);
            }
          }
        }, 200);
      }, 200)
    }
  });
  if (!bwg_error) {
    jQuery(window)
      .focus(function () {
        bwg_visibleFunction();
      })
      .blur(function () {
        bwg_hiddenFunction(true);
      })
  }
});

function bwg_main_ready() {
  if ( bwg_objectsL10n.lazy_load == 1 ) {
    jQuery(function() {
      jQuery('img.bwg_lazyload').lazy({
        onFinishedAll: function() {
          jQuery(".lazy_loader").removeClass("lazy_loader");
        }
      });
    });
  }

  /* If there is error (empty gallery).*/
  jQuery(".bwg_container").each(function () {
    if ( jQuery(this).find(".wd_error").length > 0 ) {
      var bwg = jQuery(this).data("bwg");
      bwg_container_loaded(bwg);
    }
  });
  bwg_document_ready();
  jQuery(".bwg-thumbnails, .bwg-masonry-thumbnails, .bwg-album-thumbnails").each(function () {
    bwg_all_thumnails_loaded(this);
  });

  jQuery(".bwg-mosaic-thumbnails").each(function () {
    bwg_thumbnail_mosaic(this);
  });
  bwg_slideshow_ready();
  bwg_carousel_ready();
  bwg_carousel_onload();
  bwg_image_browser_ready();
}

function bwg_resize_search_line() {
  jQuery('.search_line').each(function() {
    var element = jQuery(this);
    if (element.width() < 410) {
      element.addClass('bwg-search-line-responsive');
    }
    else {
      element.removeClass('bwg-search-line-responsive');
    }
  });
}

jQuery(window).on("resize", function () {
  /* Move to theend of query to get proper sizes. Otherwize Carousel resize does not work correctly.*/
  setTimeout(function () {
    var bwg_error = false;
    /* If there is error (empty gallery).*/
    jQuery(".bwg_container").each(function () {
      if (jQuery(this).find(".wd_error").length > 0) {
        bwg_error = true;
      }
    });
    if (!bwg_error) {
      jQuery(".bwg-thumbnails, .bwg-masonry-thumbnails, .bwg-album-thumbnails").each(function () {
        bwg_all_thumnails_loaded(this);
      });
      bwg_slideshow_resize();
      bwg_image_browser_resize();
      bwg_carousel_resize();
      bwg_blog_style_resize();
      jQuery(".bwg-mosaic-thumbnails").each(function () {
        bwg_thumbnail_mosaic(this);
      });
    }
    bwg_resize_search_line();
  }, 0);
});

jQuery(window).on("load", function () {
  var bwg_error = false;
  /* If there is error (empty gallery).*/
  jQuery(".bwg_container").each(function () {
    if ( jQuery(this).find(".wd_error").length > 0 ) {
      bwg_error = true;
    }
  });
  if ( !bwg_error ) {
    bwg_blog_style_onload();
    jQuery(".bwg-mosaic-thumbnails").each(function () {
      bwg_thumbnail_mosaic(this);
    });
  }
});

jQuery(".bwg-masonry-thumb-span img, .bwg-mosaic-thumb-span img").on("error", function() {
  jQuery(this).height(100);
  jQuery(this).width(100);
});

function bwg_slideshow_resize() {
  jQuery(".bwg_slideshow").each(function () {
    bwg = jQuery(this).attr('data-bwg');
    if ( jQuery("#bwg_slideshow_image_container_" + bwg).length ) {
      bwg_params[bwg] = JSON.parse(jQuery("#bwg_slideshow_image_container_" + bwg).attr("data-params"));
      bwg_params[bwg]['event_stack'] = [];
      bwg_popup_resize(bwg);
    }
  });
}

function bwg_blog_style_resize() {
  jQuery(".bwg_blog_style").each(function () {
    bwg = jQuery(this).attr('data-bwg');
    jQuery('.bwg_embed_frame_16x9_'+bwg).each(function (e) {
      jQuery(this).width(jQuery(this).parent().width());
      jQuery(this).height(jQuery(this).width() * 0.5625);
    });
    jQuery('.bwg_embed_frame_instapost_'+bwg).each(function (e) {
      jQuery(this).width(jQuery(this).parent().width());
      jQuery(this).height((jQuery(this).width() - 16) * jQuery(this).attr('data-height') / jQuery(this).attr('data-width') + 96);
    });
  })
}

function bwg_blog_style_onload() {
  jQuery(".bwg_blog_style").each(function () {
    bwg = jQuery(this).attr('data-bwg');
  	var data_right_click = jQuery("#bwg_blog_style_"+bwg);
    jQuery('.bwg_embed_frame_16x9_'+bwg).each(function (e) {
		/* Conflict with Sydney Theme */
		if ( jQuery('.bwg_blog_style_image_' + bwg).find('.fluid-width-video-wrapper').length ) {
			jQuery('.fluid-width-video-wrapper').removeAttr('style');
			var content = jQuery(this).parents('.bwg_blog_style_image_' + bwg).find('.fluid-width-video-wrapper').contents();
			jQuery(this).parents('.fluid-width-video-wrapper').replaceWith(content);
		}
		jQuery(this).width(jQuery(this).parents('.bwg_blog_style_image_' + bwg).width());
		jQuery(this).height(jQuery(this).width() * 0.5625);
    });

    jQuery('.bwg_embed_frame_instapost_'+bwg).each(function (e) {
      jQuery(this).width(jQuery(this).parents('.bwg_blog_style_image_' + bwg).width());
      /* 16 is 2*padding inside iframe */
      /* 96 is 2*padding(top) + 1*padding(bottom) + 40(footer) + 32(header) */
      jQuery(this).height((jQuery(this).width() - 16) * jQuery(this).attr('data-height') / jQuery(this).attr('data-width') + 96);
    });

    bwg_container_loaded(bwg);
  });
}

function bwg_blog_style_ready() {
  jQuery(".bwg_blog_style").each(function () {
    var bwg = jQuery(this).attr('data-bwg');
    bwg_container_loaded( bwg );

    var bwg_touch_flag = false;
    jQuery(this).find('.bwg_lightbox_' + bwg).on('click', function () {
	  var image_id = jQuery(this).attr('data-image-id');
	  jQuery('#bwg_blog_style_share_buttons_' + image_id ).removeAttr('data-open-comment');
      if ( !bwg_touch_flag ) {
        bwg_touch_flag = true;
        setTimeout( function(){ bwg_touch_flag = false; }, 100 );
		    bwg_gallery_box(image_id, jQuery(this).closest('.bwg_container'));
        return false;
      }
    });
    jQuery(".bwg_lightbox_" + bwg + " .bwg_ecommerce").on("click", function (event) {
      event.stopPropagation();
      if (!bwg_touch_flag) {
        bwg_touch_flag = true;
        setTimeout(function(){ bwg_touch_flag = false; }, 100);
        bwg_gallery_box(jQuery(this).attr( "data-image-id" ), jQuery(this).closest( '.bwg_container' ), true);
        return false;
      }
    });

    var bwg_hash = window.location.hash.substring(1);
    if (bwg_hash) {
      if (bwg_hash.indexOf("bwg") != "-1") {
        bwg_hash_array = bwg_hash.replace("bwg", "").split("/");
        if(bwg_hash_array[0] == "<?php echo $params_array['gallery_id']; ?>"){
          bwg_gallery_box(bwg_hash_array[1]);
        }
      }
    }
  });
}

function bwg_slideshow_focus() {
  jQuery(".bwg_slideshow").each(function () {
    bwg = jQuery(this).attr('data-bwg');
    if (jQuery('.bwg_slideshow[data-bwg=' + bwg + ']')[0].offsetHeight) {
      if ( jQuery("#bwg_slideshow_image_container_" + bwg).length ) {
        bwg_params[bwg] = JSON.parse(jQuery("#bwg_slideshow_image_container_" + bwg).attr("data-params"));
        bwg_params[bwg]['event_stack'] = [];
        window.clearInterval(window['bwg_playInterval' + bwg]);
        if (!jQuery(".bwg_ctrl_btn_" + bwg).hasClass("bwg-icon-play")) {
          bwg_play(bwg_params[bwg]['data'], bwg);
        }
      }
    }
  });
}

/**
 * @param {boolean} isAll   do you need to stop all slideshows?.
 */
function bwg_slideshow_blur(isAll) {
    jQuery(".bwg_slideshow").each(function () {
    bwg = jQuery(this).attr('data-bwg');
      if (isAll || !jQuery('.bwg_slideshow[data-bwg=' + bwg + ']')[0].offsetHeight) {
        if (jQuery("#bwg_slideshow_image_container_" + bwg).length) {
          bwg_params[bwg] = JSON.parse(jQuery("#bwg_slideshow_image_container_" + bwg).attr("data-params"));
          bwg_params[bwg]['event_stack'] = [];
          window.clearInterval(window['bwg_playInterval' + bwg]);
        }
      }
  });
}

function bwg_carousel_ready() {
  jQuery(".bwg-carousel").each(function () {
    var bwg = jQuery(this).data("bwg");
    bwg_params_carousel[bwg] = [];
    bwg_params_carousel[bwg]['bwg_currentCenterNum'] = 1;
    bwg_params_carousel[bwg]['bwg_currentlyMoving'] = false;
    bwg_params_carousel[bwg]['data'] = [];

    jQuery("#spider_carousel_left-ico_" + bwg).on("click", function (event) {
      bwg_params_carousel[bwg]['carousel'].prev();
      event.stopPropagation();
      event.stopImmediatePropagation();
    });
    jQuery("#spider_carousel_right-ico_" + bwg).on("click", function (event) {
      bwg_params_carousel[bwg]['carousel'].next();
      event.stopPropagation();
      event.stopImmediatePropagation();
    });
    if ( parseInt(bwg_params_carousel[bwg]['carousel_enable_autoplay']) ) {
      jQuery(".bwg_carousel_play_pause_" + bwg).attr("title", bwg_objectsL10n.pause);
      jQuery(".bwg_carousel_play_pause_" + bwg).attr("class", "bwg-icon-pause bwg_ctrl_btn_" + bwg + " bwg_carousel_play_pause_" + bwg + "");
    }

    jQuery(".bwg_carousel_play_pause_" + bwg).on(bwg_click, function (event) {
      if (jQuery(".bwg_ctrl_btn_" + bwg).hasClass("bwg-icon-play") ) {
        /*play*/
        jQuery(".bwg_carousel_play_pause_" + bwg).attr("title", bwg_objectsL10n.pause);
        jQuery(".bwg_carousel_play_pause_" + bwg).attr("class", "bwg-icon-pause bwg_ctrl_btn_" + bwg + " bwg_carousel_play_pause_" + bwg + "");
        bwg_params_carousel[bwg]['carousel'].start();
      }
      else {
        /* Pause.*/
        jQuery(".bwg_carousel_play_pause_" + bwg).attr("title", bwg_objectsL10n.play);
        jQuery(".bwg_carousel_play_pause_" + bwg).attr("class", "bwg-icon-play bwg_ctrl_btn_" + bwg + " bwg_carousel_play_pause_" + bwg + "");
        bwg_params_carousel[bwg]['carousel'].pause();
      }
      event.stopPropagation();
      event.stopImmediatePropagation();
    });
    if (typeof jQuery().swiperight !== 'undefined') {
      if (jQuery.isFunction(jQuery().swiperight)) {
        jQuery("#bwg_container1_" + bwg).swiperight(function () {
          bwg_params_carousel[bwg]['carousel'].prev();
        });
      }
    }
    if (typeof jQuery().swipeleft !== 'undefined') {
      if (jQuery.isFunction(jQuery().swipeleft)) {
        jQuery("#bwg_container1_" + bwg).swipeleft(function () {
          bwg_params_carousel[bwg]['carousel'].next();
        });
      }
    }
  });
}

function bwg_carousel_resize() {
  jQuery(".bwg-carousel").each(function () {
    var bwg = jQuery(this).data("bwg");
    bwg_carousel_params(bwg, true);
    bwg_params_carousel[bwg]['carousel'].pause();
    bwg_carousel_watermark(bwg);
    if ( !jQuery(".bwg_ctrl_btn_" + bwg).hasClass("bwg-icon-play") ) {
      bwg_params_carousel[bwg]['carousel'].start();
    }
  });
}

function bwg_carousel_onload() {
  jQuery(".bwg-carousel").each(function () {
    var bwg = jQuery(this).data("bwg");
    bwg_params_carousel[bwg] = jQuery(this).data("params");
    /* Store parent width to resize carousel only if width is changed. */
    bwg_params_carousel[bwg]['parent_width'] = 0;
    bwg_carousel_watermark(bwg);
    bwg_carousel_params(bwg, false);
    bwg_container_loaded(bwg);
  });
}

function bwg_carousel_params(bwg, resize) {
  var parentt = jQuery("#bwg_container1_" + bwg).parent();
  /* Trick to set parent's width to elementor tab. */
  if (parentt.hasClass('elementor-tab-content')) {
    parentt.width(parentt.closest('.elementor-widget-wrap').width());
  }
  var parent_width = parentt.width();
  var par = 1;
  if ( parent_width < bwg_params_carousel[bwg]['carousel_r_width'] ) {
    par = parent_width / bwg_params_carousel[bwg]['carousel_r_width'];
  }
  else {
    parent_width = bwg_params_carousel[bwg]['carousel_r_width'];
  }
  /* Resize carousel only if parent width is changed. */
  if ( bwg_params_carousel[bwg]['parent_width'] != parent_width ) {
    bwg_params_carousel[bwg]['parent_width'] = parent_width;
    if (bwg_params_carousel[bwg]['carousel_image_column_number'] > bwg_params_carousel[bwg]['count']) {
      bwg_params_carousel[bwg]['carousel_image_column_number'] = bwg_params_carousel[bwg]['count'];
    }

    jQuery(".bwg_carousel_play_pause_" + bwg).css({display: (!parseInt(bwg_params_carousel[bwg]['carousel_play_pause_butt']) ? 'none' : '')});

    if (!parseInt(bwg_params_carousel[bwg]['carousel_prev_next_butt'])) {
      jQuery("#bwg_carousel-left" + bwg).css({display: 'none'});
      jQuery("#bwg_carousel-right" + bwg).css({display: 'none'});
    } else {
      jQuery("#bwg_carousel-right" + bwg).css({display: ''});
      jQuery("#bwg_carousel-left" + bwg).css({display: ''});
    }

    jQuery(".inner_instagram_iframe_bwg_embed_frame_" + bwg).each(function () {
      /* 16 is 2*padding inside iframe */
      /* 96 is 2*padding(top) + 1*padding(bottom) + 40(footer) + 32(header) */
      var parent_container = jQuery(this).parent();
      if (bwg_params_carousel[bwg]['image_height'] / (parseInt(parent_container.attr('data-height')) + 96) < bwg_params_carousel[bwg]['image_width'] / parseInt(parent_container.attr('data-width'))) {
        parent_container.height(bwg_params_carousel[bwg]['image_height'] * par);
        parent_container.width((parent_container.height() - 96) * parent_container.attr('data-width') / parent_container.attr('data-height') + 16);
      } else {
        parent_container.width(bwg_params_carousel[bwg]['image_width'] * par);
        parent_container.height((parent_container.width() - 16) * parent_container.attr('data-height') / parent_container.attr('data-width') + 96);
      }
    });

    jQuery(".bwg_carousel_image_container_" + bwg).css({
      width: bwg_params_carousel[bwg]['image_width'] * par,
      height: bwg_params_carousel[bwg]['image_height'] * par
    });
    jQuery(".bwg_carousel_watermark_text_" + bwg + ", .bwg_carousel_watermark_text_" + bwg + ":hover").css({fontSize: ((parent_width) * (bwg_params_carousel[bwg]['watermark_font_size'] / bwg_params_carousel[bwg]['image_width']) * par)});
    jQuery(".bwg_carousel-image " + bwg).css({
      width: bwg_params_carousel[bwg]['image_width'] * par,
      height: bwg_params_carousel[bwg]['image_height'] * par
    });
    jQuery(".bwg_carousel_watermark_container_" + bwg).css({
      width: bwg_params_carousel[bwg]['image_width'] * par,
      height: bwg_params_carousel[bwg]['image_height'] * par
    });
    jQuery(".bwg_carousel_embed_video_" + bwg).css({
      width: bwg_params_carousel[bwg]['image_width'] * par,
      height: bwg_params_carousel[bwg]['image_height'] * par
    });
    jQuery(".bwg_carousel_watermark_spun_" + bwg).css({
      width: bwg_params_carousel[bwg]['image_width'] * par,
      height: bwg_params_carousel[bwg]['image_height'] * par
    });
    jQuery(".bwg_carousel-container" + bwg).css({
      width: parent_width,
      height: bwg_params_carousel[bwg]['image_height'] * par
    });
    jQuery(".bwg_video_hide" + bwg).css({
      width: bwg_params_carousel[bwg]['image_width'] * par,
      height: bwg_params_carousel[bwg]['image_height'] * par
    });

    if (!bwg_params_carousel[bwg]['carousel'] || resize) {
      if (resize && bwg_params_carousel[bwg]['carousel']) {
        bwg_params_carousel[bwg]['carousel'].pause();
      }
      bwg_params_carousel[bwg]['carousel'] = jQuery("#bwg_carousel" + bwg).featureCarousel({
        containerWidth: parent_width * par,
        containerHeight: bwg_params_carousel[bwg]['image_height'] * par,
        fit_containerWidth: bwg_params_carousel[bwg]['carousel_fit_containerWidth'],
        largeFeatureWidth: bwg_params_carousel[bwg]['image_width'] * par,
        largeFeatureHeight: bwg_params_carousel[bwg]['image_height'] * par,
        smallFeaturePar: bwg_params_carousel[bwg]['carousel_image_par'],
        currentlyMoving: false,
        startingFeature: bwg_params_carousel[bwg]['bwg_currentCenterNum'],
        featuresArray: [],
        timeoutVar: null,
        rotationsRemaining: 0,
        autoPlay: bwg_params_carousel[bwg]['car_inter'] * 1000,
        interval: bwg_params_carousel[bwg]['carousel_interval'] * 1000,
        imagecount: bwg_params_carousel[bwg]['carousel_image_column_number'],
        bwg_number: bwg,
        enable_image_title: bwg_params_carousel[bwg]['enable_image_title'],
        borderWidth: 0
      });
    }
  }
}

function bwg_carousel_watermark(bwg) {
  var par = 1;
  var parent_width = jQuery("#bwg_container1_" + bwg).parent().width();
  if ( parent_width < bwg_params_carousel[bwg]['carousel_r_width'] ) {
    par = parent_width / bwg_params_carousel[bwg]['carousel_r_width'];
  }
  if ( parent_width >= bwg_params_carousel[bwg]['image_width'] ) {
    /* Set watermark container size.*/
    bwg_carousel_change_watermark_container(bwg);
    jQuery("#bwg_carousel_play_pause-ico_" + bwg).css({fontSize: bwg_params_carousel[bwg]['carousel_play_pause_btn_size']});
    jQuery(".bwg_carousel_watermark_image_" + bwg).css({maxWidth: bwg_params_carousel[bwg]['watermark_width'] * par, maxHeight: bwg_params_carousel[bwg]['watermark_height'] * par});
    jQuery(".bwg_carousel_watermark_text_" + bwg + ", .bwg_carousel_watermark_text_" + bwg + ":hover").css({fontSize: par * bwg_params_carousel[bwg]['watermark_font_size']});
  }
  else {
    /* Set watermark container size.*/
    var img_width = bwg_params_carousel[bwg]['image_width'] / par;
    bwg_carousel_change_watermark_container(bwg);
    jQuery("#bwg_carousel_play_pause-ico_" + bwg).css({fontSize: (parent_width * bwg_params_carousel[bwg]['carousel_play_pause_btn_size'] / img_width )});
    jQuery(".bwg_carousel_watermark_image_" + bwg).css({maxWidth: ( parent_width * bwg_params_carousel[bwg]['watermark_width'] / img_width), maxHeight: (parent_width * bwg_params_carousel[bwg]['watermark_height'] / img_width)});
    jQuery(".bwg_carousel_watermark_text_" + bwg + ", .bwg_carousel_watermark_text_" + bwg + ":hover").css({fontSize:  (parent_width * bwg_params_carousel[bwg]['watermark_font_size'] / img_width )});
  }
}

function bwg_carousel_change_watermark_container(bwg) {
  jQuery(".bwg_carousel" + bwg).children().each(function() {
    if (jQuery(this).css("zIndex") == 2) {
      var bwg_current_image_span = jQuery(this).find("img");
      if (!bwg_current_image_span.length) {
        bwg_current_image_span = jQuery(this).find("iframe");
      }
      var width = bwg_current_image_span.width();
      var height = bwg_current_image_span.height();
      jQuery(".bwg_carousel_watermark_spun_" + bwg).width(width);
      jQuery(".bwg_carousel_watermark_spun_" + bwg).height(height);
      jQuery(".bwg_carousel_title_spun_" + bwg).width(width);
      jQuery(".bwg_carouel_title_spun_" + bwg).height(height);
      jQuery(".bwg_carousel_watermark_" + bwg).css({display: 'none'});
    }
  });
}

/* Change hidden carousel items to visiblle */
function bwg_carousel_preload( bwg, right ) {
  var preload_images_count = 1;

  var selector = jQuery(".bwg_carousel_preload").get();
  if ( !right ) {
    selector.reverse();
  }
  var i = 0;
  jQuery(selector).each(function () {
    if ( ++i > preload_images_count ) {
      return false;
    }
    if ( jQuery(this).parent().hasClass('bwg_carousel_embed_video_' + bwg)
      || jQuery(this).parent().hasClass('bwg_embed_frame_' + bwg)
      || jQuery(this).parent().hasClass('bwg_carousel_video') ) {
      /* Embed. */
      jQuery(this).attr('src', jQuery(this).attr('data-src'));
      jQuery(this).on("load", function () {
        jQuery(this).removeClass('bwg_carousel_preload');
      });
      /* Load video after changing source. */
      if ( jQuery(this).parent().hasClass('bwg_carousel_video') ) {
        jQuery(".bwg_carousel_video")[0].load();
        jQuery(this).parent().parent().removeClass('bwg_carousel_preload');
      }
      jQuery(this).removeAttr('data-src');
    }
    else {
      /* Image. */
      jQuery(this).css({
        'background-image': "url('" + jQuery(this).attr('data-background') + "')",
        'height': '100%',
      });
      jQuery(this).removeClass('bwg_carousel_preload');
      jQuery(this).removeAttr('data-background');
    }
  });
}

function bwg_slideshow_ready() {
  jQuery(".bwg_slideshow").each(function () {
    var bwg = jQuery(this).data("bwg");
    if ( jQuery("#bwg_slideshow_image_container_" + bwg).length ) {
      bwg_params[bwg] = JSON.parse(jQuery("#bwg_slideshow_image_container_" + bwg).attr("data-params"));
      bwg_params[bwg]['event_stack'] = [];
      bwg_container_loaded(bwg);
      var data = bwg_params[bwg]['data'];
      if (typeof jQuery().swiperight !== 'undefined') {
        if (jQuery.isFunction(jQuery().swiperight)) {
          jQuery("#bwg_container1_" + bwg).swiperight(function () {
            bwg_change_image(parseInt(jQuery("#bwg_current_image_key_" + bwg).val()), (parseInt(jQuery("#bwg_current_image_key_" + bwg).val()) - bwg_iterator(bwg)) >= 0 ? (parseInt(jQuery("#bwg_current_image_key_" + bwg).val()) - bwg_iterator(bwg)) % data.length : data.length - 1, data, '', bwg);
            return false;
          });
        }
      }
      if (typeof jQuery().swipeleft !== 'undefined') {
        if (jQuery.isFunction(jQuery().swipeleft)) {
          jQuery("#bwg_container1_" + bwg).swipeleft(function () {
            bwg_change_image(parseInt(jQuery("#bwg_current_image_key_" + bwg).val()), (parseInt(jQuery("#bwg_current_image_key_" + bwg).val()) + bwg_iterator(bwg) % data.length), data, '', bwg);
            return false;
          });
        }
      }
      var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
      var bwg_click = isMobile ? 'touchend' : 'click';
      bwg_popup_resize(bwg);
      jQuery(".bwg_slideshow_watermark_" + bwg).css({display: 'none'});
      jQuery(".bwg_slideshow_title_text_" + bwg).css({display: 'none'});
      jQuery(".bwg_slideshow_description_text_" + bwg).css({display: 'none'});
      setTimeout(function () {
        bwg_change_watermark_container(bwg);
      }, 500);
      /* Set image container height.*/
      if (bwg_params[bwg]['filmstrip_direction'] == 'horizontal') {
        jQuery(".bwg_slideshow_image_container_" + bwg).height(jQuery(".bwg_slideshow_image_wrap_" + bwg).height() - bwg_params[bwg]['slideshow_filmstrip_height']);
      }
      else {
        jQuery(".bwg_slideshow_image_container_" + bwg).width(jQuery(".bwg_slideshow_image_wrap_" + bwg).width() - bwg_params[bwg]['slideshow_filmstrip_width']);
      }
      var mousewheelevt = (/Firefox/i.test(navigator.userAgent)) ? "DOMMouseScroll" : "mousewheel";
      /* FF doesn't recognize mousewheel as of FF3.x */
      jQuery(".bwg_slideshow_filmstrip_" + bwg).bind(mousewheelevt, function (e) {
        var evt = window.event || e;
        /* Equalize event object.*/
        evt = evt.originalEvent ? evt.originalEvent : evt;
        /* Convert to originalEvent if possible.*/
        var delta = evt.detail ? evt.detail * (-40) : evt.wheelDelta;
        /* Check for detail first, because it is used by Opera and FF.*/
        if (delta > 0) {
          /* Scroll up.*/
          jQuery(".bwg_slideshow_filmstrip_left_" + bwg).trigger("click");
        }
        else {
          /* Scroll down.*/
          jQuery(".bwg_slideshow_filmstrip_right_" + bwg).trigger("click");
        }
        return false;
      });
      jQuery(".bwg_slideshow_filmstrip_right_" + bwg).on(bwg_click, function () {
        jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).stop(true, false);
        if (bwg_params[bwg]['left_or_top'] == 'left') { /* For left, width */
          if (bwg_params[bwg]['width_or_height'] == 'width') {
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left >= -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).width() - jQuery(".bwg_slideshow_filmstrip_" + bwg).width())) {
              jQuery(".bwg_slideshow_filmstrip_left_" + bwg).css({opacity: 1});
              if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left < -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).width() - jQuery(".bwg_slideshow_filmstrip_" + bwg).width() - (parseInt(bwg_params[bwg]['filmstrip_thumb_margin_hor']) + parseInt(bwg_params[bwg]['slideshow_filmstrip_width'])))) {
                jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({left: -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).width() - jQuery(".bwg_slideshow_filmstrip_" + bwg).width())}, 500, 'linear');
              }
              else {
                jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({left: (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left - (parseInt(bwg_params[bwg]['filmstrip_thumb_margin_hor']) + parseInt(bwg_params[bwg]['slideshow_filmstrip_width'])))}, 500, 'linear');
              }
            }
            /* Disable right arrow.*/
            window.setTimeout(function () {
              if ((jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left) == -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).width() - jQuery(".bwg_slideshow_filmstrip_" + bwg).width())) {
                jQuery(".bwg_slideshow_filmstrip_right_" + bwg).css({opacity: 0.3});
              }
            }, 500);
          }
          else { /* For left, height */
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left >= -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).height() - jQuery(".bwg_slideshow_filmstrip_" + bwg).height())) {
              jQuery(".bwg_slideshow_filmstrip_left_" + bwg).css({opacity: 1});
              if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left < -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).height() - jQuery(".bwg_slideshow_filmstrip_" + bwg).height() - (parseInt(bwg_params[bwg]['filmstrip_thumb_margin_hor']) + parseInt(bwg_params[bwg]['slideshow_filmstrip_width'])))) {
                jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({left: -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).height() - jQuery(".bwg_slideshow_filmstrip_" + bwg).height())}, 500, 'linear');
              }
              else {
                jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({left: (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left - (parseInt(bwg_params[bwg]['filmstrip_thumb_margin_hor']) + parseInt(bwg_params[bwg]['slideshow_filmstrip_width'])))}, 500, 'linear');
              }
            }
            /* Disable right arrow.*/
            window.setTimeout(function () {
              if ((jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left) == -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).height() - jQuery(".bwg_slideshow_filmstrip_" + bwg).height())) {
                jQuery(".bwg_slideshow_filmstrip_right_" + bwg).css({opacity: 0.3});
              }
            }, 500);
          }
        }
        else {
          if (bwg_params[bwg]['width_or_height'] == 'width') { /* For top, width */
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top >= -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).width() - jQuery(".bwg_slideshow_filmstrip_" + bwg).width())) {
              jQuery(".bwg_slideshow_filmstrip_left_" + bwg).css({opacity: 1});
              if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top < -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).width() - jQuery(".bwg_slideshow_filmstrip_" + bwg).width() - parseInt(bwg_params[bwg]['filmstrip_thumb_margin_hor']) + parseInt(bwg_params[bwg]['slideshow_filmstrip_width']))) {
                jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({top: -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).width() - jQuery(".bwg_slideshow_filmstrip_" + bwg).width())}, 500, 'linear');
              }
              else {
                jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({top: (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top - parseInt(bwg_params[bwg]['filmstrip_thumb_margin_hor']) + parseInt(bwg_params[bwg]['slideshow_filmstrip_width']))}, 500, 'linear');
              }
            }
            /* Disable right arrow.*/
            window.setTimeout(function () {
              if ((jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top) == -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).width() - jQuery(".bwg_slideshow_filmstrip_" + bwg).width())) {
                jQuery(".bwg_slideshow_filmstrip_right_" + bwg).css({opacity: 0.3});
              }
            }, 500);
          }
          else { /* For top, height */
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top >= -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).height() - jQuery(".bwg_slideshow_filmstrip_" + bwg).height())) {
              jQuery(".bwg_slideshow_filmstrip_left_" + bwg).css({opacity: 1});
              if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top < -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).height() - jQuery(".bwg_slideshow_filmstrip_" + bwg).height() - (parseInt(bwg_params[bwg]['filmstrip_thumb_margin_hor']) + parseInt(bwg_params[bwg]['slideshow_filmstrip_width'])))) {
                jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({top: -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).height() - jQuery(".bwg_slideshow_filmstrip_" + bwg).height())}, 500, 'linear');
              }
              else {
                jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({top: (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top - (parseInt(bwg_params[bwg]['filmstrip_thumb_margin_hor']) + parseInt(bwg_params[bwg]['slideshow_filmstrip_width'])))}, 500, 'linear');
              }
            }
            /* Disable right arrow.*/
            window.setTimeout(function () {
              if ((jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top) == -(jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).height() - jQuery(".bwg_slideshow_filmstrip_" + bwg).height())) {
                jQuery(".bwg_slideshow_filmstrip_right_" + bwg).css({opacity: 0.3});
              }
            }, 500);
          }
        }
      });
      jQuery(".bwg_slideshow_filmstrip_left_" + bwg).on(bwg_click, function () {
        jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).stop(true, false);
        if (bwg_params[bwg]['left_or_top'] == 'left') {
          if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left < 0) {
            jQuery(".bwg_slideshow_filmstrip_right_" + bwg).css({opacity: 1});
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left > -(bwg_params[bwg]['filmstrip_thumb_margin_hor'] + bwg_params[bwg]['slideshow_filmstrip_width'])) {
              jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({left: 0}, 500, 'linear');
            }
            else {
              jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({left: (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left + parseInt(bwg_params[bwg]['filmstrip_thumb_margin_hor']) + parseInt(bwg_params[bwg]['slideshow_filmstrip_width']))}, 500, 'linear');
            }
          }
          /* Disable left arrow.*/
          window.setTimeout(function () {
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().left == 0) {
              jQuery(".bwg_slideshow_filmstrip_left_" + bwg).css({opacity: 0.3});
            }
          }, 500);
        }
        else {
          if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top < 0) {
            jQuery(".bwg_slideshow_filmstrip_right_" + bwg).css({opacity: 1});
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top > -(bwg_params[bwg]['filmstrip_thumb_margin_hor'] + bwg_params[bwg]['slideshow_filmstrip_width'])) {
              jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({top: 0}, 500, 'linear');
            }
            else {
              jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).animate({top: (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top + parseInt(bwg_params[bwg]['filmstrip_thumb_margin_hor']) + parseInt(bwg_params[bwg]['slideshow_filmstrip_width']))}, 500, 'linear');
            }
          }
          /* Disable top arrow.*/
          window.setTimeout(function () {
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_" + bwg).position().top == 0) {
              jQuery(".bwg_slideshow_filmstrip_left_" + bwg).css({opacity: 0.3});
            }
          }, 500);
        }
      });
      if (bwg_params[bwg]['width_or_height'] == 'width') {
        /* Set filmstrip initial position.*/
        bwg_set_filmstrip_pos(jQuery(".bwg_slideshow_filmstrip_" + bwg).width(), bwg);
      }
      else {
        /* Set filmstrip initial position.*/
        bwg_set_filmstrip_pos(jQuery(".bwg_slideshow_filmstrip_" + bwg).height(), bwg);
      }
      /* Play/pause.*/
      jQuery("#bwg_slideshow_play_pause_" + bwg).off(bwg_click).on(bwg_click, function () {
        if (jQuery(".bwg_ctrl_btn_" + bwg).hasClass("bwg-icon-play")) {
          bwg_play(bwg_params[bwg]['data'], bwg);
          jQuery(".bwg_slideshow_play_pause_" + bwg).attr("title", bwg_objectsL10n.pause);
          jQuery(".bwg_slideshow_play_pause_" + bwg).attr("class", "bwg-icon-pause bwg_ctrl_btn_" + bwg + " bwg_slideshow_play_pause_" + bwg + "");
          if (bwg_params[bwg]['enable_slideshow_music'] == 1) {
            document.getElementById("bwg_audio_" + bwg).play();
          }
        }
        else {
          /* Pause.*/
          window.clearInterval(window['bwg_playInterval' + bwg]);
          jQuery(".bwg_slideshow_play_pause_" + bwg).attr("title", "Play");
          jQuery(".bwg_slideshow_play_pause_" + bwg).attr("class", "bwg-icon-play bwg_ctrl_btn_" + bwg + " bwg_slideshow_play_pause_" + bwg + "");
          if (bwg_params[bwg]['enable_slideshow_music'] == 1) {
            document.getElementById("bwg_audio_" + bwg).pause();
          }
        }
      });
      if (bwg_params[bwg]['enable_slideshow_autoplay'] != 0) {
        bwg_play(bwg_params[bwg]['data'], bwg);
        jQuery(".bwg_slideshow_play_pause_" + bwg).attr("title", bwg_objectsL10n.pause);
        jQuery(".bwg_slideshow_play_pause_" + bwg).attr("class", "bwg-icon-pause bwg_ctrl_btn_" + bwg + " bwg_slideshow_play_pause_" + bwg + "");
        if (bwg_params[bwg]['enable_slideshow_music'] == 1 && jQuery("#bwg_audio_" + bwg).length) {
          document.getElementById("bwg_audio_" + bwg).play();
        }
      }
      if (bwg_params[bwg]['preload_images']) {
        bwg_preload_images(parseInt(jQuery("#bwg_current_image_key_".$bwg).val()), bwg);
      }
      jQuery(".bwg_slideshow_image_" + bwg).removeAttr("width");
      jQuery(".bwg_slideshow_image_" + bwg).removeAttr("height");
    }
  });
}

function bwg_image_browser_resize() {
  jQuery(".bwg_image_browser").each(function () {
    var bwg = jQuery(this).attr('data-bwg');
    if (jQuery('.image_browser_images_conteiner_' + bwg).length) {
      bwg_params_ib[bwg] = JSON.parse(jQuery('#bwg_container1_' + bwg + ' .image_browser_images_conteiner_' + bwg ).attr('data-params'));
      bwg_image_browser( bwg );
    }
  });
}

function bwg_image_browser_ready() {
  /* For ImageBrowser */
  jQuery(".bwg_image_browser").each(function () {
    var bwg = jQuery(this).attr('data-bwg');
    bwg_container_loaded( bwg );
    if (jQuery('.image_browser_images_conteiner_' + bwg).length) {
      bwg_params_ib[bwg] = JSON.parse(jQuery('.image_browser_images_conteiner_' + bwg).attr('data-params'));
      setTimeout(function () {
        bwg_image_browser(bwg);
      }, 3);
    }
  });
}

/* hide search_placeholder_title class container */
function bwg_search_focus(that) {
  jQuery(that).parent().find('.bwg_search_input').focus();
  jQuery(that).hide();
}

/* show search and reset icons */
function bwg_key_press(that) {
  jQuery(that).parent().find('.bwg_search_reset_container').removeClass("bwg-hidden");
  jQuery(that).parent().find('.bwg_search_loupe_container1').removeClass("bwg-hidden");
}

function bwg_all_thumnails_loaded(that) {
  var thumbnails_count = 0;
  var thumbnails_loaded = jQuery(that).find("img").length;
  if (0 == thumbnails_loaded) {
    bwg_all_thumbnails_loaded_callback(that);
  }
  else {
    jQuery( that ).find( "img" ).each( function () {
      var fakeSrc = jQuery( this ).attr( "src" );
      jQuery( "<img/>" ).attr( "src", fakeSrc ).on( "load error", function () {
        if ( ++thumbnails_count >= thumbnails_loaded ) {
          bwg_all_thumbnails_loaded_callback( that );
        }
      });
    });
  }

  return thumbnails_loaded == 0;
}

function bwg_all_thumbnails_loaded_callback(that) {
  if (jQuery(that).hasClass('bwg-thumbnails') && !jQuery(that).hasClass('bwg-masonry-thumbnails')) {
    bwg_thumbnail( that );
  }
  if (jQuery(that).hasClass('bwg-masonry-thumbnails')) {
    bwg_thumbnail_masonry( that );
  }
  if (jQuery(that).hasClass('bwg-album-extended')) {
    bwg_album_extended( that );
  }
}


function bwg_album_thumbnail(that) {
  bwg_container_loaded(jQuery(that).data('bwg'));
}
function bwg_album_extended(that) {
  var container_width = jQuery(that).width();
  var thumb_width = jQuery(that).data("thumbnail-width");
  var spacing = jQuery(that).data("spacing");
  var max_count = jQuery(that).data("max-count");
  var column_count = parseInt(container_width / (2 * thumb_width));
  if ( column_count < 1 ) {
    column_count = 1;
  }
  if (column_count > max_count) {
    column_count = max_count;
  }
  var min_width = 100 / column_count;
  var bwg_item = jQuery(that).find(".bwg-extended-item");
  var margin_left = parseInt(bwg_item.css("margin-left"));
  var margin_right = parseInt(bwg_item.css("margin-right"));
  bwg_item.css({
    width: "calc(" + min_width + "% - " + (margin_left + margin_right) + "px)"
  });
  if ( bwg_item.width() < thumb_width ) {
    bwg_item.find(".bwg-extended-item0, .bwg-extended-item1").css({
      width: 'calc(100% - ' + spacing + 'px)'
    });
  }
  else if ( bwg_item.width() > 2 * thumb_width ) {
    bwg_item.find(".bwg-extended-item0").css({
      width: 'calc(50% - ' + spacing + 'px)'
    });
    bwg_item.find(".bwg-extended-item1").css({
      width: 'calc(100% - ' + (thumb_width + spacing * 2) + 'px)'
    });
  }
  else {
    bwg_item.find(".bwg-extended-item0, .bwg-extended-item1").css({
      width: 'calc(50% - ' + spacing + 'px)'
    });
  }

  jQuery(that).children(".bwg-extended-item").each(function () {
    var image = jQuery(this).find("img");
    var item0 = jQuery(this).find(".bwg-item0");
    var item2 = jQuery(this).find(".bwg-item2");
    var image_width = image.data('width');
    var image_height = image.data('height');
    if(image_width == '' || image_height == '') {
      image_width = image.width();
      image_height = image.height();
    }
    var scale = image_width/image_height;

    if ( (item2.width() / item2.height()) > (image_width / image_height) ) {
      if ( item2.width() > image_width ) {
        image.css({width: "100%", height: item2.width()/scale});
      }
      else {
        image.css({maxWidth: "100%", height: item2.width()/scale});
      }
      image_width = item2.width();
      image_height = item2.width()/scale;
    }
    else {
      if ( item2.height() > image_height ) {
        image.css({height : "100%", width : item2.height()*scale, maxWidth : 'initial'});
      }
      else {
        image.css({maxHeight: "100%", width: item2.height()*scale, maxWidth:'initial'});
      }
      image_height = item2.height();
      image_width = item2.height()*scale;
    }
    jQuery(this).find(".bwg-item2").css({
      marginLeft: (item0.width() - image_width) / 2,
      marginTop: (item0.height() - image_height) / 2
    });
  });
  bwg_container_loaded(jQuery(that).data('bwg'));
}

function bwg_thumbnail(that) {
  var container_width = jQuery(that).width();
  var thumb_width = jQuery(that).data("thumbnail-width");
  var max_count = jQuery(that).data("max-count");
  var column_count = parseInt(container_width / thumb_width) + 1;
  if (column_count > max_count) {
    column_count = max_count;
  }
  /*var flex = 1 / column_count;*/
  var min_width = 100 / column_count;
  var bwg_item = jQuery(that).find(".bwg-item");
  bwg_item.css({
    /*flexGrow: flex,*/
    width: min_width + "%"
  });
  jQuery(that).children(".bwg-item").each(function () {
    var image = jQuery(this).find("img");
    var item2 = jQuery(this).find(".bwg-item2");
    var item1 = jQuery(this).find(".bwg-item1");
    var container_width = item2.width() > 0 ? item2.width() : item1.width();
    var container_height = item2.height() > 0 ? item2.height() : item1.height();
    var image_width = image.data('width');
    var image_height = image.data('height');
    if(image_width == '' || image_height == '' || typeof image_width === 'undefined' || typeof image_height === 'undefined') {
      image_width = image.width();
      image_height = image.height();
    }
    var scale = image_width/image_height;
    image.removeAttr("style");
    if ( (container_width / container_height) > scale ) {
      if ( container_width > image_width ) {
        image.css({width: "100%", height: container_width/scale});
      }
      else {
        /* Math.ceil image width in some cases less from the container with due to rounded */
        image.css({maxWidth: "100%", height: Math.ceil(container_width/scale)});
      }
      image_width = container_width;
      image_height = container_width/scale;
    }
    else {
      if ( container_height > image.height() ) {
        image.css({height : "100%", width : container_height*scale, maxWidth : 'initial'});
      }
      else {
        image.css({maxHeight: "100%", width: container_height*scale, maxWidth:'initial'});
      }
      image_height = container_height;
      image_width = container_height*scale;
    }

    jQuery(this).find(".bwg-item2").css({
      marginLeft: (container_width - image_width) / 2,
      marginTop: (container_height - image_height) / 2
    });
  });
  bwg_container_loaded(jQuery(that).data('bwg'));
}

function bwg_thumbnail_masonry(that) {
  bwg = jQuery(that).attr("data-bwg");
  var type = "#bwg_thumbnails_masonry_"+bwg;
  if(jQuery("#bwg_album_masonry_"+bwg).length) {
    type = "#bwg_album_masonry_"+bwg;
  }
  if(jQuery(".bwg-container-temp"+bwg).length === 0) {
    jQuery(type).clone().appendTo("#bwg_container3_" + bwg).removeAttr("id").removeClass("bwg-container-" + bwg).addClass("bwg-container-temp" + bwg);
    jQuery(".bwg-container-temp"+bwg).empty();
  }
  var temp = jQuery(".bwg-container-temp"+bwg);
  var cont = jQuery(type);

  var container = temp;
  temp.prepend( cont.html() );

  container.find('.bwg-empty-item').remove();
  var masonry_type = container.data("masonry-type");

  if ('horizontal' == masonry_type) {
    var thumb_height = container.data( "thumbnail-height" );
    var max_count = container.data( "max-count" );
    var column_widths = [];
    for ( i = 0; i < max_count; i++ ) {
      column_widths.push( 0 );
    }
    container.find( ".bwg-item" ).each( function () {
      var order = column_widths.indexOf( Math.min.apply( Math, column_widths ) );
      jQuery( this ).css( { height: thumb_height, order: order + 1 } );
      /* Use getBoundingClientRect instead of jQuery.width() to avoid rounding. */
      column_widths[order] += jQuery( this )[0].getBoundingClientRect().width;
    } );
    var container_width = Math.max.apply( Math, column_widths );
    container.width( container_width );

    /* Equalize all rows. */
    for ( i = 0; i < max_count; i++ ) {
      if ( column_widths[i] < container_width ) {
        container.append( jQuery( '<div class="bwg-item bwg-empty-item"></div>' ).css( {
          height: thumb_height,
          order: i + 1,
          width: container_width - column_widths[i]
        } ) );
      }
    }
  }
  else {
    container.removeAttr('style');
    var container_width = container.width();
    var thumb_width = container.data( "thumbnail-width" );
    var max_count = container.data( "max-count" );
    var column_count = parseInt( container_width / thumb_width ) + (container.data('resizable-thumbnails') == '0' ? 0 : 1);
    if ( column_count > max_count ) {
      column_count = max_count;
    }
    var thumb_count = container.find( ".bwg-item" ).length;
    if ( thumb_count < column_count ) {
      column_count = thumb_count;
    }
    var min_width = 100 / column_count;
    var column_heights = [];
    var scaleHeight;
    var scale;
    for ( i = 0; i < column_count; i++ ) {
      column_heights.push( 0 );
    }
    container.find( ".bwg-item" ).each( function () {
      var order = column_heights.indexOf( Math.min.apply( Math, column_heights ) );
      jQuery( this ).css( { width: min_width + "%", order: order + 1 } );
      if ( jQuery( this ).find("img").attr("data-width").length > 0 && jQuery( this ).find("img").attr("data-height").length > 0 ) {
        scale = jQuery( this ).find("img").data("width")/jQuery( this ).find("img").data("height");
        scaleHeight = jQuery( this ).width()/scale;
        /* calculating height of image title and description */
        /* finding title/description with 'a>' selector to avoid calculating heights for 'Show on hover' option. */
        title_h = this.querySelector("a .bwg-title2") ? this.querySelector("a .bwg-title2").getClientRects()[0].height : 0;
        desc_h = this.querySelector("a .bwg-masonry-thumb-description") ? this.querySelector("a .bwg-masonry-thumb-description").getClientRects()[0].height : 0;
        var k = title_h + desc_h;
        jQuery(this).height( scaleHeight + k );
      }
      /* Use getBoundingClientRect instead of jQuery.height() to avoid rounding. */
      column_heights[order] += jQuery( this )[0].getBoundingClientRect().height;
    } );
    var container_height = Math.max.apply( Math, column_heights );

    /* Equalize all columns. */
    for ( i = 0; i < column_count; i++ ) {
      if ( column_heights[i] < container_height ) {
        container.append( jQuery( '<div class="bwg-item bwg-empty-item"></div>' ).css( {
          width: min_width + "%",
          order: i + 1,
          height: container_height - column_heights[i]
        } ) );
      }
    }
    container.outerWidth( column_count * thumb_width );
    container.height( container_height );
  }
  if ( temp.html() != "" ) {
    cont.outerWidth( column_count * thumb_width );
    if (container_height != '0') {
      cont.css('opacity',"1");
      cont.height( container_height )
    } else {
      cont.css('opacity',"0");
    }

    cont.empty();
    var html = temp.html();
    cont.append(html);
    cont.find('.bwg_lazyload').each( function() {
      if (jQuery(this).attr("data-original") != undefined && jQuery(this).attr("data-original") != '') {
        jQuery(this).attr("src", jQuery(this).attr("data-original"));
      }
    });
    temp.empty().hide();
  }
  bwg_container_loaded(container.data('bwg'));
}

function bwg_container_loaded(bwg) {
  jQuery('#gal_front_form_' + bwg).removeClass('bwg-hidden');
  jQuery('#ajax_loading_' + bwg).addClass('bwg-hidden');
}

function bwg_thumbnail_mosaic_logic( container ) {
  var bwg = container.attr('data-bwg');
  var block_id = container.attr('data-block-id');
  var padding_px = parseInt(container.attr('data-thumb-padding')) / 2;
  var border_px = parseInt(container.attr('data-thumb-border'));
  var border_and_padding = border_px + padding_px;
  if (container.attr('data-mosaic-direction') == 'horizontal') {
    var thumb_height = parseInt(container.attr('data-height'));
    /*resizable mosaic*/
    if (container.attr('data-resizable') == '1') {
      if (jQuery(window).width() >= 1920) {
        var thumbnail_height = (1+jQuery(window).width()/1920)*thumb_height;
      }
      else if (jQuery(window).width() <= 640) {
        var thumbnail_height = jQuery(window).width()/640*thumb_height;
      }
      else {
        var thumbnail_height = thumb_height;
      }
    }
    else {
      var thumbnail_height = thumb_height;
    }

    /* initialize */
    var mosaic_pics = jQuery(".bwg_mosaic_thumb_" + bwg);
    mosaic_pics.each(function (index) {
      var thumb_w = jQuery(this).data('width');
      var thumb_h = jQuery(this).data('height');
      if(thumb_w == '' || thumb_h == '' || typeof thumb_w === 'undefined' || typeof thumb_h === 'undefined') {
        thumb_w = mosaic_pics.get(index).naturalWidth;
        thumb_h = mosaic_pics.get(index).naturalHeight;
      }

      thumb_w = thumb_w * thumbnail_height / thumb_h;
      mosaic_pics.eq(index).height(thumbnail_height);
      mosaic_pics.eq(index).width(thumb_w);
    });
    /* resize */
    var divwidth = jQuery("#bwg_mosaic_thumbnails_div_" + bwg).width() / 100 * parseInt(container.attr('data-total-width'));
    /*set absolute mosaic width*/
    jQuery("#" + block_id).width(divwidth);
    var row_height = thumbnail_height + 2 * border_and_padding;
    var row_number = 0;
    var row_of_img = [];
    /* row of the current image*/
    row_of_img[0] = 0;
    var imgs_by_rows = [];
    /* number of images in each row */
    imgs_by_rows[0] = 0;
    var row_cum_width = 0;
    /* width of the current row */
    /* create masonry horizontal */
    mosaic_pics.each(function (index) {
      row_cum_width2 = row_cum_width + mosaic_pics.eq(index).width() + 2 * border_and_padding;
      if (row_cum_width2 - divwidth < 0) { /* add the image to the row */
        row_cum_width = row_cum_width2;
        row_of_img[index] = row_number;
        imgs_by_rows[row_number]++;
      }
      else {
        if (index !== mosaic_pics.length - 1) { /* if not last element */
          if ((Math.abs(row_cum_width - divwidth) > Math.abs(row_cum_width2 - divwidth)) || !(!(Math.abs(row_cum_width - divwidth) <= Math.abs(row_cum_width2 - divwidth)) || !(imgs_by_rows[row_number] == 0))) {
            if (index !== mosaic_pics.length - 2) { /* add and shrink if not the second */
              row_cum_width = row_cum_width2;
              row_of_img[index] = row_number;
              imgs_by_rows[row_number]++;
              row_number++;
              imgs_by_rows[row_number] = 0;
              row_cum_width = 0;
            }
            else { /* add second but NOT shrink and not change row */
              row_cum_width = row_cum_width2;
              row_of_img[index] = row_number;
              imgs_by_rows[row_number]++;
            }
          }
          else { /* add to new row and  stretch prev row (or shrink if even one pic is big) */
            row_number++;
            imgs_by_rows[row_number] = 1;
            row_of_img[index] = row_number;
            row_cum_width = row_cum_width2 - row_cum_width;
          }
        }
        else { /* if the last element, add and shrink */
          row_cum_width = row_cum_width2;
          row_of_img[index] = row_number;
          imgs_by_rows[row_number]++;
        }
      }
    });
    /* create mosaics */
    var stretch = [];
    /* stretch[row] factors */
    var row_new_height = [];
    /* array to store height of every column */
    for (var row = 0; row <= row_number; row++) {
      stretch[row] = 1;
      row_new_height[row] = row_height;
    }
    /* find stretch factors */
    for (var row = 0; row <= row_number; row++) {
      row_cum_width = 0;
      mosaic_pics.each(function (index) {
        if (row_of_img[index] == row) {
          row_cum_width += mosaic_pics.eq(index).width();
        }
      });
      stretch[row] = x = (divwidth - imgs_by_rows[row] * 2 * border_and_padding) / row_cum_width;
      row_new_height[row] = (row_height - 2 * border_and_padding) * stretch[row] + 2 * border_and_padding;
    }
    /* stretch and shift to create mosaic horizontal */
    var last_img_index = [];
    /* last image in row */
    last_img_index[0] = 0;
    /* zero points */
    var img_left = [];
    var row_top = [];
    img_left[0] = 0;
    row_top[0] = 0;
    for (var row = 1; row <= row_number; row++) {
      img_left[row] = img_left[0];
      row_top[row] = row_top[row - 1] + row_new_height[row - 1];
    }
    mosaic_pics.each(function (index) {
      var thumb_w = mosaic_pics.eq(index).width();
      var thumb_h = mosaic_pics.eq(index).height();
      mosaic_pics.eq(index).width(thumb_w * stretch[row_of_img[index]]);
      mosaic_pics.eq(index).height(thumb_h * stretch[row_of_img[index]]);
      mosaic_pics.eq(index).parent().css({
        top: row_top[row_of_img[index]],
        left: img_left[row_of_img[index]]
      });
      img_left[row_of_img[index]] += thumb_w * stretch[row_of_img[index]] + 2 * border_and_padding;
      last_img_index[row_of_img[index]] = index;
    });
    jQuery("#" + block_id).height(row_top[row_number] + row_new_height[row_number] - row_top[0]);
  }
  else {
    var thumb_width = parseInt(container.attr('data-width'));
    /* Resizable mosaic.*/
    if (container.attr('data-resizable') == '1') {
      if (jQuery(window).width() >= 1920) {
        var thumbnail_width = (1 + jQuery(window).width() / 1920) * thumb_width;
      }
      else if (jQuery(window).width() <= 640) {
        var thumbnail_width = jQuery(window).width() / 640 * thumb_width;
      }
      else {
        var thumbnail_width = thumb_width;
      }
      /* Custom solution for 10web.io web site gallery plugin page mosaic view for 4 columns view */
      if( jQuery(".header-content-with_tab").length > 0 ) {
        var thumbnail_width = (jQuery(".header-content-with_tab").width()) / 4 - 10;
      }
    }
    else {
      var thumbnail_width = thumb_width;
    }
    /* Initialize.*/
    var mosaic_pics = jQuery(".bwg_mosaic_thumb_" + bwg);
    mosaic_pics.each(function (index) {
      jQuery(this).removeAttr('style');
      jQuery(this).parent().removeAttr('style');
      var thumb_w = jQuery(this).data('width');
      var thumb_h = jQuery(this).data('height');
      if(thumb_w == '' || thumb_h == '' || typeof thumb_w === 'undefined' || typeof thumb_h === 'undefined') {
        thumb_w = mosaic_pics.get(index).naturalWidth;
        thumb_h = mosaic_pics.get(index).naturalHeight;
      }
      mosaic_pics.eq(index).height(thumb_h * thumbnail_width / thumb_w);
      mosaic_pics.eq(index).width(thumbnail_width);
    });
    /* Resize.*/
    var divwidth = jQuery("#bwg_mosaic_thumbnails_div_" + bwg).width() / 100 * parseInt(container.attr('data-total-width'));
    /* Set absolute width of mosaic.*/
    jQuery('#' + block_id).width(divwidth);
    var col_width = thumbnail_width + 2 * border_and_padding < divwidth ? thumbnail_width : divwidth - 2 * border_and_padding;
    var col_number = Math.floor(divwidth / (col_width + 2 * border_and_padding));
    var col_of_img = [];
    /*column of the current image*/
    col_of_img[0] = 0;
    var imgs_by_cols = [];
    /*number of images in each column*/
    /*zero points*/
    var min_top = [];
    for (var x = 0; x < col_number; x++) {
      min_top[x] = 0;
      imgs_by_cols[x] = 0;
    }
    var img_wrap_left = 0;
    /*create masonry vertical*/
    mosaic_pics.each(function (index) {
      var col = 0;
      var min = min_top[0];
      for (var x = 0; x < col_number; x++) {
        if (min > min_top[x]) {
          min = min_top[x];
          col = x;
        }
      }
      col_of_img[index] = col;
      /*store in which col is arranged*/
      imgs_by_cols[col]++;
      img_container_top = min;
      img_container_left = img_wrap_left + col * (col_width + 2 * border_and_padding);
      mosaic_pics.eq(index).parent().css({top: img_container_top, left: img_container_left});
      min_top[col] += mosaic_pics.eq(index).height() + 2 * border_and_padding;
    });
    /*create mosaics*/
    var stretch = [];
    stretch[0] = 1;
    var sum_col_width = 0;
    var sum_col_height = [];
    /*array to store height of every column*/
    /*solve equations to calculate stretch[col] factors*/
    var axbx = 0;
    var axpxbx = 0;
    for (var x = 0; x < col_number; x++) {
      sum_col_width += col_width;
      sum_col_height[x] = 0;
      mosaic_pics.each(function (index) {
        if (col_of_img[index] == x) {
          sum_col_height[x] += mosaic_pics.eq(index).height();
        }
      });
      if (sum_col_height[x] != 0) {
        axbx += col_width / sum_col_height[x];
        axpxbx += col_width * imgs_by_cols[x] * 2 * border_and_padding / sum_col_height[x];
      }
    }
    var common_height = 0;
    if (axbx != 0) {
      common_height = (sum_col_width + axpxbx) / axbx;
    }
    for (var x = 0; x < col_number; x++) {
      if (sum_col_height[x] != 0) {
        stretch[x] = (common_height - imgs_by_cols[x] * 2 * border_and_padding) / sum_col_height[x];
      }
    }
    var img_container_left = [];
    /*position.left of every column*/
    img_container_left[0] = img_wrap_left;
    for (var x = 1; x <= col_number; x++) {
      img_container_left[x] = img_container_left[x - 1] + col_width * stretch[x - 1] + 2 * border_and_padding;
    }
    /*reset min_top array to the position.top of #wrap container*/
    var img_container_top = [];
    for (var x = 0; x < col_number; x++) {
      img_container_top[x] = 0;
    }
    /*stretch and shift to create mosaic verical*/
    var last_img_index = [];
    /* last image in column*/
    last_img_index[0] = 0;
    mosaic_pics.each(function (index) {
      var thumb_w = mosaic_pics.eq(index).width();
      var thumb_h = mosaic_pics.eq(index).height();
      mosaic_pics.eq(index).width(thumb_w * stretch[col_of_img[index]]);
      mosaic_pics.eq(index).height(thumb_h * stretch[col_of_img[index]]);
      mosaic_pics.eq(index).parent().css({
        top: img_container_top[col_of_img[index]],
        left: img_container_left[col_of_img[index]]
      });
      img_container_top[col_of_img[index]] += thumb_h * stretch[col_of_img[index]] + 2 * border_and_padding;
      last_img_index[col_of_img[index]] = index;
    });
    jQuery("#" + block_id).width(img_container_left[col_number]).height(img_container_top[0]);
  }
}

function bwg_thumbnail_mosaic(that) {
	var container = jQuery(that);
	var dfd = jQuery.Deferred();
	dfd.done( [bwg_thumbnail_mosaic_logic] )
		.done( function(container) {
			if ( container.data('mosaic-thumb-transition') != '1' ) {
				jQuery('.bwg_mosaic_thumb_spun_' + bwg).css({
					'transition': 'all 0.3s ease 0s',
					'-webkit-transition': 'all 0.3s ease 0s'
				});
			}
			/*IMPORTANT!*/
      var bwg = container.data('bwg');
			jQuery(".bwg_mosaic_thumbnails_" + bwg).css({visibility: 'visible'});
			jQuery(".tablenav-pages_" + bwg).css({visibility: 'visible'});
			bwg_container_loaded(bwg);
			jQuery(".bwg_mosaic_thumb_"+bwg).removeClass("bwg-hidden");
			jQuery("#bwg_mosaic_thumbnails_div_"+bwg).removeClass("bwg-hidden");
		});

	dfd.resolve(container);

  if (container.attr('data-image-title') == 'hover') {
    var padding_px = parseInt(container.attr('data-thumb-padding')) / 2;
    var border_px = parseInt(container.attr('data-thumb-border'));
    var border_and_padding = border_px + padding_px;
    bwg_mosaic_title_on_hover(container.data('bwg'), container, border_and_padding);
  }
  if (container.attr('data-ecommerce-icon') == 'hover') {
    jQuery(".bwg_mosaic_thumb_spun_" + bwg).on("mouseenter", function() {
      var img_w = jQuery(this).parents(".bwg-mosaic-thumb-span").children(".bwg_mosaic_thumb_" + bwg).width();
      var img_h = jQuery(this).parents(".bwg-mosaic-thumb-span").children(".bwg_mosaic_thumb_" + bwg).height();
      jQuery(this).children(".bwg_ecommerce_spun1_" + bwg).width(img_w);
      var title_w = jQuery(this).children(".bwg_ecommerce_spun1_" + bwg).width();
      var title_h = jQuery(this).children(".bwg_ecommerce_spun1_" + bwg).height();
      jQuery(this).children(".bwg_ecommerce_spun1_" + bwg).css({
        top: border_and_padding + 0.5 * img_h - 0.5 * title_h,
        left: border_and_padding +  0.5 * img_w - 0.5 * title_w,
        'opacity': 1
      });
    });
    jQuery(".bwg_mosaic_thumb_spun_" + bwg).on("mouseleave", function() {
      jQuery(this).children(".bwg_ecommerce_spun1_" + bwg).css({ top: 0, left: -10000,'opacity': 0,'padding': container.attr('data-title-margin')});
    });
  }
}

function bwg_mosaic_title_on_hover(bwg, container, border_and_padding) {
  jQuery(".bwg-mosaic-thumb-span").on("mouseenter", function() {
      var img_w = jQuery(this).children(".bwg_mosaic_thumb_" + bwg).width();
      jQuery(this).find(".bwg_mosaic_title_spun1_" + bwg).width(img_w);
      jQuery(this).find(".bwg_mosaic_title_spun1_" + bwg).css({
        'opacity': 1,
        'max-height' : 'calc(100% - ' + 2 * border_and_padding + 'px)',
        'overflow' : 'hidden'
      });
    });
    jQuery(".bwg-mosaic-thumb-span").on("mouseleave", function() {
      jQuery(this).find(".bwg_mosaic_title_spun1_" + bwg).css({
        'opacity' : 0,
        'padding' : container.attr('data-title-margin'),
        'max-height' : 'calc(100% - ' + 2 * border_and_padding + 'px)',
        'overflow' : 'hidden'
      });
    });
}

function bwg_mosaic_ajax(bwg, tot_cccount_mosaic_ajax) {
  var cccount_mosaic_ajax = 0;
  jQuery(".bwg_mosaic_thumb_spun_" + bwg + " img").on("load", function() {
    if (++cccount_mosaic_ajax >= tot_cccount_mosaic_ajax) {
      bwg_thumbnail_mosaic(jQuery('.bwg-mosaic-thumbnails[data-bwg=' + bwg + ']'));
    }
  });
  jQuery(".bwg_mosaic_thumb_spun_" + bwg + " img").on("error", function() {
    jQuery(this).height(100);
    jQuery(this).width(100);
    if (++cccount_mosaic_ajax >= tot_cccount_mosaic_ajax) {
      bwg_thumbnail_mosaic(jQuery('.bwg-mosaic-thumbnails[data-bwg=' + bwg + ']'));
    }
  });
}

function bwg_add_album() {
  var bwg_touch_flag = false;
  if ( bwg_objectsL10n.front_ajax != "1" ) {
    jQuery(document).off("click",".bwg-album").on("click", ".bwg-album", function () {
      if (!bwg_touch_flag) {
        var bwg = jQuery(this).attr("data-bwg");
        bwg_touch_flag = true;
        setTimeout(function () {
          bwg_touch_flag = false;
        }, 100);
        bwg_ajax('gal_front_form_' + bwg, bwg, jQuery(this).attr("data-container_id"), jQuery(this).attr("data-alb_gal_id"), jQuery(this).attr("data-album_gallery_id"), jQuery(this).attr("data-def_type"), '', jQuery(this).attr("data-title"));
        return false;
      }
    });
  }
  /* Add description more button event.*/
  jQuery( ".bwg_description_more" ).on("click", function () {
    if ( jQuery(this).hasClass("bwg_more") ) {
      jQuery(this).parent().find(".bwg_description_full").show();
      jQuery(this).addClass("bwg_hide").removeClass("bwg_more");
      jQuery(this).html(jQuery(this).data("hide-msg"));
    }
    else {
      jQuery(this).parent().find(".bwg_description_full").hide();
      jQuery(this).addClass("bwg_more").removeClass("bwg_hide");
      jQuery(this).html(jQuery(this).data("more-msg"));
    }
  });
}

function bwg_add_lightbox() {
  var bwg_touch_flag = false;
  jQuery(document).on("click", ".bwg_lightbox .bwg-item0, .bwg_lightbox .bwg_slide, .bwg_lightbox .bwg-carousel-image, .bwg_lightbox .bwg-title1" ,function ( event ) {
    event.stopPropagation();
    event.preventDefault();
    var that = jQuery(this).closest('a');
    if ( !bwg_touch_flag ) {
      bwg_touch_flag = true;
      setTimeout( function () {
        bwg_touch_flag = false;
      }, 100 );
      bwg_gallery_box( jQuery( that ).attr( "data-image-id" ), jQuery( that ).closest( '.bwg_container' ) );
      return false;
    }
  });

  jQuery( ".bwg_lightbox .bwg_ecommerce" ).on("click", function ( event ) {
    event.stopPropagation();
    if ( !bwg_touch_flag ) {
      bwg_touch_flag = true;
      setTimeout( function () {
        bwg_touch_flag = false;
      }, 100 );
      var image_id = jQuery( this ).closest( ".bwg_lightbox" ).attr( "data-image-id" );
      bwg_gallery_box( image_id, jQuery( this ).closest( '.bwg_container' ), true );
      return false;
    }
  });
}

function bwg_filter_by_tag(that) {
  var newTags = '';
  var curCont = jQuery(that).parent().parent();
  var current_view = curCont.find('.current_view').val();
  var form_id = curCont.find('.form_id').val();
  var cur_gal_id = curCont.find('.cur_gal_id').val();
  var album_gallery_id = curCont.find('.album_gallery_id').val();
  var type = curCont.find('.type').val();

  jQuery(that).parent().find('.opt.selected').each( function () {
    newTags = newTags + jQuery(that).text() + ',';
  });
  newTags = newTags.slice(0, -1);
  if ( newTags == '' ) {
    newTags = bwg_objectsL10n.bwg_select_tag;
  }

  jQuery(that).parent().find('.CaptionCont').attr('title', newTags);
  jQuery(that).parent().find('.CaptionCont .placeholder').html(newTags);

  jQuery('#bwg_tag_id_' + current_view).val(jQuery('#bwg_tag_id_' + cur_gal_id).val());
  bwg_select_tag(current_view, form_id, cur_gal_id, album_gallery_id, type, false);
}

function bwg_document_ready() {
  bwg_add_lightbox();
  jQuery(".bwg_container img").removeAttr('width').removeAttr('height');
  jQuery( 'div[id^="bwg_container1_"]' ).each( function () {
    var bwg_container = jQuery( this );
    if ( bwg_container.data( 'right-click-protection' ) ) {
		bwg_disable_right_click( bwg_container );
    }

    /* Add dashicon  to select container. */
    jQuery(".SumoSelect > .CaptionCont > label > i").addClass("bwg-icon-angle-down closed");
    var search_tags = bwg_container.find('.search_tags');
    if ( bwg_objectsL10n.front_ajax == "1" ) {
      if ( search_tags.length ) {
        for (var i = 0; i < search_tags[0].length; i++) {
          if ( typeof search_tags[0][i].attributes.selected === "undefined" ) {
            search_tags[0][i].selected = false;
          }
        }
      }
    }
    if ( search_tags.length ) {
      search_tags.SumoSelect({
        triggerChangeCombined: true,
        placeholder: bwg_objectsL10n.bwg_select_tag,
        search: true,
        searchText : bwg_objectsL10n.bwg_search,
        forceCustomRendering: true,
        noMatch: bwg_objectsL10n.bwg_tag_no_match,
        captionFormatAllSelected : bwg_objectsL10n.bwg_all_tags_selected,
        captionFormat: '{0} '+ bwg_objectsL10n.bwg_tags_selected,
        okCancelInMulti: true,
        locale: [bwg_objectsL10n.ok, bwg_objectsL10n.cancel, bwg_objectsL10n.select_all]
      } );
      search_tags.off("change").on("change", function () {
        bwg_filter_by_tag(this);
      });
    }

    var bwg_order = bwg_container.find('.bwg_order');
    if (bwg_order.length) {
      bwg_order.SumoSelect({
        triggerChangeCombined: true,
        forceCustomRendering: true,
      });
    }

    /* Show/Hide search_placeholder_title class container */
	jQuery(this).find('search_placeholder_title').hide();
    if (jQuery(this).find('.bwg_search_input').val() == '') {
      jQuery(this).find('search_placeholder_title').show();
    }

    /* Show search_placeholder_title class container on focusout and hide reset, search icons*/
    jQuery(".bwg_thumbnail .bwg_search_container_2").focusout(function (e) {
      if (jQuery(this).find('.bwg_search_input').val() == '') {
         jQuery(this).find('.search_placeholder_title').show();
         jQuery(this).find('.bwg_search_loupe_container1').addClass("bwg-hidden");
         jQuery(this).find('.bwg_search_reset_container').addClass("bwg-hidden");
      }
    });
  });

  /* Show No tags text if tags empty. */
  jQuery(".search_tags").on("sumo:opened", function () {
    if ( jQuery(this).parent().find('ul li').length == 0 ) {
      jQuery(".no-match").html(bwg_objectsL10n.bwg_tag_no_match);
      jQuery(".no-match").show();
    }
  });

  /* Change dashicon from up arrow to down arrow when select box is close. */
  jQuery('.bwg_thumbnail .SumoSelect').on('sumo:closed', function(){
    jQuery(this).find('label i').removeClass('bwg-icon-angle-up opened');
    jQuery(this).find('label i').addClass("bwg-icon-angle-down closed");
  });

  /* Change dashicon from down arrow to up arrow when select box is open. */
  jQuery('.bwg_thumbnail .SumoSelect').on('sumo:opened', function() {
    jQuery(this).find('label i').removeClass('bwg-icon-angle-down closed');
    jQuery(this).find('label i').addClass("bwg-icon-angle-up opened");
  });

  bwg_add_album();

  var bwg_hash = window.location.hash.substring( 1 );
  if ( bwg_hash ) {
    if ( bwg_hash.indexOf( "bwg" ) != "-1" ) {
      bwg_hash_array = bwg_hash.replace( "bwg", "" ).split( "/" );
      var bwg_container = jQuery( '.bwg_container' );

      if ( bwg_container ) {
        bwg_gallery_box( bwg_hash_array[1], bwg_container, false, bwg_hash_array[0] );
      }
    }
  }

  bwg_blog_style_ready();
  bwg_image_browser_ready();
  bwg_resize_search_line();
}

function bwg_clear_search_input (current_view) {
  if ( bwg_objectsL10n.front_ajax == "1" ) {
    var current_url = window.location.href;
    var redirect_url = bwg_remove_url_parameter("bwg_search_"+current_view,current_url, current_url);
    window.location.replace(redirect_url);
    return;
  }
  jQuery("#bwg_search_input_" + current_view).val('');
  jQuery("#bwg_search_container_1_" + current_view + " .bwg_search_loupe_container1").addClass("bwg-hidden");
  jQuery("#bwg_search_container_1_" + current_view + " .bwg_search_reset_container").addClass("bwg-hidden");
}

function bwg_check_search_input_enter(that, e) {
  if (  e.key == 'Enter' ) {
    jQuery(that).closest('.bwg_search_container_1').find('.bwg_search').trigger('click');
    return false;
  }
  return true;
}

/* Ajax call for filters and pagination.*/
function bwg_ajax(form_id, current_view, id, album_gallery_id, cur_album_id, type, srch_btn, title, sortByParam, load_more, description, scroll_to_top) {
  if ( bwg_objectsL10n.front_ajax == "1"  && load_more!==true ) {
    if (album_gallery_id === "back") {
      if (document.referrer.indexOf(window.location.host) == -1) {
        str = jQuery(location).attr('href');
        window.location.replace(str.substring(0,str.indexOf("type_0")));
        return;
      }
      else {
        window.history.back();
        return;
      }
    }
    var search_input_val = jQuery("#bwg_search_input_" + current_view).val();
    var current_url = window.location.href;
    var redirect_url = "";
    var filter_tag = jQuery("#bwg_tag_id_" + current_view).val();

    if ( current_url.substr(-1) == '#' ) {
      current_url = current_url.slice(0, -1);
    }
    if ( search_input_val !== "" &&  typeof search_input_val !== "undefined" ) {
      redirect_url = bwg_remove_url_parameter("page_number_" + current_view, current_url);
      redirect_url = bwg_add_url_parameter(redirect_url, "bwg_search_" + current_view, search_input_val);
      if ( redirect_url !== false ) {
        current_url = redirect_url;
      }
    }
    else {
      /* Delete search. */
      redirect_url = bwg_remove_url_parameter("bwg_search_" + current_view, current_url);
      if ( redirect_url !== false ) {
        current_url = redirect_url;
      }
    }
    if ( typeof sortByParam !== "undefined" && sortByParam !== "" ) {
      redirect_url = bwg_add_url_parameter(current_url, "sort_by_" + current_view, sortByParam);
      if ( redirect_url !== false ) {
        current_url = redirect_url;
      }
    }
    if ( typeof filter_tag !== "undefined" && filter_tag !== null && filter_tag.length > 0 ) {
      var tag_ides = "";
      var filter_tags = filter_tag.split(',');
      jQuery.each(filter_tags, function (key) {
        var flag = ",";
        if (key === filter_tags.length - 1) {
          flag = "";
        }
        tag_ides += filter_tags[key] + flag;
      });
      if ( tag_ides !== "" ) {
        redirect_url = bwg_add_url_parameter(current_url, "filter_tag_" + current_view, tag_ides);
        if (redirect_url !== false) {
          current_url = redirect_url;
        }
      }
    }
    else {
      redirect_url = bwg_remove_url_parameter("filter_tag_" + current_view, redirect_url);
      if ( redirect_url !== false ) {
        current_url = redirect_url;
      }
    }
    window.location.href = current_url;
    return;
  }
  /* Show loading.*/
  jQuery("#ajax_loading_" + current_view).removeClass('bwg-hidden');
  jQuery(".bwg_load_more_ajax_loading").css({top: jQuery('#bwg_container1_' + bwg).height() - jQuery(".bwg_load_more_ajax_loading").height() });
  /* Disable scroll to prevent bugs with load more.*/
  if (typeof bwg_scroll_load_action === "function") {
    jQuery(window).off("scroll", bwg_scroll_load_action);
  }

  /* To prevent bugs with filters.*/
  jQuery(".bwg_thumbnail .search_tags").off("sumo:closed");

  var ajax_url = jQuery('#' + form_id).data("ajax-url");

  var masonry_loaded = 0;
  var mosaic_loaded = 0;
  if (typeof load_more == "undefined") {
    var load_more = false;
  }
  var page_number = jQuery("#page_number_" + current_view).val();
  var search = jQuery("#bwg_search_input_" + current_view).val();
  var post_data = {};

  var breadcrumb_str = jQuery('#bwg_album_breadcrumb_' + current_view).val();
  if ( breadcrumb_str && load_more !== true ) { /* For album views.*/
    var breadcrumb = JSON.parse(breadcrumb_str);
    if ( album_gallery_id == 'back' ) {
      /* Remove last element of array.*/
      breadcrumb.splice(-1, 1);
      var last_el = breadcrumb.slice(-1)[0];
      album_gallery_id = last_el["id"];
      page_number = last_el["page"];
      post_data["action_" + current_view] = 'back';
    }
    else if ( load_more === 'numeric' || srch_btn ) {/* From numeric pagination.*/
      breadcrumb.splice(-1, 1);
      breadcrumb.push({ id: album_gallery_id, page: page_number, search: search });
    }
    else {
      breadcrumb.push({id: album_gallery_id, page: 1});
      page_number = 1;
    }
    post_data["bwg_album_breadcrumb_" + current_view] = JSON.stringify(breadcrumb);
  }

  /** Set values for elementor widget.*/
  post_data['gallery_type'] = jQuery('#' + form_id).data("gallery-type");
  post_data['gallery_id'] = jQuery('#' + form_id).data("gallery-id");
  post_data['tag'] = jQuery('#' + form_id).data("tag");
  post_data['album_id'] = jQuery('#' + form_id).data("album-id");
  post_data['theme_id'] = jQuery('#' + form_id).data("theme-id");

  post_data['shortcode_id'] = jQuery('#' + form_id).data("shortcode-id");
  post_data['bwg'] = current_view;
  post_data['current_url'] = encodeURI(jQuery('#bwg_container1_' + current_view).data("current-url"));

  if ( srch_btn ) { /* Start search. */
    page_number = 1;
  }
  if (typeof title == "undefined" || title == '') {
    var title = "";
  }
  if (typeof description == "undefined" || description == '') {
    var description = "";
  }
  if (typeof sortByParam == "undefined" || sortByParam == '') {
    var sortByParam = jQuery(".bwg_order_" + current_view).val();
  }
  if (typeof scroll_to_top == "undefined") {
    var scroll_to_top = true;
  }
  if (scroll_to_top == true) {
    jQuery("html, body").animate({scrollTop: jQuery('#' + form_id).offset().top - 150}, 500);
  }

  post_data["page_number_" + current_view] = page_number;
  post_data["bwg_load_more_" + current_view] = jQuery("#bwg_load_more_" + current_view).val();
  post_data["album_gallery_id_" + current_view] = album_gallery_id;
  post_data["type_" + current_view] = type;
  post_data["title_" + current_view] = title;
  post_data["description_" + current_view] = description;
  post_data["sortImagesByValue_" + current_view] = sortByParam;
  post_data["bwg_random_seed_" + current_view] = jQuery("#bwg_random_seed_" + current_view).val();

  if (jQuery("#bwg_search_input_" + current_view).length > 0) { /* Search box exists.*/
	  post_data["bwg_search_" + current_view] = jQuery("#bwg_search_input_" + current_view).val();
  }

  if ( typeof post_data["bwg_album_breadcrumb_" + current_view] != "undefined" ) {
    var breadcrumbObj = JSON.parse(post_data["bwg_album_breadcrumb_" + current_view]);
    jQuery.each(breadcrumbObj, function (index, value) {
      post_data["bwg_search_" + current_view] = '';
      if (album_gallery_id == value.id) {
        post_data["bwg_search_" + current_view] = value.search;
      }
    });
  }

  post_data["bwg_tag_id_" + id] = jQuery("#bwg_tag_id_" + id).val();

  /* Loading.*/
  jQuery("#ajax_loading_" + current_view).removeClass('bwg-hidden');
  jQuery(".bwg_load_more_ajax_loading").css({top: jQuery('#bwg_container1_' + bwg).height() - jQuery(".bwg_load_more_ajax_loading").height()});
  jQuery.ajax({
    type: "POST",
    url: ajax_url,
    data: post_data,
    success: function (data) {
      masonry_loaded = jQuery(data).find(".bwg_masonry_thumb_spun_" + current_view + " img").length;
      mosaic_loaded = jQuery(data).find(".bwg_mosaic_thumb_spun_" + current_view + " img").length;

      if ( load_more === true ) {
        /* Add next page images after current ones instead of change all container.*/
        if ( id == "bwg_thumbnails_mosaic_" + current_view ) {
          /* ToDo: Remove after changing mosaic view.*/
          jQuery('#' + id).append(jQuery(data).closest(".bwg-container-" + current_view).find("#" + id).html());
        }
        else if ( id == "bwg_album_compact_" + current_view ) {
          jQuery('#' + id).append(jQuery(data).closest(".bwg-album-thumbnails").html());
        }
        else if ( id == "bwg_thumbnails_masonry_" + current_view ) {
          jQuery('.bwg-container-temp' + current_view).append(jQuery(data).closest(".bwg-container-" + current_view).html());
        }
        else {
          jQuery('#' + id).append(jQuery(data).closest(".bwg-container-" + current_view).html());
        }
        /* Change load more button container withe new one.*/
        jQuery('.bwg_nav_cont_' + current_view).html(jQuery(data).closest('.bwg_nav_cont_' + current_view).html());
      }
      else {
        /* Change all container with new data.*/
        jQuery('#bwg_container3_' + current_view).html(data);
      }
    },
    complete: function() {
      /* To prevent conflict with lazy load.*/
      jQuery("div[id^='bwg_container1_'] img").each(function () {
        if (jQuery(this).attr("data-lazy-src") != undefined && jQuery(this).attr("data-lazy-src") != '') {
          jQuery(this).attr("src", jQuery(this).attr("data-lazy-src"));
        }
        else if (jQuery(this).attr("data-original") != undefined && jQuery(this).attr("data-original") != '') {
          jQuery(this).attr("src", jQuery(this).attr("data-original"));
        }
      });
      jQuery(".blog_style_image_buttons_conteiner_" + current_view).find(jQuery(".bwg_blog_style_img_" + current_view)).on("load", function() {
        jQuery(".bwg_blog_style_img_" + current_view).closest(jQuery(".blog_style_image_buttons_conteiner_" + current_view)).show();
      });
      jQuery("#bwg_tags_id_" + id).val(jQuery("#bwg_tag_id_" + id).val());

      if ( jQuery(".pagination-links_" + current_view).length ) {
        jQuery("html, body").animate({scrollTop: jQuery('#' + form_id).offset().top - 150}, 500);
      }
      /* For all views. */
      bwg_document_ready();
      bwg_carousel_ready();
      bwg_carousel_onload();
      bwg_slideshow_ready();
      bwg_mosaic_ajax(current_view, mosaic_loaded);
      var no_data = bwg_all_thumnails_loaded(".bwg-container-" + current_view);
      if ( no_data ) {
        /* If there where no data after after ajax request.*/
        bwg_container_loaded(current_view);
      }
      /* For Blog style view.*/
      jQuery(".blog_style_images_conteiner_" + current_view + " .bwg_embed_frame_16x9_" + current_view).each(function (e) {
        jQuery(this).width(jQuery(this).parent().width());
        jQuery(this).height(jQuery(this).width() * 0.5625);
      });
      jQuery(".blog_style_images_conteiner_" + current_view + " .bwg_embed_frame_instapost_" + current_view).each(function (e) {
        jQuery(this).width(jQuery(this).parent().width());
        /* 16 is 2*padding inside iframe */
        /* 96 is 2*padding(top) + 1*padding(bottom) + 40(footer) + 32(header) */
        jQuery(this).height((jQuery(this).width() - 16) * jQuery(this).attr('data-height') / jQuery(this).attr('data-width') + 96);
      });
      /* For Image browser view.*/
      jQuery('#bwg_embed_frame_16x9_' + current_view).width(jQuery('#bwg_embed_frame_16x9_' + current_view).parent().width());
      jQuery('#bwg_embed_frame_16x9_' + current_view).height(jQuery('#bwg_embed_frame_16x9_' + current_view).width() * 0.5625);
      jQuery('#bwg_embed_frame_instapost_' + current_view).width(jQuery('#bwg_embed_frame_16x9_' + current_view).parent().width());
      /* 16 is 2*padding inside iframe */
      /* 96 is 2*padding(top) + 1*padding(bottom) + 40(footer) + 32(header) */
      jQuery('.bwg_embed_frame_instapost_' + current_view).height((jQuery('.bwg_embed_frame_instapost_' + current_view).width() - 16) * jQuery('.bwg_embed_frame_instapost_' + current_view).attr('data-height') / jQuery('.bwg_embed_frame_instapost_' + current_view).attr('data-width') + 96);

      /* Return value to search input field. */
	    jQuery("#bwg_search_input_" + current_view).val(post_data["bwg_search_" + current_view]);

      if ( jQuery("#bwg_search_input_" + current_view).val() != '' ) {
        jQuery("#bwg_search_input_" + current_view).parent().find('.search_placeholder_title').hide();
        jQuery("#bwg_search_input_" + current_view).parent().parent().find('.bwg_search_reset_container').show();
        jQuery("#bwg_search_input_" + current_view).parent().parent().find('.bwg_search_loupe_container1').show();
      } else {
        jQuery("#bwg_search_input_" + current_view).parent().find('.search_placeholder_title').show();
      }
      var cur_gal_id = jQuery("#bwg_container2_"+current_view+" .cur_gal_id").val();
      jQuery('#bwg_tag_id_' + current_view).val(jQuery('#bwg_tag_id_'+cur_gal_id).val());
    }
  });

  return false;
}

function bwg_add_url_parameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
        return uri + separator + key + "=" + value;
    }
}

function bwg_remove_url_parameter(sParam , link)
{
    var url_split = link.split('?');
    var url = url_split[0]+'?';

    var window_loc_search = "";
    if(typeof url_split[1] !== "undefined"){
        window_loc_search = url_split[1];
    }
    if(window_loc_search === ""){
      return link;
    }
    var sPageURL = decodeURIComponent(window_loc_search),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] != sParam) {
            url = url + sParameterName[0] + '=' + sParameterName[1] + '&'
        }
    }
    return url.substring(0,url.length-1);
}

function bwg_select_tag(current_view, form_id, cur_gal_id, album_gallery_id, type, reset) {
  if ( reset ) {
    jQuery("#bwg_tag_id_" + cur_gal_id).val('');
  }
  bwg_ajax(form_id, current_view, cur_gal_id, album_gallery_id, '', type, 1, '');
}

function bwg_cube(tz, ntx, nty, nrx, nry, wrx, wry, current_image_class, next_image_class, direction, bwg) {
  var type_slideshow = false;
  var bwg_prefix = "";
  var bwg_transition_dur;
  if ( typeof bwg !== 'undefined' && bwg !== '' ) {
    type_slideshow = true;
    bwg_params[bwg]['bwg_trans_in_progress'] = true;
    bwg_prefix = "_"+bwg;
    bwg_transition_dur = bwg_params[bwg]['bwg_transition_duration'];
    var event_stack = bwg_params[bwg]['event_stack']
  } else {
    bwg_transition_dur = bwg_transition_duration;
  }
  /* If browser does not support 3d transforms/CSS transitions.*/
  if (!bwg_testBrowser_cssTransitions( bwg )) {
    return bwg_fallback(current_image_class, next_image_class, direction, bwg);
  }
  if (!bwg_testBrowser_cssTransforms3d( bwg )) {
    return bwg_fallback3d(current_image_class, next_image_class, direction, bwg);
  }
  if( !type_slideshow) { /* from lightbox */
      gallery_box_data['bwg_trans_in_progress'] = true;
      /* Set active thumbnail.*/
      jQuery(".bwg_filmstrip_thumbnail").removeClass("bwg_thumb_active").addClass("bwg_thumb_deactive");
      jQuery("#bwg_filmstrip_thumbnail_" + gallery_box_data['bwg_current_key']).removeClass("bwg_thumb_deactive").addClass("bwg_thumb_active");
      jQuery(".bwg_slide_bg").css('perspective', 1000);
  } else { /* from slideshow */
      /* Set active thumbnail.*/
      jQuery(".bwg_slideshow_filmstrip_thumbnail_"+bwg).removeClass("bwg_slideshow_thumb_active_"+bwg).addClass("bwg_slideshow_thumb_deactive_"+bwg);
      jQuery("#bwg_filmstrip_thumbnail_" + bwg_params[bwg]['bwg_current_key']+ "_"+bwg).removeClass("bwg_slideshow_thumb_deactive_"+bwg).addClass("bwg_slideshow_thumb_active_"+bwg);
      jQuery(".bwg_slideshow_dots_"+bwg).removeClass("bwg_slideshow_dots_active_"+bwg).addClass("bwg_slideshow_dots_deactive_"+bwg);
      jQuery("#bwg_dots_" + bwg_params[bwg]['bwg_current_key'] + "_" + bwg).removeClass("bwg_slideshow_dots_deactive_"+bwg).addClass("bwg_slideshow_dots_active_"+bwg);
      jQuery(".bwg_slide_bg_"+bwg).css('perspective', 1000);
  }
  jQuery(current_image_class).css({
    transform : 'translateZ(' + tz + 'px)',
    backfaceVisibility : 'hidden'
  });
  jQuery(next_image_class).css({
    opacity : 1,
    backfaceVisibility : 'hidden',
    transform : 'translateY(' + nty + 'px) translateX(' + ntx + 'px) rotateY('+ nry +'deg) rotateX('+ nrx +'deg)'
  });
  jQuery(".bwg_slider"+bwg_prefix).css({
    transform: 'translateZ(-' + tz + 'px)',
    transformStyle: 'preserve-3d'
  });
  /* Execution steps.*/
  setTimeout(function () {
    jQuery(".bwg_slider"+bwg_prefix).css({
      transition: 'all ' + bwg_transition_dur + 'ms ease-in-out',
      transform: 'translateZ(-' + tz + 'px) rotateX('+ wrx +'deg) rotateY('+ wry +'deg)'
    });
  }, 20);
  /* After transition.*/
  jQuery(".bwg_slider"+bwg_prefix).one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(bwg_after_trans));
  function bwg_after_trans() {
    jQuery(current_image_class).removeAttr('style');
    jQuery(next_image_class).removeAttr('style');
    jQuery(".bwg_slider"+bwg_prefix).removeAttr('style');
    jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
    jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
    jQuery(".bwg_image_info").show();

    jQuery(current_image_class).html('');

    if ( type_slideshow ) {  /*check if cube works from slideshow*/
      bwg_change_watermark_container( bwg );
      bwg_params[bwg]['bwg_trans_in_progress'] = false;
      var data = bwg_params[bwg]['data'];
      var event_stack = bwg_params[bwg]['event_stack'];
    }
    else {
      var data = '';
      gallery_box_data['bwg_trans_in_progress'] = false;
      var event_stack = gallery_box_data['event_stack'];
    }
    if (typeof event_stack !== 'undefined') {
      if (event_stack.length > 0) {
        var key = event_stack[0].split("-");
        event_stack.shift();
        bwg_change_image(key[0], key[1], data, true, bwg);
      }
    }
    bwg_change_watermark_container();
  }
  if (bwg_transition_dur == 0) {
    bwg_after_trans();
  }
}

function bwg_fade(current_image_class, next_image_class, direction, bwg) {
  var type_slideshow = false;
  var bwg_transition_dur;
  if( typeof bwg !== 'undefined' && bwg !== '' ) {
      type_slideshow = true;
      bwg_params[bwg]['bwg_trans_in_progress'] = true;
      bwg_transition_dur = bwg_params[bwg]['bwg_transition_duration'];
  } else {
      gallery_box_data['bwg_trans_in_progress'] = true;
      bwg_transition_dur = gallery_box_data['bwg_transition_duration'];
  }
  if(type_slideshow) {
      /* Set active thumbnail.*/
      jQuery(".bwg_slideshow_filmstrip_thumbnail_"+bwg).removeClass("bwg_slideshow_thumb_active_"+bwg).addClass("bwg_slideshow_thumb_deactive_"+bwg);
      jQuery("#bwg_filmstrip_thumbnail_" + bwg_params[bwg]['bwg_current_key'] + "_"+bwg).removeClass("bwg_slideshow_thumb_deactive_"+bwg).addClass("bwg_slideshow_thumb_active_"+bwg);
      jQuery(".bwg_slideshow_dots_"+bwg).removeClass("bwg_slideshow_dots_active_"+bwg).addClass("bwg_slideshow_dots_deactive_"+bwg);
      jQuery("#bwg_dots_" + bwg_params[bwg]['bwg_current_key'] + "_"+bwg).removeClass("bwg_slideshow_dots_deactive_"+bwg).addClass("bwg_slideshow_dots_active_"+bwg);
  } else {
      /* Set active thumbnail.*/
      jQuery(".bwg_filmstrip_thumbnail").removeClass("bwg_thumb_active").addClass("bwg_thumb_deactive");
      jQuery("#bwg_filmstrip_thumbnail_" + gallery_box_data['bwg_current_key']).removeClass("bwg_thumb_deactive").addClass("bwg_thumb_active");
  }
  function bwg_after_trans() {
    jQuery(".bwg_image_info").show();
    bwg_change_watermark_container( bwg );
    if ( type_slideshow ) {
      bwg_params[bwg]['bwg_trans_in_progress'] = false;
    }
    else {
      gallery_box_data['bwg_trans_in_progress'] = false;
    }
  }
  if (bwg_testBrowser_cssTransitions()) {
      jQuery(next_image_class).css('transition', 'opacity ' + bwg_transition_dur + 'ms linear');
      jQuery(current_image_class).css('transition', 'opacity ' + bwg_transition_dur + 'ms linear');
      jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
      jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
      jQuery(next_image_class).one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(bwg_after_trans));
  }
  else {
      jQuery(current_image_class).animate({'opacity' : 0, 'z-index' : 1}, bwg_transition_dur);
      jQuery(next_image_class).animate({
          'opacity' : 1,
          'z-index': 2
      }, {
          duration: bwg_transition_dur,
          complete: function () {
            if( type_slideshow ) {
              bwg_params[bwg]['bwg_trans_in_progress'] = false;
            } else {
              gallery_box_data['bwg_trans_in_progress'] = false;
            }
            jQuery(current_image_class).html('');
              bwg_after_trans();
          }
      });
      /* For IE.*/
      jQuery(current_image_class).fadeTo(bwg_transition_dur, 0);
      jQuery(next_image_class).fadeTo(bwg_transition_dur, 1);
  }
  if (bwg_transition_dur == 0) {
      bwg_after_trans();
    }
}

/* Set watermark container size.*/
function bwg_change_watermark_container( bwg ) {
  var defix = ( typeof bwg !== 'undefined' && bwg !== '' ) ? '_'+bwg : '';
  jQuery(".bwg_slider"+defix).children().each(function() {
    if (jQuery(this).css("zIndex") == 2) {
      /* For images.*/
      var bwg_current_image_span = jQuery(this).find("img");
      if (bwg_current_image_span.length) {
        if (bwg_current_image_span.prop('complete')) {
          var width = bwg_current_image_span.width();
          var height = bwg_current_image_span.height();
          bwg_change_each_watermark_container(width, height, bwg);
        }
        else {
          bwg_current_image_span.on("load", function () {
            var width = bwg_current_image_span.width();
            var height = bwg_current_image_span.height();
            bwg_change_each_watermark_container(width, height, bwg);
          });
        }
      }
      else {
        /* For embeds and videos.*/
        bwg_current_image_span = jQuery(this).find("iframe");
        if (!bwg_current_image_span.length) {
          bwg_current_image_span = jQuery(this).find("video");
        }
        var width = bwg_current_image_span.width();
        var height = bwg_current_image_span.height();
        bwg_change_each_watermark_container(width, height, bwg);
      }
    }
  });
}

/* Set each watermark container size.*/
function bwg_change_each_watermark_container(width, height, bwg) {

  var defix = ( typeof bwg !== 'undefined' && bwg !== '' ) ? '_'+bwg : '';
  var source = ( typeof bwg !== 'undefined' && bwg !== '' ) ? '_slideshow' : '';

  jQuery(".bwg"+source+"_watermark_spun" + defix).width(width);
  jQuery(".bwg"+source+"_watermark_spun" + defix).height(height);
  jQuery(".bwg"+source+"_watermark" + defix).css({display: ''});

  if( typeof bwg === 'undefined' || bwg === '' ) {
      /* Set watermark image size.*/
      var comment_container_width = 0;
      if (jQuery(".bwg_comment_container").hasClass("bwg_open") || jQuery(".bwg_ecommerce_container").hasClass("bwg_open")) {
        comment_container_width = gallery_box_data['lightbox_comment_width'];
      }
      if (width <= (jQuery(window).width() - comment_container_width)) {
        jQuery(".bwg_watermark_image").css({
          width: ((jQuery(".spider_popup_wrap").width() - comment_container_width) * gallery_box_data['watermark_font_size'] / gallery_box_data['image_width'])
        });
        jQuery(".bwg_watermark_text, .bwg_watermark_text:hover").css({
          fontSize: ((jQuery(".spider_popup_wrap").width() - comment_container_width) * gallery_box_data['watermark_font_size'] / gallery_box_data['image_width'])
        });
      }
  } else {
    jQuery(".bwg" + source + "_title_spun" + defix).width(width);
    jQuery(".bwg" + source + "_title_spun" + defix).height(height);
    jQuery(".bwg" + source + "_description_spun" + defix).width(width);
    jQuery(".bwg" + source + "_description_spun" + defix).height(height);
  }
  if (jQuery.trim(jQuery(".bwg"+source+"_title_text" + defix).text())) {
    jQuery(".bwg_slideshow_title_text" + defix).css({display: ''});
  }
  if (jQuery.trim(jQuery(".bwg"+source+"_description_text" + defix).text())) {
    jQuery(".bwg"+source+"_description_text" + defix).css({display: ''});
  }

}

/* Set filmstrip initial position.*/
function bwg_set_filmstrip_pos( filmStripWidth, bwg, data ) {
  var defix = ( typeof bwg !== 'undefined' && bwg !== '' ) ? '_'+bwg : '';
  var source = ( typeof bwg !== 'undefined' && bwg !== '' ) ? '_slideshow' : '';

  var left_or_top;
  if ( typeof bwg !== 'undefined' && bwg !== '' ) {
    left_or_top = bwg_params[bwg]['left_or_top'];
  }
  else {
    left_or_top = gallery_box_data['left_or_top'];
  }
  var top_bottom_space = parseInt(jQuery(".bwg_filmstrip_thumbnails").attr('data-all-images-top-bottom-space'));
  var right_left_space = parseInt(jQuery(".bwg_filmstrip_thumbnails").attr('data-all-images-right-left-space'));
  if( typeof bwg === 'undefined' || bwg === '' ) {  /* for lightbox */
      if ( gallery_box_data['outerWidth_or_outerHeight'] == 'outerWidth' ) {
        var selectedImagePos = -bwg_current_filmstrip_pos - (jQuery(".bwg_filmstrip_thumbnail").outerWidth(true)) / 2;
      } else if ( gallery_box_data['outerWidth_or_outerHeight'] == 'outerHeight' ) {
        var selectedImagePos = -bwg_current_filmstrip_pos - (jQuery(".bwg_filmstrip_thumbnail").outerHeight(true)) / 2;
      }
      if ( gallery_box_data['width_or_height'] == 'width' ) {
        var imagesContainerLeft = Math.min(0, Math.max(filmStripWidth - jQuery(".bwg_filmstrip_thumbnails").width(), selectedImagePos + filmStripWidth / 2));
      } else if (gallery_box_data['width_or_height'] == 'height') {
        var imagesContainerLeft = Math.min(0, Math.max(filmStripWidth - jQuery(".bwg_filmstrip_thumbnails").height(), selectedImagePos + filmStripWidth / 2));
      }
  } else { /* for slideshow */
      if (bwg_params[bwg]['width_or_height'] == 'width') {
        var selectedImagePos = -bwg_params[bwg]['bwg_current_filmstrip_pos'] - (jQuery(".bwg_slideshow_filmstrip_thumbnail" + defix).width() + bwg_params[bwg]['filmstrip_thumb_margin_hor']) / 2;
        var imagesContainerLeft = Math.min(0, Math.max(filmStripWidth - jQuery(".bwg_slideshow_filmstrip_thumbnails" + defix).width(), selectedImagePos + filmStripWidth / 2));
      }
      else {
        var selectedImagePos = -bwg_params[bwg]['bwg_current_filmstrip_pos'] - (jQuery(".bwg_slideshow_filmstrip_thumbnail" + defix).height() + bwg_params[bwg]['filmstrip_thumb_margin_hor']) / 2;
        var imagesContainerLeft = Math.min(0, Math.max(filmStripWidth - jQuery(".bwg_slideshow_filmstrip_thumbnails" + defix).height(), selectedImagePos + filmStripWidth / 2));
      }
  }

	if ( imagesContainerLeft + right_left_space > 0 ) {
		right_left_space = 0;
	}
	if ( imagesContainerLeft + top_bottom_space > 0 ) {
		top_bottom_space = 0;
	}

  if( left_or_top == 'left' ) {
    jQuery(".bwg"+source+"_filmstrip_thumbnails" + defix).animate({
      left: imagesContainerLeft + right_left_space
    }, {
      duration: 500,
      complete: function () { bwg_filmstrip_arrows( bwg ); }
    });
  } else {
    jQuery(".bwg"+source+"_filmstrip_thumbnails" + defix).animate({
      top: imagesContainerLeft + top_bottom_space
    }, {
      duration: 500,
      complete: function () { bwg_filmstrip_arrows( bwg ); }
    });
  }
}

/* Show/hide filmstrip arrows.*/
function bwg_filmstrip_arrows( bwg ) {
  var defix = ( typeof bwg !== 'undefined' && bwg !== '' ) ? '_'+bwg : '';
  var source = ( typeof bwg !== 'undefined' && bwg !== '' ) ? '_slideshow' : '';
  var width_or_height = ( typeof bwg !== 'undefined' && bwg !== '' ) ? bwg_params[bwg]['width_or_heigh'] : gallery_box_data['width_or_height'];

  if ( width_or_height == 'width' ){
    var condition1 = jQuery(".bwg"+source+"_filmstrip_thumbnails"+defix).width();
    var condition2 = jQuery(".bwg"+source+"_filmstrip"+defix).width();
  } else {
    var condition1 = jQuery(".bwg"+source+"_filmstrip_thumbnails"+defix).height();
    var condition2 = jQuery(".bwg"+source+"_filmstrip"+defix).height();
  }
  if (condition1 < condition2) {
    jQuery(".bwg"+source+"_filmstrip_left" + defix).hide();
    jQuery(".bwg"+source+"_filmstrip_right" + defix).hide();
  }
  else {
    jQuery(".bwg"+source+"_filmstrip_left" + defix).show();
    jQuery(".bwg"+source+"_filmstrip_right" + defix).show();
  }
}

function bwg_move_filmstrip( bwg ) {
  var bwg_filmstrip_width;
  var bwg_filmstrip_thumbnails_width;
  var image_left;
  var image_right;
  var long_filmstrip_cont_left;
  var long_filmstrip_cont_right;

  var defix = ( typeof bwg !== 'undefined' && bwg !== '' ) ? '_'+bwg : '';
  var source = ( typeof bwg !== 'undefined' && bwg !== '' ) ? '_slideshow' : '';
  var outerWidth_or_outerHeight = ( typeof bwg !== 'undefined' && bwg !== '' ) ? bwg_params[bwg]['outerWidth_or_outerHeight'] : gallery_box_data['outerWidth_or_outerHeight'];
  var left_or_top = ( typeof bwg !== 'undefined' && bwg !== '' ) ? bwg_params[bwg]['left_or_top'] : gallery_box_data['left_or_top'];

  if(outerWidth_or_outerHeight == 'outerWidth') {
    bwg_filmstrip_width = jQuery(".bwg" + source + "_filmstrip" + defix).outerWidth(true);
    bwg_filmstrip_thumbnails_width = jQuery(".bwg" + source + "_filmstrip_thumbnails" + defix).outerWidth(true);
  } else {
    bwg_filmstrip_width = jQuery(".bwg" + source + "_filmstrip" + defix).outerHeight(true);
    bwg_filmstrip_thumbnails_width = jQuery(".bwg" + source + "_filmstrip_thumbnails" + defix).outerHeight(true);
  }
  if( left_or_top == 'left' ) {
    image_left = jQuery(".bwg" + source + "_thumb_active" + defix).position().left;
    if( outerWidth_or_outerHeight == 'outerWidth' ) {
      image_right = jQuery(".bwg" + source + "_thumb_active" + defix).position().left + jQuery(".bwg" + source + "_thumb_active" + defix).outerWidth(true);
    } else {
      image_right = jQuery(".bwg" + source + "_thumb_active" + defix).position().left + jQuery(".bwg" + source + "_thumb_active" + defix).outerHeight(true);
    }
    long_filmstrip_cont_left = jQuery(".bwg" + source + "_filmstrip_thumbnails" + defix).position().left;
    long_filmstrip_cont_right = Math.abs(jQuery(".bwg" + source + "_filmstrip_thumbnails" + defix).position().left) + bwg_filmstrip_width;
  } else {
    image_left = jQuery(".bwg" + source + "_thumb_active" + defix).position().top;
    if( outerWidth_or_outerHeight == 'outerWidth' ) {
      image_right = jQuery(".bwg" + source + "_thumb_active" + defix).position().top + jQuery(".bwg" + source + "_thumb_active" + defix).outerWidth(true);
    } else {
      image_right = jQuery(".bwg" + source + "_thumb_active" + defix).position().top + jQuery(".bwg" + source + "_thumb_active" + defix).outerHeight(true);
    }
    long_filmstrip_cont_left = jQuery(".bwg" + source + "_filmstrip_thumbnails" + defix).position().top;
    long_filmstrip_cont_right = Math.abs(jQuery(".bwg" + source + "_filmstrip_thumbnails" + defix).position().top) + bwg_filmstrip_width;
  }
  if (bwg_filmstrip_width > bwg_filmstrip_thumbnails_width) {
    return;
  }
  if (image_left < Math.abs(long_filmstrip_cont_left)) {
    if ( left_or_top == 'left' ) {
      jQuery(".bwg" + source + "_filmstrip_thumbnails" + defix).animate({
        left: -image_left
      }, {
        duration: 500,
        complete: function () { bwg_filmstrip_arrows( bwg ); }
      });
    } else {
      jQuery(".bwg" + source + "_filmstrip_thumbnails" + defix).animate({
        top: -image_left
      }, {
        duration: 500,
        complete: function () { bwg_filmstrip_arrows( bwg ); }
      });
    }
  }
  else if (image_right > long_filmstrip_cont_right) {
    if ( left_or_top == 'left' ) {
      jQuery(".bwg" + source + "_filmstrip_thumbnails" + defix).animate({
        left: -(image_right - bwg_filmstrip_width)
      }, {
        duration: 500,
        complete: function () {
          bwg_filmstrip_arrows(bwg);
        }
      });
    } else {
      jQuery(".bwg" + source + "_filmstrip_thumbnails" + defix).animate({
        top: -(image_right - bwg_filmstrip_width)
      }, {
        duration: 500,
        complete: function () {
          bwg_filmstrip_arrows(bwg);
        }
      });
    }
  }
}

function bwg_move_dots( bwg ) {
  var image_left = jQuery(".bwg_slideshow_dots_active_" + bwg).position().left;
  var image_right = jQuery(".bwg_slideshow_dots_active_" + bwg).position().left + jQuery(".bwg_slideshow_dots_active_" + bwg).outerWidth(true);
  var bwg_dots_width = jQuery(".bwg_slideshow_dots_container_" + bwg).outerWidth(true);
  var bwg_dots_thumbnails_width = jQuery(".bwg_slideshow_dots_thumbnails_" + bwg).outerWidth(false);
  var long_filmstrip_cont_left = jQuery(".bwg_slideshow_dots_thumbnails_" + bwg).position().left;
  var long_filmstrip_cont_right = Math.abs(jQuery(".bwg_slideshow_dots_thumbnails_" + bwg).position().left) + bwg_dots_width;
  if (bwg_dots_width > bwg_dots_thumbnails_width) {
    return;
  }
  if (image_left < Math.abs(long_filmstrip_cont_left)) {
    jQuery(".bwg_slideshow_dots_thumbnails_" + bwg).animate({
      left: -image_left
    }, {
      duration: 500,
      complete: function () {  }
    });
  }
  else if (image_right > long_filmstrip_cont_right) {
    jQuery(".bwg_slideshow_dots_thumbnails_" + bwg).animate({
      left: -(image_right - bwg_dots_width)
    }, {
      duration: 500,
      complete: function () {  }
    });
  }
}

function bwg_testBrowser_cssTransitions( bwg ) {
  return bwg_testDom('Transition', bwg);
}

function bwg_testBrowser_cssTransforms3d( bwg ) {
  return bwg_testDom('Perspective', bwg);
}

function bwg_testDom(prop, bwg) {
  /* Browser vendor CSS prefixes.*/
  var browserVendors = ['', '-webkit-', '-moz-', '-ms-', '-o-', '-khtml-'];
  /* Browser vendor DOM prefixes.*/
  var domPrefixes = ['', 'Webkit', 'Moz', 'ms', 'O', 'Khtml'];
  var i = domPrefixes.length;
  while (i--) {
    if (typeof document.body.style[domPrefixes[i] + prop] !== 'undefined') {
      return true;
    }
  }
  return false;
}

/* For browsers that does not support transitions.*/
function bwg_fallback(current_image_class, next_image_class, direction, bwg) {
  bwg_fade(current_image_class, next_image_class, direction, bwg);
}

/* For browsers that support transitions, but not 3d transforms (only used if primary transition makes use of 3d-transforms).*/
function bwg_fallback3d(current_image_class, next_image_class, direction, bwg) {
  bwg_sliceV(current_image_class, next_image_class, direction, bwg);
}

function bwg_none(current_image_class, next_image_class, direction, bwg) {

  var defix = ( typeof bwg !== 'undefined' && bwg !== '' ) ? '_'+bwg : '';

  jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
  jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});

  if ( typeof bwg !== 'undefined' && bwg !== '' ) {
    var bwg_current_key = bwg_params[bwg]['bwg_current_key'];
    bwg_change_watermark_container(bwg);
    /* Set active thumbnail.*/
    jQuery(".bwg_slideshow_filmstrip_thumbnail" + defix).removeClass("bwg_slideshow_thumb_active" + defix).addClass("bwg_slideshow_thumb_deactive" + defix);
    jQuery("#bwg_filmstrip_thumbnail_" + bwg_current_key + defix).removeClass("bwg_slideshow_thumb_deactive" + defix).addClass("bwg_slideshow_thumb_active" + defix);
    jQuery(".bwg_slideshow_dots" + defix).removeClass("bwg_slideshow_dots_active" + defix).addClass("bwg_slideshow_dots_deactive" + defix);
    jQuery("#bwg_dots_" + bwg_current_key + defix).removeClass("bwg_slideshow_dots_deactive" + defix).addClass("bwg_slideshow_dots_active" + defix);
  } else {
    /* Lightbox */
    jQuery(".bwg_image_info").show();
    gallery_box_data['bwg_trans_in_progress'] = false;
    jQuery(current_image_class).html('');
    bwg_change_watermark_container();
  }


}

function bwg_iterator( bwg ) {
  var iterator = 1;
  if ( typeof bwg !== 'undefined' && bwg !== '' && typeof bwg_params[bwg] != 'undefined' && bwg_params[bwg]['enable_slideshow_shuffle'] == 1 ) {
    iterator = Math.floor((bwg_params[bwg]['data'].length - 1) * Math.random() + 1);
  }
  return iterator;
}

function bwg_change_image_slideshow(current_key, key, data, from_effect, bwg) {
  var data = bwg_params[bwg]['data'];
  /* Pause videos.*/
  jQuery("#bwg_slideshow_image_container_" + bwg).find("iframe").each(function () {
    jQuery(this)[0].contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
    jQuery(this)[0].contentWindow.postMessage('{ "method": "pause" }', "*");
    jQuery(this)[0].contentWindow.postMessage('pause', '*');
  });
  /* Pause videos facebook video.*/
  jQuery('#image_id_' +bwg + '_' + data[current_key]["id"]).find('.bwg_fb_video').each(function () {
    jQuery(this).attr('src', jQuery(this).attr('src'));
  });
  if (data[key]) {
    if (jQuery('.bwg_ctrl_btn_' + bwg).hasClass('bwg-icon-pause')) {
      bwg_play( bwg_params[bwg]['data'], bwg );
    }

    if (!from_effect) {
      /* Change image key.*/
      jQuery("#bwg_current_image_key_" + bwg).val(key);
      if (current_key == '-1') { /* Filmstrip.*/
        current_key = jQuery(".bwg_slideshow_thumb_active_" + bwg).children("img").attr("image_key");
      }
      else if (current_key == '-2') { /* Dots.*/
        current_key = jQuery(".bwg_slideshow_dots_active_" + bwg).attr("image_key");
      }
    }
    if ( bwg_params[bwg]['bwg_trans_in_progress'] ) {
      bwg_params[bwg]['event_stack'].push(current_key + '-' + key);
      return;
    }
    var direction = 'right';
    if (current_key > key) {
      var direction = 'left';
    } else if (current_key == key) {
      return;
    }

    jQuery(".bwg_slideshow_watermark_" + bwg).css({display: 'none'});
    jQuery(".bwg_slideshow_title_text_" + bwg).css({display: 'none'});
    jQuery(".bwg_slideshow_description_text_" + bwg).css({display: 'none'});
    /* Set active thumbnail position.*/
    if ( bwg_params[bwg]['width_or_height'] == 'width' ) {
      bwg_params[bwg]['bwg_current_filmstrip_pos'] = key * (jQuery(".bwg_slideshow_filmstrip_thumbnail_" + bwg).width() + 2 + 2 * bwg_params[bwg]['lightbox_filmstrip_thumb_border_width']);
    } else {
      bwg_params[bwg]['bwg_current_filmstrip_pos'] = key * (jQuery(".bwg_slideshow_filmstrip_thumbnail_" + bwg).height() + 2 + 2 * bwg_params[bwg]['lightbox_filmstrip_thumb_border_width']);
    }

    current_key = key;
    bwg_params[bwg]['bwg_current_key'] = current_key;
    /* Change image id, title, description.*/
    jQuery("#bwg_slideshow_image_" + bwg).attr('image_id', data[key]["id"]);
    jQuery(".bwg_slideshow_title_text_" + bwg).html(jQuery('<span style="display: block;" />').html(data[key]["alt"]).text());
    jQuery(".bwg_slideshow_description_text_" + bwg).html(jQuery('<span style="display: block;" />').html(data[key]["description"]).text());
    var current_image_class = jQuery(".bwg_slideshow_image_spun_" + bwg).css("zIndex") == 2 ? ".bwg_slideshow_image_spun_" + bwg : ".bwg_slideshow_image_second_spun_" + bwg;
    var next_image_class = current_image_class == ".bwg_slideshow_image_second_spun_" + bwg ? ".bwg_slideshow_image_spun_" + bwg : ".bwg_slideshow_image_second_spun_" + bwg;
    var is_embed = data[key]['filetype'].indexOf("EMBED_") > -1 ? true : false;
    var is_embed_instagram_post = data[key]['filetype'].indexOf('INSTAGRAM_POST') > -1 ? true :false;
    var is_embed_instagram_video = data[key]['filetype'].indexOf('INSTAGRAM_VIDEO') > -1 ? true :false;
    var cur_height = jQuery(current_image_class).height();
    var cur_width = jQuery(current_image_class).width();
    var innhtml = '<span class="bwg_slideshow_image_spun1_' + bwg +'" style="display:  ' + (!is_embed ? 'table' : 'block') + ' ;width: inherit; height: inherit;"><span class="bwg_slideshow_image_spun2_' + bwg + '" style="display: ' + (!is_embed ? 'table-cell' : 'block') + '; vertical-align: middle; text-align: center; ">';
    if (!is_embed) {
      if (bwg_params[bwg]['thumb_click_action'] != 'do_nothing' ) {
        var argument = '';
        if (bwg_params[bwg]['thumb_click_action'] == 'open_lightbox')
        {
          argument += ' class="bwg_lightbox" data-image-id="' + data[key]["id"] + '"';
        } else {
          if ( bwg_params[bwg]["thumb_click_action"] == "redirect_to_url" && data[key]["redirect_url"] ) {
            argument += 'href="' + data[key]["redirect_url"] + '"' + ( (bwg_params[bwg]['thumb_link_target'] && bwg_params[bwg]['thumb_link_target'] == 1) ? ' target="_blank"' : '' );
          }
        }
        innhtml += '<a ' + argument + '>';
      }
      innhtml += '<img style="max-height: ' + cur_height + 'px !important; max-width: ' + cur_width + 'px !important; display:inline-block;" ';
      innhtml +=   ' class="bwg_slide bwg_slideshow_image_'+bwg+'" ';
      innhtml +=   ' id="bwg_slideshow_image_' + bwg + '" ';
      innhtml +=   ' src="' + bwg_params[bwg]['upload_url'] + jQuery("<span style=\'display: block;\' />").html(decodeURIComponent(data[key]["image_url"])).text() + '" alt="' + data[key]["alt"] + '" image_id="' + data[key]["id"] + '" /></a>';
    } else { /*is_embed*/
      innhtml += '<span style="height: ' + cur_height + 'px; width: ' + cur_width + 'px;" class="bwg_popup_embed bwg_popup_watermark">';
      if (is_embed_instagram_video ) {
        innhtml += '<span class="bwg_inst_play_btn_cont" onclick="bwg_play_instagram_video(this)"><span class="bwg_inst_play"></span></span>';
      }
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
        innhtml += spider_display_embed(data[key]['filetype'], decodeURIComponent(data[key]['image_url']), data[key]['filename'], {class:"bwg_embed_frame", 'data-width': data[key]['image_width'], 'data-height': data[key]['image_height'], frameborder: "0", allowfullscreen: "allowfullscreen", style: "width:" + post_width + "px; height:" + post_height + "px; vertical-align:middle; display:inline-block; position:relative;"});
      } else {
        innhtml += spider_display_embed(data[key]['filetype'], decodeURIComponent(data[key]['image_url']), data[key]['filename'], {class:"bwg_embed_frame", frameborder:"0", allowfullscreen:"allowfullscreen", style:"width:inherit; height:inherit; vertical-align:middle; display:table-cell;" });
      }
      innhtml += "</span>";
    }
    innhtml += '</span></span>';
    jQuery(next_image_class).html(innhtml);
    if (bwg_params[bwg]['preload_images']) {
      bwg_preload_images(key, bwg);
    }
    window["bwg_" + bwg_params[bwg]['slideshow_effect']](current_image_class, next_image_class, direction, bwg);
    if ( bwg_params[bwg]['enable_slideshow_filmstrip'] > 0 ) {
      bwg_move_filmstrip( bwg );
    }
    else {
      bwg_move_dots( bwg);
    }
    if (data[key]["is_embed_video"]) {
      jQuery("#bwg_slideshow_play_pause_" + bwg).css({display: 'none'});
    }
    else {
      jQuery("#bwg_slideshow_play_pause_" + bwg).css({display: ''});
    }
  }
  bwg_add_lightbox();
}

function bwg_preload_images_slideshow( key, bwg ) {
  var data = bwg_params[bwg]['data'];
  count = bwg_params[bwg]['preload_images_count'] / 2;
  var count_all = data.length;
  if (count_all < bwg_params[bwg]['preload_images_count']) {
    count = 0;
  }
  if (count != 0) {
    for (var i = key - count; i < key + count; i++) {
      var index = parseInt((i + count_all) % count_all);
      var is_embed = data[index]['filetype'].indexOf("EMBED_") > -1 ? true : false;
      if (typeof data[index] != "undefined") {
        if (!is_embed) {
          jQuery("<img/>").attr("src", bwg_params[bwg]['upload_url'] + jQuery('<span style="display: block;" />').html(decodeURIComponent(data[index]["image_url"])).text());
        }
      }
    }
  } else {
    for (var i = 0; i < data.length; i++) {
      var is_embed = data[i]['filetype'].indexOf("EMBED_") > -1 ? true : false;
      if (typeof data[i] != "undefined") {
        if (!is_embed) {
          jQuery("<img/>").attr("src", bwg_params[bwg]['upload_url'] + jQuery('<span style="display: block;" />').html(decodeURIComponent(data[i]["image_url"])).text());
        }
      }
    }
  }
}

function bwg_preload_images( key, bwg ) {
  if ( typeof bwg !== 'undefined' && bwg !== '' ) { /* SLIDESHOW */
    bwg_preload_images_slideshow( key, bwg );
  } else {  /* LIGHTBOX */
    bwg_preload_images_lightbox( key );
  }
}

function bwg_popup_resize_slidshow( bwg ) {
  var parentt = jQuery("#bwg_container1_" + bwg).parent();
  /* Trick to set parent's width to elementor tab. */
  if (parentt.hasClass('elementor-tab-content')) {
    parentt.width(parentt.closest('.elementor-widget-wrap').width());
  }
  var parent_width = parentt.width();
  var data = bwg_params[bwg]['data'];
  if (parent_width >= bwg_params[bwg]['image_width']) {
    jQuery(".bwg_slideshow_image_wrap_"+bwg).css({width: bwg_params[bwg]['image_width']});
    jQuery(".bwg_slideshow_image_wrap_"+bwg).css({height: bwg_params[bwg]['image_height']});
    jQuery(".bwg_slideshow_image_container_"+bwg).css({width: (bwg_params[bwg]['filmstrip_direction'] == 'horizontal') ? bwg_params[bwg]['image_width'] : (bwg_params[bwg]['image_width'] - bwg_params[bwg]['slideshow_filmstrip_width']) });
    jQuery(".bwg_slideshow_image_container_"+bwg).css({height: (bwg_params[bwg]['filmstrip_direction'] == 'horizontal') ? bwg_params[bwg]['image_height'] - bwg_params[bwg]['slideshow_filmstrip_height'] : bwg_params[bwg]['image_height'] });
    jQuery(".bwg_slideshow_image_"+bwg).css({
      cssText: "max-width: " + (bwg_params[bwg]['filmstrip_direction'] == 'horizontal ') ? bwg_params[bwg]['image_width'] : (bwg_params[bwg]['image_width'] - bwg_params[bwg]['slideshow_filmstrip_width'])+"px !important; max-height: " + (bwg_params[bwg]['filmstrip_direction'] == 'horizontal') ? (bwg_params[bwg]['image_height'] - bwg_params[bwg]['slideshow_filmstrip_height']) : bwg_params[bwg]['image_height']+"px !important;"
    });
    jQuery(".bwg_slideshow_embed_"+bwg).css({
      cssText: "width: "+(bwg_params[bwg]['filmstrip_direction'] == 'horizontal') ? bwg_params[bwg]['image_width'] : (bwg_params[bwg]['image_width'] - bwg_params[bwg]['slideshow_filmstrip_width'])+"px !important; height:"+ (bwg_params[bwg]['filmstrip_direction'] == 'horizontal') ? (bwg_params[bwg]['image_height'] - bwg_params[bwg]['slideshow_filmstrip_height']) : bwg_params[bwg]['image_height']+"px !important;"
    });
    bwg_resize_instagram_post( bwg );
    /* Set watermark container size. */
    bwg_change_watermark_container( bwg );
    var filmstrip_container_css = (bwg_params[bwg]['filmstrip_direction'] == 'horizontal')  ? 'width: ' + bwg_params[bwg]['image_width'] : 'height: ' + bwg_params[bwg]['image_height'];
    var filmstrip_css = (bwg_params[bwg]['filmstrip_direction'] == 'horizontal') ? 'width: ' + (bwg_params[bwg]['image_width'] - 40) : 'height: ' + (bwg_params[bwg]['image_height'] - 40);
    jQuery(".bwg_slideshow_filmstrip_container_" + bwg).css({ cssText: filmstrip_container_css });
    jQuery(".bwg_slideshow_filmstrip_"+bwg).css({ cssText: filmstrip_css });
    jQuery(".bwg_slideshow_dots_container_"+bwg).css({width: bwg_params[bwg]['image_width'] });
    jQuery("#bwg_slideshow_play_pause-ico_"+bwg).css({fontSize: (bwg_params[bwg]['slideshow_play_pause_btn_size'])});
    if ( bwg_params[bwg]['watermark_type'] == 'image') {
      jQuery(".bwg_slideshow_watermark_image_" + bwg).css({
        maxWidth: bwg_params[bwg]['watermark_width'],
        maxHeight: bwg_params[bwg]['watermark_height']
      });
    }
    if ( bwg_params[bwg]['watermark_type'] == 'text') {
      jQuery(".bwg_slideshow_watermark_text_" + bwg + ", .bwg_slideshow_watermark_text_" + bwg + " : hover").css({
        fontSize: (bwg_params[bwg]['watermark_font_size'])
      });
    }
    jQuery(".bwg_slideshow_title_text_"+bwg).css({fontSize: ( bwg_params[bwg]['slideshow_title_font_size'] * 2 )});
    jQuery(".bwg_slideshow_description_text_"+bwg).css({fontSize: (bwg_params[bwg]['slideshow_description_font_size'] * 2)});
  }
  else {
    jQuery(".bwg_slideshow_image_wrap_"+bwg).css({width: (parent_width)});
    jQuery(".bwg_slideshow_image_wrap_"+bwg).css({ height: ((parent_width) * bwg_params[bwg]['image_height'] / bwg_params[bwg]['image_width'] )});
    jQuery(".bwg_slideshow_image_container_"+bwg).css({width: (parent_width - (bwg_params[bwg]['filmstrip_direction'] == 'horizontal' ? 0 : bwg_params[bwg]['slideshow_filmstrip_width']))});
    jQuery(".bwg_slideshow_image_container_"+bwg).css({height: ((parent_width) * bwg_params[bwg]['image_height'] / bwg_params[bwg]['image_width'] - (bwg_params[bwg]['filmstrip_direction'] == 'horizontal' ? bwg_params[bwg]['slideshow_filmstrip_height'] : 0))});
    jQuery(".bwg_slideshow_image_"+bwg).css({
      cssText: "max-width: " + (parent_width - (bwg_params[bwg]['filmstrip_direction'] == 'horizontal' ? 0 : bwg_params[bwg]['slideshow_filmstrip_width'])) + "px !important; max-height: " + (parent_width * (bwg_params[bwg]['image_height'] / bwg_params[bwg]['image_width']) - (bwg_params[bwg]['filmstrip_direction'] == 'horizontal' ? bwg_params[bwg]['slideshow_filmstrip_height'] : 0) - 1) + "px !important;"
      });
    jQuery(".bwg_slideshow_embed_"+bwg).css({
      cssText: "width: " + (parent_width - (bwg_params[bwg]['filmstrip_direction'] == 'horizontal' ? 0 : bwg_params[bwg]['slideshow_filmstrip_width']) ) + "px !important; height: " + (parent_width * (bwg_params[bwg]['image_height'] / bwg_params[bwg]['image_width']) - (bwg_params[bwg]['filmstrip_direction'] == 'horizontal' ? bwg_params[bwg]['slideshow_filmstrip_height'] : 0) - 1) + "px !important;"
      });
    bwg_resize_instagram_post( bwg );
    /* Set watermark container size.*/
    bwg_change_watermark_container( bwg );
    if (bwg_params[bwg]['filmstrip_direction'] == 'horizontal') {
      jQuery(".bwg_slideshow_filmstrip_container_"+bwg).css({width: (parent_width)});
      jQuery(".bwg_slideshow_filmstrip_"+bwg).css({width: (parent_width - 40)});
    } else {
      jQuery(".bwg_slideshow_filmstrip_container_"+bwg).css({height: (parent_width * bwg_params[bwg]['image_height'] / bwg_params[bwg]['image_width'])});
      jQuery(".bwg_slideshow_filmstrip_"+bwg).css({height: (parent_width * bwg_params[bwg]['image_height'] / bwg_params[bwg]['image_width'] - 40)});
    }
  jQuery(".bwg_slideshow_dots_container_"+bwg).css({width: (parent_width)});
  jQuery("#bwg_slideshow_play_pause-ico_"+bwg).css({fontSize: ((parent_width) * bwg_params[bwg]['slideshow_play_pause_btn_size'] / bwg_params[bwg]['image_width'])});
  jQuery(".bwg_slideshow_watermark_image_"+bwg).css({maxWidth: ((parent_width) * bwg_params[bwg]['watermark_width'] / bwg_params[bwg]['image_width']), maxHeight: ((parent_width) * bwg_params[bwg]['watermark_height'] / bwg_params[bwg]['image_width'])});
  jQuery(".bwg_slideshow_watermark_text_"+bwg+", .bwg_slideshow_watermark_text_"+bwg+":hover").css({fontSize: ((parent_width) * bwg_params[bwg]['watermark_font_size'] / bwg_params[bwg]['image_width'])});
  jQuery(".bwg_slideshow_title_text_"+bwg).css({fontSize: ((parent_width) * 2 * bwg_params[bwg]['slideshow_title_font_size'] / bwg_params[bwg]['image_width'])});
  jQuery(".bwg_slideshow_description_text_"+bwg).css({fontSize: ((parent_width) * 2 * bwg_params[bwg]['slideshow_description_font_size'] / bwg_params[bwg]['image_width'])});
  jQuery(".bwg_slideshow_image_"+bwg).css({'display':'inline-block'});
  }
  if (data[parseInt(jQuery("#bwg_current_image_key_"+bwg).val())]["is_embed_video"]) {
            jQuery("#bwg_slideshow_play_pause_"+bwg).css({display: 'none'});
  }
  else {
    jQuery("#bwg_slideshow_play_pause_"+bwg).css({display: ''});
  }
}

function bwg_popup_resize( bwg ) {

  if ( typeof bwg !== 'undefined' && bwg !== '' ) { /* SLIDESHOW */
    bwg_popup_resize_slidshow( bwg );
  } else {  /* LIGHTBOX */
    bwg_popup_resize_lightbox();
  }
}

function bwg_change_image(current_key, key, data, from_effect, bwg) {
  if ( typeof bwg !== 'undefined' && bwg !== '' ) { /* SLIDESHOW */
    bwg_change_image_slideshow(current_key, key, data, from_effect, bwg);
  }
  else {  /* LIGHTBOX */
    data = gallery_box_data['data'];
    bwg_change_image_lightbox(current_key, key, data, from_effect);
  }
}

function bwg_resize_instagram_post( bwg ) {
  if ( typeof bwg !== 'undefined' && bwg !== '' ) { /* SLIDESHOW */
      if (jQuery(".inner_instagram_iframe_bwg_embed_frame_"+bwg).length) {
        var post_width = jQuery(".bwg_slideshow_embed_"+bwg).width();
        var post_height = jQuery(".bwg_slideshow_embed_").height();
        jQuery(".inner_instagram_iframe_bwg_embed_frame_"+bwg).each(function() {
          var parent_container = jQuery(this).parent();
          if (post_height / (parseInt(parent_container.attr('data-height')) + 96) < post_width / parseInt(parent_container.attr('data-width'))) {
            parent_container.height(post_height);
            parent_container.width((parent_container.height() - 96) * parent_container.attr('data-width') / parent_container.attr('data-height') + 16);
          }
          else {
            parent_container.width(post_width);
            parent_container.height((parent_container.width() - 16) * parent_container.attr('data-height') / parent_container.attr('data-width') + 96);
          }
        });
        bwg_change_watermark_container( bwg );
      }
  } else { /* LIGHTBOX */
      if (jQuery('.inner_instagram_iframe_bwg_embed_frame').length) {
        var post_width = jQuery(".bwg_image_container").width();
        var post_height = jQuery(".bwg_image_container").height();
        var FeedbackSocialProofHeight = 176;
        jQuery('.inner_instagram_iframe_bwg_embed_frame').each(function() {
          var parent_container = jQuery(this).parent();
          if (post_height / (parseInt(parent_container.attr('data-height')) + FeedbackSocialProofHeight) < post_width / parseInt(parent_container.attr('data-width'))) {
            parent_container.height(post_height);
            parent_container.width((parent_container.height() - FeedbackSocialProofHeight) * parent_container.attr('data-width') / parent_container.attr('data-height') + 16);
          }
          else {
            parent_container.width(post_width);
            parent_container.height((parent_container.width() - 16) * parent_container.attr('data-height') / parent_container.attr('data-width') + 96);
          }
          parent_container.css({top: 0.5 * (post_height - parent_container.height())});
        });
        bwg_change_watermark_container();
      }
  }
}

function bwg_play( data, bwg  ) {
  if ( typeof bwg !== 'undefined' && bwg !== '' ) {
    var data = bwg_params[bwg]['data'];
  }
  /* Play.*/
  if ( typeof bwg !== 'undefined' && bwg !== '' ) { /* SLIDESHOW */
    window.clearInterval(window['bwg_playInterval' + bwg]);
    window['bwg_playInterval' + bwg] = setInterval(function () {
      var iterator = 1;
      if (bwg_params[bwg]['enable_slideshow_shuffle'] == 1) {
        iterator = Math.floor((data.length - 1) * Math.random() + 1);
      }
      bwg_change_image( parseInt(jQuery("#bwg_current_image_key_"+bwg).val()), (parseInt(jQuery("#bwg_current_image_key_"+bwg).val()) + iterator) % data.length, data, '', bwg )
    }, bwg_params[bwg]['slideshow_interval'] * 1000);
  } else {
      window.clearInterval(gallery_box_data['bwg_playInterval']);
      gallery_box_data['bwg_playInterval'] = setInterval(function () {
        /* Stop play of lightbox if comment was opened*/
        if ( jQuery(".bwg_comment_container").hasClass("bwg_open") || (jQuery(".bwg_play_pause").length && jQuery(".bwg_play_pause").hasClass("bwg-icon-play"))) {
          return;
        }

        if (typeof data != 'undefined' && typeof data[(parseInt(jQuery('#bwg_current_image_key').val()) + 1)] == 'undefined') {
            if (gallery_box_data['enable_loop'] == 1) {
              /* Wrap around.*/
              bwg_change_image(parseInt(jQuery('#bwg_current_image_key').val()), 0);
            }
            return;
        }
        bwg_change_image(parseInt(jQuery('#bwg_current_image_key').val()), parseInt(jQuery('#bwg_current_image_key').val()) + 1)
      }, gallery_box_data['slideshow_interval'] * 1000);
  }
}

/*------------------ Image Browser Functions ---------------------------*/
function bwg_image_browser( bwg ) {
  jQuery('#bwg_embed_frame_16x9_' + bwg).width(jQuery('#bwg_embed_frame_16x9_' + bwg).parents('.image_browser_image_buttons_' + bwg).width());
  jQuery('#bwg_embed_frame_16x9_' + bwg).height(jQuery('#bwg_embed_frame_16x9_' + bwg).width() * 0.5625);
  jQuery('#bwg_embed_frame_instapost_' + bwg).width(jQuery('#bwg_embed_frame_16x9_' + bwg).parents('.image_browser_image_buttons_' + bwg).width());
  /* Conflict with Sydney Theme */
  if ( jQuery('.image_browser_images_conteiner_' + bwg).find('.fluid-width-video-wrapper').length ) {
	var content = jQuery('.image_browser_images_conteiner_' + bwg).find('.fluid-width-video-wrapper').contents();
	jQuery('.image_browser_images_conteiner_' + bwg).find('.fluid-width-video-wrapper').replaceWith(content);
  }
  /* 16 is 2*padding inside iframe */
  /* 96 is 2*padding(top) + 1*padding(bottom) + 40(footer) + 32(header) */
  jQuery('.bwg_embed_frame_instapost_' + bwg).height((jQuery('.bwg_embed_frame_instapost_' + bwg).width() - 16) * jQuery('.bwg_embed_frame_instapost_' + bwg).attr('data-height') / jQuery('.bwg_embed_frame_instapost_' + bwg).attr('data-width') + 96);

  var bwg_image_browser_width = jQuery('.image_browser_images_' + bwg).width();
  if (bwg_image_browser_width <= 108) {
    jQuery('.paging-input_' + bwg).css('display', 'none');
  }
  else if (bwg_image_browser_width <= 200) {
    jQuery('.paging-input_' + bwg).css('margin', '0% 0% 0% 0%');
    jQuery('.paging-input_' + bwg).css('display', 'inline');
    jQuery('.tablenav-pages_' + bwg + ' .next-page').css('margin', '0% 0% 0% 0%');
    jQuery('.tablenav-pages_' + bwg + ' .prev-page').css('margin', '0% 0% 0% 0%');
  }
  else if (bwg_image_browser_width <= 580) {
    jQuery('.paging-input_' + bwg).css('display', 'inline');
    jQuery('.tablenav-pages_' + bwg + ' a').css('font-size', '13px');
    jQuery('.paging-input_' + bwg).css('margin', '0% 7% 0% 7%');
    jQuery('.tablenav-pages_' + bwg + ' .next-page').css('margin', '0% 0% 0% 0%');
    jQuery('.tablenav-pages_' + bwg + ' .prev-page').css('margin', '0% 0% 0% 0%');
  }
  else {
    jQuery('.tablenav-pages_' + bwg + ' a').css('font-size', '15px');
    jQuery('.paging-input_' + bwg).css('margin', '0%  14% 0%  14%');
    jQuery('.paging-input_' + bwg).css('display', 'inline');
    jQuery('.tablenav-pages_' + bwg + ' .next-page').css('margin', '0% 0% 0% 0%');
    jQuery('.tablenav-pages_' + bwg + ' .prev-page').css('margin', '0% 0% 0% 0%');
  }
}

/**
 * Disable right click.
 *
 * @param container
 */
function bwg_disable_right_click( container ) {
  container.bind( "contextmenu", function () {
	return false;
  });
  container.css( 'webkitTouchCallout', 'none' );
}