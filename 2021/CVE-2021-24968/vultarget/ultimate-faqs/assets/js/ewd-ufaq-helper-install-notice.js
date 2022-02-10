jQuery( document ).ready( function( $ ) {

  jQuery(document).on( 'click', '.ewd-ufaq-helper-install-notice .notice-dismiss', function( event ) {
    var data = jQuery.param({
      action: 'ewd_ufaq_hide_helper_notice',
      nonce: ewd_ufaq_helper_notice.nonce
    });

    jQuery.post( ajaxurl, data, function() {} );
  });
});