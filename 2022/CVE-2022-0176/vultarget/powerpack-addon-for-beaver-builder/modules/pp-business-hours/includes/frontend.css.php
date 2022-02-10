.fl-node-<?php echo $id; ?> .pp-business-hours-content {
	<?php if ( ! empty( $settings->box_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value($settings->box_bg_color); ?>;
	<?php } ?>
}

<?php
// Border - Settings
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'box_border_setting',
	'selector' 		=> ".fl-node-$id .pp-business-hours-content",
) );
?>

<?php if( $settings->zebra_pattern == 'yes' ) { ?>
.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row:nth-of-type(odd) {
	<?php if ( ! empty( $settings->row_bg_color_1 ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->row_bg_color_1 ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row:nth-of-type(even) {
	<?php if ( ! empty( $settings->row_bg_color_2 ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->row_bg_color_2 ); ?>;
	<?php } ?>
}
<?php } ?>
.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row {
    padding: <?php echo $settings->spacing; ?>px 10px <?php echo $settings->spacing; ?>px 10px;
}
<?php if( $settings->separator == 'yes' ) { ?>
.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row {
	border-bottom-style: <?php echo $settings->separator_style; ?>;
	<?php if( $settings->separator_width ) { ?>border-bottom-width: <?php echo $settings->separator_width; ?>px; <?php } ?>
	<?php if( $settings->separator_color ) { ?> border-bottom-color: #<?php echo $settings->separator_color; ?>; <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row:last-child {
	border-bottom-width: 0;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row .pp-bh-title {
   <?php if( $settings->title_color ) { ?>
	   color: #<?php echo $settings->title_color; ?>;
   <?php } ?>
}

<?php
// Title Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'title_typography',
	'selector' 		=> ".fl-node-$id .pp-business-hours-content .pp-bh-row .pp-bh-title",
) );
?>

.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row .pp-bh-timing {
   <?php if( $settings->timing_color ) { ?>
	   color: #<?php echo $settings->timing_color; ?>;
   <?php } ?>
   text-align: right;
}

<?php
// Timing Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'timing_typography',
	'selector' 		=> ".fl-node-$id .pp-business-hours-content .pp-bh-row .pp-bh-timing",
) );
?>

.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row.pp-closed .pp-bh-timing {
	<?php if( $settings->status_color ) { ?>
 	   color: #<?php echo $settings->status_color; ?>;
    <?php } ?>
}

<?php
for ($i=0; $i < count($settings->business_hours_rows); $i++) :

	if(!is_object($settings->business_hours_rows[$i])) continue;

	$bh_row = $settings->business_hours_rows[$i];
?>

<?php if ( ! empty( $bh_row->hl_row_bg_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row-<?php echo $i; ?>.pp-highlight-row {
	background-color: <?php echo pp_get_color_value( $bh_row->hl_row_bg_color ); ?>;
}
<?php } ?>

<?php if( $bh_row->hl_title_color ) { ?>
.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row-<?php echo $i; ?>.pp-highlight-row .pp-bh-title {
	color: <?php echo '#' . $bh_row->hl_title_color; ?>;
}
<?php } ?>

<?php if( $bh_row->hl_timing_color ) { ?>
.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row-<?php echo $i; ?>.pp-highlight-row .pp-bh-timing {
	color: <?php echo '#' . $bh_row->hl_timing_color; ?>;
}
<?php } ?>

<?php if( $bh_row->hl_status_color ) { ?>
.fl-node-<?php echo $id; ?> .pp-business-hours-content .pp-bh-row-<?php echo $i; ?>.pp-highlight-row.pp-closed .pp-bh-timing {
	color: <?php echo '#' . $bh_row->hl_status_color; ?>;
}
<?php } ?>

<?php endfor; ?>
