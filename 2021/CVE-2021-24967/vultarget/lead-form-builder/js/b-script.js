/*
 *Tabs in admin area
 */
jQuery(function() {
// *Send data to save by Ajax 
    if(jQuery('#sortable').length){
        jQuery( "#sortable tbody" ).sortable();
    }

    if(jQuery('.datepicker').length){
        jQuery( ".datepicker" ).datepicker({
            dateFormat : "dd MM yy"
        });
    }

 /** Form Color option start **/    
    //open the lateral panel
  
     if(jQuery('.lfb-cd-btn').length){
      // url reset
      var resetUrl = window.location.href;
    $newUrl = resetUrl.substring(0, resetUrl.indexOf('&reset'));
    if($newUrl!=''){
    window.history.pushState('page', 'title', $newUrl);
    }

    jQuery( "#lfb-accordion" ).accordion({
        collapsible: true,
        heightStyle: "content",
      });

        jQuery('.cd-panel').addClass('is-visible');
        jQuery('.lfb-cd-btn').on('click', function(event){
            event.preventDefault();
        jQuery('.cd-panel').addClass('is-visible');
            });

            jQuery("#saveColor").css({"background":'#000'});
            jQuery("#saveColor").val('Save Changes');
            jQuery( "#saveColor, .saveColor" ).prop( "disabled", true );
   
    //code the lateral panel
    jQuery('.cd-panel-close').on('click', function(event){
        if( jQuery(event.target).is('.cd-panel') || jQuery(event.target).is('.cd-panel-close') ) { 
            jQuery('.cd-panel').removeClass('is-visible');
            event.preventDefault();
        }
    });

    jQuery('#lfb_custom_css').on('keyup', function(event){
        var cssValue = jQuery('#lfb_custom_css').val();
        lfbColorChanger(cssValue);
    });

  // form width expand
      var handleFormWidth = jQuery( "#lfb-formwidth-handle" );
      var valueFormWidth = jQuery('#lfb_form_width').val();
      var valueFormWidthRange = jQuery('#lfb_form_width').val();
      jQuery( "#lfb-formwidth" ).slider({
      max: 100,
      value: valueFormWidth,
      create: function() {
        handleFormWidth.text( jQuery( this ).slider( "value" ) );
        jQuery('.lfb-form-size').text('Form Width in ('+valueFormWidthRange+'%)');
        jQuery(".leadform-show-form").css("max-width", valueFormWidthRange +"%");  
        jQuery("#lfb-formwidth-handle-rng").css("width", valueFormWidthRange +"%");
      },
      slide: function( event, ui ) {
        handleFormWidth.text( ui.value );
        jQuery('#lfb_form_width').val(ui.value);
        jQuery('.lfb-form-size').text('Form Width in ('+ui.value+'%)');
        jQuery(".leadform-show-form").css("max-width", ui.value +"%");
        jQuery("#lfb-formwidth-handle-rng").css("width", ui.value +"%");
        saveButtonActive(); 
      }
    });


// heading manage
      var handleHead = jQuery( "#lfb-heading-handle" );
      var valueHead = jQuery('#lfb_heading_font_size').val();
      var valueRange = jQuery('#lfb_heading_font_size').val();
      jQuery( "#lfb-heading-font" ).slider({
      max: 99,
      value: valueHead,
      create: function() {
        handleHead.text( jQuery( this ).slider( "value" ) );
        jQuery('.lfb-header-fontsize').text('Font Size ('+valueRange+'%)');
        jQuery("#lfb-heading-handle-rng").css("width", valueRange +"%");  
      },
      slide: function( event, ui ) {
        handleHead.text( ui.value );
        jQuery('#lfb_heading_font_size').val(ui.value);
        jQuery('.lfb-header-fontsize').text('Font Size ('+ui.value+'%)');
        jQuery(".lead-form-front h1").css("font-size", ui.value);
        jQuery("#lfb-heading-handle-rng").css("width", ui.value +"%");
        
        saveButtonActive(); 
      }
    });
      // header top/bottom aligment
      var handleHeadTB = jQuery( "#lfb-header-algmnt-tb-handle" );
      var valueHeadTB = jQuery('#lfb_header_algmnt_tb').val();
      var valueRangeTB = jQuery('#lfb_header_algmnt_tb').val();
      jQuery( "#lfb-header-algmnt-tb" ).slider({
      max: 99,
      value: valueHeadTB,
      create: function() {
        handleHeadTB.text( jQuery( this ).slider( "value" ) );
        jQuery('.lfb-header-paddingtb').text('Top Padding ('+valueRangeTB+'%)');
        jQuery('.lead-head').css({"padding-top":valueRangeTB+'%','padding-bottom':valueRangeTB+'%'});
        jQuery("#lfb-header-algmnt-tb-rng").css("width", valueRangeTB +"%");
      },
      slide: function( event, ui ) {
        handleHeadTB.text( ui.value );
        jQuery('#lfb_header_algmnt_tb').val(ui.value);
        jQuery('.lfb-header-paddingtb').text('Top Padding ('+ui.value+'%)');
        jQuery('.lead-head').css({"padding-top":ui.value+'%','padding-bottom':ui.value+'%'});
        jQuery("#lfb-header-algmnt-tb-rng").css("width", ui.value +"%");
        saveButtonActive(); 
      }
    });
        //lef/right aligment
      var handleHeadLR = jQuery( "#lfb-header-algmnt-lr-handle" );
      var valueHeadLR = jQuery('#lfb_header_algmnt_lr').val();
      var valueRangeLR = jQuery('#lfb_header_algmnt_lr').val();
      jQuery( "#lfb-header-algmnt-lr" ).slider({
      max: 99,
      value: valueHeadLR,
      create: function() {
        handleHeadLR.text( jQuery( this ).slider( "value" ) );
        jQuery('.lfb-header-paddinglr').text('Left/Right Padding ('+valueRangeLR+'%)');
        jQuery('.lead-head').css({"padding-left":valueRangeLR+'%','padding-right':valueRangeLR+'%'});
        jQuery("#lfb-header-algmnt-lr-rng").css("width", valueRangeLR +"%");
      },
      slide: function( event, ui ) {
        handleHeadLR.text( ui.value );
        jQuery('#lfb_header_algmnt_lr').val(ui.value);
        jQuery('.lfb-header-paddinglr').text('Left/Right Padding ('+ui.value+'%)');
        jQuery('.lead-head').css({"padding-left":ui.value+'%','padding-right':ui.value+'%'});
        jQuery("#lfb-header-algmnt-lr-rng").css("width", ui.value +"%");
        saveButtonActive(); 
      }
    });

    jQuery('.lfb_heading_hide').on('click', function(event){
        saveButtonActive();
        var headingHide = jQuery(this).val();
        if(headingHide=='none'){
            jQuery('.lead-form-front h1').hide();
        }else{
        jQuery('.lead-form-front h1').show();

        }
    });

    jQuery('.alignment-heading').on('click', function(event){
        var headingAlign = jQuery(this).val();
        jQuery(".lead-form-front h1").css("text-align", headingAlign); 
        saveButtonActive();
    });

// button manage
    var handleBtn = jQuery( "#lfb-button-handle" );
    var valueBtn = jQuery('#lfb_button_font_size').val();
    var valueBtnRange = jQuery('#lfb_button_font_size').val();
        jQuery( "#lfb-button-font" ).slider({
            max: 99,
            value: valueBtn,
          create: function() {
            handleBtn.text( jQuery( this ).slider( "value" ) );
            jQuery("#lfb-button-font-rng").css("width", valueBtnRange +"%");
            jQuery('.lfb-button-fontsize').text('Text Size ('+valueBtnRange+'%)');
          },
          slide: function( event, ui ) {
            handleBtn.text( ui.value );
            jQuery('#lfb_button_font_size').val(ui.value);
            jQuery('.lfb-button-fontsize').text('Text Size ('+ui.value+'%)');
            jQuery('.leadform-show-form input[type="submit"]').css("font-size", ui.value);
            jQuery("#lfb-button-font-rng").css("width", ui.value +"%");
            saveButtonActive();
          }
        });

        var handleBtnTB = jQuery( "#lfb-btn-padding-tb-handle" );
        var valueBtnTB = jQuery('#lfb_btn_padding_tb').val();
        var valueBtnTBRange = jQuery('#lfb_btn_padding_tb').val();
        jQuery( "#lfb-btn-padding-tb" ).slider({
            max: 30,
            value: valueBtnTB,
          create: function() {
            handleBtnTB.text( jQuery( this ).slider( "value" ) );
            jQuery('.lfb-button-paddingtb').text('Top/Bottom Padding ('+valueBtnTBRange+'%)');
            jQuery("#lfb-btn-padding-tb-rng").css("width", valueBtnTBRange * 3.33 +"%");  
          },
          slide: function( event, ui ) {
            handleBtnTB.text( ui.value );
            jQuery('#lfb_btn_padding_tb').val(ui.value);
            jQuery('.lfb-button-paddingtb').text('Top/Bottom Padding ('+ui.value+'%)');
            jQuery('.leadform-show-form input[type="submit"]').css({"padding-top":ui.value+'%','padding-bottom':ui.value+'%'});
            jQuery("#lfb-btn-padding-tb-rng").css("width", ui.value * 3.33 +"%");
            saveButtonActive();
          }
        });

        var handleBtnLR = jQuery( "#lfb-btn-padding-lr-handle" );
        var valueBtnLR = jQuery('#lfb_btn_padding_lr').val();
        var valueBtnLRRange = jQuery('#lfb_btn_padding_lr').val();

        jQuery( "#lfb-btn-padding-lr" ).slider({
            max: 99,
            value: valueBtnLR,
          create: function() {
            handleBtnLR.text( jQuery( this ).slider( "value" ) );
            jQuery('.lfb-button-paddinglr').text('Button Width ('+valueBtnLRRange+'%)');
            jQuery("#lfb-btn-padding-lr-rng").css("width", valueBtnLRRange+"%");  
          },
          slide: function( event, ui ) {
            handleBtnLR.text( ui.value );
            jQuery('#lfb_btn_padding_lr').val(ui.value);
            jQuery('.lfb-button-paddinglr').text('Button Width ('+ui.value+'%)');
            jQuery('.leadform-show-form input[type="submit"]').css({"width":ui.value+'%'});
            jQuery("#lfb-btn-padding-lr-rng").css("width", ui.value+"%");
          saveButtonActive();
          }
        });
        jQuery('.lfb-btn-align').on('click', function(event){
        var headingAlign = jQuery(this).val();
        jQuery(".submit-type.lf-field").css("text-align", headingAlign); 
        saveButtonActive();
    });

        // background
        var handleFormTop = jQuery( "#lfb-form-padding-top-handle" );
        var valueFormTop = jQuery('#lfb_form_padding_top').val();
        var valueFormTopRange = jQuery('#lfb_form_padding_top').val();
        jQuery( "#lfb-form-padding-top" ).slider({
            max: 100,
            value: valueFormTop,  
            create: function() {
            handleFormTop.text( jQuery( this ).slider( "value" ) );
          jQuery('.lfb-form-paddingtop').text('Top ('+valueFormTopRange+'%)');
            jQuery("#lfb-form-padding-top-rng").css("width",valueFormTopRange +"%"); 
          },
          slide: function( event, ui ) {
            handleFormTop.text( ui.value );
            jQuery('#lfb_form_padding_top').val(ui.value);
            jQuery('.lfb-form-paddingtop').text('Top ('+ui.value+'%)');
            jQuery('.leadform-show-form .lead-form-front').css({"padding-top":ui.value+'%'});
            jQuery("#lfb-form-padding-top-rng").css("width", ui.value +"%");
            saveButtonActive();
          }
        });
        var handleFormBottom = jQuery( "#lfb-form-padding-bottom-handle" );
        var valueFormBottom = jQuery('#lfb_form_padding_bottom').val();
        var valueFormBottomRange = jQuery('#lfb_form_padding_bottom').val();
        jQuery( "#lfb-form-padding-bottom" ).slider({
            max: 100,
            value: valueFormBottom,
          create: function() {
            handleFormBottom.text( jQuery( this ).slider( "value" ) );
        jQuery('.lfb-form-paddingbottom').text('Bottom ('+valueFormBottomRange+'%)');
            jQuery("#lfb-form-padding-bottom-rng").css("width",valueFormBottomRange +"%"); 
          },
          slide: function( event, ui ) {
            handleFormBottom.text( ui.value );
            jQuery('#lfb_form_padding_bottom').val(ui.value);
            jQuery('.lfb-form-paddingbottom').text('Bottom ('+ui.value+'%)');
            jQuery('.leadform-show-form .lead-form-front').css({"padding-bottom":ui.value+'%'});
            jQuery("#lfb-form-padding-bottom-rng").css("width", ui.value +"%");
            saveButtonActive();
          }
        });

        var handleFormLeft = jQuery( "#lfb-form-padding-left-handle" );
        var valueFormLeft = jQuery('#lfb_form_padding_left').val();
        var valueFormLeftRange = jQuery('#lfb_form_padding_left').val();
        jQuery( "#lfb-form-padding-left" ).slider({
            max: 100,
            value: valueFormLeft,
          create: function() {
            handleFormLeft.text( jQuery( this ).slider( "value" ) );
        jQuery('.lfb-form-paddingleft').text('Left ('+valueFormLeftRange+'%)');
            jQuery("#lfb-form-padding-left-rng").css("width",valueFormLeftRange +"%"); 
          },
          slide: function( event, ui ) {
            handleFormLeft.text( ui.value );
            jQuery('#lfb_form_padding_left').val(ui.value);
            jQuery('.lfb-form-paddingleft').text('Left ('+ui.value+'%)');
            jQuery('.leadform-show-form .lead-form-front').css({"padding-left":ui.value+'%'});
            jQuery("#lfb-form-padding-left-rng").css("width", ui.value +"%");
            saveButtonActive();
          }
        });

        var handleFormRight = jQuery( "#lfb-form-padding-right-handle" );
        var valueFormRight = jQuery('#lfb_form_padding_right').val();
        var valueFormRightRange = jQuery('#lfb_form_padding_right').val();
        jQuery( "#lfb-form-padding-right" ).slider({
            max: 100,
            value: valueFormRight,
          create: function() {
            handleFormRight.text( jQuery( this ).slider( "value" ) );
            jQuery('.lfb-form-paddingright').text('Right ('+valueRangeLR+'%)');
            jQuery("#lfb-form-padding-right-rng").css("width",valueFormRightRange +"%");
          },
          slide: function( event, ui ) {
            handleFormRight.text( ui.value );
            jQuery('#lfb_form_padding_right').val(ui.value);
            jQuery('.lfb-form-paddingright').text('Right ('+ui.value+'%)');
            jQuery('.leadform-show-form .lead-form-front').css({"padding-right":ui.value+'%'});
            jQuery("#lfb-form-padding-right-rng").css("width", ui.value +"%");
            saveButtonActive();
          }
        });

        function saveButtonActive(){
            jQuery("#saveColor, .saveColor").css({"background":'#000'});
            jQuery("#saveColor, .saveColor").val('Save Changes');
            jQuery( "#saveColor, .saveColor" ).prop( "disabled", false );

        }

    function lfbColorChanger(cssValue){
        saveButtonActive();
        var arrColor = [];


      //Button background color
        $lfb_color_button_text      = jQuery( '#lfb_color_button_text' ).val();
        $lfb_color_button_bg        = jQuery( '#lfb_color_button_bg' ).val();
        $lfb_color_button_bg_hover  = jQuery( '#lfb_color_button_bg_hover' ).val();
        $lfb_color_button_border    = jQuery('#lfb_color_button_border' ).val();
         jQuery(".leadform-show-form input[type='submit']").css({
            "color" : $lfb_color_button_text, "background-color" : $lfb_color_button_bg, "border-color" : $lfb_color_button_border });
    


        $lfb_color_bg = jQuery( '#lfb_color_bg' ).val();
        
        //jQuery(".leadform-show-form .lead-form-front").css("background-color", $lfb_color_bg);
        //jQuery(".leadform-show-form .lead-form-front:before").css("background-color", $lfb_color_bg);
        
        //heading color
        $lfb_color_heading = jQuery( '#lfb_color_heading' ).val();
        jQuery(".lead-form-front h1").css("color", $lfb_color_heading); 
        //Label color
        $lfb_color_label = jQuery( '#lfb_color_label' ).val();
        jQuery(".leadform-show-form span ul li, .leadform-show-form label").css("color", $lfb_color_label);

      // field background and border color
       $lfb_color_field_bg = jQuery( '#lfb_color_field_bg' ).val();
        $lfb_color_field_border = jQuery( '#lfb_color_field_border' ).val();

       // label placeholder  
        $lfb_color_field_placeholder = jQuery( '#lfb_color_field_placeholder' ).val();
        jQuery(".leadform-show-form select").css("color", $lfb_color_field_placeholder);
        jQuery(".leadform-show-form ::-webkit-input-placeholder").css("color", $lfb_color_field_placeholder);
        
         var stylePlaceholder = '.leadform-show-form .lead-form-front:before{background-color:'+$lfb_color_bg+' !important; } .leadform-show-form input[type="submit"]:hover {background:'+$lfb_color_button_bg_hover+' !important; } .leadform-show-form ::-webkit-input-placeholder {color:' + $lfb_color_field_placeholder + '!important;} .leadform-show-form :-moz-placeholder {color:' + $lfb_color_field_placeholder + '!important;} .leadform-show-form .leadform-show-form :-ms-input-placeholder {color:' + $lfb_color_field_placeholder + '!important;}';
         
         var iconColor = '.lfb-date-icon, .lfb_input_upload::before { background-color:'+$lfb_color_field_border+'!important; }';
         var styleBlock = '<style id="placeholder-style">'+iconColor+stylePlaceholder+cssValue+'</style>';

          jQuery(document).find('#placeholder-style').remove();  
          jQuery('.cd-main-content').append(styleBlock);

            //Label background color
       
        jQuery('.leadform-show-form select, .leadform-show-form textarea, .leadform-show-form input:not([type]), .leadform-show-form input[type="email"], .leadform-show-form input[type="number"], .leadform-show-form input[type="password"], .leadform-show-form input[type="tel"], .leadform-show-form input[type="url"], .leadform-show-form input[type="text"], .leadform-show-form input[type="radio"], .leadform-show-form input[type="checkbox"], .leadform-show-form input[type="number"]').css({
            "background-color": $lfb_color_field_bg , "border-color":$lfb_color_field_border, "color": $lfb_color_field_placeholder});   
    }

    //color picker & color settings
    var myOptions = {
    // a callback to fire whenever the color changes to a valid color
    change: function(event, ui){
        lfbColorChanger('');
    },
    };
  jQuery( 'input.alpha-color-picker' ).alphaColorPicker();
  jQuery('input.alpha-color-picker').wpColorPicker(myOptions);


// save color settings
jQuery(document).on('click', '#saveColor', function(event) {
             jQuery(".spinner").css({"visibility":'visible'});
             jQuery("#saveColor").css({"color":'#9b9d9f'});
            jQuery('.spin-over').append('<style>.spin-over:before{background:rgba(17, 17, 17, 0.41)!important; z-index:99!important;}</style>');
            $colorid =  jQuery('.cd-main-content').attr('colorid');
            $serialize = jQuery('#lfb_formColor').serialize();
            $form_data = $serialize + "&colorid="+$colorid+"&action=SaveColorsSettings";

        SaveByAjaxRequest($form_data, 'POST').success(function(response) {
            //alert(response);
        if(jQuery.trim(response) == 1){
        setTimeout( function() {
            //do something special
            jQuery(".spinner").css({"visibility":'hidden'});
            jQuery('.spin-over').append('<style>.spin-over:before{background:transparent!important; z-index:-1!important;}</style>');
             jQuery("#saveColor, .saveColor").css({"color":'#fff'});
             jQuery("#saveColor, .saveColor").css({"background":'#1cb23a'});
             jQuery("#saveColor, .saveColor").val('Updated');
             jQuery( "#saveColor, .saveColor" ).prop( "disabled", true );

               }, 1000);
            }
         });
      });

    } // color length
}); // jquery functin

