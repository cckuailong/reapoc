.fl-node-<?php echo $id; ?> .pp-breadcrumbs {
	text-align: <?php echo $settings->alignment; ?>;
	<?php if ( '' !== $settings->box_bg_color ) { ?>
		background-color: #<?php echo $settings->box_bg_color; ?>;
	<?php } ?>
	color: #<?php echo $settings->text_color; ?>;
}

.fl-node-<?php echo $id; ?> .pp-breadcrumbs span.separator {
	color: #<?php echo $settings->text_color; ?>;
}

<?php

// Box Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'box_padding',
	'selector' 		=> ".fl-node-$id .pp-breadcrumbs",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'box_padding_top',
		'padding-right' 	=> 'box_padding_right',
		'padding-bottom' 	=> 'box_padding_bottom',
		'padding-left' 		=> 'box_padding_left',
	),
) );

// Box Border - Settings
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'box_border',
	'selector' 		=> ".fl-node-$id .pp-breadcrumbs",
) );

FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'link_typography',
	'selector'		=> ".fl-node-$id .pp-breadcrumbs a, .fl-node-$id .pp-breadcrumbs span:not(.separator)"
) );
?>

.fl-node-<?php echo $id; ?> .pp-breadcrumbs a,
.fl-node-<?php echo $id; ?> .pp-breadcrumbs span.breadcrumb_last,
.fl-node-<?php echo $id; ?> .pp-breadcrumbs span.last,
.fl-node-<?php echo $id; ?> .pp-breadcrumbs span.current-item {
	color: #<?php echo $settings->link_color; ?>;
}

.fl-node-<?php echo $id; ?> .pp-breadcrumbs a:hover,
.fl-node-<?php echo $id; ?> .pp-breadcrumbs span.breadcrumb_last:hover,
.fl-node-<?php echo $id; ?> .pp-breadcrumbs span.last:hover,
.fl-node-<?php echo $id; ?> .pp-breadcrumbs span.current-item:hover {
	color: #<?php echo $settings->link_hover_color; ?>;
}

.fl-node-<?php echo $id; ?> .pp-breadcrumbs a,
.fl-node-<?php echo $id; ?> .pp-breadcrumbs span:not(.separator) {
	<?php if ( '' !== $settings->link_bg_color ) { ?>
		background-color: #<?php echo $settings->link_bg_color; ?>;
	<?php } ?>
	<?php if ( '' != $settings->link_spacing ) { ?>
		margin-left: <?php echo ( $settings->link_spacing / 2 ); ?>px;
		margin-right: <?php echo ( $settings->link_spacing / 2 ); ?>px;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-breadcrumbs a:hover,
.fl-node-<?php echo $id; ?> .pp-breadcrumbs span:not(.separator):hover {
	<?php if ( '' !== $settings->link_bg_hover ) { ?>
		background-color: #<?php echo $settings->link_bg_hover; ?>;
	<?php } ?>
}

<?php
// Link Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'link_padding',
	'selector' 		=> ".fl-node-$id .pp-breadcrumbs a, .fl-node-$id .pp-breadcrumbs span:not(.separator)",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'link_padding_top',
		'padding-right' 	=> 'link_padding_right',
		'padding-bottom' 	=> 'link_padding_bottom',
		'padding-left' 		=> 'link_padding_left',
	),
) );

// Link Border - Settings
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'link_border',
	'selector' 		=> ".fl-node-$id .pp-breadcrumbs a, .fl-node-$id .pp-breadcrumbs span:not(.separator)",
) );

?>