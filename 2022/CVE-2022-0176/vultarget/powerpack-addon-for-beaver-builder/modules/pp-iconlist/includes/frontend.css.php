.fl-node-<?php echo $id; ?> .pp-icon-list:before {
	content: "" !important;
}
.fl-node-<?php echo $id; ?> .pp-icon-list:not(.pp-user-agent-ie) [class^="pp-icon-list"] {
	font-family: unset !important;
}
.fl-node-<?php echo $id; ?> .pp-icon-list .pp-icon-list-items .pp-icon-list-item {
	display: table;
	margin-bottom: <?php echo $settings->item_margin; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-icon-list .pp-icon-list-items .pp-icon-list-item .pp-list-item-icon {
	float: left;
	margin-right: <?php echo $settings->icon_space; ?>px;
	<?php if ( isset( $settings->icon_bg ) && ! empty( $settings->icon_bg ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->icon_bg ); ?>;
	<?php } ?>
	color: #<?php echo $settings->icon_color; ?>;
	font-size: <?php echo $settings->icon_size; ?>px;
	padding: <?php echo $settings->icon_padding; ?>px;
	text-align: center;
	display: inline-block;
	line-height: <?php echo ('' != $settings->icon_size) ? $settings->icon_size : ''; ?>px;
	<?php if ( $settings->list_type == 'number' || $settings->icon_bg != '' ) : ?>
		width: <?php echo ('' != $settings->icon_size) ? $settings->icon_size * 2 : ''; ?>px;
		height: <?php echo ('' != $settings->icon_size) ? $settings->icon_size * 2 : ''; ?>px;
		line-height: <?php echo ('' != $settings->icon_size) ? $settings->icon_size * 2 : ''; ?>px;
	<?php endif; ?>
	vertical-align: middle;
	-webkit-transition: all 0.3s ease-in-out;
	-moz-transition: all 0.3s ease-in-out;
	transition: all 0.3s ease-in-out;
}
<?php
	// Icon - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'icon_border',
		'selector' 		=> ".fl-node-$id .pp-icon-list .pp-icon-list-items .pp-icon-list-item .pp-list-item-icon",
	) );
?>
.fl-node-<?php echo $id; ?> .pp-icon-list .pp-icon-list-items .pp-icon-list-item:hover .pp-list-item-icon {
	<?php if ( isset( $settings->icon_bg_hover ) && ! empty( $settings->icon_bg_hover ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->icon_bg_hover ); ?>;
	<?php } ?>
	color: #<?php echo $settings->icon_color_hover; ?>;
	border-color: #<?php echo $settings->icon_border_color_hover; ?>;
	-webkit-transition: all 0.3s ease-in-out;
	-moz-transition: all 0.3s ease-in-out;
	transition: all 0.3s ease-in-out;
}
.fl-node-<?php echo $id; ?> .pp-icon-list .pp-icon-list-items.pp-list-type-number .pp-icon-list-item .pp-list-item-icon {
	<?php if ( isset( $settings->text_typography['font_family'] ) && 'Default' != $settings->text_typography['font_family'] ) { ?>
		font-family: <?php echo $settings->text_typography['font_family']; ?>;
		<?php if ( isset( $settings->text_typography['font_weight'] ) ) { ?>
			font-weight: <?php echo $settings->text_typography['font_weight']; ?>;
		<?php } ?>
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-icon-list .pp-icon-list-items .pp-icon-list-item .pp-list-item-text {
	display: table-cell;
	color: #<?php echo $settings->text_color; ?>;
	vertical-align: middle;
}

<?php
	// Caption Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'text_typography',
		'selector' 		=> ".fl-node-$id .pp-icon-list .pp-icon-list-items .pp-icon-list-item .pp-list-item-text",
	) );
?>
