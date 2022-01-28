var bwg_shortcode_type;

jQuery(function() {
  jQuery(".bwg_tw-container").parents().find(".wrap.wd-wrap-ajax").css({
    'height': 'calc(100% - 55px)'
  });

  jQuery(".mce-toolbar-grp.mce-inline-toolbar-grp.mce-container.mce-panel", parent.document).hide();
  /* Add tabs. */
  jQuery(".bwg_tabs").each(function () {
    jQuery(this).tabs({
      activate: function( event, ui ) {
        var bwg_shortcode_type_new = bwg_shortcode_type ? bwg_shortcode_type : (ui.newPanel.attr('id') == 'bwg_tab_albums_content' ? 'album_compact_preview' : 'thumbnails');
        bwg_shortcode_type = jQuery('input[name=gallery_type]:checked').val();
        bwg_gallery_type(bwg_shortcode_type_new);
      }
    });
  });
  jQuery("#use_option_defaults").on("change", function () {
    var use_option_defaults = jQuery(this).prop('checked');
    var custom_options_conainer = jQuery("#custom_options_conainer");
    if (use_option_defaults) {
      custom_options_conainer.hide();
    }
    else {
      custom_options_conainer.show();
    }
  }).trigger('change');
  jQuery(".hndle, .handlediv").each(function () {
    jQuery(this).off('click').on('click', function () {
      jQuery(this).parent(".postbox").toggleClass('closed');
    });
  });
  if (typeof bwg_update_shortcode == 'function') {
    bwg_update_shortcode();
  }

  jQuery('#custom_options_conainer .postbox .hndle').click(function() {
		var body = jQuery('html, body');
		var top  = jQuery(this).offset().top - 35;
			body.animate({ scrollTop: top }, 500 );
  });

  /* Changing label Number of image rows to columns in masonry view */
  jQuery('input[name=masonry]').on('click', function(){
    if(jQuery(this).val() == 'horizontal') {
      jQuery('.masonry_col_num').hide();
      jQuery('.masonry_row_num').show();
    } else {
      jQuery('.masonry_row_num').hide();
      jQuery('.masonry_col_num').show();
    }
  });
	show_hide_compact_album_view( jQuery('#album_view_type option:selected').val() );
	jQuery(document).on('change', '#album_view_type', function() {
		var value = jQuery(this).val();
		show_hide_compact_album_view( value );
	});
	show_hide_extended_album_view( jQuery('#album_extended_view_type option:selected').val() );
	jQuery(document).on('change', '#album_extended_view_type', function() {
		var value = jQuery(this).val();
		show_hide_extended_album_view( value );
	});

  /* images in select list */

  //change selected view
  jQuery('#bwg_shortcode_form .bwg-gallery-ul li').click(function(){
    if( jQuery(this).hasClass('gallery-type-li')) {
      jQuery('.type-selected').removeClass('type-selected');
      jQuery(this).addClass('type-selected');
      var value = jQuery(this).data('value');
      var item = jQuery(this).clone();
      var parent_el = jQuery(this).parent().parent().prev('.bwg-btn-gallery-type-select').attr('id');
      jQuery('#' + parent_el ).html(item);
      jQuery('#' + parent_el ).attr('value', value);
      bwg_gallery_type(value);
      change(parent_el);
    }
  });

  jQuery('body').click(function(){
    jQuery("#bwg_shortcode_form  .bwg-btn-gallery-type-select").each(function(){
      if( jQuery(this).hasClass("type-opened") ) {
        jQuery(this).removeClass("type-opened");
        jQuery(this).addClass("type-closed");
        jQuery(this).next(".bwg-gallery-ul-div").toggle();
      }
    })
  });

  /* functions to view div as select box */
  jQuery('#bwg_shortcode_form  .bwg-btn-gallery-type-select').click(function() {
    var id = jQuery(this).attr('id');
    if( !jQuery(this).next().find('.bwg-gallery-ul .type-selected').length ) {
      jQuery(this).next().find('.bwg-gallery-ul li:first-child').addClass('type-selected');
    }
    change(id);
  });

});



jQuery(window).on("load", function() {
	bwg_shortcode_load();
});

function change(view_type){
  var view_type_div =  jQuery('#' + view_type ).closest('.bwg-btn-gallery-type-select');
  if( view_type_div.hasClass('type-closed') ) {
    view_type_div.removeClass('type-closed');
    view_type_div.addClass('type-opened');
  } else {
    view_type_div.removeClass('type-opened');
    view_type_div.addClass('type-closed');
  }
  jQuery('#' + view_type ).next('.bwg-gallery-ul-div').toggle();
  event.stopPropagation();
}

