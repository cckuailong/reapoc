(function($) {

	<?php $classes = '';
	if( $settings->icon_animation ) {
		$classes = $settings->icon_animation;
	}
	else {
		$classes= '';
	} ?>
	$('.fl-node-<?php echo $id; ?> .pp-infobox').on('mouseenter', function() {
		$('.fl-node-<?php echo $id; ?> .pp-icon-wrapper').addClass(' <?php echo $classes; ?>');
	});
	$('.fl-node-<?php echo $id; ?> .pp-infobox').on('mouseleave', function() {
		$('.fl-node-<?php echo $id; ?> .pp-icon-wrapper').removeClass(' <?php echo $classes; ?>');
	});

})(jQuery);