/** Color option close **/
jQuery(document).on('click', '.nav-tab-wrapper a.nav-tab', function() {
        jQuery('section').hide();
        jQuery(this).parent().find('a').removeClass('nav-tab-active');
        jQuery(this).addClass('nav-tab-active');
        jQuery('section').eq(jQuery(this).index()).show();
        return false;
});
/*
 *Add dynamic Form Fields in admin area
 */

function add_new_form_fields(this_field_id) {
    var f_name = jQuery("#field_name_" + this_field_id).val();
    var f_type = jQuery("#field_type_" + this_field_id).val();
    if (f_type != "select") {
        jQuery("#field_type_" + this_field_id).removeClass('form_field_error');
    } else {
        jQuery("#field_type_" + this_field_id).addClass('form_field_error');
        jQuery("#field_type_" + this_field_id).focus();
    }
    if (f_name != '') {
        jQuery("#field_name_" + this_field_id).removeClass('form_field_error');
    } else {
        jQuery("#field_name_" + this_field_id).addClass('form_field_error');
        jQuery("#field_type_" + this_field_id).focus();
    }
    if ((f_type != "select") && (f_name != '')) {
        jQuery('#add_new_' + this_field_id).remove();
       // jQuery("#wpth_add_form_table_" + this_field_id).append("<input type='button' class='button lf_remove' name='remove_field' id='remove_field_" + this_field_id + "' onclick='remove_form_fields(" + this_field_id + ")' value='Remove'>");
        var field_id = this_field_id + 1;
        var field_sr = "<td>" + field_id + "</td>";
        var field_name = "<td><input type='text' name='form_field_" + field_id + "[field_name]' id='field_name_" + field_id + "' value=''></td>";
        var field_type = "<td><select name='form_field_" + field_id + "[field_type][type]' id='field_type_" + field_id + "'><option value='select'>Select Field Type</option><option value='name'>Name</option><option value='email'>Email</option><option value='message'>Message</option><option value='dob'>DOB</option><option value='date'>Date</option><option value='text'>Text (Single Line Text)</option><option value='textarea'>Textarea (Multiple Line Text)</option><option value='htmlfield'>Content Area (Read only Text)</option><option value='url'>Url (Website url)</option><option value='number'>Number (Only Numeric 0-9 )</option><option value='radio'>Radio (Choose Single Option)</option><option value='option'>Option (Choose Single Option)</option><option value='checkbox'>Checkbox (Choose Multiple Option)</option><option value='terms'>Checkbox (Terms & condition)</option></select><div class='add_radio_checkbox_" + field_id + "' id='add_radio_checkbox'><div class='' id='add_radio'></div><div class='' id='add_checkbox'></div><div class='' id='add_option'></div></div></td>";
        var field_default = "<td><input type='text' class='default_value' name='form_field_" + field_id + "[default_value]' id='default_value_" + field_id + "' value=''><div class='default_htmlfield_" + field_id + "'' id='default_htmlfield'></div><div class='default_terms_" + field_id + "'' id='default_terms'></div><div class='add_default_radio_checkbox_" + field_id + "' id='add_default_radio_checkbox'><div class='' id='default_add_radio'></div><div class='' id='default_add_checkbox'></div><div class='' id='default_add_option'></div></div></td>";
        var field_placeholder = "<td><input type='checkbox' class='default_placeholder' name='form_field_" + field_id + "[default_placeholder]' id='default_placeholder_" + field_id + "' value='1'></td>";
        var field_required = "<td><input type='checkbox' class='is_required' name='form_field_" + field_id + "[is_required]' id='is_required_" + field_id + "' value='1'></td>";
     
       // var field_add_button = "<td id='wpth_add_form_table_" + field_id + "'><input type='button' class='button lf_addnew' name='save' id='add_new_" + field_id + "' onclick='add_new_form_fields(" + field_id + ")' value='Add New'></td>";
       
        var field_remove_button = "<td><input type='button' class='button lf_remove' name='remove_field' id='remove_field_" + field_id + "' onclick='remove_form_fields(" + field_id + ")' value='Remove'></td>";
     
     
        var field_hidden_id = "<input type='hidden' value=" + field_id + " name='form_field_" + field_id + "[field_id]'>";
        var new_form_field = "<tr id='form_field_row_" + field_id + "'>" + field_name + field_type + field_default + field_placeholder + field_required  + field_remove_button+ field_hidden_id + "</tr>";
        jQuery(".append_new").append(new_form_field);

        jQuery('.add-field').html("<span><input type='button' class='button lf_addnew' name='save' id='add_new_" + field_id + "' onclick='add_new_form_fields(" + field_id + ")' value='Add New'></span>");

    }
}
/*
 *Delete Form Fields in admin area
 */