function bwg_shortcode_load() {
  jQuery(".loading_div", window.parent.document).remove();
  jQuery("#loading_div.bwg_show").hide();
  jQuery(document).trigger("onUploadShortcode");
  jQuery(".spider_int_input").keypress(function (event) {
    var chCode1 = event.which || event.paramlist_keyCode;
    if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57) && (chCode1 != 46)) {
      return false;
    }
    return true;
  });
}

function bwg_lightbox_hide_show_params() {
  bwg_thumb_click_action();
  bwg_popup_fullscreen();
  if (jQuery("input[name=popup_enable_filmstrip]:checked").val() == 1 && jQuery("#thumb_click_action_1").is(':checked')) {
    bwg_enable_disable('', 'tr_popup_filmstrip_height', 'popup_filmstrip_yes');
  }
  else {
    bwg_enable_disable('none', 'tr_popup_filmstrip_height', 'popup_filmstrip_no');
  }
  if (jQuery("input[name=popup_enable_ctrl_btn]:checked").val() == 1 && jQuery("#thumb_click_action_1").is(':checked')) {
    jQuery("#tr_popup_fullscreen").css('display', '');
    jQuery("#tr_popup_info").css('display', '');
    jQuery("#tr_popup_download").css('display', '');
    jQuery("#tr_popup_fullsize_image").css('display', '');
    jQuery("#tr_popup_comment").css('display', '');
    jQuery("#tr_comment_moderation").css('display', '');
    jQuery("#tr_popup_email").css('display', '');
    jQuery("#tr_popup_captcha").css('display', '');
    jQuery("#tr_popup_facebook").css('display', '');
    jQuery("#tr_popup_twitter").css('display', '');
    jQuery("#tr_popup_pinterest").css('display', '');
    jQuery("#tr_popup_thumblr").css('display', '');
  }
  else {
    jQuery("#tr_popup_fullscreen").css('display', 'none');
    jQuery("#tr_popup_info").css('display', 'none');
    jQuery("#tr_popup_download").css('display', 'none');
    jQuery("#tr_popup_fullsize_image").css('display', 'none');
    jQuery("#tr_popup_comment").css('display', 'none');
    jQuery("#tr_comment_moderation").css('display', 'none');
    jQuery("#tr_popup_email").css('display', 'none');
    jQuery("#tr_popup_captcha").css('display', 'none');
    jQuery("#tr_popup_facebook").css('display', 'none');
    jQuery("#tr_popup_twitter").css('display', 'none');
    jQuery("#tr_popup_pinterest").css('display', 'none');
    jQuery("#tr_popup_thumblr").css('display', 'none');
  }
  if (jQuery("input[name=popup_enable_comment]:checked").val() == 1 && jQuery("input[name=popup_enable_ctrl_btn]:checked").val() == 1 && jQuery("#thumb_click_action_1").is(':checked')) {
    jQuery("#tr_popup_email").css('display', '');
    jQuery("#tr_popup_captcha").css('display', '');
    jQuery("#tr_comment_moderation").css('display', '');
  }
  else {
    jQuery("#tr_popup_email").css('display', 'none');
    jQuery("#tr_popup_captcha").css('display', 'none');
    jQuery("#tr_comment_moderation").css('display', 'none');
  }
  if (jQuery("input[name=enable_addthis]:checked").val() == 1 && jQuery("#thumb_click_action_1").is(':checked')) {
    jQuery("#tr_addthis_profile_id").css('display', '');
  }
  else {
    jQuery("#tr_addthis_profile_id").css('display', 'none');
  }
}

