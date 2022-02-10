;(function($) {
	
	$( '.fl-node-<?php echo $id; ?> .pp-breadcrumbs a' ).parent().css({'padding' : '0', 'background-color' : 'transparent', 'border' : '0', 'margin' : '0', 'box-shadow' : 'none'});
	<?php if ( 'yoast' === $settings->seo_type || 'rankmath' === $settings->seo_type ) { ?>
	$( '.fl-node-<?php echo $id; ?> .pp-breadcrumbs a' ).parent().parent().css({'padding' : '0', 'background-color' : 'transparent', 'border' : '0', 'margin' : '0', 'box-shadow' : 'none'});
	<?php } ?>

})(jQuery);