function remove_form_fields(field_id) {
    jQuery("#form_field_row_" + field_id).remove();
}
/*
 *Save forms in admin area

function save_new_form() {
    var form_heading = jQuery(".new_form_heading").val();
    if (form_heading != '') {
        jQuery(".new_form_heading").removeClass('form_field_error');
        jQuery(".new_lead_form").submit();
    } else {
        jQuery(".new_form_heading").addClass('form_field_error');
    }
}

 *Save forms in admin area
 */
    jQuery("form#new_lead_form").submit(function(event) {
        var form_heading = jQuery(".new_form_heading").val();
        if (form_heading != '') {
            jQuery(".new_form_heading").removeClass('form_field_error');
        } else {
            event.preventDefault();
            jQuery(".new_form_heading").addClass('form_field_error');
            jQuery(".new_form_heading").focus();
        }
    })
    /*
     *Add dynamic sub-fields according to Field Type
     */



 function htmlfield(parent_id,this_parent_id){
        jQuery(parent_id).find('input.default_value').attr('disabled', 'disabled');
        jQuery(parent_id).find('input.default_value').hide();
        jQuery(parent_id).find('input.is_required').hide();
        jQuery(parent_id).find('#default_htmlfield').show();


          var html_text = jQuery(parent_id).find('#default_htmlfield textarea').length;
        if (html_text < 1) {
            var html_fields = "<textarea class='default_value default_htmlfield' name='form_field_" + this_parent_id + "[default_value]''[default_value]' id='default_value_" + this_parent_id + "'''> </textarea>";
            jQuery(parent_id).find('#default_htmlfield').append(html_fields);
            jQuery(parent_id).find('input.default_value').hide();
            jQuery(parent_id).find('input.default_placeholder').hide();

        }
 }

 function multioptionFieldHide(parent_id){
        jQuery(parent_id).find('input.default_value').show();
        jQuery(parent_id).find('#default_htmlfield').hide();
        jQuery(parent_id).find('#add_radio').hide();
        jQuery(parent_id).find('#default_add_radio').hide();
        jQuery(parent_id).find('#add_checkbox').hide();
        jQuery(parent_id).find('#default_add_checkbox').hide();
        jQuery(parent_id).find('#add_option').hide();
        jQuery(parent_id).find('#default_add_option').hide();
        jQuery(parent_id).find('input.default_value').removeAttr('disabled');
        jQuery(parent_id).find('input.default_placeholder').removeAttr('disabled');
 }