function bwg_shortcode_hide_show_params() {
  jQuery("#tr_search_box_width").hide();
  jQuery("#tr_search_box_placeholder").hide();
  jQuery("#tr_masonry_search_box_width").hide();
  jQuery("#tr_masonry_search_box_placeholder").hide();
  jQuery("#tr_mosaic_search_box_width").hide();
  jQuery("#tr_mosaic_search_box_placeholder").hide();
  jQuery("#tr_image_browser_search_box_width").hide();
  jQuery("#tr_image_browser_search_box_placeholder").hide();
  jQuery("#tr_blog_style_search_box_width").hide();
  jQuery("#tr_blog_style_search_box_placeholder").hide();
  jQuery("#tr_album_search_box_width").hide();
  jQuery("#tr_album_search_box_placeholder").hide();
  jQuery("#tr_album_masonry_search_box_width").hide();
  jQuery("#tr_album_masonry_search_box_placeholder").hide();
  jQuery("#tr_album_extended_search_box_width").hide();
  jQuery("#tr_album_extended_search_box_placeholder").hide();
  jQuery("#tr_slideshow_filmstrip_height").hide();
  jQuery("#tr_slideshow_title_position").hide();
  jQuery("#tr_slideshow_full_width_title").hide();
  jQuery("#tr_slideshow_description_position").hide();
  jQuery("#tr_slideshow_music_url").hide();
  jQuery("#tr_autohide_slideshow_navigation").hide();
  jQuery("#tr_load_more_image_count").hide();
  jQuery("#tr_masonry_load_more_image_count").hide();
  jQuery("#tr_mosaic_load_more_image_count").hide();
  jQuery("#tr_blog_style_load_more_image_count").hide();
  jQuery("#tr_album_mosaic").hide();
  jQuery("#tr_album_resizable_mosaic").hide();
  jQuery("#tr_album_mosaic_total_width").hide();
  jQuery("#tr_album_extended_mosaic").hide();
  jQuery("#tr_album_extended_resizable_mosaic").hide();
  jQuery("#tr_album_extended_mosaic_total_width").hide();
  jQuery("#tr_show_masonry_thumb_description").hide();

  if (jQuery('#show_search_box_1').is(':checked')) {
    jQuery( "#tr_search_box_width" ).show();
    jQuery( "#tr_search_box_placeholder" ).show();
  }
  if (jQuery('#masonry_show_search_box_1').is(':checked')) {
    jQuery( "#tr_masonry_search_box_width" ).show();
    jQuery( "#tr_masonry_search_box_placeholder" ).show();
  }
  if (jQuery('#mosaic_show_search_box_1').is(':checked')) {
    jQuery("#tr_mosaic_search_box_width").show();
    jQuery("#tr_mosaic_search_box_placeholder").show();
  }
  if (jQuery('#image_browser_show_search_box_1').is(':checked')) {
    jQuery("#tr_image_browser_search_box_width").show();
    jQuery("#tr_image_browser_search_box_placeholder").show();
  }
  if (jQuery('#blog_style_show_search_box_1').is(':checked')) {
    jQuery("#tr_blog_style_search_box_width").show();
    jQuery("#tr_blog_style_search_box_placeholder").show();
  }
  if (jQuery('#album_show_search_box_1').is(':checked')) {
    jQuery("#tr_album_search_box_width").show();
    jQuery("#tr_album_search_box_placeholder").show();
  }
  if (jQuery('#album_masonry_show_search_box_1').is(':checked')) {
    jQuery("#tr_album_masonry_search_box_width").show();
    jQuery("#tr_album_masonry_search_box_placeholder").show();
  }
  if (jQuery('#album_extended_show_search_box_1').is(':checked')) {
    jQuery("#tr_album_extended_search_box_width").show();
    jQuery("#tr_album_extended_search_box_placeholder").show();
  }
  if (jQuery('#slideshow_enable_filmstrip_yes').is(':checked')) {
    jQuery("#tr_slideshow_filmstrip_height").show();
  }
  if (jQuery('#slideshow_enable_title_yes').is(':checked')) {
    jQuery("#tr_slideshow_title_position").show();
    jQuery("#tr_slideshow_full_width_title").show();
  }
  if (jQuery('#slideshow_enable_description_yes').is(':checked')) {
    jQuery("#tr_slideshow_description_position").show();
  }
  if (jQuery('#slideshow_enable_music_yes').is(':checked')) {
    jQuery("#tr_slideshow_music_url").show();
  }
  if (jQuery('#slideshow_enable_ctrl_yes').is(':checked')) {
    jQuery("#tr_autohide_slideshow_navigation").show();
  }
  if (jQuery('#image_enable_page_2').is(':checked')) {
    jQuery("#tr_load_more_image_count").show();
  }
  if (jQuery('#masonry_image_enable_page_2').is(':checked')) {
    jQuery("#tr_masonry_load_more_image_count").show();
  }
  if (jQuery('#mosaic_image_enable_page_2').is(':checked')) {
    jQuery("#tr_mosaic_load_more_image_count").show();
  }
  if (jQuery('#blog_style_enable_page_2').is(':checked')) {
    jQuery("#tr_blog_style_load_more_image_count").show();
  }
  if (jQuery('#album_view_type_2').is(':checked')) {
    jQuery("#tr_album_mosaic").show();
    jQuery("#tr_album_resizable_mosaic").show();
    jQuery("#tr_album_mosaic_total_width").show();
  }
  if (jQuery('#album_extended_view_type_2').is(':checked')) {
    jQuery("#tr_album_extended_mosaic").show();
    jQuery("#tr_album_extended_resizable_mosaic").show();
    jQuery("#tr_album_extended_mosaic_total_width").show();
  }
  if (jQuery('#thumbnails_masonry').is(':checked')) {
    jQuery("#tr_show_masonry_thumb_description").show();
  }
  bwg_pagination_description(jQuery('input[name=image_enable_page]:checked'));
  bwg_pagination_description(jQuery('input[name=masonry_image_enable_page]:checked'));
  bwg_pagination_description(jQuery('input[name=mosaic_image_enable_page]:checked'));
  bwg_pagination_description(jQuery('input[name=blog_style_enable_page]:checked'));
  bwg_pagination_description(jQuery('input[name=album_enable_page]:checked'));
  bwg_pagination_description(jQuery('input[name=album_masonry_enable_page]:checked'));
  bwg_pagination_description(jQuery('input[name=album_extended_enable_page]:checked'));
}

