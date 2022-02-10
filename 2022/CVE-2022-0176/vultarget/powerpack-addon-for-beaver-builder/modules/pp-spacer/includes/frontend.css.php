
<?php
$medium_breakpoint = $global_settings->medium_breakpoint;
$small_breakpoint = $global_settings->responsive_breakpoint;
$hide_on = $settings->hide_on;
$custom_breakpoint = $settings->custom_breakpoint;
$breakpoint_condition = $settings->breakpoint_condition;
$width_type = 'lt_equals_to' == $breakpoint_condition ? 'max' : 'min';
?>

<?php if ( 'large' == $hide_on ) { ?>
    <?php if ( 'yes' == $settings->hide_column ) { ?>
    .fl-node-<?php echo $module->parent;?>,
    <?php } ?>
    .fl-node-<?php echo $id; ?> {
        display: none !important;
    }
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-spacer-module {
    height: <?php echo $settings->spacer_height_lg; ?>px;
    width: 100%;
}

<?php if ( FLBuilderModel::is_builder_active() ) { ?>
	.fl-node-<?php echo $id; ?> .pp-spacer-module:before {
		content: "<?php esc_html_e( 'Click here to edit Spacer module.', 'bb-powerpack-lite' ); ?>";
	}
<?php } ?>

@media only screen and (max-width: <?php echo $medium_breakpoint; ?>px) {
    <?php if ( 'medium' == $hide_on && ! FLBuilderModel::is_builder_active() ) { ?>
        <?php if ( 'yes' == $settings->hide_column ) { ?>
        .fl-node-<?php echo $module->parent;?>,
        <?php } ?>
        .fl-node-<?php echo $id; ?> {
            display: none !important;
        }
    <?php } ?>
    .fl-node-<?php echo $id; ?> .pp-spacer-module {
        height: <?php echo '' == $settings->spacer_height_md ? $settings->spacer_height_lg : $settings->spacer_height_md; ?>px;
    }
}
@media only screen and (max-width: <?php echo $small_breakpoint; ?>px) {
    <?php if ( 'small' == $hide_on && ! FLBuilderModel::is_builder_active() ) { ?>
        <?php if ( 'yes' == $settings->hide_column ) { ?>
        .fl-node-<?php echo $module->parent;?>,
        <?php } ?>
        .fl-node-<?php echo $id; ?> {
            display: none !important;
        }
    <?php } ?>
    .fl-node-<?php echo $id; ?> .pp-spacer-module {
        height: <?php echo '' == $settings->spacer_height_sm ? $settings->spacer_height_lg : $settings->spacer_height_sm; ?>px;
    }
}
<?php if ( 'custom' == $hide_on && ! FLBuilderModel::is_builder_active() ) { ?>
    @media only screen and (<?php echo $width_type; ?>-width: <?php echo $custom_breakpoint; ?>px) {
        <?php if ( 'yes' == $settings->hide_column ) { ?>
        .fl-node-<?php echo $module->parent;?>,
        <?php } ?>
        .fl-node-<?php echo $id; ?> {
            display: none !important;
        }
    }
<?php } ?>