jQuery("#wpth_add_form").on('change', 'select', function() {
    var this_parent_id = jQuery(this).parent().parent().attr("id");
    var parent_id = String("#" + this_parent_id);
    var this_parent_id = this_parent_id.replace("form_field_row_", "");
    var field_id = "1";
    var str = "";
    str = jQuery(parent_id  + " select option:selected").val();
        jQuery(parent_id).find('#default_htmlfield').hide();

    if (str == 'radio') {
        jQuery(parent_id).find('#add_radio').css("display", "block");
        jQuery(parent_id).find('#add_checkbox').css("display", "none");
        jQuery(parent_id).find('#add_option').css("display", "none");
        jQuery(parent_id).find('#default_add_radio').css("display", "block");
        jQuery(parent_id).find('#default_add_checkbox').css("display", "none");
        jQuery(parent_id).find('#default_add_option').css("display", "none");

        var radio_res = jQuery(parent_id).find('#add_radio input').length;
        if (radio_res < 1) {
            var radio_fields = "<input type='text' class='input_radio_val' name='form_field_" + this_parent_id + "[field_type][field_1]' id='radio_field_1' placeholder='radio name 1'value=''><p class='button lf_minus' id='delete_radio_1' onclick='delete_radio_fields(" + this_parent_id + ",1)'><i class='fa fa-minus' aria-hidden='true'></i></p><p class='button lf_plus' id='add_new_radio_1' onclick='add_new_radio_fields(" + this_parent_id + ",1)'><i class='fa fa-plus' aria-hidden='true'></i></p>";
            var default_add_radio = "<p id='default_radio_value_1'>radio name 1 <input type='radio' class='' name='form_field_" + this_parent_id + "[default_value][field]' id='default_radio_value_1' value='1'></p>";
            jQuery(parent_id).find('#add_radio').append(radio_fields);
            jQuery(parent_id).find('#default_add_radio').append(default_add_radio);
            jQuery(parent_id).find('#delete_radio_1').css("display", "none");
            jQuery(parent_id).find('input.default_value').attr('disabled', 'disabled');
            jQuery(parent_id).find('input.default_placeholder').attr('disabled', 'disabled');

        }
    } else if (str == 'option') {
        jQuery(parent_id).find('#add_option').css("display", "block");
        jQuery(parent_id).find('#add_radio').css("display", "none");
        jQuery(parent_id).find('#add_checkbox').css("display", "none");
        jQuery(parent_id).find('#default_add_option').css("display", "block");
        jQuery(parent_id).find('#default_add_radio').css("display", "none");
        jQuery(parent_id).find('#default_add_checkbox').css("display", "none");

        var radio_res = jQuery(parent_id).find('#add_option input').length;
        if (radio_res < 1) {
            var option_fields = "<input type='text' class='input_option_val' name='form_field_" + this_parent_id + "[field_type][field_1]' id='option_field_1' placeholder='option name 1'value=''><p class='button lf_minus' id='delete_option_1' onclick='delete_option_fields(" + this_parent_id + ",1)'><i class='fa fa-minus' aria-hidden='true'></i></p><p class='button lf_plus' id='add_new_option_1' onclick='add_new_option_fields(" + this_parent_id + ",1)'><i class='fa fa-plus' aria-hidden='true'></i></p>";
            var default_add_option = "<p id='default_option_value_1'>option name 1 <input type='radio' class='' name='form_field_" + this_parent_id + "[default_value][field]' id='default_option_value_1' value='1'></p>";
            jQuery(parent_id).find('#add_option').append(option_fields);
            jQuery(parent_id).find('#default_add_option').append(default_add_option);
            jQuery(parent_id).find('#delete_option_1').css("display", "none");
            jQuery(parent_id).find('input.default_value').attr('disabled', 'disabled');
            jQuery(parent_id).find('input.default_placeholder').attr('disabled', 'disabled');


        }
    } else if (str == 'checkbox') {
        jQuery(parent_id).find(' #add_checkbox').css("display", "block");
        jQuery(parent_id).find(' #add_radio').css("display", "none");
        jQuery(parent_id).find(' #add_option').css("display", "none");
        jQuery(parent_id).find(' #default_add_checkbox').css("display", "block");
        jQuery(parent_id).find(' #default_add_radio').css("display", "none");
        jQuery(parent_id).find('#default_add_option').css("display", "none");
        var checkbox_res = jQuery(parent_id).find('#add_checkbox input').length;
        if (checkbox_res < 1) {
            var checkbox_fields = "<input type='text' class='input_checkbox_val' name='form_field_" + this_parent_id + "[field_type][field_1]' id='checkbox_field_1' placeholder='check box name 1'value=''><p class='button lf_minus' id='delete_checkbox_1' onclick='delete_checkbox_fields(" + this_parent_id + ",1)'><i class='fa fa-minus' aria-hidden='true'></i></p><p class='button lf_plus' id='add_new_checkbox_1' onclick='add_new_checkbox_fields(" + this_parent_id + ",1)'><i class='fa fa-plus' aria-hidden='true'></i></p>";
            var default_add_checkbox = "<p id='default_checkbox_value_1'>checkbox name 1 <input type='checkbox' class='' name='form_field_" + this_parent_id + "[default_value][field_1]' id='default_checkbox_value_1' value='1'></p>";
            jQuery(parent_id).find('#add_checkbox').append(checkbox_fields);
            jQuery(parent_id).find('#default_add_checkbox').append(default_add_checkbox);
            jQuery(parent_id).find('#delete_checkbox_1').css("display", "none");
            jQuery(parent_id).find('input.default_value').attr('disabled', 'disabled');
            jQuery(parent_id).find('input.default_placeholder').attr('disabled', 'disabled');

        }
    } else if (str == 'htmlfield') {
            multioptionFieldHide(parent_id);

         htmlfield(parent_id,this_parent_id);
    } else {
        multioptionFieldHide(parent_id);
    }
});
/*
 *Delete dynamic sub-fields of Radio
 */