function bwg_watermark(watermark_type) {
  jQuery("#" + watermark_type).prop('checked', true);
  jQuery("#tr_watermark_link").css('display', 'none');
  jQuery("#tr_watermark_url").css('display', 'none');
  jQuery("#tr_watermark_width_height").css('display', 'none');
  jQuery("#tr_watermark_opacity").css('display', 'none');
  jQuery("#tr_watermark_text").css('display', 'none');
  jQuery("#tr_watermark_font_size").css('display', 'none');
  jQuery("#tr_watermark_font").css('display', 'none');
  jQuery("#tr_watermark_color").css('display', 'none');
  jQuery("#tr_watermark_position").css('display', 'none');
  bwg_enable_disable('', '', 'watermark_bottom_right');
  switch (watermark_type) {
    case 'watermark_type_text': {
      jQuery("#tr_watermark_link").css('display', '');
      jQuery("#tr_watermark_opacity").css('display', '');
      jQuery("#tr_watermark_text").css('display', '');
      jQuery("#tr_watermark_font_size").css('display', '');
      jQuery("#tr_watermark_font").css('display', '');
      jQuery("#tr_watermark_color").css('display', '');
      jQuery("#tr_watermark_position").css('display', '');
      break;

    }
    case 'watermark_type_image': {
      jQuery("#tr_watermark_link").css('display', '');
      jQuery("#tr_watermark_url").css('display', '');
      jQuery("#tr_watermark_width_height").css('display', '');
      jQuery("#tr_watermark_opacity").css('display', '');
      jQuery("#tr_watermark_position").css('display', '');
      break;
    }
  }
}

function bwg_enable_disable(display, id, current) {
  jQuery("#" + current).prop('checked', true);
  jQuery("#" + id).css('display', display);
  if(id == 'tr_slideshow_title_position') {
    jQuery("#tr_slideshow_full_width_title").css('display', display);
  }
}

function bwg_popup_fullscreen() { 
  if (jQuery("#popup_fullscreen_0").is(':checked') && jQuery("#thumb_click_action_1").is(':checked')) {
    jQuery("#tr_popup_dimensions").css('display', '');
  }
  else {
    jQuery("#tr_popup_dimensions").css('display', 'none');
  }
}

function bwg_thumb_click_action() {
  jQuery('.bwg-lightbox').hide();
  if (jQuery("#thumb_click_action_1").is(':checked')) {
    jQuery('.bwg-lightbox-lightbox').show();
  }
  else if (jQuery("#thumb_click_action_2").is(':checked')) {
    jQuery('.bwg-lightbox-redirect').show();
  }
}

function bwg_show_search_box() { 
  if (jQuery("#show_search_box_1").is(':checked')) {
    jQuery("#tr_search_box_width").css('display', '');
  }
  else {
    jQuery("#tr_search_box_width").css('display', 'none');
  }
}

function bwg_change_compuct_album_view_type() {
  if (jQuery("input[name=compuct_album_view_type]:checked").val() == 'thumbnail') {
    jQuery("#compuct_album_image_thumb_dimensions").html(bwg_image_thumb);
    jQuery("#compuct_album_image_thumb_dimensions_x").css('display', '');
    jQuery("#compuct_album_image_thumb_width").css('display', '');
    jQuery("#compuct_album_image_thumb_height").css('display', '');
    jQuery("#tr_compuct_album_image_title").css('display', '');
    jQuery("#tr_compuct_album_mosaic_hor_ver").css('display', 'none');
    jQuery("#tr_compuct_album_resizable_mosaic").css('display', 'none');
    jQuery("#tr_compuct_album_mosaic_total_width").css('display', 'none');
    jQuery("#tr_compuct_album_image_column_number").css('display', '');
  }
  
  else if(jQuery("input[name=compuct_album_view_type]:checked").val() == 'masonry'){
    jQuery("#compuct_album_image_thumb_dimensions").html(bwg_image_thumb_width); 
    jQuery("#compuct_album_image_thumb_dimensions_x").css('display', 'none');
    jQuery("#compuct_album_image_thumb_width").css('display', '');
    jQuery("#compuct_album_image_thumb_height").css('display', 'none');
    jQuery("#tr_compuct_album_image_title").css('display', 'none');
    jQuery("#tr_compuct_album_mosaic_hor_ver").css('display', 'none');
    jQuery("#tr_compuct_album_resizable_mosaic").css('display', 'none');
    jQuery("#tr_compuct_album_mosaic_total_width").css('display', 'none');
    jQuery("#tr_compuct_album_image_column_number").css('display', '');
  }
  else {/*mosaic*/
    jQuery("#compuct_album_image_thumb_dimensions_x").css('display', 'none');
    jQuery("#tr_compuct_album_image_column_number").css('display', 'none');
    jQuery("#tr_compuct_album_image_title").css('display', '');
    jQuery("#tr_compuct_album_mosaic_hor_ver").css('display', '');
    jQuery("#tr_compuct_album_resizable_mosaic").css('display', '');
    jQuery("#tr_compuct_album_mosaic_total_width").css('display', '');
    if(jQuery("input[name=compuct_album_mosaic_hor_ver]:checked").val() == 'vertical'){
      jQuery("#compuct_album_image_thumb_dimensions").html(bwg_image_thumb_width);
      jQuery("#compuct_album_image_thumb_height").css('display', 'none');
      jQuery("#compuct_album_image_thumb_width").css('display', '');
    }
    else{
      jQuery("#compuct_album_image_thumb_dimensions").html(bwg_image_thumb_height);
      jQuery("#compuct_album_image_thumb_width").css('display', 'none');
      jQuery("#compuct_album_image_thumb_height").css('display', '');
    }
  }
}

