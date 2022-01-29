(function($){
    
    $(document).ready(function(){
        $(".rm_formcard_menu_icon").click(function(){
            var $this = $(this);
            $(".rm-formcard-menu").hide();
            $($this.data("menu-panel")).show();
        });
        
        $(".rm-formcard-menu-close").click(function(){
            $(this).closest(".rm-formcard-menu").hide();
        });
        
        $(".rm-formcard-menu").each(function(){
            var parent_id = $(this).attr("id");
            $("#"+parent_id+" .rm-formcard-tabpanel").hide().eq(0).show().addClass("rm_active_tabpanel");
        });
        
        $(".rm_formcard_tabhead").click(function(){
            var $this = $(this);
            var parent_id = $this.closest(".rm-formcard-menu").attr("id");
            if($this.hasClass('rm_active_tabhead'))
                return;
            $("#"+parent_id+" .rm_formcard_tabhead.rm_active_tabhead").removeClass("rm_active_tabhead");
            $this.addClass("rm_active_tabhead");
            $("#"+parent_id+" .rm-formcard-tabpanel.rm_active_tabpanel").hide().removeClass("rm_active_tabpanel");
            $("#"+parent_id+" "+$this.data("tabpanel")).show().addClass("rm_active_tabpanel");
        });
        
        $(".rm_formname_display_span").click(function(){
            var $this = $(this);
            var parent_id = $this.closest(".rm-formcard-menu").attr("id");
            var $edit_input = $("#"+parent_id+" .rm_formname_edit_input");
            var $formname_span = $("#"+parent_id+" .rm_formname_display_span");
            $formname_span.hide();
            $edit_input.show().focus();
        });
        
        var last_label;
        $(".rm_formname_edit_input").each(function(){
            var $this = $(this);
            var $parent = $this.closest(".rm-formcard-menu");
            var parent_id = $parent.attr("id");
            var $formname_span = $("#"+parent_id+" .rm_formname_display_span");
            var form_id = $parent.data("formid");
            var old_edit_name = $("#"+parent_id+" .rm_formname_edit_link_span").text();
            
            $this.on("focus", function(e){
                var temp = $this.val().trim();
                if(temp.length)
                    last_label = temp;
            })
            
            $this.blur(function(){
                var new_name = $this.val().trim();
                $this.hide();
                
                if(new_name.length <= 0) {
                    $formname_span.show();
                    $this.val(last_label);
                    return;
                }
                
                if(new_name === last_label) {
                    $formname_span.show();
                    return;
                }               
                
                $formname_span.text(new_name).show();
                $parent.find('.rm-formcard-menu-form-name-save-loader').show();
                $("#"+parent_id+" .rm_formname_edit_link_span").text('');
                update_form(form_id, {"form_name":new_name}, function(){
                    $(".rmcard#"+form_id+" span.rm_form_name").text(new_name);
                    $parent.find('.rm-formcard-menu-form-name-save-loader').hide();
                    $parent.find('.rm-formcard-menu-form-name-save-icon').show();
                    $("#"+parent_id+" .rm_formname_edit_link_span").text(old_edit_name);
                    setTimeout(function(){
                        $parent.find('.rm-formcard-menu-form-name-save-icon').fadeOut();
                    }, 1000);
                });
            });
            
            $this.on("keydown",function(e){
                if(e.keyCode === 13 || e.keyCode === 27) {
                    jQuery(this).blur();
                } 
            });
        });
        
        position_formcard_menu();
        
        $(window).resize(position_formcard_menu);
        
        $(".rm_publish_popup_link").each(function(){
            jQuery(this).click(function(){
                rm_set_publish_popup( jQuery(this).data("form_id"), jQuery(this).data("publish_type"));
                jQuery("#rm_form_publish_popup").show();
            });            
        });
    });
    
    $(document).mouseup(function (e) {
        var container = $(".rm-formcard-menu");
        if (!container.is(e.target) // if the target of the click isn't the container... 
                && container.has(e.target).length === 0) // ... nor a descendant of the container 
        {
            container.hide();
        }
    });
    
    var update_form = function(form_id, data, callback) {
        //console.log(form_id);console.log(data);return;
        var req_data = {
                        'action': 'rm_fcm_update_form',
                        'data': data,
                        'form_id': form_id
                    };

        $.post(ajaxurl, req_data, function (response) {
            if(typeof callback === "function")
                callback();
        });
    }
    
    var position_formcard_menu = function(){
        var window_width = $(window).width();
        $(".rm_formcard_menu_icon").each(function(){
            var $parent_card = $(this).closest(".rmcard");            
            var $fc_menu = $parent_card.find(".rm-formcard-menu");
            var $nub = $parent_card.find(".rm-formcard-menu-nub");
            
            $fc_menu.css("visibility","hidden").show();
            
            var fcm_width = $fc_menu.outerWidth();
            var fcm_pos = $fc_menu.offset();

            if(fcm_pos.left + fcm_width >= window_width) {
                fcm_pos.left -= (fcm_pos.left + fcm_width - window_width + 20);
                $fc_menu.offset(fcm_pos);
            }
            
            var nub_pos = {
                            'left': $parent_card.find(".rm_formcard_menu_icon i").offset().left + 6,
                            'top' : $nub.offset().top
                          };
            
            $nub.offset(nub_pos);
            $fc_menu.hide().css("visibility","visible");
            
            var current_left_nav = $('.rm-formcard-menu-nub').css('left');
            $('.rm_formcard_menu_icon').click(function(){
                var menu_id = $(this).attr('data-menu-panel');
                if($(this).hasClass('rm_form_name')){
                    $(menu_id+' .rm-formcard-menu-nub').css('left','55px');
                    $(menu_id).css('left','10px');
                }else{
                    $(menu_id+' .rm-formcard-menu-nub').css('left',current_left_nav);
                    $(menu_id).css('left','90px');
                }
            });
        });
    }
    
})(jQuery);

function rm_copy_content_2(target) {

    var text_to_copy = jQuery(target).text();

    var tmp = jQuery("<input id='fd_form_shortcode_input' readonly>");
    var target_html = jQuery(target).html();
    jQuery(target).html('');
    jQuery(target).append(tmp);
    tmp.val(text_to_copy).select();
    var result = document.execCommand("copy");

    if (result != false) {
        jQuery(target).html(target_html);
        var el_success_msg = jQuery(target).data("copy_success_msg");
        if(typeof el_success_msg !== "undefined") {
            jQuery(el_success_msg).fadeIn('slow');
            jQuery(el_success_msg).fadeOut('slow');
        }
    } else {
        jQuery(document).mouseup(function (e) {
            var container = jQuery("#fd_form_shortcode_input");
            if (!container.is(e.target) // if the target of the click isn't the container... 
                    && container.has(e.target).length === 0) // ... nor a descendant of the container 
            {
                jQuery(target).html(target_html);
            }
        });
    }
}