function delete_radio_fields(this_parent_id, radio_id) {
    var parent_id = "#form_field_row_" + this_parent_id;
    var radio_del_res = jQuery(parent_id + ' #add_radio_checkbox').find('#add_radio input').length;
    if (radio_del_res > 1) {
        jQuery(parent_id + " #radio_field_" + radio_id).remove();
        jQuery(parent_id + " #delete_radio_" + radio_id).remove();
        //jQuery(parent_id + " #add_new_radio_" + radio_id).remove();
        jQuery(parent_id + " #default_radio_value_" + radio_id).remove();
    }
}
/*
 *Add dynamic sub-fields of Radio
 */
function add_new_radio_fields(this_parent_id, radio_id) {
    var parent_id = "#form_field_row_" + this_parent_id;
    var new_radio_id = radio_id + 1;
    jQuery(parent_id + " #add_new_radio_" + radio_id).remove();
    jQuery(parent_id + ' #delete_radio_' + radio_id).css("display", "inline-block");
    var radio_add = "<p class='button lf_plus' id='add_new_radio_" + new_radio_id + "' onclick='add_new_radio_fields(" + this_parent_id + "," + new_radio_id + ")'><i class='fa fa-plus' aria-hidden='true'></i></p>";
    var radio_del = "<p class='button lf_minus' id='delete_radio_" + new_radio_id + "' onclick='delete_radio_fields(" + this_parent_id + "," + new_radio_id + ")'><i class='fa fa-minus' aria-hidden='true'></i></p>";
    var radio_field = "<input type='text' class='input_radio_val' name='form_field_" + this_parent_id + "[field_type][field_" + new_radio_id + "]' id='radio_field_" + new_radio_id + "' placeholder='radio name " + new_radio_id + "'value=''>";
    var radio_fields = radio_field + "" + radio_del + "" + radio_add;
    jQuery(parent_id + ' #add_radio').append(radio_fields);
    var default_add_radio = "<p id='default_radio_value_" + new_radio_id + "'>radio name " + new_radio_id + " <input type='radio' class='' name='form_field_" + this_parent_id + "[default_value][field]' id='default_radio_val_" + new_radio_id + "' value='" + new_radio_id + "'></p>";
    jQuery(parent_id + ' #default_add_radio').append(default_add_radio);
    jQuery(parent_id + ' #delete_radio_' + new_radio_id).css("display", "none");
}
/*
 *Delete dynamic sub-fields of Checkbox
 */