function bwg_change_label(id, text) {
  jQuery('#' + id).html(text);
}

function bwg_gallery_type(gallery_type) {
  jQuery('.gallery_type').find('.view_type_img_active').css('display','none');
  jQuery('.gallery_type').find('.view_type_img').css('display','inline');
  jQuery('.gallery_type').removeClass('gallery_type_active');
  jQuery("#" + gallery_type).prop('checked', true);
  jQuery('input[name=gallery_type][id=' + gallery_type + ']').prop('checked', 'checked').closest('.gallery_type').addClass('gallery_type_active');
  jQuery('.gallery_type_active').find('.view_type_img').css('display','none');
  jQuery('.gallery_type_active').find('.view_type_img_active').css('display','inline');

  jQuery("#tr_gallery").css('display', 'none');
  jQuery("#tr_album").css('display', 'none');
  var basic_metabox_title = jQuery('#bwg_basic_metabox_title');
  if( jQuery("#" + gallery_type).attr('class') == 'album_type_radio' ) {
    basic_metabox_title.text(basic_metabox_title.attr('data-title-album'));
  }
  else {
    basic_metabox_title.text(basic_metabox_title.attr('data-title-gallery'));
  }
  jQuery("#tr_ecommerce_icon_hover").css('display', 'none');
  jQuery("#tr_ecommerce_icon_hover .ecommerce_icon_show").css('display', 'none');
  jQuery("#tr_tag").css('display', 'none');

  /* Watermark. */
  jQuery("#tr_watermark_type").css('display', '');
  if (jQuery("input[name=watermark_type]:checked").val() == 'image') {
    bwg_watermark('watermark_type_image');
  }
  else if (jQuery("input[name=watermark_type]:checked").val() == 'text'){
    bwg_watermark('watermark_type_text');
  }
  else {
    bwg_watermark('watermark_type_none');
  }
  jQuery('.gallery_options, .album_options').hide();
  jQuery('#' + gallery_type + '_options').show();

  if( jQuery("#" + gallery_type).closest('.bwg_change_gallery_type').parent().attr('id') == 'bwg_tab_galleries_content' ) {
    jQuery('#options_link').attr('href', jQuery('#options_link').attr('data-href') + '&active_tab=1&gallery_type=' + gallery_type);
  } else {
    jQuery('#options_link').attr('href', jQuery('#options_link').attr('data-href') + '&active_tab=2&album_type=' + gallery_type);
  }

  gallery_type_name = jQuery('.bwg-' + gallery_type).data('title');
  pro_img_url = jQuery('.bwg-' + gallery_type).data('img-url');
  pro_demo_link = jQuery('.bwg-' + gallery_type).data('demo-link');

  switch (gallery_type) {
    case 'thumbnails': {
      jQuery("#tr_gallery").css('display', '');
	    jQuery("#tr_ecommerce_icon_hover").css('display', '');
      jQuery("#tr_ecommerce_icon_hover .ecommerce_icon_show").css('display', '');  
      jQuery("#tr_tag").css('display', '');
      jQuery(".wd-free-msg").hide();
      jQuery("#insert").attr("style", "visibility: visible;");
      jQuery(".bwg-section.bwg-pro-views").show();
      break;
    }
    case 'thumbnails_masonry': {
	    jQuery("#tr_ecommerce_icon_hover").css('display', '');
      jQuery("#tr_gallery").css('display', '');
      jQuery("#tr_tag").css('display', '');
      jQuery(".wd-free-msg").show();
      jQuery(".upgrade-to-pro-title").html( gallery_type_name + bwg_premium_text);
      jQuery(".pro-views-img").attr('src',pro_img_url);
      jQuery(".button-demo").attr('href', pro_demo_link );
      if ( jQuery(".wd-free-msg").length != 0 ) {
        jQuery("#insert").attr("style", "visibility: hidden;");
        jQuery(".bwg-pro-views").hide();
      }
      break;
    }
    case 'thumbnails_mosaic': {
  	  jQuery("#tr_ecommerce_icon_hover ").css('display', '');
      jQuery("#tr_gallery").css('display', '');
      jQuery("#tr_tag").css('display', '');
      jQuery(".wd-free-msg").show();
      jQuery(".upgrade-to-pro-title").html( gallery_type_name + bwg_premium_text);
      jQuery(".pro-views-img").attr('src',pro_img_url);
      jQuery(".button-demo").attr('href', pro_demo_link );
      if ( jQuery(".wd-free-msg").length != 0 ) {
        jQuery("#insert").attr("style", "visibility: hidden;");
        jQuery(".bwg-pro-views").hide();
      }
      break;
    }
    case 'slideshow': {
      jQuery("#tr_gallery").css('display', '');
      jQuery("#tr_tag").css('display', '');
      jQuery(".wd-free-msg").hide();
      jQuery("#insert").attr("style", "visibility: visible;");
      jQuery(".bwg-section.bwg-pro-views").show();
      break;
    }
    case 'image_browser': {
      jQuery("#tr_gallery").css('display', '');
      jQuery("#tr_tag").css('display', '');
      jQuery(".wd-free-msg").hide();
      jQuery("#insert").attr("style", "visibility: visible;");
      jQuery(".bwg-section.bwg-pro-views").show();
      break;
    }
    case 'album_compact_preview': {
      jQuery("#tr_album").css('display', '');
      basic_metabox_title.text(basic_metabox_title.attr('data-title-album'));
      jQuery(".wd-free-msg").hide();
      jQuery("#insert").attr("style", "visibility: visible;");
      jQuery(".bwg-section.bwg-pro-views").show();
      break;
    }
    case 'album_extended_preview': {
      jQuery("#tr_album").css('display', '');
      jQuery(".wd-free-msg").hide();
      jQuery("#insert").attr("style", "visibility: visible;");
      jQuery(".bwg-section.bwg-pro-views").show();
      break;
    }
	case 'album_masonry_preview': {
      jQuery("#tr_album").css('display', '');
      jQuery(".wd-free-msg").show();
      jQuery(".upgrade-to-pro-title").html( gallery_type_name + bwg_premium_text);
      jQuery(".pro-views-img").attr('src',pro_img_url);
      jQuery(".button-demo").attr('href', pro_demo_link );
      if ( jQuery(".wd-free-msg").length != 0 ) {
        jQuery("#insert").attr("style", "visibility: hidden;");
        jQuery(".bwg-pro-views").hide();
      }
      break;
    }		
    case 'blog_style': {
      jQuery("#tr_gallery").css('display', '');
      jQuery("#tr_tag").css('display', '');
      jQuery(".wd-free-msg").show();
      jQuery(".upgrade-to-pro-title").html( gallery_type_name + bwg_premium_text);
      jQuery(".pro-views-img").attr('src',pro_img_url);
      jQuery(".button-demo").attr('href', pro_demo_link );
      if ( jQuery(".wd-free-msg").length != 0 ) {
        jQuery("#insert").attr("style", "visibility: hidden;");
        jQuery(".bwg-pro-views").hide();
      }
      break;
    }
	case 'carousel': {
	  jQuery("#tr_gallery").css('display', '');
      jQuery("#tr_tag").css('display', '');
      jQuery(".wd-free-msg").show();
      jQuery(".upgrade-to-pro-title").html( gallery_type_name + bwg_premium_text);
      jQuery(".pro-views-img").attr('src',pro_img_url);
      jQuery(".button-demo").attr('href', pro_demo_link );
      if ( jQuery(".wd-free-msg").length != 0 ) {
        jQuery("#insert").attr("style", "visibility: hidden;");
        jQuery(".bwg-pro-views").hide();
      }
      break;
	}
  }

  bwg_lightbox_hide_show_params();

  bwg_shortcode_hide_show_params();
}

