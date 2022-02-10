( function( $ ) {
    "use strict";
    
    var social_id = '';
    var second_social_id = '';
    var $i = 0;
    var $flg = false;
    var social_tab_click = 0;
    var open_first_click = -1;

    $(document).ready(function(){
        if ($.cookie("hide_mystickyelements") == 'closed') {
            $('.mystickyelements-fixed').each(function(){
                jQuery(this).hide();
            });
        }

        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            $(".mystickyelements-fixed").addClass("mystickyelements-on-click").removeClass("mystickyelements-on-hover");
        }

        $('#stickyelements-form').on( 'submit', function(event){
            event.preventDefault();

            $('#stickyelements-form .mse-input-error').removeClass("mse-input-error");
            $('#stickyelements-form .mse-input-message').remove();

            var totalErrors = 0;
            if($("#stickyelements-form .required").length) {
                $("#stickyelements-form .required").each(function(){
                    if($.trim($(this).val()) == "") {
                        $(this).addClass("mse-input-error");
                        $(this).after("<span class='mse-input-message'>This field is required</span>");
                        totalErrors++;
                    }
                });
            }
            if($("#stickyelements-form .email.required:not(.mse-input-error)").length) {
                $("#stickyelements-form .email.required:not(.mse-input-error)").each(function(){
                    var thisVal = $.trim($(this).val());
                    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    if (!regex.test(thisVal)) {
                        $(this).addClass("mse-input-error");
                        $(this).after("<span class='mse-input-message'>Email address is not valid</span>");
                        totalErrors++;
                    }
                });
            }

            if(totalErrors == 0) {
                jQuery.ajax({
                    url: mystickyelements.ajaxurl,
                    type: 'post',
                    data: 'action=mystickyelements_contact_form&' + jQuery("form#stickyelements-form").serialize() + '&security=' + mystickyelements.ajax_nonce,
                    beforeSend: function() {
                        $( '#stickyelements-submit-form' ).prop('disabled', true);
                    },
                    success: function (data) {
                        $( '#stickyelements-submit-form' ).prop('disabled', false);
                        $('#stickyelements-form .mse-input-error').removeClass("mse-input-error");
                        $('#stickyelements-form .mse-input-message').remove();

                        data = $.parseJSON(data);
                        if(data.error == '1') {
                            for(var i=0; i<data.errors.length; i++) {
                                if(data.errors[i].key != "mse-form-error") {
                                    $('#stickyelements-form #' + data.errors[i].key).addClass("mse-input-error");
                                    $('#stickyelements-form #' + data.errors[i].key).after("<span class='mse-input-message'>" + data.errors[i].message + "</span>");
                                } else {
                                    $("#mse-form-error").removeClass("mse-form-success-message").addClass("mse-form-error-message").show();
                                    $("#mse-form-error").html(data.errors[i].message);
                                }
                            }
                        } else if(data.status == '0') {
                            $("#mse-form-error").removeClass("mse-form-success-message").addClass("mse-form-error-message").show();
                            $("#mse-form-error").html(data.message);
                        } else {
                            $("#mse-form-error").removeClass("mse-form-error-message").addClass("mse-form-success-message").show();
                            $("#mse-form-error").html(data.message);

                            $('#stickyelements-form input[type="text"], #stickyelements-form input[type="tel"], #stickyelements-form input[type="email"]').val("");
                            $('#stickyelements-form textarea').val("");
                            $.cookie("closed_contactform"  , "closed", { path: '/' });
                        }
                        setTimeout(function () {
                            $('.mse-form-success-message').slideUp("slow");
                        }, 5000);

                        /* redirct Page After Submission */
                        if ( data.status == 1 && data.redirect_link != '' ) {
                            window.location = data.redirect_link;
                        }
                        return false;
                    }
                });
            }
            return false;
        });

        

        function set_open_tab_first_click( thisElement ){
            thisElement.find('a').attr('href', "#");
            thisElement.find('a').attr('target', "");
            social_tab_click++; 
            open_first_click = 0;
        }

        function set_open_channel_first_click( thisElement , url ){
            open_first_click = 1;
            social_tab_click++;
           // window.open(url,'_blank');
           thisElement.find('a').attr('href',url);
           thisElement.find('a').attr('target', "_blank");
        }

        function setMobileTabBehavior( thisElement , tab_setting , click , url  ) {
            
            if( tab_setting == 'hover' && thisElement.data('mobile-behavior') == 'enable' ){
                thisElement.data('click','1');
                if( social_tab_click == 0 ){
                    set_open_tab_first_click( thisElement );  
                } else {
                   set_open_channel_first_click( thisElement , url );
                    return;
                }
            }else if( tab_setting == 'click' &&  thisElement.data('flyout') == 'enable' ){
                thisElement.data('click','1');
                if( social_tab_click == 0 ){
                    set_open_tab_first_click( thisElement );    
                } else {
                    set_open_channel_first_click( thisElement , url );
                    return;
                }
            } else if( tab_setting == 'click' &&  thisElement.data('flyout') == 'disable' ){
                thisElement.find('a').attr('target', "_blank");
                open_first_click = 1;
                return;
            } else{
                open_first_click = 1;
                thisElement.find('a').attr('target', "_blank");
            } 
        }   

        function setDesktopTabBehavior( thisElement , tab_setting , click , url  ) {
           
            if( thisElement.data('flyout') == 'enable' ){
               thisElement.data('click','1');
               if( social_tab_click == 0 ){
                    set_open_tab_first_click( thisElement );    
                } else {
                    set_open_channel_first_click( thisElement , url );
                    return;
                }
            } else {
                if( thisElement.data('flyout') == 'disable' ){
                    open_first_click = 1;
                    thisElement.find('a').attr('href',url);
                    thisElement.find('a').attr('target', "_blank");
                    return;
                }
            } 
        }

        function setTabBehaviorSettings( thisElement , device_type  ) {
            var tab_setting = thisElement.data('tab-setting');
            var click = thisElement.data('click');
            var url = thisElement.find('a').data('url');

            if( device_type == 'mobile' ){
                setMobileTabBehavior( thisElement , tab_setting , click , url );
            } else {
                setDesktopTabBehavior( thisElement , tab_setting , click , url);
            }
        }

        /* Open tab on Click Event */
        $('.mystickyelements-on-click .mystickyelements-social-icon').on( 'click touch', function(event){
            var click = $(this).data('click');
            var device_type = getDeviceType();
            var thisElement = $(this);

            if(!$( this ).parent( 'li' ).hasClass( 'mystickyelements-contact-form' )){
                if(click == '0'){
                    $('.mystickyelements-social-icon').data('click','0');
                    social_tab_click = 0;
                }
    
                setTabBehaviorSettings( thisElement , device_type );
                if( open_first_click == 1 ) {
                    return;
                }
            }

            if(!$(this).parent('li').hasClass("elements-active")) {
                $('.mystickyelements-on-click .elements-active').removeClass("elements-active");
                $(this).parent('li').addClass('elements-active');
            } else {
                $(this).parent('li').removeClass('elements-active');
                $.cookie("closed_contactform", "closed", { path: '/' });
                event.preventDefault();
            }
        });



        /*close contact form on click close icon*/
        $('.mystickyelements-on-hover .mystickyelements-social-icon').on( 'click', function(event){

            if($(this).parent('li').hasClass("elements-active") && $( this ).children('a').length == 0 ) {
                $(this).parent('li').removeClass('elements-hover-active');
                event.preventDefault();
                $(this).parent().parent().parent().parent('.mystickyelements-on-hover').removeClass('mystickyelements-on-click');
            }
        });
        $( '.mystickyelements-on-hover .mystickyelements-social-icon-li' ).on('mouseenter', function(){
            if($(this).hasClass("elements-active")) {
                //$(this).parent('li').removeClass('elements-active');
            }
            if(!$(this).hasClass("elements-active")) {
                $('.mystickyelements-on-click .elements-active').removeClass("elements-active");
                $(this).addClass('elements-active');
                $(this).addClass('elements-hover-active');
                $(this).parent().parent().parent('.mystickyelements-on-hover').addClass('mystickyelements-on-click');
            }
        }).on('mouseleave', function(){
            $(this).removeClass('elements-active');
            $(this).removeClass('elements-hover-active');
            $(this).parent().parent().parent('.mystickyelements-on-hover').removeClass('mystickyelements-on-click');
        });
        $( '.mystickyelements-on-hover ul li.mystickyelements-contact-form' ).on('mouseenter', function(){
            $( this ).addClass( 'element-contact-active' );
        } ).on('mouseleave', function(){
            $( this ).removeClass( 'element-contact-active' );
        });
        $( '.element-contact-close' ).on( 'click touch', function(event){
            $( '.mystickyelements-contact-form' ).removeClass('elements-active');
            $( '.mystickyelements-contact-form' ).removeClass('element-contact-active');
            $.cookie("closed_contactform"  , "closed", { path: '/' });
        });

        $('#stickyelements-form input:not(#stickyelements-submit-form), #stickyelements-form textarea ').on( 'keyup', function(event){
            if ($(this).val()){
                $(this).css('background-color', '#EFF5F8');
                $(this).css('border-color', '#7761DF');
            }

        });
        mystickyelements_border_radius();
        /* Minimize Sticky Elements  */
        //$('.mystickyelements-fixed').css( 'height', $('.mystickyelements-fixed').height() + 'px');
        $('li.mystickyelements-minimize').on('click',function(event){
            var element_minimize, minimize_device, position_device,element_on_device;

            $( this ).toggleClass( 'element-minimize' );
            if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                minimize_device = 'mobile';
                position_device = 'mobile-';
                element_on_device = 'element-mobile-on';
            } else {
                minimize_device = 'desktop';
                position_device = '';
                element_on_device = 'element-desktop-on';
            }
            if ( $( this ).hasClass( 'element-minimize' ) === true ) {
                $.cookie("minimize_" + minimize_device, "minimize", { path: '/' });
                element_minimize = true;
            } else {
                $.cookie("minimize_" + minimize_device, 'minimize_not', { path: '/' });
                element_minimize = false;
            }

            /* Left Position */
            $(".mystickyelements-position-" + position_device + "left ul li").each( function() {
                if ( $(this).hasClass( element_on_device ) == true ) {
                    var mystickyelements_size = $( '.mystickyelements-fixed' ).hasClass( 'mystickyelements-size-large' );
                    if ( mystickyelements_size == true ) {
                        $(this).animate({
                            width: 'toggle',
                            left: ( element_minimize === true ) ? '-=80' : ''
                        });
                    } else {
                        $(this).animate({
                            width: 'toggle',
                            left: ( element_minimize === true ) ? '-=50' : ''
                        });
                    }
                }
            });

            /* Right Position */
            $(".mystickyelements-position-" + position_device + "right ul li").each( function() {
                if ( $(this).hasClass( element_on_device ) == true ) {
                    var mystickyelements_size = $( '.mystickyelements-fixed' ).hasClass( 'mystickyelements-size-large' );
                    if ( mystickyelements_size == true ) {
                        $(this).animate({
                            width: 'toggle',
                            left: ( element_minimize === true ) ? '+=80' : ''
                        }, 300 , function() {
                        });
                    } else {
                        $(this).animate({
                            width: 'toggle',
                            left: ( element_minimize === true ) ? '+=50' : ''
                        }, 300 , function() {
                        });
                    }
                }
            });

            /* Bottom Position */
            $(".mystickyelements-position-" + position_device + "bottom ul li").each( function() {
                if ( $(this).hasClass( element_on_device ) == true ) {
                    $(this).css( 'position', 'relative' );
                    var mystickyelements_size = $( '.mystickyelements-fixed' ).hasClass( 'mystickyelements-size-large' );
                    if ( mystickyelements_size == true ) {
                        $(this).animate({
                            height: 'toggle',
                            bottom: ( element_minimize === true ) ? '-=80' : '',
                        }, 300 , function() {
                            $(this).css( 'position', ( element_minimize === true ) ? 'relative' : 'static' );
                        });
                    } else {
                        $(this).animate({
                            height: 'toggle',
                            bottom: ( element_minimize === true ) ? '-=60' : '',
                        }, 300 , function() {
                            $(this).css( 'position', ( element_minimize === true ) ? 'relative' : 'static' );
                        });
                    }
                }
            });
            /* Top Position */
            $(".mystickyelements-position-" + position_device + "top ul li").each( function() {
                if ( $(this).hasClass( element_on_device ) == true ) {
                    $(this).css( 'position', 'relative' );
                    var mystickyelements_size = $( '.mystickyelements-fixed' ).hasClass( 'mystickyelements-size-large' );
                    if ( mystickyelements_size == true ) {
                        $(this).animate({
                            height: 'toggle',
                            top: ( element_minimize === true ) ? '-=80' : '',
                        }, 300 , function() {
                            $(this).css( 'position', ( element_minimize === true ) ? 'relative' : 'static' );
                        });
                    } else {
                        $(this).animate({
                            height: 'toggle',
                            top: ( element_minimize === true ) ? '-=60' : '',
                        }, 300 , function() {
                            $(this).css( 'position', ( element_minimize === true ) ? 'relative' : 'static' );
                        });
                    }
                }
            });

            /* Move arrow base on minimize */
            if ( $( 'span.mystickyelements-minimize' ).hasClass( 'minimize-position-' + position_device + 'left' ) === true ) {

                if ( $( 'li.mystickyelements-minimize' ).hasClass( 'element-minimize' ) === true) {
                    $( '.mystickyelements-minimize.minimize-position-' + position_device + 'left' ).html('&rarr;')
                } else {
                    $( '.mystickyelements-minimize.minimize-position-' + position_device + 'left' ).html('&larr;')
                }
            } else if ( $( 'span.mystickyelements-minimize' ).hasClass( 'minimize-position-' + position_device + 'bottom' ) === true ) {

                if ( $( 'li.mystickyelements-minimize' ).hasClass( 'element-minimize' ) === true) {
                    $( '.mystickyelements-minimize.minimize-position-' + position_device + 'bottom' ).html('&uarr;')
                } else {
                    $( '.mystickyelements-minimize.minimize-position-' + position_device + 'bottom' ).html('&darr;')
                }
            } else if ( $( 'span.mystickyelements-minimize' ).hasClass( 'minimize-position-' + position_device + 'top' ) === true ) {

                if ( $( 'li.mystickyelements-minimize' ).hasClass( 'element-minimize' ) === true) {
                    $( '.mystickyelements-minimize.minimize-position-' + position_device + 'top' ).html('&darr;')
                } else {
                    $( '.mystickyelements-minimize.minimize-position-' + position_device + 'top' ).html('&uarr;')
                }
            } else {
                if ( $( 'li.mystickyelements-minimize' ).hasClass( 'element-minimize' ) === true) {
                    $( '.mystickyelements-minimize.minimize-position-' + position_device + 'right' ).html('&larr;')
                } else {
                    $( '.mystickyelements-minimize.minimize-position-' + position_device + 'right' ).html('&rarr;')
                }
            }
        });
        /*iframe set*/
        $( '.mystickyelements-fixed ul li' ).each( function(){
            var custom_html_class = $( this ).hasClass( 'mystickyelements-custom-html-main' );
            if( custom_html_class ) {
                var custom_html_child_class = $( this ).hasClass( 'mystickyelements-custom-html-iframe' );
                if( custom_html_child_class ) {
                    //var custom_html_iframe = $( this ).find( 'iframe' ).height();
                    var custom_html_iframe = $( this ).find( '.mystickyelements-custom-html' ).height();
                    var main_ul_height = $( '.mystickyelements-fixed ul' ).height();
                    if( main_ul_height > custom_html_iframe ) {
                        //$( this ).addClass( 'mystickyelements-custom-iframe-bottom' );
                    }
                }
            }
        });

        setTimeout( function(){
            $( '.mystickyelements-entry-effect-fade.entry-effect,.mystickyelements-entry-effect-slide-in.entry-effect' ).css( 'transition', 'all 0s ease 0s' );
        }, 1000 );

        $( '.mystickyelements-fixed ul li' ).on( 'click', function(){
            if ( $( this ).hasClass( 'mystickyelements-custom-html-iframe' ) ) {
                $( '.mystickyelements-fixed' ).toggleClass( 'mystickyelements-custom-html-iframe-open' );
            } else {
                $( '.mystickyelements-fixed' ).removeClass( 'mystickyelements-custom-html-iframe-open' );
            }
        } );

        $( '.mystickyelements-fixed' ).addClass( 'entry-effect' );
        if ( $( window ).width() > 1024  ) {
            var mystickyelements_bottom_width = $( '.mystickyelements-position-bottom .mystickyelements-lists' ).width();
            if ( mystickyelements_bottom_width < 300 ) {
                $( '.mystickyelements-position-bottom .mystickyelements-contact-form .element-contact-form' ).width( '300' );
            }
        }
        mystickyelements_mobile_top_pos();
    });

    $( window ).on( 'resize', function() {
        mystickyelements_border_radius();
        mystickyelements_mobile_top_pos();
    });

    function getDeviceType() {
        if( /Android|webOS|iPhone|iPad|Mac|Macintosh|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            return 'mobile';
        }else{
            return 'desktop';
        }
    }
    function mystickyelements_mobile_top_pos() {
        if ( $( window ).width() <= 1024  ) {
            if ( $( '.mystickyelements-fixed' ).hasClass( 'mystickyelements-position-mobile-top' ) ) {
                var mystickyelements_height = $( '.mystickyelements-fixed' ).height();
                $( 'html' ).attr( 'style', 'margin-top: ' + mystickyelements_height + 'px !important' );
            }
        } else {
            $( 'html' ).css( 'margin-top', '' );
        }
    }


    function mystickyelements_border_radius(){

        /* Contact Us form Height */
        if ( $('.element-contact-form').length !== 0 ) {
            var win_height = $(window).height();
            var element_position = $('.mystickyelements-fixed').position().top;
            var element_offset = $('.element-contact-form').offset().top;
            //var contact_frm_height = $('#mystickyelements-contact-form .element-contact-form').height();
            var contact_frm_height = $('#mystickyelements-contact-form #stickyelements-form').innerHeight() + $( '.element-contact-form h3' ).innerHeight();
           

            if ( win_height < contact_frm_height ) {
                var new_height = (win_height - 70 );
                $('#mystickyelements-contact-form .element-contact-form').css('max-height', new_height+ 'px' );
                $('#mystickyelements-contact-form .element-contact-form').css('overflowY', 'auto' );
                var contact_form_top = element_position - 10;
                if( $(window).width() > 1025 &&  ! $('.mystickyelements-fixed').hasClass('mystickyelements-position-bottom') ){
                    $('#mystickyelements-contact-form .element-contact-form').css('top', '-' + contact_form_top + 'px' );
                }
                if( $(window).width() < 1024 &&  ! $('.mystickyelements-fixed').hasClass('mystickyelements-position-mobile-bottom') ){
                    $('#mystickyelements-contact-form .element-contact-form').css('top', '-' + contact_form_top + 'px' );
                }

            } else {

                var minimize_height = $('ul.mystickyelements-lists .mystickyelements-minimize').height();
                if ( minimize_height === null ) {
                    minimize_height = 0;
                }
                var contact_form_top = element_position - (win_height - contact_frm_height) + minimize_height + 10;
                if( $(window).width() > 1025 &&  ! $('.mystickyelements-fixed').hasClass('mystickyelements-position-bottom') ){

                    $('#mystickyelements-contact-form .element-contact-form').css('top', '-' + contact_form_top + 'px' );
                }
                if( $(window).width() < 1024 &&  ! $('.mystickyelements-fixed').hasClass('mystickyelements-position-mobile-bottom') ){
                    $('#mystickyelements-contact-form .element-contact-form').css('top', '-' + contact_form_top + 'px' );
                }

                $('#mystickyelements-contact-form .element-contact-form').css('overflowY', '' );
                $('#mystickyelements-contact-form .element-contact-form').css('max-height', '');
            }
        }

        var position_device = '';
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            position_device = 'mobile-';
        }
        var $mobile_bottom = 0;
        $('.mystickyelements-fixed ul li').each( function () {
            /* Check google analytics enable then add 'update-analytics' class */
            if ( mystickyelements.google_analytics === '1'  && $(this).hasClass('mystickyelements-minimize') !== true && $(this).attr('id') !== 'mystickyelements-contact-form' ) {
                if ( $(this).find( "a" ).length !== 0 ) {
                    var elementname = $(this).attr("id").split('mystickyelements-social-');
                    $(this).find( "a" ).addClass('update-analytics');
                    $(this).find( "a" ).attr( 'data-social-slug', elementname[1]);
                } else {
                    $(this).addClass('analytics-update');
                }
            }

            $('.mystickyelements-position-' + position_device + 'left #' + $(this).attr('id') + ' .mystickyelements-social-icon').css('border-radius','');
            $('.mystickyelements-position-' + position_device + 'right #' + $(this).attr('id') + ' .mystickyelements-social-icon').css('border-radius','');

            /* Check First LI */
            if ( $i == 0 ){
                if ( $( window ).width() > 1024 &&  !$(this).hasClass('element-desktop-on')){
                    $flg = true;
                }
                if ( $( window ).width() < 1025 &&  !$(this).hasClass('element-mobile-on')){
                    $flg = true;
                }
            }

            if ( $i == 1 && $flg === true) {
                if ( $( window ).width() > 1024){
                    second_social_id = $(this).attr('id');
                }
                if ( $( window ).width() < 1025){
                    second_social_id = $(this).attr('id');
                }
            }

            if ( $( window ).width() > 1024 &&  $(this).hasClass('element-desktop-on')){
                social_id = $(this).attr('id');
            }
            if ( $( window ).width() < 1025 &&  $(this).hasClass('element-mobile-on')){
                social_id = $(this).attr('id');
                $mobile_bottom++;
            }

            $i++;
        });

        $( '.mystickyelements-fixed.mystickyelements-position-mobile-bottom').addClass( 'mystickyelements-bottom-social-channel-' + $mobile_bottom );
        $( '.mystickyelements-fixed.mystickyelements-position-mobile-top').addClass( 'mystickyelements-top-social-channel-' + $mobile_bottom );

        if ( social_id != '' ) {
            if ( social_id === 'mystickyelements-contact-form' ){
                $('.mystickyelements-position-' + position_device + 'left #' + social_id + ' .mystickyelements-social-icon').css('border-bottom-left-radius', '10px' );
                $('.mystickyelements-position-' + position_device + 'right #' + social_id + ' .mystickyelements-social-icon').css('border-top-left-radius', '10px' );
                $('.mystickyelements-position-' + position_device + 'bottom #' + social_id + ' .mystickyelements-social-icon').css('border-top-right-radius', '10px' );

                if( $( 'li.mystickyelements-minimize' ).length !== 1 ){
                    $('.mystickyelements-position-' + position_device + 'left #' + social_id + ' .mystickyelements-social-icon').css('border-bottom-right-radius', '10px' );
                    $('.mystickyelements-position-' + position_device + 'right #' + social_id + ' .mystickyelements-social-icon').css('border-top-right-radius', '10px' );
                }
            } else if ( social_id !== 'mystickyelements-contact-form') {
                if ( $i=== 1 ) {
                    $('.mystickyelements-position-' + position_device + 'left #' + social_id + ' .mystickyelements-social-icon').css('border-radius', '0px 10px 10px 0' );
                    $('.mystickyelements-position' + position_device + '-right #' + social_id + ' .mystickyelements-social-icon').css('border-radius', '10px 0 0 10px' );
                } else {
                    $('.mystickyelements-position-' + position_device + 'left #' + social_id + ' .mystickyelements-social-icon').css('border-bottom-right-radius', '10px' );
                    $('.mystickyelements-position-' + position_device + 'right #' + social_id + ' .mystickyelements-social-icon').css('border-bottom-left-radius', '10px' );
                    $('.mystickyelements-position-' + position_device + 'bottom #' + social_id + ' .mystickyelements-social-icon').css('border-top-right-radius', '10px' );
                }
            }
        } else {
            $('.mystickyelement-credit').hide();
            $('.mystickyelements-fixed').hide();
        }
        if ( second_social_id != '' && second_social_id !== 'mystickyelements-contact-form' && $( 'li.mystickyelements-minimize' ).length !== 1  ) {
            $('.mystickyelements-position-' + position_device + 'left #' + second_social_id + ' .mystickyelements-social-icon').css('border-top-right-radius', '10px' );
            $('.mystickyelements-position-' + position_device + 'right #' + second_social_id + ' .mystickyelements-social-icon').css('border-top-left-radius', '10px' );
            $('.mystickyelements-position-' + position_device + 'bottom #' + second_social_id + ' .mystickyelements-social-icon').css('border-top-left-radius', '10px' );
        }
    }
    
    jQuery(document).on('click','.mystickyelements-social-text a',function(){
        social_tab_click = 0;
         var thisElement = $(this);
        $(this).attr('target','_blank');
        remove_tab_active_class( thisElement );
        
    });

    function remove_tab_active_class( thisElement ){
        thisElement.parent().parent().removeClass("elements-active");
    }
    jQuery(document).on("click",".mystickyelements-social-icon a",function(){
        var device_type = getDeviceType();
        var thisElement = $(this);
        if( device_type == 'mobile' ) {
            
            if( $(this).data('tab-setting') == 'hover' && $(this).data('mobile-behavior') !== 'enable') {
                    remove_tab_active_class( thisElement );
            } else if( $(this).data('tab-setting') == 'click' && $(this).data('flyout') !== 'enable' ){
                    remove_tab_active_class( thisElement );
            } else{
                if( social_tab_click > 1 ){
                    remove_tab_active_class( thisElement );
                    social_tab_click = 0;
                }
            }
        }else{
            if( $(this).data('flyout') !== 'enable') {
                remove_tab_active_class( thisElement );
            } else{
                if( social_tab_click > 1 ){
                    remove_tab_active_class( thisElement );
                    social_tab_click = 0;
                }
            } 
        }
      
    });

    jQuery('body').mouseup(function (e) {
        if ($(e.target).closest(".mystickyelement-lists-wrap").length
                    === 0) {
            social_tab_click = 0;           
            jQuery('.mystickyelements-social-icon-li').removeClass('elements-active');        
        }
    });
    
})( jQuery );

