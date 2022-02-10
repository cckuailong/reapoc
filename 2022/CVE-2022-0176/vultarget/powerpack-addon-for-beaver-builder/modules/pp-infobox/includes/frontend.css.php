.fl-node-<?php echo $id; ?> .pp-infobox-title-prefix {
	<?php if ( empty( $settings->title_prefix ) ) { ?>
	display: none;
	<?php } ?>
	<?php if( $settings->title_prefix_color ) { ?>color: #<?php echo $settings->title_prefix_color; ?>;<?php } ?>
	<?php if( $settings->title_prefix_margin['top'] ) { ?>margin-top: <?php echo $settings->title_prefix_margin['top']; ?>px;<?php } ?>
	<?php if( $settings->title_prefix_margin['bottom'] ) { ?>margin-bottom: <?php echo $settings->title_prefix_margin['bottom']; ?>px;<?php } ?>
}
<?php
// Typography - Title Prefix
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'title_prefix_typography',
	'selector' 		=> ".fl-node-$id .pp-infobox-title-prefix",
) );
?>
.fl-node-<?php echo $id; ?> .pp-infobox-title-wrapper .pp-infobox-title {
	<?php if( $settings->title_color ) { ?>color: #<?php echo $settings->title_color; ?>;<?php } ?>
	margin-top: <?php echo $settings->title_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->title_margin['bottom']; ?>px;
}
<?php
// Typography - Title
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'title_typography',
	'selector' 		=> ".fl-node-$id .pp-infobox-title-wrapper .pp-infobox-title",
) );
?>
.fl-node-<?php echo $id; ?> .pp-infobox-description {
	<?php if( $settings->text_color ) { ?>color: #<?php echo $settings->text_color; ?>;<?php } ?>
	margin-top: <?php echo $settings->text_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->text_margin['bottom']; ?>px;
}
<?php
// Typography - Description
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'desc_typography',
	'selector' 		=> ".fl-node-$id .pp-infobox-description",
) );
?>
.fl-node-<?php echo $id; ?> .pp-infobox:hover .pp-infobox-title {
	<?php if ( !empty( $settings->title_color_h ) ) { ?>
		color: #<?php echo $settings->title_color_h; ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-infobox:hover .pp-infobox-description {
	<?php if ( !empty( $settings->text_color_h ) ) { ?>
		color: #<?php echo $settings->text_color_h; ?>;
	<?php } ?>
}
<?php
// Icon - Border
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'icon_border',
	'selector' 		=> ".fl-node-$id .pp-infobox-icon, .fl-node-$id .pp-infobox-image img",
) );
?>
<?php if( $settings->icon_type == 'icon' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-infobox-icon {
		<?php if( $settings->icon_box_size ) { ?>padding: <?php echo $settings->icon_box_size; ?>px;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-icon-inner  {
		<?php if( $settings->icon_width ) { ?>height: <?php echo $settings->icon_width; ?>px;<?php } ?>
		<?php if( $settings->icon_width ) { ?>width: <?php echo $settings->icon_width; ?>px;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-icon-inner span.pp-icon {
		<?php if( $settings->icon_background ) { ?>background: <?php echo pp_get_color_value( $settings->icon_background ); ?>;<?php } ?>
		<?php if( $settings->icon_color ) { ?>color: #<?php echo $settings->icon_color; ?>;<?php } ?>
		<?php if( $settings->icon_font_size ) { ?>font-size: <?php echo $settings->icon_font_size; ?>px;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-icon-inner span.pp-icon,
	.fl-node-<?php echo $id; ?> .pp-infobox-icon-inner span:before {
		<?php if( $settings->icon_font_size ) { ?>font-size: <?php echo $settings->icon_font_size; ?>px;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-infobox:hover .pp-infobox-icon {
		<?php if( ! empty( $settings->icon_border_color_hover ) ) { ?>border-color: #<?php echo $settings->icon_border_color_hover; ?>;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-infobox:hover .pp-infobox-icon span.pp-icon {
		<?php if( $settings->icon_background_hover ) { ?>background: <?php echo pp_get_color_value( $settings->icon_background_hover ); ?>;<?php } ?>
		<?php if( $settings->icon_color_hover ) { ?>color: #<?php echo $settings->icon_color_hover; ?>;<?php } ?>
	}
<?php } ?>
<?php if( $settings->icon_type == 'image' ) { ?>

	.fl-node-<?php echo $id; ?> .pp-infobox-image {
		<?php if( $settings->layouts == '3' || $settings->layouts == '4' ) { ?>
			margin-bottom: 0;
		<?php } ?>
		<?php if( $settings->layouts == '5' || $settings->layouts == '6' ) { ?>
			text-align: <?php echo $settings->alignment; ?>
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-image img {
		height: auto;
		<?php if( $settings->icon_box_size ) { ?>padding: <?php echo $settings->icon_box_size; ?>px;<?php } ?>
		<?php if( $settings->image_width_type == 'custom' && $settings->image_width ) { ?>width: <?php echo $settings->image_width; ?>px;<?php } ?>
		<?php if( $settings->image_width_type == 'default' ) { ?>width: auto;<?php } ?>
		max-width: 100%;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox:hover .pp-infobox-image img {
		<?php if( ! empty( $settings->icon_border_color_hover ) ) { ?>
			border-color: #<?php echo $settings->icon_border_color_hover; ?>;
		<?php } ?>
	}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-infobox-icon-inner span.pp-icon,
.fl-node-<?php echo $id; ?> .pp-infobox-image img {
	<?php if ( isset( $settings->icon_border ) && is_array( $settings->icon_border ) ) { ?>
	border-top-left-radius: <?php echo $settings->icon_border['radius']['top_left']; ?>px;
	border-top-right-radius: <?php echo $settings->icon_border['radius']['top_right']; ?>px;
	border-bottom-left-radius: <?php echo $settings->icon_border['radius']['bottom_left']; ?>px;
	border-bottom-right-radius: <?php echo $settings->icon_border['radius']['bottom_right']; ?>px;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-infobox {
	<?php if( $settings->box_background ) { ?>
		background: <?php echo pp_get_color_value( $settings->box_background ); ?>;
	<?php } ?>
	<?php if( $settings->padding_top >= 0 && '' != $settings->padding_top ) { ?>
		padding-top: <?php echo $settings->padding_top; ?>px;
		padding-bottom: <?php echo $settings->padding_top; ?>px;
	<?php } ?>
	<?php if( $settings->padding_left >= 0 && '' != $settings->padding_left ) { ?>
		padding-left: <?php echo $settings->padding_left; ?>px;
		padding-right: <?php echo $settings->padding_left; ?>px;
	<?php } ?>
	<?php if ( $settings->alignment ) { ?>text-align: <?php echo $settings->alignment; ?>;<?php } ?>
}

<?php
// Box - Border
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'box_border',
	'selector' 		=> ".fl-node-$id .pp-infobox",
) );
?>

.fl-node-<?php echo $id; ?> .pp-infobox:hover {
	<?php if( $settings->box_background_hover ) { ?>
	background: <?php echo pp_get_color_value( $settings->box_background_hover ); ?>;
	<?php } ?>
}

<?php
// Typography - Button
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'button_typography',
	'selector' 		=> ".fl-node-$id .pp-more-link",
) );
?>
<?php if( $settings->pp_infobox_link_type == 'read_more' || $settings->pp_infobox_link_type == 'button' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-infobox .pp-more-link {
		<?php if( $settings->pp_infobox_read_more_color ) { ?>color: #<?php echo $settings->pp_infobox_read_more_color; ?>;<?php } ?>
		<?php if( $settings->pp_infobox_link_type == 'button' && $settings->button_bg_color != '' ) { ?>
			background-color: <?php echo pp_get_color_value( $settings->button_bg_color) ; ?>;
		<?php } ?>
		<?php if( $settings->pp_infobox_link_type == 'button' ) { ?>
			text-decoration: none;
			text-align: center;
			margin: 0 auto;
			<?php if ( $settings->button_width == 'full' ) { ?>
				width: 100%;
			<?php } ?>
		<?php } ?>
	}

	<?php
		// Button - Custom Width
		FLBuilderCSS::responsive_rule( array(
			'settings'		=> $settings,
			'setting_name'	=> 'button_width_custom',
			'selector'		=> ".fl-node-$id .pp-infobox .pp-more-link",
			'prop'			=> 'width',
			'unit'			=> 'px',
			'enabled'		=> ( 'button' == $settings->pp_infobox_link_type && 'custom' == $settings->button_width )
		) );

		// Button - Padding
		FLBuilderCSS::dimension_field_rule( array(
			'settings'		=> $settings,
			'setting_name' 	=> 'button_padding',
			'selector' 		=> ".fl-node-$id .pp-infobox .pp-more-link",
			'unit'			=> 'px',
			'props'			=> array(
				'padding-top' 		=> 'button_padding_top',
				'padding-right' 	=> 'button_padding_right',
				'padding-bottom' 	=> 'button_padding_bottom',
				'padding-left' 		=> 'button_padding_left',
			),
			'enabled'		=> 'button' == $settings->pp_infobox_link_type
		) );

		// Link - Margin
		FLBuilderCSS::dimension_field_rule( array(
			'settings'		=> $settings,
			'setting_name' 	=> 'read_more_margin',
			'selector' 		=> ".fl-node-$id .pp-infobox .pp-more-link",
			'unit'			=> 'px',
			'props'			=> array(
				'margin-top' 		=> 'read_more_margin_top',
				'margin-right' 		=> 'read_more_margin_right',
				'margin-bottom' 	=> 'read_more_margin_bottom',
				'margin-left' 		=> 'read_more_margin_left',
			),
			'enabled'		=> 'button' == $settings->pp_infobox_link_type
		) );

		// Button - Border
		FLBuilderCSS::border_field_rule( array(
			'settings' 		=> $settings,
			'setting_name' 	=> 'button_border_setting',
			'selector' 		=> ".fl-node-$id .pp-infobox .pp-more-link",
			'enabled'		=> 'button' == $settings->pp_infobox_link_type
		) );
	?>

	.fl-node-<?php echo $id; ?> .pp-infobox .pp-more-link:hover {
		<?php if( $settings->pp_infobox_read_more_color_hover ) { ?>
			color: #<?php echo $settings->pp_infobox_read_more_color_hover; ?>;
		<?php } ?>
		<?php if( $settings->pp_infobox_link_type == 'button' && $settings->button_bg_hover_color != '' ) { ?>
			background-color: <?php echo pp_get_color_value( $settings->button_bg_hover_color ); ?>;
		<?php } ?>
	}

	<?php
		// Border - Hover Settings
		if ( ! empty( $settings->button_border_hover_color ) && is_array( $settings->button_border_setting ) ) {
			$settings->button_border_setting['color'] = $settings->button_border_hover_color;
		}
		FLBuilderCSS::border_field_rule( array(
			'settings' 		=> $settings,
			'setting_name' 	=> 'button_border_setting',
			'selector' 		=> ".fl-node-$id .pp-infobox .pp-more-link:hover",
			'enabled'		=> 'button' == $settings->pp_infobox_link_type
		) );
	?>
	.fl-node-<?php echo $id; ?> .pp-infobox .pp-more-link .pp-button-icon {
		font-size: <?php echo $settings->button_icon_size; ?>px;
		color: <?php echo pp_get_color_value( $settings->button_icon_color ); ?>;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox .pp-more-link:hover .pp-button-icon {
		color: <?php echo pp_get_color_value( $settings->button_icon_color_hover ); ?>;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox .pp-more-link .pp-button-icon-left {
		margin-right: <?php echo $settings->button_icon_spacing; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox .pp-more-link .pp-button-icon-right {
		margin-left: <?php echo $settings->button_icon_spacing; ?>px;
	}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-infobox .animated {
	<?php if( $settings->animation_duration ) { ?>-webkit-animation-duration: <?php echo $settings->animation_duration; ?>ms;<?php } ?>
	<?php if( $settings->animation_duration ) { ?>-moz-animation-duration: <?php echo $settings->animation_duration; ?>ms;<?php } ?>
	<?php if( $settings->animation_duration ) { ?>-o-animation-duration: <?php echo $settings->animation_duration; ?>ms;<?php } ?>
	<?php if( $settings->animation_duration ) { ?>-ms-animation-duration: <?php echo $settings->animation_duration; ?>ms;<?php } ?>
	<?php if( $settings->animation_duration ) { ?>animation-duration: <?php echo $settings->animation_duration; ?>ms;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3-wrapper,
.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4-wrapper {
	<?php if ( isset( $settings->icon_position ) ) { ?>
		<?php if ( 'top' == $settings->icon_position ) { ?>
		align-items: flex-start;
		<?php } ?>
		<?php if ( 'center' == $settings->icon_position ) { ?>
		align-items: center;
		<?php } ?>
		<?php if ( 'bottom' == $settings->icon_position ) { ?>
		align-items: flex-end;
		<?php } ?>
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .pp-icon-wrapper {
	margin-right: <?php echo $settings->space_bt_icon_text; ?>px;
}

.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-icon-wrapper {
	margin-left: <?php echo $settings->space_bt_icon_text; ?>px;
}

<?php if ( 'left' == $settings->alignment ) { ?>
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-infobox-description,
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-heading-wrapper {
		float: left;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-heading-wrapper {
		flex: 0 1 auto;
	}
<?php } ?>

<?php if ( 'center' == $settings->alignment ) { ?>
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-1 .pp-infobox-description,
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-infobox-description {
		float: none;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-1 .pp-heading-wrapper,
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-heading-wrapper {
		margin: 0 auto;
		float: none;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .pp-heading-wrapper,
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .pp-icon-wrapper,
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-heading-wrapper,
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-icon-wrapper {
		/*flex: auto;*/
	}
<?php } ?>

<?php if ( 'right' == $settings->alignment ) { ?>
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-1 .pp-heading-wrapper,
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-heading-wrapper,
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-infobox-description {
		float: right;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-1 .pp-infobox-description {
		clear: both;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .pp-heading-wrapper {
		flex: 0 1 auto;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .pp-icon-wrapper {
		flex: 1;
		text-align: right;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-heading-wrapper {
		flex: 1;
	}
	.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-icon-wrapper {
		flex: 0 1 auto;
		text-align: right;
	}
<?php } ?>

@media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-infobox {
		<?php if ( isset( $settings->alignment_medium ) && 'default' != $settings->alignment_medium ) { ?>
		text-align: <?php echo $settings->alignment_medium; ?>;
		<?php } ?>
	}
}

@media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-infobox {
		<?php if ( isset( $settings->alignment_responsive ) && 'default' != $settings->alignment_responsive ) { ?>
		text-align: <?php echo $settings->alignment_responsive; ?>;
		<?php } ?>
	}
	<?php if ( isset( $settings->alignment_responsive ) && 'default' != $settings->alignment_responsive ) { ?>
		.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .layout-3-wrapper,
		.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .layout-4-wrapper {
			align-items: normal;
		}
		<?php if ( 'left' == $settings->alignment_responsive ) { ?>
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-infobox-description,
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-heading-wrapper {
				float: left;
			}
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-heading-wrapper {
				flex: 0 1 auto;
			}
		<?php } ?>

		<?php if ( 'center' == $settings->alignment_responsive ) { ?>
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-1 .pp-infobox-description,
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-infobox-description {
				float: none;
				text-align: center;
			}
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-1 .pp-heading-wrapper,
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-heading-wrapper {
				margin: 0 auto;
				float: none;
			}
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .pp-heading-wrapper,
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .pp-icon-wrapper,
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-heading-wrapper,
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-icon-wrapper {
				flex: auto;
			}
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .layout-3-wrapper,
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .layout-4-wrapper,
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-5 {
				text-align: center;
			}
		<?php } ?>

		<?php if ( 'right' == $settings->alignment_responsive ) { ?>
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-1 .pp-heading-wrapper,
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-heading-wrapper,
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-2 .pp-infobox-description {
				float: right;
			}
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-1 .pp-infobox-description {
				clear: both;
			}
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .pp-icon-wrapper {
				flex: 1;
				text-align: right;
			}
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-3 .pp-heading-wrapper {
				flex: 0 1 auto;
			}
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-heading-wrapper {
				flex: 1;
			}
			.fl-node-<?php echo $id; ?> .pp-infobox-wrap .layout-4 .pp-icon-wrapper {
				flex: 0;
				text-align: right;
			}
		<?php } ?>
	<?php } ?>	
}