function bwg_onKeyDown(e) {
  var e = e || window.event;
  var chCode1 = e.which || e.paramlist_keyCode;
  if (chCode1 != 37 && chCode1 != 38 && chCode1 != 39 && chCode1 != 40) {
    if ((!e.ctrlKey && !e.metaKey) || (chCode1 != 86 && chCode1 != 67 && chCode1 != 65 && chCode1 != 88)) {
      e.preventDefault();
    }
  }
}

function spider_select_value(obj) {
  obj.focus();
  obj.select();
}

function bwg_change_fonts(cont, google_fonts) {
  var fonts;
  if (jQuery("#" + google_fonts).is(":checked") == true) {
    fonts = bwg_objectGGF;
  }
  else {
    fonts = {'arial' : 'Arial', 'lucida grande' : 'Lucida grande', 'segoe ui' : 'Segoe ui', 'tahoma' : 'Tahoma', 'trebuchet ms' : 'Trebuchet ms', 'verdana' : 'Verdana', 'cursive' : 'Cursive', 'fantasy' : 'Fantasy', 'monospace' : 'Monospace', 'serif' : 'Serif'};
  }
  var fonts_option = "";
  for (var i in fonts) {
    fonts_option += '<option value="' + i + '">' + fonts[i] + '</option>';
  }
  jQuery("#" + cont).html(fonts_option);
}

