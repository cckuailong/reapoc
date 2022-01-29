(function($){
    $(document).ready(function(){
        $('.rm_otpw_login_back_btn').each(function(){
            $(this).click(function(){
                var $container = $(this).closest(".rm_otp_widget_container");
                $container.find("#rm_otp_enter_email").addClass("rm-otpw-animationLeft").show();
                $container.find(".rm_otp_after_email").hide();
            });            
        });
    });
})(jQuery);


function rm_otpw_proceed(e, ele, op) {
    var parent_container_id = jQuery(ele).closest(".rm_otp_widget_container").attr("id");
    return rm_call_otp(e, "#"+parent_container_id, op);
}