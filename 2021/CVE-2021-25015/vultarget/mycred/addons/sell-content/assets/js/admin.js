(function($) {
    $( document ).on( 'click', '#update-sales-count', function( e ){
		e.preventDefault();
		$.ajax({
            url: ajaxurl,
            type: 'POST',
            data:{
                action: 'mycred_ajax_update_sell_count'
            },
            beforeSend: function() {
                jQuery('.mycred-update-sells-count').css("display", "inherit");
            },
            success:function(data) {
                alert( data );
                jQuery('.mycred-update-sells-count').hide();
            }
        })
	} )
})( jQuery );