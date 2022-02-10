jQuery(function($) {

	var LivePreview = $( "input[name='wprmenu_options[wpr_live_preview]']:checked" ).val();
  
  if( wprmenu_params.enable_preview == '1' ) {
  	$( '#wpadminbar .wprmenu-mobile-preview-btn' ).find( '.ab-item' ).show();
  }
  else {
  	$( '#wpadminbar .wprmenu-mobile-preview-btn' ).find( '.ab-item' ).hide();
  }

	//Demo import section
  $( 'body' ).on( 'click' , '.wprmenu-data.import-demo', function(e) {
    e.preventDefault();
    var SelectedButton = $( this );
    var SelectedButtonText = $( this ).text();
    var SelectedNode = $( this ).parents( '.wprmenu-content' ).find( '.wprmenu-content-image' );
    var DemoType = SelectedNode.attr( 'data-demo-type' );
    var DemoId = SelectedNode.attr( 'data-demo-id' );
    var SettingsId = SelectedNode.attr( 'data-settings' );

    if( SettingsId == '' &&  SelectedButton.hasClass( 'required-pro' ) ) {
      Swal({
        type  : 'info',
        title : wprmenu_params.import_error_title,
        text  : wprmenu_params.pro_version_error,
      });
      return false;
    }

    if( SettingsId !== '' 
      && DemoType !== ''
      && DemoId !== '' ) {
      SelectedButton.text( wprmenu_params.please_wait );
      
      $.ajax({
        type    : 'POST',
        url     : wprmenu_params.ajax_url,
        data    : 'settings_id='+ SettingsId + '&demo_id=' + DemoId + '&demo_type=' +DemoType+ '&action=wprmenu_import_data',
        success : function( response ) {
          response = $.parseJSON( response );

          if( response.status == 'success' ) {
            wprmenu_setCookie( 'wprmenu_live_preview', '', 1 );
            Swal({
              type  : 'success',
              title : wprmenu_params.import_done,
              text  : wprmenu_params.please_reload,
            }).then(function () {
              location.reload();
            }).catch(swal.noop);
            
            SelectedButton.text( wprmenu_params.import_done );
            
          }
          else {
            Swal({
              type  : 'error',
              title : wprmenu_params.import_error_title,
              text  : wprmenu_params.import_error,
            });

            SelectedButton.text( wprmenu_params.import_error );
          }
        }
      });
    }
  });

  $.exitIntent( 'enable' );
  $(document).bind( 'exitintent', function() {
    
    $check_cookie = wprmenu_getCookie( 'wprmenu_live_preview' );

    if( $check_cookie !== 'yes' )
      return;
    
    const swalWithBootstrapButtons = Swal.mixin({
      confirmButtonClass: 'btn btn-primary',
      cancelButtonClass: 'btn btn-secondary',
      buttonsStyling: false,
    })

    swalWithBootstrapButtons({
      title: '<strong>'+wprmenu_params.navigating_away+'</strong>',
      type: 'info',
      html: wprmenu_params.confirm_message,
      showCloseButton: true,
      showCancelButton: true,
      focusConfirm: false,
      confirmButtonText:'Save Changes',
      cancelButtonText: 'Don\'t Save Changes'
    }).then((result) => {
      if (result.value) {
        
        $.ajax({
          type    : 'POST',
          url     : wprmenu_params.ajax_url,
          data    : 'action=wpr_get_transient_from_data',
          success : function( response ) {
            response = $.parseJSON( response );

            if( response.status == 'success' ) {
              wprmenu_setCookie( 'wprmenu_live_preview', '', 1 );

              swalWithBootstrapButtons(
                wprmenu_params.options_saved,
                wprmenu_params.options_saved_msg,
                'success'
              ).then(function () {
              location.reload();
            }).catch(swal.noop);
            }
            else {
              Swal({
                type  : 'error',
                title : wprmenu_params.import_error_title,
                text  : wprmenu_params.import_error,
              });
            }
          }
        });

      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
        wprmenu_setCookie( 'wprmenu_live_preview', '', 1 );
        swalWithBootstrapButtons(
          wprmenu_params.options_not_saved,
          'The recent changes are reverted back',
          'error'
        )
      }
    })
  });

   // Set Cookie
  function wprmenu_setCookie( cname, cvalue, exdays ) {
    var d = new Date();
    d.setTime( d.getTime() + (exdays*24*60*60*1000) );
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires + ";path=/";
  }

  // Get Cookie
  function wprmenu_getCookie( cname ) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0)==' ') c = c.substring(1);
      if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    }
    return "";
  }

  jQuery( 'body' ).on( 'click', '.wprmenu-mobile-preview-btn .ab-item', function () {

    var $btn = jQuery( this  );
    var padding_top = 30;
    
    if ( jQuery('#wprmenu-mobile-preview').length > 0 ) {
      var $mobile_preview = jQuery( '#wprmenu-mobile-preview' );
    } else {
      var $mobile_preview = jQuery( '<div id="wprmenu-mobile-preview" style="display: none;"></div>' );
      jQuery( 'body' ).append( $mobile_preview );
    }

    if( $mobile_preview.is( ':visible' ) ) {
      $mobile_preview.slideUp( 'fast', function () {
        jQuery( this ).remove();
        $btn.parent().removeClass( 'hover' );
        $btn.find( '.dashicons' ).addClass( 'dashicons-visibility' ).removeClass( 'dashicons-hidden' );
      });
      return;
    }

    $mobile_preview.html('');

    var viewport_height = jQuery(window).height() - jQuery( '#wpadminbar' ).height() - 15;
    var scale_ratio = 1;
    
    if ( viewport_height < ( 701 + ( padding_top * 2 ) + 10 ) ) {
      scale_ratio = viewport_height / ( 701 + ( padding_top * 2 ) + 10 );
    }

    var left_offset = jQuery( '.wprmenu-mobile-preview-btn .ab-item' ).offset().left;
    var right_boundary = left_offset + ( scale_ratio * ( 375+40 ) );
    
    if( right_boundary > jQuery( window ).outerWidth() ) {
      left_offset = left_offset - ( right_boundary - jQuery(window).outerWidth() );
    }

    left_offset = left_offset - ( 25*scale_ratio );
    var $mobile_preview_frame = jQuery('<iframe src="'+window.wprmenu_params.preview_url+'?__show_admin_bar=0&__mobile_preview=1" style="width: 375px; height: 701px; border: 1px solid #000; background: #FFF; border-radius: 10px;" frameborder="0"></iframe>');
    
    $mobile_preview.append( $mobile_preview_frame );
    $mobile_preview.css({
      'position': 'fixed',
      'top': '32px',
      'left': left_offset+'px',
      'display': 'none',
      'background': '#FFF',
      'padding': padding_top+'px 5px',
      'border-top': 'none',
      'border-radius': '25px',
      'background': jQuery('.wprmenu-mobile-preview-btn .ab-item').css('background-color'),
      'box-shadow': 'rgba(0, 0, 0, 0.23) 3px 3px 8px 1px',
      'z-index': '100000',
      '-ms-zoom': scale_ratio,
      '-moz-transform': 'scale('+scale_ratio+')',
      '-moz-transform-origin': 'left top',
      '-o-transform': 'scale('+scale_ratio+')',
      '-o-transform-origin': 'left top',
      '-webkit-transform': 'scale('+scale_ratio+')',
      '-webkit-transform-origin': 'left top'
    });
    
    $mobile_preview_frame.on( 'load', function () {
      jQuery( '#wpadminbar' , $mobile_preview_frame[0].contentDocument).remove();
      jQuery( 'body' , $mobile_preview_frame[0].contentDocument).removeClass( 'admin-bar' );
      jQuery( 'html' , $mobile_preview_frame[0].contentDocument).attr( 'style' , 'margin-top: 0!important' );
      jQuery( '* html body', $mobile_preview_frame[0].contentDocument).attr( 'style' , 'margin-top: 0!important' );
    });

    $btn.parent().addClass( 'hover' );
    
    $btn.find( '.dashicons' ).removeClass( 'dashicons-visibility' ).addClass( 'dashicons-hidden' );
    
    $mobile_preview.slideDown();
  });

  //Demo data 
  $('.mg-box-content').find('li.wprmenu-data-list').hover(function() {
    $( this ).find( '.wprmenu-content' ).toggleClass( 'overlay' );
    $( this ).find( '.wprmenu-content' ).toggleClass( 'image-overlay' );
  });

});