function bwg_change_tab() {
  var width = jQuery(window).width();
  if (width < 1280) {
    jQuery(".bwg_change_gallery_type").hide();
    jQuery(".bwg_select_gallery_type").show();
    jQuery(".tabs_views").show();
    jQuery(".bwg_hr_shortcode").css({'display':'none'});
  }
  else {
    jQuery(".bwg_change_gallery_type").show();
    jQuery(".bwg_select_gallery_type").hide();
    jQuery(".tabs_views").hide(); 
    jQuery(".bwg_hr_shortcode").css({'display':''});
  }
}

/**
 * Get selected text from textarea.
 *
 * @param id
 * @returns {*}
 */
function bwg_get_textarea_selection(id) {
  var textComponent = top.document.getElementById(id);
  var selectedText;
  if (textComponent.selectionStart !== undefined) {
    /* Standards Compliant Version */
    var startPos = textComponent.selectionStart;
    var endPos = textComponent.selectionEnd;
    selectedText = textComponent.value.substring(startPos, endPos);
  }
  else if (document.selection !== undefined) {
    /* IE Version */
    textComponent.focus();
    var sel = document.selection.createRange();
    selectedText = sel.text;
  }
  return selectedText;
}

function bwg_pagination_description(that) {
  obj = jQuery(that);
  obj.closest('.wd-group').find('.description').hide();
  jQuery('#' + obj.attr('name') + '_' + obj.val() + '_description').show();
}

function show_hide_compact_album_view ( val ) {
	switch(val) {
		case 'thumbnail': {
			bwg_show_hide('tr_album_mosaic', 'none');
			bwg_show_hide('tr_album_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_mosaic_total_width', 'none');
			bwg_show_hide('for_album_image_title_show_hover_0', '');
			bwg_show_hide('album_image_title_show_hover_0', '');
			bwg_show_hide('for_album_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_thumbnail_dimensions', '');
			bwg_show_hide('tr_album_images_per_page', '');
		}
		break;
		case 'masonry': {
			bwg_show_hide('tr_album_mosaic', 'none');
			bwg_show_hide('tr_album_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_mosaic_total_width', 'none');
			bwg_show_hide('for_album_image_title_show_hover_0', '');
			bwg_show_hide('album_image_title_show_hover_0', '');
			bwg_show_hide('for_album_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_thumbnail_dimensions', '');
			bwg_show_hide('tr_album_images_per_page', '');
		}
		break;
		case 'mosaic': {
			bwg_show_hide('tr_album_mosaic', '');
			bwg_show_hide('tr_album_resizable_mosaic', '');
			bwg_show_hide('tr_album_mosaic_total_width', '');;
			bwg_show_hide('for_album_image_title_show_hover_0', 'none');
			bwg_show_hide('album_image_title_show_hover_0', 'none');
			bwg_show_hide('for_album_ecommerce_icon_show_hover_0', 'none');
			bwg_show_hide('tr_album_thumbnail_dimensions', '');
			bwg_show_hide('tr_album_images_per_page', '');
		}
		break;
		case 'slideshow': {
			bwg_show_hide('tr_album_mosaic', 'none');
			bwg_show_hide('tr_album_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_mosaic_total_width', 'none');
			bwg_show_hide('for_album_image_title_show_hover_0', '');
			bwg_show_hide('album_image_title_show_hover_0', '');
			bwg_show_hide('for_album_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_thumbnail_dimensions', 'none');
			bwg_show_hide('tr_album_images_per_page', 'none');
		}
		case 'image_browser': {
			bwg_show_hide('tr_album_mosaic', 'none');
			bwg_show_hide('tr_album_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_mosaic_total_width', 'none');
			bwg_show_hide('for_album_image_title_show_hover_0', '');
			bwg_show_hide('album_image_title_show_hover_0', '');
			bwg_show_hide('for_album_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_thumbnail_dimensions', 'none');
			bwg_show_hide('tr_album_images_per_page', 'none');
		}
		break;
		case 'blog_style': {
			bwg_show_hide('tr_album_mosaic', 'none');
			bwg_show_hide('tr_album_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_mosaic_total_width', 'none');
			bwg_show_hide('for_album_image_title_show_hover_0', '');
			bwg_show_hide('album_image_title_show_hover_0', '');
			bwg_show_hide('for_album_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_thumbnail_dimensions', 'none');
			bwg_show_hide('tr_album_images_per_page', '');
		}
		break;
		case 'carousel': {
			bwg_show_hide('tr_album_mosaic', 'none');
			bwg_show_hide('tr_album_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_mosaic_total_width', 'none');
			bwg_show_hide('for_album_image_title_show_hover_0', '');
			bwg_show_hide('album_image_title_show_hover_0', '');
			bwg_show_hide('for_album_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_thumbnail_dimensions', 'none');
			bwg_show_hide('tr_album_images_per_page', 'none');
		}
		break;
	}
}