function delete_checkbox_fields(this_parent_id, checkbox_id) {
    var parent_id = "#form_field_row_" + this_parent_id;
    var checkbox_del_res = jQuery(parent_id + ' #add_radio_checkbox').find('#add_checkbox input').length;
    if (checkbox_del_res > 1) {
        jQuery(parent_id + " #checkbox_field_" + checkbox_id).remove();
        jQuery(parent_id + " #delete_checkbox_" + checkbox_id).remove();
        //jQuery(parent_id + " #add_new_checkbox_" + checkbox_id).remove();
        jQuery(parent_id + " #default_checkbox_value_" + checkbox_id).remove();
    }
}
/*
 *Add dynamic sub-fields of Checkbox
 */
function add_new_checkbox_fields(this_parent_id, checkbox_id) {
    var new_checkbox_id = checkbox_id + 1;
    var parent_id = "#form_field_row_" + this_parent_id;
    jQuery(parent_id + " #add_new_checkbox_" + checkbox_id).remove();
    jQuery(parent_id + ' #delete_checkbox_' + checkbox_id).css("display", "inline-block");
    var checkbox_add = "<p class='button lf_plus' id='add_new_checkbox_" + new_checkbox_id + "' onclick='add_new_checkbox_fields(" + this_parent_id + "," + new_checkbox_id + ")'><i class='fa fa-plus' aria-hidden='true'></i></p>";
    var checkbox_del = "<p class='button lf_minus' id='delete_checkbox_" + new_checkbox_id + "' onclick='delete_checkbox_fields(" + this_parent_id + "," + new_checkbox_id + ")'><i class='fa fa-minus' aria-hidden='true'></i></p>";
    var checkbox_field = "<input type='text' class='input_checkbox_val' name='form_field_" + this_parent_id + "[field_type][field_" + new_checkbox_id + "]' id='checkbox_field_" + new_checkbox_id + "' placeholder='checkbox name " + new_checkbox_id + "'value=''>";
    var checkbox_fields = checkbox_field + "" + checkbox_del + "" + checkbox_add;
    jQuery(parent_id + ' #add_checkbox').append(checkbox_fields);
    var default_add_checkbox = "<p id='default_checkbox_value_" + new_checkbox_id + "'>checkbox name " + new_checkbox_id + " <input type='checkbox' class='' name='form_field_" + this_parent_id + "[default_value][field_" + new_checkbox_id + "]' id='default_checkbox_val_" + new_checkbox_id + "' value='1'></p>";
    jQuery(parent_id + ' #default_add_checkbox').append(default_add_checkbox);
    jQuery(parent_id + ' #delete_checkbox_' + new_checkbox_id).css("display", "none");
}
/*
 *Delete dynamic sub-fields of Option
 */