function launch_mystickyelements( ele_no ){
    var ele_device = 'desktop';
    var lists_loop =1;
	ele_no = (typeof ele_no !== 'undefined') ?  ele_no : 1
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        ele_device = 'mobile';
    }
    jQuery('.mystickyelements-fixed .mystickyelements-lists').each(function(){

        if ( lists_loop > 1) {
            return;
        }

        var count = 1;
        jQuery(this).find('li').each(function(){
            /* Return Minimize Element */
            if ( jQuery(this).hasClass('mystickyelements-minimize')) {
                if ( jQuery(this).hasClass('element-minimize')) {
                    jQuery(this).trigger('click');
                }
                return;
            }
            /* Return element device not found */
            if ( !jQuery(this).hasClass('element-' + ele_device + '-on')) {
                return;
            }

            if ( ele_no == count) {
                jQuery(this).addClass('elements-active');
                if ( !jQuery(this).parent().parent().parent().hasClass('mystickyelements-on-click') ) {
                    jQuery(this).parent().parent().parent().addClass('mystickyelements-on-click');
                }
                return false;
            }
            count++;
        });

        lists_loop++;
    });
}

function close_mystickyelements(){
    var ele_device = 'desktop';
    var lists_loop =1;
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        ele_device = 'mobile';
    }

    jQuery('.mystickyelements-fixed .mystickyelements-lists').each(function(){
        jQuery(this).find('li').each(function(){
            if ( jQuery(this).hasClass('elements-active') ) {
                jQuery(this).removeClass('elements-active');
            }
        });
    });
}

function hide_mystickyelements(){
    jQuery('.mystickyelements-fixed').each(function(){
        jQuery(this).hide();
        jQuery.cookie("hide_mystickyelements"  , "closed", { expires: 365, path: '/' });
    });
}

function show_mystickyelements(){
    jQuery('.mystickyelements-fixed').each(function(){
        jQuery(this).show();
        jQuery.cookie("hide_mystickyelements"  , "opened", { expires: 1, path: '/' });
    });
}