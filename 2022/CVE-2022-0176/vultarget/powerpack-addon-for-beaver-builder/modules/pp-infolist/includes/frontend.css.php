.fl-node-<?php echo $id; ?> .pp-infolist-wrap .pp-list-item {
	padding-bottom: <?php echo ($settings->list_spacing >= 0) ? $settings->list_spacing.'px' : '25px'; ?>;
	<?php if ( $settings->connector_type == 'none' ) : ?>
		margin-bottom: 0;
	<?php endif; ?>
}
<?php
	// List - Spacing
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'list_spacing',
		'selector'		=> ".fl-node-$id .pp-infolist-wrap .pp-list-item",
		'prop'			=> 'padding-bottom',
		'unit'			=> 'px',
		'enabled'		=> ( isset( $settings->list_spacing ) && $settings->list_spacing >= 0 )
	) );
?>
.fl-node-<?php echo $id; ?> .pp-infolist-wrap .pp-list-item:last-child {
	margin-bottom: 0;
}
.fl-node-<?php echo $id; ?> .pp-infolist-title .pp-infolist-title-text {
	<?php if( $settings->title_color ) { ?>color: #<?php echo $settings->title_color; ?>;<?php } ?>
	margin-top: <?php echo $settings->title_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->title_margin['bottom']; ?>px;
}

<?php
	// Title Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'title_typography',
		'selector' 		=> ".fl-node-$id .pp-infolist-title .pp-infolist-title-text",
	) );
?>

.fl-node-<?php echo $id; ?> .pp-infolist-description {
	<?php if( $settings->text_color ) { ?>color: #<?php echo $settings->text_color; ?>;<?php } ?>
}

<?php
	// Text Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'text_typography',
		'selector' 		=> ".fl-node-$id .pp-infolist-description",
	) );
?>

.fl-node-<?php echo $id; ?> .pp-infolist-icon {
	<?php if( $settings->icon_border_radius ) { ?>border-radius: <?php echo $settings->icon_border_radius; ?>px;<?php } ?>
	<?php if( $settings->show_border == 'yes' ) { ?>
		<?php if( $settings->icon_border_color ) { ?>border-color: #<?php echo $settings->icon_border_color; ?>;<?php } ?>
		<?php if( $settings->icon_border_style ) { ?>border-style: <?php echo $settings->icon_border_style; ?>;<?php } ?>
		<?php if( $settings->icon_border_width ) { ?>border-width: <?php echo $settings->icon_border_width; ?>px;<?php } ?>
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-infolist-icon-inner img {
	<?php if( $settings->icon_border_radius ) { ?>border-radius: <?php echo $settings->icon_border_radius; ?>px;<?php } ?>
}

<?php
	// Icon - Inside Spacing
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'icon_box_size',
		'selector'		=> ".fl-node-$id .pp-infolist-icon",
		'prop'			=> 'padding',
		'unit'			=> 'px',
	) );

	// Icon - Size
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'icon_font_size',
		'selector'		=> ".fl-node-$id .pp-infolist-icon-inner img",
		'prop'			=> 'width',
		'unit'			=> 'px',
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'icon_font_size',
		'selector'		=> ".fl-node-$id .pp-infolist-icon-inner img",
		'prop'			=> 'height',
		'unit'			=> 'px',
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'icon_font_size',
		'selector'		=> ".fl-node-$id .pp-infolist-icon-inner span.pp-icon, .fl-node-$id .pp-infolist-icon-inner span.pp-icon:before",
		'prop'			=> 'font-size',
		'unit'			=> 'px',
	) );

	// Icon - Box Size
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'icon_box_width',
		'selector'		=> ".fl-node-$id .pp-infolist-icon-inner",
		'prop'			=> 'width',
		'unit'			=> 'px',
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'icon_box_width',
		'selector'		=> ".fl-node-$id .pp-infolist-icon-inner",
		'prop'			=> 'height',
		'unit'			=> 'px',
	) );
?>

.fl-node-<?php echo $id; ?> .pp-infolist-icon:hover {
	<?php if( $settings->show_border == 'yes' ) { ?>
		<?php if( $settings->icon_border_color_hover ) { ?>border-color: #<?php echo $settings->icon_border_color_hover; ?>;<?php } ?>
	<?php } ?>
}

<?php
	// Icons - Gap
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'icon_gap',
		'selector'		=> ".fl-node-$id .pp-infolist-wrap .layout-1 .pp-icon-wrapper",
		'prop'			=> 'margin-right',
		'unit'			=> 'px',
		'enabled'		=> ( $settings->icon_gap >= 0 )
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'icon_gap',
		'selector'		=> ".fl-node-$id .pp-infolist-wrap .layout-2 .pp-icon-wrapper",
		'prop'			=> 'margin-left',
		'unit'			=> 'px',
		'enabled'		=> ( $settings->icon_gap >= 0 )
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'icon_gap',
		'selector'		=> ".fl-node-$id .pp-infolist-wrap .layout-3 .pp-icon-wrapper",
		'prop'			=> 'margin-bottom',
		'unit'			=> 'px',
		'enabled'		=> ( $settings->icon_gap >= 0 )
	) );
