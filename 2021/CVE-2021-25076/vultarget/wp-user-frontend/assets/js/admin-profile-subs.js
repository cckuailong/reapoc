jQuery(function($) {
    $('.wpuf-post-exp-time').hide();
    $(".wpuf-post-exp-enabled").click(function(){
        if($(this).prop("checked")) {
            $('.wpuf-post-exp-time').show();
        } else {
            $('.wpuf-post-exp-time').hide();
        }
    });
});