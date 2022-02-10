jQuery( function ( $ ) {

	// TABS
	$('ul.a3-metabox-data-tabs').show();
	$('div.a3-metabox-panel-wrap').each(function(){
		$(this).find('div.a3-metabox-panel').hide();
		$(this).find('div.a3-metabox-panel').first().show();
	});
	$('ul.a3-metabox-data-tabs a').on( 'click', function(){
		var panel_wrap =  $(this).closest('div.a3-metabox-panel-wrap');
		$('ul.a3-metabox-data-tabs li', panel_wrap).removeClass('active');
		$(this).parent().addClass('active');
		$('div.a3-metabox-panel', panel_wrap).hide();
		$( $(this).attr('href') ).show();
		return false;
	});
	$('ul.a3-metabox-data-tabs li:visible').eq(0).find('a').trigger('click');

	// META BOXES - Open/close
	$('.a3-metabox-wrapper').on('click', '.a3-metabox-item h3', function(event){
		// If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
		if ($(event.target).filter(':input, option').length) return;
		$( this ).parent( '.a3-metabox-item' ).toggleClass( 'closed' ).toggleClass( 'open' );
		$(this).next('.a3-metabox-item-content').slideToggle();
	})
	.on('click', '.expand_all', function(event){
		$(this).closest('.a3-metabox-wrapper').find('.a3-metabox-item').removeClass( 'closed' ).addClass( 'open' );
		$(this).closest('.a3-metabox-wrapper').find('.a3-metabox-item-content').slideDown();
		return false;
	})
	.on('click', '.close_all', function(event){
		$(this).closest('.a3-metabox-wrapper').find('.a3-metabox-item').removeClass( 'open' ).addClass( 'closed' );
		$(this).closest('.a3-metabox-wrapper').find('.a3-metabox-item-content').slideUp();
		return false;
	});
	$('.a3-metabox-item.closed').each(function(){
		$(this).find('.a3-metabox-item-content').slideUp();
	});

});