function delete_option_fields(this_parent_id, option_id) {
    var parent_id = "#form_field_row_" + this_parent_id;
    var option_del_res = jQuery(parent_id + ' #add_radio_checkbox').find('#add_option input').length;
    if (option_del_res > 1) {
        jQuery(parent_id + " #option_field_" + option_id).remove();
        jQuery(parent_id + " #delete_option_" + option_id).remove();
       // jQuery(parent_id + " #add_new_option_" + option_id).remove();
        jQuery(parent_id + " #default_option_value_" + option_id).remove();
    }
}
/*
 *Add dynamic sub-fields of Option
 */
function add_new_option_fields(this_parent_id, option_id) {
    var new_option_id = option_id + 1;
    var parent_id = "#form_field_row_" + this_parent_id;
    jQuery(parent_id + " #add_new_option_" + option_id).remove();
    jQuery(parent_id + ' #delete_option_' + option_id).css("display", "inline-block");
    var option_add = "<p class='button lf_plus' id='add_new_option_" + new_option_id + "' onclick='add_new_option_fields(" + this_parent_id + "," + new_option_id + ")'><i class='fa fa-plus' aria-hidden='true'></i></p>";
    var option_del = "<p class='button lf_minus' id='delete_option_" + new_option_id + "' onclick='delete_option_fields(" + this_parent_id + "," + new_option_id + ")'><i class='fa fa-minus' aria-hidden='true'></i></p>";
    var option_field = "<input type='text' class='input_option_val' name='form_field_" + this_parent_id + "[field_type][field_" + new_option_id + "]' id='option_field_" + new_option_id + "' placeholder='option name " + new_option_id + "'value=''>";
    var option_fields = option_field + "" + option_del + "" + option_add;
    jQuery(parent_id + ' #add_option').append(option_fields);
    var default_add_option = "<p id='default_option_value_" + new_option_id + "'>option name " + new_option_id + " <input type='radio' class='' name='form_field_" + this_parent_id + "[default_value][field]' id='default_option_val_" + new_option_id + "' value=" + new_option_id + "></p>";
    jQuery(parent_id + ' #default_add_option').append(default_add_option);
    jQuery(parent_id + ' #delete_option_' + new_option_id).css("display", "none");
}
/*
 *Save email setting for each form
 */
jQuery("form#form-email-setting").submit(function(event) {    
        var form_data = jQuery("form#form-email-setting").serialize();
        form_data = form_data + "&action=SaveEmailSettings";
        event.preventDefault();
        jQuery("#error-message-email-setting").find("div").remove();
        SaveByAjaxRequest(form_data, 'POST').success(function(response) {
        //alert(response);
         if(jQuery.trim(response)=='updated'|| jQuery.trim(response) ==''){
            jQuery("#error-message-email-setting").append("<div class='success'><p>Updated Succesfully..!!</p></div>");
            }else{
             jQuery("#error-message-email-setting").append("<div class='error'><p>Something Went Wrong..!!</p></div>");   
            }
        });
    })
    /*
 *Save user email setting for each form
 */
jQuery("form#form-user-email-setting").submit(function(event) {    
        var form_data = jQuery("form#form-user-email-setting").serialize();
        form_data = form_data + "&action=SaveUserEmailSettings";
        event.preventDefault();
        jQuery("#error-message-user-email-setting").find("div").remove();
        SaveByAjaxRequest(form_data, 'POST').success(function(response) {
         if(jQuery.trim(response)=='updated'|| jQuery.trim(response) ==''){
            jQuery("#error-message-user-email-setting").append("<div class='success'><p>Updated Succesfully..!!</p></div>");
            }else{
             jQuery("#error-message-user-email-setting").append("<div class='error'><p>Something Went Wrong..!!</p></div>");   
            }
        });
    })
    /*
     *Save captcha setting for each form
     */
     jQuery("form#captcha-form").submit(function(event) {
        var form_data = jQuery("form#captcha-form").serialize();
        form_data = form_data + "&action=SaveCaptchaSettings";
        jQuery("#error-message-captcha-key").find("div").remove();
        SaveByAjaxRequest(form_data, 'POST').success(function(response) {
         if(jQuery.trim(response)=='updated'|| jQuery.trim(response) ==''){
            jQuery("#error-message-captcha-key").append("<div class='success'><p>Updated Succesfully..!!</p></div>");
            }
            
        });
        event.preventDefault();
    })
    /*
     *Save leads setting for each form
     */
    jQuery("form#lead-email-setting").submit(function(event) {
        var form_data = jQuery("form#lead-email-setting").serialize();
        event.preventDefault();
        form_data = form_data + "&action=SaveLeadSettings";
        jQuery("#error-message-lead-store").find("div").remove();
        SaveByAjaxRequest(form_data, 'POST').success(function(response) {
            //alert(response);
             if(jQuery.trim(response)=='updated'||jQuery.trim(response)==''){
            jQuery("#error-message-lead-store").append("<div class='success'><p>Updated Succesfully..!!</p></div>");
            }else{
             jQuery("#error-message-lead-store").append("<div class='error'><p>Something Went Wrong..!!</p></div>");   
            }
        });
    })

    /*
     *Save captcha enable/disable for each form
     */
