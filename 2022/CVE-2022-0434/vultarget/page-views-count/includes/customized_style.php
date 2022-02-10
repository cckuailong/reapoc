<style>
<?php
global $pvc_settings;
?>
.pvc_clear {
	clear: both;
}
/* Stats Icon */
body .pvc-stats-icon, body .pvc-stats-icon svg {
	color: <?php echo $pvc_settings['icon_color']; ?> !important;
	fill: <?php echo $pvc_settings['icon_color']; ?> !important;
}
body .pvc_stats {
<?php if ( 'centre' == $pvc_settings['aligment'] ) { ?>
	text-align: center;
	float: none;
<?php } elseif ( 'right' == $pvc_settings['aligment'] ) { ?>
	text-align: right;
	float: right;
<?php } ?>
}
body .pvc_stats .pvc-stats-icon {
	vertical-align: middle;
}
body .pvc_stats .pvc-stats-icon.small svg {
	width: 18px;
}
body .pvc_stats .pvc-stats-icon.medium svg {
	width: 24px;
}
body .pvc_stats .pvc-stats-icon.large svg {
	width: 30px;
}
</style>