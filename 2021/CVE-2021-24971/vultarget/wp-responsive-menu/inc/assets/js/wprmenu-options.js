/**
 * Custom scripts needed for the colorpicker, image button selectors,
 * and navigation tabs.
 */

jQuery( document ).ready( function($) {
  /**
  ----------------------------------------
  *
  * Default Configs
  *
  ----------------------------------------
  */

  $( 'select#google_web_font_family' ).select2();
  $( 'select#google_font_family' ).select2();

  $( '.wprmenu-hide-menu-pages' ).select2();

  // Loads the color pickers
  $( '.of-color' ).wpColorPicker();

  $( '[data-toggle="tooltip"]' ).tooltip();

  $( '.of-radio-img-label' ).hide();
  $( '.of-radio-img-img' ).show();
  $( '.of-radio-img-radio' ).hide();


  /**
  ----------------------------------------
  *
  * Pro Option Info
  *
  ----------------------------------------
  */
  var ProHtml = '<div class="wpr-pro-block"><span><a target="_blank" href="'+wprmenu_params.ugrade_pro_link+'">'+wprmenu_params.upgrade_to_pro+'</a></span></div>';

  $( '#wpr_optionsframework .pro-feature' ).append( ProHtml );

  $( '.pro-feature' ).hover( function() {
    $( this ).find( '.wpr-pro-block' ).toggleClass( 'show' );
  });


  /**
  ----------------------------------------
  *
  * Image Option
  *
  ----------------------------------------
  */
  $( '.of-radio-img-img' ).click( function(){
    $( this ).parent().parent().find( '.of-radio-img-img' ).removeClass('of-radio-img-selected');
    $( this ).addClass( 'of-radio-img-selected' );
  });


  /**
  ----------------------------------------
  *
  * Color Picker
  *
  ----------------------------------------
  */
  if (typeof $.fn.wpColorPicker !== 'undefined') {
    var calcLuminance = function calcLuminance(rgb) {
      var c = rgb.substring(1);

      var _rgb = parseInt(c, 16);

      var r = _rgb >> 16 & 0xff;
      var g = _rgb >> 8 & 0xff;
      var b = _rgb >> 0 & 0xff;
      return 0.2126 * r + 0.7152 * g + 0.0722 * b;
    };

    var formColor = jQuery( '#wpr_optionsframework' ).find( ".form-control.form-color" );

    $( formColor ).each(function () {
      var picker = $( this );
      var pickerWrap = $( this ).closest( '.wprmenu-color-picker' );
      var eyedropper = pickerWrap.find( '.eyedropper' );
      picker.css( 'background-color', picker.val() );

      if ( calcLuminance( picker.val() ) > 175 ) {
        picker.css( 'color', '#000000' );
      }

      picker.iris({
        mode: 'hsv',
        target: $( this ).parent().parent(),
        change: function change( event, ui ) {
          if ( calcLuminance( ui.color.toCSS() ) > 175 ) {
            $( this ).css( 'color', '#000000' );
          } else {
            $( this ).css( 'color', '' );
          }

          $( this ).css( 'background-color', ui.color.toCSS() );
        }
      });
      
      pickerWrap.on( 'click', '.iris-square-value' , function (e) {
        e.preventDefault();
        picker.iris( 'toggle' );
      });
      
      eyedropper.on( 'click', function (e) {
        e.preventDefault();
        picker.iris( 'toggle' );
      });
      
      picker.on( 'focusin', function () {
        picker.iris( 'show' );
      });
    });
  }


  /**
  ----------------------------------------
  *
  * Checkbox Option
  *
  ----------------------------------------
  */
  $( '.wprmenu_checkbox_container' ).each(function(){
    var _self = $(this);
    if( $( this ).find( 'input' ).is( ':checked' ) ) {
      _self.addClass( 'checked' );
    }
  });
  
  jQuery( 'body' ).on( 'click', '.wprmenu_checkbox_container', function() {
    $( this ).toggleClass( 'checked' );
    if( $( this ).hasClass( 'checked' ) ) {
      $( this ).find( 'input' ).prop( 'checked', true );
    }
    else {
      $( this ).find( 'input' ).prop( 'checked', false );
    }
  });


  /**
  ----------------------------------------
  *
  * WPR MENU Save Settings
  *
  ----------------------------------------
  */
  $( 'body' ).on( 'click', '#wpr_optionsframework-submit .save-settings', function(e) {
    e.preventDefault();
    $( 'form#wpr_form_settings' ).submit();
  });

  /**
  ----------------------------------------
  *
  * WPR MENU Reset Settings
  *
  ----------------------------------------
  */
  $( 'body' ).on( 'click', '#wpr_optionsframework-submit .reset-settings', function(e) {
    var ConfirmString = confirm( wprmenu_params.reset_text );
    
    if( ConfirmString == true ) {
      $( 'form#wpr_form_settings' ).find( '.reset-button.wpr-reset-button' ).trigger( 'click' );
    }
  });


  /**
  ----------------------------------------
  *
  * Show Cart In Header Elements 
  * if woocommerce is active
  *
  ----------------------------------------
  */
  if( wprmenu_params.woocommerce_integration !== 'yes' ) {
    $( '#wpr-header-sortable li' ).each( function( index, elem ) {
      if( $( this ).attr( 'data-id' ) == 'cart' ) {
        $( this ).addClass( 'wpr-hide-element' );
      }
      else {
        $( this ).addClass( 'wpr-show-element' );
      }
    })
  }


  /**
  ----------------------------------------
  *
  * Sortable Menu Items
  *
  ----------------------------------------
  */
  $( '#wpr-sortable' ).sortable({
    update: function( event, ui ) {
      var order = []; 
      $( '#wpr-sortable li' ).each( function(e) {
        order.push( $(this).attr('id'));
      });
      $( '#wpr_optionsframework' ).find( 'input#order_menu_items' ).val( order );
     }
  });
  $( "#wpr-sortable" ).disableSelection();
  
  
  /**
  ----------------------------------------
  *
  * Tabbed Elements
  *
  ----------------------------------------
  */
  if ( $( '.mg-navtabs-wrapper' ).length > 0 ) {
    options_framework_tabs();
  }

  function options_framework_tabs() {
    var $group = $('.group'),
      $navtabs = $('.mg-navtabs-wrapper a'),
      active_tab = '';

    // Hides all the .group sections to start
    $group.hide();

    // Find if a selected tab is saved in localStorage
    if (  typeof( localStorage ) != 'undefined'  ) {
      active_tab = localStorage.getItem( 'active_tab' );
    }

    // If active tab is saved and exists, load it's .group
    if ( active_tab != '' && $( active_tab ).length ) {
      $( active_tab ).fadeIn();
      $( active_tab + '-tab' ).addClass( 'nav-tab-active' );
    } else {
      $( '.group:first' ).fadeIn();
      $( '.mg-navtabs-wrapper a:first' ).addClass( 'nav-tab-active' );
    }

    // Bind tabs clicks
    $navtabs.click( function( e ) {

      e.preventDefault();

      // Remove active class from all tabs
      $navtabs.removeClass( 'nav-tab-active' );

      $( this ).addClass( 'nav-tab-active' ).blur();

      if ( typeof( localStorage ) != 'undefined' ) {
        localStorage.setItem( 'active_tab', $( this ).attr( 'href' ) );
      }

      var selected = $( this ).attr( 'href' );

      $group.hide();
      $( selected ).fadeIn();
    });
  }

  /**
  ----------------------------------------
  * 
  * Live Preview Enable / Disable
  *
  ----------------------------------------
  **/
  var LivePreview = $( "input[name='wprmenu_options[wpr_live_preview]']:checked" ).val();
  
  if( LivePreview == 'on' ) {
    $( '#wpadminbar .wprmenu-mobile-preview-btn' ).find( '.ab-item' ).show();
  }
  else {
    $( '#wpadminbar .wprmenu-mobile-preview-btn' ).find( '.ab-item' ).hide();
  }

  $( 'body' ).on( 'click', '#wprmenu_options_wpr_live_preview', function() {
    if( $( this ).hasClass( 'checked' ) ) {
      $( '#wpadminbar .wprmenu-mobile-preview-btn' ).find( '.ab-item' ).show();
    }
    else {
      $( '#wpadminbar .wprmenu-mobile-preview-btn' ).find( '.ab-item' ).hide();
    }
  });


  /**
  ----------------------------------------
  * 
  * Menu Slide Options
  *
  ----------------------------------------
  **/
  var slideOpt = $( '#section-slide_type option:selected' ).val();
  if ( slideOpt == 'bodyslide' ) {
    $( '#section-position option:eq( 2 ), #section-position option:eq( 3 )' ).css( 'display', 'none' );
  }
  
  $( '#slide_type' ).change( function() {
    if ( $( this ).val() == 'bodyslide' ) {
      $( '#section-position option:eq( 2 ), #section-position option:eq( 3 )' ).css( 'display', 'none' );
    }
    else {
      $( '#section-position option:eq( 2 ), #section-position option:eq( 3 )' ).css( 'display', 'block' );
    }
  });


  /**
  ----------------------------------------
  * 
  * Menu Icon Position Options
  *
  ----------------------------------------
  **/
  var menutype = $( "input[name='wprmenu_options[menu_type]']:checked" ).val();
  if ( menutype == 'default' ) {
    $( '#section-custom_menu_top, #section-custom_menu_left, #section-custom_menu_bg_color' ).css(  'display', 'none' );
    $( '#section-hide_menubar_on_scroll' ).css(  'display', 'block' );
  }
  else {
    $( '#section-hide_menubar_on_scroll' ).css(  'display', 'none' );
  }

  $( "#section-menu_type input" ).on( 'change', function() {
    var menutype = $( 'input[name="wprmenu_options[menu_type]"]:checked', '#section-menu_type' ).val();
    if( menutype == 'default' ) {
      $( '#section-hide_menubar_on_scroll' ).css(  'display', 'block' );
    }
    else {
      $( '#section-hide_menubar_on_scroll' ).css(  'display', 'none' );
    }
  });

  $( '#section-menu_icon_type input' ).on( 'change', function() {
    var menuIconType = $( 'input[name="wprmenu_options[menu_icon_type]"]:checked', '#section-menu_icon_type' ).val(); 
    
    if ( menuIconType == 'default' ) {
      $( '#section-menu_icon, #section-custom_menu_icon_top, #section-custom_menu_font_size, #section-menu_close_icon' ).css(  'display', 'none' );
    }
    else {
      $( '#section-menu_icon, #section-custom_menu_font_size, #section-custom_menu_icon_top, #section-menu_close_icon' ).css(  'display', 'block' );           
    }
  });

  var menu_icon_type = $( "input[name='wprmenu_options[menu_icon_type]']:checked" ).val();
  if ( menu_icon_type == 'default' ) {
    $( '#section-menu_icon' ).css(  'display', 'none' );
    $( '#section-custom_menu_font_size' ).css(  'display', 'none' );
    $( '#section-custom_menu_icon_top' ).css(  'display', 'none' );
    $( '#section-menu_close_icon' ).css(  'display', 'none' );
    $( '#section-menu_close_icon' ).css(  'display', 'none' );
  }
  else {
    $( '#section-menu_icon' ).css( 'display', 'block' );
    $( '#section-custom_menu_font_size' ).css( 'display', 'block' );
    $( '#section-custom_menu_icon_top' ).css( 'display', 'block' );
    $( '#section-menu_close_icon' ).css( 'display', 'block' );
  }


  /**
  ----------------------------------------
  * 
  * WooCommerce Integartion Options
  *
  ----------------------------------------
  **/
  var woocommerce_integration = $( "input[name='wprmenu_options[woocommerce_integration]']:checked" ).val();

  if( woocommerce_integration == 'on' ) 
    $( 'div#wpr_optionsframework' ).find( 'div.woocommerce' ).show();
  
  else
    $( 'div#wpr_optionsframework' ).find( 'div.woocommerce' ).hide();

  $( 'body' ).on( 'click', '#wprmenu_options_woocommerce_integration' , function() {
    if( $( this ).hasClass( 'checked' ) ) {
      $( 'div#wpr_optionsframework' ).find( 'div.woocommerce' ).show();
    }
    else {
      $( 'div#wpr_optionsframework' ).find( 'div.woocommerce' ).hide();
    }
  });

  
  $( '#section-menu_type input' ).on( 'change', function() {
    var menuType = $( 'input[name="wprmenu_options[menu_type]"]:checked', '#section-menu_type' ).val(); 
    
    if ( menuType == 'default' ) {
      $( '#section-custom_menu_top' ).css( 'display', 'none' );
      $( '#section-custom_menu_left' ).css( 'display', 'none' );
      $( '#section-custom_menu_bg_color' ).css( 'display', 'none' );        
    }
    else {
      $( '#section-custom_menu_top' ).css( 'display', 'block' );
      $( '#section-custom_menu_left' ).css( 'display', 'block' );
      $( '#section-custom_menu_bg_color' ).css( 'display', 'block' );                 
    }
  });

  

  /**
  ----------------------------------------
  * 
  * Show Minify Options
  *
  ----------------------------------------
  **/
  $( 'body' ).on( 'click', '#wprmenu_options_wpr_enable_external_css', function() {
    if( $( this ).hasClass( 'checked' ) ) {
       $( '#section-wpr_enable_minify' ).css( 'display', 'block' );
    }
    else {
      $( '#section-wpr_enable_minify' ).css( 'display', 'none' );
    }
  });


  var showMinify = $( "input[name='wprmenu_options[wpr_enable_external_css]']:checked" ).val();

  if( showMinify == 'on' || showMinify == 'yes' ) {
    $( '#section-wpr_enable_minify' ).css( 'display', 'block' );
  }
  else {
    $( '#section-wpr_enable_minify' ).css( 'display', 'none' );
  }

  //wpml menu add new lang menu
  $('body').on( 'click', '.wpml-new', function(){
    MenuField = $( 'div.wpr-menu-fields' ).find( '.wpr-new-fields:first-child' ).clone().html();
    //field = '<div class="wpr-new-fields"><input type="text" name="wprmenu_options[social][icon][]" class="wpr-icon-picker" value=""><input type="text" name="wprmenu_options[social][link][]" placeholder="Enter your url here" class="social_link form-control" value=""><input type="button" value="Remove" class="wpr-remove-field btn btn-danger"></div>';
    $( '.wpr-menu-fields' ).append( MenuField );
  });

  //social icon add new 
  $( 'body' ).on( 'click', '.wpr-add-new', function(){
    field = '<div class="wpr-new-fields"><input type="text" name="wprmenu_options[social][icon][]" class="wpr-icon-picker" value=""><input type="text" name="wprmenu_options[social][link][]" placeholder="Enter your url here" class="social_link form-control" value=""><input type="button" value="Remove" class="wpr-remove-field btn btn-danger pull-right"><div class="clear"></div></div>';
    $( '.wpr-social-fields' ).append( field );
    createIconpicker();
  });

  
  $( 'body' ).on( 'click', '.wpr-new-fields .btn', function(){
    if ( confirm( wprmenu_params.social_link_remove_confirmation ) )
      $(this).parent().remove();
  });

  $( 'select#google_font_type' ).on( 'change', function() {
    var selectedFontType = $( this ).val();
    if( selectedFontType == 'standard' ) {
      $( 'div.wpr_web_font_family' ).hide();
      $( 'div.wpr_font_family' ).show();
    }
    else {
      $( 'div.wpr_web_font_family' ).show();
      $( 'div.wpr_font_family' ).hide();
    }
  });

  if( wprmenu_params.font_type == 'web_fonts' ) {
    $( 'div.wpr_web_font_family' ).show();
    $( 'div.wpr_font_family' ).hide();
  }
  else {
    $( 'div.wpr_web_font_family' ).hide();
    $( 'div.wpr_font_family' ).show();
  }

  $( 'div#section-google_font_family' ).find( 'span.select2-container' ).width( '100%' );

  $( 'select#google_web_font_family' ).on('change', function() {
    var SelectedFont = $( this ).val();
  });


  /**
  ----------------------------------------
  * 
  * Create IconPicker
  *
  ----------------------------------------
  **/
  function createIconpicker() {
    var iconPicker = $( '.wpr-icon-picker' ).fontIconPicker({
      theme: 'fip-bootstrap'
    }),icomoon_json_icons = [],
    icomoon_json_search = [];
    // Get the JSON file
    $.ajax({
      url: wprmenu_params.options_path + '/assets/icons/selection.json',
      type: 'GET',
      dataType: 'json'
    })
    .done(function(response) {
      // Get the class prefix
      var classPrefix = response.preferences.fontPref.prefix;
      
      $.each(response.icons, function(i, v) {
        // Set the source
        icomoon_json_icons.push( classPrefix + v.properties.name );
  
        // Create and set the search source
        if ( v.icon && v.icon.tags && v.icon.tags.length ) {
          icomoon_json_search.push( v.properties.name + ' ' + v.icon.tags.join(' ') );
        } else {
          icomoon_json_search.push( v.properties.name );
        }
      });
    
      setTimeout(function() {
        // Set new fonts
        iconPicker.setIcons(icomoon_json_icons, icomoon_json_search);
        
      }, 1000);
    })
    .fail(function() {
      // Show error message and enable
      alert('Failed to load the icons, Please check file permission.');
    });
  }
  createIconpicker();


  //Show widget menu tab if widget menu is active
  var widgetMenu = $( "input[name='wprmenu_options[wpr_enable_widget]']:checked" ).val();

  if( widgetMenu == 'on' ) {
    $( 'div.mg-navtabs-wrapper' ).find( 'a.widgetmenu-tab' ).show();
  }
  else {
    $( 'div.mg-navtabs-wrapper' ).find( 'a.widgetmenu-tab' ).hide();
  }

  $( 'body' ).on( 'click', '#wprmenu_options_wpr_enable_widget', function() {
    if( $(this).hasClass( 'checked' ) ) {
      $( 'div.mg-navtabs-wrapper' ).find( 'a.widgetmenu-tab' ).show();
    }
    else {
      $( 'div.mg-navtabs-wrapper' ).find( 'a.widgetmenu-tab' ).hide();
    }
  });

  var container = jQuery( '#wpr_custom_css' );
  container.width( container.parent().width() ).height( 200 );

  var editor = ace.edit( "wpr_custom_css" );
  container.css( 'width', 'auto' );
  editor.setValue( container.siblings('textarea').val() );
  editor.setTheme( "ace/theme/chrome" );
  editor.getSession().setMode( 'ace/mode/css' );
  editor.setShowPrintMargin( false );
  editor.setHighlightActiveLine( false );
  editor.gotoLine( 1 );
  editor.session.setUseWorker( false );

  editor.getSession().on( 'change', function(e) {
    $( editor.container ).siblings( 'textarea' ).val( editor.getValue() );
  });


  $( '.mg-box-content' ).find( 'li.wprmenu-data-list' ).hover( function() {
    $( this ).find( '.wprmenu-content' ).toggleClass( 'overlay' );
    $( this ).find( '.wprmenu-content' ).toggleClass( 'image-overlay' );
  });


  //Hide top and bottom options when push menu is activated
  var MenuSlideStyle = $( "input[name='wprmenu_options[slide_type]']:checked" ).val();
  if( MenuSlideStyle == 'bodyslide' ) {
    $( 'label[for="wprmenu_options-position-top"]' ).css( 'display', 'none' );
    $( 'label[for="wprmenu_options-position-bottom"]' ).css( 'display', 'none' );
  }
  
  $( '#section-slide_type input' ).on( 'change', function() {
    var SlideType = $( 'input[name="wprmenu_options[slide_type]"]:checked' ).val(); 
    
    if (  SlideType == 'bodyslide' ) {
      $( 'label[for="wprmenu_options-position-top"]' ).css( 'display', 'none' );
      $( 'label[for="wprmenu_options-position-bottom"]' ).css( 'display', 'none' );        
    }
    else {
      $( 'label[for="wprmenu_options-position-top"]' ).css( 'display', 'block' );
      $( 'label[for="wprmenu_options-position-bottom"]' ).css( 'display', 'block' );               
    }
  });

  /**
  ----------------------------------------
  * 
  * Mobile Preview
  *
  ----------------------------------------
  */

  jQuery( 'body' ).on( 'click', '.wprmenu-mobile-preview-btn .ab-item', function () {
    var $btn = jQuery(this);
    var padding_top = 30;
    
    if( jQuery( '#wprmenu-mobile-preview' ).length > 0 ) {
      var $mobile_preview = jQuery( '#wprmenu-mobile-preview' );
    } else {
      var $mobile_preview = jQuery( '<div id="wprmenu-mobile-preview" style="display: none;"></div>' );
      jQuery( 'body' ).append( $mobile_preview );
    }

    if( $mobile_preview.is(':visible') ) {
      $mobile_preview.slideUp( 'fast', function () {
        jQuery( this ).remove();
        $btn.parent().removeClass( 'hover' );
        $btn.find( '.dashicons' ).addClass( 'dashicons-visibility' ).removeClass( 'dashicons-hidden' );
      });
      return;
    }

    $mobile_preview.html('');

    var viewport_height = jQuery( window ).height() - jQuery( '#wpadminbar' ).height() - 15;
    var scale_ratio = 1;
    
    if( viewport_height < ( 701 + ( padding_top * 2 ) + 10 ) ) {
      scale_ratio = viewport_height / ( 701 + ( padding_top * 2 ) + 10 );
    }

    var left_offset = jQuery('.wprmenu-mobile-preview-btn .ab-item').offset().left;
    var right_boundary = left_offset + (scale_ratio * (375+40));
    
    if( right_boundary > jQuery( window ).outerWidth() ) {
      left_offset = left_offset - ( right_boundary - jQuery( window ).outerWidth() );
    }

    left_offset = left_offset - ( 25 * scale_ratio );
    var $mobile_preview_frame = jQuery( '<iframe src="'+window.wprmenu_params.preview_url+'?__show_admin_bar=0&__mobile_preview=1" style="width: 375px; height: 701px; border: 1px solid #000; background: #FFF; border-radius: 10px;" scroll="yes" frameborder="0"></iframe>' );
    
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
      jQuery( '#wpadminbar', $mobile_preview_frame[0].contentDocument ).remove();
      jQuery( 'body', $mobile_preview_frame[0].contentDocument ).removeClass( 'admin-bar' );
      jQuery( 'html', $mobile_preview_frame[0].contentDocument ).attr( 'style', 'margin-top: 0!important' );
      jQuery( '* html body', $mobile_preview_frame[0].contentDocument ).attr( 'style', 'margin-top: 0!important' );
    });

    $btn.parent().addClass('hover');
    
    $btn.find( '.dashicons' ).removeClass( 'dashicons-visibility' ).addClass( 'dashicons-hidden' );
    
    $mobile_preview.slideDown();
  });


});