jQuery("form#captcha-on-off-setting").submit(function(event) {
        var form_data = jQuery("form#captcha-on-off-setting").serialize();
        form_data = form_data + "&action=SaveCaptchaOption";
        event.preventDefault();
        jQuery("#error-message-captcha-option").find("div").remove();
        SaveByAjaxRequest(form_data, 'POST').success(function(response) {
            //alert(response);
            if(jQuery.trim(response)=='updated'|| jQuery.trim(response)==''){
            jQuery("#error-message-captcha-option").append("<div class='success'><p>Updated Succesfully..!!</p></div>");
            }else{
             jQuery("#error-message-captcha-option").append("<div class='error'><p>Something Went Wrong..!!</p></div>");   
            }
        });
    })
   
/*
 *Show leads according to form in back-end.
 */
jQuery('#select_form_lead').on('change', function() {
    var form_id = jQuery(this).val();
    form_data = "slectleads=1&form_id=" + form_id + "&action=ShowAllLeadThisForm";
    SaveByAjaxRequest(form_data, 'POST').success(function(response) {
        jQuery('#form-leads-show').empty();
        jQuery('#form-leads-show').append(response);
    });
});
/*
 *Delete particular Leads
 */
function delete_this_lead(this_lead_id) {
  if (confirm("OK to Delete?")) {
    jQuery('#lead-id-'+this_lead_id).remove();
    form_data = "&lead_id="+this_lead_id+"&action=delete_leads_backend";
    SaveByAjaxRequest(form_data, 'POST').success(function(response) {
    })
    }
}

function lead_pagination(page_id,form_id){
event.preventDefault();
    var form_data = "form_id=" + form_id + "&id=" + page_id + "&action=ShowAllLeadThisForm";
    SaveByAjaxRequest(form_data, 'GET').success(function(response) {
        jQuery('#form-leads-show').empty();
        jQuery('#form-leads-show').append(response);
    });
}

function lead_pagi_view(page_id,form_id){
event.preventDefault();
    var form_data = "form_id=" + form_id + "&id=" + page_id + "&action=ShowLeadPagi";
    SaveByAjaxRequest(form_data, 'GET').success(function(response) {
        jQuery('#form-leads-show').empty();
        jQuery('#form-leads-show').append(response);
    });
}

function lead_pagination_datewise(page_id,form_id,datewise){
event.preventDefault();
    var form_data = "form_id=" + form_id + "&id=" + page_id + "&datewise=" + datewise + "&action=ShowAllLeadThisFormDate";
   //alert(form_data);
   SaveByAjaxRequest(form_data, 'GET').success(function(response) {
    jQuery('#form-leads-show').empty();
    jQuery('#form-leads-show').append(response);
  });
}

function show_all_leads(page_id,form_id){
    event.preventDefault();
    var form_data = "form_id=" + form_id + "&id=" + page_id + "&detailview=1&action=ShowAllLeadThisForm";
    SaveByAjaxRequest(form_data, 'GET').success(function(response) {
        jQuery('#form-leads-show').empty();
        jQuery('#form-leads-show').append(response);
    });
}


function remember_this_form_id(){
if (confirm("OK to Remember?")) {
var form_id = jQuery('#select_form_lead').val();
jQuery('#remember_this_message').find('div').remove();
var form_data = "form_id=" + form_id + "&action=RememberMeThisForm";
    SaveByAjaxRequest(form_data, 'POST').success(function(response) {
        if(jQuery.trim(form_id)==jQuery.trim(response)){
       jQuery('#remember_this_message').append("<div><i>Saved Succesfully...!!</i></div>");
        }
    });
    }
}

// extension on off
jQuery(function(){
    jQuery(".onoffswitch-checkbox").click(function(){
   // if (confirm("OK to Change?")) {
        var this_catcha_status = jQuery(this).val();
        jQuery(this).val('OFF');
        if(jQuery.trim(this_catcha_status) == 'OFF'){
        jQuery(this).val('ON');
        }
        var this_catcha_id = jQuery(this).attr("id"); 
        var this_catcha_name = jQuery(this).attr("name"); 

        if(jQuery.trim(this_catcha_name)=='onoffswitch'){
        var this_catcha_id = this_catcha_id.substr(18);

        var form_data = "captcha-on-off-setting=" + this_catcha_status + "&captcha_on_off_form_id=" + this_catcha_id + "&action=SaveCaptchaOption";
            SaveByAjaxRequest(form_data, 'POST').success(function(response) {
            });
        }else if(jQuery.trim(this_catcha_name)=='extonoffswitch'){
        var this_ext_name = jQuery(this).attr("extension"); 
        var ext_data = "ext_name="+this_ext_name+"&extension_onoff_value=" + this_catcha_status + "&extension_on_off_form_id=" + this_catcha_id + "&action=SaveExtensionOption";
        SaveByAjaxRequest(ext_data, 'POST').success(function(response) {
            //alert(response);
            });

        }
   // }
    });
});

   /*
     *Save success msg , form submit
     */
    jQuery("form#lfb-form-success-msg").submit(function(event) {
        var form_data = jQuery("form#lfb-form-success-msg").serialize();
        event.preventDefault();
        //alert(form_data);
        form_data = form_data + "&action=lfbsavesuccessmsg";
        jQuery("#lfb-error-message-form-success").find("div").remove();
        SaveByAjaxRequest(form_data, 'POST').success(function(response) {
           // alert(response);
             if(jQuery.trim(response)=='updated'|| jQuery.trim(response)==''){
            jQuery("#lfb-error-message-form-success").append("<div class='success'><p>Updated Succesfully..!!</p></div>");
            }else{
             jQuery("#lfb-error-message-form-success").append("<div class='error'><p>Something Went Wrong..!!</p></div>");   
            }
        });
    })

function SaveByAjaxRequest(data, method) {
    return jQuery.ajax({
        url: backendajax.ajaxurl,
        type: method,
        data: data,
        cache: false
    });
}

var deleteLinks = document.querySelectorAll('.reset-frm-btn');
for (var i = 0; i < deleteLinks.length; i++) {
  deleteLinks[i].addEventListener('click', function(event) {
    event.preventDefault();
    var choice = confirm(this.getAttribute('data-confirm'));
    if (choice) {
      window.location.href = this.getAttribute('href');
    }
  });
}
// customize-form backend content fix size
jQuery(document).ready(function(){
jQuery('#lfb_formColor').append('<style>#wpbody-content{width:800px;}</style>');
});