?>

.fl-node-<?php echo $id; ?> .pp-infolist-wrap .layout-1 .pp-list-connector {
	<?php if( $settings->connector_color ) { ?>border-left-color: #<?php echo $settings->connector_color; ?>;<?php } ?>
	<?php if( $settings->connector_type ) { ?>border-left-style: <?php echo $settings->connector_type; ?>;<?php } ?>
	<?php if( $settings->connector_width ) { ?>border-left-width: <?php echo $settings->connector_width; ?>px;<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-infolist-wrap .layout-2 .pp-list-connector {
	<?php if( $settings->connector_color ) { ?>border-right-color: #<?php echo $settings->connector_color; ?>;<?php } ?>
	<?php if( $settings->connector_type ) { ?>border-right-style: <?php echo $settings->connector_type; ?>;<?php } ?>
	<?php if( $settings->connector_width ) { ?>border-right-width: <?php echo $settings->connector_width; ?>px;<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-infolist-wrap .layout-3 .pp-list-connector {
	<?php if( $settings->connector_color ) { ?>border-top-color: #<?php echo $settings->connector_color; ?>;<?php } ?>
	<?php if( $settings->connector_type ) { ?>border-top-style: <?php echo $settings->connector_type; ?>;<?php } ?>
	<?php if( $settings->connector_width ) { ?>border-top-width: <?php echo $settings->connector_width; ?>px;<?php } ?>
}

<?php
$number_items = count($settings->list_items);
for($i=0; $i < $number_items; $i++) :
	$items = $settings->list_items[$i]; ?>

	.fl-node-<?php echo $id; ?> .pp-list-item-<?php echo $i; ?> .pp-infolist-icon-inner .pp-icon {
		<?php if ( isset( $items->icon_background ) && ! empty( $items->icon_background ) ) { ?>
		background-color: <?php echo pp_get_color_value( $items->icon_background ); ?>;
		<?php } ?>
		<?php if( $settings->icon_border_radius ) { ?>border-radius: <?php echo $settings->icon_border_radius; ?>px;<?php } ?>
		<?php if( $items->icon_color ) { ?>color: #<?php echo $items->icon_color; ?>;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-list-item-<?php echo $i; ?> .pp-infolist-icon:hover .pp-icon {
		<?php if ( isset( $items->icon_background_hover ) && ! empty( $items->icon_background_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $items->icon_background_hover ); ?>;
		<?php } ?>
		<?php if ( isset( $items->icon_color_hover ) && ! empty( $items->icon_color_hover ) ) { ?>
		color: #<?php echo $items->icon_color_hover; ?>;
		<?php } ?>
	}

	<?php if( $items->link_type == 'read_more' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-list-item-<?php echo $i; ?> .pp-more-link {
			<?php if( $items->read_more_color ) { ?>color: #<?php echo $items->read_more_color; ?>;<?php } ?>
		}
		.fl-node-<?php echo $id; ?> .pp-list-item-<?php echo $i; ?> .pp-more-link:hover {
			<?php if( $items->read_more_color_hover ) { ?>color: #<?php echo $items->read_more_color_hover; ?>;<?php } ?>
		}
	<?php } ?>

	.fl-node-<?php echo $id; ?> .pp-list-item-<?php echo $i; ?> .animated {
		<?php if( $items->animation_duration ) { ?>-webkit-animation-duration: <?php echo $items->animation_duration; ?>ms;<?php } ?>
		<?php if( $items->animation_duration ) { ?>-moz-animation-duration: <?php echo $items->animation_duration; ?>ms;<?php } ?>
		<?php if( $items->animation_duration ) { ?>-o-animation-duration: <?php echo $items->animation_duration; ?>ms;<?php } ?>
		<?php if( $items->animation_duration ) { ?>-ms-animation-duration: <?php echo $items->animation_duration; ?>ms;<?php } ?>
		<?php if( $items->animation_duration ) { ?>animation-duration: <?php echo $items->animation_duration; ?>ms;<?php } ?>
	}
<?php endfor; ?>

.fl-node-<?php echo $id; ?> .pp-infolist-wrap .layout-3 .pp-list-item {
	width: <?php echo 100 / $number_items; ?>%;
}

@media only screen and (max-width: 768px) {
	.fl-node-<?php echo $id; ?> .pp-infolist-wrap .layout-3 .pp-list-item {
		width: 100%;
		max-width: 400px;
		float: none;
	}
}
