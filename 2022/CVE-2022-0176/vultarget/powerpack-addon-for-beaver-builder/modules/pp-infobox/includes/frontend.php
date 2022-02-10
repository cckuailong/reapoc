<?php
$layout 		= $settings->layouts;
$wrap_class 	= 'pp-infobox-wrap';
$main_class 	= 'pp-infobox layout-' . $layout;
$button_class 	= ( 'button' == $settings->pp_infobox_link_type && '' != $settings->link_css_class ) ? ' ' . $settings->link_css_class : '';
$nofollow		= ( isset( $settings->link_nofollow ) && 'yes' == $settings->link_nofollow ) ? ' rel="nofollow"' : '';
$title_prefix_tag = ( isset( $settings->title_prefix_tag ) ) ? $settings->title_prefix_tag : 'span';
?>
<div class="<?php echo $wrap_class; ?>">
	<?php
	if( $settings->pp_infobox_link_type == 'box' ) { ?>
		<a class="pp-infobox-link" href="<?php echo $settings->link; ?>" target="<?php echo $settings->link_target; ?>"<?php echo $nofollow; ?>>
	<?php }
	
	include apply_filters( 'pp_infobox_layout_path', $module->dir . 'includes/layout-' . $layout . '.php', $layout, $settings );
	
	if( $settings->pp_infobox_link_type == 'box' ) { ?>
		</a>
	<?php } ?>
</div>