function show_hide_extended_album_view ( val ) {
	switch(val) {
		case 'thumbnail': {
			bwg_show_hide('tr_album_extended_mosaic', 'none');
			bwg_show_hide('tr_album_extended_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_extended_mosaic_total_width', 'none');
			bwg_show_hide('for_album_extended_image_title_show_hover_0', '');
			bwg_show_hide('album_extended_image_title_show_hover_0', '');
			bwg_show_hide('for_album_extended_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_extended_thumbnail_dimensions', '');
			bwg_show_hide('tr_album_extended_images_per_page', '');
		}
		break;
		case 'masonry': {
			bwg_show_hide('tr_album_extended_mosaic', 'none');
			bwg_show_hide('tr_album_extended_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_extended_mosaic_total_width', 'none');
			bwg_show_hide('for_album_extended_image_title_show_hover_0', '');
			bwg_show_hide('album_extended_image_title_show_hover_0', '');
			bwg_show_hide('for_album_extended_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_extended_thumbnail_dimensions', '');
			bwg_show_hide('tr_album_extended_images_per_page', '');
		}
		break;
		case 'mosaic': {
			bwg_show_hide('tr_album_extended_mosaic', '');
			bwg_show_hide('tr_album_extended_resizable_mosaic', '');
			bwg_show_hide('tr_album_extended_mosaic_total_width', '');
			bwg_show_hide('for_album_extended_image_title_show_hover_0', 'none');
			bwg_show_hide('album_extended_image_title_show_hover_0', 'none');
			bwg_show_hide('for_album_extended_ecommerce_icon_show_hover_0', 'none');
			bwg_show_hide('tr_album_extended_thumbnail_dimensions', '');
			bwg_show_hide('tr_album_extended_images_per_page', '');
		}
		break;
		case 'slideshow': {
			bwg_show_hide('tr_album_extended_mosaic', 'none');
			bwg_show_hide('tr_album_extended_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_extended_mosaic_total_width', 'none');
			bwg_show_hide('for_album_extended_image_title_show_hover_0', '');
			bwg_show_hide('album_extended_image_title_show_hover_0', '');
			bwg_show_hide('for_album_extended_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_extended_thumbnail_dimensions', 'none');
			bwg_show_hide('tr_album_extended_images_per_page', 'none');
		}
		case 'image_browser': {
			bwg_show_hide('tr_album_extended_mosaic', 'none');
			bwg_show_hide('tr_album_extended_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_extended_mosaic_total_width', 'none');
			bwg_show_hide('for_album_extended_image_title_show_hover_0', '');
			bwg_show_hide('album_extended_image_title_show_hover_0', '');
			bwg_show_hide('for_album_extended_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_extended_thumbnail_dimensions', 'none');
			bwg_show_hide('tr_album_extended_images_per_page', 'none');
		}
		break;
		case 'blog_style': {
			bwg_show_hide('tr_album_extended_mosaic', 'none');
			bwg_show_hide('tr_album_extended_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_extended_mosaic_total_width', 'none');
			bwg_show_hide('for_album_extended_image_title_show_hover_0', '');
			bwg_show_hide('album_extended_image_title_show_hover_0', '');
			bwg_show_hide('for_album_extended_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_extended_thumbnail_dimensions', 'none');
			bwg_show_hide('tr_album_extended_images_per_page', '');
		}
		break;
		case 'carousel': {
			bwg_show_hide('tr_album_extended_mosaic', 'none');
			bwg_show_hide('tr_album_extended_resizable_mosaic', 'none');
			bwg_show_hide('tr_album_extended_mosaic_total_width', 'none');
			bwg_show_hide('for_album_extended_image_title_show_hover_0', '');
			bwg_show_hide('album_extended_image_title_show_hover_0', '');
			bwg_show_hide('for_album_extended_ecommerce_icon_show_hover_0', '');
			bwg_show_hide('tr_album_extended_thumbnail_dimensions', 'none');
			bwg_show_hide('tr_album_extended_images_per_page', 'none');
		}
		break;
	}
}

function bwg_show_hide(id, display) {
	jQuery("#" + id).css('display', display);
}