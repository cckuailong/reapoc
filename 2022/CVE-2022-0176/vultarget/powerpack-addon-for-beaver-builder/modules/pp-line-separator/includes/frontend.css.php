.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-only {
	<?php if($settings->separator_alignment) { ?>text-align: <?php echo $settings->separator_alignment; ?>;<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-only .pp-line-separator {
	<?php if($settings->line_color) { ?>border-bottom-color: #<?php echo $settings->line_color; ?>;<?php } ?>
	<?php if($settings->line_style) { ?>border-bottom-style: <?php echo $settings->line_style; ?>;<?php } ?>
	<?php if($settings->line_height) { ?>border-bottom-width: <?php echo $settings->line_height; ?>px;<?php } ?>
	<?php if($settings->line_width) { ?>width: <?php echo $settings->line_width; ?>%;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-icon:before,
.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-icon:after {
	<?php if($settings->line_color) { ?>border-bottom-color: #<?php echo $settings->line_color; ?>;<?php } ?>
	<?php if($settings->line_style) { ?>border-bottom-style: <?php echo $settings->line_style; ?>;<?php } ?>
	<?php if($settings->line_height) { ?>border-bottom-width: <?php echo $settings->line_height; ?>px;<?php } ?>
	<?php if($settings->line_width) { ?>width: <?php echo $settings->line_width; ?>%;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-icon:before {
	left: auto;
	right: 50%;
	<?php if($settings->icon_line_space) { ?>margin-right: <?php echo $settings->icon_line_space; ?>px;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-icon:after {
	left: 50%;
	right: auto;
	<?php if($settings->icon_line_space) { ?>margin-left: <?php echo $settings->icon_line_space; ?>px;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-icon.left:before {
	display: none;
}
.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-icon.left:after {
	left: 1%;
}
.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-icon.right:after {
	display: none;
}
.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-icon.right:before {
	right: 1%;
}

.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-line-icon .pp-line-separator.pp-icon-image {
	<?php if($settings->line_color) { ?>border-bottom-color: #<?php echo $settings->line_color; ?>;<?php } ?>
	<?php if($settings->line_style) { ?>border-bottom-style: <?php echo $settings->line_style; ?>;<?php } ?>
	<?php if($settings->line_height) { ?>border-bottom-width: <?php echo $settings->line_height; ?>px;<?php } ?>
	<?php if($settings->line_width) { ?>width: <?php echo $settings->line_width; ?>%;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-icon-image .pp-icon-wrap {
	<?php if($settings->separator_alignment) { ?>text-align: <?php echo $settings->separator_alignment; ?>;<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-icon-image .pp-icon-wrap span.pp-icon {
	<?php if($settings->font_icon_bg_color) { ?>background: #<?php echo $settings->font_icon_bg_color; ?>;<?php } ?>
	<?php if($settings->font_icon_color) { ?>color: #<?php echo $settings->font_icon_color; ?>;<?php } ?>
	<?php if($settings->font_icon_font_size) { ?>font-size: <?php echo $settings->font_icon_font_size; ?>px;<?php } ?>
	<?php if($settings->font_icon_padding_top_bottom) { ?>
		padding-top: <?php echo $settings->font_icon_padding_top_bottom; ?>px;
		padding-bottom: <?php echo $settings->font_icon_padding_top_bottom; ?>px;
	<?php } ?>
	<?php if($settings->font_icon_padding_top_bottom) { ?>
		padding-left: <?php echo $settings->font_icon_padding_left_right; ?>px;
		padding-right: <?php echo $settings->font_icon_padding_left_right; ?>px;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-icon-image {
	<?php if($settings->separator_alignment) { ?>text-align: <?php echo $settings->separator_alignment; ?>;<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-icon-image .pp-image-wrap {
	<?php if($settings->font_icon_bg_color) { ?>background: #<?php echo $settings->font_icon_bg_color; ?>;<?php } ?>
	<?php if($settings->font_icon_padding_top_bottom) { ?>
		padding-top: <?php echo $settings->font_icon_padding_top_bottom; ?>px;
		padding-bottom: <?php echo $settings->font_icon_padding_top_bottom; ?>px;
	<?php } ?>
	<?php if($settings->font_icon_padding_top_bottom) { ?>
		padding-left: <?php echo $settings->font_icon_padding_left_right; ?>px;
		padding-right: <?php echo $settings->font_icon_padding_left_right; ?>px;
	<?php } ?>
}

<?php
	// Icon - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'icon_border',
		'selector' 		=> ".fl-node-$id .pp-line-separator-inner.pp-icon-image .pp-icon-wrap span.pp-icon, .fl-node-$id .pp-line-separator-inner.pp-icon-image .pp-image-wrap",
	) );
?>
.fl-node-<?php echo $id; ?> .pp-line-separator-inner.pp-icon-image .pp-image-wrap img {
	<?php if ( isset( $settings->icon_border ) && isset( $settings->icon_border['radius'] ) ) { ?>
		border-top-left-radius: <?php echo $settings->icon_border['radius']['top_left']; ?>px;
		border-top-right-radius: <?php echo $settings->icon_border['radius']['top_right']; ?>px;
		border-bottom-left-radius: <?php echo $settings->icon_border['radius']['bottom_left']; ?>px;
		border-bottom-right-radius: <?php echo $settings->icon_border['radius']['bottom_right']; ?>px;
	<?php } ?>
	<?php if($settings->icon_image_select != 'image' && $settings->font_icon_font_size) { ?>height: <?php echo $settings->font_icon_font_size; ?>px;<?php } ?>
	<?php if($settings->font_icon_font_size) { ?>width: <?php echo $settings->font_icon_font_size; ?>px;<?php } ?>
}
