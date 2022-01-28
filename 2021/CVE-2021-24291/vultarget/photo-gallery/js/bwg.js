jQuery( function () {
  /* Change google font for <select>. */
  if ( jQuery( '.google_font' ).length ) {
    jQuery( '.google_font' ).each( function () {
      var bwg_google_font = jQuery( this );
      bwg_google_font.fontselect();
      input_name = jQuery( this ).closest( 'td' ).find( '.radio_google_fonts' ).children( 'input' ).attr( 'name' );
      data_view = input_name + '0';
      if ( jQuery( "#" + data_view ).is( ":checked" ) ) {
        bwg_google_font.next( '.font-select' ).hide();
        jQuery( '#' + input_name ).show();
      }
      else {
        bwg_google_font.next( '.font-select' ).show();
        jQuery( '#' + input_name ).hide();
      }
    } )
  }
  jQuery( '.default-font' ).on( 'change', function () {
    jQuery( this ).css( { 'font-family': jQuery( this ).val() } );
  } );

  /* press ESC hide loading. */
  jQuery( document ).keyup( function ( e ) {
    if ( e.key == 'Escape') {
      jQuery( '#loading_div' ).hide();
    }
  } );
  /* Galleries form. */
  if ( jQuery( "form" ).hasClass( "bwg_galleries" ) ) {
    if ( jQuery( "#tbody_arr" ).hasClass( "bwg-ordering" ) ) {
      wd_showhide_weights();
    }
    wd_pagination();
    if ( jQuery( "#bwg-table-sortable" ).hasClass( "bwg-ordering" ) ) {
      bwg_galleries_ordering();
    }
  }

  jQuery( "#check_all" ).on( "click", function () {
    spider_check_all( "#check_all" );
  } );

  /* Add tooltip to elements with "wd-info" class. */
  if ( typeof jQuery( document ).tooltip != "undefined" ) {
    jQuery( document ).tooltip( {
      show: null,
      items: ".wd-info",
      content: function () {
        var element = jQuery( this );
        if ( element.is( ".wd-info" ) ) {
          var html = jQuery( '#' + jQuery( this ).data( "id" ) ).html();
          return html;
        }
      },
      open: function ( event, ui ) {
        if ( typeof ( event.originalEvent ) === 'undefined' ) {
          return false;
        }
        var $id = jQuery( ui.tooltip ).attr( 'id' );
        /* close any lingering tooltips. */
        jQuery( 'div.ui-tooltip' ).not( '#' + $id ).remove();
      },
      close: function ( event, ui ) {
        ui.tooltip.hover( function () {
            jQuery( this ).stop( true ).fadeTo( 400, 1 );
          },
          function () {
            jQuery( this ).fadeOut( '400', function () {
              jQuery( this ).remove();
            } );
          } );
      },
      position: {
        my: "center top+30",
        at: "center top",
        using: function ( position, feedback ) {
          jQuery( this ).css( position );
          jQuery( "<div>" )
            .addClass( "tooltip-arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    } );
  }

  bwg_change_theme_tab_item();
  bwg_filters();
  bwg_toggle_postbox();

  jQuery( ".bwg_requried" ).on( "keypress", function () {
    jQuery( ".bwg_requried" ).removeAttr( "style" );
  } );

  jQuery( ".wd-filter" ).on( "change", function () {
    var form = jQuery( this ).parents( "form" );

    var action = form.attr( "action" );
    action += "&paged=1";
    action += "&s=" + jQuery( "input[name='s']" ).val();
    action += "&filter-by-gallery=" + jQuery( "select[name='filter[filter-by-gallery]']" ).val();
    action += "&filter-by-image=" + jQuery( "select[name='filter[filter-by-image]']" ).val();
    form.attr( "action", action );

    form.submit();
  } );

  /* Options form. */
  if ( jQuery( "form#bwg_options_form" ).length > 0 ) {
    jQuery( ".bwg_tabs" ).each( function () {
      jQuery( this ).tabs( {
        active: jQuery( '#active_tab' ).val(),
        activate: function ( event, ui ) {
          jQuery( '#active_tab' ).val( ui.newTab.index() );
          if ( ui.newTab.index() == 1 ) {
            bwg_gallery_type_options();
          }
          else if ( ui.newTab.index() == 2 ) {
            bwg_album_type_options();
          }
        }
      } );
    } );
    bwg_gallery_type_options();
    bwg_album_type_options();
  }

  /* Changing label Number of image rows to columns in masonry view. */
  jQuery( 'input[name=masonry]' ).on( 'click', function () {
    if ( jQuery( this ).val() == 'horizontal' ) {
      jQuery( '.masonry_col_num' ).hide();
      jQuery( '.masonry_row_num' ).show();
    }
    else {
      jQuery( '.masonry_row_num' ).hide();
      jQuery( '.masonry_col_num' ).show();
    }
  } );

  jQuery( '#bwg_image_editor_notice .notice-dismiss' ).on( 'click', function () {
    var dismiss_url = bwg_ajax_url + '=' + jQuery( '#bwg_image_editor_notice' ).data( 'action' );
    jQuery.ajax( {
      method: "POST",
      url: dismiss_url,
    } );
  } );

  /* Change the popup dimensions. */
  bwg_tb_window();

  /* Hide loading */
  jQuery( '#loading_div.bwg_show' ).hide();

  bwg_lazy_load_gallery();

  /* Albums form. */
  if ( jQuery( "form" ).hasClass( "bwg_albums" ) ) {
    jQuery( "#bwg_tabs" ).sortable( {
      items: ".connectedSortable",
      update: function ( event, tr ) {
        bwg_albums_galleries();
      }
    } );
    bwg_albums_galleries();

    setTimeout( function () {
      if ( !jQuery( '#loading_div' ).is( ':visible' ) ) {
        jQuery( '#bwg_albums #bwg_tabs' ).removeClass( 'hidden' );
      }
    }, 500 );
  }
  show_hide_compact_album_view( jQuery( '#album_view_type option:selected' ).val() );
  jQuery( document ).on( 'change', '#album_view_type', function () {
    var value = jQuery( this ).val();
    show_hide_compact_album_view( value );
  } );
  show_hide_extended_album_view( jQuery( '#album_extended_view_type option:selected' ).val() );
  jQuery( document ).on( 'change', '#album_extended_view_type', function () {
    var value = jQuery( this ).val();
    show_hide_extended_album_view( value );
  } );

  jQuery( '#bwg_ask_question' ).parent().attr( 'target', '_blank' );
  if ( jQuery( '#search_in_options_container' ).length ) {
    jQuery( window ).scroll( function () {
      if ( jQuery( window ).scrollTop() >= jQuery( 'div.wd-list-view-header' ).offset().top + 150 ) {
        jQuery( '#search_in_tablet' ).addClass( 'fixed' );
        jQuery( '#search_in_options_container' ).addClass( 'fixed' );
        jQuery( '#search_in_options_container' ).css( "width", +jQuery( '#search_in_options_container' ).parent().width() - jQuery( '#search_in_options_container' ).css( 'marginLeft' ).replace( 'px', '' ) - jQuery( '#search_in_options_container' ).css( 'marginRight' ).replace( 'px', '' ) );
        jQuery( '#search_in_tablet' ).css( "width", +jQuery( '#search_in_tablet' ).parent().width() - jQuery( '#search_in_tablet' ).css( 'marginLeft' ).replace( 'px', '' ) - jQuery( '#search_in_tablet' ).css( 'marginRight' ).replace( 'px', '' ) );
      }
      else {
        jQuery( '#search_in_options_container' ).removeClass( 'fixed' );
        jQuery( '#search_in_tablet' ).removeClass( 'fixed' );
      }
    } );
  }
  jQuery( '.tabs' ).click( function () {
    search_options();
  } );
  jQuery( '.search_in_options:visible' ).keydown( function ( e ) {
    var shifted = false;
    if ( e.key == 'ArrowLeft' ) {
      jQuery( '.search_prev:visible' ).click();
    }
    else if ( e.key == 'ArrowRight' ) {
      jQuery( '.search_next:visible' ).click();
    }
    else if ( e.key == 'Enter' ) {
      e.preventDefault();
      return;
    }
  } );
  jQuery( '.search_in_options:visible' ).keyup( function ( e ) {
    var w_key = e.key;
    if ( ( typeof w_key == 'string' && w_key.length == 1 ) || !w_key || w_key == 'Backspace') {
      search_options();
    }
    else if ( w_key == 'Enter' ) {
      if ( e.shiftKey ) {
        jQuery( '.search_prev:visible' ).click();
      }
      else {
        jQuery( '.search_next:visible' ).click();
      }
    }
    else {
      return;
    }
  } );
  jQuery( '.search_next' ).click( function () {
    search_get_current( 'search_next' );
  } )
  jQuery( '.search_prev' ).click( function () {
    search_get_current( 'search_prev' );
  } )
  jQuery( '.search_close' ).on( 'click', function () {
    jQuery( '.search_in_options:visible' ).val( '' );
    search_options();
  } )
  jQuery( '.search_prev' ).hide();
  jQuery( '.search_next' ).hide();
  jQuery( '.search_in_options' ).val( '' );
  jQuery( '.total_matches' ).hide();
  jQuery( '.current_match' ).empty();
  jQuery( '.search_close' ).hide();


  /* images in select list */

  /* change selected view*/
  jQuery( '#bwg_options_form .bwg-gallery-ul li' ).click( function () {
    if ( jQuery( this ).hasClass( 'gallery-type-li' ) ) {
      jQuery( '.type-selected' ).removeClass( 'type-selected' );
      jQuery( this ).addClass( 'type-selected' );
      var value = jQuery( this ).data( 'value' );
      var item = jQuery( this ).clone();
      var parent_el = jQuery( this ).parent().parent().prev( '.bwg-btn-gallery-type-select' ).attr( 'id' );
      jQuery( '#' + parent_el ).html( item );
      jQuery( '#' + parent_el ).attr( 'value', value );
      if ( parent_el == 'album-view-type' ) {
        bwg_album_type_options( value );
        jQuery( '#album_types_name' ).val( value );

      }
      else {
        bwg_gallery_type_options( value );
        jQuery( '#gallery_types_name' ).val( value );
      }
      change( parent_el );
    }
  } );

  jQuery( 'body' ).click( function () {
    jQuery( "#bwg_options_form .bwg-btn-gallery-type-select" ).each( function () {
      if ( jQuery( this ).hasClass( "type-opened" ) ) {
        jQuery( this ).removeClass( "type-opened" );
        jQuery( this ).addClass( "type-closed" );
        jQuery( this ).next( ".bwg-gallery-ul-div" ).toggle();
      }
    } )
  } );

  /* functions to view div as select box */
  jQuery( '#bwg_options_form .bwg-btn-gallery-type-select' ).click( function () {
    var id = jQuery( this ).attr( 'id' );
    if ( !jQuery( this ).next().find( '.bwg-gallery-ul .type-selected' ).length ) {
      jQuery( this ).next().find( '.bwg-gallery-ul li:first-child' ).addClass( 'type-selected' );
    }
    change( id );
  } );

} );

function change( view_type ) {
  var view_type_div = jQuery( '#' + view_type ).closest( '.bwg-btn-gallery-type-select' );
  if ( view_type_div.hasClass( 'type-closed' ) ) {
    view_type_div.removeClass( 'type-closed' );
    view_type_div.addClass( 'type-opened' );
  }
  else {
    view_type_div.removeClass( 'type-opened' );
    view_type_div.addClass( 'type-closed' );
  }
  jQuery( '#' + view_type ).next( '.bwg-gallery-ul-div' ).toggle();
  event.stopPropagation();
}

/* Load gallery images */
function bwg_lazy_load_gallery() {
  jQuery( ".gallery_image_thumb" ).each( function () {
    var currImg = jQuery( this );
    var src = currImg.attr( "data-src" );
    if ( typeof src != "undefined" && src.length > 0 ) {
      currImg.attr( "src", src );
      currImg.removeAttr( "data-src" );
      currImg.on( "load", function () {
        currImg.removeClass( "bwg_no_border" );
      } );
    }
  } );
}

function bwg_albums_galleries() {
  var str = '';
  jQuery( "#bwg_tabs>.connectedSortable" ).each( function () {
    str += jQuery( this ).data( 'id' ) + ':' + jQuery( this ).data( 'is-album' ) + ',';
  } );
  jQuery( "#albums_galleries" ).val( str );
}

function bwg_remove_album_gallery( obj ) {
  jQuery( obj ).closest( ".connectedSortable" ).remove();
  bwg_albums_galleries();
}

function bwg_add_album_gallery( alb_gal_id, is_album, preview_image, name, status, tb_remove ) {
  var html = jQuery( '#bwg_template' ).html()
    .replace( /%%alb_gal_id%%/g, alb_gal_id )
    .replace( /%%is_album%%/g, is_album )
    .replace( /%%preview_image%%=""/g, 'style="background-image:url(&quot;' + preview_image + '&quot;)"' )
    .replace( /%%name%%/g, name )
    .replace( /%%status%%/g, status );
  jQuery( '#bwg_tabs' ).children( '#bwg_template' ).last().before( html );
  bwg_albums_galleries();
  if ( tb_remove != false ) {
    window.parent.tb_remove();
  }
}

function spider_get_items() {
  jQuery( '#tbody_albums_galleries input[type=checkbox]' ).each( function () {
    obj = jQuery( this );
    if ( obj.prop( 'checked' ) ) {
      window.parent.bwg_add_album_gallery( obj.attr( 'data-id' ), obj.attr( 'data-is-album' ), obj.attr( 'data-preview-image' ), obj.attr( 'data-name' ), obj.attr( 'data-status' ), false );
    }
  } );
  window.parent.tb_remove();
}

function addPricelist( pricelist ) {
  jQuery( '#image_pricelist_id', window.parent.document ).val( pricelist.id );
  window.parent.spider_set_input_value( 'ajax_task', 'set_image_pricelist' );
  window.parent.spider_ajax_save( 'bwg_gallery' );
  window.parent.tb_remove();
}

function bwg_remove_pricelist( obj ) {
  jQuery( "#remove_pricelist" ).val( jQuery( obj ).attr( "data-image-id" ) );
  jQuery( "#pricelist_id_" + jQuery( obj ).attr( "data-pricelist-id" ) ).val( "" );
  spider_set_input_value( 'ajax_task', 'remove_image_pricelist' );
  spider_ajax_save( 'bwg_gallery' );
}

var bwg_save_count = 50;

/**
 * Save gallery and images.
 *
 * @param form_id
 * @param tr_group Save counter.
 * @returns {boolean}
 */
function spider_ajax_save( form_id, tr_group ) {
  if ( spider_check_required( 'name', 'Name' ) ) {
    return false;
  }
  var post_data = {};
  post_data[ "task" ] = "save";
  var ajax_task = jQuery( "#ajax_task" ).val(); /* Images list action task.*/
  post_data[ "current_id" ] = jQuery( "#current_id" ).val(); /* Current gallery id.*/
  post_data[ "image_current_id" ] = jQuery( "#image_current_id" ).val(); /* Current image id.*/
  var ids_string = jQuery( "#ids_string" ).val(); /* Images ids separated by comma.*/
  ids_string = ids_string.replace( /,\s*$/, "" );
  post_data[ "image_bulk_action" ] = jQuery( "[name=image_bulk_action]" ).val(); /* Bulk action for images.*/
  post_data[ "order_by" ] = jQuery( "select[name='order_by']" ).val(); /* Images sorting.*/
  post_data[ "s" ] = jQuery( "input[name='s']" ).val(); /* Images filter.*/
  post_data[ "paged" ] = jQuery( "#paged" ).val(); /* Images page number.*/
  post_data[ "bwg_nonce" ] = jQuery( "#bwg_nonce" ).val(); /* Nonce*/
  post_data[ "image_pricelist_id" ] = jQuery( "#image_pricelist_id" ).val();
  post_data[ "remove_pricelist" ] = jQuery( "#remove_pricelist" ).val();

  /* Images ids array. */
  var ids_array = ids_string.split( "," );
  /* Images count on page. */
  var tr_count = ids_array.length;

  if ( !tr_group ) {
    var tr_group = 1;
  }

  /* Selected images count for message.*/
  post_data[ "checked_items_count" ] = jQuery( "[name^=check]:not([id=check_all_items]):checked" ).length;
  /* Select all.*/
  post_data[ "check_all_items" ] = jQuery( "[name=check_all_items]" ).is( ":checked" ) ? 1 : 0;
  var limit = ( ajax_task == 'image_set_watermark' || ajax_task == 'image_reset' || ajax_task == 'image_recreate_thumbnail' || ajax_task == 'image_resize' || ajax_task == 'image_rotate_left' || ajax_task == 'image_rotate_right' || ajax_task == 'image_edit_alt' || ajax_task == 'image_edit_description' || ajax_task == 'image_edit_redirect' || ajax_task == 'image_add_tag' || ajax_task == 'image_publish' || ajax_task == 'image_delete' || ajax_task == 'image_unpublish') && ( post_data[ "check_all_items" ] || tr_count > bwg_save_count ) ? bwg_save_count * ( tr_group - 1 ) : false;
  post_data[ "limit" ] = limit;
  /* Gallery paramters. */
  post_data[ "name" ] = jQuery( "#name" ).val();
  post_data[ "slug" ] = jQuery( "#slug" ).val();
  post_data[ "old_slug" ] = jQuery( "#old_slug" ).val();
  post_data[ "preview_image" ] = jQuery( "#preview_image" ).val();
  post_data[ "published" ] = jQuery( "input[name=published]:checked" ).val();
  if ( ( typeof tinyMCE != "undefined" ) &&
    tinyMCE.activeEditor &&
    !tinyMCE.activeEditor.isHidden() &&
    tinyMCE.activeEditor.getContent ) {
    post_data[ "description" ] = tinyMCE.activeEditor.getContent();
  }
  else {
    post_data[ "description" ] = jQuery( "#description" ).val();
  }
  var gallery_type_input = jQuery( "#gallery_type" ).val();
  post_data[ "gallery_source" ] = ( gallery_type_input == 'facebook' ) ? jQuery( "#facebook_gallery_source" ).val() : jQuery( "#gallery_source" ).val();
  post_data[ "autogallery_image_number" ] = ( gallery_type_input == 'facebook' ) ? jQuery( "#facebook_gallery_image_limit" ).val() : jQuery( "#autogallery_image_number" ).val();
  post_data[ "update_flag" ] = ( gallery_type_input == 'facebook' ) ? jQuery( "input[name=facebook_update]:checked" ).val() : jQuery( "input[name=update_flag]:checked" ).val();
  var gallery_content_type = ( gallery_type_input == 'facebook' ) ? jQuery( "input[name=facebook_content_type]:checked" ).val() : jQuery( "input[name=instagram_post_gallery]:checked" ).val();
  post_data[ "gallery_type" ] = gallery_type_input + ( gallery_content_type == 1 ? "_post" : "" );
  post_data[ "gallery_type_old" ] = jQuery( "#gallery_type_old" ).val();
  post_data[ "instagram_post_gallery" ] = gallery_content_type;
  post_data[ "modified_date" ] = jQuery( "#modified_date" ).val();

  /* Remove images ids from begin and end of array. */
  if ( tr_count > bwg_save_count ) {
    ids_array.splice( tr_group * bwg_save_count, ids_array.length );
    ids_array.splice( 0, ( tr_group - 1 ) * bwg_save_count );
    ids_string = ids_array.join( "," );
  }

  post_data[ "ajax_task" ] = ajax_task;
  post_data[ "ids_string" ] = ids_string;

  /* Images dimensions to resize. */
  post_data[ "image_width" ] = jQuery( "#image_width" ).val();
  post_data[ "image_height" ] = jQuery( "#image_height" ).val();
  /* Images bulk edit values. */
  post_data[ "title" ] = jQuery( "#title" ).val();
  post_data[ "desc" ] = jQuery( "#desc" ).val();
  post_data[ "redirecturl" ] = jQuery( "#redirecturl" ).val();
  /* Images bulk add tags ids. */
  post_data[ "added_tags_id" ] = jQuery( "#added_tags_id" ).val();
  /* Images bulk add tags act. */
  post_data[ "added_tags_act" ] = jQuery( "#added_tags_act" ).val();
  /* Images data. */
  for ( var i in ids_array ) {
    if ( ids_array.hasOwnProperty(i) && ids_array[i] ) {
      var filetype = jQuery("#input_filetype_" + ids_array[i]).val();
      if ( jQuery("#check_" + ids_array[i]).prop('checked') == true ) {
        post_data["check_" + ids_array[i]] = true; /* jQuery("#check_" + ids_array[i]).val(); */
      }
      if ( filetype == 'EMBED_OEMBED_INSTAGRAM_POST' ) {
        post_data["image_url_" + ids_array[i]] = jQuery("#image_url_" + ids_array[i]).val();
      }
      else {
        post_data["image_url_" + ids_array[i]] = decodeURIComponent(jQuery("#image_url_" + ids_array[i]).val());
      }
      post_data["thumb_url_" + ids_array[i]] = decodeURIComponent(jQuery("#thumb_url_" + ids_array[i]).val());
      post_data["input_filename_" + ids_array[i]] = jQuery("#input_filename_" + ids_array[i]).val();
      post_data["image_description_" + ids_array[i]] = (typeof jQuery("#image_description_" + ids_array[i]).val() !== 'undefined' && jQuery("#image_description_" + ids_array[i]).val()) ? jQuery("#image_description_" + ids_array[i]).val() : '';
      post_data["image_alt_text_" + ids_array[i]] = (typeof jQuery("#image_alt_text_" + ids_array[i]).val() !== 'undefined' && jQuery("#image_alt_text_" + ids_array[i]).val()) ? jQuery("#image_alt_text_" + ids_array[i]).val() : '';
      post_data["redirect_url_" + ids_array[i]] = jQuery("#redirect_url_" + ids_array[i]).val();
      post_data["input_date_modified_" + ids_array[i]] = jQuery("#input_date_modified_" + ids_array[i]).val();
      post_data["input_size_" + ids_array[i]] = jQuery("#input_size_" + ids_array[i]).val();
      post_data["input_filetype_" + ids_array[i]] = filetype;
      post_data["input_resolution_" + ids_array[i]] = jQuery("#input_resolution_" + ids_array[i]).val();
      post_data["input_resolution_thumb_" + ids_array[i]] = jQuery("#input_resolution_thumb_" + ids_array[i]).val();
      post_data["input_crop_" + ids_array[i]] = jQuery("#input_crop_" + ids_array[i]).val();
      post_data["order_input_" + ids_array[i]] = jQuery("#order_input_" + ids_array[i]).val();
      post_data["tags_" + ids_array[i]] = jQuery("#tags_" + ids_array[i]).val();
    }
  }
  /* Filter data before passing to ajax from add-ons. */
  jQuery( document ).trigger( 'bwg_before_gallery_save_ajax', post_data );

  /* Loading. */
  jQuery( "#loading_div" ).show();

  jQuery.post(
    jQuery( '#' + form_id ).attr( 'action' ),
    post_data,
    function ( data ) {
      var str = jQuery( data ).find( "#current_id" ).val();
      if ( typeof str != "undefined" ) {
        jQuery( "#current_id" ).val( str );
      }
    }
  ).success( function ( data, textStatus, errorThrown ) {
    if ( tr_count > bwg_save_count * tr_group || ( limit !== false && limit < jQuery( "#total" ).val() ) ) {
      spider_ajax_save( form_id, ++tr_group );
      return;
    }
    else {
      var form_action = jQuery( data ).find( '#bwg_gallery' ).attr( "action" );
      /* Something went wrong.*/
      if ( typeof form_action == "undefined" ) {
        jQuery( "#loading_div" ).hide();
        return;
      }
      jQuery( '#bwg_gallery' ).attr( "action", form_action );
      /*
      var str = jQuery(data).find('#bwg_gallery').html();
      jQuery('#bwg_gallery').html(str);
      var current_id = jQuery(data).find("#current_id").val();
      window.history.pushState(null, null, window.location.href + '&current_id=' + current_id);
      */
      var str = jQuery( data ).find( '.bwg-page-header' ).html();
      jQuery( '.bwg-page-header' ).html( str );
      var str = jQuery( data ).find( '.ajax-msg' ).html();
      jQuery( '.ajax-msg' ).html( str );
      jQuery( ".ajax-msg" ).addClass( "wd-hide" );
      var str = jQuery( data ).find( '.gal-msg' ).html();
      jQuery( '.gal-msg' ).html( str );
      var str = jQuery( data ).find( '.tablenav.top' ).html();
      jQuery( '.tablenav.top' ).html( str );
      var str = jQuery( data ).find( '#images_table' ).html();
      jQuery( '#images_table' ).html( str );
      var str = jQuery( data ).find( '.tablenav.bottom' ).html();
      jQuery( '.tablenav.bottom' ).html( str );
      var str = jQuery( data ).find( '.wd-hidden-values' ).html();
      jQuery( '.wd-hidden-values' ).html( str );
      var str = jQuery( data ).find( '#task' ).html();
      jQuery( '#task' ).html( str );
      var str = jQuery( data ).find( '#current_id' ).html();
      jQuery( '#current_id' ).html( str );

      if ( ajax_task != '' ) {
        jQuery( ".ajax-msg" ).removeClass( "wd-hide" );
      }
      jQuery( ".gal-msg" ).removeClass( "wd-hide" );

      jQuery( ".unsaved-msg" ).addClass( "wd-hide" );
      if ( jQuery( "#tbody_arr" ).hasClass( "bwg-ordering" ) ) {
        wd_showhide_weights();
      }
      wd_pagination();
      /* bwg_toggle_postbox();*/

      jQuery( "#check_all" ).on( "click", function () {
        spider_check_all( "#check_all" );
      } );
      jQuery( "#loading_div" ).hide();
      bwg_lazy_load_gallery();
      wd_howto_src_change();

      /* Add click event to toggle button to expand columns.*/
      jQuery( "tbody" ).on( "click", ".toggle-row", function () {
        jQuery( this ).closest( "tr" ).toggleClass( "is-expanded" );
      } );

      /* Change the popup dimensions. */
      bwg_tb_window( "#images_table" );

      /* Show popup for install manager if first gallery inserted */
      var popup_status = jQuery( data ).find( '#twbb_layout' ).attr( "data-status" );
      if ( popup_status == 1 ) {
        var win_height = jQuery( window ).height();
        if ( win_height < 500 ) {
          jQuery( "#twbb_layout_container" ).css( 'height', ( win_height - 35 ) );
        }
        jQuery( "#twbb_layout" ).removeClass( "hide" );
      }
    }
  } );

  return false;
}

function bwg_sort_images( sorting ) {
  var msg = jQuery( '.sorting-msg' );
  if ( sorting != 'order_asc' ) {
    msg.removeClass( 'wd-hide' );
  }
  else {
    msg.addClass( 'wd-hide' );
  }
  spider_set_input_value( 'task', 'save' );
  spider_ajax_save( 'bwg_gallery' );
}

/* Set value by id. */
function spider_set_input_value( input_id, input_value ) {
  if ( document.getElementById( input_id ) ) {
    document.getElementById( input_id ).value = input_value;
  }
}

/* Submit form by id. */
function spider_form_submit( event, form_id ) {
  if ( document.getElementById( form_id ) ) {
    document.getElementById( form_id ).submit();
  }
  if ( event.preventDefault ) {
    event.preventDefault();
  }
  else {
    event.returnValue = false;
  }
}

/* Check if required field is empty. */
function spider_check_required( id, name ) {
  if ( jQuery( '#' + id ).val() == '' ) {
    alert( name + ' ' + bwg_objectL10B.bwg_field_required );
    jQuery( '#' + id ).attr( 'style', 'border-color: #FF0000;' );
    jQuery( '#' + id ).focus();
    jQuery( 'html, body' ).animate( {
      scrollTop: jQuery( '#' + id ).offset().top - 200
    }, 500 );
    return true;
  }
  else {
    return false;
  }
}

/**
 * Show/hide order inputs/drag and drop columns.
 *
 * @param click
 */
function wd_showhide_weights( click ) {
  if ( typeof click == "undefined" ) {
    var click = false;
  }
  if ( jQuery( "select[name='order_by']" ).val() == 'order_asc' ) {
    if ( click ) {
      jQuery( ".wd-order" ).toggleClass( "wd-hide" );
      jQuery( ".wd-drag" ).toggleClass( "wd-hide" );
    }
  }
  else {
    jQuery( ".wd-order" ).removeClass( "wd-hide" );
    jQuery( ".wd-drag" ).addClass( "wd-hide" );
  }

  if ( !jQuery( ".wd-drag" ).hasClass( "wd-hide" ) ) { /* Drag and drop. */
    jQuery( ".wd-order-thead" ).attr( "title", bwg_objectL10B.bwg_show_order );
    jQuery( "#tbody_arr" ).sortable( {
      handle: ".connectedSortable",
      connectWith: ".connectedSortable",
      update: function ( event, tr ) {
        jQuery( ".unsaved-msg" ).removeClass( "wd-hide" );
        jQuery( ".ajax-msg" ).addClass( "wd-hide" );
        var i;
        if (jQuery( "td.col_drag" ).data( "page-number" ) == 0) {
          i = -jQuery( ".wd-order" ).length;
        } else {
          i = jQuery( "td.col_drag" ).data( "page-number" );
        }
        jQuery( ".wd-order" ).each( function () {
          jQuery( this ).val( ++i );
        } );
      }
    } );
  }
  else { /* Order inputs. */
    jQuery( ".wd-order-thead" ).attr( "title", bwg_objectL10B.bwg_hide_order );
  }
}

/*jQuery(".wd-check-all").on("click", function () {
  jQuery("#check_all").trigger("click");
  var checkbox = jQuery("#check_all_items");
  if (checkbox.is(":checked")) {
    checkbox.attr("checked", false);
  }
  else {
    checkbox.attr("checked", true);
  }
});*/

/* Check all items. */
function spider_check_all_items( event ) {
  if ( jQuery( "#check_all_items" ).is( ':checked' ) ) {
    jQuery( "#check_all_items" ).prop( 'checked', false );
  }
  else {
    jQuery( "#check_all_items" ).prop( 'checked', true );
  }
  spider_check_all_items_checkbox( event );
}

function spider_check_all_items_checkbox( event ) {
  if ( jQuery( "#check_all_items" ).is( ':checked' ) ) {
    /* Generate message about how many images are selected. */
    var saved_items = ( parseInt( jQuery( ".displaying-num" ).html() ) ? parseInt( jQuery( ".displaying-num" ).html() ) : 0 );
    var added_items = ( jQuery( 'input[id^="check_pr_"]' ).length ? parseInt( jQuery( 'input[id^="check_pr_"]' ).length ) : 0 );
    var items_count = added_items + saved_items;
    if ( items_count ) {
      jQuery( ".ajax-msg" )
        .html( "<div class='notice notice-warning wd-notice'><p><strong>" + ( items_count == 1 ? bwg_objectL10B.selected_item : bwg_objectL10B.selected_items ).replace( "%d", items_count ) + "</strong></p></div>" )
        .removeClass( "wd-hide" );
    }

    if ( !jQuery( "#check_all" ).is( ':checked' ) ) {
      jQuery( '#check_all' ).trigger( 'click' );
    }
  }
  else {
    if ( jQuery( "#check_all" ).is( ':checked' ) ) {
      jQuery( '#check_all' ).trigger( 'click' );
    }
  }
  event.stopPropagation();
}

function spider_check_all( current ) {
  if ( !jQuery( current ).is( ':checked' ) ) {
    jQuery( '#check_all_items' ).prop( 'checked', false );
    jQuery( ".ajax-msg" ).addClass( "wd-hide" );
  }
}

/* Set uploader to button class. */
function spider_uploader( button_id, input_id, delete_id, img_id ) {
  if ( typeof img_id == 'undefined' ) {
    img_id = '';
  }
  jQuery( function () {
    var formfield = null;
    window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function ( html ) {
      if ( formfield ) {
        var fileurl = jQuery( 'img', html ).attr( 'src' );
        if ( !fileurl ) {
          var exploded_html;
          var exploded_html_askofen;
          exploded_html = html.split( '"' );
          for ( i = 0; i < exploded_html.length; i++ ) {
            exploded_html_askofen = exploded_html[ i ].split( "'" );
          }
          for ( i = 0; i < exploded_html.length; i++ ) {
            for ( j = 0; j < exploded_html_askofen.length; j++ ) {
              if ( exploded_html_askofen[ j ].search( "href" ) ) {
                fileurl = exploded_html_askofen[ i + 1 ];
                break;
              }
            }
          }
          if ( img_id != '' ) {
            alert( bwg_objectL10B.bwg_select_image );
            tb_remove();
            return;
          }
          window.parent.document.getElementById( input_id ).value = fileurl;
          window.parent.document.getElementById( button_id ).style.display = "none";
          window.parent.document.getElementById( input_id ).style.display = "inline-block";
          window.parent.document.getElementById( delete_id ).style.display = "inline-block";
        }
        else {
          if ( img_id == '' ) {
            alert( bwg_objectL10B.bwg_field_required );
            tb_remove();
            return;
          }
          window.parent.document.getElementById( input_id ).value = fileurl;
          window.parent.document.getElementById( button_id ).style.display = "none";
          window.parent.document.getElementById( delete_id ).style.display = "inline-block";
          if ( ( img_id != '' ) && window.parent.document.getElementById( img_id ) ) {
            window.parent.document.getElementById( img_id ).src = fileurl;
            window.parent.document.getElementById( img_id ).style.display = "inline-block";
          }
        }
        formfield.val( fileurl );
        tb_remove();
      }
      else {
        window.original_send_to_editor( html );
      }
      formfield = null;
    };
    formfield = jQuery( this ).parent().parent().find( ".url_input" );
    tb_show( '', 'media-upload.php?type=image&TB_iframe=true' );
    jQuery( '#TB_overlay,#TB_closeWindowButton' ).bind( "click", function () {
      formfield = null;
    } );
    return false;
  } );
}

/* Remove uploaded file. */
function spider_remove_url( button_id, input_id, delete_id, img_id ) {
  if ( typeof img_id == 'undefined' ) {
    img_id = '';
  }
  if ( document.getElementById( button_id ) ) {
    document.getElementById( button_id ).style.display = '';
  }
  if ( document.getElementById( input_id ) ) {
    document.getElementById( input_id ).value = '';
    document.getElementById( input_id ).style.display = 'none';
  }
  if ( document.getElementById( delete_id ) ) {
    document.getElementById( delete_id ).style.display = 'none';
  }
  if ( ( img_id != '' ) && window.parent.document.getElementById( img_id ) ) {
    document.getElementById( img_id ).src = '';
    document.getElementById( img_id ).style.display = 'none';
  }
}

/* Add album preview image. */
function bwg_add_preview_image( files ) {
  document.getElementById( "preview_image" ).value = files[ 0 ][ 'thumb_url' ];
  document.getElementById( "button_preview_image" ).style.display = "none";
  document.getElementById( "delete_preview_image" ).style.display = "inline-block";
  if ( document.getElementById( "img_preview_image" ) ) {
    document.getElementById( "img_preview_image" ).src = files[ 0 ][ 'reliative_url' ];
    document.getElementById( "img_preview_image" ).style.display = "inline-block";
  }
}

function spider_reorder_items( tbody_id ) {
  jQuery( "#" + tbody_id ).sortable( {
    handle: ".connectedSortable",
    connectWith: ".connectedSortable",
    update: function ( event, tr ) {
      spider_sortt( tbody_id );
    }
  } );
}

function spider_sortt( tbody_id ) {
  var str = "";
  var counter = 0;
  jQuery( "#" + tbody_id ).children().each( function () {
    str += ( ( jQuery( this ).attr( "id" ) ).substr( 3 ) + "," );
    counter++;
  } );
  jQuery( "#albums_galleries" ).val( str );
  if ( !counter ) {
    document.getElementById( "table_albums_galleries" ).style.display = "none";
  }
}

function spider_remove_row( tbody_id, event, obj ) {
  var span = obj;
  var tr = jQuery( span ).closest( "tr" );
  jQuery( tr ).remove();
  spider_sortt( tbody_id );
}

function spider_jslider( idtaginp ) {
  jQuery( function () {
    var inpvalue = jQuery( "#" + idtaginp ).val();
    if ( inpvalue == "" ) {
      inpvalue = 50;
    }
    jQuery( "#slider-" + idtaginp ).slider( {
      range: "min",
      value: inpvalue,
      min: 1,
      max: 100,
      slide: function ( event, ui ) {
        jQuery( "#" + idtaginp ).val( "" + ui.value );
      }
    } );
    jQuery( "#" + idtaginp ).val( "" + jQuery( "#slider-" + idtaginp ).slider( "value" ) );
  } );
}

/**
 * Bulk add selected tags to images.
 *
 * @param image_id
 */
function bwg_bulk_add_tags( tag_id, act ) {
  var tagIds = "";
  if ( tag_id == "" ) {
    jQuery( ".tags:checked" ).each( function () {
      tagIds += jQuery( this ).data( "id" ).toString() + ",";
    } );
  }
  else {
    tagIds = tag_id;
  }
  jQuery( '#added_tags_id', window.parent.document ).val( tagIds );
  jQuery( '#added_tags_act', window.parent.document ).val( act );
  window.parent.spider_set_input_value( 'ajax_task', 'image_add_tag' );
  window.parent.spider_ajax_save( 'bwg_gallery' );
  window.parent.tb_remove();
}

/**
 * Add selected tags to image.
 *
 * @param image_id
 */
function bwg_add_tags( image_id ) {
  var tagIds = [];
  var titles = [];
  jQuery( ".tags:checked" ).each( function () {
    tagIds.push( jQuery( this ).data( "id" ).toString() );
    titles.push( jQuery( this ).data( "name" ) );
  } );
  window.parent.bwg_add_tag( image_id, tagIds, titles );
}

/**
 * Add tag to image.
 *
 * @param image_id
 * @param tagIds
 * @param titles
 */
function bwg_add_tag( image_id, tagIds, titles ) {
  window.parent.bwg_create_loading_block();
  /* Images ids array. */
  var ids_array;
  if ( image_id == '0' ) {
    var flag = false;
    var ids_string = jQuery( "#ids_string" ).val();
    ids_array = ids_string.split( "," );
    if ( jQuery( "#check_all_items" ).attr( "checked" ) ) {
      var added_tags = '';
      for ( i = 0; i < tagIds.length; i++ ) {
        added_tags = added_tags + tagIds[ i ] + ',';
      }
      jQuery( "#added_tags_id" ).val( added_tags );
    }
  }
  else {
    image_id = image_id + ',';

    ids_array = image_id.split( "," );
    var flag = true;
  }
  for ( var i in ids_array ) {
    if ( ids_array.hasOwnProperty( i ) && ids_array[ i ] ) {
      if ( jQuery( "#check_" + ids_array[ i ] ).prop( 'checked' ) || flag ) {
        image_id = ids_array[ i ];
        var tag_ids = document.getElementById( 'tags_' + image_id ).value;
        tags_array = tag_ids.split( ',' );
        var counter = 0;
        for ( i = 0; i < tagIds.length; i++ ) {
          if ( tags_array.indexOf( tagIds[ i ] ) == -1 ) { /* To prevent add same tag multiple times. */
            tag_ids = tag_ids + tagIds[ i ] + ',';
            var html = jQuery( "#" + image_id + "_tag_temptagid" ).clone().html();
            /* Remove white spaces from keywords to set as id and remove prefix.*/
            var id = tagIds[ i ].replace( /\s+/g, '_' ).replace( 'bwg_', '' ).replace( /\//g, "" ).replace( /&amp;/g, "" ).replace( /&/g, "" ).replace( /@/g, "" ).replace( /'/g, "39" ).replace( /"/g, "34" ).replace( /!/g, "" ).replace(".", "");
            html = html.replace( /temptagid/g, id )
              .replace( /temptagname/g, titles[ i ] );
            jQuery( "#tags_div_" + image_id ).append( "<div class='tag_div' id='" + image_id + "_tag_" + id + "'>" );
            jQuery( "#" + image_id + "_tag_" + id ).html( html );

            counter++;
          }
        }
        document.getElementById( 'tags_' + image_id ).value = tag_ids;
        if ( counter ) {
          jQuery( "#tags_div_" + image_id ).parent().removeClass( "tags_div_empty" );
        }
        else {
          jQuery( "#tags_div_" + image_id ).parent().addClass( "tags_div_empty" );
        }
      }
    }
  }
  jQuery( ".unsaved-msg", window.parent.document ).removeClass( "wd-hide" );
  jQuery( ".ajax-msg", window.parent.document ).addClass( "wd-hide" );
  tb_remove();
  window.parent.bwg_remove_loading_block();
}

function bwg_remove_tag( tag_id, image_id ) {
  if ( jQuery( '#' + image_id + '_tag_' + tag_id ) ) {
    jQuery( '#' + image_id + '_tag_' + tag_id ).remove();
    var tag_ids_string = jQuery( "#tags_" + image_id ).val();
    tag_ids_string = tag_ids_string.replace( tag_id + ',', '' );
    jQuery( "#tags_" + image_id ).val( tag_ids_string );
    if ( jQuery( "#tags_" + image_id ).val() == '' ) {
      jQuery( "#tags_div_" + image_id ).parent().addClass( "tags_div_empty" );
    }
    jQuery( ".unsaved-msg" ).removeClass( "wd-hide" );
    jQuery( ".ajax-msg" ).addClass( "wd-hide" );
  }
}

function bwg_remove_tags( image_id ) {
  var tagIds = [];
  jQuery( ".tags:checked" ).each( function () {
    tagIds.push( jQuery( this ).data( "id" ).toString() );
  } );
  tagIds.forEach( function ( item ) {
    window.parent.bwg_remove_tag( item.toString(), image_id );
  } )
  window.parent.tb_remove();
}

function preview_watermark() {
  setTimeout( function () {
    watermark_type = window.parent.document.getElementById( 'watermark_type_text' ).checked;
    if ( watermark_type ) {
      watermark_text = document.getElementById( 'watermark_text' ).value;
      watermark_link = document.getElementById( 'watermark_link' ).value;
      watermark_font_size = document.getElementById( 'watermark_font_size' ).value;
      watermark_font = document.getElementById( 'watermark_font' ).value;
      watermark_color = document.getElementById( 'watermark_color' ).value;
      watermark_opacity = document.getElementById( 'watermark_opacity' ).value;
      watermark_position = jQuery( "input[name=watermark_position]:checked" ).val().split( '-' );
      document.getElementById( "preview_watermark" ).style.verticalAlign = watermark_position[ 0 ];
      document.getElementById( "preview_watermark" ).style.textAlign = watermark_position[ 1 ];
      stringHTML = ( watermark_link ? '<a href="' + watermark_link + '" target="_blank" style="text-decoration: none;' : '<span style="cursor:default;' ) + 'margin:4px;font-size:' + watermark_font_size + 'px;font-family:' + watermark_font + ';color:#' + watermark_color + ';opacity:' + ( watermark_opacity / 100 ) + ';" class="non_selectable">' + watermark_text + ( watermark_link ? '</a>' : '</span>' );
      document.getElementById( "preview_watermark" ).innerHTML = stringHTML;
    }
    watermark_type = window.parent.document.getElementById( 'watermark_type_image' ).checked;
    if ( watermark_type ) {
      watermark_url = document.getElementById( 'watermark_url' ).value;
      watermark_link = document.getElementById( 'watermark_link' ).value;
      watermark_width = document.getElementById( 'watermark_width' ).value;
      watermark_height = document.getElementById( 'watermark_height' ).value;
      watermark_opacity = document.getElementById( 'watermark_opacity' ).value;
      watermark_position = jQuery( "input[name=watermark_position]:checked" ).val().split( '-' );
      document.getElementById( "preview_watermark" ).style.verticalAlign = watermark_position[ 0 ];
      document.getElementById( "preview_watermark" ).style.textAlign = watermark_position[ 1 ];
      stringHTML = ( watermark_link ? '<a href="' + watermark_link + '" target="_blank">' : '' ) + '<img class="non_selectable" src="' + watermark_url + '" style="margin:0 4px 0 4px;max-width:' + watermark_width + 'px;max-height:' + watermark_height + 'px;opacity:' + ( watermark_opacity / 100 ) + ';" />' + ( watermark_link ? '</a>' : '' );
      document.getElementById( "preview_watermark" ).innerHTML = stringHTML;
    }
  }, 50 );
}

/* Escape a string for HTML.*/
function tw_escape( string ) {
  /* List of HTML entities for escaping.*/
  var htmlEscapes = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#x27;',
    '/': '&#x2F;'
  };
  /* Regex containing the keys.*/
  var htmlEscaper = /[&<>"'\/]/g;

  return ( '' + string ).replace( htmlEscaper, function ( match ) {
    return htmlEscapes[ match ];
  } );
};

function preview_built_in_watermark() {
  setTimeout( function () {
    watermark_type = window.parent.document.getElementById( 'built_in_watermark_type_text' ).checked;
    if ( watermark_type ) {
      watermark_text = tw_escape( document.getElementById( 'built_in_watermark_text' ).value );
      watermark_font_size = document.getElementById( 'built_in_watermark_font_size' ).value * 400 / 500;
      watermark_font = 'bwg_' + document.getElementById( 'built_in_watermark_font' ).value.replace( '.TTF', '' ).replace( '.ttf', '' );
      watermark_color = document.getElementById( 'built_in_watermark_color' ).value;
      watermark_opacity = document.getElementById( 'built_in_watermark_opacity' ).value;
      watermark_position = jQuery( "input[name=built_in_watermark_position]:checked" ).val().split( '-' );
      document.getElementById( "preview_built_in_watermark" ).style.verticalAlign = watermark_position[ 0 ];
      document.getElementById( "preview_built_in_watermark" ).style.textAlign = watermark_position[ 1 ];
      stringHTML = '<span style="cursor:default;margin:4px;font-size:' + watermark_font_size + 'px;font-family:' + watermark_font + ';color:#' + watermark_color + ';opacity:' + ( watermark_opacity / 100 ) + ';" class="non_selectable">' + watermark_text + '</span>';
      document.getElementById( "preview_built_in_watermark" ).innerHTML = stringHTML;
    }
    watermark_type = window.parent.document.getElementById( 'built_in_watermark_type_image' ).checked;
    if ( watermark_type ) {
      watermark_url = document.getElementById( 'built_in_watermark_url' ).value;
      watermark_size = document.getElementById( 'built_in_watermark_size' ).value;
      watermark_position = jQuery( "input[name=built_in_watermark_position]:checked" ).val().split( '-' );
      document.getElementById( "preview_built_in_watermark" ).style.verticalAlign = watermark_position[ 0 ];
      document.getElementById( "preview_built_in_watermark" ).style.textAlign = watermark_position[ 1 ];
      stringHTML = '<img class="non_selectable" src="' + watermark_url + '" style="margin:0 4px 0 4px;max-width:95%;width:' + watermark_size + '%;" />';
      document.getElementById( "preview_built_in_watermark" ).innerHTML = stringHTML;
    }
  }, 50 );
  search_options();
}

function bwg_watermark( watermark_type ) {
  jQuery( "#" + watermark_type ).prop( 'checked', true );
  jQuery( "#tr_watermark_url" ).css( 'display', 'none' );
  jQuery( "#tr_watermark_width_height" ).css( 'display', 'none' );
  jQuery( "#tr_watermark_opacity" ).css( 'display', 'none' );
  jQuery( "#tr_watermark_text" ).css( 'display', 'none' );
  jQuery( "#tr_watermark_link" ).css( 'display', 'none' );
  jQuery( "#tr_watermark_font_size" ).css( 'display', 'none' );
  jQuery( "#tr_watermark_font" ).css( 'display', 'none' );
  jQuery( "#tr_watermark_color" ).css( 'display', 'none' );
  jQuery( "#tr_watermark_position" ).css( 'display', 'none' );
  jQuery( "#tr_watermark_preview" ).css( 'display', 'none' );
  jQuery( "#preview_watermark" ).css( 'display', 'none' );
  switch ( watermark_type ) {
    case 'watermark_type_text': {
      jQuery( "#tr_watermark_opacity" ).css( 'display', '' );
      jQuery( "#tr_watermark_text" ).css( 'display', '' );
      jQuery( "#tr_watermark_link" ).css( 'display', '' );
      jQuery( "#tr_watermark_font_size" ).css( 'display', '' );
      jQuery( "#tr_watermark_font" ).css( 'display', '' );
      jQuery( "#tr_watermark_color" ).css( 'display', '' );
      jQuery( "#tr_watermark_position" ).css( 'display', '' );
      jQuery( "#tr_watermark_preview" ).css( 'display', '' );
      jQuery( "#preview_watermark" ).css( 'display', 'table-cell' );
      break;
    }
    case 'watermark_type_image': {
      jQuery( "#tr_watermark_url" ).css( 'display', '' );
      jQuery( "#tr_watermark_link" ).css( 'display', '' );
      jQuery( "#tr_watermark_width_height" ).css( 'display', '' );
      jQuery( "#tr_watermark_opacity" ).css( 'display', '' );
      jQuery( "#tr_watermark_position" ).css( 'display', '' );
      jQuery( "#tr_watermark_preview" ).css( 'display', '' );
      jQuery( "#preview_watermark" ).css( 'display', 'table-cell' );
      break;
    }
  }
}

function bwg_built_in_watermark( watermark_type ) {
  jQuery( "#built_in_" + watermark_type ).prop( 'checked', true );
  jQuery( "#tr_built_in_watermark_url" ).css( 'display', 'none' );
  jQuery( "#tr_built_in_watermark_size" ).css( 'display', 'none' );
  jQuery( "#tr_built_in_watermark_opacity" ).css( 'display', 'none' );
  jQuery( "#tr_built_in_watermark_text" ).css( 'display', 'none' );
  jQuery( "#tr_built_in_watermark_font_size" ).css( 'display', 'none' );
  jQuery( "#tr_built_in_watermark_font" ).css( 'display', 'none' );
  jQuery( "#tr_built_in_watermark_color" ).css( 'display', 'none' );
  jQuery( "#tr_built_in_watermark_position" ).css( 'display', 'none' );
  jQuery( "#tr_built_in_watermark_preview" ).css( 'display', 'none' );
  jQuery( "#preview_built_in_watermark" ).css( 'display', 'none' );
  switch ( watermark_type ) {
    case 'watermark_type_text': {
      jQuery( "#tr_built_in_watermark_opacity" ).css( 'display', '' );
      jQuery( "#tr_built_in_watermark_text" ).css( 'display', '' );
      jQuery( "#tr_built_in_watermark_font_size" ).css( 'display', '' );
      jQuery( "#tr_built_in_watermark_font" ).css( 'display', '' );
      jQuery( "#tr_built_in_watermark_color" ).css( 'display', '' );
      jQuery( "#tr_built_in_watermark_position" ).css( 'display', '' );
      jQuery( "#tr_built_in_watermark_preview" ).css( 'display', '' );
      jQuery( "#preview_built_in_watermark" ).css( 'display', 'table-cell' );
      break;
    }
    case 'watermark_type_image': {
      jQuery( "#tr_built_in_watermark_url" ).css( 'display', '' );
      jQuery( "#tr_built_in_watermark_size" ).css( 'display', '' );
      jQuery( "#tr_built_in_watermark_position" ).css( 'display', '' );
      jQuery( "#tr_built_in_watermark_preview" ).css( 'display', '' );
      jQuery( "#preview_built_in_watermark" ).css( 'display', 'table-cell' );
      break;
    }
  }
  search_options();
}

function bwg_inputs() {
  jQuery( ".spider_int_input" ).keypress( function ( event ) {
    var chCode1 = event.which || event.paramlist_keyCode;
    if ( chCode1 > 31 && ( chCode1 < 48 || chCode1 > 57 ) && ( chCode1 != 46 ) && ( chCode1 != 45 ) ) {
      return false;
    }
    return true;
  } );
}

function bwg_show_hide_roles() {
  if ( jQuery( "select[name='permissions']" ).val() == "Administrator" ) {
    jQuery( ".bwg_roles" ).hide();
  }
  else {
    jQuery( ".bwg_roles" ).show();
  }
}

function bwg_enable_disable( display, id, current ) {
  jQuery( "#" + current ).prop( 'checked', true );
  jQuery( "#" + id ).css( 'display', display );
  if ( id == 'tr_slideshow_title_position' ) {
    jQuery( "#tr_slideshow_full_width_title" ).css( 'display', display );
  }
}

function bwg_change_album_view_type( type ) {
  if ( type == 'thumbnail' ) {
    jQuery( "#album_thumb_dimensions" ).html( 'Album thumb dimensions: ' );
    jQuery( "#album_thumb_dimensions_x" ).css( 'display', '' );
    jQuery( "#album_thumb_height" ).css( 'display', '' );
  }
  else {
    jQuery( "#album_thumb_dimensions" ).html( 'Album thumb width: ' );
    jQuery( "#album_thumb_dimensions_x" ).css( 'display', 'none' );
    jQuery( "#album_thumb_height" ).css( 'display', 'none' );
  }
}

function spider_check_isnum( e ) {
  var chCode1 = e.which || e.paramlist_keyCode;
  if ( chCode1 > 31 && ( chCode1 < 48 || chCode1 > 57 ) && ( chCode1 != 46 ) && ( chCode1 != 45 ) ) {
    return false;
  }
  return true;
}

function bwg_gallery_type( instagram_client_id ) {
  var response = true;
  var value = jQuery( '#gallery_type' ).val();
  response = bwg_change_gallery_type( value, 'change', instagram_client_id );
  return response;
}

function bwg_gallery_update_flag() {
  var update_flag = jQuery( '#tr_update_flag input[name=update_flag]:checked' ).val();
  if ( update_flag == '' ) {
    jQuery( '.spider_delete_button' ).show();
    /*
    jQuery("[id^=image_alt_text_]").prop("readonly",false);
    jQuery("[id^=image_description_]").prop("readonly",false);
    jQuery("[id^=redirect_url_]").prop("readonly",false);
    */
  }
  else {
    jQuery( '.spider_delete_button' ).hide();
    /*
    jQuery("[id^=image_alt_text_]").prop("readonly", true);
    jQuery("[id^=image_description_]").prop("readonly", true);
    jQuery("[id^=redirect_url_]").prop("readonly", true);
    */
  }
}

bwg_gallery_change_update_flag = jQuery( function () {
  jQuery( '#tr_update_flag input[name=update_flag]' ).change( function () {
    bwg_gallery_update_flag();
    /*var update_flag = jQuery(this).val(); */
  } );
} );

/*returns false if user cancels or impossible to do.*/

/*
   type_to_set:'' or 'instagram'
*/
function bwg_change_gallery_type( type_to_set, warning_type, instagram_client_id ) {
  warning_type = ( typeof warning_type === "undefined" ) ? "default" : warning_type;
  jQuery( '.bwg-gallery-type-options' ).hide();
  if ( type_to_set == 'instagram' ) {
    if ( instagram_client_id == '' ) {
      alert( bwg_objectL10B.bwg_access_token );
      jQuery( '#gallery_type' ).val( '' );
      return false;
    }
    if ( !bwg_check_gallery_empty( true, true ) ) {
      return false;
    }

    jQuery( "#add_instagram_gallery" ).show();

    jQuery( '#gallery_type' ).val( 'instagram' );
    jQuery( '#tr_instagram_post_gallery' ).show();

    /*hide features of only mixed gallery*/
    jQuery( '.spider_delete_button' ).hide();
    jQuery( '#spider_resize_button' ).hide();
    jQuery( '#content-add_media' ).hide();
    jQuery( '#add_image_bwg' ).hide();
    jQuery( '#import_image_bwg' ).hide();
    jQuery( '#show_add_embed' ).hide();
    jQuery( '#show_bulk_embed' ).hide();

    /*hide unused bulk action options */
    jQuery( "#bulk-action-selector-top option[value='image_resize']" ).hide();
    jQuery( "#bulk-action-selector-top option[value='image_recreate_thumbnail']" ).hide();
    jQuery( "#bulk-action-selector-top option[value='image_rotate_left']" ).hide();
    jQuery( "#bulk-action-selector-top option[value='image_rotate_right']" ).hide();
    jQuery( "#bulk-action-selector-top option[value='image_set_watermark']" ).hide();
    jQuery( "#bulk-action-selector-top option[value='image_reset']" ).hide();
    jQuery( "#auth_google_photos_gallery" ).hide();
  }
  else if ( type_to_set == 'facebook' ) {
    if ( !bwg_check_gallery_empty( true, true ) ) {
      return false;
    }
    jQuery( '#add_facebook_gallery' ).show();

    jQuery( '#gallery_type' ).val( 'facebook' );
    jQuery( '#tr_instagram_post_gallery' ).hide();

    /*hide features of only mixed gallery*/
    jQuery( '.spider_delete_button' ).hide();
    jQuery( '#spider_resize_button' ).hide();
    jQuery( '#content-add_media' ).hide();
    jQuery( '#add_image_bwg' ).hide();
    jQuery( '#import_image_bwg' ).hide();
    jQuery( '#show_add_embed' ).hide();
    jQuery( '#show_bulk_embed' ).hide();

    /*reset update_flag radio button*/
    jQuery( "#update_flag_0" ).prop( 'checked', true );
    bwg_gallery_update_flag();
    jQuery( '#tr_update_flag' ).hide();
    jQuery( '#tr_autogallery_image_number' ).hide();
    jQuery( '#tr_instagram_gallery_add_button' ).hide();
    /* default limit 20 */
    jQuery( "#facebook_gallery_image_limit" ).val( 20 );
  }
  else if ( type_to_set == 'google_photos' ) {
    var auth_google_status = jQuery( "#auth_google_status" ).val();
    if ( auth_google_status == '0' ) {
      jQuery( "#auth_google_photos_gallery" ).show();
    }
    else {
      jQuery( "#auth_google_photos_gallery" ).hide();
      /*hide features of only mixed gallery*/
      jQuery( '.spider_delete_button' ).hide();
      jQuery( '#spider_resize_button' ).hide();
      jQuery( '#content-add_media' ).hide();
      jQuery( '#add_image_bwg' ).hide();
      jQuery( '#import_image_bwg' ).hide();
      jQuery( '#show_add_embed' ).hide();
      jQuery( '#show_bulk_embed' ).hide();
      /*hide unused bulk action options */
      jQuery( "#bulk-action-selector-top option[value='image_resize']" ).hide();
      jQuery( "#bulk-action-selector-top option[value='image_recreate_thumbnail']" ).hide();
      jQuery( "#bulk-action-selector-top option[value='image_rotate_left']" ).hide();
      jQuery( "#bulk-action-selector-top option[value='image_rotate_right']" ).hide();
      jQuery( "#bulk-action-selector-top option[value='image_set_watermark']" ).hide();
      jQuery( "#bulk-action-selector-top option[value='image_reset']" ).hide();
      jQuery( document ).trigger( 'bwg_gallery_type_changed', type_to_set );
    }
  }
  else if ( type_to_set != '' ) {
    jQuery( document ).trigger( 'bwg_gallery_type_changed', type_to_set );
  }
  else {
    var ids_string = jQuery( "#ids_string" ).val();
    ids_array = ids_string.split( "," );
    var tr_count = ids_array[ 0 ] == '' ? 0 : ids_array.length;
    if ( tr_count != 0 ) {
      switch ( warning_type ) {
        case 'default':
          var allowed = confirm( bwg_objectL10B.default_warning );
          break;
        case 'change':
          var allowed = confirm( bwg_objectL10B.change_warning );
          break;
        default:
          var allowed = confirm( bwg_objectL10B.other_warning );
      }

      if ( allowed == false ) {
        jQuery( '#gallery_type' ).val( 'instagram' );
        return false;
      }
    }

    jQuery( '#gallery_type' ).val( '' );
    jQuery( '#tr_instagram_post_gallery' ).hide();

    /*reset update_flag radio button*/
    jQuery( "#update_flag_0" ).prop( 'checked', true );
    bwg_gallery_update_flag();

    /*show features of only mixed gallery*/
    jQuery( '.spider_delete_button' ).show();
    jQuery( '#spider_resize_button' ).show();
    jQuery( '#content-add_media' ).show();
    jQuery( '#add_image_bwg' ).show();
    jQuery( '#import_image_bwg' ).show();
    jQuery( '#show_add_embed' ).show();
    jQuery( '#show_bulk_embed' ).show();

    /* Show all bulk action options*/
    jQuery( "#bulk-action-selector-top option[value='image_resize']" ).hide();
    jQuery( "#bulk-action-selector-top option[value='image_recreate_thumbnail']" ).hide();
    jQuery( "#bulk-action-selector-top option[value='image_rotate_left']" ).hide();
    jQuery( "#bulk-action-selector-top option[value='image_rotate_right']" ).hide();
    jQuery( "#bulk-action-selector-top option[value='image_set_watermark']" ).hide();
    jQuery( "#bulk-action-selector-top option[value='image_reset']" ).hide();
    jQuery( "#auth_google_photos_gallery" ).hide();
  }
  return true;
}

/*bulk embed handling*/
function bwg_bulk_embed( from, key ) {
  switch ( from ) {
    case 'instagram': {
      bwg_add_instagram_gallery( key, true );
      break;
    }
    case 'facebook': {
      var appkey = key.split( '|' );
      bwg_add_facebook_gallery( true, appkey[ 0 ], appkey[ 1 ] );
      break;
    }
  }
  return "";
}

function bwg_check_instagram_gallery_input( instagram_client_id, from_popup ) {
  from_popup = typeof from_popup !== 'undefined' ? from_popup : false;
  var is_error = false;
  if ( from_popup ) {
    if ( instagram_client_id == '' ) {
      alert( bwg_objectL10B.bwg_access_token );
      is_error = true;
    }
    if ( spider_check_required( 'popup_instagram_gallery_source', 'Instagram user URL' ) ) {
      is_error = true;
    }
    if ( jQuery( '#popup_instagram_image_number' ).val() > 33 || jQuery( '#popup_instagram_image_number' ).val() < 1 ) {
      alert( bwg_objectL10B.bwg_post_number );
      jQuery( '#popup_instagram_image_number' ).attr( 'style', 'border-color: #FF0000;' );
      jQuery( '#popup_instagram_image_number' ).focus();
      jQuery( 'html, body' ).animate( {
        scrollTop: jQuery( '#popup_instagram_image_number' ).offset().top - 200
      }, 500 );
      is_error = true;
    }
  }
  else {
    if ( bwg_is_instagram_gallery() ) {
      if ( instagram_client_id == '' ) {
        alert( bwg_objectL10B.bwg_access_token );
        is_error = true;
      }

      if ( jQuery( '#autogallery_image_number' ).val() > 25 || jQuery( '#autogallery_image_number' ).val() < 1 ) {
        alert( bwg_objectL10B.bwg_post_number );
        jQuery( '#autogallery_image_number' ).attr( 'style', 'border-color: #FF0000;' );
        jQuery( '#autogallery_image_number' ).focus();
        jQuery( 'html, body' ).animate( {
          scrollTop: jQuery( '#autogallery_image_number' ).offset().top - 200
        }, 500 );
        is_error = true;
      }
    }
  }
  return is_error;
}

function bwg_is_instagram_gallery() {
  var value = jQuery( '#gallery_type' ).val();
  if ( value == 'instagram' ) {
    return true;
  }
  else {
    return false;
  }
}

/**
 *
 *  @param reset:bool true if reset to mixed in case of not empty
 *  @param message:bool true if to alert that not empty
 *  @return true if empty, false if not empty
 */
function bwg_check_gallery_empty( reset, message ) {
  var ids_string = jQuery( "#ids_string" ).val();
  var ids_array = ids_string.split( "," );
  var tr_count = ids_array[ 0 ] == '' ? 0 : ids_array.length;
  if ( tr_count != 0 ) {
    if ( reset ) {
      if ( message ) {
        alert( bwg_objectL10B.bwg_not_empty );
      }
      jQuery( '#gallery_type' ).val( '' );
      jQuery( '#tr_instagram_post_gallery' ).hide();
      jQuery( '#tr_gallery_source' ).hide();
      jQuery( '#tr_update_flag' ).hide();
      jQuery( '#tr_autogallery_image_number' ).hide();
      jQuery( '#tr_instagram_gallery_add_button' ).hide();
    }
    else {
      if ( message ) {
        alert( bwg_objectL10B.bwg_not_empty );
      }
    }
    return false;
  }
  else {
    return true;
  }
}

function bwg_convert_seconds( seconds ) {
  var sec_num = parseInt( seconds, 10 );
  var hours = Math.floor( sec_num / 3600 );
  var minutes = Math.floor( ( sec_num - ( hours * 3600 ) ) / 60 );
  var seconds = sec_num - ( hours * 3600 ) - ( minutes * 60 );

  if ( minutes < 10 && hours != 0 ) {
    minutes = "0" + minutes;
  }
  if ( seconds < 10 ) {
    seconds = "0" + seconds;
  }
  var time = ( hours != 0 ? hours + ':' : '' ) + minutes + ':' + seconds;
  return time;
}

function bwg_convert_date( date, separator ) {
  var m_names = new Array( "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" );
  date = date.split( separator );
  var dateArray = date[ 0 ].split( "-" );
  return dateArray[ 2 ] + " " + m_names[ dateArray[ 1 ] - 1 ] + " " + dateArray[ 0 ] + ", " + date[ 1 ].substring( 0, 5 );
}

/* EMBED handling */
function bwg_get_embed_info( input_id ) {
  jQuery( '#loading_div' ).show();
  var url = encodeURI( jQuery( "#" + input_id ).val() );
  if ( !url ) {
    alert( bwg_objectL10B.bwg_enter_url );
    jQuery( '#loading_div' ).hide();
    return '';
  }
  var filesValid = [];
  var data = {
    'action': 'addEmbed',
    'URL_to_embed': url,
    'async': true
  };
  /* get from the server data for the url. Here we use the server as a proxy, since Cross-Origin Resource Sharing AJAX is forbidden. */
  jQuery.post( ajax_url, data, function ( response ) {
    if ( response == false ) {
      alert( bwg_objectL10B.bwg_cannot_response );
      jQuery( '#loading_div' ).hide();
      return '';
    }
    else {
      var index_start = response.indexOf( "WD_delimiter_start" );
      var index_end = response.indexOf( "WD_delimiter_end" );
      if ( index_start == -1 || index_end == -1 ) {
        alert( bwg_objectL10B.bwg_something_wrong );
        jQuery( '#loading_div' ).hide();
        return '';
      }

      /*filter out other echoed characters*/
      /*18 is the length of "wd_delimiter_start"*/
      response = response.substring( index_start + 18, index_end );

      response_JSON = JSON.parse( response );
      /*if indexed array, it means there is error*/
      if ( typeof response_JSON[ 0 ] !== 'undefined' ) {
        alert( JSON.parse( response )[ 1 ] );
        jQuery( '#loading_div' ).hide();
        return '';
      }
      else {
        fileData = response_JSON;
        filesValid.push( fileData );
        bwg_add_image( filesValid );
        document.getElementById( input_id ).value = '';
        jQuery( '#loading_div' ).hide();
        return 'ok';
      }
    }
    return '';
  } );
  return 'ok';
}

function bwg_change_fonts( cont, google_fonts ) {
  if ( jQuery( "#" + google_fonts ).val() == 1 ) {
    jQuery( '#' + cont ).next( '.font-select' ).show();
    jQuery( '#' + cont + '_default' ).hide();
  }
  else {
    jQuery( '#' + cont ).next( '.font-select' ).hide();
    jQuery( '#' + cont + '_default' ).show();
    jQuery( '#' + cont + '_default' ).css( { 'font-family': jQuery( '#' + cont + '_default' ).val() } );
  }
}

/**
 * Open Wordpress media uploader.
 *
 * @param e
 * @param multiple
 */
function spider_media_uploader( e, multiple ) {
  if ( typeof multiple == "undefined" ) {
    var multiple = false;
  }
  var custom_uploader;
  e.preventDefault();
  /* If the uploader object has already been created, reopen the dialog. */
  if ( custom_uploader ) {
    custom_uploader.open();
  }

  custom_uploader = wp.media.frames.file_frame = wp.media( {
    title: bwg_objectL10B.choose_images,
    library: { type: 'image' },
    button: { text: bwg_objectL10B.insert },
    multiple: multiple
  } );
  /* When a file is selected, grab the URL and set it as the text field's value */
  custom_uploader.on( 'select', function () {
    if ( multiple == false ) {
      attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
    }
    else {
      attachment = custom_uploader.state().get( 'selection' ).toJSON();
    }

    var filesSelectedML = [];
    for ( var image in attachment ) {
      var image_url = attachment[ image ].url;
      image_url = image_url.replace( bwg_objectL10B.wp_upload_dir.baseurl + '/', '' );
      filesSelectedML.push( image_url );
    }
    jQuery( '#loading_div' ).show();

    postImageUrls( filesSelectedML, function ( success, result ) {
      jQuery( '#loading_div' ).hide();
      if ( success ) {
        jQuery( ".bwg-type-allowed" ).remove();
        for ( var i in result ) {
          if ( result[ i ].error ) {
            add_ajax_msg( bwg_objectL10B.only_the_following_types_are_allowed, 'error' );
          }
          result[ i ].alt = attachment[ i ].alt ? attachment[ i ].alt : attachment[ i ].title;
          result[ i ].description = attachment[ i ].description;
        }
        bwg_add_image( result );
      }
      else {
        alert( bwg_objectL10B.import_failed );
      }
    } );

    function postImageUrls( imageUrls, callback, index, results ) {
      var imagesChunkLength = 50;

      if ( !index ) {
        index = 0;
      }
      if ( !results ) {
        results = [];
      }

      var imageUrlsChunk = imageUrls.slice( index, index + imagesChunkLength );
      index += imagesChunkLength;
      jQuery.ajax( {
        url: bwg_objectL10B.ajax_url,
        type: "POST",
        dataType: "json",
        data: {
          action: "bwg_UploadHandler",
          file_namesML: JSON.stringify( imageUrlsChunk ),
          import: 1
        },
        success: function ( result ) {
          results = results.concat( result );

          if ( index < imageUrls.length ) {
            postImageUrls( imageUrls, callback, index, results );
          }
          else {
            callback( true, results );
          }
        },
        error: function ( xhr ) {
          callback( false );
        }
      } );
    }
  } );

  /* Open the uploader dialog. */
  custom_uploader.open();
}

function add_ajax_msg( msg, status ) {
  if ( !jQuery( '.ajax-msg' ).hasClass( 'bwg-type-allowed' ) ) {
    var html = '<div class="ajax-msg bwg-type-allowed">' +
      '<div class="' + status + ' inline">' +
      '<p><strong>' + msg + '</strong></p>' +
      '</div>' +
      '</div>';
    jQuery( html ).insertAfter( "#add_desc" );
  }
}

/**
 * Search.
 *
 * @param that
 */
function search( that ) {
  var form = jQuery( that ).parents( "form" );

  if ( form.attr( "id" ) == "bwg_gallery" ) { /* Gallery edit page. */
    jQuery( "#paged" ).val( 1 );
    jQuery( "#ajax_task" ).val( 'ajax_apply' );
    spider_ajax_save( form.attr( "id" ) );
  }
  else {
    var action = form.attr( "action" );
    form.attr( "action", action + "&paged=1&s=" + jQuery( "input[name='s']" ).val() );
    form.submit();
  }
}

/**
 * Search on input enter.
 *
 * @param e
 * @param that
 * @returns {boolean}
 */
function input_search( e, that ) {
  if (  e.key == 'Enter' ) { /*Enter keycode*/
    search( that );
    return false;
  }
}

/**
 * Change page on input enter.
 *
 * @param e
 * @param that
 * @returns {boolean}
 */
function input_pagination( e, that ) {
  if (  e.key == 'Enter' ) { /*Enter keycode*/
    var to_page = jQuery( that ).val();
    var pages_count = jQuery( that ).parents( ".pagination-links" ).data( "pages-count" );
    var form = jQuery( that ).parents( "form" );
    if ( form.attr( "id" ) == "bwg_gallery" ) { /* Gallery edit page. */
      if ( to_page > pages_count ) {
        to_page = 1;
      }
      jQuery( "#paged" ).val( to_page );
      jQuery( "#ajax_task" ).val( 'ajax_apply' );
      spider_ajax_save( form.attr( "id" ) );
      return false;
    }
    else {
      if ( to_page > 0 && to_page <= pages_count ) {
        var search = jQuery( "input[name='s']" ).val() ? ( "&s=" + jQuery( "input[name='s']" ).val() ) : "";
        var action = form.attr( "action" );
        form.attr( "action", action + "&paged=" + to_page + search );
      }
      form.submit();
    }
  }
  return true;
}

/**
 * Bulk actions.
 *
 * @param that
 */
function wd_bulk_action( that ) {
  var form = jQuery( that ).parents( "form" );
  var action = jQuery( "select[name='" + ( form.attr( "id" ) == "bwg_gallery" ? 'image_' : '' ) + "bulk_action']" ).val();
  if ( action != -1 ) {
    if ( !jQuery( "input[name^='check']" ).is( ':checked' ) ) {
      alert( bwg.select_at_least_one_item );
      return;
    }
    if ( action == 'delete' ) {
      if ( !confirm( bwg.delete_confirmation ) ) {
        return false;
      }
    }
    else if ( action == 'image_resize' ) {
      jQuery( ".opacity_resize_image" ).show();
      return false;
    }
    else if ( action == 'image_edit' ) {
      jQuery( ".opacity_image_desc" ).show();
      return false;
    }
    else if ( action == 'image_edit_alt' ) {
      jQuery( ".opacity_image_alt" ).show();
      return false;
    }
    else if ( action == 'image_edit_redirect' ) {
      jQuery( ".opacity_image_redirect" ).show();
      return false;
    }
    else if ( action == 'image_edit_description' ) {
      jQuery( ".opacity_image_description" ).show();
      return false;
    }
    else if ( action == 'image_add_tag' ) {
      jQuery( ".wd-add-tags" ).trigger( "click" );
      return;
    }
    else if ( action == 'set_image_pricelist' ) {
      jQuery( ".wd-add-pricelist" ).trigger( "click" );
      return;
    }
    else if ( action == 'remove_pricelist_all' ) {
      if ( !confirm( bwg.remove_pricelist_confirmation ) ) {
        return false;
      }
    }
    if ( form.attr( "id" ) == "bwg_gallery" ) { /* Gallery edit page. */
      jQuery( "input[name='task']" ).val( "save" );
      jQuery( "input[name='ajax_task']" ).val( action );
      spider_ajax_save( form.attr( "id" ) );
    }
    else {
      jQuery( "input[name='task']" ).val( action );
      form.submit();
    }
  }
}

function bwg_change_theme_tab_item() {
  var id = jQuery( '.bwg-tabs .bwg-tab-item.active' ).attr( 'data-id' );
  jQuery( 'fieldset#' + id ).show();

  jQuery( document ).on( 'click', '.bwg-tabs .bwg-tab-item', function () {
    jQuery( '.bwg-tabs .bwg-tab-item' ).removeClass( 'active' );
    jQuery( this ).addClass( 'active' );
    var id = jQuery( this ).attr( 'data-id' );
    jQuery( '.spider_type_fieldset' ).hide();
    jQuery( '#' + id ).show();
    jQuery( '#active_tab' ).val( jQuery( this ).attr( 'data-id' ) );
  } );
}

function bwg_filters() {
  jQuery( document ).on( 'change', 'select[id^=filter-by]', function () {
    var val = jQuery( this ).val();
    var id = jQuery( this ).attr( 'id' );
    window.location.href = bwg_updateQueryStringParameter( window.location.href, id, val );
  } );
}

function bwg_updateQueryStringParameter( uri, key, value ) {
  var re = new RegExp( "([?&])" + key + "=.*?(&|$)", "i" );
  var separator = uri.indexOf( '?' ) !== -1 ? "&" : "?";
  if ( uri.match( re ) ) {
    return uri.replace( re, '$1' + key + "=" + value + '$2' );
  }
  else {
    return uri + separator + key + "=" + value;
  }
}

/* Open/close section container on its header click. */
function bwg_toggle_postbox() {
  jQuery( ".hndle, .handlediv" ).each( function () {
    jQuery( this ).on( "click", function () {
      jQuery( this ).parent( ".postbox" ).toggleClass( "closed" );
    } );
  } );
}

function spider_select_value( obj ) {
  obj.focus();
  obj.select();
}

var j_int = 0;
var bwg_j = 'pr_' + j_int;

/**
 * Add image to images list.
 *
 * @param files
 */
function bwg_add_image( files ) {
  var gallery_type = jQuery( '#gallery_type option:selected' ).val();
  jQuery( '#check_all_items, #check_all' ).prop( 'checked', false );
  for ( var i in files ) {
    if ( files[ i ][ 'error' ] == true ) {
      continue;
    }
    var is_embed = files[ i ][ 'filetype' ].indexOf( "EMBED_" ) > -1 ? true : false;
    var is_direct_url = files[ i ][ 'filetype' ].indexOf( "DIRECT_URL_" ) > -1 ? true : false;
    var is_facebook_post = files[ i ][ 'filetype' ].indexOf( "_FACEBOOK_POST" ) > -1 ? 1 : 0;
    var fb_post_url = ( is_facebook_post ) ? files[ i ][ 'filename' ] : '';
    if ( typeof files[ i ][ 'resolution' ] != "undefined" ) {
      var instagram_post_width = files[ i ][ 'resolution' ].split( 'x' )[ 0 ].trim();
      var instagram_post_height = files[ i ][ 'resolution' ].split( 'x' )[ 1 ].trim();
    }
    else {
      var instagram_post_width = "";
      var instagram_post_height = "";
    }

    var html = jQuery( ".wd-template" ).clone().html();
    // Google Photos add-on is active.
    if ( gallery_type == 'google_photos' ) {
      jQuery( '.bulkactions' ).remove();
      jQuery( '#images_table' ).find( '.col_drag' ).remove();
      jQuery( '#images_table' ).find( '.check-column' ).remove();
      jQuery( '.wd-template' ).find( '.col_drag' ).remove();
      jQuery( '.wd-template' ).find( '.check-column' ).remove();
      jQuery( '.wd-template' ).find( '.bwg-td-item-redirect-url' ).remove();
      jQuery( '.wd-template' ).find( '.bwg-td-item-tags' ).remove();
      jQuery( '.wd-template' ).find( '#image_alt_text_tempid' ).prop( "disabled", true );
      jQuery( '.wd-template' ).find( '#image_description_tempid' ).prop( "disabled", true );
      html = jQuery( ".wd-template" ).clone().html();
    }

    if ( files[ i ][ 'filetype' ] == 'SVG' ) {
      jQuery( '#images_table' ).find( '.wd-image-actions' ).remove();
      html = jQuery( ".wd-template" ).clone().html();
    }
    var name = files[ i ][ 'name' ].substr( 0, files[ i ][ 'name' ].lastIndexOf( '.' ) ) || files[ i ][ 'name' ];
    var filename = files[ i ][ 'filename' ];
    if ( name != "" ) {
      filename = name;
    }
    var filetype = files[i]['filetype'];

    html = html.replace(/tempid/g, bwg_j)
               .replace(/tempnum/g, 1)
               .replace(/tempthumb_src=""/g, 'src="' + files[i]['thumb'] + '"')
               .replace(/tempfilename/g, filename)
               .replace(/tempdate/g, files[i]['date_modified'])
               .replace(/tempresolution/g, files[i]['resolution'])
               .replace(/tempthumbresolution/g, files[i]['resolution_thumb'])
               .replace(/temp_instagram_post_width/g, instagram_post_width)
               .replace(/temp_instagram_post_height/g, instagram_post_height)
               .replace(/tempsize/g, files[i]['size'])
               .replace(/tempfiletype/g, filetype)
               .replace(/tempis_facebook_post/g, (is_facebook_post ? files[i]['is_facebook_post'] : 0))
               .replace(/tempfb_post_url/g, (is_facebook_post ? files[i]['fb_post_url'] : 0));

    if ( filetype == 'EMBED_OEMBED_INSTAGRAM_POST' ) {
      html = html.replace(/tempimage_url/g, files[i]['url']);
    }
    else {
      html = html.replace(/tempimage_url/g, encodeURIComponent(files[i]['url']));
    }
    html = html.replace(/tempthumb_url/g, encodeURIComponent(files[i]['thumb_url']));
    if ( is_embed ) {
      html = html.replace(/tempalt/g, name);
      html = html.replace(/wd-image-actions/g, 'wd-image-actions wd-hide');
    }
    else {
      html = html.replace(/tempalt/g, files[i]['alt']);
    }

    var description = files[ i ][ 'description' ] ? files[ i ][ 'description' ] : '';
    if ( jQuery( "#tbody_arr" ).data( "meta" ) == 1 && !is_embed ) {
      description += files[ i ][ 'description' ] ? '\n' : '';
      description += files[ i ][ 'credit' ] ? 'Author: ' + files[ i ][ 'credit' ] + '\n' : '';
      description += ( ( files[ i ][ 'aperture' ] != 0 && files[ i ][ 'aperture' ] != '' ) ? 'Aperture: ' + files[ i ][ 'aperture' ] + '\n' : '' );
      description += ( ( files[ i ][ 'camera' ] != 0 && files[ i ][ 'camera' ] != '' ) ? 'Camera: ' + files[ i ][ 'camera' ] + '\n' : '' );
      description += ( ( files[ i ][ 'caption' ] != 0 && files[ i ][ 'caption' ] != '' ) ? 'Caption: ' + files[ i ][ 'caption' ] + '\n' : '' );
      description += ( ( files[ i ][ 'iso' ] != 0 && files[ i ][ 'iso' ] != '' ) ? 'Iso: ' + files[ i ][ 'iso' ] + '\n' : '' );
      description += ( ( files[ i ][ 'copyright' ] != 0 && files[ i ][ 'copyright' ] != '' ) ? 'Copyright: ' + files[ i ][ 'copyright' ] + '\n' : '' );
      description += ( ( files[ i ][ 'orientation' ] != 0 && files[ i ][ 'orientation' ] != '' ) ? 'Orientation: ' + files[ i ][ 'orientation' ] + '\n' : '' );
    }
    html = html.replace( /tempdescription/g, description );

    jQuery( "#tbody_arr" ).prepend( "<tr id='tr_" + bwg_j + "'>" );
    jQuery( "#tr_" + bwg_j ).html( html );

    /* Change the popup dimensions. */
    bwg_tb_window( "#tr_" + bwg_j );

    jQuery( "#ids_string" ).val( jQuery( "#ids_string" ).val() + bwg_j + ',' );
    if ( jQuery( "#tbody_arr" ).data( "meta" ) == 1 && files[ i ][ 'tags' ] ) {
      /* If tags added to image from image file meta keywords.*/
      var tagsTitles = JSON.parse( files[ i ][ 'tags' ] );
      /* Add prefix to keywords to differ from other tags on save.*/
      var tagsIds = [];
      for ( var i in tagsTitles ) {
        tagsIds[ i ] = 'bwg_' + tagsTitles[ i ];
      }
      /* Add titles instead of ids.*/
      bwg_add_tag( bwg_j, tagsIds, tagsTitles );
    }

    j_int++;
    bwg_j = 'pr_' + j_int;
  }
  /* Add drag and drop to new rows. */
  wd_showhide_weights();
  /* Set order input values after adding rows. */
  var i;
  if (jQuery( "td.col_drag" ).data( "page-number" ) == 0) {
    i = -jQuery( ".wd-order" ).length;
  } else {
    i = jQuery( "td.col_drag" ).data( "page-number" );
  }
  jQuery( ".wd-order" ).each( function () {
    jQuery( this ).val( ++i );
  } );
  /* Set number column values after adding rows. */
  var i = 0;
  jQuery( "#tbody_arr .col_num" ).each( function () {
    jQuery( this ).html( ++i );
  } );
  window.parent.jQuery( ".no-items" ).remove();
  jQuery( ".unsaved-msg", window.parent.document ).removeClass( "wd-hide" );
  jQuery( ".ajax-msg", window.parent.document ).addClass( "wd-hide" );
  jQuery( ".bwg-type-allowed", window.parent.document ).removeClass( "wd-hide" );
}

/**
 * Change pagination to ajax pagination.
 */
function wd_pagination() {
  jQuery( "#bwg_gallery a.wd-page " ).each( function () {
    jQuery( this ).removeAttr( "href" );
    jQuery( this ).on( "click", function () {
      var paged = jQuery( this ).data( "paged" );
      jQuery( "#paged" ).val( paged );
      jQuery( "#ajax_task" ).val( 'ajax_apply' );
      spider_ajax_save( 'bwg_gallery' );
    } );
  } );
}

function bwg_tb_window( cont_id ) {
  if ( typeof cont_id === 'undefined' ) {
    var cont_id = '';
  }
  var thickDims, tbWidth, tbHeight;
  thickDims = function () {
    var tbWindow = jQuery( '#TB_window' ), H = jQuery( window ).height(), W = jQuery( window ).width(), w, h;
    w = ( tbWidth && tbWidth < W - 90 ) ? tbWidth : W - 40;
    h = ( tbHeight && tbHeight < H - 60 ) ? tbHeight : H - 40;
    if ( tbWindow.length ) {
      tbWindow.width( w ).height( h );
      jQuery( '#TB_iframeContent' ).width( w ).height( h - 30 );
      tbWindow.css( { 'margin-left': '-' + parseInt( ( w / 2 ), 10 ) + 'px' } );
      if ( typeof document.body.style.maxWidth != 'undefined' ) {
        tbWindow.css( { 'top': ( H - h ) / 2, 'margin-top': '0' } );
      }
    }
  };
  thickDims();
  jQuery( window ).resize( function () {
    thickDims()
  } );
  jQuery( cont_id + ' a.thickbox-preview' ).click( function () {
    tb_click.call( this );
    var alink = jQuery( this ).parents( '.available-theme' ).find( '.activatelink' ), link = '',
      href = jQuery( this ).attr( 'href' ), url, text;
    if ( tbWidth = href.match( /&bwg_width=[0-9]+/ ) ) {
      tbWidth = parseInt( tbWidth[ 0 ].replace( /[^0-9]+/g, '' ), 10 );
    }
    else {
      tbWidth = jQuery( window ).width() - 120;
    }

    if ( tbHeight = href.match( /&bwg_height=[0-9]+/ ) ) {
      tbHeight = parseInt( tbHeight[ 0 ].replace( /[^0-9]+/g, '' ), 10 );
    }
    else {
      tbHeight = jQuery( window ).height() - 120;
    }
    if ( alink.length ) {
      url = alink.attr( 'href' ) || '';
      text = alink.attr( 'title' ) || '';
      link = '&nbsp; <a href="' + url + '" target="_top" class="tb-theme-preview-link">' + text + '</a>';
    }
    else {
      text = jQuery( this ).attr( 'title' ) || '';
      link = '&nbsp; <span class="tb-theme-preview-link">' + text + '</span>';
    }
    /* jQuery('#TB_title').css({'background-color': '#222', 'color': '#dfdfdf'}); */
    jQuery( '#TB_closeAjaxWindow' ).css( { 'float': 'right' } );
    jQuery( '#TB_ajaxWindowTitle' ).css( { 'float': 'left' } ).html( link );
    jQuery( '#TB_iframeContent' ).width( '100%' );
    thickDims();
    return false;
  } );
  /* Theme details*/
  jQuery( '.theme-detail' ).click( function () {
    jQuery( this ).siblings( '.themedetaildiv' ).toggle();
    return false;
  } );
}

/* Prevent new line. */
function prevent_new_line( e ) {
  if ( e.key == 'Enter' ) {
    e.preventDefault();
    return false;
  }
}

function bwg_gallery_type_options( gallery_type ) {
  if ( gallery_type === undefined ) {
    gallery_type = jQuery( '#gallery_type' ).val();
  }
  gallery_type_name = jQuery( '.bwg-' + gallery_type ).data( 'title' );
  pro_img_url = jQuery( '.bwg-' + gallery_type ).data( 'img-url' );
  pro_demo_link = jQuery( '.bwg-' + gallery_type ).data( 'demo-link' );
  jQuery( '.gallery_options' ).hide();
  jQuery( '#' + gallery_type + '_options' ).show();
  jQuery( '#gallery_type' ).val( gallery_type );
  if ( jQuery( ".wd-free-msg" ).length != 0 ) {
    jQuery( ".wd-free-msg" ).hide();
    jQuery( ".bwg-pro-views" ).hide();
    if ( jQuery( '#' + gallery_type + '_options' ).hasClass( "bwg-pro-views" ) ) {
      jQuery( ".wd-free-msg" ).show();
      jQuery( ".upgrade-to-pro-title" ).html( gallery_type_name + ' view is<br>available in Premium Version' );
      jQuery( ".pro-views-img" ).attr( 'src', pro_img_url );
      jQuery( ".button-demo" ).attr( 'href', pro_demo_link );
    }
  }
  jQuery( '#bwg_tab_galleries_content .gallery_type' ).find( '.view_type_img_active' ).css( 'display', 'none' );
  jQuery( '#bwg_tab_galleries_content .gallery_type' ).find( '.view_type_img' ).css( 'display', 'inline' );
  jQuery( '#bwg_tab_galleries_content .gallery_type' ).removeClass( 'gallery_type_active' );
  jQuery( 'input[name=gallery_type][id=' + gallery_type + ']' ).prop( 'checked', true ).closest( '.gallery_type' ).addClass( 'gallery_type_active' );
  jQuery( '#bwg_tab_galleries_content .gallery_type_active' ).find( '.view_type_img_active' ).css( 'display', 'inline' );
  jQuery( '#bwg_tab_galleries_content .gallery_type_active' ).find( '.view_type_img' ).css( 'display', 'none' );
  search_options();
}

function bwg_album_type_options( album_type ) {
  if ( album_type === undefined ) {
    album_type = jQuery( '#album_type' ).val();
  }
  gallery_type_name = jQuery( '.bwg-' + album_type ).data( 'title' );
  pro_img_url = jQuery( '.bwg-' + album_type ).data( 'img-url' );
  pro_demo_link = jQuery( '.bwg-' + album_type ).data( 'demo-link' );
  jQuery( '.album_options' ).hide();
  jQuery( '#' + album_type + '_options' ).show();
  jQuery( '#album_type' ).val( album_type );
  if ( jQuery( ".wd-free-msg" ).length != 0 ) {
    jQuery( ".wd-free-msg" ).hide();
    jQuery( ".bwg-pro-views" ).hide();
    if ( jQuery( '#' + album_type + '_options' ).hasClass( "bwg-pro-views" ) ) {
      jQuery( ".wd-free-msg" ).show();
      jQuery( ".upgrade-to-pro-title" ).html( gallery_type_name + bwg.bwg_premium_text );
      jQuery( ".pro-views-img" ).attr( 'src', pro_img_url );
      jQuery( ".button-demo" ).attr( 'href', pro_demo_link );
    }
  }
  jQuery( '#bwg_tab_albums_content .gallery_type' ).find( '.view_type_img_active' ).css( 'display', 'none' );
  jQuery( '#bwg_tab_albums_content .gallery_type' ).find( '.view_type_img' ).css( 'display', 'inline' );
  jQuery( '#bwg_tab_albums_content .gallery_type' ).removeClass( 'gallery_type_active' );
  jQuery( 'input[name=album_type][id=' + album_type + ']' ).prop( 'checked', true ).closest( '.gallery_type' ).addClass( 'gallery_type_active' );
  jQuery( '#bwg_tab_albums_content .gallery_type_active' ).find( '.view_type_img_active' ).css( 'display', 'inline' );
  jQuery( '#bwg_tab_albums_content .gallery_type_active' ).find( '.view_type_img' ).css( 'display', 'none' );
  search_options();
}

function bwg_pagination_description( that ) {
  obj = jQuery( that );
  obj.closest( '.wd-group' ).find( '.description' ).hide();
  jQuery( '#' + obj.attr( 'name' ) + '_' + obj.val() + '_description' ).show();
}

function bwg_thumb_click_action() {
  if ( jQuery( "#thumb_click_action_2" ).is( ':checked' ) ) {
    jQuery( '.bwg-lightbox-redirect' ).show();
  }
  else {
    jQuery( '.bwg-lightbox-redirect' ).hide();
  }
}

/**
 * Recreate thumbs part by part limit 50.
 *
 * @param limit
 * @returns {boolean}
 */
function bwg_recreate_thumb( limit ) {
  var img_option_width = jQuery( "#upload_thumb_width" ).val();
  var img_option_height = jQuery( "#upload_thumb_height" ).val();

  var imgcount = jQuery( '#bwg_imgcount' ).val();
  var post_data = {
    'task': 'resize_image_thumb',
    'img_option_width': img_option_width,
    'img_option_height': img_option_height,
    'limitstart': limit,
  };

  if ( limit == 0 ) {
    jQuery( '#loading_div' ).show();
    jQuery( '.updated' ).remove();
  }
  jQuery.ajax( {
    type: "POST",
    url: bwg_options_url_ajax,
    data: post_data,
    success: function () {
      if ( limit < imgcount ) {
        limit += 50;
        bwg_recreate_thumb( limit );
      }
      else {
        jQuery( '#loading_div' ).hide();
        jQuery( "<div class=\"updated inline\">\n" +
          "      <p><strong>" + bwg_objectL10B.recreate_success + "</strong></p>" +
          "      </div>" ).insertBefore( jQuery( "#bwg_options_form" ).parent() );
      }
    }
  } );
  return false;
}

/**
 * Set watermark on images part by part limit 50.
 *
 * @param limit
 * @returns {boolean}
 */
function bwg_set_watermark( limit ) {
  var built_in_watermark_type = jQuery( 'input[name=built_in_watermark_type]:checked' ).val();
  var opacity = jQuery('input[name=built_in_watermark_opacity]').val();
  var imgcount = jQuery( '#bwg_imgcount' ).val();
  var post_data = {
    'task': 'image_set_watermark',
    'built_in_watermark_type': built_in_watermark_type,
    'built_in_watermark_position': jQuery( 'input[name=built_in_watermark_position]:checked' ).val(),
    'limitstart': limit,
    'built_in_opacity' : opacity,
  };
  if ( built_in_watermark_type == 'text' ) {
    post_data.built_in_watermark_text = jQuery( '#built_in_watermark_text' ).val();
    post_data.built_in_watermark_font_size = jQuery( '#built_in_watermark_font_size' ).val();
    post_data.built_in_watermark_font = jQuery( '#built_in_watermark_font' ).val();
    post_data.built_in_watermark_color = jQuery( '#built_in_watermark_color' ).val();
  }
  else {
    post_data.built_in_watermark_size = jQuery( '#built_in_watermark_size' ).val();
    post_data.built_in_watermark_url = jQuery( '#built_in_watermark_url' ).val();
  }

  if ( limit == 0 ) {
    jQuery( '#loading_div' ).show();
    jQuery( '.updated' ).remove();
  }
  jQuery.ajax( {
    type: "POST",
    url: bwg_options_url_ajax,
    data: post_data,
    dataType: 'json',
    success: function ( response ) {
      if ( limit < imgcount && response.error === false ) {
        limit += 50;
        bwg_set_watermark( limit );
      }
      else {
        jQuery( '#loading_div' ).hide();
        jQuery( '.bwg_error' ).remove();
        jQuery( '<div class="bwg_error">' + response.message + '</div>' ).insertBefore( jQuery( "#bwg_options_form" ).parent() );
      }
    }
  } );

  return false;
}

/**
 * Reset watermarks from images part by part limit 50.
 *
 * @param limit
 * @returns {boolean}
 */
function bwg_reset_watermark_all( limit ) {
  var imgcount = jQuery( '#bwg_imgcount' ).val();
  var post_data = {
    'task': 'image_recover_all',
    'limitstart': limit,
  };
  if ( limit == 0 ) {
    jQuery( '#loading_div' ).show();
    jQuery( '.updated' ).remove();
  }
  jQuery.ajax( {
    type: "POST",
    url: bwg_options_url_ajax,
    data: post_data,
    success: function () {
      if ( limit < imgcount ) {
        limit += 50;
        bwg_reset_watermark_all( limit );
      }
      else {
        jQuery( '#loading_div' ).hide();
        jQuery( "<div class=\"updated inline\">\n" +
          "      <p><strong>" + bwg_objectL10B.watermark_option_reset + "</strong></p>" +
          "      </div>" ).insertBefore( jQuery( "#bwg_options_form" ).parent() );
      }
    }
  } );

  return false;
}

/*galleries sortable */
function bwg_galleries_ordering() {
  jQuery( "#bwg-table-sortable" ).sortable( {
    handle: ".connectedSortable",
    connectWith: ".connectedSortable",
    update: function () {
      var ids = [];
      var ordering_ajax_url = jQuery( "td.col_drag" ).data( "ordering-url" );
      jQuery( ".wd-id" ).each( function () {
        ids.push( jQuery( this ).val() );
      } );
      jQuery.ajax( {
        type: "POST",
        dataType: "json",
        url: ordering_ajax_url,
        data: { 'orders': ids },
        success: function ( response ) {
          jQuery( ".ajax-msg" ).remove();
          if ( response.message ) {
            jQuery( '<div class="ajax-msg">' + response.message + '</div>' ).insertAfter( '.wrap .bwg-head-notice' );
          }
        }
      } );
    }
  } );
}

function show_hide_compact_album_view( val ) {
  switch ( val ) {
    case 'thumbnail': {
      bwg_show_hide( 'tr_album_mosaic', 'none' );
      bwg_show_hide( 'tr_album_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_thumbnail_dimensions', '' );
      bwg_show_hide( 'tr_album_images_per_page', '' );
    }
      break;
    case 'masonry': {
      bwg_show_hide( 'tr_album_mosaic', 'none' );
      bwg_show_hide( 'tr_album_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_thumbnail_dimensions', '' );
      bwg_show_hide( 'tr_album_images_per_page', '' );
    }
      break;
    case 'mosaic': {
      bwg_show_hide( 'tr_album_mosaic', '' );
      bwg_show_hide( 'tr_album_resizable_mosaic', '' );
      bwg_show_hide( 'tr_album_mosaic_total_width', '' );
      bwg_show_hide( 'for_album_image_title_show_hover_0', 'none' );
      bwg_show_hide( 'album_image_title_show_hover_0', 'none' );
      bwg_show_hide( 'for_album_ecommerce_icon_show_hover_0', 'none' );
      bwg_show_hide( 'tr_album_thumbnail_dimensions', '' );
      bwg_show_hide( 'tr_album_images_per_page', '' );
    }
      break;
    case 'slideshow': {
      bwg_show_hide( 'tr_album_mosaic', 'none' );
      bwg_show_hide( 'tr_album_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_thumbnail_dimensions', 'none' );
      bwg_show_hide( 'tr_album_images_per_page', 'none' );
    }
    case 'image_browser': {
      bwg_show_hide( 'tr_album_mosaic', 'none' );
      bwg_show_hide( 'tr_album_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_thumbnail_dimensions', 'none' );
      bwg_show_hide( 'tr_album_images_per_page', 'none' );
    }
      break;
    case 'blog_style': {
      bwg_show_hide( 'tr_album_mosaic', 'none' );
      bwg_show_hide( 'tr_album_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_thumbnail_dimensions', 'none' );
      bwg_show_hide( 'tr_album_images_per_page', '' );
    }
      break;
    case 'carousel': {
      bwg_show_hide( 'tr_album_mosaic', 'none' );
      bwg_show_hide( 'tr_album_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_thumbnail_dimensions', 'none' );
      bwg_show_hide( 'tr_album_images_per_page', 'none' );
    }
      break;
  }
}

function show_hide_extended_album_view( val ) {
  switch ( val ) {
    case 'thumbnail': {
      bwg_show_hide( 'tr_album_extended_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_extended_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_extended_thumbnail_dimensions', '' );
      bwg_show_hide( 'tr_album_extended_images_per_page', '' );
    }
      break;
    case 'masonry': {
      bwg_show_hide( 'tr_album_extended_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_extended_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_extended_thumbnail_dimensions', '' );
      bwg_show_hide( 'tr_album_extended_images_per_page', '' );
    }
      break;
    case 'mosaic': {
      bwg_show_hide( 'tr_album_extended_mosaic', '' );
      bwg_show_hide( 'tr_album_extended_resizable_mosaic', '' );
      bwg_show_hide( 'tr_album_extended_mosaic_total_width', '' );
      bwg_show_hide( 'for_album_extended_image_title_show_hover_0', 'none' );
      bwg_show_hide( 'album_extended_image_title_show_hover_0', 'none' );
      bwg_show_hide( 'for_album_extended_ecommerce_icon_show_hover_0', 'none' );
      bwg_show_hide( 'tr_album_extended_thumbnail_dimensions', '' );
      bwg_show_hide( 'tr_album_extended_images_per_page', '' );
    }
      break;
    case 'slideshow': {
      bwg_show_hide( 'tr_album_extended_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_extended_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_extended_thumbnail_dimensions', 'none' );
      bwg_show_hide( 'tr_album_extended_images_per_page', 'none' );
    }
    case 'image_browser': {
      bwg_show_hide( 'tr_album_extended_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_extended_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_extended_thumbnail_dimensions', 'none' );
      bwg_show_hide( 'tr_album_extended_images_per_page', 'none' );
    }
      break;
    case 'blog_style': {
      bwg_show_hide( 'tr_album_extended_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_extended_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_extended_thumbnail_dimensions', 'none' );
      bwg_show_hide( 'tr_album_extended_images_per_page', '' );
    }
      break;
    case 'carousel': {
      bwg_show_hide( 'tr_album_extended_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_resizable_mosaic', 'none' );
      bwg_show_hide( 'tr_album_extended_mosaic_total_width', 'none' );
      bwg_show_hide( 'for_album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'album_extended_image_title_show_hover_0', '' );
      bwg_show_hide( 'for_album_extended_ecommerce_icon_show_hover_0', '' );
      bwg_show_hide( 'tr_album_extended_thumbnail_dimensions', 'none' );
      bwg_show_hide( 'tr_album_extended_images_per_page', 'none' );
    }
      break;
  }
}

function bwg_show_hide( id, display ) {
  jQuery( "#" + id ).css( 'display', display );
}

function search_get_current( sort ) {
  var current, div, div_id;
  current = jQuery( '#search_current_match' ).attr( 'class' );
  div = jQuery( '.search-div:visible' );
  div_id = div.attr( 'id' );
  if ( sort == 'search_next' ) {
    if ( !current ) {
      if ( jQuery( '.search-div' ).last().attr( 'id' ) == div_id ) {
        jQuery( 'a[href$=' + jQuery( '.search-div' ).first().attr( 'id' ) + ']' ).click();
      }
      else if ( div.is( ':has(div.bwg_change_gallery_type)' ) && !div.find( '.bwg_change_gallery_type span:last-child' ).hasClass( 'gallery_type_active' ) ) {
        var i = 0,
          j;
        div.find( '.gallery_type' ).each( function () {
          if ( jQuery( this ).hasClass( 'gallery_type_active' ) ) {
            jQuery( this ).removeClass( 'gallery_type_active' );
            j = i;
          }
          i++;
        } )
        div.find( '.bwg_change_gallery_type' ).children().eq( j + 1 ).addClass( 'gallery_type_active' );
        div.find( '#gallery_types_name > option:selected' ).removeAttr( 'selected' ).next( 'option' ).prop( 'selected', true );
        jQuery( '.gallery_type_active:visible' ).trigger( 'click' );
      }
      else if ( div.next().is( ':has(div.bwg_change_gallery_type)' ) ) {
        jQuery( 'a[href$=' + div.next().attr( 'id' ) + ']' ).click();
        div.next().find( '.bwg_change_gallery_type' ).children().removeClass( 'gallery_type_active' );
        div.next().find( '.bwg_change_gallery_type' ).children().eq( 0 ).addClass( 'gallery_type_active' );
        jQuery( '#' + div.next().find( '#gallery_types_name > option:selected' ).val() ).css( { 'display': 'none' } );
        div.next().find( '#gallery_types_name > option:selected' ).removeAttr( 'selected' );
        div.next().find( '#gallery_types_name > option:first' ).prop( 'selected', true );
        jQuery( '#' + div.next().find( '#gallery_types_name > option:selected' ).val() ).css( { 'display': 'flex' } );
        jQuery( '.gallery_type_active:visible' ).click();
        bwg_gallery_type_options( div.next().find( '#gallery_types_name > option:selected' ).val() );

      }
      else {
        jQuery( 'a[href$=' + div.next().attr( 'id' ) + ']' ).click();
      }
    }
    else {
      ind = +current.indexOf( ' search_highlight' );
      var next = +current.substring( 9, ind ) + 1;
      var total = +jQuery( '.total_matches:visible' ).text();
      if ( next <= total ) {
        jQuery( '#search_current_match' ).removeAttr( 'id' );
        jQuery( '.' + next.toString() ).attr( 'id', 'search_current_match' );
        jQuery( '.current_match' ).empty();
        jQuery( '.current_match' ).append( next + '/' );
      }
      else {
        if ( div.is( ':has(div.bwg_change_gallery_type)' ) && !div.find( '.bwg_change_gallery_type span:last-child' ).hasClass( 'gallery_type_active' ) ) {
          var i = 0,
            j;
          div.find( '.gallery_type' ).each( function () {
            if ( jQuery( this ).hasClass( 'gallery_type_active' ) ) {
              jQuery( this ).removeClass( 'gallery_type_active' );
              j = i;
            }
            i++;
          } )
          div.find( '.bwg_change_gallery_type' ).children().eq( j + 1 ).addClass( 'gallery_type_active' );
          div.find( '#gallery_types_name > option:selected' ).removeAttr( 'selected' ).next( 'option' ).prop( 'selected', true );
          jQuery( '.gallery_type_active:visible' ).click();
          bwg_gallery_type_options( div.find( '#gallery_types_name > option:selected' ).val() );
        }
        else if ( div.next().is( ':has(div.bwg_change_gallery_type)' ) ) {
          jQuery( 'a[href$=' + div.next().attr( 'id' ) + ']' ).click();
          div.next().find( '.bwg_change_gallery_type' ).children().removeClass( 'gallery_type_active' );
          div.next().find( '.bwg_change_gallery_type span:first-child' ).addClass( 'gallery_type_active' );
          jQuery( '#' + div.next().find( '#gallery_types_name > option:selected' ).val() ).css( { 'display': 'none' } );
          div.next().find( '#gallery_types_name > option:selected' ).removeAttr( 'selected' );
          div.next().find( '#gallery_types_name > option:first' ).prop( 'selected', true );
          jQuery( '#' + div.next().find( '#gallery_types_name > option:selected' ).val() ).css( { 'display': 'flex' } );
          div.next().find( '.gallery_type_active:visible' ).click();
          bwg_gallery_type_options( div.next().find( '#gallery_types_name > option:selected' ).val() );
        }
        else if ( jQuery( '.search-div' ).last().attr( 'id' ) == div_id ) {
          jQuery( 'a[href$=' + jQuery( '.search-div' ).first().attr( 'id' ) + ']' ).click();
        }
        else {
          jQuery( 'a[href$=' + div.next().attr( 'id' ) + ']' ).click();
        }
      }
    }
  }
  else if ( sort == 'search_prev' ) {
    if ( !current ) {
      if ( jQuery( '.search-div' ).first().attr( 'id' ) == div_id ) {
        jQuery( 'a[href$=' + jQuery( '.search-div' ).last().attr( 'id' ) + ']' ).click();
        jQuery( '.1' ).attr( 'id', '' );
        jQuery( '.' + jQuery( '.total_matches:visible' ).text() ).attr( 'id', 'search_current_match' );
      }
      else if ( div.is( ':has(div.bwg_change_gallery_type)' ) && !div.find( '.bwg_change_gallery_type span:first-child' ).hasClass( 'gallery_type_active' ) ) {
        var i = 0,
          j = 0;
        div.find( '.gallery_type:visible' ).each( function () {
          if ( jQuery( this ).hasClass( 'gallery_type_active' ) ) {
            jQuery( this ).removeClass( 'gallery_type_active' );
            j = i;
          }
          i++;
        } )
        div.find( '.bwg_change_gallery_type' ).children().eq( j - 1 ).addClass( 'gallery_type_active' );
        div.find( '#gallery_types_name > option:selected' ).removeAttr( 'selected' ).prev( 'option' ).prop( 'selected', true );
        jQuery( '.gallery_type_active:visible' ).click();
        bwg_gallery_type_options( div.find( '#gallery_types_name > option:selected' ).val() );
        jQuery( '.1' ).removeAttr( 'id' );
        var total_matches = jQuery( '.total_matches:visible' ).text();
        jQuery( '.' + total_matches ).attr( 'id', 'search_current_match' );
      }
      else if ( div.prev().is( ':has(div.bwg_change_gallery_type)' ) ) {
        jQuery( 'a[href$=' + div.prev().attr( 'id' ) + ']' ).click();
        div.prev().find( '.bwg_change_gallery_type' ).children().removeClass( 'gallery_type_active' );
        div.prev().find( '.bwg_change_gallery_type span:last-child' ).addClass( 'gallery_type_active' );
        div.prev().find( '#gallery_types_name > option:selected' ).removeAttr( 'selected' );
        div.prev().find( '#gallery_types_name > option:last' ).prop( 'selected', true );
        jQuery( '.gallery_type_active:visible' ).click();
        bwg_gallery_type_options( div.prev().find( '#gallery_types_name > option:selected' ).val() );
        jQuery( '.1' ).removeAttr( 'id' );
        var total_matches = jQuery( '.total_matches:visible' ).text();
        jQuery( '.' + total_matches ).attr( 'id', 'search_current_match' );
      }
      else {
        jQuery( 'a[href$=' + div.prev().attr( 'id' ) + ']' ).click();
        jQuery( '.1' ).removeAttr( 'id' );
        var total_matches = jQuery( '.total_matches:visible' ).text();
        jQuery( '.' + total_matches ).attr( 'id', 'search_current_match' );
      }
    }
    else {
      var total = +jQuery( '.total_matches:visible' ).text();
      ind = +current.indexOf( ' search_highlight' );
      var back = +current.substring( 9, ind ) - 1;
      if ( back > 0 ) {
        jQuery( '#search_current_match' ).removeAttr( 'id' );
        jQuery( '#search_current_color' ).removeAttr( 'id' );
        jQuery( '.' + back.toString() ).attr( 'id', 'search_current_match' );
        jQuery( '.current_match' ).empty();
        jQuery( '.current_match' ).append( back + '/' );
      }
      else if ( back == 0 ) {
        if ( div.is( ':has(div.bwg_change_gallery_type)' ) && !div.find( '.bwg_change_gallery_type span:first-child' ).hasClass( 'gallery_type_active' ) ) {
          var i = 0,
            j = 0;
          div.find( '.gallery_type' ).each( function () {
            if ( jQuery( this ).hasClass( 'gallery_type_active' ) ) {
              jQuery( this ).removeClass( 'gallery_type_active' );
              j = i;
            }
            i++;
          } )
          div.find( '.bwg_change_gallery_type' ).children().eq( j - 1 ).addClass( 'gallery_type_active' );
          div.find( '#gallery_types_name > option:selected' ).removeAttr( 'selected' ).prev( 'option' ).prop( 'selected', true );
          jQuery( '.gallery_type_active:visible' ).click();
          bwg_gallery_type_options( div.find( '#gallery_types_name > option:selected' ).val() );
          jQuery( '.1' ).removeAttr( 'id' );
          var total_matches = jQuery( '.total_matches:visible' ).text();
          jQuery( '.' + total_matches ).attr( 'id', 'search_current_match' );
        }
        else if ( div.prev().is( ':has(div.bwg_change_gallery_type)' ) ) {
          jQuery( 'a[href$=' + div.prev().attr( 'id' ) + ']' ).click();
          div.prev().find( '.bwg_change_gallery_type' ).children().removeClass( 'gallery_type_active' );
          div.prev().find( '.bwg_change_gallery_type span:last-child' ).addClass( 'gallery_type_active' );
          div.prev().find( '#gallery_types_name > option:selected' ).removeAttr( 'selected' );
          div.prev().find( '#gallery_types_name > option:last' ).prop( 'selected', true );
          jQuery( '.gallery_type_active:visible' ).click();
          bwg_gallery_type_options( div.prev().find( '#gallery_types_name > option:selected' ).val() );
          jQuery( '.1' ).removeAttr( 'id' );
          var total_matches = jQuery( '.total_matches:visible' ).text();
          jQuery( '.' + total_matches ).attr( 'id', 'search_current_match' );
        }
        else if ( jQuery( '.search-div' ).first().attr( 'id' ) == div_id ) {
          jQuery( 'a[href$=' + jQuery( '.search-div' ).last().attr( 'id' ) + ']' ).click();
          jQuery( '.1' ).removeAttr( 'id' );
          var total_matches = jQuery( '.total_matches:visible' ).text();
          jQuery( '.' + total_matches ).attr( 'id', 'search_current_match' );
        }
        else {
          jQuery( 'a[href$=' + div.prev().attr( 'id' ) + ']' ).click();
          jQuery( '.1' ).removeAttr( 'id' );
          var total_matches = jQuery( '.total_matches:visible' ).text();
          jQuery( '.' + total_matches ).attr( 'id', 'search_current_match' );
        }
      }
    }
  }
  if ( jQuery( '#search_current_match' ).length ) {
    if ( jQuery( '#search_current_match' ).offset().top > jQuery( window ).height() - 50 ) {
      jQuery( 'html, body' ).animate( {
        scrollTop: jQuery( "#search_current_match" ).offset().top - 300
      }, 5 );
    }
    else {
      jQuery( window ).scrollTop( 0 );
    }
  }
}

function search_options() {
  var val, tab_id, div;
  val = jQuery( '.search_in_options:visible' ).val();
  jQuery( '.search_in_options' ).val( val );
  div = jQuery( '.search-div:visible' );
  tab_id = div.attr( 'id' );
  if ( val ) {
    val = val.toLowerCase().trim();
  }
  jQuery( '.total_matches' ).empty();
  jQuery( '.current_match' ).empty();
  jQuery( '.search_prev' ).hide();
  jQuery( '.search_next' ).hide();
  jQuery( '.search_close' ).hide();
  jQuery( '.search_count' ).hide();
  jQuery( '.wd-group' ).each( function () {
    jQuery( this ).removeClass();
    jQuery( this ).addClass( 'wd-group' );
  } )
  jQuery( '#search_current_match' ).removeAttr( 'id' );
  if ( val != '' ) {
    //css({'padding': '20px 5px 20px 5px','margin': '0 0 0 15px'});
    if ( div.has( '.postbox' ) ) {
      jQuery( '.postbox' ).removeClass( 'closed' );
    }
    jQuery( '.search_prev' ).show();
    jQuery( '.search_next' ).show();
    jQuery( '.search_close' ).show();
    if (val) {
      val = val.replace(/\s{2,}/g, ' ');
    }
    var matchcount = 0;
    jQuery( '#' + tab_id ).find( '.wd-group' ).each( function () {
      if ( jQuery( this ).is( ':visible' ) ) {
        var label, description;
        label = jQuery( this ).find( '.wd-label' ).text().toLowerCase();
        description = jQuery( this ).find( 'p.description' ).text().toLowerCase();
        if ( label.match( val, 'gi' ) || description.match( val, 'gi' ) ) {
          matchcount = matchcount + 1;
          if ( matchcount == 1 ) {
            jQuery( this ).addClass( matchcount + ' search_highlight' );
            jQuery( this ).attr( 'id', 'search_current_match' );
          }
          else {
            jQuery( this ).addClass( matchcount + ' search_highlight' );
          }
        }
      }
    } )
    if ( jQuery( '#search_current_match' ).length ) {
      if ( jQuery( '#search_current_match' ).offset().top > jQuery( window ).height() - 50 ) {
        jQuery( 'html, body' ).animate( {
          scrollTop: jQuery( "#search_current_match" ).offset().top - 100
        }, 5 );
      }
      else {
        jQuery( window ).scrollTop( 0 );
      }
    }
    jQuery( '.total_matches' ).show();
    jQuery( '.total_matches' ).append( matchcount );
    if ( matchcount != 0 ) {
      jQuery( '.current_match' ).append( 1 + '/' );
      if ( jQuery( '#search_current_match' ).offset().top > jQuery( window ).height() - 50 ) {
        jQuery( 'html, body' ).animate( {
          scrollTop: jQuery( "#search_current_match" ).offset().top - 100
        }, 5 );
      }
      else {
        jQuery( window ).scrollTop( 0 );
      }
    }
    else if ( matchcount == 0 ) {
      jQuery( '.current_match' ).append( 0 + '/' );
    }
  }
  else {
    jQuery( '.total_matches' ).empty();
    jQuery( '.current_match' ).empty();
  }
  search_options_for_count();
}

function search_options_for_count() {
  var val, tab_id, div;
  val = jQuery( '.search_in_options:visible' ).val();
  jQuery( '.search_in_options' ).val( val );
  if ( val != '' ) {
    jQuery( '.search-div' ).each( function () {
      div = jQuery( this );
      tab_id = div.attr( 'id' );
      val = val.toLowerCase().trim();
      jQuery( '#' + tab_id + '_bage' ).empty();
      if ( div.has( '.postbox' ) ) {
        jQuery( '.postbox' ).removeClass( 'closed' );
      }
      val = val.replace( /\s{2,}/g, ' ' );
      var matchcount = 0;
      jQuery( '#' + tab_id ).find( '.wd-group' ).each( function () {
        var label, description;
        label = jQuery( this ).find( '.wd-label' ).text().toLowerCase();
        description = jQuery( this ).find( 'p.description' ).text().toLowerCase();
        if ( label.match( val, 'gi' ) || description.match( val, 'gi' ) ) {
          matchcount = matchcount + 1;
        }
      } )
      if ( matchcount > 0 ) {
        jQuery( '#' + tab_id + '_bage' ).html( matchcount ).show();
      }
    } )
  }
  else {
  }
}

/**
 * Get value of keyboard key
 *
 * @param $object
 *
 * @return bool
 */
function which_key_is_pressed( $object ) {
  if($object.keyCode !== undefined) {
     return ($object.keyCode);
  }
 else if($object.key !== undefined ) {
     return ($object.key);
  }
}