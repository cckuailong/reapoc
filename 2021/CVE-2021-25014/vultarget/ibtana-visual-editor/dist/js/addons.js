(function($) {
 $(document).ready(function() {

   var data_to_post = {
     action: 'ive_addons_page_cards',
     wpnonce: ibtana_addons.wpnonce
   };
   jQuery.ajax({
     method: "POST",
     url:    ibtana_addons.admin_ajax,
     data:   data_to_post
   }).done(function( data, status ) {
     if ( 'success' == status ) {
       $( '#the-ive-addons-list' ).append( data );
     }
   });


   $( '#the-ive-addons-list' ).on( 'click', '.ive-ibtana-add-on-buttons [data-href]', function() {
     $(this).addClass('updating-message');
     $(this).text( $(this).attr('data-text') );
     var data_href = $( this ).attr( 'data-href' );
     jQuery.get(data_href, function( data, status ) {
       location.reload(true);
     });
   });

 });


})(jQuery);
